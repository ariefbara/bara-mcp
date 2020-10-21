<?php

namespace App\Http\Controllers;

use Bara\ {
    Application\Service\SendUserActivationCodeMail,
    Domain\Model\User as User2
};
use Client\ {
    Application\Service\ClientSignup,
    Domain\Model\Client,
    Domain\Model\ClientData
};
use Config\EventList;
use Notification\ {
    Application\Listener\Client\ActivationCodeGeneratedListener,
    Application\Service\Client\CreateActivationMail,
    Domain\Model\Firm\Client as Client2,
    Domain\Model\Firm\Client\ClientMail
};
use Query\Domain\Model\Firm;
use Resources\Application\Event\Dispatcher;
use User\ {
    Application\Service\UserSignup,
    Domain\Model\User,
    Domain\Model\UserData
};
use function response;

class SignupController extends Controller
{

    public function clientSignup()
    {
        $service = $this->buildClientSignup();

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

//        $listener = new SendMailWhenUserActivationCodeGeneratedListener($this->buildSendUserActivationCodeMail());
//        $dispatcher->addListener(UserActivationCodeGenerated::EVENT_NAME, $listener);

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

    protected function buildClientSignup()
    {
        $clientRepository = $this->em->getRepository(Client::class);
        $firmRepository = $this->em->getRepository(Firm::class);
        $dispatcher = new Dispatcher();
        $dispatcher->addListener(
                EventList::CLIENT_ACTIVATION_CODE_GENERATED, $this->buildActivationCodeGeneratedListener());
        
        return new ClientSignup($clientRepository, $firmRepository, $dispatcher);
    }

    protected function buildActivationCodeGeneratedListener()
    {
        $clientMailRepository = $this->em->getRepository(ClientMail::class);
        $clientRepository = $this->em->getRepository(Client2::class);
        $createActivationMail = new CreateActivationMail($clientMailRepository, $clientRepository);
        return new ActivationCodeGeneratedListener($createActivationMail, $this->buildSendImmediateMail());
    }

    protected function buildSendUserActivationCodeMail()
    {
        $userRepository = $this->em->getRepository(User2::class);
        $mailer = SwiftMailerBuilder::build();
        return new SendUserActivationCodeMail($userRepository, $mailer);
    }

}
