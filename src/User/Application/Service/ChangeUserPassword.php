<?php

namespace User\Application\Service;

class ChangeUserPassword
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
    
    public function execute(string $userId, string $previousPassword, string $newPassword): void
    {
        $this->userRepository->ofId($userId)
                ->changePassword($previousPassword, $newPassword);
        $this->userRepository->update();
    }

}
