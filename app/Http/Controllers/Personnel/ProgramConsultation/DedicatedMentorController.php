<?php

namespace App\Http\Controllers\Personnel\ProgramConsultation;

use Query\Application\Service\Consultant\ViewDedicatedMentor;
use Query\Domain\Model\Firm\Program\Consultant;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;

class DedicatedMentorController extends ProgramConsultationBaseController
{

    public function showAll($programConsultationId)
    {
        $cancelledStatus = $this->filterBooleanOfQueryRequest('cancelledStatus');
        $dedicatedMentors = $this->buildViewService()->showAll(
                $this->firmId(), $this->personnelId(), $programConsultationId, $this->getPage(), $this->getPageSize(),
                $cancelledStatus);
        
        $result = [];
        $result['total'] = count($dedicatedMentors);
        foreach ($dedicatedMentors as $dedicatedMentor) {
            $result['list'][] = $this->arrayDataOfDedicatedMentor($dedicatedMentor);
        }
        return $this->listQueryResponse($result);
    }

    public function show($programConsultationId, $dedicatedMentorId)
    {
        $dedicatedMentor = $this->buildViewService()
                ->showById($this->firmId(), $this->personnelId(), $programConsultationId, $dedicatedMentorId);
        return $this->singleQueryResponse($this->arrayDataOfDedicatedMentor($dedicatedMentor));
    }

    protected function arrayDataOfDedicatedMentor(DedicatedMentor $dedicatedMentor): array
    {
        return [
            'id' => $dedicatedMentor->getId(),
            'modifiedTime' => $dedicatedMentor->getModifiedTimeString(),
            'cancelled' => $dedicatedMentor->isCancelled(),
            'participant' => [
                'id' => $dedicatedMentor->getParticipant()->getId(),
                'name' => $dedicatedMentor->getParticipant()->getName(),
            ],
        ];
    }

    protected function buildViewService()
    {
        $consultantRepository = $this->em->getRepository(Consultant::class);
        $dedicatedMentorRepository = $this->em->getRepository(DedicatedMentor::class);
        return new ViewDedicatedMentor($consultantRepository, $dedicatedMentorRepository);
    }

}
