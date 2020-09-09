<?php

namespace Personnel\Application\Service\Firm\Personnel;

use Personnel\ {
    Application\Service\Firm\PersonnelRepository,
    Domain\Model\Firm\Personnel\PersonnelFileInfo
};
use SharedContext\Domain\ {
    Model\SharedEntity\FileInfo,
    Model\SharedEntity\FileInfoData,
    Service\UploadFile
};

class PersonnelFileUpload
{

    /**
     *
     * @var PersonnelFileInfoRepository
     */
    protected $personnelFileInfoRepository;

    /**
     *
     * @var PersonnelRepository
     */
    protected $personnelRepository;

    /**
     *
     * @var UploadFile
     */
    protected $uploadFile;

    function __construct(PersonnelFileInfoRepository $personnelFileInfoRepository,
            PersonnelRepository $personnelRepository, UploadFile $uploadFile)
    {
        $this->personnelFileInfoRepository = $personnelFileInfoRepository;
        $this->personnelRepository = $personnelRepository;
        $this->uploadFile = $uploadFile;
    }

    public function execute(string $firmId, string $personnelId, FileInfoData $fileInfoData, $contents): string
    {
        $personnel = $this->personnelRepository->ofId($firmId, $personnelId);
        $id = $this->personnelFileInfoRepository->nextIdentity();
        $fileInfo = new FileInfo($id, $fileInfoData);

        $this->uploadFile->execute($fileInfo, $contents);

        $personnelFileInfo = new PersonnelFileInfo($personnel, $id, $fileInfo);
        $this->personnelFileInfoRepository->add($personnelFileInfo);
        return $id;
    }

}
