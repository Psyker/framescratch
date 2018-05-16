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
     * @var \DateTime $createdAt
     */
    public $createdAt;

    /**
     * @var \DateTime $updatedAt
     */
    public $updatedAt;

    /**
     * @var string $image
     */
    public $image;

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

    public function getThumb()
    {
        ['filename' => $filename, 'extension' => $extension] = pathinfo($this->image);
        return '/uploads/posts/' . $filename . '_thumb' . $extension;
    }
}
