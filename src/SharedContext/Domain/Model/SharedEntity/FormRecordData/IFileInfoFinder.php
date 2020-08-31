<?php

namespace SharedContext\Domain\Model\SharedEntity\FormRecordData;

use SharedContext\Domain\Model\SharedEntity\FileInfo;

interface IFileInfoFinder
{
    public function ofId(string $fileInfoId): FileInfo;
}
