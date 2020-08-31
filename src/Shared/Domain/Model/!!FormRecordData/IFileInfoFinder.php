<?php

namespace Shared\Domain\Model\FormRecordData;

use Shared\Domain\Model\FileInfo;

interface IFileInfoFinder
{
    public function ofId(string $fileInfoId): FileInfo;
}
