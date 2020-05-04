<?php

namespace App\Http\Controllers\Personnel\AsProgramCoordinator;

use Client\ {
    Application\Listener\ProgramManageParticipantListener,
    Domain\Model\Client\ClientNotification,
    Domain\Model\Client\ProgramParticipation
};
use Firm\ {
    Application\Service\Firm\Program\ProgramCompositionId,
    Application\Service\Firm\Program\RegistrantAccept,
    Application\Service\Firm\Program\RegistrantReject,
    Domain\Event\ProgramManageParticipantEvent,
    Domain\Model\Firm\Program,
    Domain\Model\Firm\Program\Registrant
};
use Query\ {
    Application\Service\Firm\Program\RegistrantView,
    Domain\Model\Firm\Program\Registrant as Registrant2
};
use Resources\Application\Event\Dispatcher;

class RegistrantController extends AsProgramCoordinatorBaseController
{

    public function accept($programId, $registrantId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);

        $service = $this->buildAcceptService();
        $service->execute($this->firmId(), $programId, $registrantId);
        
        return $this->show($programId, $registrantId);
    }

    public function reject($programId, $registrantId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);

        $service = $this->buildRejectService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);
        $service->execute($programCompositionId, $registrantId);
        
        return $this->show($programId, $registrantId);
    }

    public function show($programId, $registrantId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);

        $service = $this->buildViewService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);
        $registrant = $service->showById($programCompositionId, $registrantId);
        return $this->singleQueryResponse($this->arrayDataOfRegistrant($registrant));
    }

    public function showAll($programId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);

        $service = $this->buildViewService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);
        $registrants = $service->showAll($programCompositionId, $this->getPage(), $this->getPageSize());

        $result = [];
        $result['total'] = count($registrants);
        foreach ($registrants as $registrant) {
            $result['list'][] = $this->arrayDataOfRegistrant($registrant);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfRegistrant(Registrant2 $registrant): array
    {
        return [
            "id" => $registrant->getId(),
            "appliedTime" => $registrant->getAppliedTimeString(),
            "concluded" => $registrant->isConcluded(),
            "note" => $registrant->getNote(),
            "client" => [
                "id" => $registrant->getClient()->getId(),
                "name" => $registrant->getClient()->getName(),
            ],
        ];
    }

    protected function buildAcceptService()
    {
        $programRepository = $this->em->getRepository(Program::class);
        $dispatcher = new Dispatcher();

        $clientNotificationRepository = $this->em->getRepository(ClientNotification::class);
        $programParticipationRepository = $this->em->getRepository(ProgramParticipation::class);
        
        $listener = new ProgramManageParticipantListener($clientNotificationRepository, $programParticipationRepository);
        $dispatcher->addListener(ProgramManageParticipantEvent::EVENT_NAME, $listener);

        return new RegistrantAccept($programRepository, $dispatcher);
    }

    protected function buildRejectService()
    {
        $registrantRepository = $this->em->getRepository(Registrant::class);
        return new RegistrantReject($registrantRepository);
    }

    protected function buildViewService()
    {
        $registrantRepository = $this->em->getRepository(Registrant2::class);
        return new RegistrantView($registrantRepository);
    }

}
