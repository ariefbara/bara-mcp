<?php

namespace Query\Application\Service\Firm\Personnel;

use Query\Domain\Model\Firm\Personnel\PersonnelFileInfo;

class PersonnelFileInfoView
{

    /**
     *
     * @var PersonnelFileInfoRepository
     */
    protected $personnelFileInfoRepository;

    function __construct(PersonnelFileInfoRepository $personnelFileInfoRepository)
    {
        $this->personnelFileInfoRepository = $personnelFileInfoRepository;
    }

    public function showById(PersonnelCompositionId $personnelCompositionId, string $personnelFileInfoId): PersonnelFileInfo
    {
        return $this->personnelFileInfoRepository->ofId($personnelCompositionId, $personnelFileInfoId);
    }

}
