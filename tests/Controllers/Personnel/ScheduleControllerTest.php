<?php

namespace Tests\Controllers\Personnel\Coordinator;

use DateTime;
use Tests\Controllers\Personnel\ExtendedPersonnelTestCase;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Manager\RecordOfManagerInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\Activity\RecordOfInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\ActivityType\RecordOfActivityParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\MentoringSlot\RecordOfBookedMentoringSlot;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfMentoringSlot;
use Tests\Controllers\RecordPreparation\Firm\Program\Coordinator\RecordOfCoordinatorInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MentoringRequest\RecordOfNegotiatedMentoring;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfDeclaredMentoring;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMentoringRequest;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfParticipantInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfActivity;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfActivityType;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfManager;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\RecordOfUser;
use Tests\Controllers\RecordPreparation\Shared\RecordOfMentoring;
use Tests\Controllers\RecordPreparation\User\RecordOfUserParticipant;

class ScheduleControllerTest extends ExtendedPersonnelTestCase
{
    protected $programOne;
    protected $programTwo;

    protected $clientParticipantOne;
    protected $teamParticipantTwo;
    protected $userParticipantThree;
    protected $teamParticipant_21;
    //
    protected $coordinatorOne;
    protected $consultantOne;
    protected $consultantTwo;
    protected $managerOne;
    
    //
    protected $consultationSetup;
    protected $consultationSetupTwo;
    protected $mentoringSlotOne;
    protected $bookedMentoringSlotOneA;
    protected $bookedMentoringSlotOneB;
    protected $negotiatedMentoringOne;
    protected $declaredMentoringOne;
    protected $negotiatedMentoringFive;
    //
    protected $activityParticipant;
    protected $activityOne;
    protected $managerInviteeOneA;
    protected $coordinatorInviteeOneB;
    protected $participantInviteeOneC;
    //
    protected $uri;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('User')->truncate();
        //
        $this->connection->table('Manager')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Coordinator')->truncate();
        //
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        //
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('BookedMentoringSlot')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        $this->connection->table('NegotiatedMentoring')->truncate();
        $this->connection->table('DeclaredMentoring')->truncate();
        //
        $this->connection->table('ActivityType')->truncate();
        $this->connection->table('ActivityParticipant')->truncate();
        $this->connection->table('Activity')->truncate();
        $this->connection->table('Invitee')->truncate();
        $this->connection->table('ManagerInvitee')->truncate();
        $this->connection->table('CoordinatorInvitee')->truncate();
        $this->connection->table('ParticipantInvitee')->truncate();
        //
        $firm = $this->personnel->firm;
        $this->programOne = new RecordOfProgram($firm, 1);
        $this->programTwo = new RecordOfProgram($firm, 2);
        //
        $client = new RecordOfClient($firm, '00');
        $team = new RecordOfTeam($firm, $client, '00');
        $user = new RecordOfUser('00');
        //
        $participantOne = new RecordOfParticipant($this->programOne, 1);
        $participantTwo = new RecordOfParticipant($this->programOne, 2);
        $participantThree = new RecordOfParticipant($this->programOne, 3);
        $participant_21 = new RecordOfParticipant($this->programTwo, '21');
        //
        $this->clientParticipantOne = new RecordOfClientParticipant($client, $participantOne);
        $this->teamParticipantTwo = new RecordOfTeamProgramParticipation($team, $participantTwo);
        $this->userParticipantThree = new RecordOfUserParticipant($user, $participantThree);
        $this->teamParticipant_21 = new RecordOfTeamProgramParticipation($team, $participant_21);
        
        //
        $this->coordinatorOne = new RecordOfCoordinator($this->programOne, $this->personnel, 1);
        $this->consultantOne = new RecordOfConsultant($this->programOne, $this->personnel, 1);
        $this->consultantTwo = new RecordOfConsultant($this->programTwo, $this->personnel, 2);
        //
        $this->managerOne = new RecordOfManager($firm, 1);
        //
        $this->consultationSetup = new RecordOfConsultationSetup($this->programOne, null, null, '00');
        $this->consultationSetupTwo = new RecordOfConsultationSetup($this->programTwo, null, null, '02');
        //
        
