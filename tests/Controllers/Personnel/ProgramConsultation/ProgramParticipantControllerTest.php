<?php

namespace Tests\Controllers\Personnel\ProgramConsultation;

use DateTime;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport\RecordOfAssignmentFieldValue;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\RecordOfAssignmentField;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\RecordOfMetricAssignmentReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMetricAssignment;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfParticipantProfile;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\Worksheet\RecordOfCompletedMission;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMetric;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfProgramsProfileForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProfileForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfMember;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\RecordOfUser;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;
use Tests\Controllers\RecordPreparation\User\RecordOfUserParticipant;

class ProgramParticipantControllerTest extends ExtendedConsultantTestCase
{
    protected $clientParticipant;
    protected $teamParticipant;
    protected $userParticipant;
    
    protected $teamMemberOne;
    protected $teamMemberTwo;
    
    protected $missionOne;
    protected $missionTwo;
    protected $missionThree;
    
    protected $completedMissionOne_last;
    protected $completedMissionTwo;
    
    protected $participantProfileOne;
    protected $participantProfileTwo;

    protected $metricAssignment;
    protected $assignmentFieldOne;
    protected $assignmentFieldTwo;
    protected $metricAssignmentReportOne;
    protected $metricAssignmentReportTwo_last;
    protected $assignmentFieldValueOne_mar1af1;
    protected $assignmentFieldValueTwo_mar1af2;
    protected $assignmentFieldValueThree_mar2af1;
    protected $assignmentFieldValueFour_mar2af2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('CompletedMission')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('ProfileForm')->truncate();
        $this->connection->table('ProgramsProfileForm')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('ParticipantProfile')->truncate();
        $this->connection->table('MetricAssignment')->truncate();
        $this->connection->table('Metric')->truncate();
        $this->connection->table('AssignmentField')->truncate();
        $this->connection->table('MetricAssignmentReport')->truncate();
        $this->connection->table('AssignmentFieldValue')->truncate();
        
        $firm = $this->personnel->firm;
        $program = $this->consultant->program;
        
        $clientOne = new RecordOfClient($firm, 1);
        $clientTwo = new RecordOfClient($firm, 2);
        
        $teamOne = new RecordOfTeam($firm, $clientOne, 1);
        
        $this->teamMemberOne = new RecordOfMember($teamOne, $clientOne, 1);
        $this->teamMemberTwo = new RecordOfMember($teamOne, $clientTwo, 2);
        
        $userOne = new RecordOfUser(1);
        
        $participantOne = new RecordOfParticipant($program, 1);
        
        $this->clientParticipant = new RecordOfClientParticipant($clientOne, $participantOne);
        
        $this->teamParticipant = new RecordOfTeamProgramParticipation($teamOne, $participantOne);
        
        $this->userParticipant = new RecordOfUserParticipant($userOne, $participantOne);
        //
        $this->missionOne = new RecordOfMission($program, null, 1, null);
        $this->missionOne->published = true;
        $this->missionTwo = new RecordOfMission($program, null, 2, null);
        $this->missionTwo->published = true;
        $this->missionThree = new RecordOfMission($program, null, 3, null);
        $this->missionThree->published = true;
        
        $this->completedMissionOne_last = new RecordOfCompletedMission($participantOne, $this->missionOne, 1);
        $this->completedMissionOne_last->completedTime = (new DateTime('-24 hours'))->format('Y-m-d H:i:s');
        $this->completedMissionTwo = new RecordOfCompletedMission($participantOne, $this->missionTwo, 2);
        $this->completedMissionTwo->completedTime = (new DateTime('-48 hours'))->format('Y-m-d H:i:s');
        //
        $formOne = new RecordOfForm(1);
        $formTwo = new RecordOfForm(2);
        
        $profileFormOne = new RecordOfProfileForm($firm, $formOne);
        $profileFormTwo = new RecordOfProfileForm($firm, $formTwo);
        
        $programsProfileFormOne = new RecordOfProgramsProfileForm($program, $profileFormOne, 1);
        $programsProfileFormTwo = new RecordOfProgramsProfileForm($program, $profileFormTwo, 2);
        
