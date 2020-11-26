<?php

namespace User\Application\Service\Manager;

class ResetPassword
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
    
    public function execute(string $firmIdentifier, string $email, string $resetPasswordCode, string $password): void
    {
        $this->managerRepository->aManagerInFirmByEmailAndIdentifier($firmIdentifier, $email)
                ->resetPassword($resetPasswordCode, $password);
        $this->managerRepository->update();
    }

}
