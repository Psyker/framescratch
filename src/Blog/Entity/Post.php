<?php

namespace App\Blog\Entity;

class Post
{

    /**
     * @var int $id
     */
    public $id;

    /**
     * @var string $name
     */
    public $name;

    /**
     * @var string $slug
     */
    public $slug;

    /**
     * @var string $content
     */
    public $content;

    /**
     * @var
     */
    public $createdAt;

    /**
     * @var
     */
    public $updatedAt;

    /**
     * @var string
     */
    public $categoryName;

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        if (is_string($updatedAt)) {
            $this->updatedAt = new \DateTime($updatedAt);
        }
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        if (is_string($createdAt)) {
            $this->createdAt = new \DateTime($createdAt);
        }
    }
}
