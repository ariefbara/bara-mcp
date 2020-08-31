<?php

namespace User\Application\Service;

use Resources\Application\Event\Dispatcher;

class GenerateUserResetPasswordCode
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
    
    function __construct(UserRepository $userRepository, Dispatcher $dispatcher)
    {
        $this->userRepository = $userRepository;
        $this->dispatcher = $dispatcher;
    }

    
    public function execute(string $email): void
    {
        $user = $this->userRepository->ofEmail($email);
        $user->generateResetPasswordCode();
        $this->userRepository->update();
        $this->dispatcher->dispatch($user);
    }

}
