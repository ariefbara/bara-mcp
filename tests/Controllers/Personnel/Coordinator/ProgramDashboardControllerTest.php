<?php

namespace Tests\Controllers\Personnel\Coordinator;

use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientRegistrant;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\MentoringSlot\RecordOfBookedMentoringSlot;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfConsultantComment;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfMentoringSlot;
use Tests\Controllers\RecordPreparation\Firm\Program\Mission\RecordOfMissionComment;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MentoringRequest\RecordOfNegotiatedMentoring;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport\RecordOfAssignmentFieldValue;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\RecordOfAssignmentField;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\RecordOfMetricAssignmentReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfDeclaredMentoring;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMentoringRequest;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMetricAssignment;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfWorksheet;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\Worksheet\RecordOfComment;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\Worksheet\RecordOfCompletedMission;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMetric;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfRegistrant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramRegistration;
use Tests\Controllers\RecordPreparation\RecordOfUser;
use Tests\Controllers\RecordPreparation\Shared\Mentoring\RecordOfMentorReport;
use Tests\Controllers\RecordPreparation\Shared\Mentoring\RecordOfParticipantReport;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;
use Tests\Controllers\RecordPreparation\Shared\RecordOfMentoring;
use Tests\Controllers\RecordPreparation\User\RecordOfUserParticipant;
use function GuzzleHttp\json_encode;

class ProgramDashboardControllerTest extends ExtendedCoordinatorTestCase
{
    //
    protected $missionOne;
    protected $missionTwo;
    protected $missionThree;
    //
    protected $clientRegistrantOne;
    protected $teamRegistrantTwo;
    //
    protected $clientParticipantOne;
    protected $teamParticipantTwo;
    protected $userParticipantThree;
    protected $clientParticipantFour;
    //
    protected $completedMissionOneA;
    protected $completedMissionOneB;
    protected $completedMissionOneC;
    protected $completedMissionTwoA;
    //
    protected $metricOne;
    protected $metricTwo;
    //
    protected $metricAssignmentOne;
    protected $assignmentFieldOneA;
    protected $assignmentFieldOneB;
    //
    protected $metricAssignmentTwo;
    protected $assignmentFieldTwoB;
    //
    protected $metricAssignmentThree;
    protected $assignmentFieldThreeA;
    //
    protected $metricAssignmentReportOne;
    protected $metricAssignmentReportOne_previous;
    protected $assignmentFieldValueOneA;
    protected $assignmentFieldValueOneB;
    protected $assignmentFieldValueOneA_previous;
    protected $assignmentFieldValueOneB_previous;
    //
    protected $metricAssignmentReportThree;
    protected $assignmentFieldValueThreeA;
    //
    protected $consultantOne;
    protected $consultantTwo;
    //
    protected $form;
    //
    protected $worksheetOne;
    protected $worksheetTwo;
    //
    protected $consultantCommentOneA;
    protected $consultantCommentOneB;
    protected $consultantCommentTwoA;
    //
    protected $missionCommentOneA;
    protected $missionCommentTwoA;
    protected $missionCommentTwoB;
    //
    protected $consultationSetup;
    protected $negotiatedMentoringOneA;
    protected $mentoringSlotOneB;
    protected $bookedMentoringSlotOneBOne;
    protected $bookedMentoringSlotOneBTwo;
    protected $declaredMentoringTwoA;
    //
    protected $participantReportOneA;
    protected $participantReportOneATwo;
    protected $participantReportTwoA;
    //
    protected $mentorReportOneAOne;
    protected $mentorReportTwoA;
    //
    protected $uri;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('User')->truncate();
        //
        $this->connection->table('Registrant')->truncate();
        $this->connection->table('ClientRegistrant')->truncate();
        $this->connection->table('TeamRegistrant')->truncate();
        $this->connection->table('UserRegistrant')->truncate();
        //
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        //
        $this->connection->table('Mission')->truncate();
        $this->connection->table('CompletedMission')->truncate();
        //
        $this->connection->table('Metric')->truncate();
        $this->connection->table('MetricAssignment')->truncate();
        $this->connection->table('AssignmentField')->truncate();
        $this->connection->table('MetricAssignmentReport')->truncate();
        $this->connection->table('AssignmentFieldValue')->truncate();
        //
        $this->connection->table('Consultant')->truncate();
        
        $this->connection->table('Form')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('Worksheet')->truncate();
        
