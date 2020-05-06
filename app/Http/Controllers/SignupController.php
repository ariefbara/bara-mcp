<?php

namespace App\Http\Controllers;

use Client\ {
    Application\Service\ClientSignup,
    Domain\Event\ClientActivationCodeGenerated,
    Domain\Model\Client
};
use Resources\Application\Event\Dispatcher;
use function response;

class SignupController extends Controller
{

    public function ClientSignup()
    {
        $clientRepository = $this->em->getRepository(Client::class);

        $dispatcher = new Dispatcher();
        $listener = SendMailListenerBuilder::build();
        $dispatcher->addListener(ClientActivationCodeGenerated::EVENT_NAME, $listener);

        $service = new ClientSignup($clientRepository, $dispatcher);
        $name = $this->stripTagsInputRequest('name');
        $email = $this->stripTagsInputRequest('email');
        $password = $this->stripTagsInputRequest('password');

        $service->execute($name, $email, $password);

        $content = [
            "meta" => [
                "code" => 201,
                "type" => "Created",
            ]
        ];
        return response()->json($content, 201);
    }

}
