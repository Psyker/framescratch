<?php

namespace Tests\Framework\Database;

use App\Framework\Database\Repository;
use PDO;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    /**
     * @var Repository
     */
    private $repository;


    public function setUp()
    {
        $pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ]);
        $pdo->exec('CREATE TABLE test (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255)
        )');

        $this->repository = new Repository($pdo);
        $reflexion = new \ReflectionClass($this->repository);
        $property = $reflexion->getProperty('table');
        $property->setAccessible(true);
        $property->setValue($this->repository, 'test');
    }

    public function testFind()
    {
        $this->repository->getPdo()->exec('INSERT INTO test (name) VALUES ("a1")');
        $this->repository->getPdo()->exec('INSERT INTO test (name) VALUES ("a2")');
        $test = $this->repository->find(1);
        $this->assertInstanceOf(\stdClass::class, $test);
        $this->assertEquals('a1', $test->name);
    }

    public function testFindList()
    {
        $this->repository->getPdo()->exec('INSERT INTO test (name) VALUES ("a1")');
        $this->repository->getPdo()->exec('INSERT INTO test (name) VALUES ("a2")');
        $test = $this->repository->findList();
        $this->assertEquals(['1' => "a1", "2" => "a2"], $test);
    }

    public function testFindAll()
    {
        $this->repository->getPdo()->exec('INSERT INTO test (name) VALUES ("a1")');
        $this->repository->getPdo()->exec('INSERT INTO test (name) VALUES ("a2")');
        $categories = $this->repository->findAll();
        $this->assertCount(2, $categories);
        $this->assertInstanceOf(\stdClass::class, $categories[0]);
        $this->assertEquals('a1', $categories[0]->name);
        $this->assertEquals('a2', $categories[1]->name);
    }

    public function testFindBy()
    {
        $this->repository->getPdo()->exec('INSERT INTO test (name) VALUES ("a1")');
        $this->repository->getPdo()->exec('INSERT INTO test (name) VALUES ("a2")');
        $this->repository->getPdo()->exec('INSERT INTO test (name) VALUES ("a2")');
        $category = $this->repository->findBy('name', 'a1');
        $this->assertInstanceOf(\stdClass::class, $category);
        $this->assertEquals(1, (int) $category->id);
    }

    public function testExists()
    {
        $this->repository->getPdo()->exec('INSERT INTO test (name) VALUES ("a1")');
        $this->repository->getPdo()->exec('INSERT INTO test (name) VALUES ("a2")');
        $this->assertTrue($this->repository->exists(1));
        $this->assertTrue($this->repository->exists(1));
        $this->assertFalse($this->repository->exists(374682));
    }

    public function testCount()
    {
        $this->repository->getPdo()->exec('INSERT INTO test (name) VALUES ("a1")');
        $this->repository->getPdo()->exec('INSERT INTO test (name) VALUES ("a2")');
        $this->assertEquals(2, $this->repository->count());
    }
}