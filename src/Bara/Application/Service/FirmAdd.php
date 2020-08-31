<?php

namespace Bara\Application\Service;

use Bara\Domain\Model\ {
    Firm,
    Firm\ManagerData,
    FirmData
};
use Resources\Exception\RegularException;

class FirmAdd
{

    protected $firmRepository;

    function __construct(FirmRepository $firmRepository)
    {
        $this->firmRepository = $firmRepository;
    }

    public function execute(FirmData $firmData,  ManagerData $managerData): string
    {
        $this->assertIdentifierAvailable($firmData->getIdentifier());
        
        $id = $this->firmRepository->nextIdentity();
        $firm = new Firm($id, $firmData, $managerData);
        $this->firmRepository->add($firm);
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
