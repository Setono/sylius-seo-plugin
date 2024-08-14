<?php

declare(strict_types=1);

namespace Setono\SyliusSEOPlugin\Model;

use Doctrine\ORM\Mapping as ORM;

trait ChannelTrait
{
    /** @ORM\Column(type="text", nullable=true) */
    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $robotsTxt = null;

    public function getRobotsTxt(): ?string
    {
        return $this->robotsTxt;
    }

    public function setRobotsTxt(?string $robotsTxt): void
    {
        $this->robotsTxt = $robotsTxt;
    }
}
