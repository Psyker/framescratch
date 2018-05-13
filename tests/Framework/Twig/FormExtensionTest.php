<?php

namespace Tests\Framework\Twig;

use Framework\Twig\FormExtension;
use PHPUnit\Framework\TestCase;

class FormExtensionTest extends TestCase
{

    /**
     * @var FormExtension
     */
    private $formExtension;

    public function setUp()
    {
        $this->formExtension = new FormExtension();
    }

    public function testField()
    {
        $html = $this->formExtension->field([], 'name', 'demo', 'Post title');
        $this->assertSimilar("
            <div class=\"form-group\">
                <label for=\"name\">Post title</label>
                <input type=\"text\" class=\"form-control\" id=\"name\" name=\"name\" value=\"demo\">
            </div>
        ", $html);
    }

    public function testTextarea()
    {
        $html = $this->formExtension->field([], 'name', 'demo', 'Post content', ['type' => 'textarea']);
        $this->assertSimilar("
            <div class=\"form-group\">
                <label for=\"name\">Post content</label>
                <textarea class=\"form-control\" id=\"name\" name=\"name\">demo</textarea>
            </div>
        ", $html);
    }

    public function testFieldWithErrors()
    {
        $context = ['errors' => ['name' => 'error']];
        $html = $this->formExtension->field($context, 'name', 'demo', 'Post title');
        $this->assertSimilar("
            <div class=\"form-group has-danger\">
                <label for=\"name\">Post title</label>
                <input type=\"text\" class=\"form-control form-control-danger\" id=\"name\" name=\"name\" value=\"demo\">
                <small class=\"form-text text-muted\">error</small>
            </div>
        ", $html);
    }

    public function testSelect()
    {
        $html = $this->formExtension->field(
            [],
            'name',
            2,
            'Category',
            ['options' => ['1' => 'demo', '2' => 'demo2']]
        );

        $this->assertSimilar("
            <div class=\"form-group\">
                <label for=\"name\">Category</label>
                <select class=\"form-control\" id=\"name\" name=\"name\" >
                    <option value=\"1\">demo</option>
                    <option value=\"2\" selected>demo2</option>
                </select>
            </div>
        ", $html);
    }

    public function testFieldWithClass()
    {
        $html = $this->formExtension->field(
            [], 'name', 'demo', 'Post title', ['class' => 'demo']
        );
        $this->assertSimilar("
            <div class=\"form-group\">
                <label for=\"name\">Post title</label>
                <input type=\"text\" class=\"form-control demo\" id=\"name\" name=\"name\" value=\"demo\">
            </div>
        ", $html);
    }

    public function trim(string $string)
    {
        $lines = explode(PHP_EOL, $string);
        $lines = array_map('trim', $lines);
        return implode('', $lines);
    }

    public function assertSimilar(string $expected, string $actual)
    {
        $this->assertEquals($this->trim($expected), $this->trim($actual));
    }
}