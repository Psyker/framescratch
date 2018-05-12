<?php

namespace Framework\Database;

class Query
{
    /**
     * @var array|null $select
     */
    private $select = null;

    /**
     * @var string|null $from
     */
    private $from = null;

    /**
     * @var null|array $join
     */
    private $join = [];

    /**
     * @var string|null $entity
     */
    private $entity = null;

    /**
     * @var array $where
     */
    private $where = [];

    /**
     * @var string|null $group
     */
    private $group = null;

    /**
     * @var string|null $order
     */
    private $order;

    /**
     * @var int $limit
     */
    private $limit;

    /**
     * @var array $params
     */
    private $params;

    /**
     * @var null|\PDO
     */
    private $pdo;

    /**
     * Query constructor.
     * @param null|\PDO $pdo
     */
    public function __construct(?\PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    /**
     * Store the entity name that has to be hydrated.
     * @param string $entity
     * @return Query
     */
    public function into(string $entity): self
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Execute the current query as an associative array.
     * @return QueryResult
     */
    public function all(): QueryResult
    {
        return new QueryResult(
            $this->execute()->fetchAll(\PDO::FETCH_ASSOC),
            $this->entity
        );
    }

    /**
     * @param string $table
     * @param null|string $alias
     * @return Query
     */
    public function from(string $table, ?string $alias = null): self
    {
        if ($alias) {
            $this->from[$alias] = $table;
        } else {
            $this->from[] = $table;
        }

        return $this;
    }

    /**
     * Build the select method.
     * @param string[] ...$fields
     * @return Query
     */
    public function select(string ...$fields): self
    {
        $this->select = $fields;

        return $this;
    }

    public function limit(int $limit, int $offset)
    {
        $this->limit = compact('limit', 'offset');

        return $this;
    }

    /**
     * Add conditions to the current query.
     * @param string[] ...$condition
     * @return Query
     */
    public function where(string ...$condition): self
    {
        $this->where = array_merge($this->where, $condition);
        return $this;
    }

    public function join(string $table, string $alias , string $condition): self
    {
        $this->join[] = compact('table', 'alias', 'condition');

        return $this;
    }

    /**
     * Add count method in select.
     * @return int
     */
    public function count(): int
    {
        $this->select('COUNT(id)');
        return $this->execute()->fetchColumn();
    }

    /**
     * Return the params
     * @param array $params
     * @return Query
     */
    public function params(array $params): self
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Execute the current query with params.
     * @return \PDOStatement
     */
    public function execute()
    {
        $query = $this->__toString();
        if ($this->params) {
            $statement = $this->pdo->prepare($query);
            $statement->execute($this->params);

            return $statement;
        }
        return $this->pdo->query($query);
    }

    /**
     * Return the final builded query.
     * @return string
     */
    public function __toString()
    {
        $parts = ['SELECT'];
        if ($this->select) {
            $parts[] = join(', ', $this->select);
        } else {
            $parts[] = '*';
        }
        $parts[] = 'FROM';
        $parts[] = $this->buildFrom();
        if (!empty($this->where)) {
            $parts[] = 'WHERE';
            $parts[] = "(" . join(') AND (', $this->where) . ")";
        }
        if (!empty($this->join)) {
            $parts[] = $this->buildJoin();
        }
        if (!empty($this->limit)) {
            $parts[] = "LIMIT " . $this->limit['offset'].", ". $this->limit['limit'];
        }


        return join(' ', $parts);
    }

    /**
     * Build from parameters.
     * @return string
     */
    private function buildFrom(): string
    {
        $from = [];
        foreach ($this->from as $key => $value) {
            if (is_string($key)) {
                $from[] = "$value as $key";
            } else {
                $from[] = $value;
            }
        }

        return join(',', $from);
    }

    /**
     * Build join  parameters.
     * @return string
     */
    private function buildJoin(): string
    {
        $joins = [];
        foreach ($this->join as $value) {
            $joins[] = "LEFT JOIN ${value['table']} as ${value['alias']} ON ${value['condition']}";
        }

        return join(',', $joins);
    }
}
