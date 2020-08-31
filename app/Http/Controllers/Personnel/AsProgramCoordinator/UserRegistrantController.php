<?php

namespace App\Http\Controllers\Personnel\AsProgramCoordinator;

use App\Http\Controllers\SwiftMailerBuilder;
use Config\EventList;
use Firm\ {
    Application\Listener\Firm\Program\SendMailWhenUserRegistrationAcceptedListener,
    Application\Service\Firm\Program\AcceptUserRegistration,
    Application\Service\Firm\Program\RejectUserRegistration,
    Application\Service\Firm\Program\SendUserRegistrationAcceptedMail,
    Domain\Model\Firm\Program,
    Domain\Model\Firm\Program\UserParticipant,
    Domain\Model\Firm\Program\UserRegistrant as UserRegistrant2
};
use Query\ {
    Application\Service\Firm\Program\ViewUserRegistrant,
    Domain\Model\Firm\Program\UserRegistrant
};
use Resources\Application\Event\Dispatcher;

class UserRegistrantController extends AsProgramCoordinatorBaseController
{
    public function accept($programId, $userRegistrantId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $service = $this->buildAcceptService();
        $service->execute($this->firmId(), $programId, $userRegistrantId);
        
        return $this->show($programId, $userRegistrantId);
    }

    public function reject($programId, $userRegistrantId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $service = $this->buildRejectService();
        $service->execute($this->firmId(), $programId, $userRegistrantId);
        
        return $this->show($programId, $userRegistrantId);
    }

    public function show($programId, $userRegistrantId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $service = $this->buildViewService();
        $userRegistrant = $service->showById($this->firmId(), $programId, $userRegistrantId);
        
        return $this->singleQueryResponse($this->arrayDataOfUserRegistrant($userRegistrant));
    }

    public function showAll($programId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $service = $this->buildViewService();
        $userRegistrants = $service->showAll($this->firmId(), $programId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result['total'] = count($userRegistrants);
        foreach ($userRegistrants as $userRegistrant) {
            $result['list'][] = $this->arrayDataOfUserRegistrant($userRegistrant);
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfUserRegistrant(UserRegistrant $userRegistrant): array
    {
        return [
            "id" => $userRegistrant->getId(),
            "user" => [
                "id" => $userRegistrant->getUser()->getId(),
                "name" => $userRegistrant->getUser()->getFullName(),
            ],
            "registeredTime" => $userRegistrant->getRegisteredTimeString(),
            "concluded" => $userRegistrant->isConcluded(),
            "note" => $userRegistrant->getNote(),
        ];
    }
    
    protected function buildViewService()
    {
        $userRegistrantRepository = $this->em->getRepository(UserRegistrant::class);
        return new ViewUserRegistrant($userRegistrantRepository);
    }
    protected function buildAcceptService()
    {
        $userParticipantRepository = $this->em->getRepository(UserParticipant::class);
        $mailer = SwiftMailerBuilder::build();
        
        $sendUserRegistrationAcceptedMail = new SendUserRegistrationAcceptedMail($userParticipantRepository, $mailer);
        $listener = new SendMailWhenUserRegistrationAcceptedListener($sendUserRegistrationAcceptedMail);
        
        $programRepository = $this->em->getRepository(Program::class);
        $dispatcher = new Dispatcher();
        $dispatcher->addListener(EventList::USER_REGISTRATION_ACCEPTED, $listener);
        
        return new AcceptUserRegistration($programRepository, $dispatcher);
    }
    protected function buildRejectService()
    {
        $userRegistrantRepository = $this->em->getRepository(UserRegistrant2::class);
        return new RejectUserRegistration($userRegistrantRepository);
    }
}
