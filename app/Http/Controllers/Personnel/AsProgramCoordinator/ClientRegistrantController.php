<?php

namespace App\Http\Controllers\Personnel\AsProgramCoordinator;

use App\Http\Controllers\SwiftMailerBuilder;
use Config\EventList;
use Firm\ {
    Application\Listener\Firm\Program\SendMailWhenClientRegistrationAcceptedListener,
    Application\Service\Firm\Program\AcceptClientRegistration,
    Application\Service\Firm\Program\RejectClientRegistration,
    Application\Service\Firm\Program\SendClientRegistrationAcceptedMail,
    Domain\Model\Firm\Program,
    Domain\Model\Firm\Program\ClientParticipant,
    Domain\Model\Firm\Program\ClientRegistrant as ClientRegistrant2
};
use Query\ {
    Application\Service\Firm\Program\ViewClientRegistrant,
    Domain\Model\Firm\Program\ClientRegistrant
};
use Resources\Application\Event\Dispatcher;

class ClientRegistrantController extends AsProgramCoordinatorBaseController
{

    public function accept($programId, $clientRegistrantId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $service = $this->buildAcceptService();
        $service->execute($this->firmId(), $programId, $clientRegistrantId);
        
        return $this->show($programId, $clientRegistrantId);
    }

    public function reject($programId, $clientRegistrantId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $service = $this->buildRejectService();
        $service->execute($this->firmId(), $programId, $clientRegistrantId);
        
        return $this->show($programId, $clientRegistrantId);
    }

    public function show($programId, $clientRegistrantId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $service = $this->buildViewService();
        $clientRegistrant = $service->showById($this->firmId(), $programId, $clientRegistrantId);
        
        return $this->singleQueryResponse($this->arrayDataOfClientRegistrant($clientRegistrant));
    }

    public function showAll($programId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $service = $this->buildViewService();
        $clientRegistrants = $service->showAll($this->firmId(), $programId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result['total'] = count($clientRegistrants);
        foreach ($clientRegistrants as $clientRegistrant) {
            $result['list'][] = $this->arrayDataOfClientRegistrant($clientRegistrant);
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfClientRegistrant(ClientRegistrant $clientRegistrant): array
    {
        return [
            "id" => $clientRegistrant->getId(),
            "client" => [
                "id" => $clientRegistrant->getClient()->getId(),
                "name" => $clientRegistrant->getClient()->getFullName(),
            ],
            "registeredTime" => $clientRegistrant->getRegisteredTimeString(),
            "concluded" => $clientRegistrant->isConcluded(),
            "note" => $clientRegistrant->getNote(),
        ];
    }
    
    protected function buildViewService()
    {
        $clientRegistrantRepository = $this->em->getRepository(ClientRegistrant::class);
        return new ViewClientRegistrant($clientRegistrantRepository);
    }
    protected function buildAcceptService()
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant::class);
        $mailer = SwiftMailerBuilder::build();
        
        $sendClientRegistrationAcceptedMail = new SendClientRegistrationAcceptedMail($clientParticipantRepository, $mailer);
        $listener = new SendMailWhenClientRegistrationAcceptedListener($sendClientRegistrationAcceptedMail);
        
        $programRepository = $this->em->getRepository(Program::class);
        $dispatcher = new Dispatcher();
        $dispatcher->addListener(EventList::CLIENT_REGISTRATION_ACCEPTED, $listener);
        
        return new AcceptClientRegistration($programRepository, $dispatcher);
    }
    protected function buildRejectService()
    {
        $clientRegistrantRepository = $this->em->getRepository(ClientRegistrant2::class);
        return new RejectClientRegistration($clientRegistrantRepository);
    }

}
