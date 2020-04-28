<?php

namespace App\Http\Controllers;

use League\Flysystem\ {
    Adapter\Local,
    Filesystem
};
use Shared\ {
    Domain\Service\UploadFile,
    Infrastructure\Persistence\Flysystem\FlysystemFileRepository
};

class FlySystemUploadFileBuilder
{
    public static function build(): UploadFile
    {
        $root = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . "storage" . DIRECTORY_SEPARATOR . "app";
        $adapter = new Local($root);
        $filessystem = new Filesystem($adapter);
        $fileRepository = new FlysystemFileRepository($filessystem);
        return new UploadFile($fileRepository);
    }
}
