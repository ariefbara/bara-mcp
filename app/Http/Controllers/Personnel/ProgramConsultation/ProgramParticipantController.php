<?php

namespace App\Http\Controllers\Personnel\ProgramConsultation;

use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\Firm\Program\Participant\CompletedMission;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\AssignmentField;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport\AssignmentFieldValue;
use Query\Domain\Model\Firm\Program\Participant\ParticipantProfile;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\InProgram\ViewClientParticipantDetail;
use Query\Domain\Task\InProgram\ViewTeamParticipantDetail;
use Query\Domain\Task\InProgram\ViewUserParticipantDetail;

class ProgramParticipantController extends ConsultantBaseController
{

    public function viewTeamParticipantDetail($consultantId, $id)
    {
        $teamParticipantRepository = $this->em->getRepository(TeamProgramParticipation::class);
        $task = new ViewTeamParticipantDetail($teamParticipantRepository);
        $payload = new CommonViewDetailPayload($id);
        $this->executeProgramQueryTask($consultantId, $task, $payload);

        return $this->singleQueryResponse($this->detailOfTeamParticipant($payload->result));
    }
    protected function detailOfTeamParticipant(TeamProgramParticipation $teamParticipant): array
    {
        $teamMembers = [];
        foreach ($teamParticipant->getTeam()->iterateActiveMember() as $member) {
            $teamMembers[] = [
                'id' => $member->getId(),
                'client' => [
                    'id' => $member->getClient()->getId(),
                    'name' => $member->getClient()->getFullName(),
                ],
            ];
        }
        
        return array_merge($this->detailOfParticipant($teamParticipant->getProgramParticipation()), [
            'id' => $teamParticipant->getId(),
            'team' => [
                'id' => $teamParticipant->getTeam()->getId(),
                'name' => $teamParticipant->getTeam()->getName(),
                'members' => $teamMembers
            ],
        ]);
    }

