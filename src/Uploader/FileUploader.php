<?php

namespace Kematjaya\Upload;

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
    private $targetDirectory;
    private $slugger;

    public function __construct($targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file, string $directory = null):?File
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = strtolower($this->slugger->slug($originalFilename));
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        $targetDir = $directory ? $this->targetDirectory . DIRECTORY_SEPARATOR . $directory : $this->targetDirectory;
        $fileSystem = new Filesystem();
        
        try {
            if(!$fileSystem->exists($targetDir))
            {
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
