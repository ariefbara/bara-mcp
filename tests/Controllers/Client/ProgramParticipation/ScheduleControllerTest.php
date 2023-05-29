<?php

namespace Tests\Controllers\Client\ProgramParticipation;

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
use Tests\Controllers\RecordPreparation\Shared\RecordOfMentoring;

class ScheduleControllerTest extends ExtendedClientParticipantTestCase
{
    protected $showAllUri;

    protected $personnelOne;
    protected $personnelTwo;

    protected $mentorOne_p1;
    protected $mentorTwo_p2;
    
    protected $negotiatedMentoringOne_m1;
    protected $negotiatedMentoringTwo_m2;
    
    protected $mentoringSlotOne_m1;
    protected $mentoringSlotTwo_m2;
    
    protected $bookedMentoringSlotOne_m1;
    protected $bookedMentoringSlotTwo_m2;
    
    protected $participantInviteeOne;
    protected $participantInviteeTwo;
    
    protected $otherClientParticipant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->showAllUri = $this->clientParticipantUri . "/schedules";
        
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('NegotiatedMentoring')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('BookedMentoringSlot')->truncate();
        $this->connection->table('ActivityType')->truncate();
        $this->connection->table('ActivityParticipant')->truncate();
        $this->connection->table('Activity')->truncate();
        $this->connection->table('Invitee')->truncate();
        $this->connection->table('ParticipantInvitee')->truncate();
        
        $participant = $this->clientParticipant->participant;
        $program = $participant->program;
        $firm = $program->firm;
        
        $this->personnelOne = new RecordOfPersonnel($firm, '1');
        $this->personnelTwo = new RecordOfPersonnel($firm, '2');
        
        $this->mentorOne_p1 = new RecordOfConsultant($program, $this->personnelOne, '1');
        $this->mentorTwo_p2 = new RecordOfConsultant($program, $this->personnelTwo, '2');
        
        $consultationSetupOne = new RecordOfConsultationSetup($program, null, null, '1');
        
        $mentoringRequestOne = new RecordOfMentoringRequest($participant, $this->mentorOne_p1, $consultationSetupOne, '1');
        $mentoringRequestTwo = new RecordOfMentoringRequest($participant, $this->mentorTwo_p2, $consultationSetupOne, '2');
        
        $mentoringOne = new RecordOfMentoring('1');
        $mentoringTwo = new RecordOfMentoring('2');
        $mentoringThree = new RecordOfMentoring('3');
        $mentoringFour = new RecordOfMentoring('4');
        
        $this->negotiatedMentoringOne_m1 = new RecordOfNegotiatedMentoring($mentoringRequestOne, $mentoringOne);
        $this->negotiatedMentoringTwo_m2 = new RecordOfNegotiatedMentoring($mentoringRequestTwo, $mentoringTwo);
        
        $this->mentoringSlotOne_m1 = new RecordOfMentoringSlot($this->mentorOne_p1, $consultationSetupOne, '1');
        $this->mentoringSlotTwo_m2 = new RecordOfMentoringSlot($this->mentorTwo_p2, $consultationSetupOne, '2');
        
        $this->bookedMentoringSlotOne_m1 = new RecordOfBookedMentoringSlot($this->mentoringSlotOne_m1, $mentoringThree, $participant);
        $this->bookedMentoringSlotTwo_m2 = new RecordOfBookedMentoringSlot($this->mentoringSlotTwo_m2, $mentoringFour, $participant);
        
        $activityTypeOne = new RecordOfActivityType($program, 1);
        
        $activityParticipantOne = new RecordOfActivityParticipant($activityTypeOne, null, 1);
        
        $activityOne = new RecordOfActivity($activityTypeOne, '1');
        $activityTwo = new RecordOfActivity($activityTypeOne, '2');
        
        $inviteeOne = new RecordOfInvitee($activityOne, $activityParticipantOne, '1');
        $inviteeTwo = new RecordOfInvitee($activityTwo, $activityParticipantOne, '2');
        
        $this->participantInviteeOne = new RecordOfParticipantInvitee($participant, $inviteeOne);
        $this->participantInviteeTwo = new RecordOfParticipantInvitee($participant, $inviteeTwo);
        
