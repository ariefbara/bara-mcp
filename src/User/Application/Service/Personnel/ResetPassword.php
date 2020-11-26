<?php

namespace User\Application\Service\Personnel;

class ResetPassword
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
    
    public function execute(string $firmIdentifier, string $email, string $resetPasswordCode, string $password): void
    {
        $this->personnelRepository->aPersonnelInFirmByEmailAndIdentifier($firmIdentifier, $email)
                ->resetPassword($resetPasswordCode, $password);
        
        $this->personnelRepository->update();
    }

}
