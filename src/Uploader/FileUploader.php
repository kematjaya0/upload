<?php

namespace Kematjaya\Upload\Uploader;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class FileUploader implements UploaderInterface
{

    /**
     *
     * @var string
     */
    private $targetDirectory;

    /**
     *
     * @var SluggerInterface
     */
    private $slugger;

    public function __construct(string $targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file, string $directory = null, bool $compress = true):?File
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = strtolower($this->slugger->slug($originalFilename));
        $fileName = sprintf("%s-%s.%s", $safeFilename, uniqid(), $file->guessExtension());

        $targetDir = $directory ? $this->targetDirectory . DIRECTORY_SEPARATOR . $directory : $this->targetDirectory;
        $fileSystem = new Filesystem();
        try {
            if (!$fileSystem->exists($targetDir)) {
                $fileSystem->mkdir($targetDir, 0777);
            }

            return $file->move($targetDir, $fileName);

        } catch (FileException $e) {
            throw $e;
        }

        return $fileName;
    }

    public function getTargetDirectory():?string
    {
        return $this->targetDirectory;
    }
}