    public function viewClientParticipantDetail($consultantId, $id)
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant::class);
        $task = new ViewClientParticipantDetail($clientParticipantRepository);
        $payload = new CommonViewDetailPayload($id);
        $this->executeProgramQueryTask($consultantId, $task, $payload);

        return $this->singleQueryResponse($this->detailOfClientParticipant($payload->result));
    }
    protected function detailOfClientParticipant(ClientParticipant $clientParticipant): array
    {
        return array_merge($this->detailOfParticipant($clientParticipant->getParticipant()), [
            'id' => $clientParticipant->getId(),
            'client' => [
                'id' => $clientParticipant->getClient()->getId(),
                'name' => $clientParticipant->getClient()->getFullName(),
            ],
        ]);
    }

    public function viewUserParticipantDetail($consultantId, $id)
    {
        $userParticipantRepository = $this->em->getRepository(UserParticipant::class);
        $task = new ViewUserParticipantDetail($userParticipantRepository);
        $payload = new CommonViewDetailPayload($id);
        $this->executeProgramQueryTask($consultantId, $task, $payload);

        return $this->singleQueryResponse($this->detailOfUserParticipant($payload->result));
    }
    protected function detailOfUserParticipant(UserParticipant $userParticipant): array
    {
        return array_merge($this->detailOfParticipant($userParticipant->getParticipant()), [
            'id' => $userParticipant->getId(),
            'user' => [
                'id' => $userParticipant->getUser()->getId(),
                'name' => $userParticipant->getUser()->getFullName(),
            ],
        ]);
        
    }

    //
    protected function detailOfParticipant(Participant $participant): array
    {
        $profiles = [];
        foreach ($participant->iterateActiveParticipantProfiles() as $profile) {
            $profiles[] = $this->detailOfParticipantProfile($profile);
        }
        //
        $dedicatedMentors = [];
        foreach ($participant->iterateActiveDedicatedMentors() as $dedicatedMentor) {
            $dedicatedMentors[] = $this->detailOfDedicatedMentor($dedicatedMentor);
        }
        
        return [
            'completedMissionCount' => $participant->getCompletedMissionCount(),
            'activeMissionCount' => $participant->getActiveMissionCount(),
            'lastCompletedMission' => $this->detailOfLastCompletedMission($participant->getLastCompletedMission()),
            'metricAssignment' => $this->detailOfMetricAssignment($participant->getMetricAssignment()),
            'profiles' => $profiles,
            'dedicatedMentors' => $dedicatedMentors,
        ];
    }
    //
    protected function detailOfParticipantProfile(ParticipantProfile $profile): ?array
    {
        return [
            'id' => $profile->getId(),
            'submitTime' => $profile->getSubmitTimeString(),
            'profileForm' => [
                'id' => $profile->getProgramsProfileForm()->getId(),
                'name' => $profile->getProgramsProfileForm()->getProfileForm()->getName(),
            ],
        ];
    }
    protected function detailOfDedicatedMentor(DedicatedMentor $dedicatedMentor): array
    {
        return [
            'id' => $dedicatedMentor->getId(),
            'modifiedTime' => $dedicatedMentor->getModifiedTimeString(),
            'consultant' => [
                'id' => $dedicatedMentor->getConsultant()->getId(),
                'personnel' => [
                    'id' => $dedicatedMentor->getConsultant()->getPersonnel()->getId(),
                    'name' => $dedicatedMentor->getConsultant()->getPersonnel()->getName(),
                    'bio' => $dedicatedMentor->getConsultant()->getPersonnel()->getBio(),
                ],
            ],
        ];
    }
    protected function detailOfLastCompletedMission(?CompletedMission $completedMission): ?Array
    {
        return empty($completedMission) ? null : [
            'id' => $completedMission->getId(),
            'completedTime' => $completedMission->getCompletedTimeString(),
            'mission' => [
                'id' => $completedMission->getMission()->getId(),
                'name' => $completedMission->getMission()->getName(),
            ],
        ];
    }
    
    protected function detailOfMetricAssignment(?MetricAssignment $metricAssignment): ?array
    {
        if (empty($metricAssignment)) {
            return null;
        }
        $assignmentFields = [];
        foreach ($metricAssignment->iterateActiveAssignmentFields() as $assignmentField) {
            $assignmentFields[] = $this->detailOfAssignmentField($assignmentField);
        }
        return [
            "id" => $metricAssignment->getId(),
            "startDate" => $metricAssignment->getStartDateString(),
            "endDate" => $metricAssignment->getEndDateString(),
            "assignmentFields" => $assignmentFields,
            'lastMetricAssignmentReport' => $this->detailOfMetricAssignmentReport(
                    $metricAssignment->getLastApprovedMetricAssignmentReports()),
        ];
    }
    protected function detailOfAssignmentField(AssignmentField $assignmentField): array
    {
        return [
            "id" => $assignmentField->getId(),
            "target" => $assignmentField->getTarget(),
            "metric" => [
                "id" => $assignmentField->getMetric()->getId(),
                "name" => $assignmentField->getMetric()->getName(),
            ],
        ];
    }
    protected function detailOfMetricAssignmentReport(?MetricAssignmentReport $metricAssignmentReport): ?array
    {
        if (empty($metricAssignmentReport)) {
            return null;
        }
        $assignmentFieldValues = [];
        foreach ($metricAssignmentReport->iterateNonremovedAssignmentFieldValues() as $assignmentFieldValue) {
            $assignmentFieldValues[] = $this->detailOfAssignmentFieldValue($assignmentFieldValue);
        }
        return [
            "id" => $metricAssignmentReport->getId(),
            "observationTime" => $metricAssignmentReport->getObservationTimeString(),
            "assignmentFieldValues" => $assignmentFieldValues,
        ];
    }
    protected function detailOfAssignmentFieldValue(AssignmentFieldValue $assignmentFieldValue): array
    {
        return [
            "id" => $assignmentFieldValue->getId(),
            "value" => $assignmentFieldValue->getValue(),
            "assignmentFieldId" => $assignmentFieldValue->getAssignmentField()->getId(),
        ];
    }

}
