<?php

namespace Query\Application\Service\Firm;

use Query\Domain\Model\Firm\FirmFileInfo;

class ViewFirmFileInfo
{
    /**
     *
     * @var FirmFileInfoRepository
     */
    protected $firmFileInfoRepository;
    
    public function __construct(FirmFileInfoRepository $firmFileInfoRepository)
    {
        $this->firmFileInfoRepository = $firmFileInfoRepository;
    }
    
    public function showById(string $firmId, string $firmFileInfoId): FirmFileInfo
    {
        return $this->firmFileInfoRepository->aFirmFileInfoBelongsToFirm($firmId, $firmFileInfoId);
    }

}
