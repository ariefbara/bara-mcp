<?php

namespace Query\Domain\Task\InFirm\FirmFileInfo;

use Query\Domain\Model\Firm;
use Query\Domain\Model\Firm\ManagerQueryInFirm;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\FirmFileInfoRepository;

class ViewFirmFileInfoDetail implements ManagerQueryInFirm
{

    protected FirmFileInfoRepository $firmFileInfoRepository;

    public function __construct(FirmFileInfoRepository $firmFileInfoRepository)
    {
        $this->firmFileInfoRepository = $firmFileInfoRepository;
    }

    /**
     * 
     * @param Firm $firm
     * @param CommonViewDetailPayload $payload
     * @return void
     */
    public function executeQueryInFirm(Firm $firm, $payload): void
    {
        $payload->result = $this->firmFileInfoRepository->aFirmFileInfoInFirm($firm->getId(), $payload->getId());
    }

}
