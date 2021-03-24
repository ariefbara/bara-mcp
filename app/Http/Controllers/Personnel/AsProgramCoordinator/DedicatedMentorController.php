<?php

namespace App\Http\Controllers\Personnel\AsProgramCoordinator;

use Firm\Application\Service\Coordinator\CancelMentorDedication;
use Firm\Application\Service\Coordinator\DedicateMentorToParticipant;
use Firm\Domain\Model\Firm\Program\Consultant;
use Firm\Domain\Model\Firm\Program\Coordinator;
use Firm\Domain\Model\Firm\Program\Participant;
use Query\Application\Service\Coordinator\ViewDedicatedMentor;
use Query\Domain\Model\Firm\Program\Coordinator as Coordinator2;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;

class DedicatedMentorController extends AsProgramCoordinatorBaseController
{

    public function assign($programId, $participantId)
    {
        $consultantId = $this->stripTagsInputRequest('consultantId');
        $dedicatedMentorId = $this->buildDedicateService()
                ->execute($this->firmId(), $this->personnelId(), $programId, $participantId, $consultantId);

        return $this->show($programId, $dedicatedMentorId);
    }

    public function cancel($programId, $dedicatedMentorId)
    {
        $this->buildCancelService()->execute($this->firmId(), $this->personnelId(), $programId, $dedicatedMentorId);
        return $this->show($programId, $dedicatedMentorId);
    }

    public function show($programId, $dedicatedMentorId)
    {
        $dedicatedMentor = $this->buildViewService()
                ->showById($this->firmId(), $this->personnelId(), $programId, $dedicatedMentorId);

        return $this->singleQueryResponse($this->arrayDataOfDedicatedMentor($dedicatedMentor));
    }

    public function showAllBelongsToParticipant($programId, $participantId)
    {
        $cancelledStatus = $this->filterBooleanOfQueryRequest('cancelledStatus');
        $dedicatedMentors = $this->buildViewService()->showAllBelongsToParticipant(
                $this->firmId(), $this->personnelId(), $programId, $participantId, $this->getPage(),
                $this->getPageSize(), $cancelledStatus);

        $result = [];
        $result['total'] = count($dedicatedMentors);
        foreach ($dedicatedMentors as $dedicatedMentor) {
            $result['list'][] = [
                'id' => $dedicatedMentor->getId(),
                'modifiedTime' => $dedicatedMentor->getModifiedTimeString(),
                'cancelled' => $dedicatedMentor->isCancelled(),
                'consultant' => [
                    'id' => $dedicatedMentor->getConsultant()->getId(),
                    'personnel' => [
                        'id' => $dedicatedMentor->getConsultant()->getPersonnel()->getId(),
                        'name' => $dedicatedMentor->getConsultant()->getPersonnel()->getName(),
                    ],
                ],
            ];
        }
        return $this->listQueryResponse($result);
    }

    public function showAllBelongsToConsultant($programId, $consultantId)
    {
        $cancelledStatus = $this->filterBooleanOfQueryRequest('cancelledStatus');
        $dedicatedMentors = $this->buildViewService()->showAllBelongsToConsultant(
                $this->firmId(), $this->personnelId(), $programId, $consultantId, $this->getPage(),
                $this->getPageSize(), $cancelledStatus);

        $result = [];
        $result['total'] = count($dedicatedMentors);
        foreach ($dedicatedMentors as $dedicatedMentor) {
            $result['list'][] = [
                'id' => $dedicatedMentor->getId(),
                'modifiedTime' => $dedicatedMentor->getModifiedTimeString(),
                'cancelled' => $dedicatedMentor->isCancelled(),
                'participant' => [
                    'id' => $dedicatedMentor->getParticipant()->getId(),
                    'name' => $dedicatedMentor->getParticipant()->getName(),
                ],
            ];
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfDedicatedMentor(DedicatedMentor $dedicatedMentor): array
    {
        return [
            'id' => $dedicatedMentor->getId(),
            'modifiedTime' => $dedicatedMentor->getModifiedTimeString(),
            'cancelled' => $dedicatedMentor->isCancelled(),
            'consultant' => [
                'id' => $dedicatedMentor->getConsultant()->getId(),
                'personnel' => [
                    'id' => $dedicatedMentor->getConsultant()->getPersonnel()->getId(),
                    'name' => $dedicatedMentor->getConsultant()->getPersonnel()->getName(),
                ],
            ],
            'participant' => [
                'id' => $dedicatedMentor->getParticipant()->getId(),
                'name' => $dedicatedMentor->getParticipant()->getName(),
            ],
        ];
    }

    protected function buildDedicateService()
    {
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        $participantRepository = $this->em->getRepository(Participant::class);
        $consultantRepository = $this->em->getRepository(Consultant::class);
        return new DedicateMentorToParticipant($coordinatorRepository, $participantRepository, $consultantRepository);
    }

    protected function buildCancelService()
    {
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        $dedicatedMentorRepository = $this->em->getRepository(Participant\DedicatedMentor::class);
        return new CancelMentorDedication($coordinatorRepository, $dedicatedMentorRepository);
    }

    protected function buildViewService()
    {
        $coordinatorRepository = $this->em->getRepository(Coordinator2::class);
        $dedicatedMentorRepository = $this->em->getRepository(DedicatedMentor::class);
        return new ViewDedicatedMentor($coordinatorRepository, $dedicatedMentorRepository);
    }

}
