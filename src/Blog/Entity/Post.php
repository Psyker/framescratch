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
    public $created_at;

    /**
     * @var
     */
    public $updated_at;

    /**
     * @var string
     */
    public $category_name;

    public function __construct()
    {
        if ($this->created_at) {
            $this->created_at = new \DateTime($this->created_at);
        }
        if ($this->updated_at) {
            $this->updated_at = new \DateTime($this->updated_at);
        }
    }
}
