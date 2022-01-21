<?php

namespace Tests\Controllers\Personnel;

use DateTimeImmutable;
use SharedContext\Domain\ValueObject\MentoringRequestStatus;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfMentoringSlot;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMentoringRequest;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;

class MentoringRequestControllerTest extends PersonnelTestCase
{
    protected $clientParticipantOne;
    protected $mentoringRequestOne;
    protected $mentoringRequestTwo;
    protected $mentoringSlotOne;
    protected $offerRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('NegotiatedMentoring')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        
        $program = $this->mentor->program;
        $firm = $program->firm;
        
        $participantOne = new RecordOfParticipant($program, '1');
        $clientOne = new RecordOfClient($firm, '1');
        $this->clientParticipantOne = new RecordOfClientParticipant($clientOne, $participantOne);
        
        $consultationSetupOne = new RecordOfConsultationSetup($program, null, null, '1');
        $this->mentoringRequestOne = new RecordOfMentoringRequest($participantOne, $this->mentor, $consultationSetupOne, '1');
        $this->mentoringRequestTwo = new RecordOfMentoringRequest($participantOne, $this->mentor, $consultationSetupOne, '2');
        
        $this->mentoringSlotOne = new RecordOfMentoringSlot($this->mentor, $consultationSetupOne, '1');
        
