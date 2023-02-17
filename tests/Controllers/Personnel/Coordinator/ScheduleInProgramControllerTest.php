<?php

namespace Tests\Controllers\Personnel\Coordinator;

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
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\RecordOfUser;
use Tests\Controllers\RecordPreparation\Shared\RecordOfMentoring;
use Tests\Controllers\RecordPreparation\User\RecordOfUserParticipant;

class ScheduleInProgramControllerTest extends ExtendedCoordinatorTestCase
{

    protected $clientParticipantOne;
    protected $teamParticipantTwo;
    protected $userParticipantThree;
    //
    protected $coordinatorOne;
    protected $consultantOne;
    protected $managerOne;
    
    //
    protected $consultationSetup;
    protected $mentoringSlotOne;
    protected $bookedMentoringSlotOneA;
    protected $bookedMentoringSlotOneB;
    protected $negotiatedMentoringOne;
    protected $declaredMentoringOne;
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
        $this->connection->table('Manager')->truncate();
        //
        $this->connection->table('Consultant')->truncate();
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
        $program = $this->coordinator->program;
        $firm = $program->firm;
        //
        $client = new RecordOfClient($firm, '00');
        $team = new RecordOfTeam($firm, $client, '00');
        $user = new RecordOfUser('00');
        //
        $participantOne = new RecordOfParticipant($program, 1);
        $participantTwo = new RecordOfParticipant($program, 2);
        $participantThree = new RecordOfParticipant($program, 3);
        //
        $this->clientParticipantOne = new RecordOfClientParticipant($client, $participantOne);
        $this->teamParticipantTwo = new RecordOfTeamProgramParticipation($team, $participantTwo);
        $this->userParticipantThree = new RecordOfUserParticipant($user, $participantThree);
        //
        $personnelOne = new RecordOfPersonnel($firm, 1);
        $personnelTwo = new RecordOfPersonnel($firm, 2);
        //
        $this->coordinatorOne = new RecordOfCoordinator($program, $personnelOne, 1);
        $this->consultantOne = new RecordOfConsultant($program, $personnelTwo, 1);
        //
        $this->managerOne = new RecordOfManager($firm, 1);
        //
        $this->consultationSetup = new RecordOfConsultationSetup($program, null, null, '00');
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
        $activityTypeOne = new RecordOfActivityType($program, 1);
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
        $this->uri = $this->coordinatorUri . "/schedules";
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('Manager')->truncate();
        //
        $this->connection->table('Consultant')->truncate();
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
        $this->persistCoordinatorDependency();
        //
        $this->clientParticipantOne->client->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        $this->teamParticipantTwo->team->insert($this->connection);
        $this->teamParticipantTwo->insert($this->connection);
        $this->userParticipantThree->user->insert($this->connection);
        $this->userParticipantThree->insert($this->connection);
        //
        $this->managerOne->insert($this->connection);
        //
        $this->coordinatorOne->personnel->insert($this->connection);
        $this->coordinatorOne->insert($this->connection);
        $this->consultantOne->personnel->insert($this->connection);
        $this->consultantOne->insert($this->connection);
        //
        $this->consultationSetup->insert($this->connection);
        $this->mentoringSlotOne->insert($this->connection);
        $this->bookedMentoringSlotOneA->insert($this->connection);
        $this->bookedMentoringSlotOneB->insert($this->connection);
        $this->negotiatedMentoringOne->mentoringRequest->insert($this->connection);
        $this->negotiatedMentoringOne->insert($this->connection);
        $this->declaredMentoringOne->insert($this->connection);
        //
        $this->activityOne->activityType->insert($this->connection);
        $this->activityOne->insert($this->connection);
        
        $this->activityParticipant->insert($this->connection);
        $this->managerInviteeOneA->insert($this->connection);
        $this->coordinatorInviteeOneB->insert($this->connection);
        $this->participantInviteeOneC->insert($this->connection);
        //
        $this->get($this->uri, $this->coordinator->personnel->token);
echo $this->uri;
$this->seeJsonContains(['print']);
    }
    public function test_viewAll_200()
    {
$this->disableExceptionHandling();
        $from = (new \DateTime('-1 weeks'))->format('Y-m-d');
        $to = (new \DateTime('+1 weeks'))->format('Y-m-d');
        $this->uri .= "?from=$from&to=$to";
        $this->viewAll();
        $this->seeStatusCode(200);
    }

}
