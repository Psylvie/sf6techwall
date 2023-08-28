<?php

namespace App\Traits;

use App\Entity\Tag;
use Doctrine\ORM\Mapping as ORM;

trait TagTrait
{
    #[ORM\ManyToMany(targetEntity: "Tag", cascade: ["persist"])]
    private $tags;

    public function addTag(Tag $tag)
    {
        $this->tags[] = $tag;

        return $this;
    }
    public function removeTag(Tag $tag)
    {
        $this->tags->removeElement($tag);
    }
    public function getTags()
    {
        return $this->tags;
    }
}