        $this->connection->table('Comment')->truncate();
        $this->connection->table('ConsultantComment')->truncate();
        $this->connection->table('MissionComment')->truncate();
        //
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        $this->connection->table('NegotiatedMentoring')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('BookedMentoringSlot')->truncate();
        $this->connection->table('DeclaredMentoring')->truncate();
        $this->connection->table('ParticipantReport')->truncate();
        $this->connection->table('MentorReport')->truncate();
        //
        $program = $this->coordinator->program;
        $firm = $program->firm;
        //
        $clientOne = new RecordOfClient($firm, 1);
        $clientTwo = new RecordOfClient($firm, 2);
        $clientThree = new RecordOfClient($firm, 3);
        $teamOne = new RecordOfTeam($firm, $clientOne, 1);
        $teamTwo = new RecordOfTeam($firm, $clientTwo, 2);
        $userOne = new RecordOfUser(1);
        //
        $registrantOne = new RecordOfRegistrant($program, 1);
        $registrantTwo = new RecordOfRegistrant($program, 2);
        
        $this->clientRegistrantOne = new RecordOfClientRegistrant($clientTwo, $registrantOne);
        $this->teamRegistrantTwo = new RecordOfTeamProgramRegistration($teamTwo, $registrantTwo);
        //
        $participantOne = new RecordOfParticipant($program, 1);
        $participantTwo = new RecordOfParticipant($program, 2);
        $participantThree = new RecordOfParticipant($program, 3);
        $participantFour = new RecordOfParticipant($program, 4);

        $this->clientParticipantOne = new RecordOfClientParticipant($clientOne, $participantOne);
        $this->teamParticipantTwo = new RecordOfTeamProgramParticipation($teamOne, $participantTwo);
        $this->userParticipantThree = new RecordOfUserParticipant($userOne, $participantThree);
        $this->clientParticipantFour = new RecordOfClientParticipant($clientThree, $participantFour);
        //
        $this->missionOne = new RecordOfMission($program, null, 1, null);
        $this->missionOne->published = true;
        $this->missionTwo = new RecordOfMission($program, null, 2, null);
        $this->missionTwo->published = true;
        $this->missionThree = new RecordOfMission($program, null, 3, null);
        $this->missionThree->published = true;
        
        $this->completedMissionOneA = new RecordOfCompletedMission($participantOne, $this->missionOne, '1a');
        $this->completedMissionOneB = new RecordOfCompletedMission($participantOne, $this->missionTwo, '1b');
        $this->completedMissionOneC = new RecordOfCompletedMission($participantOne, $this->missionThree, '1c');
        $this->completedMissionTwoA = new RecordOfCompletedMission($participantTwo, $this->missionOne, '2a');
        //
        $this->metricOne = new RecordOfMetric($program, 1);
        $this->metricTwo = new RecordOfMetric($program, 2);
        
        $this->metricAssignmentOne = new RecordOfMetricAssignment($participantOne, 1);
        $this->assignmentFieldOneA = new RecordOfAssignmentField($this->metricAssignmentOne, $this->metricOne, '1a');
        $this->assignmentFieldOneA->target = 100;
        $this->assignmentFieldOneB = new RecordOfAssignmentField($this->metricAssignmentOne, $this->metricTwo, '1b');
        $this->assignmentFieldOneB->target = 200;
        
        $this->metricAssignmentTwo = new RecordOfMetricAssignment($participantTwo, 2);
        $this->assignmentFieldTwoB = new RecordOfAssignmentField($this->metricAssignmentTwo, $this->metricTwo, '2b');
        $this->assignmentFieldTwoB->target = 100000;
        
        $this->metricAssignmentThree = new RecordOfMetricAssignment($participantThree, 3);
        $this->assignmentFieldThreeA = new RecordOfAssignmentField($this->metricAssignmentThree, $this->metricOne, '3a');
        $this->assignmentFieldThreeA->target = 1000;
        
        $this->metricAssignmentReportOne = new RecordOfMetricAssignmentReport($this->metricAssignmentOne, 1);
        $this->assignmentFieldValueOneA = new RecordOfAssignmentFieldValue($this->metricAssignmentReportOne, $this->assignmentFieldOneA, '1a');
        $this->assignmentFieldValueOneA->inputValue = 80;
        $this->assignmentFieldValueOneB = new RecordOfAssignmentFieldValue($this->metricAssignmentReportOne, $this->assignmentFieldOneB, '1b');
        $this->assignmentFieldValueOneB->inputValue = 80;
        
