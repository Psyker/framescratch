<?php

namespace Framework;

use Psr\Http\Message\UploadedFileInterface;

class Upload
{
    protected $path;

    protected $formats = [
        'thumb' => [140, 140]
    ];

    public function __construct(?string $path = null)
    {
        if ($path) {
            $this->path = $path;
        }
    }

    public function upload(UploadedFileInterface $file): string
    {
        $filename = $file->getClientFilename();
        $file->moveTo($this->path . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }
}