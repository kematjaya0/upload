<?php

namespace Kematjaya\Upload\Uploader;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class FileUploader implements UploaderInterface
{
    public function __construct(
        private readonly string $targetDirectory,
        private readonly SluggerInterface $slugger,
    ) {}

    public function upload(UploadedFile $file, ?string $directory = null, bool $compress = true): ?File
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename)->lower()->toString();
        // guessExtension() null untuk tipe yang tidak dikenali: pakai ekstensi dari nama asli
        $extension = $file->guessExtension() ?? $file->getClientOriginalExtension();
        $fileName = sprintf('%s-%s', $safeFilename, uniqid());
        if ('' !== $extension) {
            $fileName .= '.' . $extension;
        }

        // lewat getter, agar subclass yang mengganti target directory ikut dipakai
        $targetDir = $this->getTargetDirectory();
        if ($directory) {
            $targetDir .= DIRECTORY_SEPARATOR . $directory;
        }
        (new Filesystem())->mkdir($targetDir, 0o777);

        return $file->move($targetDir, $fileName);
    }

    public function getTargetDirectory(): ?string
    {
        return $this->targetDirectory;
    }
}
