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
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;

class MentoringRequestControllerTest extends PersonnelTestCase
{
    protected $mentorTwo;
    protected $consultationSetupOne;
    protected $consultationSetupTwo;
    protected $clientParticipantOne;
    protected $teamParticipantTwo;
    protected $mentoringRequestOne;
    protected $mentoringRequestTwo;
    protected $mentoringRequestThree;
    protected $mentoringSlotOne;
    protected $offerRequest, $proposeRequest;
    protected $showAllUnrespondedUri;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('NegotiatedMentoring')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        
        $program = $this->mentor->program;
        $firm = $program->firm;
        
        $programTwo = new RecordOfProgram($firm, '2');
        
        $this->mentorTwo = new RecordOfConsultant($programTwo, $this->personnel, '2');
        
        $participantOne = new RecordOfParticipant($program, '1');
        $participantTwo = new RecordOfParticipant($programTwo, '2');
        
        $clientOne = new RecordOfClient($firm, '1');
        $this->clientParticipantOne = new RecordOfClientParticipant($clientOne, $participantOne);

        $teamOne = new RecordOfTeam($firm, $clientOne, '1');
        $this->teamParticipantTwo = new RecordOfTeamProgramParticipation($teamOne, $participantTwo);
        
        $this->consultationSetupOne = new RecordOfConsultationSetup($program, null, null, '1');
        $this->consultationSetupTwo = new RecordOfConsultationSetup($programTwo, null, null, '2');
        
        $this->mentoringRequestOne = new RecordOfMentoringRequest($participantOne, $this->mentor, $this->consultationSetupOne, '1');
        $this->mentoringRequestTwo = new RecordOfMentoringRequest($participantOne, $this->mentor, $this->consultationSetupOne, '2');
        $this->mentoringRequestThree = new RecordOfMentoringRequest($participantTwo, $this->mentorTwo, $this->consultationSetupTwo, '3');
        
        $this->mentoringSlotOne = new RecordOfMentoringSlot($this->mentor, $this->consultationSetupOne, '1');
        
        $this->showAllUnrespondedUri = $this->personnelUri . "/mentoring-requests/all-unresponded";
        
        $this->offerRequest = [
            'startTime' => (new DateTimeImmutable('+600 minutes'))->format('Y-m-d H:i:s'),
            'mediaType' => 'new media type',
            'location' => 'new location',
        ];
        $this->proposeRequest = [
            'consultationSetupId' => $this->consultationSetupOne->id,
            'participantId' => $this->clientParticipantOne->participant->id,
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
        $this->connection->table('Team')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('NegotiatedMentoring')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
    }
    
