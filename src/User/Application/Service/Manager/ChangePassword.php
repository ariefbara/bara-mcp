<?php

namespace User\Application\Service\Manager;

class ChangePassword
{

    /**
     *
     * @var ManagerRepository
     */
    protected $managerRepository;

    function __construct(ManagerRepository $managerRepository)
    {
        $this->managerRepository = $managerRepository;
    }
    
    public function execute(string $firmId, string $managerId, string $oldPassword, string $newPassword): void
    {
        $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->changePassword($oldPassword, $newPassword);
        $this->managerRepository->update();
    }

}