        $otherClient = new RecordOfClient($firm, 'other');
        $otherParticipant = new RecordOfParticipant($program, 'other');
        $this->otherClientParticipant = new RecordOfClientParticipant($otherClient, $otherParticipant);
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('NegotiatedMentoring')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('BookedMentoringSlot')->truncate();
        $this->connection->table('ActivityType')->truncate();
        $this->connection->table('ActivityParticipant')->truncate();
        $this->connection->table('Activity')->truncate();
        $this->connection->table('Invitee')->truncate();
        $this->connection->table('ParticipantInvitee')->truncate();
    }
    
    protected function showAll()
    {
        $this->insertClientParticipantRecord();
        
        $this->mentorOne_p1->personnel->insert($this->connection);
        $this->mentorTwo_p2->personnel->insert($this->connection);
        
        $this->mentorOne_p1->insert($this->connection);
        $this->mentorTwo_p2->insert($this->connection);
        
        $this->negotiatedMentoringOne_m1->mentoringRequest->insert($this->connection);
        $this->negotiatedMentoringTwo_m2->mentoringRequest->insert($this->connection);
        
        $this->negotiatedMentoringOne_m1->insert($this->connection);
        $this->negotiatedMentoringTwo_m2->insert($this->connection);
        
        $this->bookedMentoringSlotOne_m1->mentoringSlot->insert($this->connection);
        $this->bookedMentoringSlotTwo_m2->mentoringSlot->insert($this->connection);
        
        $this->bookedMentoringSlotOne_m1->insert($this->connection);
        $this->bookedMentoringSlotTwo_m2->insert($this->connection);
        
        $this->participantInviteeOne->invitee->activity->activityType->insert($this->connection);
        
        $this->participantInviteeOne->invitee->activityParticipant->insert($this->connection);
        
        $this->participantInviteeOne->invitee->activity->insert($this->connection);
        $this->participantInviteeTwo->invitee->activity->insert($this->connection);
        
        $this->participantInviteeOne->insert($this->connection);
        $this->participantInviteeTwo->insert($this->connection);
        
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
                    'startTime' => $this->negotiatedMentoringOne_m1->mentoringRequest->startTime,
                    'teamId' => null,
                    'programId' => $this->negotiatedMentoringOne_m1->mentoringRequest->participant->program->id,
                    'programType' => $this->negotiatedMentoringOne_m1->mentoringRequest->participant->program->programType,
                    'participantId' => $this->negotiatedMentoringOne_m1->mentoringRequest->participant->id,
//                    'participantId' => $this->clientParticipant->participant->id,
//                    'programId' => $this->negotiatedMentoringOne_m1->mentoringRequest->participant->program->id,
//                    'programType' => $this->negotiatedMentoringOne_m1->mentoringRequest->participant->program->programType,
                    'bookedMentoringSlotId' => null,
                    'negotiatedMentoringId' => $this->negotiatedMentoringOne_m1->id,
                    'mentorName' => $this->negotiatedMentoringOne_m1->mentoringRequest->mentor->personnel->getFullName(),
                    'invitationId' => null,
                    'agendaType' => null,
                    'agendaName' => null,
                ],
                [
                    'startTime' => $this->negotiatedMentoringTwo_m2->mentoringRequest->startTime,
                    'teamId' => null,
                    'programId' => $this->negotiatedMentoringTwo_m2->mentoringRequest->participant->program->id,
                    'programType' => $this->negotiatedMentoringTwo_m2->mentoringRequest->participant->program->programType,
                    'participantId' => $this->negotiatedMentoringTwo_m2->mentoringRequest->participant->id,
//                    'participantId' => $this->clientParticipant->participant->id,
//                    'programId' => $this->negotiatedMentoringTwo_m2->mentoringRequest->participant->program->id,
//                    'programType' => $this->negotiatedMentoringTwo_m2->mentoringRequest->participant->program->programType,
                    'bookedMentoringSlotId' => null,
                    'negotiatedMentoringId' => $this->negotiatedMentoringTwo_m2->id,
                    'mentorName' => $this->negotiatedMentoringTwo_m2->mentoringRequest->mentor->personnel->getFullName(),
                    'invitationId' => null,
                    'agendaType' => null,
                    'agendaName' => null,
                ],
                [
                    'startTime' => $this->bookedMentoringSlotOne_m1->mentoringSlot->startTime->format('Y-m-d H:i:s'),
                    'teamId' => null,
                    'programId' => $this->bookedMentoringSlotOne_m1->mentoringSlot->consultant->program->id,
                    'programType' => $this->bookedMentoringSlotOne_m1->mentoringSlot->consultant->program->programType,
                    'participantId' => $this->bookedMentoringSlotOne_m1->participant->id,
//                    'participantId' => $this->clientParticipant->participant->id,
//                    'programId' => $this->bookedMentoringSlotOne_m1->mentoringSlot->consultant->program->id,
//                    'programType' => $this->bookedMentoringSlotOne_m1->mentoringSlot->consultant->program->programType,
                    'bookedMentoringSlotId' => $this->bookedMentoringSlotOne_m1->id,
                    'negotiatedMentoringId' => null,
                    'mentorName' => $this->bookedMentoringSlotOne_m1->mentoringSlot->consultant->personnel->getFullName(),
                    'invitationId' => null,
                    'agendaType' => null,
                    'agendaName' => null,
                ],
                [
                    'startTime' => $this->bookedMentoringSlotTwo_m2->mentoringSlot->startTime->format('Y-m-d H:i:s'),
                    'teamId' => null,
                    'programId' => $this->bookedMentoringSlotTwo_m2->mentoringSlot->consultant->program->id,
                    'programType' => $this->bookedMentoringSlotTwo_m2->mentoringSlot->consultant->program->programType,
                    'participantId' => $this->bookedMentoringSlotTwo_m2->participant->id,
//                    'participantId' => $this->clientParticipant->participant->id,
//                    'programId' => $this->bookedMentoringSlotTwo_m2->mentoringSlot->consultant->program->id,
//                    'programType' => $this->bookedMentoringSlotTwo_m2->mentoringSlot->consultant->program->programType,
                    'bookedMentoringSlotId' => $this->bookedMentoringSlotTwo_m2->id,
                    'negotiatedMentoringId' => null,
                    'mentorName' => $this->bookedMentoringSlotTwo_m2->mentoringSlot->consultant->personnel->getFullName(),
                    'invitationId' => null,
                    'agendaType' => null,
                    'agendaName' => null,
                ],
                [
                    'startTime' => $this->participantInviteeOne->invitee->activity->startDateTime,
                    'teamId' => null,
                    'programId' => $this->participantInviteeOne->participant->program->id,
                    'programType' => $this->participantInviteeOne->participant->program->programType,
                    'participantId' => $this->participantInviteeOne->participant->id,
//                    'participantId' => $this->clientParticipant->participant->id,
//                    'programId' => $this->participantInviteeOne->participant->program->id,
//                    'programType' => $this->participantInviteeOne->participant->program->programType,
                    'bookedMentoringSlotId' => null,
                    'negotiatedMentoringId' => null,
                    'mentorName' => null,
                    'invitationId' => $this->participantInviteeOne->invitee->id,
                    'agendaType' => $this->participantInviteeOne->invitee->activity->activityType->name,
                    'agendaName' => $this->participantInviteeOne->invitee->activity->name,
                ],
                [
                    'startTime' => $this->participantInviteeTwo->invitee->activity->startDateTime,
                    'teamId' => null,
                    'programId' => $this->participantInviteeTwo->participant->program->id,
                    'programType' => $this->participantInviteeTwo->participant->program->programType,
                    'participantId' => $this->participantInviteeTwo->participant->id,
//                    'participantId' => $this->clientParticipant->participant->id,
//                    'programId' => $this->participantInviteeTwo->participant->program->id,
//                    'programType' => $this->participantInviteeTwo->participant->program->programType,
                    'bookedMentoringSlotId' => null,
                    'negotiatedMentoringId' => null,
                    'mentorName' => null,
                    'invitationId' => $this->participantInviteeTwo->invitee->id,
                    'agendaType' => $this->participantInviteeTwo->invitee->activity->activityType->name,
                    'agendaName' => $this->participantInviteeTwo->invitee->activity->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
//$this->seeJsonContains(['print']);
    }
    public function test_showAll_paginationApply()
    {
        $this->negotiatedMentoringOne_m1->mentoringRequest->startTime = (new \DateTimeImmutable('+1 hours'))->format('Y-m-d H:i:s');
        $this->negotiatedMentoringTwo_m2->mentoringRequest->startTime = (new \DateTimeImmutable('+25 hours'))->format('Y-m-d H:i:s');
        $this->bookedMentoringSlotOne_m1->mentoringSlot->startTime = (new \DateTimeImmutable('+27 hours'));
        $this->bookedMentoringSlotTwo_m2->mentoringSlot->startTime = (new \DateTimeImmutable('+5 hours'));
        $this->participantInviteeOne->invitee->activity->startDateTime = (new \DateTimeImmutable('+47 hours'))->format('Y-m-d H:i:s');
        $this->participantInviteeTwo->invitee->activity->startDateTime = (new \DateTimeImmutable('+49 hours'))->format('Y-m-d H:i:s');
        
        $this->showAllUri .= "?pageSize=2&page=1";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 6];
        $this->seeJsonContains($totalResponse);
        
        $negotiatedMentoringOneResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringOne_m1->id];
        $this->seeJsonContains($negotiatedMentoringOneResponse);
        
        $negotiatedMentoringTwoResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringTwo_m2->id];
        $this->seeJsonDoesntContains($negotiatedMentoringTwoResponse);
        
        $bookedMentoringSlotOneResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotOne_m1->id];
        $this->seeJsonDoesntContains($bookedMentoringSlotOneResponse);
        
        $bookedMentoringSlotTwoResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotTwo_m2->id];
        $this->seeJsonContains($bookedMentoringSlotTwoResponse);
        
        $participantInviteeOneResponse = ['invitationId' => $this->participantInviteeOne->invitee->id];
        $this->seeJsonDoesntContains($participantInviteeOneResponse);
        
        $participantInviteeTwoResponse = ['invitationId' => $this->participantInviteeTwo->invitee->id];
        $this->seeJsonDoesntContains($participantInviteeTwoResponse);
    }
    public function test_showAll_fromFilterApply()
    {
        $this->negotiatedMentoringOne_m1->mentoringRequest->startTime = (new \DateTimeImmutable('+1 hours'))->format('Y-m-d H:i:s');
        $this->negotiatedMentoringTwo_m2->mentoringRequest->startTime = (new \DateTimeImmutable('+25 hours'))->format('Y-m-d H:i:s');
        $this->bookedMentoringSlotOne_m1->mentoringSlot->startTime = (new \DateTimeImmutable('+27 hours'));
        $this->bookedMentoringSlotTwo_m2->mentoringSlot->startTime = (new \DateTimeImmutable('+5 hours'));
        $this->participantInviteeOne->invitee->activity->startDateTime = (new \DateTimeImmutable('+47 hours'))->format('Y-m-d H:i:s');
        $this->participantInviteeTwo->invitee->activity->startDateTime = (new \DateTimeImmutable('+49 hours'))->format('Y-m-d H:i:s');
        
        $fromFilter = (new \DateTimeImmutable('+27 hours'))->format('Y-m-d H:i:s');
        $this->showAllUri .= "?from=$fromFilter";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 3];
        $this->seeJsonContains($totalResponse);
        
        $negotiatedMentoringOneResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringOne_m1->id];
        $this->seeJsonDoesntContains($negotiatedMentoringOneResponse);
        
        $negotiatedMentoringThreeResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringTwo_m2->id];
        $this->seeJsonDoesntContains($negotiatedMentoringThreeResponse);
        
        $bookedMentoringSlotOneResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotOne_m1->id];
        $this->seeJsonContains($bookedMentoringSlotOneResponse);
        
        $bookedMentoringSlotThreeResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotTwo_m2->id];
        $this->seeJsonDoesntContains($bookedMentoringSlotThreeResponse);
        
        $participantInviteeOneResponse = ['invitationId' => $this->participantInviteeOne->invitee->id];
        $this->seeJsonContains($participantInviteeOneResponse);
        
        $participantInviteeThreeResponse = ['invitationId' => $this->participantInviteeTwo->invitee->id];
        $this->seeJsonContains($participantInviteeThreeResponse);
    }
    public function test_showAll_toFilterApply()
    {
        $this->negotiatedMentoringOne_m1->mentoringRequest->startTime = (new \DateTimeImmutable('+1 hours'))->format('Y-m-d H:i:s');
        $this->negotiatedMentoringTwo_m2->mentoringRequest->startTime = (new \DateTimeImmutable('+25 hours'))->format('Y-m-d H:i:s');
        $this->bookedMentoringSlotOne_m1->mentoringSlot->startTime = (new \DateTimeImmutable('+27 hours'));
        $this->bookedMentoringSlotTwo_m2->mentoringSlot->startTime = (new \DateTimeImmutable('+5 hours'));
        $this->participantInviteeOne->invitee->activity->startDateTime = (new \DateTimeImmutable('+47 hours'))->format('Y-m-d H:i:s');
        $this->participantInviteeTwo->invitee->activity->startDateTime = (new \DateTimeImmutable('+49 hours'))->format('Y-m-d H:i:s');
        
        $toFilter = (new \DateTimeImmutable('+26 hours'))->format('Y-m-d H:i:s');
        $this->showAllUri .= "?to=$toFilter";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 3];
        $this->seeJsonContains($totalResponse);
        
        $negotiatedMentoringOneResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringOne_m1->id];
        $this->seeJsonContains($negotiatedMentoringOneResponse);
        
        $negotiatedMentoringThreeResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringTwo_m2->id];
        $this->seeJsonContains($negotiatedMentoringThreeResponse);
        
        $bookedMentoringSlotOneResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotOne_m1->id];
        $this->seeJsonDoesntContains($bookedMentoringSlotOneResponse);
        
        $bookedMentoringSlotThreeResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotTwo_m2->id];
        $this->seeJsonContains($bookedMentoringSlotThreeResponse);
        
        $participantInviteeOneResponse = ['invitationId' => $this->participantInviteeOne->invitee->id];
        $this->seeJsonDoesntContains($participantInviteeOneResponse);
        
        $participantInviteeThreeResponse = ['invitationId' => $this->participantInviteeTwo->invitee->id];
        $this->seeJsonDoesntContains($participantInviteeThreeResponse);
    }
    public function test_showAll_orderApply()
    {
        $this->negotiatedMentoringOne_m1->mentoringRequest->startTime = (new \DateTimeImmutable('+1 hours'))->format('Y-m-d H:i:s');
        $this->negotiatedMentoringTwo_m2->mentoringRequest->startTime = (new \DateTimeImmutable('+25 hours'))->format('Y-m-d H:i:s');
        $this->bookedMentoringSlotOne_m1->mentoringSlot->startTime = (new \DateTimeImmutable('+27 hours'));
        $this->bookedMentoringSlotTwo_m2->mentoringSlot->startTime = (new \DateTimeImmutable('+5 hours'));
        $this->participantInviteeOne->invitee->activity->startDateTime = (new \DateTimeImmutable('+47 hours'))->format('Y-m-d H:i:s');
        $this->participantInviteeTwo->invitee->activity->startDateTime = (new \DateTimeImmutable('+49 hours'))->format('Y-m-d H:i:s');
        
        $toFilter = (new \DateTimeImmutable('+26 hours'))->format('Y-m-d H:i:s');
        $this->showAllUri .= "?to=$toFilter&order=DESC&pageSize=2";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 3];
        $this->seeJsonContains($totalResponse);
        
        $negotiatedMentoringOneResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringOne_m1->id];
        $this->seeJsonDoesntContains($negotiatedMentoringOneResponse);
        
        $negotiatedMentoringThreeResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringTwo_m2->id];
        $this->seeJsonContains($negotiatedMentoringThreeResponse);
        
        $bookedMentoringSlotOneResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotOne_m1->id];
        $this->seeJsonDoesntContains($bookedMentoringSlotOneResponse);
        
        $bookedMentoringSlotThreeResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotTwo_m2->id];
        $this->seeJsonContains($bookedMentoringSlotThreeResponse);
        
        $participantInviteeOneResponse = ['invitationId' => $this->participantInviteeOne->invitee->id];
        $this->seeJsonDoesntContains($participantInviteeOneResponse);
        
        $participantInviteeThreeResponse = ['invitationId' => $this->participantInviteeTwo->invitee->id];
        $this->seeJsonDoesntContains($participantInviteeThreeResponse);
    }
    public function test_showAll_containDisabledBookedMentoringSlot_excludeDisabledReservationSlot()
    {
        $this->bookedMentoringSlotOne_m1->cancelled = true;
        
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 5];
        $this->seeJsonContains($totalResponse);
        
        $negotiatedMentoringOneResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringOne_m1->id];
        $this->seeJsonContains($negotiatedMentoringOneResponse);
        
        $negotiatedMentoringThreeResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringTwo_m2->id];
        $this->seeJsonContains($negotiatedMentoringThreeResponse);
        
        $bookedMentoringSlotOneResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotOne_m1->id];
        $this->seeJsonDoesntContains($bookedMentoringSlotOneResponse);
        
        $bookedMentoringSlotThreeResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotTwo_m2->id];
        $this->seeJsonContains($bookedMentoringSlotThreeResponse);
        
        $participantInviteeOneResponse = ['invitationId' => $this->participantInviteeOne->invitee->id];
        $this->seeJsonContains($participantInviteeOneResponse);
        
        $participantInviteeThreeResponse = ['invitationId' => $this->participantInviteeTwo->invitee->id];
        $this->seeJsonContains($participantInviteeThreeResponse);
    }
    public function test_showAll_containCancelledInvitation_excludeCancelledInvitation()
    {
        $this->participantInviteeTwo->invitee->cancelled = true;
        
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 5];
        $this->seeJsonContains($totalResponse);
        
        $negotiatedMentoringOneResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringOne_m1->id];
        $this->seeJsonContains($negotiatedMentoringOneResponse);
        
        $negotiatedMentoringThreeResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringTwo_m2->id];
        $this->seeJsonContains($negotiatedMentoringThreeResponse);
        
        $bookedMentoringSlotOneResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotOne_m1->id];
        $this->seeJsonContains($bookedMentoringSlotOneResponse);
        
        $bookedMentoringSlotThreeResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotTwo_m2->id];
        $this->seeJsonContains($bookedMentoringSlotThreeResponse);
        
        $participantInviteeOneResponse = ['invitationId' => $this->participantInviteeOne->invitee->id];
        $this->seeJsonContains($participantInviteeOneResponse);
        
        $participantInviteeThreeResponse = ['invitationId' => $this->participantInviteeTwo->invitee->id];
        $this->seeJsonDoesntContains($participantInviteeThreeResponse);
    }
    public function test_showAll_excludeScheduleOfOtherParticipant()
    {
        $this->otherClientParticipant->client->insert($this->connection);
        $this->otherClientParticipant->insert($this->connection);
        
        $this->negotiatedMentoringTwo_m2->mentoringRequest->participant = $this->otherClientParticipant->participant;
        $this->bookedMentoringSlotOne_m1->participant = $this->otherClientParticipant->participant;
        $this->participantInviteeOne->participant = $this->otherClientParticipant->participant;
        
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 3];
        $this->seeJsonContains($totalResponse);

        $negotiatedMentoringOneResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringOne_m1->id];
        $this->seeJsonContains($negotiatedMentoringOneResponse);

        $negotiatedMentoringTwoResponse = ['negotiatedMentoringId' => $this->negotiatedMentoringTwo_m2->id];
        $this->seeJsonDoesntContains($negotiatedMentoringTwoResponse);

        $bookedMentoringSlotOneResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotOne_m1->id];
        $this->seeJsonDoesntContains($bookedMentoringSlotOneResponse);

        $bookedMentoringSlotTwoResponse = ['bookedMentoringSlotId' => $this->bookedMentoringSlotTwo_m2->id];
        $this->seeJsonContains($bookedMentoringSlotTwoResponse);

        $participantInviteeOneResponse = ['invitationId' => $this->participantInviteeOne->invitee->id];
        $this->seeJsonDoesntContains($participantInviteeOneResponse);

        $participantInviteeTwoResponse = ['invitationId' => $this->participantInviteeTwo->invitee->id];
        $this->seeJsonContains($participantInviteeTwoResponse);
    }
}
