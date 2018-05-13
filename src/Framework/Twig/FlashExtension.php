<?php
namespace Framework\Twig;

use Framework\Session\FlashService;
use Twig_SimpleFunction;

class FlashExtension extends \Twig_Extension
{

    /**
     * @var FlashService
     */
    private $flash;

    public function __construct(FlashService $flash)
    {
        $this->flash = $flash;
    }

    public function getFunctions(): array
    {
        return [
            new Twig_SimpleFunction('flash', [$this, 'getFlash'])
        ];
    }

    public function getFlash(string $type): ?array
    {
        return [
            'class' => $type,
            'message' => $this->flash->get($type)
        ];
    }
}