    protected function propose() {
        $this->insertMentorDependency();
        
        $this->consultationSetupOne->insert($this->connection);
        $this->clientParticipantOne->client->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        
        $uri = $this->personnelUri . "/mentors/{$this->mentor->id}/mentoring-requests/";
        $this->post($uri, $this->proposeRequest, $this->mentor->personnel->token);
//echo $uri;
//echo json_encode($this->proposeRequest);
//$this->seeJsonContains(['print']);
    }
    public function test_propose_201() {
$this->disableExceptionHandling();
        $this->propose();
        $this->seeStatusCode(201);
        
        $endTime = (new DateTimeImmutable('+660 minutes'))->format('Y-m-d H:i:s');
        $response = [
            'startTime' => $this->proposeRequest['startTime'],
            'endTime' => $endTime,
            'mediaType' => $this->proposeRequest['mediaType'],
            'location' => $this->proposeRequest['location'],
            'requestStatus' => MentoringRequestStatus::DISPLAY_VALUE[MentoringRequestStatus::OFFERED],
            'participant' => [
                'id' => $this->clientParticipantOne->participant->id,
                'client' => [
                    'id' => $this->clientParticipantOne->client->id,
                    'name' => $this->clientParticipantOne->client->getFullName(),
                ],
                'team' => null,
                'user' => null,
            ],
            'consultationSetup' => [
                'id' => $this->consultationSetupOne->id,
                'name' => $this->consultationSetupOne->name,
            ],
            'mentor' => [
                'id' => $this->mentor->id,
                'program' => [
                    'id' => $this->mentor->program->id,
                    'name' => $this->mentor->program->name,
                ],
                
            ],
        ];
        $this->seeJsonContains($response);
        
        $record = [
            'Consultant_id' => $this->mentor->id,
            'Participant_id' => $this->clientParticipantOne->participant->id,
            'ConsultationSetup_id' => $this->consultationSetupOne->id,
            'startTime' => $this->proposeRequest['startTime'],
            'endTime' => $endTime,
            'mediaType' => $this->proposeRequest['mediaType'],
            'location' => $this->proposeRequest['location'],
            'requestStatus' => MentoringRequestStatus::OFFERED,
        ];
        $this->seeInDatabase('MentoringRequest', $record);
    }
    public function test_propose_obsoleteSchedule_403() {
        $this->proposeRequest['startTime'] = (new DateTimeImmutable('-1 hours'))->format('Y-m-d H:i:s');
        
        $this->propose();
        $this->seeStatusCode(403);
    }
    public function test_propose_unuseableConsultationSetup_belongsToOtherProgram_403() {
        $this->mentorTwo->program->insert($this->connection);
        $this->mentorTwo->insert($this->connection);
        $this->consultationSetupOne->program = $this->mentorTwo->program;
        
        $this->propose();
        $this->seeStatusCode(403);
    }
    public function test_propose_unusableConsultationSetup_disabled_403() {
        $this->consultationSetupOne->removed = true;
        
        $this->propose();
        $this->seeStatusCode(403);
    }
    public function test_propose_unusableParticipant_belongsToOtherProgram_403() {
        $this->mentorTwo->program->insert($this->connection);
        $this->mentorTwo->insert($this->connection);
        $this->clientParticipantOne->participant->program = $this->mentorTwo->program;
        
        $this->propose();
        $this->seeStatusCode(403);
    }
    public function test_propose_unusableParticipant_inactive_403() {
        $this->clientParticipantOne->participant->active = false;
        
        $this->propose();
        $this->seeStatusCode(403);
    }
    public function test_propose_conflictedSchedule_403() {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::OFFERED;
        $this->mentoringRequestOne->insert($this->connection);
        $this->proposeRequest['startTime'] = $this->mentoringRequestOne->startTime;
        
        $this->propose();
        $this->seeStatusCode(403);
    }
    public function test_propose_inactiveMentor_403() {
        $this->mentor->active = false;
        
        $this->propose();
        $this->seeStatusCode(403);
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
// echo $uri;
        $this->get($uri, $this->mentor->personnel->token);
    }
    public function test_show_200()
    {
        $this->show();
        $this->seeStatusCode(200);
// $this->seeJsonContains(['print']);
        
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
            'mentor' => [
                'id' => $this->mentoringRequestOne->mentor->id,
                'program' => [
                    'id' => $this->mentoringRequestOne->mentor->program->id,
                    'name' => $this->mentoringRequestOne->mentor->program->name,
                ],
                
            ],
        ];
        $this->seeJsonContains($response);
    }
    
    protected function showAllUnresponded()
    {
        $this->insertMentorDependency();
        
        $this->clientParticipantOne->client->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        
        $this->teamParticipantTwo->team->insert($this->connection);
        $this->teamParticipantTwo->insert($this->connection);
        
        $this->mentoringRequestOne->consultationSetup->insert($this->connection);
        $this->mentoringRequestThree->consultationSetup->insert($this->connection);
        
        $this->mentoringRequestThree->mentor->program->insert($this->connection);
        $this->mentoringRequestThree->mentor->insert($this->connection);
        
        $this->mentoringRequestOne->insert($this->connection);
        $this->mentoringRequestTwo->insert($this->connection);
        $this->mentoringRequestThree->insert($this->connection);
        
        $this->get($this->showAllUnrespondedUri, $this->personnel->token);
    }
    public function test_showAllUnresponded_200()
    {
$this->disableExceptionHandling();
        $this->showAllUnresponded();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 3,
            'list' => [
                [
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
                    'mentor' => [
                        'id' => $this->mentoringRequestOne->mentor->id,
                        'program' => [
                            'id' => $this->mentoringRequestOne->mentor->program->id,
                            'name' => $this->mentoringRequestOne->mentor->program->name,
                        ],

                    ],
                ],
                [
                    'id' => $this->mentoringRequestTwo->id,
                    'startTime' => $this->mentoringRequestTwo->startTime,
                    'endTime' => $this->mentoringRequestTwo->endTime,
                    'mediaType' => $this->mentoringRequestTwo->mediaType,
                    'location' => $this->mentoringRequestTwo->location,
                    'requestStatus' => MentoringRequestStatus::DISPLAY_VALUE[$this->mentoringRequestTwo->requestStatus],
                    'participant' => [
                        'id' => $this->mentoringRequestTwo->participant->id,
                        'client' => [
                            'id' => $this->clientParticipantOne->client->id,
                            'name' => $this->clientParticipantOne->client->getFullName(),
                        ],
                        'team' => null,
                        'user' => null,
                    ],
                    'consultationSetup' => [
                        'id' => $this->mentoringRequestTwo->consultationSetup->id,
                        'name' => $this->mentoringRequestTwo->consultationSetup->name,
                    ],
                    'mentor' => [
                        'id' => $this->mentoringRequestTwo->mentor->id,
                        'program' => [
                            'id' => $this->mentoringRequestTwo->mentor->program->id,
                            'name' => $this->mentoringRequestTwo->mentor->program->name,
                        ],

                    ],
                ],
                [
                    'id' => $this->mentoringRequestThree->id,
                    'startTime' => $this->mentoringRequestThree->startTime,
                    'endTime' => $this->mentoringRequestThree->endTime,
                    'mediaType' => $this->mentoringRequestThree->mediaType,
                    'location' => $this->mentoringRequestThree->location,
                    'requestStatus' => MentoringRequestStatus::DISPLAY_VALUE[$this->mentoringRequestThree->requestStatus],
                    'participant' => [
                        'id' => $this->mentoringRequestThree->participant->id,
                        'client' => null,
                        'team' => [
                            'id' => $this->teamParticipantTwo->team->id,
                            'name' => $this->teamParticipantTwo->team->name,
                        ],
                        'user' => null,
                    ],
                    'consultationSetup' => [
                        'id' => $this->mentoringRequestThree->consultationSetup->id,
                        'name' => $this->mentoringRequestThree->consultationSetup->name,
                    ],
                    'mentor' => [
                        'id' => $this->mentoringRequestThree->mentor->id,
                        'program' => [
                            'id' => $this->mentoringRequestThree->mentor->program->id,
                            'name' => $this->mentoringRequestThree->mentor->program->name,
                        ],

                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAllUnresponded_excludeRespondedRequest_offered_cancelled_rejected()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::OFFERED;
        $this->mentoringRequestTwo->requestStatus = MentoringRequestStatus::CANCELLED;
        $this->mentoringRequestThree->requestStatus = MentoringRequestStatus::REJECTED;
        
        $this->showAllUnresponded();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 0]);
        $this->seeJsonDoesntContains(['id' => $this->mentoringRequestOne->id]);
        $this->seeJsonDoesntContains(['id' => $this->mentoringRequestTwo->id]);
        $this->seeJsonDoesntContains(['id' => $this->mentoringRequestThree->id]);
    }
    public function test_showAllUnresponded_excludeRespondedRequest_approved_accepted()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::APPROVED_BY_MENTOR;
        $this->mentoringRequestTwo->requestStatus = MentoringRequestStatus::ACCEPTED_BY_PARTICIPANT;
        
        $this->showAllUnresponded();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 1]);
        $this->seeJsonDoesntContains(['id' => $this->mentoringRequestOne->id]);
        $this->seeJsonDoesntContains(['id' => $this->mentoringRequestTwo->id]);
        $this->seeJsonContains(['id' => $this->mentoringRequestThree->id]);
    }
    public function test_showAllUnresponded_excludeRequestToInactiveMentor()
    {
        $this->mentorTwo->active = false;
        
        $this->showAllUnresponded();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 2]);
        $this->seeJsonContains(['id' => $this->mentoringRequestOne->id]);
        $this->seeJsonContains(['id' => $this->mentoringRequestTwo->id]);
        $this->seeJsonDoesntContains(['id' => $this->mentoringRequestThree->id]);
    }
    public function test_showAllUnresponded_excludeRequestToOtherMentor()
    {
        $otherPersonnel = new RecordOfPersonnel($this->personnel->firm, 'other');
        $otherPersonnel->insert($this->connection);
        $otherMentor = new RecordOfConsultant($this->mentorTwo->program, $otherPersonnel, 'other');
        $this->mentoringRequestThree->mentor = $otherMentor;
        
        $this->showAllUnresponded();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 2]);
        $this->seeJsonContains(['id' => $this->mentoringRequestOne->id]);
        $this->seeJsonContains(['id' => $this->mentoringRequestTwo->id]);
        $this->seeJsonDoesntContains(['id' => $this->mentoringRequestThree->id]);
    }
    public function test_showAllUnresponded_fromFilter()
    {
        $this->mentoringRequestTwo->startTime = (new \DateTime('+72 hours'))->format('y-m-d H:i:s');
        $this->mentoringRequestTwo->endTime = (new \DateTime('+73 hours'))->format('y-m-d H:i:s');
        
        $fromQuery = (new \DateTime('+70 hours'))->format('Y-m-d H:i:s');
        $this->showAllUnrespondedUri .= "?from=$fromQuery";
        
        $this->showAllUnresponded();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 1]);
        $this->seeJsonDoesntContains(['id' => $this->mentoringRequestOne->id]);
        $this->seeJsonContains(['id' => $this->mentoringRequestTwo->id]);
        $this->seeJsonDoesntContains(['id' => $this->mentoringRequestThree->id]);
    }
    public function test_showAllUnresponded_toFilter()
    {
        $this->mentoringRequestTwo->startTime = (new \DateTime('-73 hours'))->format('y-m-d H:i:s');
        $this->mentoringRequestTwo->endTime = (new \DateTime('-72 hours'))->format('y-m-d H:i:s');
        
        $toQuery = (new \DateTime('-70 hours'))->format('Y-m-d H:i:s');
        $this->showAllUnrespondedUri .= "?to=$toQuery";
        
        $this->showAllUnresponded();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 1]);
        $this->seeJsonDoesntContains(['id' => $this->mentoringRequestOne->id]);
        $this->seeJsonContains(['id' => $this->mentoringRequestTwo->id]);
        $this->seeJsonDoesntContains(['id' => $this->mentoringRequestThree->id]);
    }
    public function test_showAllUnresponded_orderSet()
    {
        $this->mentoringRequestTwo->startTime = (new \DateTime('+72 hours'))->format('y-m-d H:i:s');
        $this->mentoringRequestTwo->endTime = (new \DateTime('+73 hours'))->format('y-m-d H:i:s');
        $this->mentoringRequestThree->startTime = (new \DateTime('+124 hours'))->format('y-m-d H:i:s');
        $this->mentoringRequestThree->endTime = (new \DateTime('+125 hours'))->format('y-m-d H:i:s');
        
        $this->showAllUnrespondedUri .= "?order=DESC&pageSize=1";
        
        $this->showAllUnresponded();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 3]);
        $this->seeJsonDoesntContains(['id' => $this->mentoringRequestOne->id]);
        $this->seeJsonDoesntContains(['id' => $this->mentoringRequestTwo->id]);
        $this->seeJsonContains(['id' => $this->mentoringRequestThree->id]);
    }
}
