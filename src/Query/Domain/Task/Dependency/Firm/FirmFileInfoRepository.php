<?php

namespace Query\Domain\Task\Dependency\Firm;

use Query\Domain\Model\Firm\FirmFileInfo;

interface FirmFileInfoRepository
{

    public function ofId(string $id): FirmFileInfo;

    public function aFirmFileInfoInFirm(string $firmId, string $id): FirmFileInfo;
}
