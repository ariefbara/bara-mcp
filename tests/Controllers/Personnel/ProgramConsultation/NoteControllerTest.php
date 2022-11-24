<?php

namespace Tests\Controllers\Personnel\ProgramConsultation;

use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfConsultantNote;
use Tests\Controllers\RecordPreparation\Firm\Program\Coordinator\RecordOfCoordinatorNote;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfParticipantNote;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Shared\RecordOfNote;

class NoteControllerTest extends ExtendedConsultantTestCase
{
    protected $clientParticipantOne;
    protected $consultantNoteOne;
    protected $coordinatorNoteOne;
    protected $participantNoteOne;
    //
    protected $submitRequest;
    protected $updateRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Coordinator')->truncate();
        //
        $this->connection->table('Note')->truncate();
        $this->connection->table('CoordinatorNote')->truncate();
        $this->connection->table('ConsultantNote')->truncate();
        $this->connection->table('ParticipantNote')->truncate();
        
        $program = $this->consultant->program;
        $firm = $program->firm;
        
        $clientOne = new RecordOfClient($firm, 1);
        
        $personnelOne = new RecordOfPersonnel($firm, 1);
        
        $participantOne = new RecordOfParticipant($program, 1);
        
        $this->clientParticipantOne = new RecordOfClientParticipant($clientOne, $participantOne);
        
        $coordinatorOne = new RecordOfCoordinator($program, $personnelOne, 1);
        
        $noteOne = new RecordOfNote(1);
        $noteTwo = new RecordOfNote(2);
        $noteThree = new RecordOfNote(3);
        
        $this->consultantNoteOne = new RecordOfConsultantNote($noteOne, $this->consultant, $this->clientParticipantOne->participant);
        
        $this->coordinatorNoteOne = new RecordOfCoordinatorNote($noteTwo, $coordinatorOne, $this->clientParticipantOne->participant);
        
        $this->participantNoteOne = new RecordOfParticipantNote($noteThree, $this->clientParticipantOne->participant);
        
        $this->submitRequest = [
            'participantId' => $this->clientParticipantOne->participant->id,
            'name' => 'new note name',
            'description' => 'new note description',
            'viewableByParticipant' => true,
        ];
        
