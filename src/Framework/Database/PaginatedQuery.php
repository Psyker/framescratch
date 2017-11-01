<?php

namespace Framework\Database;

use Pagerfanta\Adapter\AdapterInterface;

class PaginatedQuery implements AdapterInterface
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var string
     */
    private $query;

    /**
     * @var int|string
     */
    private $countQuery;

    /**
     * @var
     */
    private $class;

    /**
     * PaginatedQuery constructor.
     * @param \PDO $pdo
     * @param string $query Retrieve posts
     * @param int|string $countQuery Count posts
     * @param string $class
     */
    public function __construct(\PDO $pdo, string $query, string $countQuery, string $class)
    {
        $this->pdo = $pdo;
        $this->query = $query;
        $this->countQuery = $countQuery;
        $this->class = $class;
    }

    /**
     * Returns the number of results.
     *
     * @return integer The number of results.
     */
    public function getNbResults(): int
    {
        return $this->pdo->query($this->countQuery)->fetchColumn();
    }

    /**
     * Returns an slice of the results.
     *
     * @param integer $offset The offset.
     * @param integer $length The length.
     *
     * @return array|\Traversable The slice.
     */
    public function getSlice($offset, $length): array
    {
        $offset = (int) $offset;
        $length = (int) $length;

        $statement = $this->pdo->prepare($this->query . ' LIMIT :offset, :length');
        $statement->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $statement->bindParam(':length', $length, \PDO::PARAM_INT);
        $statement->setFetchMode(\PDO::FETCH_CLASS, $this->class);
        $statement->execute();

        return $statement->fetchAll();
    }
}
