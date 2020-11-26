<?php

namespace Notification\Application\Service;

class CreateManagerResetPasswordMail
{

    /**
     *
     * @var ManagerMailRepository
     */
    protected $managerMailRepository;

    /**
     *
     * @var ManagerRepository
     */
    protected $managerRepository;

    function __construct(ManagerMailRepository $managerMailRepository, ManagerRepository $managerRepository)
    {
        $this->managerMailRepository = $managerMailRepository;
        $this->managerRepository = $managerRepository;
    }
    
    public function execute(string $managerId): void
    {
        $id = $this->managerMailRepository->nextIdentity();
        $managerMail = $this->managerRepository->ofId($managerId)->createResetPasswordMail($id);
        $this->managerMailRepository->add($managerMail);
    }

}
