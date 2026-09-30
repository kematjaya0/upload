<?php

namespace Kematjaya\Upload\Tests;

use Kematjaya\Upload\Transformer\FileTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;

class FileTransformerTest extends TestCase
{
    public function testTransform(): void
    {
        $transformer = new FileTransformer(__DIR__);

        $file = $transformer->transform(basename(__FILE__));
        $this->assertInstanceOf(File::class, $file);
        $this->assertSame(__FILE__, $file->getPathname());

        $this->assertNull($transformer->transform(null));
        $this->assertNull($transformer->transform(''));
        $this->assertNull($transformer->transform('tidak-ada.txt'));
    }

    public function testReverseTransformReturnsValueAsIs(): void
    {
        $transformer = new FileTransformer(__DIR__);
        $file = new File(__FILE__);

        $this->assertSame($file, $transformer->reverseTransform($file));
        $this->assertNull($transformer->reverseTransform(null));
    }
}
