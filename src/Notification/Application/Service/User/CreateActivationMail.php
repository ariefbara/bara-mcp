<?php

namespace Notification\Application\Service\User;

class CreateActivationMail
{

    /**
     *
     * @var UserMailRepository
     */
    protected $userMailRepository;

    /**
     *
     * @var UserRepository
     */
    protected $userRepository;

    public function __construct(UserMailRepository $userMailRepository, UserRepository $userRepository)
    {
        $this->userMailRepository = $userMailRepository;
        $this->userRepository = $userRepository;
    }
    
    public function execute(string $userId): void
    {
        $id = $this->userMailRepository->nextIdentity();
        $userMail = $this->userRepository->ofId($userId)->createActivationMail($id);
        $this->userMailRepository->add($userMail);
    }

}
