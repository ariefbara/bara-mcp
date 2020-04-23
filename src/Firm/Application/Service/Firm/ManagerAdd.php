<?php

namespace Firm\Application\Service\Firm;

use Firm\ {
    Application\Service\FirmRepository,
    Domain\Model\Firm\Manager,
    Domain\Model\Firm\ManagerData
};

class ManagerAdd
{

    /**
     *
     * @var ManagerRepository
     */
    protected $managerRepository;

    /**
     *
     * @var FirmRepository
     */
    protected $firmRepository;
    
    function __construct(ManagerRepository $managerRepository, FirmRepository $firmRepository)
    {
        $this->managerRepository = $managerRepository;
        $this->firmRepository = $firmRepository;
    }
    
    public function execute(string $firmId, ManagerData $managerData): Manager
    {
        $this->assertEmailAvailable($firmId, $managerData->getEmail());
        
        $firm = $this->firmRepository->ofId($firmId);
        $id = $this->managerRepository->nextIdentity();
        $manager = new Manager($firm, $id, $managerData);
        $this->managerRepository->add($manager);
        return $manager;
    }
    
    protected function assertEmailAvailable(string $firmId, string $email): void
    {
        if (!$this->managerRepository->isEmailAvailable($firmId, $email)) {
            $errorDetail = "conflict: email already registered";
            throw \Resources\Exception\RegularException::conflict($errorDetail);
        }
    }


}
