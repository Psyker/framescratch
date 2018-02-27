<?php
namespace Tests\Framework;

use Framework\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    public function testRequireIfFail()
    {
        $errors = $this->makeValidator(['name' => 'theo'])
            ->required('name', 'content')
            ->getErrors();

        $this->assertCount(1, $errors);
    }

    public function testNotEmpty()
    {
        $errors = $this->makeValidator(['name' => 'theo', 'content' => ''])
            ->notEmpty('content')
            ->getErrors();

        $this->assertCount(1, $errors);
    }

    public function testRequireIfSuccess()
    {
        $errors = $this->makeValidator(['name' => 'theo', 'content' => 'test'])
            ->required('name', 'content')
            ->getErrors();

        $this->assertCount(0, $errors);
    }

    public function testSlugSuccess()
    {
        $errors = $this->makeValidator(['slug' => 'test-slug01'])
            ->slug('slug')
            ->getErrors();

        $this->assertCount(0, $errors);
    }

    public function testSlugError()
    {
        $errors = $this->makeValidator(
            [
                'slug' => 'test-Slug01',
                'slug2' => 'test-slUg01_demo',
                'slug3' => 'test--demo-slug',
            ]
        )
            ->slug('slug', 'slug2', 'slug3')
            ->getErrors();

        $this->assertCount(3, $errors);
    }

    public function testLength()
    {
        $params = ['slug' => '123456789'];
        $this->assertCount(0, $this->makeValidator($params)->length('slug', 3)->getErrors());
        $errors = $this->makeValidator($params)->length('slug', 12)->getErrors();
        $this->assertCount(1, $errors);
        $this->assertEquals('The field slug has to contain more than 12 characters', $errors['slug']);
        $this->assertCount(1, $this->makeValidator($params)->length('slug', 3, 4)->getErrors());
        $this->assertCount(0, $this->makeValidator($params)->length('slug', 3, 20)->getErrors());
        $this->assertCount(0, $this->makeValidator($params)->length('slug', null, 20)->getErrors());
        $this->assertCount(1, $this->makeValidator($params)->length('slug', null, 8)->getErrors());
    }

    public function testDatetime()
    {
        $this->assertCount(0, $this->makeValidator(['date' => '2012-12-12 11:12:13'])->dateTime('date')->getErrors());
        $this->assertCount(0, $this->makeValidator(['date' => '2012-12-12 00:00:00'])->dateTime('date')->getErrors());
        $this->assertCount(1, $this->makeValidator(['date' => '2012-21-12'])->dateTime('date')->getErrors());
        $this->assertCount(1, $this->makeValidator(['date' => '2013-02-29 11:12:13'])->dateTime('date')->getErrors());
    }

    private function makeValidator(array $params)
    {
        return new Validator($params);
    }
}