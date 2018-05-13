<?php

namespace App\Blog\Repository;

use App\Blog\Entity\Post;
use App\Framework\Database\Repository;
use Framework\Database\PaginatedQuery;
use Pagerfanta\Pagerfanta;

class PostRepository extends Repository
{

    protected $entity = Post::class;

    protected $table = 'posts';

    public function findWithCategory(int $id)
    {
        return $this->fetchOrFail('
            SELECT p.*, c.name as category_name, c.slug as category_slug
            FROM posts as p
            LEFT JOIN categories as c ON c.id = p.category_id
            WHERE p.id = ?
        ', [$id]);
    }

    protected function paginationQuery()
    {
        return "SELECT p.id, p.name, p.content, c.name as category_name
        FROM {$this->table} as p
        LEFT JOIN categories as c ON p.category_id = c.id
        ORDER BY created_at DESC";
    }

    public function findPaginatedPublic(int $perPage, int $currentPage): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->pdo,
            "SELECT p.*, c.name as category_name, c.slug as category_slug
                   FROM posts as p
                   LEFT JOIN categories as c ON c.id = p.category_id
                   ORDER BY p.created_at DESC",
            "SELECT COUNT(id) FROM {$this->table}",
            $this->entity
        );

        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    public function findPaginatedForCategory(int $perPage, int $currentPage, int $categoryId): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->pdo,
            "SELECT p.*, c.name as category_name, c.slug as category_slug
                   FROM posts as p
                   LEFT JOIN categories as c ON c.id = p.category_id
                   WHERE p.category_id = :category
                   ORDER BY p.created_at DESC",
            "SELECT COUNT(id) FROM {$this->table} WHERE category_id = :category",
            $this->entity,
            ['category' => $categoryId]
        );

        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }
}
