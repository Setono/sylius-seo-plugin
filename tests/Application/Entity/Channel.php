<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Tests\Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setono\SyliusSEOPlugin\Model\ChannelInterface as SEOChannelInterface;
use Setono\SyliusSEOPlugin\Model\ChannelTrait as SEOChannelTrait;
use Sylius\Component\Core\Model\Channel as BaseChannel;

/**
 * @ORM\Entity()
 *
 * @ORM\Table(name="sylius_channel")
 */
class Channel extends BaseChannel implements SEOChannelInterface
{
    use SEOChannelTrait;
}
