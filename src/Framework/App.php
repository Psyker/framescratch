<?php

namespace Framework;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class App
{

    /**
     * Run the app.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        // If the uri contain a / at his end, redirect to the "without /" version of the uri.
        $uri = $request->getUri()->getPath();
        if (!empty($uri) && $uri[-1] === "/") {
            return $response = (new Response())
                ->withStatus(301)
                ->withHeader('Location', substr($uri, 0, -1));
        }
        if (!empty($uri) && $uri === "/blog") {
            return $response = (new Response(200, [], "<h1>Blog</h1>"));
        }

        return new Response(404, [], "<h1>Erreur 404</h1>");
    }
}
