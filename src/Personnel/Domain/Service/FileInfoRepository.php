<?php

namespace Personnel\Domain\Service;

use SharedContext\Domain\Model\SharedEntity\FileInfo;

interface FileInfoRepository
{
    public function aFileInfoOfPersonnel(string $firmId, string $personnelId, string $fileInfoId): FileInfo;
}
