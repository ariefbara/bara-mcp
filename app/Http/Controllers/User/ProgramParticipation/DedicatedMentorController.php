<?php

namespace App\Http\Controllers\User\ProgramParticipation;

use App\Http\Controllers\User\UserBaseController;
use Query\Application\Service\User\AsProgramParticipant\ViewDedicatedMentor;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;
use Query\Domain\Model\User\UserParticipant;

class DedicatedMentorController extends UserBaseController
{

    public function showAll($programParticipationId)
    {
        $cancelledStatus = $this->filterBooleanOfQueryRequest('cancelledStatus');
        $dedicatedMentors = $this->buildViewService()->showAll(
                $this->userId(), $programParticipationId, $this->getPage(), $this->getPageSize(), $cancelledStatus);

        $result = [];
        $result['total'] = count($dedicatedMentors);
        foreach ($dedicatedMentors as $dedicatedMentor) {
            $result['list'][] = $this->arrayDataOfDedicatedMentor($dedicatedMentor);
        }
        return $this->listQueryResponse($result);
    }

    public function show($programParticipationId, $dedicatedMentorId)
    {
        $dedicatedMentor = $this->buildViewService()->showById($this->userId(), $programParticipationId, $dedicatedMentorId);
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
        $userParticipantRepository = $this->em->getRepository(UserParticipant::class);
        $dedicatedMentorRepository = $this->em->getRepository(DedicatedMentor::class);
        return new ViewDedicatedMentor($userParticipantRepository, $dedicatedMentorRepository);
    }

}
