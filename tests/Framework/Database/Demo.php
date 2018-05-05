<?php

namespace Tests\Framework\Database;

class Demo
{
    private $slug;

    public function setSlug($slug)
    {
        $this->slug = $slug . 'demo';
    }

    public function getSlug()
    {
        return $this->slug;
    }
}