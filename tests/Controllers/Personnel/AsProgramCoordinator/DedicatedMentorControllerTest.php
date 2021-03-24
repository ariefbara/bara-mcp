<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator;

use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfDedicatedMentor;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;

class DedicatedMentorControllerTest extends AsProgramCoordinatorTestCase
{
    protected $dedicatedMentorOne;
    protected $dedicatedMentorTwo;
    protected $clientParticipant;
    protected $clientParticipantTwo;
    protected $consultant;
    protected $assignRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('DedicatedMentor')->truncate();
        
        $program = $this->coordinator->program;
        $firm = $program->firm;
        
        $client = new RecordOfClient($firm, '99');
        $clientTwo = new RecordOfClient($firm, '2');
        
        $participant = new RecordOfParticipant($program, '99');
        $participantTwo = new RecordOfParticipant($program, '2');
        
        $this->clientParticipant = new RecordOfClientParticipant($client, $participant);
        $this->clientParticipantTwo = new RecordOfClientParticipant($clientTwo, $participantTwo);
        
        $personnel = new RecordOfPersonnel($firm, '99');
        $personnelOne = new RecordOfPersonnel($firm, '1');
        $personnelTwo = new RecordOfPersonnel($firm, '2');
        
        $this->consultant = new RecordOfConsultant($program, $personnel, '99');
        $consultantOne = new RecordOfConsultant($program, $personnelOne, '1');
        $consultantTwo = new RecordOfConsultant($program, $personnelTwo, '2');
        
        $this->dedicatedMentorOne = new RecordOfDedicatedMentor($participant, $consultantOne, '1');
        $this->dedicatedMentorTwo = new RecordOfDedicatedMentor($participant, $consultantTwo, '2');
        
