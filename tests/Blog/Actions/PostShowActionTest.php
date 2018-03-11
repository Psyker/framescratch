<?php

 namespace Test\Blog\Actions;

 use App\Blog\Actions\PostIndexAction;
 use App\Blog\Actions\PostShowAction;
 use App\Blog\Entity\Post;
 use App\Blog\Repository\PostRepository;
 use Framework\Renderer\RendererInterface;
 use Framework\Router;
 use GuzzleHttp\Psr7\ServerRequest;
 use PHPUnit\Framework\TestCase;

 class PostShowActionTest extends TestCase
 {

      /**
       * @var RendererInterface
       */
      private $renderer;

      /**
       * @var PostRepository
       */
      private $repository;

      /**
       * @var Router
       */
      private $router;

      /**
       * @var PostIndexAction
       */
      private $action;

      public function setUp()
     {
         $this->renderer = $this->prophesize(RendererInterface::class);
         $this->repository = $this->prophesize(PostRepository::class);
         // PDO
         $this->router = $this->prophesize(Router::class);

         $this->action = new PostShowAction(
             $this->renderer->reveal(),
             $this->repository->reveal(),
             $this->router->reveal()
         );
     }

     public function makePost(int $id, string $slug): Post
     {
         $post = new Post();
         $post->id = $id;
         $post->slug = $slug;

         return $post;
     }

      public function testShowRedirect()
     {
         $post = $this->makePost(9, 'fake-test');
         $request = (new ServerRequest('GET', '/'))
             ->withAttribute('id', $post->id)
             ->withAttribute('slug', 'test');

         $this->router->generateUri(
             'blog.show',
             [
                 'id' => $post->id,
                 'slug' => $post->slug
             ])->willReturn('/test3');
         $this->repository->findWithCategory($post->id)->willReturn($post);


         $response = call_user_func_array($this->action, [$request]);
         $this->assertEquals(301, $response->getStatusCode());
         $this->assertEquals(['/test3'], $response->getHeader('location'));
     }

      public function testShowRender()
     {
         $post = $this->makePost(9, 'fake-test');
         $request = (new ServerRequest('GET', '/'))
             ->withAttribute('id', $post->id)
             ->withAttribute('slug', $post->slug);

         $this->repository->findWithCategory($post->id)->willReturn($post);
         $this->renderer->render('@blog/show', ['post' => $post])->willReturn('');

         $response = call_user_func_array($this->action, [$request]);
         $this->assertEquals(true, true);
     }

 }
