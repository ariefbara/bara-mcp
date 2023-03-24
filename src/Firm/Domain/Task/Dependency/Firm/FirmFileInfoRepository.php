<?php

namespace Firm\Domain\Task\Dependency\Firm;

use Firm\Domain\Model\Firm\FirmFileInfo;

interface FirmFileInfoRepository
{

    public function nextIdentity(): string;

    public function add(FirmFileInfo $firmFileInfo): void;

    public function ofId(string $id): FirmFileInfo;
}
