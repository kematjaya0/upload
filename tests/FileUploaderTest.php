<?php

namespace Kematjaya\Upload\Tests;

use Kematjaya\Upload\Uploader\FileUploader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\AsciiSlugger;

class FileUploaderTest extends TestCase
{
    private string $targetDirectory;

    protected function setUp(): void
    {
        $this->targetDirectory = sys_get_temp_dir() . '/kmj-upload-' . uniqid();
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->targetDirectory);
    }

    public function testUploadToTargetDirectory(): void
    {
        $uploader = new FileUploader($this->targetDirectory, new AsciiSlugger());

        $file = $uploader->upload($this->uploadedFile('Laporan Bulanan.txt', 'isi laporan'));

        $this->assertSame($this->targetDirectory, $file->getPath());
        $this->assertMatchesRegularExpression('/^laporan-bulanan-[0-9a-f]+\.txt$/', $file->getFilename());
        $this->assertStringEqualsFile($file->getPathname(), 'isi laporan');
        $this->assertSame($this->targetDirectory, $uploader->getTargetDirectory());
    }

    public function testUploadToSubDirectory(): void
    {
        $uploader = new FileUploader($this->targetDirectory, new AsciiSlugger());

        $file = $uploader->upload($this->uploadedFile('a.txt', 'a'), 'dokumen/2026');

        $this->assertSame($this->targetDirectory . '/dokumen/2026', $file->getPath());
    }

    public function testUnknownMimeTypeKeepsOriginalExtension(): void
    {
        $uploader = new FileUploader($this->targetDirectory, new AsciiSlugger());

        // isi biner acak: mime type-nya tidak punya ekstensi yang dikenal
        $file = $uploader->upload($this->uploadedFile('data.kmj', "\x00\x01\x02\x03\xff\xfe"));

        $this->assertMatchesRegularExpression('/^data-[0-9a-f]+\.(kmj|bin)$/', $file->getFilename());
    }

    public function testSubclassTargetDirectoryIsUsed(): void
    {
        $other = $this->targetDirectory . '/lain';
        $uploader = new class ($this->targetDirectory, new AsciiSlugger(), $other) extends FileUploader {
            public function __construct(string $targetDirectory, AsciiSlugger $slugger, private readonly string $other)
            {
                parent::__construct($targetDirectory, $slugger);
            }

            public function getTargetDirectory(): ?string
            {
                return $this->other;
            }
        };

        $file = $uploader->upload($this->uploadedFile('a.txt', 'a'));

        $this->assertSame($other, $file->getPath());
    }

    private function uploadedFile(string $name, string $content): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'kmj');
        file_put_contents($path, $content);

        // test: true melewati pengecekan is_uploaded_file()
        return new UploadedFile($path, $name, null, null, true);
    }
}
