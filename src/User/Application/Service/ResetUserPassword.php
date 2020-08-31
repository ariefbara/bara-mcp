<?php

namespace User\Application\Service;

class ResetUserPassword
{
    /**
     *
     * @var UserRepository
     */
    protected $userRepository;
    
    function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    public function execute(string $email, string $resetPasswordCode, string $password): void
    {
        $this->userRepository->ofEmail($email)
                ->resetPassword($resetPasswordCode, $password);
        $this->userRepository->update();
    }

}
