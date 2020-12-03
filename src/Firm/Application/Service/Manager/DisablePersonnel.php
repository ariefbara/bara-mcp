<?php

namespace Firm\Application\Service\Manager;

class DisablePersonnel
{

    /**
     *
     * @var PersonnelRepository
     */
    protected $personnelRepository;

    /**
     *
     * @var ManagerRepository
     */
    protected $managerRepository;

    function __construct(PersonnelRepository $personnelRepository, ManagerRepository $managerRepository)
    {
        $this->personnelRepository = $personnelRepository;
        $this->managerRepository = $managerRepository;
    }
    
    public function execute(string $firmId, string $managerId, string $personnelId): void
    {
        $personnel = $this->personnelRepository->aPersonnelOfId($personnelId);
        $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->disablePersonnel($personnel);
        $this->personnelRepository->update();
    }

}
