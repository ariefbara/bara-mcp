<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\{
    Controller,
    SwiftMailerBuilder
};
use Bara\{
    Application\Listener\SendMailWhenUserActivationCodeGeneratedListener,
    Application\Listener\SendMailWhenUserResetPasswordCodeGeneratedListener,
    Application\Service\SendUserActivationCodeMail,
    Application\Service\SendUserResetPasswordCodeMail,
    Domain\Model\User as User2
};
use Resources\Application\Event\Dispatcher;
use User\{
    Application\Service\ActivateUser,
    Application\Service\GenerateUserActivationCode,
    Application\Service\GenerateUserResetPasswordCode,
    Application\Service\ResetUserPassword,
    Domain\Event\UserActivationCodeGenerated,
    Domain\Event\UserPasswordResetCodeGenerated,
    Domain\Model\User
};

class UserAccountController extends Controller
{

    public function activate()
    {
        $userRepository = $this->em->getRepository(User::class);
        $service = new ActivateUser($userRepository);

        $email = $this->stripTagsInputRequest('email');
        $activationCode = $this->stripTagsInputRequest('activationCode');

        $service->execute($email, $activationCode);

        return $this->commandOkResponse();
    }

    public function resetPassword()
    {
        $userRepository = $this->em->getRepository(User::class);
        $service = new ResetUserPassword($userRepository);

        $email = $this->stripTagsInputRequest('email');
        $resetPasswordCode = $this->stripTagsInputRequest('resetPasswordCode');
        $password = $this->stripTagsInputRequest('password');
        $service->execute($email, $resetPasswordCode, $password);

        return $this->commandOkResponse();
    }

    public function generateActivationCode()
    {
        $userRepository = $this->em->getRepository(User::class);
        $dispatcher = new Dispatcher();

        $listener = $this->buildSendMailWhenUserActivationCodeGeneratedListener();
        $dispatcher->addListener(UserActivationCodeGenerated::EVENT_NAME, $listener);

        $service = new GenerateUserActivationCode($userRepository, $dispatcher);
        $email = $this->stripTagsInputRequest('email');
        $service->execute($email);

        return $this->commandOkResponse();
    }

    public function generateResetPasswordCode()
    {
        $userRepository = $this->em->getRepository(User::class);
        $dispatcher = new Dispatcher();

        $listener = $this->buildSendMailWhenUserResetPasswordCodeGeneratedListener();
        $dispatcher->addListener(UserPasswordResetCodeGenerated::EVENT_NAME, $listener);

        $service = new GenerateUserResetPasswordCode($userRepository, $dispatcher);
        $email = $this->stripTagsInputRequest('email');
        $service->execute($email);

        return $this->commandOkResponse();
    }

    protected function buildSendMailWhenUserActivationCodeGeneratedListener()
    {
        $userRepository = $this->em->getRepository(User2::class);
        $mailer = SwiftMailerBuilder::build();

        $sendUserActivationCodeMail = new SendUserActivationCodeMail($userRepository, $mailer);
        return new SendMailWhenUserActivationCodeGeneratedListener($sendUserActivationCodeMail);
    }

    protected function buildSendMailWhenUserResetPasswordCodeGeneratedListener()
    {
        $userRepository = $this->em->getRepository(User2::class);
        $mailer = SwiftMailerBuilder::build();

        $sendUserResetPasswordCodeMail = new SendUserResetPasswordCodeMail($userRepository, $mailer);
        return new SendMailWhenUserResetPasswordCodeGeneratedListener($sendUserResetPasswordCodeMail);
    }

}