        $this->offerRequest = [
            'startTime' => (new DateTimeImmutable('+600 minutes'))->format('Y-m-d H:i:s'),
            'mediaType' => 'new media type',
            'location' => 'new location',
        ];
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('NegotiatedMentoring')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
    }
    
    protected function reject()
    {
        $this->insertMentorDependency();
        
        $this->clientParticipantOne->client->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        
        $this->mentoringRequestOne->consultationSetup->insert($this->connection);
        $this->mentoringRequestOne->insert($this->connection);
        
        $uri = $this->personnelUri . "/mentors/{$this->mentor->id}/mentoring-requests/{$this->mentoringRequestOne->id}/reject";
        $this->patch($uri, [], $this->mentor->personnel->token);
    }
    public function test_reject_200()
    {
        $this->reject();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->mentoringRequestOne->id,
            'requestStatus' => MentoringRequestStatus::DISPLAY_VALUE[MentoringRequestStatus::REJECTED],
        ];
        $this->seeJsonContains($response);
        
        $mentoringRequestRecord = [
            'id' => $this->mentoringRequestOne->id,
            'requestStatus' => MentoringRequestStatus::REJECTED,
        ];
        $this->seeInDatabase('MentoringRequest', $mentoringRequestRecord);
    }
    public function test_reject_concludedRequest_accepted_403()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::ACCEPTED_BY_PARTICIPANT;
        $this->reject();
        $this->seeStatusCode(403);
    }
    public function test_reject_concludedRequest_approved_403()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::APPROVED_BY_MENTOR;
        $this->reject();
        $this->seeStatusCode(403);
    }
    public function test_reject_concludedRequest_cancelled_403()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::CANCELLED;
        $this->reject();
        $this->seeStatusCode(403);
    }
    public function test_reject_concludedRequest_rejected_403()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::REJECTED;
        $this->reject();
        $this->seeStatusCode(403);
    }
    public function test_reject_unamnaged_notOwned_403()
    {
        $program = $this->mentor->program;
        $firm = $program->firm;
        $personnel = new RecordOfPersonnel($firm, 'zzz');
        $mentor = new RecordOfConsultant($program, $personnel, 'zzz');
        
        $mentor->personnel->insert($this->connection);
        $mentor->insert($this->connection);
        
        $this->mentoringRequestOne->mentor = $mentor;
        $this->reject();
        $this->seeStatusCode(403);
    }
    public function test_reject_inactiveMentor_403()
    {
        $this->mentor->active = false;
        $this->reject();
        $this->seeStatusCode(403);
    }
    
    protected function approve()
    {
        $this->insertMentorDependency();
        
        $this->clientParticipantOne->client->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        
        $this->mentoringRequestOne->consultationSetup->insert($this->connection);
        $this->mentoringRequestOne->insert($this->connection);
        
        $uri = $this->personnelUri . "/mentors/{$this->mentor->id}/mentoring-requests/{$this->mentoringRequestOne->id}/approve";
        $this->patch($uri, [], $this->mentor->personnel->token);
    }
    public function test_approve_200()
    {
        $this->approve();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->mentoringRequestOne->id,
            'requestStatus' => MentoringRequestStatus::DISPLAY_VALUE[MentoringRequestStatus::APPROVED_BY_MENTOR],
        ];
        $this->seeJsonContains($response);
        
        $mentoringRequestRecord = [
            'id' => $this->mentoringRequestOne->id,
            'requestStatus' => MentoringRequestStatus::APPROVED_BY_MENTOR,
        ];
        $this->seeInDatabase('MentoringRequest', $mentoringRequestRecord);
    }
    public function test_approve_appendNegotiatedMentoring_403()
    {
        $this->approve();
        $this->seeStatusCode(200);
        
        $negotiatedMentoringRecord = [
            'MentoringRequest_id' => $this->mentoringRequestOne->id,
        ];
        $this->seeInDatabase('NegotiatedMentoring', $negotiatedMentoringRecord);
    }
    public function test_approve_notUpcomingRequest_403()
    {
        $this->mentoringRequestOne->startTime = (new DateTimeImmutable('-1 hours'))->format('Y-m-d H:i:s');
        $this->approve();
        $this->seeStatusCode(403);
    }
    public function test_approve_notRequestedStatus_403()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::OFFERED;
        $this->approve();
        $this->seeStatusCode(403);
    }
    public function test_approve_inConflictWithExistingRequest_Approved_403()
    {
        $this->mentoringRequestTwo->requestStatus = MentoringRequestStatus::APPROVED_BY_MENTOR;
        $this->mentoringRequestTwo->insert($this->connection);
        $this->approve();
        $this->seeStatusCode(403);
    }
    public function test_approve_inConflictWithExistingRequest_Accepted_403()
    {
        $this->mentoringRequestTwo->requestStatus = MentoringRequestStatus::ACCEPTED_BY_PARTICIPANT;
        $this->mentoringRequestTwo->insert($this->connection);
        $this->approve();
        $this->seeStatusCode(403);
    }
    public function test_approve_inConflictWithExistingRequest_Offered_403()
    {
        $this->mentoringRequestTwo->requestStatus = MentoringRequestStatus::OFFERED;
        $this->mentoringRequestTwo->insert($this->connection);
        $this->approve();
        $this->seeStatusCode(403);
    }
    public function test_approve_inConflictWithExistingRequest_Requested_200()
    {
        $this->mentoringRequestTwo->requestStatus = MentoringRequestStatus::REQUESTED;
        $this->mentoringRequestTwo->insert($this->connection);
        $this->approve();
        $this->seeStatusCode(200);
    }
    public function test_approve_inConflictWithExistingRequest_cancelled_200()
    {
        $this->mentoringRequestTwo->requestStatus = MentoringRequestStatus::CANCELLED;
        $this->mentoringRequestTwo->insert($this->connection);
        $this->approve();
        $this->seeStatusCode(200);
    }
    public function test_approve_inConflictWithExistingRequest_rejected_200()
    {
        $this->mentoringRequestTwo->requestStatus = MentoringRequestStatus::REJECTED;
        $this->mentoringRequestTwo->insert($this->connection);
        $this->approve();
        $this->seeStatusCode(200);
    }
    public function test_approve_inConflictWithMentoringSlot_403()
    {
        $this->mentoringSlotOne->insert($this->connection);
        $this->approve();
        $this->seeStatusCode(403);
    }
    public function test_approve_inConflictWithInactiveMentoringSlot_200()
    {
        $this->mentoringSlotOne->cancelled = true;
        $this->mentoringSlotOne->insert($this->connection);
        $this->approve();
        $this->seeStatusCode(200);
    }
    public function test_approve_unamnaged_notOwned_403()
    {
        $program = $this->mentor->program;
        $firm = $program->firm;
        $personnel = new RecordOfPersonnel($firm, 'zzz');
        $mentor = new RecordOfConsultant($program, $personnel, 'zzz');
        
        $mentor->personnel->insert($this->connection);
        $mentor->insert($this->connection);
        
        $this->mentoringRequestOne->mentor = $mentor;
        $this->approve();
        $this->seeStatusCode(403);
    }
    public function test_approved_inactiveMentor_403()
    {
        $this->mentor->active = false;
        $this->approve();
        $this->seeStatusCode(403);
    }
    
    protected function offer()
    {
        $this->insertMentorDependency();
        
        $this->clientParticipantOne->client->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        
        $this->mentoringRequestOne->consultationSetup->insert($this->connection);
        $this->mentoringRequestOne->insert($this->connection);
        
        $uri = $this->personnelUri . "/mentors/{$this->mentor->id}/mentoring-requests/{$this->mentoringRequestOne->id}/offer";
        $this->patch($uri, $this->offerRequest, $this->mentor->personnel->token);
    }
    public function test_offer_200()
    {
        $endTime = (new DateTimeImmutable('+660 minutes'))->format('Y-m-d H:i:s');
        
        $this->offer();
        $this->seeStatusCode(200);
        
        $request = [
            'id' => $this->mentoringRequestOne->id,
            'startTime' => $this->offerRequest['startTime'],
            'endTime' => $endTime,
            'mediaType' => $this->offerRequest['mediaType'],
            'location' => $this->offerRequest['location'],
            'requestStatus' => MentoringRequestStatus::DISPLAY_VALUE[MentoringRequestStatus::OFFERED],
        ];
        $this->seeJsonContains($request);
        
        $record = [
            'id' => $this->mentoringRequestOne->id,
            'startTime' => $this->offerRequest['startTime'],
            'endTime' => $endTime,
            'mediaType' => $this->offerRequest['mediaType'],
            'location' => $this->offerRequest['location'],
            'requestStatus' => MentoringRequestStatus::OFFERED,
        ];
        $this->seeInDatabase('MentoringRequest', $record);
    }
    public function test_offer_offerNonUpcomingSchedule_403()
    {
        $this->offerRequest['startTime'] = (new DateTimeImmutable('-30 minutes'))->format('Y-m-d H:i:s');
        $this->offer();
        $this->seeStatusCode(403);
    }
    public function test_offer_newScheduleInConflictWithExistingRequest_accepted_403()
    {
        $this->mentoringRequestTwo->startTime = (new DateTimeImmutable('+550 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestTwo->endTime = (new DateTimeImmutable('+625 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestTwo->requestStatus = MentoringRequestStatus::ACCEPTED_BY_PARTICIPANT;
        
        $this->mentoringRequestTwo->insert($this->connection);
        $this->offer();
        $this->seeStatusCode(403);
    }
    public function test_offer_newScheduleInConflictWithExistingRequest_approved_403()
    {
        $this->mentoringRequestTwo->startTime = (new DateTimeImmutable('+550 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestTwo->endTime = (new DateTimeImmutable('+625 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestTwo->requestStatus = MentoringRequestStatus::APPROVED_BY_MENTOR;
        
        $this->mentoringRequestTwo->insert($this->connection);
        $this->offer();
        $this->seeStatusCode(403);
    }
    public function test_offer_newScheduleInConflictWithExistingRequest_offered_403()
    {
        $this->mentoringRequestTwo->startTime = (new DateTimeImmutable('+550 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestTwo->endTime = (new DateTimeImmutable('+625 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestTwo->requestStatus = MentoringRequestStatus::OFFERED;
        
        $this->mentoringRequestTwo->insert($this->connection);
        $this->offer();
        $this->seeStatusCode(403);
    }
    public function test_offer_newScheduleInConflictWithExistingRequest_cancelled_200()
    {
        $this->mentoringRequestTwo->startTime = (new DateTimeImmutable('+550 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestTwo->endTime = (new DateTimeImmutable('+625 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestTwo->requestStatus = MentoringRequestStatus::CANCELLED;
        
        $this->mentoringRequestTwo->insert($this->connection);
        $this->offer();
        $this->seeStatusCode(200);
    }
    public function test_offer_newScheduleInConflictWithExistingRequest_rejected_200()
    {
        $this->mentoringRequestTwo->startTime = (new DateTimeImmutable('+550 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestTwo->endTime = (new DateTimeImmutable('+625 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestTwo->requestStatus = MentoringRequestStatus::REJECTED;
        
        $this->mentoringRequestTwo->insert($this->connection);
        $this->offer();
        $this->seeStatusCode(200);
    }
    public function test_offer_newScheduleInConflictWithExistingRequest_requested_200()
    {
        $this->mentoringRequestTwo->startTime = (new DateTimeImmutable('+550 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestTwo->endTime = (new DateTimeImmutable('+625 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestTwo->requestStatus = MentoringRequestStatus::REQUESTED;
        
        $this->mentoringRequestTwo->insert($this->connection);
        $this->offer();
        $this->seeStatusCode(200);
    }
    public function test_offer_newScheduleInConflictWithMentoringSlot_403()
    {
        $this->mentoringSlotOne->startTime = (new DateTimeImmutable('+550 minutes'));
        $this->mentoringSlotOne->endTime = (new DateTimeImmutable('+625 minutes'));
        $this->mentoringSlotOne->insert($this->connection);
        
        $this->offer();
        $this->seeStatusCode(403);
    }
    public function test_offer_newScheduleInConflictWithCancelledMentoringSlot_200()
    {
        $this->mentoringSlotOne->startTime = (new DateTimeImmutable('+550 minutes'));
        $this->mentoringSlotOne->endTime = (new DateTimeImmutable('+625 minutes'));
        $this->mentoringSlotOne->cancelled = true;
        $this->mentoringSlotOne->insert($this->connection);
        
        $this->offer();
        $this->seeStatusCode(200);
    }
    public function test_offer_unamnaged_notOwned_403()
    {
        $program = $this->mentor->program;
        $firm = $program->firm;
        $personnel = new RecordOfPersonnel($firm, 'zzz');
        $mentor = new RecordOfConsultant($program, $personnel, 'zzz');
        
        $mentor->personnel->insert($this->connection);
        $mentor->insert($this->connection);
        
        $this->mentoringRequestOne->mentor = $mentor;
        $this->offer();
        $this->seeStatusCode(403);
    }
    public function test_offerd_inactiveMentor_403()
    {
        $this->mentor->active = false;
        $this->offer();
        $this->seeStatusCode(403);
    }
    
    protected function show()
    {
        $this->insertMentorDependency();
        
        $this->clientParticipantOne->client->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        
        $this->mentoringRequestOne->consultationSetup->insert($this->connection);
        $this->mentoringRequestOne->insert($this->connection);
        
        $uri = $this->personnelUri . "/mentoring-requests/{$this->mentoringRequestOne->id}";
        $this->get($uri, $this->mentor->personnel->token);
    }
    public function test_show_200()
    {
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->mentoringRequestOne->id,
            'startTime' => $this->mentoringRequestOne->startTime,
            'endTime' => $this->mentoringRequestOne->endTime,
            'mediaType' => $this->mentoringRequestOne->mediaType,
            'location' => $this->mentoringRequestOne->location,
            'requestStatus' => MentoringRequestStatus::DISPLAY_VALUE[$this->mentoringRequestOne->requestStatus],
            'participant' => [
                'id' => $this->mentoringRequestOne->participant->id,
                'client' => [
                    'id' => $this->clientParticipantOne->client->id,
                    'name' => $this->clientParticipantOne->client->getFullName(),
                ],
                'team' => null,
                'user' => null,
            ],
            'consultationSetup' => [
                'id' => $this->mentoringRequestOne->consultationSetup->id,
                'name' => $this->mentoringRequestOne->consultationSetup->name,
            ],
        ];
        $this->seeJsonContains($response);
    }
}