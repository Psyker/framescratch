<?php

namespace Framework\Twig;

use Framework\Middleware\CsrfMiddleware;
use Twig_SimpleFunction;

class CsrfExtension extends \Twig_Extension
{

    /**
     * @var CsrfMiddleware
     */
    private $csrfMiddleware;

    public function __construct(CsrfMiddleware $csrfMiddleware)
    {
        $this->csrfMiddleware = $csrfMiddleware;
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('csrf_input', [$this, 'csrfInput'], ['is_safe' => ['html']])
        ];
    }

    public function csrfInput()
    {
        return '<input type="hidden" name="'.$this->csrfMiddleware->getFormKey().'" value="'.$this->csrfMiddleware->generateToken().'"/>';
    }
}
