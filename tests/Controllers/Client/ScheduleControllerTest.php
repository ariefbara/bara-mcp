<?php

namespace Tests\Controllers\Client;

use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Activity\RecordOfInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\ActivityType\RecordOfActivityParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\MentoringSlot\RecordOfBookedMentoringSlot;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfMentoringSlot;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MentoringRequest\RecordOfNegotiatedMentoring;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMentoringRequest;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfParticipantInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfActivity;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfActivityType;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfMember;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\Shared\RecordOfMentoring;

class ScheduleControllerTest extends ClientTestCase
{
    protected $showAllUri;
    protected $programOne;
    protected $programTwo;
    
    protected $clientTwo;
    protected $clientParticipantOne_p1c1;
    protected $clientParticipantTwo_p1c2;
    
    protected $teamOne;
    protected $teamMemberOne;
    protected $teamParticipantOne_p2t1;

    protected $personnelOne;

    protected $mentorOne_prog1pers1;
    protected $mentorTwo_prog2Pers2;
    
    protected $negotiatedMentoringOne_m1cp1;
    protected $negotiatedMentoringTwo_m1cp2;
    protected $negotiatedMentoringThree_m2tp1;
    
    protected $mentoringSlotOne_m1;
    protected $mentoringSlotTwo_m2;
    
    protected $bookedMentoringSlotOne_ms1cp1;
    protected $bookedMentoringSlotTwo_ms1cp2;
    protected $bookedMentoringSlotThree_ms2tp1;
    
    protected $participantInviteeOne_cp1;
    protected $participantInviteeTwo_cp2;
    protected $participantInviteeThree_tp1;

