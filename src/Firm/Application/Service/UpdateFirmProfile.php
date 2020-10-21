<?php

namespace Firm\Application\Service;

use Firm\Application\Service\Firm\FirmFileInfoRepository;

class UpdateFirmProfile
{
    /**
     *
     * @var FirmRepository
     */
    protected $firmRepository;
    /**
     *
     * @var FirmFileInfoRepository
     */
    protected $firmFileInfoRepository;
    
    public function __construct(FirmRepository $firmRepository, FirmFileInfoRepository $firmFileInfoRepository)
    {
        $this->firmRepository = $firmRepository;
        $this->firmFileInfoRepository = $firmFileInfoRepository;
    }
    
    public function execute(string $firmId, ?string $firmFileInfoId, ?string $displaySetting): void
    {
        $logo = empty($firmFileInfoId)? null:
                $this->firmFileInfoRepository->aFirmFileInfoBelongsToFirm($firmId, $firmFileInfoId);
        $this->firmRepository->ofId($firmId)->updateProfile($logo, $displaySetting);
        $this->firmRepository->update();
    }

}
