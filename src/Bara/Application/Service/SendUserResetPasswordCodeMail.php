<?php

namespace Bara\Application\Service;

use Resources\Application\Service\Mailer;

class SendUserResetPasswordCodeMail
{

    /**
     *
     * @var UserRepository
     */
    protected $userRepository;

    /**
     *
     * @var Mailer
     */
    protected $mailer;

    public function __construct(UserRepository $userRepository, Mailer $mailer)
    {
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
    }
    
    public function execute(string $userId): void
    {
        $this->userRepository->ofId($userId)->sendResetPasswordCodeMail($this->mailer);
    }

}
