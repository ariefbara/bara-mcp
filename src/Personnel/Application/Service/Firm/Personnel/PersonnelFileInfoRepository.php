<?php

namespace Personnel\Application\Service\Firm\Personnel;

use Personnel\Domain\Model\Firm\Personnel\PersonnelFileInfo;

interface PersonnelFileInfoRepository
{

    public function nextIdentity(): string;

    public function add(PersonnelFileInfo $personnelFileInfo): void;
}
