<?php

namespace Kematjaya\Upload\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class FileTransformer implements DataTransformerInterface
{
    public function __construct(private string $targetDirectory)
    {
    }

    public function reverseTransform(mixed $value): mixed
    {
        return $value;
    }

    public function transform(mixed $value): ?File
    {
        if (!$value) {
            return null;
        }

        $path = $this->targetDirectory . DIRECTORY_SEPARATOR . $value;
        if (!is_file($path)) {
            return null;
        }
        return new File($path);
    }

}
