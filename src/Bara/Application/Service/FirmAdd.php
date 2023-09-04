<?php

namespace Bara\Application\Service;

use Bara\Domain\Model\Firm;
use Bara\Domain\Model\Firm\ManagerData;
use Bara\Domain\Model\FirmData;
use Resources\Application\Event\Dispatcher;
use Resources\Exception\RegularException;

class FirmAdd
{

    protected $firmRepository;
    protected $dispatcher;

    function __construct(FirmRepository $firmRepository, Dispatcher $dispatcher)
    {
        $this->firmRepository = $firmRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(FirmData $firmData, ManagerData $managerData): string
    {
        $this->assertIdentifierAvailable($firmData->getIdentifier());

        $id = $this->firmRepository->nextIdentity();
        $firm = new Firm($id, $firmData, $managerData);
        $this->firmRepository->add($firm);
        
        $this->dispatcher->dispatch($firm);
        return $id;
    }

    private function assertIdentifierAvailable($identifier): void
    {
        if ($this->firmRepository->containRecordOfIdentifier($identifier)) {
            $errorDetail = 'conflict: firm identifier already used';
            throw RegularException::conflict($errorDetail);
        }
    }
}