        $this->updateRequest = [
            'name' => 'udpated note name',
            'description' => 'updated note description',
        ];
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Coordinator')->truncate();
        //
        $this->connection->table('Note')->truncate();
        $this->connection->table('CoordinatorNote')->truncate();
        $this->connection->table('ConsultantNote')->truncate();
        $this->connection->table('ParticipantNote')->truncate();
    }
    
    protected function submit(): void
    {
        $this->persistConsultantDependency();
        
        $this->clientParticipantOne->client->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        
        $uri = $this->consultantUri . "/notes";
//echo $uri;
//echo json_encode($this->submitRequest);
        $this->post($uri, $this->submitRequest, $this->personnel->token);
    }
    public function test_submit_200()
    {
$this->disableExceptionHandling();
        $this->submit();
        $this->seeStatusCode(201);
        
        $response = [
//'printme',
            'consultant' => [
                'id' => $this->consultant->id,
                'personnel' => [
                    'id' => $this->consultant->personnel->id,
                    'name' => $this->consultant->personnel->getFullName(),
                ],
            ],
            'participant' => [
                'id' => $this->clientParticipantOne->participant->id,
                'client' => [
                    'id' => $this->clientParticipantOne->client->id,
                    'name' => $this->clientParticipantOne->client->getFullName(),
                ],
                'team' => null,
                'user' => null,
            ],
            'viewableByParticipant' => $this->submitRequest['viewableByParticipant'],
            'name' => $this->submitRequest['name'],
            'description' => $this->submitRequest['description'],
            'createdTime' => $this->currentTimeString(),
            'modifiedTime' => $this->currentTimeString(),
        ];
        $this->seeJsonContains($response);
        
        $consultantNoteEntry = [
            'Consultant_id' => $this->consultant->id,
            'Participant_id' => $this->clientParticipantOne->participant->id,
            'viewableByParticipant' => $this->submitRequest['viewableByParticipant'],
        ];
        $this->seeInDatabase('ConsultantNote', $consultantNoteEntry);
        
        $noteEntry = [
            'name' => $this->submitRequest['name'],
            'description' => $this->submitRequest['description'],
            'createdTime' => $this->currentTimeString(),
            'modifiedTime' => $this->currentTimeString(),
            'removed' => false,
        ];
        $this->seeInDatabase('Note', $noteEntry);
    }
    public function test_submit_emptyName_400()
    {
        $this->submitRequest['name'] = '';
        
        $this->submit();
        $this->seeStatusCode(400);
    }
    public function test_submit_unusableParticipant_belongsToOtherProgram_403()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        
        $this->clientParticipantOne->participant->program = $otherProgram;
        
        $this->submit();
        $this->seeStatusCode(403);
    }
    public function test_submit_inactiveConsultant_403()
    {
        $this->consultant->active = false;
        
        $this->submit();
        $this->seeStatusCode(403);
    }
    
    //
    protected function update()
    {
        $this->persistConsultantDependency();
        
        $this->clientParticipantOne->client->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        
        $this->consultantNoteOne->insert($this->connection);
        
        $uri = $this->consultantUri . "/notes/{$this->consultantNoteOne->id}/update";
//echo $uri;
//echo (json_encode($this->updateRequest));
        $this->patch($uri, $this->updateRequest, $this->personnel->token);
    }
    public function test_update_200()
    {
$this->disableExceptionHandling();
        $this->update();
        $this->seeStatusCode(200);
        
        $response = [
//'printme',
            'id' => $this->consultantNoteOne->id,
            'consultant' => [
                'id' => $this->consultant->id,
                'personnel' => [
                    'id' => $this->consultant->personnel->id,
                    'name' => $this->consultant->personnel->getFullName(),
                ],
            ],
            'participant' => [
                'id' => $this->clientParticipantOne->participant->id,
                'client' => [
                    'id' => $this->clientParticipantOne->client->id,
                    'name' => $this->clientParticipantOne->client->getFullName(),
                ],
                'team' => null,
                'user' => null,
            ],
            'viewableByParticipant' => $this->consultantNoteOne->viewableByParticipant,
            'name' => $this->updateRequest['name'],
            'description' => $this->updateRequest['description'],
            'createdTime' => $this->consultantNoteOne->note->createdTime,
            'modifiedTime' => $this->currentTimeString(),
        ];
        $this->seeJsonContains($response);
        
        $noteEntry = [
            'id' => $this->consultantNoteOne->id,
            'name' => $this->updateRequest['name'],
            'description' => $this->updateRequest['description'],
            'createdTime' => $this->consultantNoteOne->note->createdTime,
            'modifiedTime' => $this->currentTimeString(),
        ];
    }
    public function test_update_noChange_keepModifiedTime()
    {
        $this->updateRequest['name'] = $this->consultantNoteOne->note->name;
        $this->updateRequest['description'] = $this->consultantNoteOne->note->description;
        
        $this->update();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['modifiedTime' => $this->consultantNoteOne->note->modifiedTime]);
        
        $this->seeInDatabase('Note', ['modifiedTime' => $this->consultantNoteOne->note->modifiedTime]);
    }
    public function test_update_changeOccured_inName_udpateModifiedTime()
    {
        $this->updateRequest['description'] = $this->consultantNoteOne->note->description;
        
        $this->update();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['modifiedTime' => $this->currentTimeString()]);
        
        $this->seeInDatabase('Note', ['modifiedTime' => $this->currentTimeString()]);
    }
    public function test_update_changeOccured_inDescription_udpateModifiedTime()
    {
        $this->updateRequest['name'] = $this->consultantNoteOne->note->name;
        
        $this->update();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['modifiedTime' => $this->currentTimeString()]);
        
        $this->seeInDatabase('Note', ['modifiedTime' => $this->currentTimeString()]);
    }
    public function test_update_emptyName_400()
    {
        $this->updateRequest['name'] = '';
        
        $this->update();
        $this->seeStatusCode(400);
    }
    public function test_update_unamnagedNote_notOwnedConsultantNotes_403()
    {
        $program = $this->consultant->program;
        
        $otherPersonnel = new RecordOfPersonnel($this->personnel->firm, 'other');
        $otherPersonnel->insert($this->connection);
        
        $otherConsultant = new RecordOfConsultant($program, $otherPersonnel, 1);
        $otherConsultant->insert($this->connection);
        
        $this->consultantNoteOne->consultant = $otherConsultant;
        
        $this->update();
        $this->seeStatusCode(403);
    }
    public function test_update_inactiveCoordiantor_403()
    {
        $this->consultant->active = false;
        
        $this->update();
        $this->seeStatusCode(403);
    }
    
    //
    protected function hideFromParticipant()
    {
        $this->persistConsultantDependency();
        
        $this->clientParticipantOne->client->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        
        $this->consultantNoteOne->insert($this->connection);
        
        $uri = $this->consultantUri . "/notes/{$this->consultantNoteOne->id}/hide-from-participant";
//echo $uri;
        $this->patch($uri, [], $this->personnel->token);
    }
    public function test_hideFromParticipant_200()
    {
$this->disableExceptionHandling();
        $this->hideFromParticipant();
        $this->seeStatusCode(200);
        
        $respose = [
//'printme',
            'id' => $this->consultantNoteOne->id,
            'viewableByParticipant' => false,
        ];
        $this->seeJsonContains($respose);
        
        $consultantNoteEntry = [
            'id' => $this->consultantNoteOne->id,
            'viewableByParticipant' => false,
        ];
        $this->seeInDatabase('ConsultantNote', $consultantNoteEntry);
    }
    public function test_hideFromParticipant_unmanagedNotes_notOwnedConsultantNote_403()
    {
        $program = $this->consultant->program;
        
        $otherPersonnel = new RecordOfPersonnel($this->personnel->firm, 'other');
        $otherPersonnel->insert($this->connection);
        
        $otherConsultant = new RecordOfConsultant($program, $otherPersonnel, 1);
        $otherConsultant->insert($this->connection);
        
        $this->consultantNoteOne->consultant = $otherConsultant;
        
        $this->hideFromParticipant();
        $this->seeStatusCode(403);
    }
    public function test_hideFromParticipant_inactiveConsultant_403()
    {
        $this->consultant->active = false;
        
        $this->hideFromParticipant();
        $this->seeStatusCode(403);
    }
    
    //
    protected function showToParticipant()
    {
        $this->persistConsultantDependency();
        
        $this->clientParticipantOne->client->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        
        $this->consultantNoteOne->insert($this->connection);
        
        $uri = $this->consultantUri . "/notes/{$this->consultantNoteOne->id}/show-to-participant";
//echo $uri;
        $this->patch($uri, [], $this->personnel->token);
    }
    public function test_showToParticipant_200()
    {
        $this->consultantNoteOne->viewableByParticipant = false;
$this->disableExceptionHandling();
        $this->showToParticipant();
        $this->seeStatusCode(200);
        
        $respose = [
//'printme',
            'id' => $this->consultantNoteOne->id,
            'viewableByParticipant' => true,
        ];
        $this->seeJsonContains($respose);
        
        $consultantNoteEntry = [
            'id' => $this->consultantNoteOne->id,
            'viewableByParticipant' => true,
        ];
        $this->seeInDatabase('ConsultantNote', $consultantNoteEntry);
    }
    public function test_showToParticipant_unmanagedNotes_notOwnedConsultantNote_403()
    {
        $program = $this->consultant->program;
        
        $otherPersonnel = new RecordOfPersonnel($this->personnel->firm, 'other');
        $otherPersonnel->insert($this->connection);
        
        $otherConsultant = new RecordOfConsultant($program, $otherPersonnel, 1);
        $otherConsultant->insert($this->connection);
        
        $this->consultantNoteOne->consultant = $otherConsultant;
        
        $this->showToParticipant();
        $this->seeStatusCode(403);
    }
    public function test_showToParticipant_inactiveConsultant_403()
    {
        $this->consultant->active = false;
        
        $this->showToParticipant();
        $this->seeStatusCode(403);
    }
    
    //
    protected function remove()
    {
        $this->persistConsultantDependency();
        
        $this->clientParticipantOne->client->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        
        $this->consultantNoteOne->insert($this->connection);
        
        $uri = $this->consultantUri . "/notes/{$this->consultantNoteOne->id}";
//echo $uri;
        $this->delete($uri, $this->updateRequest, $this->personnel->token);
    }
    public function test_remove_200()
    {
$this->disableExceptionHandling();
        $this->remove();
        $this->seeStatusCode(200);
        
//$this->seeJsonContains(['printme']);
        
        $noteEntry = [
            'id' => $this->consultantNoteOne->note->id,
            'removed' => true
        ];
        $this->seeInDatabase('Note', $noteEntry);
    }
    public function test_remove_unmanagedNotes_notOwnedConsultantNote_403()
    {
        $program = $this->consultant->program;
        
        $otherPersonnel = new RecordOfPersonnel($this->personnel->firm, 'other');
        $otherPersonnel->insert($this->connection);
        
        $otherConsultant = new RecordOfConsultant($program, $otherPersonnel, 1);
        $otherConsultant->insert($this->connection);
        
        $this->consultantNoteOne->consultant = $otherConsultant;
        
        $this->remove();
        $this->seeStatusCode(403);
    }
    public function test_remove_inactiveConsultant_403()
    {
        $this->consultant->active = false;
        
        $this->remove();
        $this->seeStatusCode(403);
    }
    
    //
    protected function viewConsultantNoteDetail()
    {
        $this->persistConsultantDependency();
        
        $this->clientParticipantOne->client->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        
        $this->consultantNoteOne->insert($this->connection);
        
        $uri = $this->consultantUri . "/consultant-notes/{$this->consultantNoteOne->id}";
//echo $uri;
        $this->get($uri, $this->personnel->token);
    }
    public function test_viewConsultantNoteDetail_200()
    {
        $this->viewConsultantNoteDetail();
        $this->seeStatusCode(200);
        
        $response = [
//'printme',
            'id' => $this->consultantNoteOne->id,
            'consultant' => [
                'id' => $this->consultant->id,
                'personnel' => [
                    'id' => $this->consultant->personnel->id,
                    'name' => $this->consultant->personnel->getFullName(),
                ],
            ],
            'participant' => [
                'id' => $this->clientParticipantOne->participant->id,
                'client' => [
                    'id' => $this->clientParticipantOne->client->id,
                    'name' => $this->clientParticipantOne->client->getFullName(),
                ],
                'team' => null,
                'user' => null,
            ],
            'viewableByParticipant' => $this->consultantNoteOne->viewableByParticipant,
            'name' => $this->consultantNoteOne->note->name,
            'description' => $this->consultantNoteOne->note->description,
            'createdTime' => $this->consultantNoteOne->note->createdTime,
            'modifiedTime' => $this->consultantNoteOne->note->modifiedTime,
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewConsultantNoteDetail_unmanagedNote_belongsToParticipantOfOtherProgram_404()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        
        $this->consultantNoteOne->participant->program = $otherProgram;
        
        $this->viewConsultantNoteDetail();
        $this->seeStatusCode(404);
    }
    public function test_viewConsultantNoteDetail_inactiveConsultants_403()
    {
        $this->consultant->active = false;
        
        $this->viewConsultantNoteDetail();
        $this->seeStatusCode(403);
    }
    
    //
    protected function viewCoordinatorNoteDetail()
    {
        $this->persistConsultantDependency();
        
        $this->clientParticipantOne->client->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        
        $this->coordinatorNoteOne->coordinator->personnel->insert($this->connection);
        $this->coordinatorNoteOne->coordinator->insert($this->connection);
        
        $this->coordinatorNoteOne->insert($this->connection);
        
        $uri = $this->consultantUri . "/coordinator-notes/{$this->coordinatorNoteOne->id}";
//echo $uri;
        $this->get($uri, $this->personnel->token);
    }
    public function test_viewCoordinatorNoteDetail_200()
    {
        $this->viewCoordinatorNoteDetail();
        $this->seeStatusCode(200);
        
        $response = [
//'printme',
            'id' => $this->coordinatorNoteOne->id,
            'coordinator' => [
                'id' => $this->coordinatorNoteOne->coordinator->id,
                'personnel' => [
                    'id' => $this->coordinatorNoteOne->coordinator->personnel->id,
                    'name' => $this->coordinatorNoteOne->coordinator->personnel->getFullName(),
                ],
            ],
            'participant' => [
                'id' => $this->clientParticipantOne->participant->id,
                'client' => [
                    'id' => $this->clientParticipantOne->client->id,
                    'name' => $this->clientParticipantOne->client->getFullName(),
                ],
                'team' => null,
                'user' => null,
            ],
            'viewableByParticipant' => $this->coordinatorNoteOne->viewableByParticipant,
            'name' => $this->coordinatorNoteOne->note->name,
            'description' => $this->coordinatorNoteOne->note->description,
            'createdTime' => $this->coordinatorNoteOne->note->createdTime,
            'modifiedTime' => $this->coordinatorNoteOne->note->modifiedTime,
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewCoordinatorNoteDetail_unmanagedNote_belongsToParticipantOfOtherProgram_404()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        
        $this->coordinatorNoteOne->participant->program = $otherProgram;
        
        $this->viewCoordinatorNoteDetail();
        $this->seeStatusCode(404);
    }
    public function test_viewCoordinatorNoteDetail_inactiveConsultants_403()
    {
        $this->consultant->active = false;
        
        $this->viewCoordinatorNoteDetail();
        $this->seeStatusCode(403);
    }
    
    //
    protected function viewParticipantNoteDetail()
    {
        $this->persistConsultantDependency();
        
        $this->clientParticipantOne->client->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        
        $this->participantNoteOne->insert($this->connection);
        
        $uri = $this->consultantUri . "/participant-notes/{$this->participantNoteOne->id}";
//echo $uri;
        $this->get($uri, $this->personnel->token);
    }
    public function test_viewParticipantNoteDetail_200()
    {
        $this->viewParticipantNoteDetail();
        $this->seeStatusCode(200);
        
        $response = [
//'printme',
            'id' => $this->participantNoteOne->id,
            'participant' => [
                'id' => $this->clientParticipantOne->participant->id,
                'client' => [
                    'id' => $this->clientParticipantOne->client->id,
                    'name' => $this->clientParticipantOne->client->getFullName(),
                ],
                'team' => null,
                'user' => null,
            ],
            'name' => $this->participantNoteOne->note->name,
            'description' => $this->participantNoteOne->note->description,
            'createdTime' => $this->participantNoteOne->note->createdTime,
            'modifiedTime' => $this->participantNoteOne->note->modifiedTime,
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewParticipantNoteDetail_unmanagedNote_belongsToParticipantOfOtherProgram_404()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        
        $this->participantNoteOne->participant->program = $otherProgram;
        
        $this->viewParticipantNoteDetail();
        $this->seeStatusCode(404);
    }
    public function test_viewParticipantNoteDetail_inactiveConsultants_403()
    {
        $this->consultant->active = false;
        
        $this->viewParticipantNoteDetail();
        $this->seeStatusCode(403);
    }
}
