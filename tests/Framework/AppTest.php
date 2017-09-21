<?php

namespace Tests\Framework;

use App\Blog\BlogModule;
use Fig\Http\Message\RequestMethodInterface;
use Framework\App;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Tests\Framework\Modules\ErroredModule;

class AppTest extends TestCase {

    public function testRedirectTrailingSlash()
    {
        $app = new App();
        $request = new ServerRequest(RequestMethodInterface::METHOD_GET, '/demoslash/');
        $response = $app->run($request);
        $this->assertEquals('/demoslash', $response->getHeaderLine('Location'));
        $this->assertEquals(301, $response->getStatusCode());
    }

    public function testBlog()
    {
        $app = new App([
            BlogModule::class
        ]);
        $request = new ServerRequest(RequestMethodInterface::METHOD_GET, '/blog');
        $response = $app->run($request);
        $this->assertContains('<h1>Blog</h1>', (string) $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());

        $requestSingle = new ServerRequest(RequestMethodInterface::METHOD_GET, '/blog/my-post');
        $responseSingle = $app->run($requestSingle);
        $this->assertContains('<h1>Post my-post</h1>', (string) $responseSingle->getBody());
    }

    public function testThrowExceptionIfNoResponseSent()
    {
        $app = new App([
            ErroredModule::class
        ]);
        $request = new ServerRequest(RequestMethodInterface::METHOD_GET, '/fake');
        $this->expectException(\Exception::class);
        $app->run($request);
    }

    public function testError404()
    {
        $app = new App();
        $request = new ServerRequest(RequestMethodInterface::METHOD_GET, '/testerror');
        $response = $app->run($request);
        $this->assertContains('<h1>Erreur 404</h1>', (string) $response->getBody());
        $this->assertEquals(404, $response->getStatusCode());
    }
}