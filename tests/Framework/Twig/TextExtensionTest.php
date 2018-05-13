<?php

namespace Tests\Framework\Twig;

use Framework\Twig\TextExtension;
use PHPUnit\Framework\TestCase;

class TextExtensionTest extends TestCase
{
    /**
     * @var TextExtension
     */
    private $textExtension;

    public function setUp()
    {
        $this->textExtension = new TextExtension();
    }

    public function testExcerptWithShortText()
    {
        $text = "Hello";
        $this->assertEquals($text, $this->textExtension->excerpt($text, 10));
    }

    public function testExcerptWithLongText()
    {
        $text = "Hello guys !";
        $this->assertEquals("Hello guys...", $this->textExtension->excerpt($text, 11));
    }
}