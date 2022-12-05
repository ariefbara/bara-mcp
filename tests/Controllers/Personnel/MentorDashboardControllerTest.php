<?php

namespace Tests\Controllers\Personnel;

use DateTime;
use SharedContext\Domain\ValueObject\MentoringRequestStatus;
use SharedContext\Domain\ValueObject\TaskReportReviewStatus;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Activity\Invitee\RecordOfInviteeReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Activity\RecordOfInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\ActivityType\RecordOfActivityParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\MentoringSlot\RecordOfBookedMentoringSlot;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfConsultantComment;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfConsultantInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfConsultantTask;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfMentoringSlot;
use Tests\Controllers\RecordPreparation\Firm\Program\Coordinator\RecordOfCoordinatorTask;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MentoringRequest\RecordOfNegotiatedMentoring;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfDedicatedMentor;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMentoringRequest;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfTask;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfWorksheet;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\Task\RecordOfTaskReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\Worksheet\RecordOfComment;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfActivity;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfActivityType;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\RecordOfWorksheetForm;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\Shared\Mentoring\RecordOfMentorReport;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;
use Tests\Controllers\RecordPreparation\Shared\RecordOfMentoring;

class MentorDashboardControllerTest extends PersonnelTestCase
{

    protected $viewUri;
    protected $mentorOne;
    protected $mentorTwo;
    protected $otherMentor;
    //
    protected $clientParticipantOne;
    protected $teamParticipantOne;
    //
    protected $dedicatedMentorOne_m1p1;
    protected $dedicatedMentorTwo_m2p2;
    //
    protected $mentoringRequestOne;
    protected $mentoringRequestTwo;
    //
    protected $consultantInviteeOne;
    protected $consultantInviteeTwo;
    protected $inviteeReportOne_ci1;
    //
    protected $negotiatedMentoringOne;
    protected $negotiatedMentoringTwo;
    protected $bookedMentoringSlotOne;
    protected $bookedMentoringSlotTwo;
    protected $mentorReportOne_nm1;
    protected $mentorReportTwo_bms1;
    //
    protected $worksheetOne_p1;
    protected $worksheetTwo_p2;
    protected $consultantCommentOne_w1;
    //
    protected $consultantTaskOne;
    protected $coordinatorTaskTwo;
    protected $consultantTaskThree;
    protected $taskReportTwo;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->connection->table('Program')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('DedicatedMentor')->truncate();
        //
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        //
        $this->connection->table('ActivityType')->truncate();
        $this->connection->table('Activity')->truncate();
        $this->connection->table('ActivityParticipant')->truncate();
        $this->connection->table('Invitee')->truncate();
        $this->connection->table('ConsultantInvitee')->truncate();
        $this->connection->table('InviteeReport')->truncate();
        //
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('NegotiatedMentoring')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('BookedMentoringSlot')->truncate();
        $this->connection->table('MentorReport')->truncate();
        //
        $this->connection->table('Form')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('Worksheet')->truncate();
        $this->connection->table('Comment')->truncate();
        $this->connection->table('ConsultantComment')->truncate();
        //
        $this->connection->table('Task')->truncate();
        $this->connection->table('ConsultantTask')->truncate();
        $this->connection->table('CoordinatorTask')->truncate();
        $this->connection->table('Coordinator')->truncate();
        $this->connection->table('TaskReport')->truncate();

        //
        $this->viewUri = $this->personnelUri . "/mentor-dashboard";
        //
        $firm = $this->personnel->firm;
        //
        $programOne = new RecordOfProgram($firm, 1);
        $programTwo = new RecordOfProgram($firm, 2);
        
        $otherPersonnel = new RecordOfPersonnel($firm, 'other');
        //
        $this->mentorOne = new RecordOfConsultant($programOne, $this->personnel, 1);
        $this->mentorTwo = new RecordOfConsultant($programTwo, $this->personnel, 2);
        $this->otherMentor = new RecordOfConsultant($programOne, $otherPersonnel, 'other');
        //
        $participantOne = new RecordOfParticipant($programOne, 1);
        $participantTwo = new RecordOfParticipant($programTwo, 2);

        $clientOne = new RecordOfClient($firm, 1);

        $this->clientParticipantOne = new RecordOfClientParticipant($clientOne, $participantOne);

        $teamOne = new RecordOfTeam($firm, $clientOne, 1);