        $this->mentoringSlotOne = new RecordOfMentoringSlot($this->consultantOne, $this->consultationSetup, 1);
        $mentoringOne = new RecordOfMentoring(1);
        $this->bookedMentoringSlotOneA = new RecordOfBookedMentoringSlot($this->mentoringSlotOne, $mentoringOne, $this->clientParticipantOne->participant);
        $mentoringTwo = new RecordOfMentoring(2);
        $this->bookedMentoringSlotOneB = new RecordOfBookedMentoringSlot($this->mentoringSlotOne, $mentoringTwo, $this->teamParticipantTwo->participant);
        //
        $mentoringThree = new RecordOfMentoring(3);
        $mentoringRequestOne = new RecordOfMentoringRequest($this->clientParticipantOne->participant, $this->consultantOne, $this->consultationSetup, 1);
        $this->negotiatedMentoringOne = new RecordOfNegotiatedMentoring($mentoringRequestOne, $mentoringThree);
        //
        $mentoringFour = new RecordOfMentoring(4);
        $this->declaredMentoringOne = new RecordOfDeclaredMentoring($this->consultantOne, $this->userParticipantThree->participant, $this->consultationSetup, $mentoringFour);
        //
        $mentoringFive = new RecordOfMentoring(5);
        $mentoringRequestFive = new RecordOfMentoringRequest($this->teamParticipant_21->participant, $this->consultantTwo, $this->consultationSetupTwo, 5);
        $this->negotiatedMentoringFive = new RecordOfNegotiatedMentoring($mentoringRequestFive, $mentoringFive);
        //
        $activityTypeOne = new RecordOfActivityType($this->programOne, 1);
        $this->activityParticipant = new RecordOfActivityParticipant($activityTypeOne, null, 1);
        $this->activityOne = new RecordOfActivity($activityTypeOne, 1);
        
        $inviteeOneA = new RecordOfInvitee($this->activityOne, $this->activityParticipant, '1A');
        $this->managerInviteeOneA = new RecordOfManagerInvitee($this->managerOne, $inviteeOneA);
        
        $inviteeOneB = new RecordOfInvitee($this->activityOne, $this->activityParticipant, '1B');
        $inviteeOneB->anInitiator = true;
        $this->coordinatorInviteeOneB = new RecordOfCoordinatorInvitee($this->coordinatorOne, $inviteeOneB);
        
