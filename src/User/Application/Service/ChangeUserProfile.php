<?php

namespace User\Application\Service;

class ChangeUserProfile
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
    
    public function execute(string $userId, string $firstName, string $lastName): void
    {
        $this->userRepository->ofId($userId)
                ->changeProfile($firstName, $lastName);
        $this->userRepository->update();
    }

}
