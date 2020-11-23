<?php

namespace Kematjaya\Upload;

use Symfony\Component\HttpFoundation\File\File;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface UploaderInterface {
    
    public function upload(UploadedFile $file, string $directory = null):?File;
    
    public function getTargetDirectory():?string;
}