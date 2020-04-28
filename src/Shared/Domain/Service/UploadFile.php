<?php

namespace Shared\Domain\Service;

use Resources\Exception\RegularException;
use Shared\Domain\Model\FileInfo;

class UploadFile
{

    protected $fileRepository;

    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    public function execute(FileInfo $fileInfo, $contents): void
    {
        $fullyQualifiedFileName = $fileInfo->getFullyQualifiedFileName();
        $this->assertFileNameAvailable($fullyQualifiedFileName);

        if (!$this->fileRepository->write($fullyQualifiedFileName, $contents)) {
            $errorDetail = "internal server error: fail to write file to disc";
            throw RegularException::internalServerError($errorDetail);
        }
    }

    protected function assertFileNameAvailable(string $fullyQualifiedFileName): void
    {
        if ($this->fileRepository->has($fullyQualifiedFileName)) {
            $errorDetail = "conflict: file name already exist";
            throw RegularException::conflict($errorDetail);
        }
    }
}

