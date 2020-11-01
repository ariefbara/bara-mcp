<?php

namespace Participant\Domain\Service;

use Participant\Domain\SharedModel\FileInfo;

interface LocalFileInfoRepository
{
    public function ofId(string $fileInfoId): FileInfo;
}