        $this->assignRequest = [
            'consultantId' => $this->consultant->id,
        ];
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('DedicatedMentor')->truncate();
    }
    
    protected function executeAssign()
    {
        $this->clientParticipant->client->insert($this->connection);
        $this->clientParticipant->participant->insert($this->connection);
        $this->clientParticipant->insert($this->connection);
        $this->consultant->personnel->insert($this->connection);
        $this->consultant->insert($this->connection);
        $uri = $this->asProgramCoordinatorUri . "/participants/{$this->clientParticipant->participant->id}/dedicated-mentors";
        $this->post($uri, $this->assignRequest, $this->coordinator->personnel->token);
    }
    public function test_assign_200()
    {
        $this->executeAssign();
        $this->seeStatusCode(200);
        $response = [
            'modifiedTime' => $this->currentTimeString(),
            'cancelled' => false,
            'consultant' => [
                'id' => $this->consultant->id,
                'personnel' => [
                    'id' => $this->consultant->personnel->id,
                    'name' => $this->consultant->personnel->getFullName(),
                ],
            ],
        ];
        $this->seeJsonContains($response);
        
        $dedicatedMentorEntry = [
            'Participant_id' => $this->clientParticipant->participant->id,
            'Consultant_id' => $this->consultant->id,
            'modifiedTime' => $this->currentTimeString(),
            'cancelled' => false,
        ];
        $this->seeInDatabase('DedicatedMentor', $dedicatedMentorEntry);
    }
    public function test_assign_inactiveConsultant_403()
    {
        $this->consultant->active = false;
        $this->executeAssign();
        $this->seeStatusCode(403);
    }
    public function test_assign_inactiveParticipant_403()
    {
        $this->clientParticipant->participant->active = false;
        $this->executeAssign();
        $this->seeStatusCode(403);
    }
    public function test_assign_consultantAlreadyAssignAsDedicatedMentorOfSameParticipant_200()
    {
        $this->dedicatedMentorOne->consultant = $this->consultant;
        $this->dedicatedMentorOne->insert($this->connection);
        $this->executeAssign();
        $this->seeStatusCode(200);
        
        $dedicatedMentorEntry = [
            'id' => $this->dedicatedMentorOne->id,
            'modifiedTime' => $this->dedicatedMentorOne->modifiedTime,
        ];
        $this->seeInDatabase('DedicatedMentor', $dedicatedMentorEntry);
    }
    public function test_assign_assignmentToSameParticipantAlreadyCancelled_reassign()
    {
        $this->dedicatedMentorOne->consultant = $this->consultant;
        $this->dedicatedMentorOne->cancelled = true;
        $this->dedicatedMentorOne->insert($this->connection);
        $this->executeAssign();
        $this->seeStatusCode(200);
        
        $dedicatedMentorEntry = [
            'id' => $this->dedicatedMentorOne->id,
            'cancelled' => false,
            'modifiedTime' => $this->currentTimeString(),
        ];
        $this->seeInDatabase('DedicatedMentor', $dedicatedMentorEntry);
    }
    public function test_reassign_inactiveConsultant_403()
    {
        $this->consultant->active = false;
        $this->dedicatedMentorOne->consultant = $this->consultant;
        $this->dedicatedMentorOne->cancelled = true;
        $this->dedicatedMentorOne->insert($this->connection);
        $this->executeAssign();
        $this->seeStatusCode(403);
    }
    public function test_assign_inactiveCoordinator_403()
    {
        $this->connection->table('Coordinator')->truncate();
        $this->coordinator->active = false;
        $this->coordinator->insert($this->connection);
        $this->executeAssign();
        $this->seeStatusCode(403);
    }
    
    protected function executeCancel()
    {
        $this->clientParticipant->client->insert($this->connection);
        $this->clientParticipant->insert($this->connection);
        $this->dedicatedMentorOne->participant->insert($this->connection);
        $this->dedicatedMentorOne->consultant->personnel->insert($this->connection);
        $this->dedicatedMentorOne->consultant->insert($this->connection);
        $this->dedicatedMentorOne->insert($this->connection);
        $uri = $this->asProgramCoordinatorUri . "/dedicated-mentors/{$this->dedicatedMentorOne->id}";
        $this->delete($uri, [], $this->coordinator->personnel->token);
    }
    public function test_cancel_200()
    {
$this->disableExceptionHandling();
        $this->executeCancel();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->dedicatedMentorOne->id,
            'modifiedTime' => $this->currentTimeString(),
            'cancelled' => true,
            'consultant' => [
                'id' => $this->dedicatedMentorOne->consultant->id,
                'personnel' => [
                    'id' => $this->dedicatedMentorOne->consultant->personnel->id,
                    'name' => $this->dedicatedMentorOne->consultant->personnel->getFullName(),
                ],
            ],
        ];
        $this->seeJsonContains($response);
        
        $dedicatedMentorEntry = [
            'id' => $this->dedicatedMentorOne->id,
            'cancelled' => true,
        ];
        $this->seeInDatabase('DedicatedMentor', $dedicatedMentorEntry);
    }
    public function test_cancel_alreadyCancelled_403()
    {
        $this->dedicatedMentorOne->cancelled = true;
        $this->executeCancel();
        $this->seeStatusCode(403);
    }
    public function test_cancel_inactiveCoordinator_403()
    {
        $this->connection->table('Coordinator')->truncate();
        $this->coordinator->active = false;
        $this->coordinator->insert($this->connection);
        $this->executeCancel();
        $this->seeStatusCode(403);
    }
    
    protected function executeShow()
    {
        $this->clientParticipant->client->insert($this->connection);
        $this->clientParticipant->participant->insert($this->connection);
        $this->clientParticipant->insert($this->connection);
        $this->dedicatedMentorOne->consultant->personnel->insert($this->connection);
        $this->dedicatedMentorOne->consultant->insert($this->connection);
        $this->dedicatedMentorOne->insert($this->connection);
        $uri = $this->asProgramCoordinatorUri . "/dedicated-mentors/{$this->dedicatedMentorOne->id}";
        $this->get($uri, $this->coordinator->personnel->token);
    }
    public function test_show_200()
    {
        $this->executeShow();
        $this->seeStatusCode(200);
        $response = [
            'id' => $this->dedicatedMentorOne->id,
            'modifiedTime' => $this->dedicatedMentorOne->modifiedTime,
            'cancelled' => $this->dedicatedMentorOne->cancelled,
            'consultant' => [
                'id' => $this->dedicatedMentorOne->consultant->id,
                'personnel' => [
                    'id' => $this->dedicatedMentorOne->consultant->personnel->id,
                    'name' => $this->dedicatedMentorOne->consultant->personnel->getFullName(),
                ],
            ],
            'participant' => [
                'id' => $this->dedicatedMentorOne->participant->id,
                'name' => $this->clientParticipant->client->getFullName(),
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_show_inactiveCoordinator_403()
    {
        $this->connection->table('Coordinator')->truncate();
        $this->coordinator->active = false;
        $this->coordinator->insert($this->connection);
        $this->executeShow();
        $this->seeStatusCode(403);
    }
    
    protected function executeShowAllBelongsToParticipant(?string $uri = null)
    {
        $this->clientParticipant->client->insert($this->connection);
        $this->clientParticipant->participant->insert($this->connection);
        $this->clientParticipant->insert($this->connection);
        
        $this->dedicatedMentorOne->consultant->personnel->insert($this->connection);
        $this->dedicatedMentorOne->consultant->insert($this->connection);
        $this->dedicatedMentorOne->insert($this->connection);
        
        $this->dedicatedMentorTwo->consultant->personnel->insert($this->connection);
        $this->dedicatedMentorTwo->consultant->insert($this->connection);
        $this->dedicatedMentorTwo->insert($this->connection);
        
        $uri = $uri ?? $this->asProgramCoordinatorUri . "/participants/{$this->clientParticipant->participant->id}/dedicated-mentors";
        $this->get($uri, $this->coordinator->personnel->token);
    }
    public function test_showAllBelongsToParticipant_200()
    {
        $this->executeShowAllBelongsToParticipant();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 2];
        $this->seeJsonContains($totalResponse);
        
        $dedicatedMentorOneResponse = [
            'id' => $this->dedicatedMentorOne->id,
            'modifiedTime' => $this->dedicatedMentorOne->modifiedTime,
            'cancelled' => $this->dedicatedMentorOne->cancelled,
            'consultant' => [
                'id' => $this->dedicatedMentorOne->consultant->id,
                'personnel' => [
                    'id' => $this->dedicatedMentorOne->consultant->personnel->id,
                    'name' => $this->dedicatedMentorOne->consultant->personnel->getFullName(),
                ],
            ],
        ];
        $this->seeJsonContains($dedicatedMentorOneResponse);
        
        $dedicatedMentorTwoResponse = [
            'id' => $this->dedicatedMentorTwo->id,
            'modifiedTime' => $this->dedicatedMentorTwo->modifiedTime,
            'cancelled' => $this->dedicatedMentorTwo->cancelled,
            'consultant' => [
                'id' => $this->dedicatedMentorTwo->consultant->id,
                'personnel' => [
                    'id' => $this->dedicatedMentorTwo->consultant->personnel->id,
                    'name' => $this->dedicatedMentorTwo->consultant->personnel->getFullName(),
                ],
            ],
        ];
        $this->seeJsonContains($dedicatedMentorTwoResponse);
    }
    public function test_showAllBelongsToParticipant_useCancelledStatusFilter_200()
    {
        $this->dedicatedMentorTwo->cancelled = true;
        $uri = $this->asProgramCoordinatorUri . "/participants/{$this->clientParticipant->participant->id}/dedicated-mentors?cancelledStatus=false";
        $this->executeShowAllBelongsToParticipant($uri);
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 1];
        $this->seeJsonContains($totalResponse);
        
        $dedicatedMentorOneResponse = [
            'id' => $this->dedicatedMentorOne->id,
        ];
        $this->seeJsonContains($dedicatedMentorOneResponse);
    }
    public function test_showAllBelongsToParticipant_inactiveCoordinator_403()
    {
        $this->connection->table('Coordinator')->truncate();
        $this->coordinator->active = false;
        $this->coordinator->insert($this->connection);
        $this->executeShowAllBelongsToParticipant();
        $this->seeStatusCode(403);
    }
    
    protected function executeShowAllBelongsToConsultant(?string $uri = null)
    {
        $this->clientParticipant->client->insert($this->connection);
        $this->clientParticipant->participant->insert($this->connection);
        $this->clientParticipant->insert($this->connection);
        
        $this->clientParticipantTwo->client->insert($this->connection);
        $this->clientParticipantTwo->participant->insert($this->connection);
        $this->clientParticipantTwo->insert($this->connection);
        
        $this->consultant->personnel->insert($this->connection);
        $this->consultant->insert($this->connection);
        
        $this->dedicatedMentorOne->consultant = $this->consultant;
        $this->dedicatedMentorOne->insert($this->connection);
        
        $this->dedicatedMentorTwo->consultant = $this->consultant;
        $this->dedicatedMentorTwo->participant = $this->clientParticipantTwo->participant;
        $this->dedicatedMentorTwo->insert($this->connection);
        
        $uri = $uri ?? $this->asProgramCoordinatorUri . "/consultants/{$this->consultant->id}/dedicated-mentors";
        $this->get($uri, $this->coordinator->personnel->token);
    }
    public function test_showAllBelongsToConsultant_200()
    {
        $this->executeShowAllBelongsToConsultant();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 2];
        $this->seeJsonContains($totalResponse);
        
        $dedicatedMentorOneResponse = [
            'id' => $this->dedicatedMentorOne->id,
            'modifiedTime' => $this->dedicatedMentorOne->modifiedTime,
            'cancelled' => $this->dedicatedMentorOne->cancelled,
            'participant' => [
                'id' => $this->dedicatedMentorOne->participant->id,
                'name' => $this->clientParticipant->client->getFullName(),
            ],
        ];
        $this->seeJsonContains($dedicatedMentorOneResponse);
        
        $dedicatedMentorTwoResponse = [
            'id' => $this->dedicatedMentorTwo->id,
            'modifiedTime' => $this->dedicatedMentorTwo->modifiedTime,
            'cancelled' => $this->dedicatedMentorTwo->cancelled,
            'participant' => [
                'id' => $this->dedicatedMentorTwo->participant->id,
                'name' => $this->clientParticipantTwo->client->getFullName(),
            ],
        ];
        $this->seeJsonContains($dedicatedMentorTwoResponse);
    }
    public function test_showAllBelongsToConsultant_useCancelledFilter_200()
    {
        $this->dedicatedMentorTwo->cancelled = true;
        $uri = $this->asProgramCoordinatorUri . "/consultants/{$this->consultant->id}/dedicated-mentors?cancelledStatus=false";
        $this->executeShowAllBelongsToConsultant($uri);
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 1];
        $this->seeJsonContains($totalResponse);
        
        $dedicatedMentorOneResponse = [
            'id' => $this->dedicatedMentorOne->id,
            'modifiedTime' => $this->dedicatedMentorOne->modifiedTime,
            'cancelled' => $this->dedicatedMentorOne->cancelled,
            'participant' => [
                'id' => $this->dedicatedMentorOne->participant->id,
                'name' => $this->clientParticipant->client->getFullName(),
            ],
        ];
        $this->seeJsonContains($dedicatedMentorOneResponse);
    }
    public function test_showAllBelongsToConsultant_inactiveCoordinator_403()
    {
        $this->connection->table('Coordinator')->truncate();
        $this->coordinator->active = false;
        $this->coordinator->insert($this->connection);
        $this->executeShowAllBelongsToConsultant();
        $this->seeStatusCode(403);
    }
}