        $this->metricAssignmentReportOne_previous = new RecordOfMetricAssignmentReport($this->metricAssignmentOne, '1_p');
        $this->metricAssignmentReportOne_previous->approved = true;
        $this->metricAssignmentReportOne_previous->observationTime = (new DateTimeImmutable('-1 months'))->format('Y-m-d H:i:s');
        $this->assignmentFieldValueOneA_previous = new RecordOfAssignmentFieldValue($this->metricAssignmentReportOne_previous, $this->assignmentFieldOneA, '1a_p');
        $this->assignmentFieldValueOneA_previous->inputValue = 60;
        $this->assignmentFieldValueOneB_previous = new RecordOfAssignmentFieldValue($this->metricAssignmentReportOne_previous, $this->assignmentFieldOneB, '1b_p');
        $this->assignmentFieldValueOneB_previous->inputValue = 60;
        
        $this->metricAssignmentReportThree = new RecordOfMetricAssignmentReport($this->metricAssignmentThree, 3);
        $this->metricAssignmentReportThree->approved = true;
        $this->assignmentFieldValueThreeA = new RecordOfAssignmentFieldValue($this->metricAssignmentReportThree, $this->assignmentFieldThreeA, '3a');
        $this->assignmentFieldValueThreeA->inputValue = 500;
        //
        $personnelOne = new RecordOfPersonnel($firm, 1);
        $personnelTwo = new RecordOfPersonnel($firm, 2);
        
        $this->consultantOne = new RecordOfConsultant($program, $personnelOne, 1);
        $this->consultantTwo = new RecordOfConsultant($program, $personnelTwo, 2);
        //
        $this->form = new RecordOfForm('00');
        
        $formRecordOne = new RecordOfFormRecord($this->form, 1);
        $formRecordTwo = new RecordOfFormRecord($this->form, 2);
        
        $this->worksheetOne = new RecordOfWorksheet($participantOne, $formRecordOne, $this->missionOne, 1);
        $this->worksheetTwo = new RecordOfWorksheet($participantTwo, $formRecordTwo, $this->missionOne, 2);
        //
        $commentOneA = new RecordOfComment($this->worksheetOne, '1a');
        $commentOneB = new RecordOfComment($this->worksheetTwo, '1b');
        $commentTwoA = new RecordOfComment($this->worksheetOne, '2a');
        
        $this->consultantCommentOneA = new RecordOfConsultantComment($this->consultantOne, $commentOneA);
        $this->consultantCommentOneB = new RecordOfConsultantComment($this->consultantOne, $commentOneB);
        $this->consultantCommentTwoA = new RecordOfConsultantComment($this->consultantTwo, $commentTwoA);
        //
        $this->missionCommentOneA = new RecordOfMissionComment($this->missionOne, null, '1a');
        $this->missionCommentOneA->rolePaths = json_encode(['mentor' => $this->consultantOne->id]);
        $this->missionCommentTwoA = new RecordOfMissionComment($this->missionOne, null, '2a');
        $this->missionCommentTwoA->rolePaths = json_encode(['mentor' => $this->consultantTwo->id]);
        $this->missionCommentTwoB = new RecordOfMissionComment($this->missionTwo, null, '2b');
        $this->missionCommentTwoB->rolePaths = json_encode(['mentor' => $this->consultantTwo->id]);
        //
        $this->consultationSetup = new RecordOfConsultationSetup($program, null, null, '00');
        
        $mentoringRequestOneA = new RecordOfMentoringRequest($participantOne, $this->consultantOne, $this->consultationSetup, '1a');
        $mentoringRequestOneA->startTime = (new \DateTime('-25 hours'))->format('Y-m-d H:i:s');
        $mentoringRequestOneA->endTime = (new \DateTime('-24 hours'))->format('Y-m-d H:i:s');
        $mentoringOneA = new RecordOfMentoring('1a');
        $this->negotiatedMentoringOneA = new RecordOfNegotiatedMentoring($mentoringRequestOneA, $mentoringOneA);
        
        $this->mentoringSlotOneB = new RecordOfMentoringSlot($this->consultantOne, $this->consultationSetup, '1b');
        $this->mentoringSlotOneB->startTime = (new \DateTime('-25 hours'))->format('Y-m-d H:i:s');
        $this->mentoringSlotOneB->endTime = (new \DateTime('-24 hours'))->format('Y-m-d H:i:s');
        $mentoringOneBOne = new RecordOfMentoring('1b1');
        $this->bookedMentoringSlotOneBOne = new RecordOfBookedMentoringSlot($this->mentoringSlotOneB, $mentoringOneBOne, $participantOne);
        $mentoringOneBTwo = new RecordOfMentoring('1b2');
        $this->bookedMentoringSlotOneBTwo = new RecordOfBookedMentoringSlot($this->mentoringSlotOneB, $mentoringOneBTwo, $participantTwo);
        
