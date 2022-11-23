<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

trait Timestampable
{
    #[ORM\Column(insertable: false, updatable: false, generated: 'INSERT', columnDefinition: 'DATETIME NOT NULL DEFAULT now() COMMENT \'(DC2Type:datetime_immutable)\'')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(insertable: false, updatable: false, generated: 'ALWAYS', columnDefinition: 'DATETIME NOT NULL DEFAULT now() ON UPDATE now() COMMENT \'(DC2Type:datetime_immutable)\'')]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }
}