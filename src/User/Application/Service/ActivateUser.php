<?php

namespace User\Application\Service;

class ActivateUser
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
    
    public function execute(string $email, string $activationCode): void
    {
        $this->userRepository->ofEmail($email)
                ->activate($activationCode);
        $this->userRepository->update();
    }

}
