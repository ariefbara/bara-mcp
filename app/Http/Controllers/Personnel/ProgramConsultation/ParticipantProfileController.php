<?php

namespace App\Http\Controllers\Personnel\ProgramConsultation;

use App\Http\Controllers\FormRecordToArrayDataConverter;
use App\Http\Controllers\Personnel\ProgramConsultation\ConsultantBaseController;
use Query\Domain\Model\Firm\Program\Participant\ParticipantProfile;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\Program\Participant\ParticipantProfileFilter;
use Query\Domain\Task\Dependency\PaginationFilter;
use Query\Domain\Task\InProgram\ViewAllParticipantProfile;
use Query\Domain\Task\InProgram\ViewAllParticipantProfilePayload;
use Query\Domain\Task\InProgram\ViewParticipantProfileDetail;

class ParticipantProfileController extends ConsultantBaseController
{
    public function view($consultantId, $id)
    {
        $participantProfileRepository = $this->em->getRepository(ParticipantProfile::class);
        $task = new ViewParticipantProfileDetail($participantProfileRepository);
        $payload = new CommonViewDetailPayload($id);
        $this->executeProgramQueryTask($consultantId, $task, $payload);
        
        return $this->singleQueryResponse($this->arrayDataOfParticipantProfile($payload->result));
    }
    
    public function viewAllProfileOfParticularParticipant($consultantId, $participantId)
    {
        $participantProfileRepository = $this->em->getRepository(ParticipantProfile::class);
        $task = new ViewAllParticipantProfile($participantProfileRepository);
        
        $paginationFilter = new PaginationFilter($this->getPage(), $this->getPageSize());
        $participantProfileFilter = (new ParticipantProfileFilter($paginationFilter))
                ->setParticipantId($participantId);
        $payload = new ViewAllParticipantProfilePayload($participantProfileFilter);
        
        $this->executeProgramQueryTask($consultantId, $task, $payload);
        
        $result = [];
        $result['total'] = count($payload->result);
        foreach ($payload->result as $participantProfile) {
            $result['list'][] = [
                'id' => $participantProfile->getId(),
                "programsProfileForm" => [
                    "id" => $participantProfile->getProgramsProfileForm()->getId(),
                    "profileForm" => [
                        "id" => $participantProfile->getProgramsProfileForm()->getProfileForm()->getId(),
                        "name" => $participantProfile->getProgramsProfileForm()->getProfileForm()->getName(),
                    ],
                ],
            ];
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfParticipantProfile(ParticipantProfile $participantProfile): array
    {
        $result = (new FormRecordToArrayDataConverter())->convert($participantProfile);
        $result["id"] = $participantProfile->getId();
        $result["programsProfileForm"] = [
            "id" => $participantProfile->getProgramsProfileForm()->getId(),
            "profileForm" => [
                "id" => $participantProfile->getProgramsProfileForm()->getProfileForm()->getId(),
                "name" => $participantProfile->getProgramsProfileForm()->getProfileForm()->getName(),
            ],
        ];
        return $result;
    }
}