        $mentoringTwoA = new RecordOfMentoring('2a');
        $this->declaredMentoringTwoA = new RecordOfDeclaredMentoring($this->consultantTwo, $participantOne, $this->consultationSetup, $mentoringTwoA);
        
        $this->participantReportOneA = new RecordOfParticipantReport($mentoringOneA, null, '1a');
        $this->participantReportOneA->mentorRating = 4;
        $this->participantReportOneATwo = new RecordOfParticipantReport($mentoringOneBOne, null, '1b1');
        $this->participantReportOneATwo->mentorRating = 3;
        $this->participantReportTwoA = new RecordOfParticipantReport($mentoringTwoA, null, '2a');
        $this->participantReportTwoA->mentorRating = 4;
        
        $this->mentorReportOneAOne = new RecordOfMentorReport($mentoringOneA, null, '1a');
        $this->mentorReportTwoA = new RecordOfMentorReport($mentoringTwoA, null, '2a');
        //
        $this->uri = $this->coordinatorUri . "/program-dashboard";
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('User')->truncate();
        //
        $this->connection->table('Registrant')->truncate();
        $this->connection->table('ClientRegistrant')->truncate();
        $this->connection->table('TeamRegistrant')->truncate();
        $this->connection->table('UserRegistrant')->truncate();
        //
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        //
        $this->connection->table('Mission')->truncate();
        $this->connection->table('CompletedMission')->truncate();
        //
        $this->connection->table('Metric')->truncate();
        $this->connection->table('MetricAssignment')->truncate();
        $this->connection->table('AssignmentField')->truncate();
        $this->connection->table('MetricAssignmentReport')->truncate();
        $this->connection->table('AssignmentFieldValue')->truncate();
        //
        $this->connection->table('Consultant')->truncate();
        
        $this->connection->table('Form')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('Worksheet')->truncate();
        
