<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Config\EventList;
use Notification\{
    Application\Listener\User\ActivationCodeGeneratedListener,
    Application\Listener\User\ResetPasswordCodeGeneratedListener,
    Application\Service\User\CreateActivationMail,
    Application\Service\User\CreateResetPasswordMail,
    Domain\Model\User as User2,
    Domain\Model\User\UserMail
};
use Resources\Application\Event\Dispatcher;
use User\{
    Application\Service\ActivateUser,
    Application\Service\GenerateUserActivationCode,
    Application\Service\GenerateUserResetPasswordCode,
    Application\Service\ResetUserPassword,
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
        $service = $this->buildGenerateActivationCodeService();
        $email = $this->stripTagsInputRequest('email');
        $service->execute($email);
        
        $response = $this->commandOkResponse();
        $this->sendAndCloseConnection($response, $this->buildSendImmediateMailJob());
    }

    public function generateResetPasswordCode()
    {
        $service = $this->buildGenerateResetPasswordCodeService();
        $email = $this->stripTagsInputRequest('email');
        $service->execute($email);
        
        $response = $this->commandOkResponse();
        $this->sendAndCloseConnection($response, $this->buildSendImmediateMailJob());

    }

    protected function buildGenerateActivationCodeService()
    {
        $userRepository = $this->em->getRepository(User::class);
        $dispatcher = new Dispatcher();
        $dispatcher->addListener(
                EventList::USER_ACTIVATION_CODE_GENERATED, $this->buildActivationCodeGeneratedListener());

        return new GenerateUserActivationCode($userRepository, $dispatcher);
    }
    protected function buildActivationCodeGeneratedListener()
    {
        $userMailRepository = $this->em->getRepository(UserMail::class);
        $userRepository = $this->em->getRepository(User2::class);
        $createActivationMail = new CreateActivationMail($userMailRepository, $userRepository);

        return new ActivationCodeGeneratedListener($createActivationMail);
    }

    protected function buildGenerateResetPasswordCodeService()
    {
        $userRepository = $this->em->getRepository(User::class);
        $dispatcher = new Dispatcher();
        $dispatcher->addListener(
                EventList::USER_RESET_PASSWORD_CODE_GENERATED, $this->buildResetPasswordCodeGeneratedListener());
        
        return new GenerateUserResetPasswordCode($userRepository, $dispatcher);
    }
    protected function buildResetPasswordCodeGeneratedListener()
    {
        $userMailRepository = $this->em->getRepository(UserMail::class);
        $userRepository = $this->em->getRepository(User2::class);
        $createResetPasswordMail = new CreateResetPasswordMail($userMailRepository, $userRepository);
        return new ResetPasswordCodeGeneratedListener($createResetPasswordMail);
    }

}
