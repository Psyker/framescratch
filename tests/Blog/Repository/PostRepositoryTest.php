<?php

namespace Tests\App\Blog\Repository;

use App\Blog\Entity\Post;
use App\Blog\Repository\PostRepository;
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
        $this->postRepository = new PostRepository($this->getPDO());
    }

    public function testFind()
    {
        $this->seedDatabase();
        $post = $this->postRepository->find(1);
        $this->assertInstanceOf(Post::class, $post);
    }

    public function testFindNotFound()
    {
        $post = $this->postRepository->find(1000000);
        $this->assertNull($post);
    }
}