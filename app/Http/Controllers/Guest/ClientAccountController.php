<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Client\{
    Application\Service\ActivateAccount,
    Application\Service\GenerateActivationCode,
    Application\Service\GenerateResetPasswordCode,
    Application\Service\ResetPassword,
    Domain\Model\Client
};
use Config\EventList;
use Notification\{
    Application\Listener\Client\ActivationCodeGeneratedListener,
    Application\Listener\Client\ResetPasswordCodeGeneratedListener,
    Application\Service\Client\CreateActivationMail,
    Application\Service\Client\CreateClientResetPasswordMail,
    Domain\Model\Firm\Client as Client2,
    Domain\Model\Firm\Client\ClientMail
};
use Resources\Application\Event\Dispatcher;

class ClientAccountController extends Controller
{

    public function activate()
    {
        $clientRepository = $this->em->getRepository(Client::class);
        $service = new ActivateAccount($clientRepository);

        $firmIdentifier = $this->stripTagsInputRequest('firmIdentifier');
        $email = $this->stripTagsInputRequest('email');
        $activationCode = $this->stripTagsInputRequest('activationCode');

        $service->execute($firmIdentifier, $email, $activationCode);
        return $this->commandOkResponse();
    }

    public function resetPassword()
    {
        $clientRepository = $this->em->getRepository(Client::class);
        $service = new ResetPassword($clientRepository);

        $firmIdentifier = $this->stripTagsInputRequest('firmIdentifier');
        $email = $this->stripTagsInputRequest('email');
        $resetPasswordCode = $this->stripTagsInputRequest('resetPasswordCode');
        $password = $this->stripTagsInputRequest('password');

        $service->execute($firmIdentifier, $email, $resetPasswordCode, $password);
        return $this->commandOkResponse();
    }

    public function generateActivationCode()
    {
        $service = $this->buildGenerateActivationCodeService();

        $firmIdentifier = $this->stripTagsInputRequest('firmIdentifier');
        $email = $this->stripTagsInputRequest('email');

        $service->execute($firmIdentifier, $email);
        
        $this->sendAndCloseConnection();
        $this->sendImmediateMail();
    }

    public function generateResetPasswordCode()
    {
        $service = $this->buildGenerateResetPasswordCodeService();
        
        $firmIdentifier = $this->stripTagsInputRequest('firmIdentifier');
        $email = $this->stripTagsInputRequest('email');
        
        $service->execute($firmIdentifier, $email);
        
        $this->sendAndCloseConnection();
        $this->sendImmediateMail();
    }

    protected function buildGenerateActivationCodeService()
    {
        $clientRepository = $this->em->getRepository(Client::class);
        $dispatcher = new Dispatcher();
        $dispatcher->addListener(
                EventList::CLIENT_ACTIVATION_CODE_GENERATED, $this->buildActivationCodeGeneratedListener());

        return new GenerateActivationCode($clientRepository, $dispatcher);
    }
    protected function buildActivationCodeGeneratedListener()
    {
        $clientMailRepository = $this->em->getRepository(ClientMail::class);
        $clientRepository = $this->em->getRepository(Client2::class);
        $createActivationMail = new CreateActivationMail($clientMailRepository, $clientRepository);
        return new ActivationCodeGeneratedListener($createActivationMail);
    }

    protected function buildGenerateResetPasswordCodeService()
    {
        $clientRepository = $this->em->getRepository(Client::class);
        $dispatcher = new Dispatcher();
        $dispatcher->addListener(
                EventList::CLIENT_RESET_PASSWORD_CODE_GENERATED, $this->buildResetPasswordCodeGeneratedListener());

        return new GenerateResetPasswordCode($clientRepository, $dispatcher);
    }
    protected function buildResetPasswordCodeGeneratedListener()
    {
        $clientMailRepository = $this->em->getRepository(ClientMail::class);
        $clientRepository = $this->em->getRepository(Client2::class);
        $createClientResetPasswordMail = new CreateClientResetPasswordMail($clientMailRepository, $clientRepository);
        return new ResetPasswordCodeGeneratedListener($createClientResetPasswordMail);
    }

}
