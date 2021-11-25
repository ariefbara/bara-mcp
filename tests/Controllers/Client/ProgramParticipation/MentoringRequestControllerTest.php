<?php

namespace Tests\Controllers\Client\ProgramParticipation;

use DateTimeImmutable;
use SharedContext\Domain\ValueObject\MentoringRequestStatus;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\MentoringSlot\RecordOfBookedMentoringSlot;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfMentoringSlot;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMentoringRequest;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Shared\RecordOfMentoring;

class MentoringRequestControllerTest extends ExtendedClientParticipantTestCase
{
    protected $mentorOne;
    protected $consultationSetupOne;
    protected $mentoringRequestOne;
    protected $mentoringRequestTwo_offered;
    
    protected $submitRequestData;
    protected $updateRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('NegotiatedMentoring')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('BookedMentoringSlot')->truncate();
        
        $program = $this->clientParticipant->participant->program;
        $firm = $program->firm;
        
        $personnel = new RecordOfPersonnel($firm, '1');
        $this->mentorOne = new RecordOfConsultant($program, $personnel, '1');
        $this->consultationSetupOne = new RecordOfConsultationSetup($program, null, null, '1');
        $this->mentoringRequestOne = new RecordOfMentoringRequest($this->clientParticipant->participant, $this->mentorOne, $this->consultationSetupOne, '1');
        $this->mentoringRequestTwo_offered = new RecordOfMentoringRequest($this->clientParticipant->participant, $this->mentorOne, $this->consultationSetupOne, '2');
        $this->mentoringRequestTwo_offered->requestStatus = MentoringRequestStatus::OFFERED;
        
