<?php

namespace User\Application\Service\Manager;

use User\Domain\Model\Manager\ManagerFileInfo;

interface ManagerFileInfoRepository
{
    public function nextIdentity(): string;
    
    public function add(ManagerFileInfo $managerFileInfo): void;
}
