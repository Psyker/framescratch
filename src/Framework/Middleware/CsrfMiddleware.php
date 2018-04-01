<?php

namespace Framework\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;

class CsrfMiddleware implements MiddlewareInterface
{

    /**
     * @var string
     */
    private $formKey = '_csrf';

    /**
     * @var string
     */
    private $sessionKey = 'csrf';

    /**
     * @var \ArrayAccess
     */
    private $session;

    public function __construct(array $session, string $formKey = '_csrf', string $sessionKey = 'csrf')
    {
        $this->session = $session;
        $this->formKey = $formKey;
        $this->sessionKey = $sessionKey;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE'])) {
            $params = $request->getParsedBody() ?: [];
            if (!array_key_exists($this->formKey, $params)) {
                $this->reject();
            } else {
                $csrfList = $this->session[$this->sessionKey] ?? [];
                if (in_array($params[$this->formKey], $csrfList)) {
                    $this->useToken($params[$this->formKey]);
                    $handler->handle($request);
                } else {
                    $this->reject();
                }
            }
        } else {
            $handler->handle($request);
        }
    }

    public function generateToken(): string
    {
        $token = bin2hex(random_bytes(16));
        $csrfList = $this->session[$this->sessionKey] ?? [];
        $csrfList[] = $token;
        $this->session[$this->sessionKey] = $csrfList;

        return $token;
    }

    private function useToken($token): void
    {
        $tokens = array_filter($this->session[$this->sessionKey], function($t) use ($token) {
            return $token !== $t;
        });
        $this->session[$this->sessionKey] = $tokens;
    }

    private function reject(): void
    {
        throw new \Exception();
    }
}