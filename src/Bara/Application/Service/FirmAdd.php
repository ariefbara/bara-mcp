<?php

namespace Bara\Application\Service;

use Bara\Domain\Model\ {
    Firm,
    Firm\ManagerData
};
use Resources\Exception\RegularException;

class FirmAdd
{

    protected $firmRepository;

    function __construct(FirmRepository $firmRepository)
    {
        $this->firmRepository = $firmRepository;
    }

    public function execute(string $name, string $identifier,  ManagerData $managerData): Firm
    {
        $this->assertIdentifierAvailable($identifier);
        
        $id = $this->firmRepository->nextIdentity();
        $firm = new Firm($id, $name, $identifier, $managerData);
        $this->firmRepository->add($firm);
        return $firm;
    }
    
    private function assertIdentifierAvailable($identifier): void
    {
        if ($this->firmRepository->containRecordOfIdentifier($identifier)) {
            $errorDetail = 'conflict: firm identifier already used';
            throw RegularException::conflict($errorDetail);
        }
    }

}
