<?php

namespace Personnel\Domain\Service;

use Query\Application\Service\Firm\Personnel\PersonnelCompositionId;
use Shared\Domain\Model\ {
    FileInfo,
    FormRecordData\IFileInfoFinder
};

class PersonnelFileInfoFinder implements IFileInfoFinder
{

    /**
     *
     * @var PersonnelFileInfoRepository
     */
    protected $personnelFileInfoRepository;

    /**
     *
     * @var PersonnelCompositionId
     */
    protected $personnelCompositionId;

    function __construct(PersonnelFileInfoRepository $personnelFileInfoRepository,
            PersonnelCompositionId $personnelCompositionId)
    {
        $this->personnelFileInfoRepository = $personnelFileInfoRepository;
        $this->personnelCompositionId = $personnelCompositionId;
    }

    public function ofId(string $fileInfoId): FileInfo
    {
        return $this->personnelFileInfoRepository->fileInfoOf($this->personnelCompositionId, $fileInfoId);
    }

}
