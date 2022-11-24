<?php

namespace Tests\Controllers\Client\ProgramParticipation;

use DateTime;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfConsultantNote;
use Tests\Controllers\RecordPreparation\Firm\Program\Coordinator\RecordOfCoordinatorNote;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfParticipantNote;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Shared\RecordOfNote;

class NoteControllerTest extends ExtendedClientParticipantTestCase
{
    protected $participantNoteOne;
    protected $consultantNoteTwo;
    protected $coordinatorNoteThree;
    protected $submitOrUpdateRequest = [
        'name' => 'new note name',
        'description' => 'new note description',
    ];
    //
    protected $viewAllUri;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Coordinator')->truncate();
        $this->connection->table('Note')->truncate();
        $this->connection->table('ParticipantNote')->truncate();
        $this->connection->table('ConsultantNote')->truncate();
        $this->connection->table('CoordinatorNote')->truncate();
        
        $participant = $this->clientParticipant->participant;
        $program = $participant->program;
        $firm = $program->firm;
        
        $personnelOne = new RecordOfPersonnel($firm, 1);
        $personnelTwo = new RecordOfPersonnel($firm, 2);
        
        $consultantOne = new RecordOfConsultant($program, $personnelOne, 1);
        
        $coordinatorOne = new RecordOfCoordinator($program, $personnelTwo, 1);
        
        $noteOne = new RecordOfNote(1);
        $noteTwo = new RecordOfNote(2);
        $noteThree = new RecordOfNote(3);
        
        $this->participantNoteOne = new RecordOfParticipantNote($noteOne, $participant);
        
        $this->consultantNoteTwo = new RecordOfConsultantNote($noteTwo, $consultantOne, $participant);
        