        $this->submitRequestData = [
            'mentorId' => $this->mentorOne->id,
            'consultationSetupId' => $this->consultationSetupOne->id,
            'startTime' => (new DateTimeImmutable('+600 minutes'))->format('Y-m-d H:i:s'),
            'mediaType' => 'new media type',
            'location' => 'new location',
        ];
        $this->updateRequest = [
            'startTime' => (new DateTimeImmutable('+600 minutes'))->format('Y-m-d H:i:s'),
            'mediaType' => 'new media type',
            'location' => 'new location',
        ];
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
    }
    
    protected function submitRequest()
    {
        $this->insertClientParticipantRecord();
        
        $this->mentorOne->personnel->insert($this->connection);
        $this->mentorOne->insert($this->connection);
        $this->consultationSetupOne->insert($this->connection);
        
        $uri = $this->clientParticipantUri . "/mentoring-requests";
        $this->post($uri, $this->submitRequestData, $this->clientParticipant->client->token);
    }
    public function test_submitRequest_201()
    {
        $endTime = (new DateTimeImmutable('+660 minutes'))->format('Y-m-d H:i:s');
        $this->submitRequest();
        $this->seeStatusCode(201);
        
        $response = [
            'startTime' => $this->submitRequestData['startTime'],
            'endTime' => $endTime,
            'mediaType' => $this->submitRequestData['mediaType'],
            'location' => $this->submitRequestData['location'],
            'requestStatus' => MentoringRequestStatus::DISPLAY_VALUE[MentoringRequestStatus::REQUESTED],
            'mentor' => [
                'id' => $this->mentorOne->id,
                'personnel' => [
                    'id' => $this->mentorOne->personnel->id,
                    'name' => $this->mentorOne->personnel->getFullName(),
                ],
            ],
            'consultationSetup' => [
                'id' => $this->consultationSetupOne->id,
                'name' => $this->consultationSetupOne->name,
            ],
        ];
        $this->seeJsonContains($response);
        
        $mentoringRequestRecord = [
            'startTime' => $this->submitRequestData['startTime'],
            'endTime' => $endTime,
            'mediaType' => $this->submitRequestData['mediaType'],
            'location' => $this->submitRequestData['location'],
            'requestStatus' => MentoringRequestStatus::REQUESTED,
            'Consultant_id' => $this->mentorOne->id,
            'ConsultationSetup_id' => $this->consultationSetupOne->id,
            'Participant_id' => $this->clientParticipant->participant->id,
        ];
        $this->seeInDatabase('MentoringRequest', $mentoringRequestRecord);
    }
    public function test_submitRequest_notUpcomingSchedule_400()
    {
        $this->submitRequestData['startTime'] = (new DateTimeImmutable('-30 minutes'))->format('Y-m-d H:i:s');
        $this->submitRequest();
        $this->seeStatusCode(400);
    }
    public function test_submitRequest_inConflictWithExistingRequest_requested_403()
    {
        $this->mentoringRequestOne->startTime = (new DateTimeImmutable('+550 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestOne->endTime = (new DateTimeImmutable('+615 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestOne->insert($this->connection);
        
        $this->submitRequest();
        $this->seeStatusCode(403);
    }
    public function test_submitRequest_inConflictWithExistingRequest_approved_403()
    {
        $this->mentoringRequestOne->startTime = (new DateTimeImmutable('+550 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestOne->endTime = (new DateTimeImmutable('+615 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::APPROVED_BY_MENTOR;
        $this->mentoringRequestOne->insert($this->connection);
        
        $this->submitRequest();
        $this->seeStatusCode(403);
    }
    public function test_submitRequest_inConflictWithExistingRequest_accepted_403()
    {
        $this->mentoringRequestOne->startTime = (new DateTimeImmutable('+550 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestOne->endTime = (new DateTimeImmutable('+615 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::ACCEPTED_BY_PARTICIPANT;
        $this->mentoringRequestOne->insert($this->connection);
        
        $this->submitRequest();
        $this->seeStatusCode(403);
    }
    public function test_submitRequest_inConflictWithExistingRequest_cancelled_201()
    {
        $this->mentoringRequestOne->startTime = (new DateTimeImmutable('+550 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestOne->endTime = (new DateTimeImmutable('+615 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::CANCELLED;
        $this->mentoringRequestOne->insert($this->connection);
        
        $this->submitRequest();
        $this->seeStatusCode(201);
    }
    public function test_submitRequest_inConflictWithExistingRequest_rejected_201()
    {
        $this->mentoringRequestOne->startTime = (new DateTimeImmutable('+550 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestOne->endTime = (new DateTimeImmutable('+615 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::REJECTED;
        $this->mentoringRequestOne->insert($this->connection);
        
        $this->submitRequest();
        $this->seeStatusCode(201);
    }
    public function test_submitRequest_inConflictWithExistingRequest_offered_201()
    {
        $this->mentoringRequestOne->startTime = (new DateTimeImmutable('+550 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestOne->endTime = (new DateTimeImmutable('+615 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::OFFERED;
        $this->mentoringRequestOne->insert($this->connection);
        
        $this->submitRequest();
        $this->seeStatusCode(201);
    }
    public function test_submitRequest_inConflictWithExistingBookedMentoring_403()
    {
        $mentoringSlot = new RecordOfMentoringSlot($this->mentorOne, $this->consultationSetupOne, 'zzz');
        $mentoringSlot->startTime = new DateTimeImmutable('+550 minutes');
        $mentoringSlot->endTime = new DateTimeImmutable('+615 minutes');
        $mentoringSlot->insert($this->connection);
        $mentoring = new RecordOfMentoring('zzz');
        $bookedMentoring = new RecordOfBookedMentoringSlot($mentoringSlot, $mentoring, $this->clientParticipant->participant);
        $bookedMentoring->insert($this->connection);
        
        $this->submitRequest();
        $this->seeStatusCode(403);
    }
    public function test_submitRequest_inConflictWithExistingBookedMentoring_cancelledBooking_201()
    {
        $mentoringSlot = new RecordOfMentoringSlot($this->mentorOne, $this->consultationSetupOne, 'zzz');
        $mentoringSlot->startTime = new DateTimeImmutable('+550 minutes');
        $mentoringSlot->endTime = new DateTimeImmutable('+615 minutes');
        $mentoringSlot->insert($this->connection);
        $mentoring = new RecordOfMentoring('zzz');
        $bookedMentoring = new RecordOfBookedMentoringSlot($mentoringSlot, $mentoring, $this->clientParticipant->participant);
        $bookedMentoring->cancelled = true;
        $bookedMentoring->insert($this->connection);
        
        $this->submitRequest();
        $this->seeStatusCode(201);
    }
    public function test_submitRequest_unusableMentor_differentProgram_403()
    {
        $program = new RecordOfProgram($this->clientParticipant->client->firm, 'zzz');
        $this->mentorOne->program = $program;
        $this->submitRequest();
        $this->seeStatusCode(403);
    }
    public function test_submitRequest_unusableMentor_inactive_403()
    {
        $this->mentorOne->active = false;
        $this->submitRequest();
        $this->seeStatusCode(403);
    }
    public function test_submitRequest_unusableConsultationSetup_differentProgram_403()
    {
        $program = new RecordOfProgram($this->clientParticipant->client->firm, 'zzz');
        $this->consultationSetupOne->program = $program;
        $this->submitRequest();
        $this->seeStatusCode(403);
    }
    public function test_submitRequest_unusableConsultationSetup_inactive_403()
    {
        $this->consultationSetupOne->removed = true;
        $this->submitRequest();
        $this->seeStatusCode(403);
    }
    public function test_submitRequest_inactiveParticipant_403()
    {
        $this->clientParticipant->participant->active = false;
        $this->submitRequest();
        $this->seeStatusCode(403);
    }
    
    protected function update()
    {
        $this->insertClientParticipantRecord();
        
        $this->mentoringRequestOne->mentor->personnel->insert($this->connection);
        $this->mentoringRequestOne->mentor->insert($this->connection);
        
        $this->mentoringRequestOne->consultationSetup->insert($this->connection);
        
        $this->mentoringRequestOne->insert($this->connection);
        
        $uri = $this->clientParticipantUri . "/mentoring-requests/{$this->mentoringRequestOne->id}/update";
        $this->patch($uri, $this->updateRequest, $this->clientParticipant->client->token);
    }
    public function test_update_200()
    {
        $endTime = (new DateTimeImmutable('+660 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::OFFERED;
        $this->update();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->mentoringRequestOne->id,
            'startTime' => $this->updateRequest['startTime'],
            'endTime' => $endTime,
            'mediaType' => $this->updateRequest['mediaType'],
            'location' => $this->updateRequest['location'],
            'requestStatus' => MentoringRequestStatus::DISPLAY_VALUE[MentoringRequestStatus::REQUESTED],
            'mentor' => [
                'id' => $this->mentorOne->id,
                'personnel' => [
                    'id' => $this->mentorOne->personnel->id,
                    'name' => $this->mentorOne->personnel->getFullName(),
                ],
            ],
            'consultationSetup' => [
                'id' => $this->consultationSetupOne->id,
                'name' => $this->consultationSetupOne->name,
            ],
        ];
        $this->seeJsonContains($response);
        
        $mentoringRequestRecord = [
            'id' => $this->mentoringRequestOne->id,
            'startTime' => $this->updateRequest['startTime'],
            'endTime' => $endTime,
            'mediaType' => $this->updateRequest['mediaType'],
            'location' => $this->updateRequest['location'],
            'requestStatus' => MentoringRequestStatus::REQUESTED,
            'Consultant_id' => $this->mentorOne->id,
            'ConsultationSetup_id' => $this->consultationSetupOne->id,
            'Participant_id' => $this->clientParticipant->participant->id,
        ];
        $this->seeInDatabase('MentoringRequest', $mentoringRequestRecord);
    }
    public function test_update_newScheduleNotAnUpcoming_400()
    {
        $this->updateRequest['startTime'] = (new DateTimeImmutable('-30 minutes'))->format('Y-m-d H:i:s');
        $this->update();
        $this->seeStatusCode(400);
    }
    public function test_update_updatingNonUpcomingSchedule_403()
    {
        $this->mentoringRequestOne->startTime = (new DateTimeImmutable('-30 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestOne->endTime = (new DateTimeImmutable('+30 minutes'))->format('Y-m-d H:i:s');
        $this->update();
        $this->seeStatusCode(403);
    }
    public function test_update_inConflictWithOtherRequest_requested_403()
    {
        $this->mentoringRequestTwo_offered->startTime = (new DateTimeImmutable('+550 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestTwo_offered->endTime = (new DateTimeImmutable('+615 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestTwo_offered->requestStatus = MentoringRequestStatus::REQUESTED;
        $this->mentoringRequestTwo_offered->insert($this->connection);
        $this->update();
        $this->seeStatusCode(403);
    }
    public function test_update_inConflictWithOtherRequest_approved_403()
    {
        $this->mentoringRequestTwo_offered->startTime = (new DateTimeImmutable('+550 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestTwo_offered->endTime = (new DateTimeImmutable('+615 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestTwo_offered->requestStatus = MentoringRequestStatus::APPROVED_BY_MENTOR;
        $this->mentoringRequestTwo_offered->insert($this->connection);
        $this->update();
        $this->seeStatusCode(403);
    }
    public function test_update_inConflictWithOtherRequest_accepted_403()
    {
        $this->mentoringRequestTwo_offered->startTime = (new DateTimeImmutable('+550 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestTwo_offered->endTime = (new DateTimeImmutable('+615 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestTwo_offered->requestStatus = MentoringRequestStatus::ACCEPTED_BY_PARTICIPANT;
        $this->mentoringRequestTwo_offered->insert($this->connection);
        $this->update();
        $this->seeStatusCode(403);
    }
    public function test_update_inConflictWithOtherRequest_offered_200()
    {
        $this->mentoringRequestTwo_offered->startTime = (new DateTimeImmutable('+550 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestTwo_offered->endTime = (new DateTimeImmutable('+615 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestTwo_offered->requestStatus = MentoringRequestStatus::OFFERED;
        $this->mentoringRequestTwo_offered->insert($this->connection);
        $this->update();
        $this->seeStatusCode(200);
    }
    public function test_update_inConflictWithOtherRequest_cancelled_200()
    {
        $this->mentoringRequestTwo_offered->startTime = (new DateTimeImmutable('+550 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestTwo_offered->endTime = (new DateTimeImmutable('+615 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestTwo_offered->requestStatus = MentoringRequestStatus::CANCELLED;
        $this->mentoringRequestTwo_offered->insert($this->connection);
        $this->update();
        $this->seeStatusCode(200);
    }
    public function test_update_inConflictWithOtherRequest_rejected_200()
    {
        $this->mentoringRequestTwo_offered->startTime = (new DateTimeImmutable('+550 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestTwo_offered->endTime = (new DateTimeImmutable('+615 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestTwo_offered->requestStatus = MentoringRequestStatus::REJECTED;
        $this->mentoringRequestTwo_offered->insert($this->connection);
        $this->update();
        $this->seeStatusCode(200);
    }
    public function test_update_inConflictWithExistingBookedMentoring_403()
    {
        $mentoringSlot = new RecordOfMentoringSlot($this->mentorOne, $this->consultationSetupOne, 'zzz');
        $mentoringSlot->startTime = new DateTimeImmutable('+550 minutes');
        $mentoringSlot->endTime = new DateTimeImmutable('+615 minutes');
        $mentoringSlot->insert($this->connection);
        $mentoring = new RecordOfMentoring('zzz');
        $bookedMentoring = new RecordOfBookedMentoringSlot($mentoringSlot, $mentoring, $this->clientParticipant->participant);
        $bookedMentoring->insert($this->connection);
        
        $this->update();
        $this->seeStatusCode(403);
    }
    public function test_update_inConflictWithCancelledBookedMentoring_200()
    {
        $mentoringSlot = new RecordOfMentoringSlot($this->mentorOne, $this->consultationSetupOne, 'zzz');
        $mentoringSlot->startTime = new DateTimeImmutable('+550 minutes');
        $mentoringSlot->endTime = new DateTimeImmutable('+615 minutes');
        $mentoringSlot->insert($this->connection);
        $mentoring = new RecordOfMentoring('zzz');
        $bookedMentoring = new RecordOfBookedMentoringSlot($mentoringSlot, $mentoring, $this->clientParticipant->participant);
        $bookedMentoring->cancelled = true;
        $bookedMentoring->insert($this->connection);
        
        $this->update();
        $this->seeStatusCode(200);
    }
    public function test_update_unmanagedMentoringRequest_belongsToOtherParticipant_403()
    {
        $program = $this->clientParticipant->participant->program;
        $participant = new RecordOfParticipant($program, 'zzz');
        $participant->insert($this->connection);
        $this->mentoringRequestOne->participant = $participant;
        
        $this->update();
        $this->seeStatusCode(403);
    }
    public function test_update_unmanagedMentoringRequest_inactiveParticipant_403()
    {
        $this->clientParticipant->participant->active = false;
        $this->update();
        $this->seeStatusCode(403);
    }
    
    protected function cancel()
    {
        $this->insertClientParticipantRecord();
        
        $this->mentoringRequestOne->mentor->personnel->insert($this->connection);
        $this->mentoringRequestOne->mentor->insert($this->connection);
        
        $this->mentoringRequestOne->consultationSetup->insert($this->connection);
        
        $this->mentoringRequestOne->insert($this->connection);
        
        $uri = $this->clientParticipantUri . "/mentoring-requests/{$this->mentoringRequestOne->id}";
        $this->delete($uri, [], $this->clientParticipant->client->token);
    }
    public function test_cancel_200()
    {
        $this->cancel();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->mentoringRequestOne->id,
            'requestStatus' => MentoringRequestStatus::DISPLAY_VALUE[MentoringRequestStatus::CANCELLED],
        ];
        $this->seeJsonContains($response);
        
        $mentoringRequestRecord = [
            'id' => $this->mentoringRequestOne->id,
            'requestStatus' => MentoringRequestStatus::CANCELLED,
        ];
        $this->seeInDatabase('MentoringRequest', $mentoringRequestRecord);
    }
    public function test_cancel_concludedRequest_cancelled_403()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::CANCELLED;
        $this->cancel();
        $this->seeStatusCode(403);
    }
    public function test_cancel_concludedRequest_rejected_403()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::REJECTED;
        $this->cancel();
        $this->seeStatusCode(403);
    }
    public function test_cancel_concludedRequest_accepted_403()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::ACCEPTED_BY_PARTICIPANT;
        $this->cancel();
        $this->seeStatusCode(403);
    }
    public function test_cancel_concludedRequest_approved_403()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::APPROVED_BY_MENTOR;
        $this->cancel();
        $this->seeStatusCode(403);
    }
    public function test_cancel_unmanagedMentoringRequest_belongsToOtherParticipant_403()
    {
        $program = $this->clientParticipant->participant->program;
        $participant = new RecordOfParticipant($program, 'zzz');
        $participant->insert($this->connection);
        $this->mentoringRequestOne->participant = $participant;
        
        $this->cancel();
        $this->seeStatusCode(403);
    }
    public function test_cancel_inactiveParticipant_403()
    {
        $this->clientParticipant->participant->active = false;
        $this->cancel();
        $this->seeStatusCode(403);
    }
    
    protected function accept()
    {
        $this->insertClientParticipantRecord();
        
        $this->mentoringRequestTwo_offered->mentor->personnel->insert($this->connection);
        $this->mentoringRequestTwo_offered->mentor->insert($this->connection);
        
        $this->mentoringRequestTwo_offered->consultationSetup->insert($this->connection);
        
        $this->mentoringRequestTwo_offered->insert($this->connection);
        
        $uri = $this->clientParticipantUri . "/mentoring-requests/{$this->mentoringRequestTwo_offered->id}/accept";
        $this->patch($uri, [], $this->clientParticipant->client->token);
    }
    public function test_accept_200()
    {
        $this->accept();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->mentoringRequestTwo_offered->id,
            'requestStatus' => MentoringRequestStatus::DISPLAY_VALUE[MentoringRequestStatus::ACCEPTED_BY_PARTICIPANT],
        ];
        $this->seeJsonContains($response);
        
        $mentoringRequestRecord = [
            'id' => $this->mentoringRequestTwo_offered->id,
            'requestStatus' => MentoringRequestStatus::ACCEPTED_BY_PARTICIPANT,
        ];
        $this->seeInDatabase('MentoringRequest', $mentoringRequestRecord);
    }
    public function test_accept_appendNegotiatedMentoring_200()
    {
        $this->accept();
        $this->seeStatusCode(200);
        
        $negotiatedMentoringRecord = [
            'MentoringRequest_id' => $this->mentoringRequestTwo_offered->id,
        ];
        $this->seeInDatabase('NegotiatedMentoring', $negotiatedMentoringRecord);
    }
    public function test_accept_notOfferedMentoringRequest_403()
    {
        $this->mentoringRequestTwo_offered->requestStatus = MentoringRequestStatus::REQUESTED;
        $this->accept();
        $this->seeStatusCode(403);
    }
    public function test_accept_notUpcomingSchedule_403()
    {
        $this->mentoringRequestTwo_offered->startTime = (new DateTimeImmutable('-30 minutes'))->format('Y-m-d H:i:s');
        $this->mentoringRequestTwo_offered->endTime = (new DateTimeImmutable('+30 minutes'))->format('Y-m-d H:i:s');
        $this->accept();
        $this->seeStatusCode(403);
    }
    public function test_accept_inConflictWithExistingRequest_requested_403()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::REQUESTED;
        $this->mentoringRequestOne->insert($this->connection);
        $this->accept();
        $this->seeStatusCode(403);
    }
    public function test_accept_inConflictWithExistingRequest_accepted_403()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::ACCEPTED_BY_PARTICIPANT;
        $this->mentoringRequestOne->insert($this->connection);
        $this->accept();
        $this->seeStatusCode(403);
    }
    public function test_accept_inConflictWithExistingRequest_approved_403()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::APPROVED_BY_MENTOR;
        $this->mentoringRequestOne->insert($this->connection);
        $this->accept();
        $this->seeStatusCode(403);
    }
    public function test_accept_inConflictWithExistingRequest_offered_200()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::OFFERED;
        $this->mentoringRequestOne->insert($this->connection);
        $this->accept();
        $this->seeStatusCode(200);
    }
    public function test_accept_inConflictWithExistingRequest_cancelled_200()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::CANCELLED;
        $this->mentoringRequestOne->insert($this->connection);
        $this->accept();
        $this->seeStatusCode(200);
    }
    public function test_accept_inConflictWithExistingRequest_rejected_200()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::REJECTED;
        $this->mentoringRequestOne->insert($this->connection);
        $this->accept();
        $this->seeStatusCode(200);
    }
    public function test_accept_inConflictWithExistingBookedMentoring_403()
    {
        $mentoringSlot = new RecordOfMentoringSlot($this->mentorOne, $this->consultationSetupOne, 'zzz');
        $mentoringSlot->startTime = new DateTimeImmutable($this->mentoringRequestTwo_offered->startTime);
        $mentoringSlot->endTime = new DateTimeImmutable($this->mentoringRequestTwo_offered->endTime);
        $mentoringSlot->insert($this->connection);
        $mentoring = new RecordOfMentoring('zzz');
        $bookedMentoring = new RecordOfBookedMentoringSlot($mentoringSlot, $mentoring, $this->clientParticipant->participant);
        $bookedMentoring->insert($this->connection);
        
        $this->accept();
        $this->seeStatusCode(403);
    }
    public function test_accept_inConflictWithCancelledBookedMentoring_200()
    {
        $mentoringSlot = new RecordOfMentoringSlot($this->mentorOne, $this->consultationSetupOne, 'zzz');
        $mentoringSlot->startTime = new DateTimeImmutable($this->mentoringRequestTwo_offered->startTime);
        $mentoringSlot->endTime = new DateTimeImmutable($this->mentoringRequestTwo_offered->endTime);
        $mentoringSlot->insert($this->connection);
        $mentoring = new RecordOfMentoring('zzz');
        $bookedMentoring = new RecordOfBookedMentoringSlot($mentoringSlot, $mentoring, $this->clientParticipant->participant);
        $bookedMentoring->cancelled = true;
        $bookedMentoring->insert($this->connection);
        
        $this->accept();
        $this->seeStatusCode(200);
    }
    public function test_accept_unmanagedMentoringRequest_belongsToOtherParticipant_403()
    {
        $program = $this->clientParticipant->participant->program;
        $participant = new RecordOfParticipant($program, 'zzz');
        $participant->insert($this->connection);
        $this->mentoringRequestTwo_offered->participant = $participant;
        
        $this->accept();
        $this->seeStatusCode(403);
    }
    public function test_accept_inactiveParticipant_403()
    {
        $this->clientParticipant->participant->active = false;
        $this->accept();
        $this->seeStatusCode(403);
    }
    
    protected function show()
    {
        $this->insertClientParticipantRecord();
        
        $this->mentoringRequestOne->mentor->personnel->insert($this->connection);
        $this->mentoringRequestOne->mentor->insert($this->connection);
        
        $this->mentoringRequestOne->consultationSetup->insert($this->connection);
        
        $this->mentoringRequestOne->insert($this->connection);
        
        $uri = $this->clientParticipantUri . "/mentoring-requests/{$this->mentoringRequestOne->id}";
        $this->get($uri, $this->clientParticipant->client->token);
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
            'mentor' => [
                'id' => $this->mentorOne->id,
                'personnel' => [
                    'id' => $this->mentorOne->personnel->id,
                    'name' => $this->mentorOne->personnel->getFullName(),
                ],
            ],
            'consultationSetup' => [
                'id' => $this->consultationSetupOne->id,
                'name' => $this->consultationSetupOne->name,
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_show_unmanagedMentoringRequest_belongsToOtherParticipant_404()
    {
        $program = $this->clientParticipant->participant->program;
        $participant = new RecordOfParticipant($program, 'zzz');
        $participant->insert($this->connection);
        $this->mentoringRequestOne->participant = $participant;
        
        $this->show();
        $this->seeStatusCode(404);
    }
    public function test_show_inactiveParticipant_403()
    {
        $this->clientParticipant->participant->active = false;
        $this->show();
        $this->seeStatusCode(403);
    }
}
