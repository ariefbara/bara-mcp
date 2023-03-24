<?php

namespace Firm\Domain\Task\InFirm\FirmFileInfo;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\ManagerTaskInFirm;
use Firm\Domain\Task\Dependency\Firm\FirmFileInfoRepository;
use Resources\Application\Event\Dispatcher;
use SharedContext\Domain\Model\SharedEntity\FileInfoData;

class CreateFirmFileInfo implements ManagerTaskInFirm
{

    protected FirmFileInfoRepository $firmFileInfoRepository;
    protected Dispatcher $dispatcher;

    public function __construct(FirmFileInfoRepository $firmFileInfoRepository, Dispatcher $dispatcher)
    {
        $this->firmFileInfoRepository = $firmFileInfoRepository;
        $this->dispatcher = $dispatcher;
    }

    /**
     * 
     * @param Firm $firm
     * @param FileInfoData $payload
     * @return void
     */
    public function executeInFirm(Firm $firm, $payload): void
    {
        $payload->setId($this->firmFileInfoRepository->nextIdentity());
        $firmFileInfo = $firm->createFileInfo($payload->id, $payload);
        
        $this->firmFileInfoRepository->add($firmFileInfo);
        
        $this->dispatcher->dispatch($firmFileInfo);
    }

}
