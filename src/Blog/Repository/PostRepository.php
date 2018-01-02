<?php

namespace App\Blog\Repository;

use App\Blog\Entity\Post;
use Framework\Database\PaginatedQuery;
use Pagerfanta\Pagerfanta;

class PostRepository
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * PostRepository constructor.
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {

        $this->pdo = $pdo;
    }

    /**
     * Paginate posts.
     * @param int $perPage
     * @param int $currentPage
     * @return Pagerfanta
     */
    public function findPaginated(int $perPage, int $currentPage): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->pdo,
            'SELECT * FROM posts ORDER BY created_at DESC',
            'SELECT COUNT(id) FROM posts',
            Post::class
        );

        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    /**
     * Retrieve post by its id.
     * @param int $id
     * @return Post|null
     */
    public function find(int $id): ?Post
    {
        $query = $this->pdo
            ->prepare('SELECT * FROM posts WHERE id = ?');
        $query->execute([$id]);
        $query->setFetchMode(\PDO::FETCH_CLASS, Post::class);
        return $post = $query->fetch() ?: null;
    }

    /**
     * Update data stored in database.
     * @param int $id
     * @param array $params
     * @return bool
     * @internal param array $field
     */
    public function update(int $id, array $params): bool
    {
        $fieldQuery = $this->buildFieldQuery($params);
        $params["id"] = $id;
        $query = $this->pdo
            ->prepare(
                "UPDATE posts SET $fieldQuery WHERE id = :id"
            );

        return $query->execute($params);
    }

    /**
     * Create new data.
     * @param array $params
     * @return bool
     */
    public function insert(array $params)
    {
        $fields = array_keys($params);
        $values = array_map(function ($field) {
            return ':' . $field;
        }, $fields);
        $query = $this->pdo->prepare(
            "INSERT INTO posts (". join(',', $fields) .") VALUES (". join(',', $values) .")"
        );
        return $query->execute($params);
    }

    /**
     * Delete data by id.
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $query = $this->pdo->prepare('DELETE FROM posts WHERE id = ?');
        return $query->execute([$id]);
    }

    private function buildFieldQuery(array $params)
    {
        return join(
            ', ',
            array_map(
                function ($field) {
                    return "$field = :$field";
                },
                array_keys($params)
            )
        );
    }
}
