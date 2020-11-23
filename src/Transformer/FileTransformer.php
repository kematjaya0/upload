<?php

namespace Kematjaya\Upload;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;
/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class FileTransformer implements DataTransformerInterface
{
    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }
    
    public function reverseTransform($value) 
    {
        return $value;
    }

    public function transform($value):?File
    {
        if(!$value)
        {
            return null;
        }
        
        $path = $this->targetDirectory . DIRECTORY_SEPARATOR . $value;
        if(!is_file($path))
        {
            return null;
        }
        return new File($path);
    }

}
