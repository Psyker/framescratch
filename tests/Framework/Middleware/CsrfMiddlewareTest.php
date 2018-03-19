<?php

namespace Tests\Framework\Middleware;

use Framework\Middleware\CsrfMiddleware;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Http\Server\RequestHandlerInterface;
use PHPUnit\Framework\TestCase;

class CsrfMiddlewareTest extends TestCase
{

    /**
     * @var CsrfMiddleware
     */
    private $middleware;

    /**
     * @var array
     */
    private $session;

    public function setUp()
    {
        $this->session = [];
        $this->middleware = new CsrfMiddleware($this->session);
    }

    public function testLetGetRequestPass()
    {
        $requestHandler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $requestHandler->expects($this->once())
            ->method('handle');

        $request = (new ServerRequest('GET', '/demo'));
        $this->middleware->process($request, $requestHandler);
    }

    public function testBlockPostRequestWithoutCsrf()
    {
        $requestHandler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $requestHandler->expects($this->never())
            ->method('handle');

        $request = (new ServerRequest('POST', '/demo'));
        $this->expectException(\Exception::class);
        $this->middleware->process($request, $requestHandler);
    }

    public function testLetPostWithTokenPass()
    {
        $requestHandler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $requestHandler->expects($this->once())->method('handle');

        $request = (new ServerRequest('POST', '/demo'));
        $token = $this->middleware->generateToken();
        $request = $request->withParsedBody(['_csrf' => $token]);
        $this->middleware->process($request, $requestHandler);
    }

    public function testLetPostWithTokenPassOnce()
    {
        $requestHandler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $requestHandler->expects($this->once())->method('handle');

        $request = (new ServerRequest('POST', '/demo'));
        $token = $this->middleware->generateToken();
        $request = $request->withParsedBody(['_csrf' => $token]);
        $this->middleware->process($request, $requestHandler);
        $this->expectException(\Exception::class);
        $this->middleware->process($request, $requestHandler);
    }

    public function testBlockPostRequestWithInvalidCsrf()
    {
        $requestHandler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $requestHandler->expects($this->never())->method('handle');

        $request = (new ServerRequest('POST', '/demo'));
        $this->middleware->generateToken();
        $request = $request->withParsedBody(['_csrf' => 'zskdhfs']);
        $this->expectException(\Exception::class);
        $this->middleware->process($request, $requestHandler);
    }

    public function testLimitTokenNumber()
    {
        //TODO: Make test.
    }
}