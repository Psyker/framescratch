<?php

namespace Tests\App\Blog\Repository;

use App\Blog\Entity\Post;
use App\Blog\Repository\PostRepository;
use Framework\Database\NoRecordException;
use Tests\DatabaseTestCase;

class PostRepositoryTest extends DatabaseTestCase
{

    /**
     * @var PostRepository
     */
    private $postRepository;

    public function setUp()
    {
        parent::setUp();
        $pdo = $this->getPDO();
        $this->migrateDatabase($pdo);
        $this->postRepository = new PostRepository($pdo);
    }

    public function testFind()
    {
        $this->seedDatabase($this->postRepository->getPdo());
        $post = $this->postRepository->find(1);
        $this->assertInstanceOf(Post::class, $post);
    }

    public function testFindNotFound()
    {
        $this->expectException(NoRecordException::class);
        $this->postRepository->find(1000000);
    }

    public function testUpdate()
    {
        $this->seedDatabase($this->postRepository->getPdo());
        $this->postRepository->update(1, ['name' => 'Hi', 'slug' => 'test']);
        $post = $this->postRepository->find(1);
        $this->assertEquals('Hi', $post->name);
        $this->assertEquals('test', $post->slug);
    }

    public function testInsert()
    {
        $this->postRepository->insert(['name' => 'hi', 'slug' => 'test']);
        $post = $this->postRepository->find(1);
        $this->assertEquals('Hi', $post->name);
        $this->assertEquals('test', $post->slug);
    }

    public function testDelete()
    {
        $this->postRepository->insert(['name' => 'Hi', 'slug' => 'test']);
        $this->postRepository->insert(['name' => 'Hi', 'slug' => 'test']);
        $count = $this->postRepository->getPdo()->query('SELECT COUNT(id) FROM posts')->fetchColumn();
        $this->assertEquals(2, (int) 2);
        $this->postRepository->delete($this->postRepository->getPdo()->lastInsertId());
        $count = $this->postRepository->getPdo()->query('SELECT COUNT(id) FROM posts')->fetchColumn();
        $this->assertEquals(1, (int) 1);
    }
}