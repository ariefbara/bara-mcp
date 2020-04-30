<?php

namespace Personnel\Application\Service\Firm;

class PersonnelChangePassword
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
    
    public function execute(string $firmId, string $personnelId, string $previousPassword, string $newPassword): void
    {
        $this->personnelRepository->ofId($firmId, $personnelId)->changePassword($previousPassword, $newPassword);
        $this->personnelRepository->update();
    }

}