        $this->teamParticipantOne = new RecordOfTeamProgramParticipation($teamOne, $participantTwo);
        //
        $this->dedicatedMentorOne_m1p1 = new RecordOfDedicatedMentor($participantOne, $this->mentorOne, 1);
        $this->dedicatedMentorTwo_m2p2 = new RecordOfDedicatedMentor($participantTwo, $this->mentorTwo, 2);
        //
        $consultationSetupOne = new RecordOfConsultationSetup($programOne, null, null, 1);
        $consultationSetupTwo = new RecordOfConsultationSetup($programTwo, null, null, 2);

        $this->mentoringRequestOne = new RecordOfMentoringRequest($participantOne, $this->mentorOne, $consultationSetupOne, 1);
        $this->mentoringRequestTwo = new RecordOfMentoringRequest($participantTwo, $this->mentorTwo, $consultationSetupTwo, 2);
        $mentoringRequestThree = new RecordOfMentoringRequest($participantOne, $this->mentorOne, $consultationSetupOne, 3);
        $mentoringRequestThree->requestStatus = MentoringRequestStatus::APPROVED_BY_MENTOR;
        $mentoringRequestThree->startTime = (new \DateTime('-73 hours'))->format('Y-m-d H:i:s');
        $mentoringRequestThree->endTime = (new \DateTime('-72 hours'))->format('Y-m-d H:i:s');
        $mentoringRequestFour = new RecordOfMentoringRequest($participantTwo, $this->mentorTwo, $consultationSetupTwo, 4);
        $mentoringRequestFour->requestStatus = MentoringRequestStatus::APPROVED_BY_MENTOR;
        $mentoringRequestFour->startTime = (new \DateTime('-25 hours'))->format('Y-m-d H:i:s');
        $mentoringRequestFour->endTime = (new \DateTime('-24 hours'))->format('Y-m-d H:i:s');
        //
        $activityTypeOne = new RecordOfActivityType($programOne, 1);
        $activityTypeTwo = new RecordOfActivityType($programTwo, 2);
        
        $activityOne = new RecordOfActivity($activityTypeOne, 1);
        $activityOne->startDateTime = (new \DateTime('-49 hours'));
        $activityOne->endDateTime = (new \DateTime('-48 hours'));
        $activityTwo = new RecordOfActivity($activityTypeTwo, 2);
        $activityTwo->startDateTime = (new \DateTime('-49 hours'));
        $activityTwo->endDateTime = (new \DateTime('-48 hours'));
        
        $activityParticipantOne = new RecordOfActivityParticipant($activityTypeOne, null, 1);
        $activityParticipantTwo = new RecordOfActivityParticipant($activityTypeTwo, null, 2);
        
        $inviteeOne = new RecordOfInvitee($activityOne, $activityParticipantOne, 1);
        $inviteeTwo = new RecordOfInvitee($activityTwo, $activityParticipantTwo, 2);
        
        $this->consultantInviteeOne = new RecordOfConsultantInvitee($this->mentorOne, $inviteeOne);
        $this->consultantInviteeTwo = new RecordOfConsultantInvitee($this->mentorTwo, $inviteeTwo);
        //
        $mentoringOne = new RecordOfMentoring(1);
        $mentoringTwo = new RecordOfMentoring(2);
        $mentoringThree = new RecordOfMentoring(3);
        $mentoringFour = new RecordOfMentoring(4);
        
        $this->negotiatedMentoringOne = new RecordOfNegotiatedMentoring($mentoringRequestThree, $mentoringOne);
        $this->negotiatedMentoringTwo = new RecordOfNegotiatedMentoring($mentoringRequestFour, $mentoringTwo);
        
        $mentoringSlotOne = new RecordOfMentoringSlot($this->mentorOne, $consultationSetupOne, 1);
        $mentoringSlotOne->startTime = (new DateTime('-73 hours'))->format('Y-m-d H:i:s');
        $mentoringSlotOne->endTime = (new DateTime('-72 hours'))->format('Y-m-d H:i:s');
        $mentoringSlotTwo = new RecordOfMentoringSlot($this->mentorTwo, $consultationSetupTwo, 2);
        $mentoringSlotTwo->startTime = (new DateTime('-25 hours'))->format('Y-m-d H:i:s');
        $mentoringSlotTwo->endTime = (new DateTime('-24 hours'))->format('Y-m-d H:i:s');
        
