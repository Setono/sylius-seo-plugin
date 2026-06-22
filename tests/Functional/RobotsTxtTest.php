<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\Functional;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\Attributes\Test;
use Setono\SyliusSEOPlugin\Tests\Application\Entity\Channel;
use Sylius\Component\Currency\Model\Currency;
use Sylius\Component\Locale\Model\Locale;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class RobotsTxtTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient(['debug' => false], ['HTTP_HOST' => 'localhost']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // The booted Sylius kernel registers an exception handler (via the debug/var-dumper
        // components) that it does not restore on shutdown; restore it here so PHPUnit does not
        // flag the test as risky.
        restore_exception_handler();
    }

    #[Test]
    public function it_renders_the_robots_txt_configured_on_the_channel(): void
    {
        $this->configureChannelRobotsTxt("User-agent: *\nDisallow: /admin");

        $this->client->request('GET', '/robots.txt');

        self::assertResponseIsSuccessful();
        self::assertStringStartsWith('text/plain', (string) $this->client->getResponse()->headers->get('Content-Type'));

        $content = (string) $this->client->getResponse()->getContent();
        self::assertStringContainsString('User-agent: *', $content);
        self::assertStringContainsString('Disallow: /admin', $content);
    }

    #[Test]
    public function it_renders_the_robots_txt_as_a_twig_template(): void
    {
        // This mirrors the example we document in the admin help box, so it guards against the
        // Twig rendering (absolute_url() etc.) silently breaking.
        $this->configureChannelRobotsTxt("Sitemap: {{ absolute_url('/sitemap.xml') }}");

        $this->client->request('GET', 'http://localhost/robots.txt');

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('Sitemap: http://localhost/sitemap.xml', (string) $this->client->getResponse()->getContent());
    }

    #[Test]
    public function it_returns_a_404_when_the_channel_has_no_robots_txt(): void
    {
        $this->configureChannelRobotsTxt(null);

        $this->client->request('GET', '/robots.txt');

        self::assertResponseStatusCodeSame(404);
    }

    private function configureChannelRobotsTxt(?string $robotsTxt): void
    {
        $manager = $this->getManager();

        $channel = $manager->getRepository(Channel::class)->findOneBy(['hostname' => 'localhost']);
        if (!$channel instanceof Channel) {
            $channel = new Channel();
            $channel->setCode('WEB');
            $channel->setName('Web');
            $channel->setHostname('localhost');
            $channel->setEnabled(true);

            $locale = $this->provideLocale($manager);
            $channel->setDefaultLocale($locale);
            $channel->addLocale($locale);

            $currency = $this->provideCurrency($manager);
            $channel->setBaseCurrency($currency);
            $channel->addCurrency($currency);

            $manager->persist($channel);
        }

        $channel->setRobotsTxt($robotsTxt);

        $manager->flush();
    }

    private function provideLocale(ObjectManager $manager): Locale
    {
        $locale = $manager->getRepository(Locale::class)->findOneBy(['code' => 'en_US']);
        if (!$locale instanceof Locale) {
            $locale = new Locale();
            $locale->setCode('en_US');
            $manager->persist($locale);
        }

        return $locale;
    }

    private function provideCurrency(ObjectManager $manager): Currency
    {
        $currency = $manager->getRepository(Currency::class)->findOneBy(['code' => 'USD']);
        if (!$currency instanceof Currency) {
            $currency = new Currency();
            $currency->setCode('USD');
            $manager->persist($currency);
        }

        return $currency;
    }

    private function getManager(): ObjectManager
    {
        $registry = self::getContainer()->get('doctrine');
        \assert($registry instanceof ManagerRegistry);

        return $registry->getManager();
    }
}
