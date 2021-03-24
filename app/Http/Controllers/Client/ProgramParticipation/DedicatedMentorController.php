<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use App\Http\Controllers\Client\ClientBaseController;
use Query\Application\Service\Client\AsProgramParticipant\ViewDedicatedMentor;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;

class DedicatedMentorController extends ClientBaseController
{

    public function showAll($programParticipationId)
    {
        $cancelledStatus = $this->filterBooleanOfQueryRequest('cancelledStatus');
        $dedicatedMentors = $this->buildViewService()->showAll(
                $this->firmId(), $this->clientId(), $programParticipationId, $this->getPage(), $this->getPageSize(),
                $cancelledStatus);

        $result = [];
        $result['total'] = count($dedicatedMentors);
        foreach ($dedicatedMentors as $dedicatedMentor) {
            $result['list'][] = $this->arrayDataOfDedicatedMentor($dedicatedMentor);
        }
        return $this->listQueryResponse($result);
    }

    public function show($programParticipationId, $dedicatedMentorId)
    {
        $dedicatedMentor = $this->buildViewService()->showById(
                $this->firmId(), $this->clientId(), $programParticipationId, $dedicatedMentorId);
        return $this->singleQueryResponse($this->arrayDataOfDedicatedMentor($dedicatedMentor));
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
        ];
    }

    protected function buildViewService()
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant::class);
        $dedicatedMentorRepository = $this->em->getRepository(DedicatedMentor::class);
        return new ViewDedicatedMentor($clientParticipantRepository, $dedicatedMentorRepository);
    }

}
