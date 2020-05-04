<?php

namespace Query\Application\Service\Firm\Personnel;

use Query\Domain\Model\Firm\Personnel\PersonnelFileInfo;

interface PersonnelFileInfoRepository
{
    public function ofId(PersonnelCompositionId $personnelCompositionId, string $personnelFileInfoId): PersonnelFileInfo;
}
