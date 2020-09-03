<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\ {
    Controller,
    SwiftMailerBuilder
};
use Client\ {
    Application\Service\ActivateAccount,
    Application\Service\GenerateActivationCode,
    Application\Service\GenerateResetPasswordCode,
    Application\Service\ResetPassword,
    Domain\Event\ClientActivationCodeGenerated,
    Domain\Event\ClientResetPasswordCodeGenerated,
    Domain\Model\Client
};
use Firm\ {
    Application\Listener\SendMailOnClientActivationCodeGeneratedListener,
    Application\Listener\SendMailOnClientResetPasswordCodeGeneratedListener,
    Application\Service\Firm\SendClientActivationCodeMail,
    Application\Service\Firm\SendClientResetPasswordCodeMail,
    Domain\Model\Firm\Client as Client2
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
        $clientRepository = $this->em->getRepository(Client::class);
        $dispatcher = new Dispatcher();
        
//        $listener = $this->buildSendMailOnClientActivationCodeGeneratedListener();
//        $dispatcher->addListener(ClientActivationCodeGenerated::EVENT_NAME, $listener);
        
        $service = new GenerateActivationCode($clientRepository, $dispatcher);
        
        $firmIdentifier = $this->stripTagsInputRequest('firmIdentifier');
        $email = $this->stripTagsInputRequest('email');
        
        $service->execute($firmIdentifier, $email);
        
        return $this->commandOkResponse();
    }

    public function generateResetPasswordCode()
    {
        $clientRepository = $this->em->getRepository(Client::class);
        $dispatcher = new Dispatcher();
        
//        $listener = $this->buildSendMainOnClientResetPasswordCodeGeneratedListener();
//        $dispatcher->addListener(ClientResetPasswordCodeGenerated::EVENT_NAME, $listener);
        
        $service = new GenerateResetPasswordCode($clientRepository, $dispatcher);
        
        $firmIdentifier = $this->stripTagsInputRequest('firmIdentifier');
        $email = $this->stripTagsInputRequest('email');
        
        $service->execute($firmIdentifier, $email);
        
        return $this->commandOkResponse();
    }
    
    protected function buildSendMailOnClientActivationCodeGeneratedListener()
    {
        $clientRepository = $this->em->getRepository(Client2::class);
        $mailer = SwiftMailerBuilder::build();
        
        $sendClientActivationCodeMail = new SendClientActivationCodeMail($clientRepository, $mailer);
        return new SendMailOnClientActivationCodeGeneratedListener($sendClientActivationCodeMail);
    }
    
    protected function buildSendMainOnClientResetPasswordCodeGeneratedListener()
    {
        $clientRepository = $this->em->getRepository(Client2::class);
        $mailer = SwiftMailerBuilder::build();
        
        $sendClientResetPasswordCodeMail = new SendClientResetPasswordCodeMail($clientRepository, $mailer);
        return new SendMailOnClientResetPasswordCodeGeneratedListener($sendClientResetPasswordCodeMail);
    }

}
