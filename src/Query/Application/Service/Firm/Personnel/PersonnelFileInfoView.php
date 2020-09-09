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

    public function showById(string $firmId, string $personnelId, string $personnelFileInfoId): PersonnelFileInfo
    {
        return $this->personnelFileInfoRepository->ofId($firmId, $personnelId, $personnelFileInfoId);
    }
}
