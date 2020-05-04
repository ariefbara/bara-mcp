<?php

namespace Firm\Application\Service\Firm;

use Firm\ {
    Application\Service\FirmRepository,
    Domain\Model\Firm\Personnel,
    Domain\Model\Firm\PersonnelData
};
use Resources\Exception\RegularException;

class PersonnelAdd
{

    protected $personnelRepository;
    protected $firmRepository;

    function __construct(PersonnelRepository $personnelRepository, FirmRepository $firmRepository)
    {
        $this->personnelRepository = $personnelRepository;
        $this->firmRepository = $firmRepository;
    }
    
    public function execute($firmId, PersonnelData $personnelData): string
    {
        if (!$this->personnelRepository->isEmailAvailable($firmId, $personnelData->getEmail())) {
            $errorDetail = "conflict: email already registered";
            throw RegularException::conflict($errorDetail);
        }
        
        $firm = $this->firmRepository->ofId($firmId);
        $id = $this->personnelRepository->nextIdentity();
        
        $personnel = new Personnel($firm, $id, $personnelData);
        $this->personnelRepository->add($personnel);
        
        return $id;
    }

}
