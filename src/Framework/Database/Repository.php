<?php

namespace App\Framework\Database;

use Framework\Database\NoRecordException;
use Framework\Database\PaginatedQuery;
use Pagerfanta\Pagerfanta;
use PDO;

class Repository
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * BDD Table's name
     * @var string
     */
    protected $table;

    /**
     * Entity to use
     * @var string|null
     */
    protected $entity;

    /**
     * PostRepository constructor.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {

        $this->pdo = $pdo;
    }

    /**
     * Paginate elements.
     * @param int $perPage
     * @param int $currentPage
     * @return Pagerfanta
     */
    public function findPaginated(int $perPage, int $currentPage): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->pdo,
            $this->paginationQuery(),
            "SELECT COUNT(id) FROM {$this->table}",
            $this->entity
        );

        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    protected function paginationQuery()
    {
        return "SELECT * FROM  {$this->table}";
    }

    /**
     * Retrieve a list key => value of stored data.
     */
    public function findList(): array
    {
        $results = $this->pdo
            ->query("SELECT id, name FROM {$this->table}")
            ->fetchAll(PDO::FETCH_NUM);

        $lists = [];
        foreach ($results as $result) {
            $lists[$result[0]] = $result['1'];
        }

        return $lists;
    }

    /**
     * Retrieve an element by its id.
     * @param int $id
     * @return mixed
     * @throws NoRecordException
     */
    public function find(int $id)
    {
        return $this->fetchOrFail("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }

    /**
     * Retrieve all records.
     * @return array
     */
    public function findAll(): array
    {
        $statement = $this->pdo->query("SELECT * FROM {$this->table}");
        if ($this->entity) {
            $statement->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        } else {
            $statement->setFetchMode(PDO::FETCH_OBJ);
        }

        return $statement->fetchAll();
    }

    /**
     * Find a record relative to a field.
     * @param string $field
     * @param string $value
     * @return array
     * @throws NoRecordException
     */
    public function findBy(string $field, string $value)
    {
        return $this->fetchOrFail("SELECT * FROM {$this->table} WHERE $field = ?", [$value]);
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
                "UPDATE {$this->table} SET $fieldQuery WHERE id = :id"
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
        $values = join(', ', array_map(function ($field) {
            return ':' . $field;
        }, $fields));
        $fields = join(', ', $fields);
        $query = $this->pdo->prepare("INSERT INTO {$this->table} ($fields) VALUES ($values)");
        return $query->execute($params);
    }

    /**
     * Delete data by id.
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $query = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
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

    /**
     * Check if record exist.
     * @param $id
     * @return bool
     */
    public function exists($id): bool
    {
        $statement = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE id = ?");
        $statement->execute([$id]);
        return $statement->fetchColumn() !== false;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getEntity(): string
    {
        return $this->entity;
    }

    /**
     * @return PDO
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Allow execute a query and retrieve the first result
     * @param string $query
     * @param array $params
     * @return mixed
     * @throws NoRecordException
     */
    protected function fetchOrFail(string $query, array $params = [])
    {
        $query = $this->pdo->prepare($query);
        $query->execute($params);
        if ($this->entity) {
            $query->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        }
        $record = $query->fetch();
        if ($record === false) {
            throw new NoRecordException();
        }
        return $record;
    }
}
