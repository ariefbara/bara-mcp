<?php

namespace SharedContext\Domain\Service;

use Resources\Exception\RegularException;

class UploadFile
{

    protected $fileRepository;

    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    public function execute(CanBeSavedInStorage $fileInfo, $contents): void
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

