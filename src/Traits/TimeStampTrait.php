<?php

namespace App\Traits;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

trait TimeStampTrait
{
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updateAt = null;


    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->updateAt;
    }

    public function setUpdateAt(?\DateTimeInterface $updateAt): self
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function onPrePersist(){

        $this->createdAt = new \DateTime();
        $this->updateAt = new \DateTime();

    }

    #[ORM\PreUpdate]
    public function onPreUpdate(){

    }
}