<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\Firm\FirmFileInfo;

interface FirmFileInfoRepository
{

    public function nextIdentity(): string;

    public function add(FirmFileInfo $firmFileInfo): void;

    public function aFirmFileInfoBelongsToFirm(string $firmId, string $firmFileInfoId): FirmFileInfo;
}
