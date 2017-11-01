<?php

namespace App\Blog\Repository;

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
     * @return array
     */
    public function findPaginated(): array
    {
       return $this->pdo
            ->query('SELECT * FROM posts ORDER BY created_at DESC LIMIT 10')
            ->fetchAll();
    }

    /**
     * Retrieve post by its id.
     * @param int $id
     * @return \stdClass
     */
    public function find(int $id): \stdClass
    {
        $query = $this->pdo
            ->prepare('SELECT * FROM posts WHERE id = ?');
        $query->execute([$id]);
        return $post = $query->fetch(\PDO::FETCH_OBJ);
    }
}