        $this->connection->table('Comment')->truncate();
        $this->connection->table('ConsultantComment')->truncate();
        $this->connection->table('MissionComment')->truncate();
        //
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        $this->connection->table('NegotiatedMentoring')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('BookedMentoringSlot')->truncate();
        $this->connection->table('DeclaredMentoring')->truncate();
        $this->connection->table('ParticipantReport')->truncate();
        $this->connection->table('MentorReport')->truncate();
    }
    
    //
    protected function view()
    {
        $this->persistCoordinatorDependency();
        //
        $this->clientRegistrantOne->client->insert($this->connection);
        $this->teamRegistrantTwo->team->insert($this->connection);
        
        $this->clientRegistrantOne->insert($this->connection);
        $this->teamRegistrantTwo->insert($this->connection);
        //
        $this->clientParticipantOne->client->insert($this->connection);
        $this->teamParticipantTwo->team->insert($this->connection);
        $this->userParticipantThree->user->insert($this->connection);
        $this->clientParticipantFour->client->insert($this->connection);
        
        $this->clientParticipantOne->insert($this->connection);
        $this->teamParticipantTwo->insert($this->connection);
        $this->userParticipantThree->insert($this->connection);
        $this->clientParticipantFour->insert($this->connection);
        //
        $this->missionOne->insert($this->connection);
        $this->missionTwo->insert($this->connection);
        $this->missionThree->insert($this->connection);
        
        $this->completedMissionOneA->insert($this->connection);
        $this->completedMissionOneB->insert($this->connection);
        $this->completedMissionOneC->insert($this->connection);
        $this->completedMissionTwoA->insert($this->connection);
        //
        $this->metricOne->insert($this->connection);
        $this->metricTwo->insert($this->connection);
        
        $this->metricAssignmentOne->insert($this->connection);
        $this->assignmentFieldOneA->insert($this->connection);
        $this->assignmentFieldOneB->insert($this->connection);
        
        $this->metricAssignmentThree->insert($this->connection);
        $this->assignmentFieldThreeA->insert($this->connection);
        //
        $this->metricAssignmentReportOne->insert($this->connection);
        $this->assignmentFieldValueOneA->insert($this->connection);
        $this->assignmentFieldValueOneB->insert($this->connection);
        
        $this->metricAssignmentReportOne_previous->insert($this->connection);
        $this->assignmentFieldValueOneA_previous->insert($this->connection);
        $this->assignmentFieldValueOneB_previous->insert($this->connection);
        
        $this->metricAssignmentReportThree->insert($this->connection);
        $this->assignmentFieldValueThreeA->insert($this->connection);
        //
        $this->consultantOne->personnel->insert($this->connection);
        $this->consultantTwo->personnel->insert($this->connection);
        
        $this->consultantOne->insert($this->connection);
        $this->consultantTwo->insert($this->connection);
        //
        $this->form->insert($this->connection);
        
        $this->worksheetOne->insert($this->connection);
        $this->worksheetTwo->insert($this->connection);
        //
        $this->consultantCommentOneA->insert($this->connection);
        $this->consultantCommentOneB->insert($this->connection);
        $this->consultantCommentTwoA->insert($this->connection);
        //
        $this->missionCommentOneA->insert($this->connection);
        $this->missionCommentTwoA->insert($this->connection);
        $this->missionCommentTwoB->insert($this->connection);
        //
        $this->negotiatedMentoringOneA->mentoringRequest->insert($this->connection);
        $this->negotiatedMentoringOneA->insert($this->connection);
        $this->mentoringSlotOneB->insert($this->connection);
        $this->bookedMentoringSlotOneBOne->insert($this->connection);
        $this->bookedMentoringSlotOneBTwo->insert($this->connection);
        $this->declaredMentoringTwoA->insert($this->connection);
        //
        $this->participantReportOneA->insert($this->connection);
        $this->participantReportOneATwo->insert($this->connection);
        $this->participantReportTwoA->insert($this->connection);
        //
        $this->mentorReportOneAOne->insert($this->connection);
        $this->mentorReportTwoA->insert($this->connection);
        
        $this->get($this->uri, $this->coordinator->personnel->token);
echo $this->uri;
$this->seeJsonContains(['print']);
    }
    public function test_view_200()
    {
$this->disableExceptionHandling();
        $this->view();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains([
            'id' => $this->coordinator->program->id,
            'name' => $this->coordinator->program->name,
            'participantCount' => '4',
            'minCompletedMission' => '0',
            'maxCompletedMission' => '3',
            'averageCompletedMission' => '1.0000', // 33%
            'missionCount' => '3',
            'minMetricAchievement' => '0.44999999999999996', //45%
            'maxMetricAchievement' => '0.5',
            'averageMetricAchievement' => '0.475',
            'minMetricCompletion' => '0.0000',
            'maxMetricCompletion' => '0.0000',
            'averageMetricCompletion' => '0.00000000',
            'newApplicantInProgramCount' => '2',
            'unreviewedMetricReportCount' => '1',
            'consultants' => [
                [
                    'id' => $this->consultantOne->id,
                    'name' => $this->consultantOne->personnel->getFullName(),
                    'averageMentorRating' => '3.5000',
                    'submittedReportCount' => '1',
                    'completedSessionCount' => '2',
                    'completedMenteeCount' => '3',
                    'worksheetCommentCount' => '2',
                    'missionDiscussionCount' => '1',
                    'upcomingSessionCount' => null,
                    'upcomingMenteeCount' => null,
                    'availableMentoringSlotSessionCount' => null,
                    'availableSlotCount' => null,
                ],
                [
                    'id' => $this->consultantTwo->id,
                    'name' => $this->consultantTwo->personnel->getFullName(),
                    'averageMentorRating' => '4.0000',
                    'submittedReportCount' => '1',
                    'completedSessionCount' => '1',
                    'completedMenteeCount' => '1',
                    'worksheetCommentCount' => '1',
                    'missionDiscussionCount' => '2',
                    'upcomingSessionCount' => null,
                    'upcomingMenteeCount' => null,
                    'availableMentoringSlotSessionCount' => null,
                    'availableSlotCount' => null,
                ],
            ],
            'participants' => [
                'topMissionCompletion' => [
                    [
                        'id' => $this->clientParticipantOne->participant->id,
                        'userId' => null,
                        'clientId' => $this->clientParticipantOne->client->id,
                        'teamId' => null,
                        'name' => $this->clientParticipantOne->client->getFullName(),
                        'totalCompletedMission' => '3',
                        'totalMission' => '3',
                        'missionCompletion' => '100',
                    ],
                    [
                        'id' => $this->teamParticipantTwo->participant->id,
                        'userId' => null,
                        'clientId' => null,
                        'teamId' => $this->teamParticipantTwo->team->id,
                        'name' => $this->teamParticipantTwo->team->name,
                        'totalCompletedMission' => '1',
                        'totalMission' => '3',
                        'missionCompletion' => '33',
                    ],
                    [
                        'id' => $this->userParticipantThree->participant->id,
                        'userId' => $this->userParticipantThree->user->id,
                        'clientId' => null,
                        'teamId' => null,
                        'name' => $this->userParticipantThree->user->getFullName(),
                        'totalCompletedMission' => null,
                        'totalMission' => '3',
                        'missionCompletion' => null,
                    ],
                ],
                'bottomMissionCompletion' => [
                    [
                        'id' => $this->clientParticipantFour->participant->id,
                        'clientId' => $this->clientParticipantFour->client->id,
                        'name' => $this->clientParticipantFour->client->getFullName(),
                        'userId' => null,
                        'teamId' => null,
                        'totalCompletedMission' => null,
                        'totalMission' => '3',
                        'missionCompletion' => null,
                    ],
                    [
                        'id' => $this->userParticipantThree->participant->id,
                        'userId' => $this->userParticipantThree->user->id,
                        'clientId' => null,
                        'teamId' => null,
                        'name' => $this->userParticipantThree->user->getFullName(),
                        'totalCompletedMission' => null,
                        'totalMission' => '3',
                        'missionCompletion' => null,
                    ],
                    [
                        'id' => $this->teamParticipantTwo->participant->id,
                        'userId' => null,
                        'clientId' => null,
                        'teamId' => $this->teamParticipantTwo->team->id,
                        'name' => $this->teamParticipantTwo->team->name,
                        'totalCompletedMission' => '1',
                        'totalMission' => '3',
                        'missionCompletion' => '33',
                    ],
                ],
                'topMetricAchievement' => [
                    [
                        'id' => $this->userParticipantThree->participant->id,
                        'userId' => $this->userParticipantThree->user->id,
                        'name' => $this->userParticipantThree->user->getFullName(),
                        'clientId' => null,
                        'teamId' => null,
                        'normalizedAchievement' => '50',
                        'achievement' => '50',
                        'completedMetric' => '0',
                        'totalAssignedMetric' => '1',
                    ],
                    [
                        'id' => $this->clientParticipantOne->participant->id,
                        'clientId' => $this->clientParticipantOne->client->id,
                        'name' => $this->clientParticipantOne->client->getFullName(),
                        'userId' => null,
                        'teamId' => null,
                        'normalizedAchievement' => '45',
                        'achievement' => '45',
                        'completedMetric' => '0',
                        'totalAssignedMetric' => '2',
                    ],
                    [
                        'id' => $this->teamParticipantTwo->participant->id,
                        'teamId' => $this->teamParticipantTwo->team->id,
                        'name' => $this->teamParticipantTwo->team->name,
                        'userId' => null,
                        'clientId' => null,
                        'normalizedAchievement' => null,
                        'achievement' => null,
                        'completedMetric' => null,
                        'totalAssignedMetric' => null,
                    ],
                ],
                'bottomMetricAchievement' => [
                    [
                        'id' => $this->clientParticipantFour->participant->id,
                        'clientId' => $this->clientParticipantFour->client->id,
                        'name' => $this->clientParticipantFour->client->getFullName(),
                        'userId' => null,
                        'teamId' => null,
                        'normalizedAchievement' => null,
                        'achievement' => null,
                        'completedMetric' => null,
                        'totalAssignedMetric' => null,
                    ],
                    [
                        'id' => $this->teamParticipantTwo->participant->id,
                        'teamId' => $this->teamParticipantTwo->team->id,
                        'name' => $this->teamParticipantTwo->team->name,
                        'userId' => null,
                        'clientId' => null,
                        'normalizedAchievement' => null,
                        'achievement' => null,
                        'completedMetric' => null,
                        'totalAssignedMetric' => null,
                    ],
                    [
                        'id' => $this->clientParticipantOne->participant->id,
                        'clientId' => $this->clientParticipantOne->client->id,
                        'name' => $this->clientParticipantOne->client->getFullName(),
                        'userId' => null,
                        'teamId' => null,
                        'normalizedAchievement' => '45',
                        'achievement' => '45',
                        'completedMetric' => '0',
                        'totalAssignedMetric' => '2',
                    ],
                ],
            ],
        ]);
    }

}