        $formRecordOne = new RecordOfFormRecord($formOne, 1);
        $formRecordTwo = new RecordOfFormRecord($formTwo, 2);
        
        $this->participantProfileOne = new RecordOfParticipantProfile($participantOne, $programsProfileFormOne, $formRecordOne);
        $this->participantProfileTwo = new RecordOfParticipantProfile($participantOne, $programsProfileFormTwo, $formRecordTwo);
        //
        $this->metricAssignment = new RecordOfMetricAssignment($participantOne, 1);
        
        $metricOne = new RecordOfMetric($program, 1);
        $metricTwo = new RecordOfMetric($program, 2);
        
        $this->assignmentFieldOne = new RecordOfAssignmentField($this->metricAssignment, $metricOne, 1);
        $this->assignmentFieldTwo = new RecordOfAssignmentField($this->metricAssignment, $metricTwo, 2);
        
        $this->metricAssignmentReportOne = new RecordOfMetricAssignmentReport($this->metricAssignment, 1);
        $this->metricAssignmentReportOne->approved = true;
        $this->metricAssignmentReportOne->observationTime = (new DateTime('-48 hours'))->format('Y-m-d H:i:s');
        $this->metricAssignmentReportTwo_last = new RecordOfMetricAssignmentReport($this->metricAssignment, 2);
        $this->metricAssignmentReportTwo_last->approved = true;
        $this->metricAssignmentReportTwo_last->observationTime = (new DateTime('-24 hours'))->format('Y-m-d H:i:s');
        
