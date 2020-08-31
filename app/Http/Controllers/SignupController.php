<?php

namespace App\Http\Controllers;

use Bara\ {
    Application\Listener\SendMailWhenUserActivationCodeGeneratedListener,
    Application\Service\SendUserActivationCodeMail,
    Domain\Model\User as User2
};
use Firm\ {
    Application\Listener\SendMailOnClientActivationCodeGeneratedListener,
    Application\Service\Firm\ClientSignup,
    Application\Service\Firm\SendClientActivationCodeMail,
    Domain\Event\ClientSignupAcceptedEvent,
    Domain\Model\Firm,
    Domain\Model\Firm\Client,
    Domain\Model\Firm\ClientData
};
use Resources\Application\Event\Dispatcher;
use User\ {
    Application\Service\UserSignup,
    Domain\Event\UserActivationCodeGenerated,
    Domain\Model\User,
    Domain\Model\UserData
};
use function response;

class SignupController extends Controller
{

    public function clientSignup()
    {
        $clientRepository = $this->em->getRepository(Client::class);
        $firmRepository = $this->em->getRepository(Firm::class);
        $dipatcher = new Dispatcher();
        
        $mailer = SwiftMailerBuilder::build();
        $sendClientActivationCodeMail = new SendClientActivationCodeMail($clientRepository, $mailer);
        $listener = new SendMailOnClientActivationCodeGeneratedListener($sendClientActivationCodeMail);
        $dipatcher->addListener(ClientSignupAcceptedEvent::EVENT_NAME, $listener);
        
        $service = new ClientSignup($clientRepository, $firmRepository, $dipatcher);
        
        $firmIdentifier = $this->stripTagsInputRequest('firmIdentifier');
        $firstName = $this->stripTagsInputRequest('firstName');
        $lastName = $this->stripTagsInputRequest('lastName');
        $email = $this->stripTagsInputRequest('email');
        $password = $this->stripTagsInputRequest('password');
        $clientData = new ClientData($firstName, $lastName, $email, $password);
        
        $service->execute($firmIdentifier, $clientData);
        
        $content = [
            "meta" => [
                "code" => 201,
                "type" => "Created",
            ]
        ];
        return response()->json($content, 201);
    }
    
    public function userSignup()
    {
        $userRepository = $this->em->getRepository(User::class);
        $dispatcher = new Dispatcher();
        
        $listener = new SendMailWhenUserActivationCodeGeneratedListener($this->buildSendUserActivationCodeMail());
        $dispatcher->addListener(UserActivationCodeGenerated::EVENT_NAME, $listener);
        
        
        $firstName = $this->stripTagsInputRequest('firstName');
        $lastName = $this->stripTagsInputRequest('lastName');
        $email = $this->stripTagsInputRequest('email');
        $password = $this->stripTagsInputRequest('password');
        
        $userData = new UserData($firstName, $lastName, $email, $password);
        $service = new UserSignup($userRepository, $dispatcher);
        $service->execute($userData);
        
        $content = [
            "meta" => [
                "code" => 201,
                "type" => "Created",
            ]
        ];
        return response()->json($content, 201);
    }
    
    protected function buildSendUserActivationCodeMail()
    {
        $userRepository = $this->em->getRepository(User2::class);
        $mailer = SwiftMailerBuilder::build();
        return new SendUserActivationCodeMail($userRepository, $mailer);
    }

}
