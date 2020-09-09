<?php

namespace Personnel\Application\Service\Firm;

use Personnel\Domain\Model\Firm\PersonnelProfileData;

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
    
    public function execute(string $firmId, string $personnelId, PersonnelProfileData $personnelProfileData): void
    {
        $personnel = $this->personnelRepository->ofId($firmId, $personnelId);
        $personnel->updateProfile($personnelProfileData);
        $this->personnelRepository->update();
    }

}
