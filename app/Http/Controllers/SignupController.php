<?php

namespace App\Http\Controllers;

use Client\Application\Service\ClientSignup;
use Client\Domain\Model\Client;
use Client\Domain\Model\ClientData;
use Config\EventList;
use Notification\Application\Listener\Client\ActivationCodeGeneratedListener;
use Notification\Application\Listener\User\ActivationCodeGeneratedListener as ActivationCodeGeneratedListener2;
use Notification\Application\Service\Client\CreateActivationMail;
use Notification\Application\Service\User\CreateActivationMail as CreateActivationMail2;
use Notification\Domain\Model\Firm\Client as Client2;
use Notification\Domain\Model\Firm\Client\ClientMail;
use Notification\Domain\Model\User as User2;
use Notification\Domain\Model\User\UserMail;
use Query\Domain\Model\Firm;
use Resources\Application\Event\Dispatcher;
use User\Application\Service\UserSignup;
use User\Domain\Model\User;
use User\Domain\Model\UserData;
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

        $this->sendAndCloseConnection(null, 201);
        $this->sendImmediateMail();
    }

    public function userSignup()
    {
        $service = $this->buildUserSignup();

        $firstName = $this->stripTagsInputRequest('firstName');
        $lastName = $this->stripTagsInputRequest('lastName');
        $email = $this->stripTagsInputRequest('email');
        $password = $this->stripTagsInputRequest('password');

        $userData = new UserData($firstName, $lastName, $email, $password);
        $service->execute($userData);
        
        $this->sendAndCloseConnection(null, 201);
sleep(15);
        $this->sendImmediateMail();
    }

    protected function buildClientSignup()
    {
        $clientRepository = $this->em->getRepository(Client::class);
        $firmRepository = $this->em->getRepository(Firm::class);
        $dispatcher = new Dispatcher();
        $dispatcher->addListener(
                EventList::CLIENT_ACTIVATION_CODE_GENERATED, $this->buildClientActivationCodeGeneratedListener());

        return new ClientSignup($clientRepository, $firmRepository, $dispatcher);
    }

    protected function buildClientActivationCodeGeneratedListener()
    {
        $clientMailRepository = $this->em->getRepository(ClientMail::class);
        $clientRepository = $this->em->getRepository(Client2::class);
        $createActivationMail = new CreateActivationMail($clientMailRepository, $clientRepository);
        return new ActivationCodeGeneratedListener($createActivationMail);
    }

    protected function buildUserSignup()
    {
        $userRepository = $this->em->getRepository(User::class);
        $dispatcher = new Dispatcher();
        $dispatcher->addListener(
                EventList::USER_ACTIVATION_CODE_GENERATED, $this->buildUserActivationCodeGeneratedListener());

        return new UserSignup($userRepository, $dispatcher);
    }

    protected function buildUserActivationCodeGeneratedListener()
    {
        $userMailRepository = $this->em->getRepository(UserMail::class);
        $userRepository = $this->em->getRepository(User2::class);
        $createActivationMail = new CreateActivationMail2($userMailRepository, $userRepository);
        return new ActivationCodeGeneratedListener2($createActivationMail);
    }

}
