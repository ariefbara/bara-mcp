<?php

namespace Personnel\Application\Service\Firm;

use Personnel\Domain\Model\Firm\Personnel;

class PersonnelUpdateProfile
{
    /**
     *
     * @var PersonnelRepository
     */
    protected $personnelRepository;
    
    function __construct(PersonnelRepository $personnelRepository)
    {
        $this->personnelRepository = $personnelRepository;
    }
    
    public function execute(string $firmId, string $personnelId, string $name, ?string $phone): Personnel
    {
        $personnel = $this->personnelRepository->ofId($firmId, $personnelId);
        $personnel->updateProfile($name, $phone);
        $this->personnelRepository->update();
        return $personnel;
    }

}
