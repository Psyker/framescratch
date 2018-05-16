<?php

namespace Framework;

use Intervention\Image\ImageManager;
use Psr\Http\Message\UploadedFileInterface;

class Upload
{
    protected $path;

    protected $formats;

    public function __construct(?string $path = null)
    {
        if ($path) {
            $this->path = $path;
        }
    }

    public function upload(UploadedFileInterface $file, ?string $oldFile = null): string
    {
        $this->delete($oldFile);
        $targetPath = $this->addCopySuffix($this->path . DIRECTORY_SEPARATOR . $file->getClientFilename());
        $dirname = pathinfo($targetPath, PATHINFO_DIRNAME);
        if (!file_exists($dirname)) {
            mkdir($dirname, 777, true);
        }
        $file->moveTo($targetPath);
        $this->generateFormats($targetPath);
        return pathinfo($targetPath)['basename'];
    }

    /**
     * @param string $targetPath
     * @return string
     */
    private function addCopySuffix(string $targetPath): string
    {
        if (file_exists($targetPath)) {
            $info = pathinfo($targetPath);
            $targetPath = $info['dirname'] .
                DIRECTORY_SEPARATOR .
                $info['filename'] .
                '_copy.' .
                $info['extension'];

            return $this->addCopySuffix($this->getPathWithSuffix($targetPath, 'copy'));
        } else {
            return $targetPath;
        }
    }

    /**
     * @param null|string $oldFile
     */
    public function delete(?string $oldFile): void
    {
        if ($oldFile) {
            $oldFile = $this->path . DIRECTORY_SEPARATOR . $oldFile;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
            foreach ($this->formats as $format => $_) {
                $oldFileWithFormat = $this->getPathWithSuffix($oldFile, $format);
                if (file_exists($oldFileWithFormat)) {
                    unlink($oldFileWithFormat);
                }
            }
        }
    }

    /**
     * @param $targetPath
     */
    private function generateFormats($targetPath): void
    {
        foreach ($this->formats as $format => $size) {
            $manager = new ImageManager(['driver' => 'gd']);
            [$width, $height] = $size;
            $manager->make($targetPath)->fit($width, $height)->save($this->getPathWithSuffix($targetPath, $format));
        }
    }

    /**
     * @param string $path
     * @param string $suffix
     * @return string
     */
    private function getPathWithSuffix(string $path, string $suffix): string
    {
        $info = pathinfo($path);
        return $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '_' . $suffix . '.' . $info['extension'];
    }
}
