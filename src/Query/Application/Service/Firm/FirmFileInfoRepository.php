<?php

namespace Query\Application\Service\Firm;

use Query\Domain\Model\Firm\FirmFileInfo;

interface FirmFileInfoRepository
{
    public function aFirmFileInfoBelongsToFirm(string $firmId, string $firmFileInfoId): FirmFileInfo;
}