        $inviteeOneC = new RecordOfInvitee($this->activityOne, $this->activityParticipant, '1C');
        $this->participantInviteeOneC = new RecordOfParticipantInvitee($this->teamParticipantTwo->participant, $inviteeOneC);
        //
        $this->uri = $this->personnelUri . "/schedules";
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('User')->truncate();
        //
        $this->connection->table('Manager')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Coordinator')->truncate();
        //
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        //
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('BookedMentoringSlot')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        $this->connection->table('NegotiatedMentoring')->truncate();
        $this->connection->table('DeclaredMentoring')->truncate();
        //
        $this->connection->table('ActivityType')->truncate();
        $this->connection->table('ActivityParticipant')->truncate();
        $this->connection->table('Activity')->truncate();
        $this->connection->table('Invitee')->truncate();
        $this->connection->table('ManagerInvitee')->truncate();
        $this->connection->table('CoordinatorInvitee')->truncate();
        $this->connection->table('ParticipantInvitee')->truncate();
    }
    
    protected function viewAll()
    {
        $this->persistPersonnelDependency();
        //
        $this->programOne->insert($this->connection);
        $this->programTwo->insert($this->connection);
        //
        $this->clientParticipantOne->client->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        $this->teamParticipantTwo->team->insert($this->connection);
        $this->teamParticipantTwo->insert($this->connection);
        $this->userParticipantThree->user->insert($this->connection);
        $this->userParticipantThree->insert($this->connection);
        $this->teamParticipant_21->insert($this->connection);
        //
        $this->managerOne->insert($this->connection);
        //
        $this->coordinatorOne->insert($this->connection);
        $this->consultantOne->insert($this->connection);
        $this->consultantTwo->insert($this->connection);
        //
        $this->consultationSetup->insert($this->connection);
        $this->consultationSetupTwo->insert($this->connection);
        
        $this->mentoringSlotOne->insert($this->connection);
        $this->bookedMentoringSlotOneA->insert($this->connection);
        $this->bookedMentoringSlotOneB->insert($this->connection);
        $this->negotiatedMentoringOne->mentoringRequest->insert($this->connection);
        $this->negotiatedMentoringOne->insert($this->connection);
        $this->declaredMentoringOne->insert($this->connection);
        $this->negotiatedMentoringFive->mentoringRequest->insert($this->connection);
        $this->negotiatedMentoringFive->insert($this->connection);
        //
        $this->activityOne->activityType->insert($this->connection);
        $this->activityOne->insert($this->connection);
        
        $this->activityParticipant->insert($this->connection);
        $this->managerInviteeOneA->insert($this->connection);
        $this->coordinatorInviteeOneB->insert($this->connection);
        $this->participantInviteeOneC->insert($this->connection);
        //
        $this->get($this->uri, $this->personnel->token);
//echo $this->uri;
//$this->seeJsonContains(['print']);
    }
    public function test_viewAll_200()
    {
$this->disableExceptionHandling();
        $from = (new DateTime('-1 weeks'))->format('Y-m-d');
        $to = (new DateTime('+1 weeks'))->format('Y-m-d');
        $this->uri .= "?from=$from&to=$to";
        $this->viewAll();
        $this->seeStatusCode(200);
echo $this->uri;
$this->seeJsonContains(['print']);
        
        $this->seeJsonContains(['mentoringSlotId' => $this->mentoringSlotOne->id]);
        $this->seeJsonContains(['negotiatedMentoringId' => $this->negotiatedMentoringOne->id]);
        $this->seeJsonContains(['negotiatedMentoringId' => $this->negotiatedMentoringFive->id]);
        $this->seeJsonContains(['declaredMentoringId' => $this->declaredMentoringOne->id]);
        $this->seeJsonContains(['coordinatorInviteeId' => $this->coordinatorInviteeOneB->invitee->id]);
    }
    public function test_viewAll_excludeNotOwnSchedule()
    {
        $this->otherPersonnel->insert($this->connection);
        $this->negotiatedMentoringFive->mentoringRequest->mentor->personnel = $this->otherPersonnel;
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['mentoringSlotId' => $this->mentoringSlotOne->id]);
        $this->seeJsonContains(['negotiatedMentoringId' => $this->negotiatedMentoringOne->id]);
        $this->seeJsonDoesntContains(['negotiatedMentoringId' => $this->negotiatedMentoringFive->id]);
        $this->seeJsonContains(['declaredMentoringId' => $this->declaredMentoringOne->id]);
        $this->seeJsonContains(['coordinatorInviteeId' => $this->coordinatorInviteeOneB->invitee->id]);
    }
    public function test_viewAll_timeFilter()
    {
        $this->negotiatedMentoringFive->mentoringRequest->startTime = (new \DateTimeImmutable('+800 hours'))->format('Y-m-d H:i:s');
        $this->negotiatedMentoringFive->mentoringRequest->endTime = (new \DateTimeImmutable('+802 hours'))->format('Y-m-d H:i:s');
        
        $this->mentoringSlotOne->startTime = (new \DateTimeImmutable('+850 hours'));
        $this->mentoringSlotOne->endTime = (new \DateTimeImmutable('+855 hours'));
        
        $this->declaredMentoringOne->startTime = (new \DateTimeImmutable('+880 hours'))->format('Y-m-d H:i:s');
        $this->declaredMentoringOne->endTime = (new \DateTimeImmutable('+883 hours'))->format('Y-m-d H:i:s');
        
        $this->coordinatorInviteeOneB->invitee->activity->startDateTime = (new \DateTimeImmutable('+900 hour'))->format('Y-m-d H:i:s');
        $this->coordinatorInviteeOneB->invitee->activity->endDateTime = (new \DateTimeImmutable('+910 hour'))->format('Y-m-d H:i:s');
        
        $from = (new DateTime('+700 hours'))->format('Y-m-d');
        $to = (new DateTime('+1100 hours'))->format('Y-m-d');
        $this->uri .= "?from=$from&to=$to";
        $this->viewAll();
        $this->seeStatusCode(200);
        
        
        $this->seeJsonContains(['mentoringSlotId' => $this->mentoringSlotOne->id]);
        $this->seeJsonDoesntContains(['negotiatedMentoringId' => $this->negotiatedMentoringOne->id]);
        $this->seeJsonContains(['negotiatedMentoringId' => $this->negotiatedMentoringFive->id]);
        $this->seeJsonContains(['declaredMentoringId' => $this->declaredMentoringOne->id]);
        $this->seeJsonContains(['coordinatorInviteeId' => $this->coordinatorInviteeOneB->invitee->id]);
    }

}