    protected function setUp(): void
    {
        parent::setUp();
        $this->showAllUri = $this->clientUri . '/schedules';
        
        $this->connection->table('Client')->truncate();
        
        $this->connection->table('Program')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('NegotiatedMentoring')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('BookedMentoringSlot')->truncate();
        $this->connection->table('ActivityType')->truncate();
        $this->connection->table('Activity')->truncate();
        $this->connection->table('ActivityParticipant')->truncate();
        $this->connection->table('Invitee')->truncate();
        $this->connection->table('ParticipantInvitee')->truncate();

        $firm = $this->client->firm;
        
        $this->clientTwo = new RecordOfClient($firm, '2');
        
        $this->programOne = new RecordOfProgram($firm, '1');
        $this->programTwo = new RecordOfProgram($firm, '2');
        
        
        $participantOne = new RecordOfParticipant($this->programOne, '1');
        $participantTwo = new RecordOfParticipant($this->programOne, '2');
        $participantThree = new RecordOfParticipant($this->programTwo, '3');
        
        $this->clientParticipantOne_p1c1 = new RecordOfClientParticipant($this->client, $participantOne);
        $this->clientParticipantTwo_p1c2 = new RecordOfClientParticipant($this->clientTwo, $participantTwo);
        
        $this->teamOne = new RecordOfTeam($firm, $this->client, '1');
        
        $this->teamMemberOne = new RecordOfMember($this->teamOne, $this->client, '1');
        
        $this->teamParticipantOne_p2t1 = new RecordOfTeamProgramParticipation($this->teamOne, $participantThree);
        
        $this->personnelOne = new RecordOfPersonnel($firm, '1');
        
        $this->mentorOne_prog1pers1 = new RecordOfConsultant($this->programOne, $this->personnelOne, '1');
        $this->mentorTwo_prog2Pers2 = new RecordOfConsultant($this->programTwo, $this->personnelOne, '2');
        
        $consultationSetupOne = new RecordOfConsultationSetup($this->programOne, null, null, '1');
        $consultationSetupTwo = new RecordOfConsultationSetup($this->programTwo, null, null, '2');
        
        $mentoringRequestOne = new RecordOfMentoringRequest($this->clientParticipantOne_p1c1->participant, $this->mentorOne_prog1pers1, $consultationSetupOne, '1');
        $mentoringRequestTwo = new RecordOfMentoringRequest($this->clientParticipantTwo_p1c2->participant, $this->mentorOne_prog1pers1, $consultationSetupOne, '2');
        $mentoringRequestThree = new RecordOfMentoringRequest($this->teamParticipantOne_p2t1->participant, $this->mentorTwo_prog2Pers2, $consultationSetupTwo, '3');
        
        $mentoringOne = new RecordOfMentoring(1);
        $mentoringTwo = new RecordOfMentoring(2);
        $mentoringThree = new RecordOfMentoring(3);
        $mentoringFour = new RecordOfMentoring(4);
        $mentoringFive = new RecordOfMentoring(5);
        $mentoringSix = new RecordOfMentoring(6);
        
        $this->negotiatedMentoringOne_m1cp1 = new RecordOfNegotiatedMentoring($mentoringRequestOne, $mentoringOne);
        $this->negotiatedMentoringTwo_m1cp2 = new RecordOfNegotiatedMentoring($mentoringRequestTwo, $mentoringTwo);
        $this->negotiatedMentoringThree_m2tp1 = new RecordOfNegotiatedMentoring($mentoringRequestThree, $mentoringThree);
        
        $this->mentoringSlotOne_m1 = new RecordOfMentoringSlot($this->mentorOne_prog1pers1, $consultationSetupOne, '1');
        $this->mentoringSlotTwo_m2 = new RecordOfMentoringSlot($this->mentorTwo_prog2Pers2, $consultationSetupTwo, '2');
        
        $this->bookedMentoringSlotOne_ms1cp1 = new RecordOfBookedMentoringSlot($this->mentoringSlotOne_m1, $mentoringFour, $this->clientParticipantOne_p1c1->participant);
        $this->bookedMentoringSlotTwo_ms1cp2 = new RecordOfBookedMentoringSlot($this->mentoringSlotOne_m1, $mentoringFive, $this->clientParticipantTwo_p1c2->participant);
        $this->bookedMentoringSlotThree_ms2tp1 = new RecordOfBookedMentoringSlot($this->mentoringSlotTwo_m2, $mentoringSix, $this->teamParticipantOne_p2t1->participant);
        
        $activityTypeOne = new RecordOfActivityType($this->programOne, 1);
        $activityTypeTwo = new RecordOfActivityType($this->programTwo, 2);
        
        $activityOne = new RecordOfActivity($activityTypeOne, '1');
        $activityTwo = new RecordOfActivity($activityTypeTwo, '2');
        
        $activityParticipantOne = new RecordOfActivityParticipant($activityTypeOne, null, 1);
        $activityParticipantTwo = new RecordOfActivityParticipant($activityTypeTwo, null, 2);
        
        $inviteeOne = new RecordOfInvitee($activityOne, $activityParticipantOne, '1');
        $inviteeTwo = new RecordOfInvitee($activityOne, $activityParticipantOne, '2');
        $inviteeThree = new RecordOfInvitee($activityTwo, $activityParticipantTwo, '3');
        
        $this->participantInviteeOne_cp1 = new RecordOfParticipantInvitee($this->clientParticipantOne_p1c1->participant, $inviteeOne);
        $this->participantInviteeTwo_cp2 = new RecordOfParticipantInvitee($this->clientParticipantTwo_p1c2->participant, $inviteeTwo);
        $this->participantInviteeThree_tp1 = new RecordOfParticipantInvitee($this->teamParticipantOne_p2t1->participant, $inviteeThree);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('NegotiatedMentoring')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('BookedMentoringSlot')->truncate();
        $this->connection->table('ActivityType')->truncate();
        $this->connection->table('Activity')->truncate();
        $this->connection->table('ActivityParticipant')->truncate();
        $this->connection->table('Invitee')->truncate();
        $this->connection->table('ParticipantInvitee')->truncate();
    }
    
