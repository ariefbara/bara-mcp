<?php

namespace User\Application\Service;

use Resources\ {
    Application\Event\Dispatcher,
    Exception\RegularException
};
use User\Domain\Model\ {
    User,
    UserData
};

class UserSignup
{
    /**
     *
     * @var UserRepository
     */
    protected $userRepository;
    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;
    
    public function __construct(UserRepository $userRepository, Dispatcher $dispatcher)
    {
        $this->userRepository = $userRepository;
        $this->dispatcher = $dispatcher;
    }

    
    public function execute(UserData $userData): void
    {
        $this->assertEmailAvailable($userData->getEmail());

        $id = $this->userRepository->nextIdentity();
        $user = new User($id, $userData);
        $this->userRepository->add($user);
        $this->dispatcher->dispatch($user);
    }

    private function assertEmailAvailable(string $email): void
    {
        if ($this->userRepository->containRecordWithEmail($email)) {
            $errorDetail = "conflict: email already registered";
            throw RegularException::conflict($errorDetail);
        }
    }

}
