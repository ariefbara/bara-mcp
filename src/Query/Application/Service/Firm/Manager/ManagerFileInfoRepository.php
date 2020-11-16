<?php

namespace Query\Application\Service\Firm\Manager;

use Query\Domain\Model\Firm\Manager\ManagerFileInfo;

interface ManagerFileInfoRepository
{
    public function aManagerFileInfoBelongsToManager(string $firmId, string $managerId, string $managerFileInfoId): ManagerFileInfo;
}