        $this->bookedMentoringSlotOne = new RecordOfBookedMentoringSlot($mentoringSlotOne, $mentoringThree, $participantOne);
        $this->bookedMentoringSlotTwo = new RecordOfBookedMentoringSlot($mentoringSlotTwo, $mentoringFour, $participantTwo);
        
        //
        $formOne = new RecordOfForm(1);
        $formTwo = new RecordOfForm(2);
        
        $formRecordOne = new RecordOfFormRecord($formOne, 1);
        $formRecordTwo = new RecordOfFormRecord($formTwo, 2);
        $formRecordThree = new RecordOfFormRecord($formOne, 3);
        $formRecordFour = new RecordOfFormRecord($formTwo, 4);
        $formRecordFive = new RecordOfFormRecord($formTwo, 5);
        
        $worksheetFormOne = new RecordOfWorksheetForm($firm, $formOne);
        $worksheetFormTwo = new RecordOfWorksheetForm($firm, $formTwo);
        
        $missionOne = new RecordOfMission($programOne, $worksheetFormOne, 1, null);
        $missionTwo = new RecordOfMission($programTwo, $worksheetFormTwo, 2, null);
        
        $this->worksheetOne_p1 = new RecordOfWorksheet($participantOne, $formRecordOne, $missionOne, 1);
        $this->worksheetTwo_p2 = new RecordOfWorksheet($participantTwo, $formRecordTwo, $missionTwo, 2);
        //
        $this->inviteeReportOne_ci1 = new RecordOfInviteeReport($this->consultantInviteeOne->invitee, $formRecordFive);
        //
        $this->mentorReportOne_nm1 = new RecordOfMentorReport($mentoringOne, $formRecordThree, 1);
        $this->mentorReportTwo_bms1 = new RecordOfMentorReport($mentoringThree, $formRecordFour, 2);
        
        $commentOne = new RecordOfComment($this->worksheetOne_p1, 1);
        $this->consultantCommentOne_w1 = new RecordOfConsultantComment($this->mentorOne, $commentOne);
        
        //
        $taskOne = new RecordOfTask($participantOne, 1);
        $taskTwo = new RecordOfTask($participantTwo, 2);
        $taskThree = new RecordOfTask($participantTwo, 3);
        
        $coordinatorTwo = new RecordOfCoordinator($programTwo, $this->personnel, 2);
        
        $this->consultantTaskOne = new RecordOfConsultantTask($this->mentorOne, $taskOne);
        $this->consultantTaskThree = new RecordOfConsultantTask($this->mentorTwo, $taskThree);
        $this->coordinatorTaskTwo = new RecordOfCoordinatorTask($coordinatorTwo, $taskTwo);
        
        $this->taskReportTwo = new RecordOfTaskReport($taskTwo, 2);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('DedicatedMentor')->truncate();
        //
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        //
        $this->connection->table('ActivityType')->truncate();
        $this->connection->table('Activity')->truncate();
        $this->connection->table('ActivityParticipant')->truncate();
        $this->connection->table('Invitee')->truncate();
        $this->connection->table('ConsultantInvitee')->truncate();
        $this->connection->table('InviteeReport')->truncate();
        //
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('NegotiatedMentoring')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('BookedMentoringSlot')->truncate();
        $this->connection->table('MentorReport')->truncate();
        //
        $this->connection->table('Form')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('Worksheet')->truncate();
        $this->connection->table('Comment')->truncate();
        $this->connection->table('ConsultantComment')->truncate();
        
