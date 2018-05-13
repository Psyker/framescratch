<?php

namespace Tests\Framework;

use Framework\Renderer\PHPRenderer;
use Framework\Renderer\RendererInterface;
use PHPUnit\Framework\TestCase;

class PHPRendererTest extends TestCase
{
    /**
     * @var RendererInterface
     */
    private $renderer;

    public function setUp()
    {
        $this->renderer = new PHPRenderer(__DIR__. '/views');
    }

    public function testRenderTheRightPath()
    {
        $this->renderer->addPath('blog', __DIR__. '/views');
        $content = $this->renderer->render('@blog/demo');
        $this->assertEquals('Hello World', $content);
    }

    public function testRenderTheDefaultPath()
    {
        $content = $this->renderer->render('demo');
        $this->assertEquals('Hello World', $content);
    }

    public function testRenderWithParams()
    {
        $content = $this->renderer->render('demoparams', ['name' => 'Theo']);
        $this->assertEquals('Hello Theo', $content);
    }

    public function testGlobalParameters()
    {
        $this->renderer->addGlobal('name', 'Theo');
        $content = $this->renderer->render('demoparams');
        $this->assertEquals('Hello Theo', $content);
    }
}
