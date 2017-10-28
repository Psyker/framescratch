<?php

 namespace Test\Blog\Actions;

 use App\Blog\Actions\BlogAction;
 use Framework\Renderer\RendererInterface;
 use Framework\Router;
 use GuzzleHttp\Psr7\ServerRequest;
 use PHPUnit\Framework\TestCase;
 use Prophecy\Argument;

 class BlogActionTest extends TestCase
 {

      /**
       * @var RendererInterface
       */
      private $renderer;

      /**
       * @var \PDO
       */
      private $pdo;

      /**
       * @var Router
       */
      private $router;

      /**
       * @var BlogAction
       */
      private $action;

      public function setUp()
     {
         $this->renderer = $this->prophesize(RendererInterface::class);
         $this->renderer->render(Argument::any())->willReturn('');
         // Article
         $post = new \stdClass();
         $post->id = 9;
         $post->slug = 'fake-test';
         // PDO
         $this->pdo = $this->prophesize(\PDO::class);
         $pdoStatement = $this->prophesize(\PDOStatement::class);
         $this->pdo->prepare(Argument::any())->willReturn($pdoStatement);
         $pdoStatement->execute(Argument::any())->willReturn(null);
         $pdoStatement->fetch(\PDO::FETCH_OBJ)->willReturn($post);
         $this->router = $this->prophesize(Router::class);

         $this->action = new BlogAction(
             $this->renderer->reveal(),
             $this->pdo->reveal(),
             $this->router->reveal()
         );
     }

      public function testShowRedirect()
     {
         $this->router->generateUri('blog.show', ['id' => 9, 'slug' => 'fake-test'])->willReturn('/test3');
         $request = (new ServerRequest('GET', '/'))
             ->withAttribute('id', 9)
             ->withAttribute('slug', 'test');

         $response = call_user_func_array($this->action, [$request]);
         $this->assertEquals(301, $response->getStatusCode());
         $this->assertEquals(['/test3'], $response->getHeader('location'));
     }

 }