        $this->connection->table('Task')->truncate();
        $this->connection->table('ConsultantTask')->truncate();
        $this->connection->table('CoordinatorTask')->truncate();
        $this->connection->table('Coordinator')->truncate();
        $this->connection->table('TaskReport')->truncate();
    }
    
    protected function view()
    {
        
        $this->dedicatedMentorOne_m1p1->consultant->program->insert($this->connection);
        $this->dedicatedMentorTwo_m2p2->consultant->program->insert($this->connection);
        
        $this->dedicatedMentorOne_m1p1->consultant->insert($this->connection);
        $this->dedicatedMentorTwo_m2p2->consultant->insert($this->connection);
        
        $this->clientParticipantOne->client->insert($this->connection);
        $this->teamParticipantOne->team->insert($this->connection);
        
        $this->clientParticipantOne->insert($this->connection);
        $this->teamParticipantOne->insert($this->connection);
        
        $this->dedicatedMentorOne_m1p1->insert($this->connection);
        $this->dedicatedMentorTwo_m2p2->insert($this->connection);
        //
        $this->mentoringRequestOne->consultationSetup->insert($this->connection);
        $this->mentoringRequestTwo->consultationSetup->insert($this->connection);
        
        $this->mentoringRequestOne->insert($this->connection);
        $this->mentoringRequestTwo->insert($this->connection);
        //
        $this->consultantInviteeOne->invitee->activity->activityType->insert($this->connection);
        $this->consultantInviteeTwo->invitee->activity->activityType->insert($this->connection);
        
        $this->consultantInviteeOne->invitee->activity->insert($this->connection);
        $this->consultantInviteeTwo->invitee->activity->insert($this->connection);
        
        $this->consultantInviteeOne->invitee->activityParticipant->insert($this->connection);
        $this->consultantInviteeTwo->invitee->activityParticipant->insert($this->connection);
        
        $this->consultantInviteeOne->insert($this->connection);
        $this->consultantInviteeTwo->insert($this->connection);
        //
        $this->negotiatedMentoringOne->mentoringRequest->insert($this->connection);
        $this->negotiatedMentoringTwo->mentoringRequest->insert($this->connection);
        
        $this->negotiatedMentoringOne->insert($this->connection);
        $this->negotiatedMentoringTwo->insert($this->connection);
        
        $this->bookedMentoringSlotOne->mentoringSlot->insert($this->connection);
        $this->bookedMentoringSlotTwo->mentoringSlot->insert($this->connection);
        
        $this->bookedMentoringSlotOne->insert($this->connection);
        $this->bookedMentoringSlotTwo->insert($this->connection);
        //
        $this->worksheetOne_p1->mission->worksheetForm->form->insert($this->connection);
        $this->worksheetTwo_p2->mission->worksheetForm->form->insert($this->connection);
        
        $this->worksheetOne_p1->mission->worksheetForm->insert($this->connection);
        $this->worksheetTwo_p2->mission->worksheetForm->insert($this->connection);
        
        $this->worksheetOne_p1->mission->insert($this->connection);
        $this->worksheetTwo_p2->mission->insert($this->connection);
        
        $this->worksheetOne_p1->insert($this->connection);
        $this->worksheetTwo_p2->insert($this->connection);
        //
        $this->consultantTaskOne->insert($this->connection);
        $this->consultantTaskThree->insert($this->connection);
        
        $this->coordinatorTaskTwo->coordinator->insert($this->connection);
        $this->coordinatorTaskTwo->insert($this->connection);
        
        $this->taskReportTwo->insert($this->connection);
        //
        $this->get($this->viewUri, $this->personnel->token);
echo $this->viewUri;
$this->seeJsonContains(['print']);
    }
    public function test_view_200()
    {
$this->disableExceptionHandling();
        $this->view();
        $this->seeStatusCode(200);
        
        $response = [
            'unrespondedMentoringRequest' => '2',
            'pendingActivityReport' => '2',
            'pendingMentoringReport' => '4',
            'newWorksheetSubmission' => '2',
            'incompleteTask' => '3',
        ];
        $this->seeJsonContains($response);
    }
    
    public function test_view_pendingActivityReport_containUpcomingInvitation_excludeFromResult()
    {
        $this->consultantInviteeOne->invitee->activity->startDateTime = (new \DateTime('+23 hours'))->format('Y-m-d H:i:s');
        $this->consultantInviteeOne->invitee->activity->endDateTime = (new \DateTime('+24 hours'))->format('Y-m-d H:i:s');
        
        $this->view();
        $this->seeStatusCode(200);
        $this->seeJsonContains(['pendingActivityReport' => '1']);
    }
    public function test_view_pendingActivityReport_containReportedInvitation_excludeFromResult()
    {
        $this->inviteeReportOne_ci1->insert($this->connection);
        
        $this->view();
        $this->seeStatusCode(200);
        $this->seeJsonContains(['pendingActivityReport' => '1']);
    }
    public function test_view_pendingActivityReport_excludeInvitationToInactiveMentor()
    {
        $this->mentorOne->active = false;
        
        $this->view();
        $this->seeStatusCode(200);
        $this->seeJsonContains(['pendingActivityReport' => '1']);
    }
    public function test_view_pendingActivityReport_excludeInvitationToOtherPersonnel()
    {
        $this->otherMentor->personnel->insert($this->connection);
        $this->otherMentor->insert($this->connection);
        $this->consultantInviteeOne->consultant = $this->otherMentor;
        
        $this->view();
        $this->seeStatusCode(200);
        $this->seeJsonContains(['pendingActivityReport' => '1']);
    }
    
    public function test_view_unrespondedMentoringRequest_excludeRespondedMentoringRequest()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::OFFERED;
        
        $this->view();
        $this->seeStatusCode(200);
        $this->seeJsonContains(['unrespondedMentoringRequest' => '1']);
    }
    public function test_view_unrespondedMentoringRequest_excludeRequestToInactiveMentor()
    {
        $this->mentorOne->active = false;
        
        $this->view();
        $this->seeStatusCode(200);
        $this->seeJsonContains(['unrespondedMentoringRequest' => '1']);
    }
    public function test_view_unrespondedMentoringRequest_excludeRequestFromInactiveParticipant()
    {
        $this->mentoringRequestOne->participant->active = false;
        
        $this->view();
        $this->seeStatusCode(200);
        $this->seeJsonContains(['unrespondedMentoringRequest' => '1']);
    }
    public function test_view_unrespondedMentoringRequest_excludeRequestToOtherMentor()
    {
        $this->otherMentor->personnel->insert($this->connection);
        $this->otherMentor->insert($this->connection);
        $this->mentoringRequestOne->mentor = $this->otherMentor;
        
        $this->view();
        $this->seeStatusCode(200);
        $this->seeJsonContains(['unrespondedMentoringRequest' => '1']);
    }
    
    public function test_view_pendingMentoringReport_excludedUpcomingNegotiatedMentoring()
    {
        $this->negotiatedMentoringOne->mentoringRequest->startTime = (new \DateTime('+72 hours'))->format('Y-m-d H:i:s');
        $this->negotiatedMentoringOne->mentoringRequest->endTime = (new \DateTime('+73 hours'))->format('Y-m-d H:i:s');
        
        $this->view();
        $this->seeStatusCode(200);
        $this->seeJsonContains(['pendingMentoringReport' => '3']);
    }
    public function test_view_pendingMentoringReport_excludedReportedNegotiatedMentoring()
    {
        $this->mentorReportOne_nm1->insert($this->connection);
        
        $this->view();
        $this->seeStatusCode(200);
        $this->seeJsonContains(['pendingMentoringReport' => '3']);
    }
    public function test_view_pendingMentoringReport_excludedRequestToOtherMentor()
    {
        $this->otherMentor->personnel->insert($this->connection);
        $this->otherMentor->insert($this->connection);
        $this->negotiatedMentoringOne->mentoringRequest->mentor = $this->otherMentor;
        
        $this->view();
        $this->seeStatusCode(200);
        $this->seeJsonContains(['pendingMentoringReport' => '3']);
    }
    public function test_view_pendingMentoringReport_excludedRequestToInactiveMentor()
    {
        $this->mentorOne->active = false;
        
        $this->view();
        $this->seeStatusCode(200);
        $this->seeJsonContains(['pendingMentoringReport' => '2']);
    }
    public function test_view_pendingMentoringReport_excludedRequestFromInactiveParticipant()
    {
        $this->negotiatedMentoringOne->mentoringRequest->participant->active = false;
        
        $this->view();
        $this->seeStatusCode(200);
        $this->seeJsonContains(['pendingMentoringReport' => '2']);
    }
    public function test_view_pendingMentoringReport_excludedUpcomingMentoringSlot()
    {
        $this->bookedMentoringSlotOne->mentoringSlot->startTime = (new \DateTime('+24 hours'))->format('Y-m-d H:i:s');
        $this->bookedMentoringSlotOne->mentoringSlot->endTime = (new \DateTime('+25 hours'))->format('Y-m-d H:i:s');
        
        $this->view();
        $this->seeStatusCode(200);
        $this->seeJsonContains(['pendingMentoringReport' => '3']);
    }
    public function test_view_pendingMentoringReport_excludedReportedBookedMentoringSlot()
    {
        $this->mentorReportTwo_bms1->insert($this->connection);
        
        $this->view();
        $this->seeStatusCode(200);
        $this->seeJsonContains(['pendingMentoringReport' => '3']);
    }
    public function test_view_pendingMentoringReport_excludedMentoringSlotOfOtherMentor()
    {
        $this->otherMentor->personnel->insert($this->connection);
        $this->otherMentor->insert($this->connection);
        $this->bookedMentoringSlotOne->mentoringSlot->consultant = $this->otherMentor;
        
        $this->view();
        $this->seeStatusCode(200);
        $this->seeJsonContains(['pendingMentoringReport' => '3']);
    }
    public function test_view_pendingMentoringReport_excludedMentoringSlotOfInactiveMentor()
    {
        $this->bookedMentoringSlotOne->mentoringSlot->consultant->active = false;
        
        $this->view();
        $this->seeStatusCode(200);
        $this->seeJsonContains(['pendingMentoringReport' => '2']);
    }
    public function test_view_pendingMentoringReport_excludedBookedMentoringSlotFromInactiveParticipant()
    {
        $this->bookedMentoringSlotOne->participant->active = false;
        
        $this->view();
        $this->seeStatusCode(200);
        $this->seeJsonContains(['pendingMentoringReport' => '2']);
    }
    
    public function test_view_newWorksheetSubmission_excludeRemovedWorksheet()
    {
        $this->worksheetOne_p1->removed = true;
        
        $this->view();
        $this->seeStatusCode(200);
        $this->seeJsonContains(['newWorksheetSubmission' => '1']);
    }
    public function test_view_newWorksheetSubmission_excludeCommentedWorksheet()
    {
        $this->consultantCommentOne_w1->insert($this->connection);
        
        $this->view();
        $this->seeStatusCode(200);
        $this->seeJsonContains(['newWorksheetSubmission' => '1']);
    }
    public function test_view_newWorksheetSubmission_includeWorksheetIfCommentOnlyFromOtherMentor()
    {
        $this->otherMentor->personnel->insert($this->connection);
        $this->otherMentor->insert($this->connection);
        $this->consultantCommentOne_w1->consultant = $this->otherMentor;
        $this->consultantCommentOne_w1->insert($this->connection);
        
        $this->view();
        $this->seeStatusCode(200);
        $this->seeJsonContains(['newWorksheetSubmission' => '2']);
    }
    public function test_view_newWorksheetSubmission_excludeWorksheetOfUndedicatedMentee()
    {
        $this->dedicatedMentorOne_m1p1->cancelled = true;
        
        $this->view();
        $this->seeStatusCode(200);
        $this->seeJsonContains(['newWorksheetSubmission' => '1']);
    }
    public function test_view_newWorksheetSubmission_excludeWorksheetFromDedicatedMenteeTofInactiveMentor()
    {
        $this->dedicatedMentorOne_m1p1->consultant->active = false;
        
        $this->view();
        $this->seeStatusCode(200);
        $this->seeJsonContains(['newWorksheetSubmission' => '1']);
    }
    public function test_view_newWorksheetSubmission_excludeWorksheetOfInactiveParticipant()
    {
        $this->dedicatedMentorOne_m1p1->participant->active = false;
        
        $this->view();
        $this->seeStatusCode(200);
        $this->seeJsonContains(['newWorksheetSubmission' => '1']);
    }
    //
    public function test_view_excludeCompleteTask()
    {
        $this->taskReportTwo->reviewStatus = TaskReportReviewStatus::APPROVED;
        $this->view();
        $this->seeJsonContains(['incompleteTask' => '2']);
    }
    public function test_view_excludeCancelledTask()
    {
        $this->consultantTaskOne->task->cancelled = true;
        $this->view();
        $this->seeJsonContains(['incompleteTask' => '2']);
    }
    public function test_view_excludeTaskToNonDedicatedMentee()
    {
        $this->dedicatedMentorOne_m1p1->cancelled = true;
        $this->otherMentor->insert($this->connection);
        $this->consultantTaskOne->consultant = $this->otherMentor;
        
        $this->view();
        $this->seeJsonContains(['incompleteTask' => '2']);
    }
    public function test_view_includeOwnTaskToNonDedicatedMentee()
    {
        $this->dedicatedMentorOne_m1p1->cancelled = true;
        
        $this->view();
        $this->seeJsonContains(['incompleteTask' => '3']);
    }

}
