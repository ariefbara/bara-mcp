<?php

namespace Personnel\Domain\Service;

use Personnel\Application\Service\Firm\Personnel\PersonnelCompositionId;
use Shared\Domain\Model\FileInfo;

interface PersonnelFileInfoRepository
{

    public function fileInfoOf(PersonnelCompositionId $personnelCompositionId, string $personnelFileInfoId): FileInfo;
}
