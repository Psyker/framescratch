<?php

namespace App\Blog\Repository;

use App\Blog\Entity\Post;
use App\Framework\Database\Repository;

class PostRepository extends Repository
{

    protected $entity = Post::class;

    protected $table = 'posts';

    protected function paginationQuery()
    {
        return "SELECT p.id, p.name, c.name as category_name
        FROM {$this->table} as p
        LEFT JOIN categories as c ON p.category_id = c.id
         ORDER BY created_at DESC";
    }
}