        $this->assignmentFieldValueOne_mar1af1 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportOne, $this->assignmentFieldOne, 1);
        $this->assignmentFieldValueTwo_mar1af2 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportOne, $this->assignmentFieldTwo, 2);
        $this->assignmentFieldValueThree_mar2af1 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportTwo_last, $this->assignmentFieldOne, 3);
        $this->assignmentFieldValueFour_mar2af2 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportTwo_last, $this->assignmentFieldTwo, 4);
        
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('CompletedMission')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('ProfileForm')->truncate();
        $this->connection->table('ProgramsProfileForm')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('ParticipantProfile')->truncate();
        $this->connection->table('MetricAssignment')->truncate();
        $this->connection->table('Metric')->truncate();
        $this->connection->table('AssignmentField')->truncate();
        $this->connection->table('MetricAssignmentReport')->truncate();
        $this->connection->table('AssignmentFieldValue')->truncate();
    }
    
    protected function persistSharedDependency()
    {
        $this->missionOne->insert($this->connection);
        $this->missionTwo->insert($this->connection);
        $this->missionThree->insert($this->connection);
        
        $this->completedMissionOne_last->insert($this->connection);
        $this->completedMissionTwo->insert($this->connection);
        
        $this->participantProfileOne->programsProfileForm->profileForm->insert($this->connection);
        $this->participantProfileTwo->programsProfileForm->profileForm->insert($this->connection);
        
        $this->participantProfileOne->programsProfileForm->insert($this->connection);
        $this->participantProfileTwo->programsProfileForm->insert($this->connection);
        
        $this->participantProfileOne->insert($this->connection);
        $this->participantProfileTwo->insert($this->connection);
        
        $this->metricAssignment->insert($this->connection);
        
        $this->assignmentFieldOne->metric->insert($this->connection);
        $this->assignmentFieldTwo->metric->insert($this->connection);
        
        $this->assignmentFieldOne->insert($this->connection);
        $this->assignmentFieldTwo->insert($this->connection);
        
        $this->metricAssignmentReportOne->insert($this->connection);
        $this->metricAssignmentReportTwo_last->insert($this->connection);
        
        $this->assignmentFieldValueOne_mar1af1->insert($this->connection);
        $this->assignmentFieldValueTwo_mar1af2->insert($this->connection);
        $this->assignmentFieldValueThree_mar2af1->insert($this->connection);
        $this->assignmentFieldValueFour_mar2af2->insert($this->connection);
    }
    
    protected function viewClientParticipantDetail()
    {
        $this->persistConsultantDependency();
        
        $this->clientParticipant->client->insert($this->connection);
        $this->clientParticipant->insert($this->connection);
        
        $this->persistSharedDependency();
        
        $uri = $this->consultantUri . "/client-participants/{$this->clientParticipant->id}";
        $this->get($uri, $this->consultant->personnel->token);
    }
    public function test_viewClientParticipantDetail_200()
    {
$this->disableExceptionHandling();
        $this->viewClientParticipantDetail();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->clientParticipant->id,
            'client' => [
                'id' => $this->clientParticipant->client->id,
                'name' => $this->clientParticipant->client->getFullName(),
            ],
            'completedMissionCount' => 2,
            'activeMissionCount' => 3,
            'lastCompletedMission' => [
                'id' => $this->completedMissionOne_last->id,
                'completedTime' => $this->completedMissionOne_last->completedTime,
                'mission' => [
                    'id' => $this->completedMissionOne_last->mission->id,
                    'name' => $this->completedMissionOne_last->mission->name,
                ],
            ],
            'metricAssignment' => [
                "id" => $this->metricAssignment->id,
                "startDate" => $this->metricAssignment->startDate,
                "endDate" => $this->metricAssignment->endDate,
                'assignmentFields' => [
                    [
                        "id" => $this->assignmentFieldOne->id,
                        "target" => $this->assignmentFieldOne->target,
                        'metric' => [
                            "id" => $this->assignmentFieldOne->metric->id,
                            "name" => $this->assignmentFieldOne->metric->name,
                        ],
                    ],
                    [
                        "id" => $this->assignmentFieldTwo->id,
                        "target" => $this->assignmentFieldTwo->target,
                        'metric' => [
                            "id" => $this->assignmentFieldTwo->metric->id,
                            "name" => $this->assignmentFieldTwo->metric->name,
                        ],
                    ],
                ],
                'lastMetricAssignmentReport' => [
                    "id" => $this->metricAssignmentReportTwo_last->id,
                    "observationTime" => $this->metricAssignmentReportTwo_last->observationTime,
                    "assignmentFieldValues" => [
                        [
                            "id" => $this->assignmentFieldValueThree_mar2af1->id,
                            "value" => $this->assignmentFieldValueThree_mar2af1->inputValue,
                            "assignmentFieldId" => $this->assignmentFieldValueThree_mar2af1->assignmentField->id,
                        ],
                        [
                            "id" => $this->assignmentFieldValueFour_mar2af2->id,
                            "value" => $this->assignmentFieldValueFour_mar2af2->inputValue,
                            "assignmentFieldId" => $this->assignmentFieldValueFour_mar2af2->assignmentField->id,
                        ],
                    ],
                ],
            ],
            'profiles' => [
                [
                    'id' => $this->participantProfileOne->id,
                    'submitTime' => $this->participantProfileOne->formRecord->submitTime,
                    'profileForm' => [
                        'id' => $this->participantProfileOne->programsProfileForm->id,
                        'name' => $this->participantProfileOne->programsProfileForm->profileForm->form->name,
                    ],
                ],
                [
                    'id' => $this->participantProfileTwo->id,
                    'submitTime' => $this->participantProfileTwo->formRecord->submitTime,
                    'profileForm' => [
                        'id' => $this->participantProfileTwo->programsProfileForm->id,
                        'name' => $this->participantProfileTwo->programsProfileForm->profileForm->form->name,
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewClientParticipantDetail_inactiveConsultant_403()
    {
        $this->consultant->active = false;
        
        $this->viewClientParticipantDetail();
        $this->seeStatusCode(403);
    }
    public function test_viewClientParticipantDetail_unmanagedParticipant_belongsToOtherProgram()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        $this->clientParticipant->participant->program = $otherProgram;
        
        $this->viewClientParticipantDetail();
        $this->seeStatusCode(404);
    }
    public function test_viewClientParticipantDetail_programContainUnpublishedMission_excludeFromActiveMissionCount()
    {
        $this->missionThree->published = false;
        
        $this->viewClientParticipantDetail();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['activeMissionCount' => 2]);
    }
    
    protected function viewTeamParticipantDetail()
    {
        $this->persistConsultantDependency();
        
        $this->teamParticipant->team->insert($this->connection);
        $this->teamParticipant->insert($this->connection);
        
        $this->teamMemberOne->client->insert($this->connection);
        $this->teamMemberTwo->client->insert($this->connection);
        $this->teamMemberOne->insert($this->connection);
        $this->teamMemberTwo->insert($this->connection);
        
        $this->persistSharedDependency();
        
        $uri = $this->consultantUri . "/team-participants/{$this->teamParticipant->id}";
        $this->get($uri, $this->consultant->personnel->token);
    }
    public function test_viewTeamParticipantDetail_200()
    {
$this->disableExceptionHandling();
        $this->viewTeamParticipantDetail();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->teamParticipant->id,
            'team' => [
                'id' => $this->teamParticipant->team->id,
                'name' => $this->teamParticipant->team->name,
                'members' => [
                    [
                        'id' => $this->teamMemberOne->id,
                        'client' => [
                            'id' => $this->teamMemberOne->client->id,
                            'name' => $this->teamMemberOne->client->getFullName(),
                        ],
                    ],
                    [
                        'id' => $this->teamMemberTwo->id,
                        'client' => [
                            'id' => $this->teamMemberTwo->client->id,
                            'name' => $this->teamMemberTwo->client->getFullName(),
                        ],
                    ],
                ],
            ],
            'completedMissionCount' => 2,
            'activeMissionCount' => 3,
            'lastCompletedMission' => [
                'id' => $this->completedMissionOne_last->id,
                'completedTime' => $this->completedMissionOne_last->completedTime,
                'mission' => [
                    'id' => $this->completedMissionOne_last->mission->id,
                    'name' => $this->completedMissionOne_last->mission->name,
                ],
            ],
            'metricAssignment' => [
                "id" => $this->metricAssignment->id,
                "startDate" => $this->metricAssignment->startDate,
                "endDate" => $this->metricAssignment->endDate,
                'assignmentFields' => [
                    [
                        "id" => $this->assignmentFieldOne->id,
                        "target" => $this->assignmentFieldOne->target,
                        'metric' => [
                            "id" => $this->assignmentFieldOne->metric->id,
                            "name" => $this->assignmentFieldOne->metric->name,
                        ],
                    ],
                    [
                        "id" => $this->assignmentFieldTwo->id,
                        "target" => $this->assignmentFieldTwo->target,
                        'metric' => [
                            "id" => $this->assignmentFieldTwo->metric->id,
                            "name" => $this->assignmentFieldTwo->metric->name,
                        ],
                    ],
                ],
                'lastMetricAssignmentReport' => [
                    "id" => $this->metricAssignmentReportTwo_last->id,
                    "observationTime" => $this->metricAssignmentReportTwo_last->observationTime,
                    "assignmentFieldValues" => [
                        [
                            "id" => $this->assignmentFieldValueThree_mar2af1->id,
                            "value" => $this->assignmentFieldValueThree_mar2af1->inputValue,
                            "assignmentFieldId" => $this->assignmentFieldValueThree_mar2af1->assignmentField->id,
                        ],
                        [
                            "id" => $this->assignmentFieldValueFour_mar2af2->id,
                            "value" => $this->assignmentFieldValueFour_mar2af2->inputValue,
                            "assignmentFieldId" => $this->assignmentFieldValueFour_mar2af2->assignmentField->id,
                        ],
                    ],
                ],
            ],
            'profiles' => [
                [
                    'id' => $this->participantProfileOne->id,
                    'submitTime' => $this->participantProfileOne->formRecord->submitTime,
                    'profileForm' => [
                        'id' => $this->participantProfileOne->programsProfileForm->id,
                        'name' => $this->participantProfileOne->programsProfileForm->profileForm->form->name,
                    ],
                ],
                [
                    'id' => $this->participantProfileTwo->id,
                    'submitTime' => $this->participantProfileTwo->formRecord->submitTime,
                    'profileForm' => [
                        'id' => $this->participantProfileTwo->programsProfileForm->id,
                        'name' => $this->participantProfileTwo->programsProfileForm->profileForm->form->name,
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewTeamParticipantDetail_inactiveConsultant_403()
    {
        $this->consultant->active = false;
        
        $this->viewTeamParticipantDetail();
        $this->seeStatusCode(403);
    }
    public function test_viewTeamParticipantDetail_unmanagedParticipant_belongsToOtherProgram()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        $this->teamParticipant->participant->program = $otherProgram;
        
        $this->viewTeamParticipantDetail();
        $this->seeStatusCode(404);
    }
    
    protected function viewUserParticipantDetail()
    {
        $this->persistConsultantDependency();
        
        $this->userParticipant->user->insert($this->connection);
        $this->userParticipant->insert($this->connection);
        
        $this->persistSharedDependency();
        
        $uri = $this->consultantUri . "/user-participants/{$this->userParticipant->id}";
        $this->get($uri, $this->consultant->personnel->token);
    }
    public function test_viewUserParticipantDetail_200()
    {
$this->disableExceptionHandling();
        $this->viewUserParticipantDetail();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->userParticipant->id,
            'user' => [
                'id' => $this->userParticipant->user->id,
                'name' => $this->userParticipant->user->getFullName(),
            ],
            'completedMissionCount' => 2,
            'activeMissionCount' => 3,
            'lastCompletedMission' => [
                'id' => $this->completedMissionOne_last->id,
                'completedTime' => $this->completedMissionOne_last->completedTime,
                'mission' => [
                    'id' => $this->completedMissionOne_last->mission->id,
                    'name' => $this->completedMissionOne_last->mission->name,
                ],
            ],
            'metricAssignment' => [
                "id" => $this->metricAssignment->id,
                "startDate" => $this->metricAssignment->startDate,
                "endDate" => $this->metricAssignment->endDate,
                'assignmentFields' => [
                    [
                        "id" => $this->assignmentFieldOne->id,
                        "target" => $this->assignmentFieldOne->target,
                        'metric' => [
                            "id" => $this->assignmentFieldOne->metric->id,
                            "name" => $this->assignmentFieldOne->metric->name,
                        ],
                    ],
                    [
                        "id" => $this->assignmentFieldTwo->id,
                        "target" => $this->assignmentFieldTwo->target,
                        'metric' => [
                            "id" => $this->assignmentFieldTwo->metric->id,
                            "name" => $this->assignmentFieldTwo->metric->name,
                        ],
                    ],
                ],
                'lastMetricAssignmentReport' => [
                    "id" => $this->metricAssignmentReportTwo_last->id,
                    "observationTime" => $this->metricAssignmentReportTwo_last->observationTime,
                    "assignmentFieldValues" => [
                        [
                            "id" => $this->assignmentFieldValueThree_mar2af1->id,
                            "value" => $this->assignmentFieldValueThree_mar2af1->inputValue,
                            "assignmentFieldId" => $this->assignmentFieldValueThree_mar2af1->assignmentField->id,
                        ],
                        [
                            "id" => $this->assignmentFieldValueFour_mar2af2->id,
                            "value" => $this->assignmentFieldValueFour_mar2af2->inputValue,
                            "assignmentFieldId" => $this->assignmentFieldValueFour_mar2af2->assignmentField->id,
                        ],
                    ],
                ],
            ],
            'profiles' => [
                [
                    'id' => $this->participantProfileOne->id,
                    'submitTime' => $this->participantProfileOne->formRecord->submitTime,
                    'profileForm' => [
                        'id' => $this->participantProfileOne->programsProfileForm->id,
                        'name' => $this->participantProfileOne->programsProfileForm->profileForm->form->name,
                    ],
                ],
                [
                    'id' => $this->participantProfileTwo->id,
                    'submitTime' => $this->participantProfileTwo->formRecord->submitTime,
                    'profileForm' => [
                        'id' => $this->participantProfileTwo->programsProfileForm->id,
                        'name' => $this->participantProfileTwo->programsProfileForm->profileForm->form->name,
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewUserParticipantDetail_inactiveConsultant_403()
    {
        $this->consultant->active = false;
        
        $this->viewUserParticipantDetail();
        $this->seeStatusCode(403);
    }
    public function test_viewUserParticipantDetail_unmanagedParticipant_belongsToOtherProgram()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        $this->userParticipant->participant->program = $otherProgram;
        
        $this->viewUserParticipantDetail();
        $this->seeStatusCode(404);
    }
}
