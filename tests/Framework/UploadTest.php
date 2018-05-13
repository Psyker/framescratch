<?php

namespace Tests\Framework;

use Framework\Upload;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

class UploadTest extends TestCase
{

    /**
     * @var Upload $upload
     */
    private $upload;

    public function setUp()
    {
        $this->upload = new Upload('tests');
    }

    public function tearDown()
    {
        if (file_exists('tests/demo.jpg')) {
            unlink('tests/demo.jpg');
        }

    }

    public function testUpload()
    {
        $uploadedFile = $this->getMockBuilder(UploadedFileInterface::class)->getMock();

        $uploadedFile->expects($this->any())->method('getClientFilename')
            ->willReturn('demo.jpg');

        $uploadedFile->expects($this->once())
            ->method('moveTo')
            ->with($this->equalTo('tests/demo.jpg'));


        $this->assertEquals('demo.jpg', $this->upload->upload($uploadedFile));
    }

    public function testUploadWithExistingFile()
    {
        $uploadedFile = $this->getMockBuilder(UploadedFileInterface::class)->getMock();

        $uploadedFile->expects($this->any())->method('getClientFilename')
            ->willReturn('demo.jpg');

        touch('tests/demo.jpg');

        $uploadedFile->expects($this->once())
            ->method('moveTo')
            ->with($this->equalTo('tests/demo_copy.jpg'));


        $this->assertEquals('demo_copy.jpg', $this->upload->upload($uploadedFile));
    }
}