    protected function showAll()
    {
        $this->client->insert($this->connection);
        $this->clientTwo->insert($this->connection);
        $this->personnelOne->insert($this->connection);
        
        $this->negotiatedMentoringOne_m1cp1->mentoringRequest->consultationSetup->program->insert($this->connection);
        $this->negotiatedMentoringThree_m2tp1->mentoringRequest->consultationSetup->program->insert($this->connection);
        
        $this->negotiatedMentoringOne_m1cp1->mentoringRequest->consultationSetup->insert($this->connection);
        $this->negotiatedMentoringThree_m2tp1->mentoringRequest->consultationSetup->insert($this->connection);
        
        $this->negotiatedMentoringOne_m1cp1->mentoringRequest->mentor->insert($this->connection);
        $this->negotiatedMentoringThree_m2tp1->mentoringRequest->mentor->insert($this->connection);
        
        $this->clientParticipantOne_p1c1->insert($this->connection);
        $this->clientParticipantTwo_p1c2->insert($this->connection);
        
        $this->teamParticipantOne_p2t1->team->insert($this->connection);
        $this->teamMemberOne->insert($this->connection);
        $this->teamParticipantOne_p2t1->insert($this->connection);
        
        $this->negotiatedMentoringOne_m1cp1->mentoringRequest->insert($this->connection);
        $this->negotiatedMentoringTwo_m1cp2->mentoringRequest->insert($this->connection);
        $this->negotiatedMentoringThree_m2tp1->mentoringRequest->insert($this->connection);
        
        $this->negotiatedMentoringOne_m1cp1->insert($this->connection);
        $this->negotiatedMentoringTwo_m1cp2->insert($this->connection);
        $this->negotiatedMentoringThree_m2tp1->insert($this->connection);
        
        $this->bookedMentoringSlotOne_ms1cp1->mentoringSlot->insert($this->connection);
        $this->bookedMentoringSlotThree_ms2tp1->mentoringSlot->insert($this->connection);
        
        $this->bookedMentoringSlotOne_ms1cp1->insert($this->connection);
        $this->bookedMentoringSlotTwo_ms1cp2->insert($this->connection);
        $this->bookedMentoringSlotThree_ms2tp1->insert($this->connection);
        
        $this->participantInviteeOne_cp1->invitee->activity->activityType->insert($this->connection);
        $this->participantInviteeThree_tp1->invitee->activity->activityType->insert($this->connection);
        
        $this->participantInviteeOne_cp1->invitee->activity->insert($this->connection);
        $this->participantInviteeThree_tp1->invitee->activity->insert($this->connection);
        
        $this->participantInviteeOne_cp1->invitee->activityParticipant->insert($this->connection);
        $this->participantInviteeThree_tp1->invitee->activityParticipant->insert($this->connection);
        
        $this->participantInviteeOne_cp1->insert($this->connection);
        $this->participantInviteeTwo_cp2->insert($this->connection);
        $this->participantInviteeThree_tp1->insert($this->connection);
        
        $this->get($this->showAllUri, $this->client->token);
    }
    public function test_showAll_200()
    {
$this->disableExceptionHandling();
        $this->showAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 6,
            'list' => [
                [
                    'startTime' => $this->negotiatedMentoringOne_m1cp1->mentoringRequest->startTime,
                    'teamId' => null,
                    'programId' => $this->negotiatedMentoringOne_m1cp1->mentoringRequest->participant->program->id,
                    'programType' => $this->negotiatedMentoringOne_m1cp1->mentoringRequest->participant->program->programType,
                    'participantId' => $this->negotiatedMentoringOne_m1cp1->mentoringRequest->participant->id,
                    'bookedMentoringSlotId' => null,
                    'negotiatedMentoringId' => $this->negotiatedMentoringOne_m1cp1->id,
                    'mentorName' => $this->negotiatedMentoringOne_m1cp1->mentoringRequest->mentor->personnel->getFullName(),
                    'invitationId' => null,
                    'agendaType' => null,
                    'agendaName' => null,
                ],
                [
                    'startTime' => $this->negotiatedMentoringThree_m2tp1->mentoringRequest->startTime,
                    'teamId' => $this->teamOne->id,
                    'programId' => $this->negotiatedMentoringThree_m2tp1->mentoringRequest->participant->program->id,
                    'programType' => $this->negotiatedMentoringThree_m2tp1->mentoringRequest->participant->program->programType,
                    'participantId' => $this->negotiatedMentoringThree_m2tp1->mentoringRequest->participant->id,
                    'bookedMentoringSlotId' => null,
                    'negotiatedMentoringId' => $this->negotiatedMentoringThree_m2tp1->id,
                    'mentorName' => $this->negotiatedMentoringThree_m2tp1->mentoringRequest->mentor->personnel->getFullName(),
                    'invitationId' => null,
                    'agendaType' => null,
                    'agendaName' => null,
                ],
                [
                    'startTime' => $this->bookedMentoringSlotOne_ms1cp1->mentoringSlot->startTime->format('Y-m-d H:i:s'),
                    'teamId' => null,
                    'programId' => $this->bookedMentoringSlotOne_ms1cp1->participant->program->id,
                    'programType' => $this->bookedMentoringSlotOne_ms1cp1->participant->program->programType,
                    'participantId' => $this->bookedMentoringSlotOne_ms1cp1->participant->id,
                    'bookedMentoringSlotId' => $this->bookedMentoringSlotOne_ms1cp1->id,
                    'negotiatedMentoringId' => null,
                    'mentorName' => $this->bookedMentoringSlotOne_ms1cp1->mentoringSlot->consultant->personnel->getFullName(),
                    'invitationId' => null,
                    'agendaType' => null,
                    'agendaName' => null,
                ],
                [
                    'startTime' => $this->bookedMentoringSlotThree_ms2tp1->mentoringSlot->startTime->format('Y-m-d H:i:s'),
                    'teamId' => $this->teamOne->id,
                    'programId' => $this->bookedMentoringSlotThree_ms2tp1->participant->program->id,
                    'programType' => $this->bookedMentoringSlotThree_ms2tp1->participant->program->programType,
                    'participantId' => $this->bookedMentoringSlotThree_ms2tp1->participant->id,
                    'bookedMentoringSlotId' => $this->bookedMentoringSlotThree_ms2tp1->id,
                    'negotiatedMentoringId' => null,
                    'mentorName' => $this->bookedMentoringSlotThree_ms2tp1->mentoringSlot->consultant->personnel->getFullName(),
                    'invitationId' => null,
                    'agendaType' => null,
                    'agendaName' => null,
                ],
                [
                    'startTime' => $this->participantInviteeOne_cp1->invitee->activity->startDateTime,
                    'teamId' => null,
                    'programId' => $this->participantInviteeOne_cp1->participant->program->id,
                    'programType' => $this->participantInviteeOne_cp1->participant->program->programType,
                    'participantId' => $this->participantInviteeOne_cp1->participant->id,
                    'bookedMentoringSlotId' => null,
                    'negotiatedMentoringId' => null,
                    'mentorName' => null,
                    'invitationId' => $this->participantInviteeOne_cp1->invitee->id,
                    'agendaType' => $this->participantInviteeOne_cp1->invitee->activity->activityType->name,
                    'agendaName' => $this->participantInviteeOne_cp1->invitee->activity->name,
                ],
                [
                    'startTime' => $this->participantInviteeThree_tp1->invitee->activity->startDateTime,
                    'teamId' => $this->teamOne->id,
                    'programId' => $this->participantInviteeThree_tp1->participant->program->id,
                    'programType' => $this->participantInviteeThree_tp1->participant->program->programType,
                    'participantId' => $this->participantInviteeThree_tp1->participant->id,
                    'bookedMentoringSlotId' => null,
                    'negotiatedMentoringId' => null,
                    'mentorName' => null,
                    'invitationId' => $this->participantInviteeThree_tp1->invitee->id,
                    'agendaType' => $this->participantInviteeThree_tp1->invitee->activity->activityType->name,
                    'agendaName' => $this->participantInviteeThree_tp1->invitee->activity->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_paginationApply()
    {
        $this->negotiatedMentoringOne_m1cp1->mentoringRequest->startTime = (new \DateTimeImmutable('+1 hours'))->format('Y-m-d H:i:s');
        $this->negotiatedMentoringThree_m2tp1->mentoringRequest->startTime = (new \DateTimeImmutable('+25 hours'))->format('Y-m-d H:i:s');
        $this->bookedMentoringSlotOne_ms1cp1->mentoringSlot->startTime = (new \DateTimeImmutable('+27 hours'));
        $this->bookedMentoringSlotThree_ms2tp1->mentoringSlot->startTime = (new \DateTimeImmutable('+5 hours'));
        $this->participantInviteeOne_cp1->invitee->activity->startDateTime = (new \DateTimeImmutable('+47 hours'))->format('Y-m-d H:i:s');
        $this->participantInviteeThree_tp1->invitee->activity->startDateTime = (new \DateTimeImmutable('+49 hours'))->format('Y-m-d H:i:s');
        
        $this->showAllUri .= "?pageSize=2&page=1";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 6];
        $this->seeJsonContains($totalResponse);
        
        $negotiatedMentoringOneResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringOne_m1cp1->id];
        $this->seeJsonContains($negotiatedMentoringOneResponse);
        
        $negotiatedMentoringThreeResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringThree_m2tp1->id];
        $this->seeJsonDoesntContains($negotiatedMentoringThreeResponse);
        
        $bookedMentoringSlotOneResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotOne_ms1cp1->id];
        $this->seeJsonDoesntContains($bookedMentoringSlotOneResponse);
        
        $bookedMentoringSlotThreeResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotThree_ms2tp1->id];
        $this->seeJsonContains($bookedMentoringSlotThreeResponse);
        
        $participantInviteeOneResponse = ['invitationId' => $this->participantInviteeOne_cp1->invitee->id];
        $this->seeJsonDoesntContains($participantInviteeOneResponse);
        
        $participantInviteeThreeResponse = ['invitationId' => $this->participantInviteeThree_tp1->invitee->id];
        $this->seeJsonDoesntContains($participantInviteeThreeResponse);
    }
    public function test_showAll_fromFilterApply()
    {
        $this->negotiatedMentoringOne_m1cp1->mentoringRequest->startTime = (new \DateTimeImmutable('+1 hours'))->format('Y-m-d H:i:s');
        $this->negotiatedMentoringThree_m2tp1->mentoringRequest->startTime = (new \DateTimeImmutable('+25 hours'))->format('Y-m-d H:i:s');
        $this->bookedMentoringSlotOne_ms1cp1->mentoringSlot->startTime = (new \DateTimeImmutable('+27 hours'));
        $this->bookedMentoringSlotThree_ms2tp1->mentoringSlot->startTime = (new \DateTimeImmutable('+5 hours'));
        $this->participantInviteeOne_cp1->invitee->activity->startDateTime = (new \DateTimeImmutable('+47 hours'))->format('Y-m-d H:i:s');
        $this->participantInviteeThree_tp1->invitee->activity->startDateTime = (new \DateTimeImmutable('+49 hours'))->format('Y-m-d H:i:s');
        
        $fromFilter = (new \DateTimeImmutable('+27 hours'))->format('Y-m-d H:i:s');
        $this->showAllUri .= "?from=$fromFilter";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 3];
        $this->seeJsonContains($totalResponse);
        
        $negotiatedMentoringOneResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringOne_m1cp1->id];
        $this->seeJsonDoesntContains($negotiatedMentoringOneResponse);
        
        $negotiatedMentoringThreeResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringThree_m2tp1->id];
        $this->seeJsonDoesntContains($negotiatedMentoringThreeResponse);
        
        $bookedMentoringSlotOneResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotOne_ms1cp1->id];
        $this->seeJsonContains($bookedMentoringSlotOneResponse);
        
        $bookedMentoringSlotThreeResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotThree_ms2tp1->id];
        $this->seeJsonDoesntContains($bookedMentoringSlotThreeResponse);
        
        $participantInviteeOneResponse = ['invitationId' => $this->participantInviteeOne_cp1->invitee->id];
        $this->seeJsonContains($participantInviteeOneResponse);
        
        $participantInviteeThreeResponse = ['invitationId' => $this->participantInviteeThree_tp1->invitee->id];
        $this->seeJsonContains($participantInviteeThreeResponse);
    }
    public function test_showAll_toFilterApply()
    {
        $this->negotiatedMentoringOne_m1cp1->mentoringRequest->startTime = (new \DateTimeImmutable('+1 hours'))->format('Y-m-d H:i:s');
        $this->negotiatedMentoringThree_m2tp1->mentoringRequest->startTime = (new \DateTimeImmutable('+25 hours'))->format('Y-m-d H:i:s');
        $this->bookedMentoringSlotOne_ms1cp1->mentoringSlot->startTime = (new \DateTimeImmutable('+27 hours'));
        $this->bookedMentoringSlotThree_ms2tp1->mentoringSlot->startTime = (new \DateTimeImmutable('+5 hours'));
        $this->participantInviteeOne_cp1->invitee->activity->startDateTime = (new \DateTimeImmutable('+47 hours'))->format('Y-m-d H:i:s');
        $this->participantInviteeThree_tp1->invitee->activity->startDateTime = (new \DateTimeImmutable('+49 hours'))->format('Y-m-d H:i:s');
        
        $toFilter = (new \DateTimeImmutable('+26 hours'))->format('Y-m-d H:i:s');
        $this->showAllUri .= "?to=$toFilter";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 3];
        $this->seeJsonContains($totalResponse);
        
        $negotiatedMentoringOneResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringOne_m1cp1->id];
        $this->seeJsonContains($negotiatedMentoringOneResponse);
        
        $negotiatedMentoringThreeResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringThree_m2tp1->id];
        $this->seeJsonContains($negotiatedMentoringThreeResponse);
        
        $bookedMentoringSlotOneResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotOne_ms1cp1->id];
        $this->seeJsonDoesntContains($bookedMentoringSlotOneResponse);
        
        $bookedMentoringSlotThreeResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotThree_ms2tp1->id];
        $this->seeJsonContains($bookedMentoringSlotThreeResponse);
        
        $participantInviteeOneResponse = ['invitationId' => $this->participantInviteeOne_cp1->invitee->id];
        $this->seeJsonDoesntContains($participantInviteeOneResponse);
        
        $participantInviteeThreeResponse = ['invitationId' => $this->participantInviteeThree_tp1->invitee->id];
        $this->seeJsonDoesntContains($participantInviteeThreeResponse);
    }
    public function test_showAll_orderApply()
    {
        $this->negotiatedMentoringOne_m1cp1->mentoringRequest->startTime = (new \DateTimeImmutable('+1 hours'))->format('Y-m-d H:i:s');
        $this->negotiatedMentoringThree_m2tp1->mentoringRequest->startTime = (new \DateTimeImmutable('+25 hours'))->format('Y-m-d H:i:s');
        $this->bookedMentoringSlotOne_ms1cp1->mentoringSlot->startTime = (new \DateTimeImmutable('+27 hours'));
        $this->bookedMentoringSlotThree_ms2tp1->mentoringSlot->startTime = (new \DateTimeImmutable('+5 hours'));
        $this->participantInviteeOne_cp1->invitee->activity->startDateTime = (new \DateTimeImmutable('+47 hours'))->format('Y-m-d H:i:s');
        $this->participantInviteeThree_tp1->invitee->activity->startDateTime = (new \DateTimeImmutable('+49 hours'))->format('Y-m-d H:i:s');
        
        $toFilter = (new \DateTimeImmutable('+26 hours'))->format('Y-m-d H:i:s');
        $this->showAllUri .= "?to=$toFilter&order=DESC&pageSize=2";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 3];
        $this->seeJsonContains($totalResponse);
        
        $negotiatedMentoringOneResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringOne_m1cp1->id];
        $this->seeJsonDoesntContains($negotiatedMentoringOneResponse);
        
        $negotiatedMentoringThreeResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringThree_m2tp1->id];
        $this->seeJsonContains($negotiatedMentoringThreeResponse);
        
        $bookedMentoringSlotOneResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotOne_ms1cp1->id];
        $this->seeJsonDoesntContains($bookedMentoringSlotOneResponse);
        
        $bookedMentoringSlotThreeResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotThree_ms2tp1->id];
        $this->seeJsonContains($bookedMentoringSlotThreeResponse);
        
        $participantInviteeOneResponse = ['invitationId' => $this->participantInviteeOne_cp1->invitee->id];
        $this->seeJsonDoesntContains($participantInviteeOneResponse);
        
        $participantInviteeThreeResponse = ['invitationId' => $this->participantInviteeThree_tp1->invitee->id];
        $this->seeJsonDoesntContains($participantInviteeThreeResponse);
    }
    public function test_showAll_hasInactiveParticipant_excludeInactiveParticipant()
    {
        $this->clientParticipantOne_p1c1->participant->active = false;
        
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 3];
        $this->seeJsonContains($totalResponse);
        
        $negotiatedMentoringOneResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringOne_m1cp1->id];
        $this->seeJsonDoesntContains($negotiatedMentoringOneResponse);
        
        $negotiatedMentoringThreeResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringThree_m2tp1->id];
        $this->seeJsonContains($negotiatedMentoringThreeResponse);
        
        $bookedMentoringSlotOneResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotOne_ms1cp1->id];
        $this->seeJsonDoesntContains($bookedMentoringSlotOneResponse);
        
        $bookedMentoringSlotThreeResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotThree_ms2tp1->id];
        $this->seeJsonContains($bookedMentoringSlotThreeResponse);
        
        $participantInviteeOneResponse = ['invitationId' => $this->participantInviteeOne_cp1->invitee->id];
        $this->seeJsonDoesntContains($participantInviteeOneResponse);
        
        $participantInviteeThreeResponse = ['invitationId' => $this->participantInviteeThree_tp1->invitee->id];
        $this->seeJsonContains($participantInviteeThreeResponse);
    }
    public function test_showAll_hasInactiveMemberOfTeamParticipant_excludeResultFromThoseTeam()
    {
        $this->teamMemberOne->active = false;
        
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 3];
        $this->seeJsonContains($totalResponse);
        
        $negotiatedMentoringOneResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringOne_m1cp1->id];
        $this->seeJsonContains($negotiatedMentoringOneResponse);
        
        $negotiatedMentoringThreeResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringThree_m2tp1->id];
        $this->seeJsonDoesntContains($negotiatedMentoringThreeResponse);
        
        $bookedMentoringSlotOneResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotOne_ms1cp1->id];
        $this->seeJsonContains($bookedMentoringSlotOneResponse);
        
        $bookedMentoringSlotThreeResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotThree_ms2tp1->id];
        $this->seeJsonDoesntContains($bookedMentoringSlotThreeResponse);
        
        $participantInviteeOneResponse = ['invitationId' => $this->participantInviteeOne_cp1->invitee->id];
        $this->seeJsonContains($participantInviteeOneResponse);
        
        $participantInviteeThreeResponse = ['invitationId' => $this->participantInviteeThree_tp1->invitee->id];
        $this->seeJsonDoesntContains($participantInviteeThreeResponse);
    }
    public function test_showAll_containDisabledBookedMentoringSlot_excludeDisabledReservationSlot()
    {
        $this->bookedMentoringSlotOne_ms1cp1->cancelled = true;
        
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 5];
        $this->seeJsonContains($totalResponse);
        
        $negotiatedMentoringOneResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringOne_m1cp1->id];
        $this->seeJsonContains($negotiatedMentoringOneResponse);
        
        $negotiatedMentoringThreeResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringThree_m2tp1->id];
        $this->seeJsonContains($negotiatedMentoringThreeResponse);
        
        $bookedMentoringSlotOneResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotOne_ms1cp1->id];
        $this->seeJsonDoesntContains($bookedMentoringSlotOneResponse);
        
        $bookedMentoringSlotThreeResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotThree_ms2tp1->id];
        $this->seeJsonContains($bookedMentoringSlotThreeResponse);
        
        $participantInviteeOneResponse = ['invitationId' => $this->participantInviteeOne_cp1->invitee->id];
        $this->seeJsonContains($participantInviteeOneResponse);
        
        $participantInviteeThreeResponse = ['invitationId' => $this->participantInviteeThree_tp1->invitee->id];
        $this->seeJsonContains($participantInviteeThreeResponse);
    }
    public function test_showAll_containCancelledInvitation_excludeCancelledInvitation()
    {
        $this->participantInviteeThree_tp1->invitee->cancelled = true;
        
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 5];
        $this->seeJsonContains($totalResponse);
        
        $negotiatedMentoringOneResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringOne_m1cp1->id];
        $this->seeJsonContains($negotiatedMentoringOneResponse);
        
        $negotiatedMentoringThreeResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringThree_m2tp1->id];
        $this->seeJsonContains($negotiatedMentoringThreeResponse);
        
        $bookedMentoringSlotOneResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotOne_ms1cp1->id];
        $this->seeJsonContains($bookedMentoringSlotOneResponse);
        
        $bookedMentoringSlotThreeResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotThree_ms2tp1->id];
        $this->seeJsonContains($bookedMentoringSlotThreeResponse);
        
        $participantInviteeOneResponse = ['invitationId' => $this->participantInviteeOne_cp1->invitee->id];
        $this->seeJsonContains($participantInviteeOneResponse);
        
        $participantInviteeThreeResponse = ['invitationId' => $this->participantInviteeThree_tp1->invitee->id];
        $this->seeJsonDoesntContains($participantInviteeThreeResponse);
    }

}