        $this->coordinatorNoteThree = new RecordOfCoordinatorNote($noteThree, $coordinatorOne, $participant);
        //
        $this->viewAllUri = $this->clientParticipantUri . "/notes";
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Coordinator')->truncate();
        $this->connection->table('Note')->truncate();
        $this->connection->table('ParticipantNote')->truncate();
        $this->connection->table('ConsultantNote')->truncate();
        $this->connection->table('CoordinatorNote')->truncate();
    }
    
    //
    protected function submit()
    {
        $this->insertClientParticipantRecord();
        
        $uri = $this->clientParticipantUri . "/participant-notes";
// echo $uri;
// echo json_encode($this->submitOrUpdateRequest);
        $this->post($uri, $this->submitOrUpdateRequest, $this->client->token);
    }
    public function test_submit_200()
    {
$this->disableExceptionHandling();
        $this->submit();
        $this->seeStatusCode(201);
        
// $this->seeJsonContains(['printme']);
        
        $response = [
            'name' => $this->submitOrUpdateRequest['name'],
            'description' => $this->submitOrUpdateRequest['description'],
            'createdTime' => $this->currentTimeString(),
            'modifiedTime' => $this->currentTimeString(),
        ];
        $this->seeJsonContains($response);
        
        $noteEntry = [
            'name' => $this->submitOrUpdateRequest['name'],
            'description' => $this->submitOrUpdateRequest['description'],
            'createdTime' => $this->currentTimeString(),
            'modifiedTime' => $this->currentTimeString(),
        ];
        $this->seeInDatabase('Note', $noteEntry);
        
        $participantNoteEntry = [
            'Participant_id' => $this->clientParticipant->participant->id,
        ];
        $this->seeInDatabase('ParticipantNote', $participantNoteEntry);
    }
    public function test_submit_emptyName_400()
    {
        $this->submitOrUpdateRequest['name'] = '';
        
        $this->submit();
        $this->seeStatusCode(400);
    }
    public function test_submit_inactiveParticipant_403()
    {
        $this->clientParticipant->participant->active = false;
        
        $this->submit();
        $this->seeStatusCode(403);
    }
    
    //
    protected function update()
    {
        $this->insertClientParticipantRecord();
        
        $this->participantNoteOne->insert($this->connection);
        
        $uri = $this->clientParticipantUri . "/participant-notes/{$this->participantNoteOne->id}";
// echo $uri;
// echo json_encode($this->submitOrUpdateRequest);
        $this->patch($uri, $this->submitOrUpdateRequest, $this->client->token);
    }
    public function test_update_200()
    {
$this->disableExceptionHandling();
        $this->update();
        $this->seeStatusCode(200);
        
// $this->seeJsonContains(['printme']);
        
        $response = [
            'id' => $this->participantNoteOne->id,
            'name' => $this->submitOrUpdateRequest['name'],
            'description' => $this->submitOrUpdateRequest['description'],
            'createdTime' => $this->participantNoteOne->note->createdTime,
            'modifiedTime' => $this->currentTimeString(),
        ];
        $this->seeJsonContains($response);
        
        $noteEntry = [
            'id' => $this->participantNoteOne->note->id,
            'name' => $this->submitOrUpdateRequest['name'],
            'description' => $this->submitOrUpdateRequest['description'],
            'modifiedTime' => $this->currentTimeString(),
        ];
        $this->seeInDatabase('Note', $noteEntry);
    }
    public function test_update_emptyName_400()
    {
        $this->submitOrUpdateRequest['name'] = '';
        
        $this->update();
        $this->seeStatusCode(400);
    }
    public function test_update_sameContent_preventUpdatingModifiedTime()
    {
        $this->submitOrUpdateRequest['name'] = $this->participantNoteOne->note->name;
        $this->submitOrUpdateRequest['description'] = $this->participantNoteOne->note->description;
        
        $this->update();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->participantNoteOne->id,
            'modifiedTime' => $this->participantNoteOne->note->modifiedTime,
        ];
        $this->seeJsonContains($response);
        
        $noteEntry = [
            'id' => $this->participantNoteOne->id,
            'modifiedTime' => $this->participantNoteOne->note->modifiedTime,
            'name' => $this->submitOrUpdateRequest['name'],
            'description' => $this->submitOrUpdateRequest['description'],
        ];
        $this->seeInDatabase('Note', $noteEntry);
    }
    public function test_update_inactiveParticipant_403()
    {
        $this->clientParticipant->participant->active = false;
        
        $this->update();
        $this->seeStatusCode(403);
    }
    public function test_update_updatingNotOwnedNote_403()
    {
        $program = $this->clientParticipant->participant->program;
        $otherParticipant = new RecordOfParticipant($program, 'other');
        $otherParticipant->insert($this->connection);
        
        $this->participantNoteOne->participant = $otherParticipant;
        
        $this->update();
        $this->seeStatusCode(403);
    }
    
    //
    protected function remove()
    {
        $this->insertClientParticipantRecord();
        
        $this->participantNoteOne->insert($this->connection);
        
        $uri = $this->clientParticipantUri . "/participant-notes/{$this->participantNoteOne->id}";
// echo $uri;
        $this->delete($uri, [], $this->client->token);
    }
    public function test_remove_200()
    {
$this->disableExceptionHandling();
        $this->remove();
        $this->seeStatusCode(200);
// $this->seeJsonContains(['printme']);
        
        $noteEntry = [
            'id' => $this->participantNoteOne->note->id,
            'removed' => true,
        ];
        $this->seeInDatabase('Note', $noteEntry);
    }
    public function test_remove_inactiveParticipant_403()
    {
        $this->clientParticipant->participant->active = false;
        
        $this->remove();
        $this->seeStatusCode(403);
    }
    public function test_remove_updatingNotOwnedNote_403()
    {
        $program = $this->clientParticipant->participant->program;
        $otherParticipant = new RecordOfParticipant($program, 'other');
        $otherParticipant->insert($this->connection);
        
        $this->participantNoteOne->participant = $otherParticipant;
        
        $this->remove();
        $this->seeStatusCode(403);
    }
    
    protected function viewOwnedParticipantNote()
    {
        $this->insertClientParticipantRecord();
        
        $this->participantNoteOne->insert($this->connection);
        
        $uri = $this->clientParticipantUri . "/participant-notes/{$this->participantNoteOne->id}";
// echo $uri;
        $this->get($uri, $this->client->token);
    }
    public function test_viewOwnedParticipantNote_200()
    {
$this->disableExceptionHandling();
        $this->viewOwnedParticipantNote();
        $this->seeStatusCode(200);
// $this->seeJsonContains(['printme']);
        
        $response = [
            'id' => $this->participantNoteOne->id,
            'name' => $this->participantNoteOne->note->name,
            'description' => $this->participantNoteOne->note->description,
            'createdTime' => $this->participantNoteOne->note->createdTime,
            'modifiedTime' => $this->participantNoteOne->note->modifiedTime,
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewOwnedParticipantNote_inactiveParticipant_403()
    {
        $this->clientParticipant->participant->active = false;
        
        $this->viewOwnedParticipantNote();
        $this->seeStatusCode(403);
    }
    public function test_viewOwnedParticipantNote_notOwned_404()
    {
        $program = $this->clientParticipant->participant->program;
        $otherParticipant = new RecordOfParticipant($program, 'other');
        $otherParticipant->insert($this->connection);
        
        $this->participantNoteOne->participant = $otherParticipant;
        
        $this->viewOwnedParticipantNote();
        $this->seeStatusCode(404);
    }
    
    protected function viewAccessibleConsultantNote()
    {
        $this->insertClientParticipantRecord();
        
        $this->consultantNoteTwo->consultant->personnel->insert($this->connection);
        $this->consultantNoteTwo->consultant->insert($this->connection);
        $this->consultantNoteTwo->insert($this->connection);
        
        $uri = $this->clientParticipantUri . "/consultant-notes/{$this->consultantNoteTwo->id}";
// echo $uri;
        $this->get($uri, $this->client->token);
    }
    public function test_viewAccesibleConsultantNote_200()
    {
$this->disableExceptionHandling();
        $this->viewAccessibleConsultantNote();
        $this->seeStatusCode(200);
// $this->seeJsonContains(['printme']);
        
        $response = [
            'id' => $this->consultantNoteTwo->id,
            'name' => $this->consultantNoteTwo->note->name,
            'description' => $this->consultantNoteTwo->note->description,
            'createdTime' => $this->consultantNoteTwo->note->createdTime,
            'modifiedTime' => $this->consultantNoteTwo->note->modifiedTime,
            'consultant' => [
                'id' => $this->consultantNoteTwo->consultant->id,
                'personnel' => [
                    'id' => $this->consultantNoteTwo->consultant->personnel->id,
                    'name' => $this->consultantNoteTwo->consultant->personnel->getFullName(),
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewAccesibleConsultantNote_inactiveParticipant_403()
    {
        $this->clientParticipant->participant->active = false;
        
        $this->viewAccessibleConsultantNote();
        $this->seeStatusCode(403);
    }
    public function test_viewAccesibleConsultantNote_inaccessibleNote_belongsToOtherParticiant_404()
    {
        $program = $this->clientParticipant->participant->program;
        $otherParticipant = new RecordOfParticipant($program, 'other');
        $otherParticipant->insert($this->connection);
        
        $this->consultantNoteTwo->participant = $otherParticipant;
        
        $this->viewAccessibleConsultantNote();
        $this->seeStatusCode(404);
    }
    public function test_viewAccesibleConsultantNote_nonViewableByParticipant_404()
    {
        $this->consultantNoteTwo->viewableByParticipant = false;
        
        $this->viewAccessibleConsultantNote();
        $this->seeStatusCode(404);
    }
    
    protected function viewAccessibleCoordinatorNote()
    {
        $this->insertClientParticipantRecord();
        
        $this->coordinatorNoteThree->coordinator->personnel->insert($this->connection);
        $this->coordinatorNoteThree->coordinator->insert($this->connection);
        $this->coordinatorNoteThree->insert($this->connection);
        
        $uri = $this->clientParticipantUri . "/coordinator-notes/{$this->coordinatorNoteThree->id}";
// echo $uri;
        $this->get($uri, $this->client->token);
    }
    public function test_viewAccesibleCoordinatorNote_200()
    {
$this->disableExceptionHandling();
        $this->viewAccessibleCoordinatorNote();
        $this->seeStatusCode(200);
// $this->seeJsonContains(['printme']);
        
        $response = [
            'id' => $this->coordinatorNoteThree->id,
            'name' => $this->coordinatorNoteThree->note->name,
            'description' => $this->coordinatorNoteThree->note->description,
            'createdTime' => $this->coordinatorNoteThree->note->createdTime,
            'modifiedTime' => $this->coordinatorNoteThree->note->modifiedTime,
            'coordinator' => [
                'id' => $this->coordinatorNoteThree->coordinator->id,
                'personnel' => [
                    'id' => $this->coordinatorNoteThree->coordinator->personnel->id,
                    'name' => $this->coordinatorNoteThree->coordinator->personnel->getFullName(),
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewAccesibleCoordinatorNote_inactiveParticipant_403()
    {
        $this->clientParticipant->participant->active = false;
        
        $this->viewAccessibleCoordinatorNote();
        $this->seeStatusCode(403);
    }
    public function test_viewAccesibleCoordinatorNote_inaccessibleNote_belongsToOtherParticiant_404()
    {
        $program = $this->clientParticipant->participant->program;
        $otherParticipant = new RecordOfParticipant($program, 'other');
        $otherParticipant->insert($this->connection);
        
        $this->coordinatorNoteThree->participant = $otherParticipant;
        
        $this->viewAccessibleCoordinatorNote();
        $this->seeStatusCode(404);
    }
    public function test_viewAccesibleCoordinatorNote_inaccessibleNote_notViewbleByParticipant_404()
    {
        $this->coordinatorNoteThree->viewableByParticipant = false;
        
        $this->viewAccessibleCoordinatorNote();
        $this->seeStatusCode(404);
    }
    
    protected function viewAll()
    {
        $this->insertClientParticipantRecord();
        
        $this->participantNoteOne->insert($this->connection);
        
        $this->consultantNoteTwo->consultant->personnel->insert($this->connection);
        $this->consultantNoteTwo->consultant->insert($this->connection);
        $this->consultantNoteTwo->insert($this->connection);
        
        $this->coordinatorNoteThree->coordinator->personnel->insert($this->connection);
        $this->coordinatorNoteThree->coordinator->insert($this->connection);
        $this->coordinatorNoteThree->insert($this->connection);
        
// echo $this->viewAllUri;
        $this->get($this->viewAllUri, $this->client->token);
    }
    public function test_viewAll_200()
    {
$this->disableExceptionHandling();
        $this->viewAll();
        $this->seeStatusCode(200);
// $this->seeJsonContains(['printme']);
        
        $response = [
            'total' => '3',
            'list' => [
                [
                    'name' => $this->participantNoteOne->note->name,
                    'description' => $this->participantNoteOne->note->description,
                    'createdTime' => $this->participantNoteOne->note->createdTime,
                    'modifiedTime' => $this->participantNoteOne->note->modifiedTime,
                    'participantNoteId' => $this->participantNoteOne->id,
                    'consultantNoteId' => null,
                    'coordinatorNoteId' => null,
                    'personnelName' => null,
                ],
                [
                    'name' => $this->consultantNoteTwo->note->name,
                    'description' => $this->consultantNoteTwo->note->description,
                    'createdTime' => $this->consultantNoteTwo->note->createdTime,
                    'modifiedTime' => $this->consultantNoteTwo->note->modifiedTime,
                    'participantNoteId' => null,
                    'consultantNoteId' => $this->consultantNoteTwo->id,
                    'coordinatorNoteId' => null,
                    'personnelName' => $this->consultantNoteTwo->consultant->personnel->getFullName(),
                ],
                [
                    'name' => $this->coordinatorNoteThree->note->name,
                    'description' => $this->coordinatorNoteThree->note->description,
                    'createdTime' => $this->coordinatorNoteThree->note->createdTime,
                    'modifiedTime' => $this->coordinatorNoteThree->note->modifiedTime,
                    'participantNoteId' => null,
                    'coordinatorNoteId' => $this->coordinatorNoteThree->id,
                    'consultantNoteId' => null,
                    'personnelName' => $this->coordinatorNoteThree->coordinator->personnel->getFullName(),
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewAll_allFilterAndOrder()
    {
        $from = (new DateTime('-1 months'))->format('Y-m-d H:i:s');
        $to = (new DateTime())->format('Y-m-d H:i:s');
        $keyword = 'note';
        $source = 'consultant';
        $order = 'modified-asc';
        
        $this->viewAllUri .=
                "?from=$from"
                . "&to=$to"
                . "&keyword=$keyword"
                . "&source=$source"
                . "&order=$order";
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['participantNoteId' => $this->participantNoteOne->id]);
        $this->seeJsonContains(['consultantNoteId' => $this->consultantNoteTwo->id]);
        $this->seeJsonDoesntContains(['coordinatorNoteId' => $this->coordinatorNoteThree->id]);
    }
    public function test_viewAll_excludeRemovedParticipantNote()
    {
        $this->participantNoteOne->note->removed = true;
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonDoesntContains(['participantNoteId' => $this->participantNoteOne->id]);
        $this->seeJsonContains(['consultantNoteId' => $this->consultantNoteTwo->id]);
        $this->seeJsonContains(['coordinatorNoteId' => $this->coordinatorNoteThree->id]);
    }
    public function test_viewAll_excludeNotOwnedParticipantNote()
    {
        $program = $this->clientParticipant->participant->program;
        $otherParticipant = new RecordOfParticipant($program, 'other');
        $otherParticipant->insert($this->connection);
        
        $this->participantNoteOne->participant = $otherParticipant;
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonDoesntContains(['participantNoteId' => $this->participantNoteOne->id]);
        $this->seeJsonContains(['consultantNoteId' => $this->consultantNoteTwo->id]);
        $this->seeJsonContains(['coordinatorNoteId' => $this->coordinatorNoteThree->id]);
    }
    public function test_viewAll_excludeRemovedConsultantNote()
    {
        $this->consultantNoteTwo->note->removed = true;
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonContains(['participantNoteId' => $this->participantNoteOne->id]);
        $this->seeJsonDoesntContains(['consultantNoteId' => $this->consultantNoteTwo->id]);
        $this->seeJsonContains(['coordinatorNoteId' => $this->coordinatorNoteThree->id]);
    }
    public function test_viewAll_excludeUnviewableConsultantNote()
    {
        $this->consultantNoteTwo->viewableByParticipant = false;
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonContains(['participantNoteId' => $this->participantNoteOne->id]);
        $this->seeJsonDoesntContains(['consultantNoteId' => $this->consultantNoteTwo->id]);
        $this->seeJsonContains(['coordinatorNoteId' => $this->coordinatorNoteThree->id]);
    }
    public function test_viewAll_excludeConsultantNoteToOtherParticipant()
    {
        $program = $this->clientParticipant->participant->program;
        $otherParticipant = new RecordOfParticipant($program, 'other');
        $otherParticipant->insert($this->connection);
        
        $this->consultantNoteTwo->participant = $otherParticipant;
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonContains(['participantNoteId' => $this->participantNoteOne->id]);
        $this->seeJsonDoesntContains(['consultantNoteId' => $this->consultantNoteTwo->id]);
        $this->seeJsonContains(['coordinatorNoteId' => $this->coordinatorNoteThree->id]);
    }
    public function test_viewAll_excludeRemovedCoordinatorNote()
    {
        $this->coordinatorNoteThree->note->removed = true;
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonContains(['participantNoteId' => $this->participantNoteOne->id]);
        $this->seeJsonContains(['consultantNoteId' => $this->consultantNoteTwo->id]);
        $this->seeJsonDoesntContains(['coordinatorNoteId' => $this->coordinatorNoteThree->id]);
    }
    public function test_viewAll_excludeUnivewableCoordinatorNote()
    {
        $this->coordinatorNoteThree->viewableByParticipant = false;
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonContains(['participantNoteId' => $this->participantNoteOne->id]);
        $this->seeJsonContains(['consultantNoteId' => $this->consultantNoteTwo->id]);
        $this->seeJsonDoesntContains(['coordinatorNoteId' => $this->coordinatorNoteThree->id]);
    }
    public function test_viewAll_excludeCoordinatorNoteToOtherParticipant()
    {
        $program = $this->clientParticipant->participant->program;
        $otherParticipant = new RecordOfParticipant($program, 'other');
        $otherParticipant->insert($this->connection);
        
        $this->coordinatorNoteThree->participant = $otherParticipant;
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonContains(['participantNoteId' => $this->participantNoteOne->id]);
        $this->seeJsonContains(['consultantNoteId' => $this->consultantNoteTwo->id]);
        $this->seeJsonDoesntContains(['coordinatorNoteId' => $this->coordinatorNoteThree->id]);
    }
    public function test_viewAll_inactiveParticipant_403()
    {
        $this->clientParticipant->participant->active = false;
        
        $this->viewAll();
        $this->seeStatusCode(403);
    }
}
