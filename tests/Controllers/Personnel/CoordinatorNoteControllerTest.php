<?php

namespace Tests\Controllers\Personnel;

use DateTime;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Coordinator\RecordOfCoordinatorNote;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\RecordOfUser;
use Tests\Controllers\RecordPreparation\Shared\RecordOfNote;
use Tests\Controllers\RecordPreparation\User\RecordOfUserParticipant;

class CoordinatorNoteControllerTest extends ExtendedPersonnelTestCase
{
    protected $showAllUri;
    //
    protected $coordinatorOne_p1;
    protected $coordinatorTwo_p2;
    
    protected $clientParticipantOne_p1;
    protected $teamParticipantOne_p2;
    protected $userParticipantOne_p1;
    
    protected $coordinatorNoteOne_m1;
    protected $coordinatorNoteTwo_m2;
    protected $coordinatorNoteThree_m1;
    //
    protected $submitNoteRequest;
    protected $updateNoteRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->showAllUri = $this->personnelUri . "/coordinator-notes";
        //
        $this->connection->table('Program')->truncate();
        $this->connection->table('Coordinator')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        $this->connection->table('Note')->truncate();
        $this->connection->table('CoordinatorNote')->truncate();
        
        //
        $firm = $this->personnel->firm;
        
        $programOne = new RecordOfProgram($firm, 1);
        $programTwo = new RecordOfProgram($firm, 2);
        
        $this->coordinatorOne_p1 = new RecordOfCoordinator($programOne, $this->personnel, 1);
        $this->coordinatorTwo_p2 = new RecordOfCoordinator($programTwo, $this->personnel, 2);
        
        $clientOne = new RecordOfClient($firm, 1);
        $teamOne = new RecordOfTeam($firm, $clientOne, 1);
        $userOne = new RecordOfUser(1);
        
        $participantOne_p1 = new RecordOfParticipant($programOne, 1);
        $participantTwo_p2 = new RecordOfParticipant($programTwo, 2);
        $participantThree_p1 = new RecordOfParticipant($programOne, 3);
        
        $this->clientParticipantOne_p1 = new RecordOfClientParticipant($clientOne, $participantOne_p1);
        $this->teamParticipantOne_p2 = new RecordOfTeamProgramParticipation($teamOne, $participantTwo_p2);
        $this->userParticipantOne_p1 = new RecordOfUserParticipant($userOne, $participantThree_p1);
        
        $noteOne = new RecordOfNote(1);
        $noteTwo = new RecordOfNote(2);
        $noteThree = new RecordOfNote(3);
        
        $this->coordinatorNoteOne_m1 = new RecordOfCoordinatorNote($noteOne, $this->coordinatorOne_p1, $this->clientParticipantOne_p1->participant);
        $this->coordinatorNoteTwo_m2 = new RecordOfCoordinatorNote($noteTwo, $this->coordinatorTwo_p2, $this->teamParticipantOne_p2->participant);
        $this->coordinatorNoteThree_m1 = new RecordOfCoordinatorNote($noteThree, $this->coordinatorOne_p1, $this->userParticipantOne_p1->participant);
        
        //
        $this->submitNoteRequest = [
            'participantId' => $this->clientParticipantOne_p1->participant->id,
            'content' => 'new note',
            'viewableByParticipant' => false,
        ];
        $this->updateNoteRequest = [
            'content' => 'new note',
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Coordinator')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        $this->connection->table('Note')->truncate();
        $this->connection->table('CoordinatorNote')->truncate();
    }
    
    //
    protected function submit()
    {
        $this->persistPersonnelDependency();
        
        $this->coordinatorOne_p1->program->insert($this->connection);
        $this->coordinatorOne_p1->insert($this->connection);
        
        $this->clientParticipantOne_p1->client->insert($this->connection);
        $this->clientParticipantOne_p1->insert($this->connection);
        
        $uri = $this->personnelUri . "/coordinators/{$this->coordinatorOne_p1->id}/coordinator-notes";
        $this->post($uri, $this->submitNoteRequest, $this->personnel->token);
    }
    public function test_submit_200()
    {
$this->disableExceptionHandling();
        $this->submit();
        $this->seeStatusCode(201);
        
        $response = [
            'content' => $this->submitNoteRequest['content'],
            'createdTime' => $this->currentTimeString(),
            'modifiedTime' => $this->currentTimeString(),
            'viewableByParticipant' => $this->submitNoteRequest['viewableByParticipant'],
            'participant' => [
                'id' => $this->clientParticipantOne_p1->participant->id,
                'client' => [
                    'id' => $this->clientParticipantOne_p1->client->id,
                    'name' => $this->clientParticipantOne_p1->client->getFullName(),
                ],
                'team' => null,
                'user' => null,
            ],
            'coordinator' => [
                'id' => $this->coordinatorOne_p1->id,
                'program' => [
                    'id' => $this->coordinatorOne_p1->program->id,
                    'name' => $this->coordinatorOne_p1->program->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
        
        $coordinatorNoteEntry = [
            'Coordinator_id' => $this->coordinatorOne_p1->id,
            'Participant_id' => $this->clientParticipantOne_p1->participant->id,
            'viewableByParticipant' => $this->submitNoteRequest['viewableByParticipant'],
        ];
        $this->seeInDatabase('CoordinatorNote', $coordinatorNoteEntry);
        
        $noteEntry = [
            'content' => $this->submitNoteRequest['content'],
            'removed' => false,
        ];
        $this->seeInDatabase('Note', $noteEntry);
    }
    public function test_submit_emptyContent_400()
    {
        $this->submitNoteRequest['content'] = '';
        
        $this->submit();
        $this->seeStatusCode(400);
    }
    public function test_submit_inactiveMentor_403()
    {
        $this->coordinatorOne_p1->active = false;
        
        $this->submit();
        $this->seeStatusCode(403);
    }
    public function test_submit_unmanagedParticipant_notInSameProgram_403()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        
        $this->clientParticipantOne_p1->participant->program = $otherProgram;
        
        $this->submit();
        $this->seeStatusCode(403);
    }
    public function test_submit_unmanagedParticipant_inactiveParticipant_403()
    {
        $this->clientParticipantOne_p1->participant->active = false;
        
        $this->submit();
        $this->seeStatusCode(403);
    }
    
    //
    protected function update()
    {
        $this->persistPersonnelDependency();
        
        $this->clientParticipantOne_p1->client->insert($this->connection);
        $this->clientParticipantOne_p1->insert($this->connection);
        
        $this->coordinatorOne_p1->program->insert($this->connection);
        $this->coordinatorOne_p1->insert($this->connection);
        
        $this->coordinatorNoteOne_m1->insert($this->connection);
        
        $uri = $this->personnelUri . "/coordinators/{$this->coordinatorOne_p1->id}/coordinator-notes/{$this->coordinatorNoteOne_m1->id}/update";
        $this->patch($uri, $this->updateNoteRequest, $this->personnel->token);
    }
    public function test_update_200()
    {
$this->disableExceptionHandling();
        $this->update();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->coordinatorNoteOne_m1->id,
            'content' => $this->updateNoteRequest['content'],
            'createdTime' => $this->coordinatorNoteOne_m1->note->createdTime,
            'modifiedTime' => $this->currentTimeString(),
            'viewableByParticipant' => $this->coordinatorNoteOne_m1->viewableByParticipant,
            'participant' => [
                'id' => $this->coordinatorNoteOne_m1->participant->id,
                'client' => [
                    'id' => $this->clientParticipantOne_p1->client->id,
                    'name' => $this->clientParticipantOne_p1->client->getFullName(),
                ],
                'team' => null,
                'user' => null,
            ],
            'coordinator' => [
                'id' => $this->coordinatorNoteOne_m1->coordinator->id,
                'program' => [
                    'id' => $this->coordinatorNoteOne_m1->coordinator->program->id,
                    'name' => $this->coordinatorNoteOne_m1->coordinator->program->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
        
        $noteEntry = [
            'id' => $this->coordinatorNoteOne_m1->id,
            'content' => $this->updateNoteRequest['content'],
            'createdTime' => $this->coordinatorNoteOne_m1->note->createdTime,
            'modifiedTime' => $this->currentTimeString(),
            'removed' => false,
        ];
        $this->seeInDatabase('Note', $noteEntry);
    }
    public function test_update_emptyContent_400()
    {
        $this->updateNoteRequest['content'] = '';
        
        $this->update();
        $this->seeStatusCode(400);
    }
    public function test_update_unchangedContent_donotUpdateModifiedTime()
    {
        $this->updateNoteRequest['content'] = $this->coordinatorNoteOne_m1->note->content;
        
        $this->update();
        $this->seeStatusCode(200);
        
        $noteEntry = [
            'id' => $this->coordinatorNoteOne_m1->note->id,
            'modifiedTime' => $this->coordinatorNoteOne_m1->note->modifiedTime,
        ];
    }
    public function test_update_unmanagedNote_inactiveMentor_403()
    {
        $this->coordinatorNoteOne_m1->coordinator->active = false;
        
        $this->update();
        $this->seeStatusCode(403);
    }
    public function test_update_unmanagedNote_notOwnedByMentor_403()
    {
        $program = $this->coordinatorOne_p1->program;
        $otherMentor = new RecordOfCoordinator($program, $this->personnel, 'other');
        $otherMentor->insert($this->connection);
        
        $this->coordinatorNoteOne_m1->coordinator = $otherMentor;
        
        $this->update();
        $this->seeStatusCode(403);
    }
    public function test_update_removedNote_404()
    {
        $this->coordinatorNoteOne_m1->note->removed = true;
        
        $this->update();
        $this->seeStatusCode(404);
    }
    
    //
    protected function hideFromParticipant()
    {
        $this->persistPersonnelDependency();
        
        $this->clientParticipantOne_p1->client->insert($this->connection);
        $this->clientParticipantOne_p1->insert($this->connection);
        
        $this->coordinatorOne_p1->program->insert($this->connection);
        $this->coordinatorOne_p1->insert($this->connection);
        
        $this->coordinatorNoteOne_m1->insert($this->connection);
        
        $uri = $this->personnelUri . "/coordinators/{$this->coordinatorOne_p1->id}/coordinator-notes/{$this->coordinatorNoteOne_m1->id}/hide-from-participant";
        $this->patch($uri, [], $this->personnel->token);
    }
    public function test_hideFromParticipant_200()
    {
$this->disableExceptionHandling();
        $this->hideFromParticipant();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->coordinatorNoteOne_m1->id,
            'content' => $this->coordinatorNoteOne_m1->note->content,
            'createdTime' => $this->coordinatorNoteOne_m1->note->createdTime,
            'modifiedTime' => $this->coordinatorNoteOne_m1->note->createdTime,
            'viewableByParticipant' => false,
            'participant' => [
                'id' => $this->coordinatorNoteOne_m1->participant->id,
                'client' => [
                    'id' => $this->clientParticipantOne_p1->client->id,
                    'name' => $this->clientParticipantOne_p1->client->getFullName(),
                ],
                'team' => null,
                'user' => null,
            ],
            'coordinator' => [
                'id' => $this->coordinatorNoteOne_m1->coordinator->id,
                'program' => [
                    'id' => $this->coordinatorNoteOne_m1->coordinator->program->id,
                    'name' => $this->coordinatorNoteOne_m1->coordinator->program->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
        
        $coordinatorNoteEntry = [
            'id' => $this->coordinatorNoteOne_m1->id,
            'viewableByParticipant' => false,
        ];
        $this->seeInDatabase('CoordinatorNote', $coordinatorNoteEntry);
    }
    public function test_hideFromParticipant_unmanagedNote_inactiveMentor_403()
    {
        $this->coordinatorNoteOne_m1->coordinator->active = false;
        
        $this->hideFromParticipant();
        $this->seeStatusCode(403);
    }
    public function test_hideFromParticipant_unmanagedNote_notOwnedByMentor_403()
    {
        $program = $this->coordinatorOne_p1->program;
        $otherMentor = new RecordOfCoordinator($program, $this->personnel, 'other');
        $otherMentor->insert($this->connection);
        
        $this->coordinatorNoteOne_m1->coordinator = $otherMentor;
        
        $this->hideFromParticipant();
        $this->seeStatusCode(403);
    }
    public function test_hideFromParticipant_removedNote_404()
    {
        $this->coordinatorNoteOne_m1->note->removed = true;
        
        $this->hideFromParticipant();
        $this->seeStatusCode(404);
    }
    
    //
    protected function showToParticipant()
    {
        $this->persistPersonnelDependency();
        
        $this->clientParticipantOne_p1->client->insert($this->connection);
        $this->clientParticipantOne_p1->insert($this->connection);
        
        $this->coordinatorOne_p1->program->insert($this->connection);
        $this->coordinatorOne_p1->insert($this->connection);
        
        $this->coordinatorNoteOne_m1->viewableByParticipant = false;
        $this->coordinatorNoteOne_m1->insert($this->connection);
        
        $uri = $this->personnelUri . "/coordinators/{$this->coordinatorOne_p1->id}/coordinator-notes/{$this->coordinatorNoteOne_m1->id}/show-to-participant";
        $this->patch($uri, [], $this->personnel->token);
    }
    public function test_showToParticipant_200()
    {
$this->disableExceptionHandling();
        $this->showToParticipant();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->coordinatorNoteOne_m1->id,
            'content' => $this->coordinatorNoteOne_m1->note->content,
            'createdTime' => $this->coordinatorNoteOne_m1->note->createdTime,
            'modifiedTime' => $this->coordinatorNoteOne_m1->note->createdTime,
            'viewableByParticipant' => true,
            'participant' => [
                'id' => $this->coordinatorNoteOne_m1->participant->id,
                'client' => [
                    'id' => $this->clientParticipantOne_p1->client->id,
                    'name' => $this->clientParticipantOne_p1->client->getFullName(),
                ],
                'team' => null,
                'user' => null,
            ],
            'coordinator' => [
                'id' => $this->coordinatorNoteOne_m1->coordinator->id,
                'program' => [
                    'id' => $this->coordinatorNoteOne_m1->coordinator->program->id,
                    'name' => $this->coordinatorNoteOne_m1->coordinator->program->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
        
        $coordinatorNoteEntry = [
            'id' => $this->coordinatorNoteOne_m1->id,
            'viewableByParticipant' => true,
        ];
        $this->seeInDatabase('CoordinatorNote', $coordinatorNoteEntry);
    }
    public function test_showToParticipant_unmanagedNote_inactiveMentor_403()
    {
        $this->coordinatorNoteOne_m1->coordinator->active = false;
        
        $this->showToParticipant();
        $this->seeStatusCode(403);
    }
    public function test_showToParticipant_unmanagedNote_notOwnedByMentor_403()
    {
        $program = $this->coordinatorOne_p1->program;
        $otherMentor = new RecordOfCoordinator($program, $this->personnel, 'other');
        $otherMentor->insert($this->connection);
        
        $this->coordinatorNoteOne_m1->coordinator = $otherMentor;
        
        $this->showToParticipant();
        $this->seeStatusCode(403);
    }
    public function test_showToParticipant_removedNote_404()
    {
        $this->coordinatorNoteOne_m1->note->removed = true;
        
        $this->showToParticipant();
        $this->seeStatusCode(404);
    }
    
    //
    protected function remove()
    {
        $this->persistPersonnelDependency();
        
        $this->clientParticipantOne_p1->client->insert($this->connection);
        $this->clientParticipantOne_p1->insert($this->connection);
        
        $this->coordinatorOne_p1->program->insert($this->connection);
        $this->coordinatorOne_p1->insert($this->connection);
        
        $this->coordinatorNoteOne_m1->insert($this->connection);
        
        $uri = $this->personnelUri . "/coordinators/{$this->coordinatorOne_p1->id}/coordinator-notes/{$this->coordinatorNoteOne_m1->id}";
        $this->delete($uri, [], $this->personnel->token);
    }
    public function test_remove_200()
    {
$this->disableExceptionHandling();
        $this->remove();
        $this->seeStatusCode(200);
        
        $noteEntry = [
            'id' => $this->coordinatorNoteOne_m1->note->id,
            'removed' => true
        ];
        $this->seeInDatabase('Note', $noteEntry);
    }
    public function test_remove_unmanagedNote_inactiveMentor_403()
    {
        $this->coordinatorNoteOne_m1->coordinator->active = false;
        
        $this->remove();
        $this->seeStatusCode(403);
    }
    public function test_remove_unmanagedNote_notOwnedByMentor_403()
    {
        $program = $this->coordinatorOne_p1->program;
        $otherMentor = new RecordOfCoordinator($program, $this->personnel, 'other');
        $otherMentor->insert($this->connection);
        
        $this->coordinatorNoteOne_m1->coordinator = $otherMentor;
        
        $this->remove();
        $this->seeStatusCode(403);
    }
    public function test_remove_removedNote_404()
    {
        $this->coordinatorNoteOne_m1->note->removed = true;
        
        $this->remove();
        $this->seeStatusCode(404);
    }
    
    //
    protected function show()
    {
        $this->persistPersonnelDependency();
        
        $this->clientParticipantOne_p1->client->insert($this->connection);
        $this->clientParticipantOne_p1->insert($this->connection);
        
        $this->coordinatorOne_p1->program->insert($this->connection);
        $this->coordinatorOne_p1->insert($this->connection);
        
        $this->coordinatorNoteOne_m1->insert($this->connection);
        
        $uri = $this->personnelUri . "/coordinator-notes/{$this->coordinatorNoteOne_m1->id}";
        $this->get($uri, $this->personnel->token);
    }
    public function test_show_200()
    {
$this->disableExceptionHandling();
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->coordinatorNoteOne_m1->id,
            'content' => $this->coordinatorNoteOne_m1->note->content,
            'createdTime' => $this->coordinatorNoteOne_m1->note->createdTime,
            'modifiedTime' => $this->coordinatorNoteOne_m1->note->createdTime,
            'viewableByParticipant' => $this->coordinatorNoteOne_m1->viewableByParticipant,
            'participant' => [
                'id' => $this->coordinatorNoteOne_m1->participant->id,
                'client' => [
                    'id' => $this->clientParticipantOne_p1->client->id,
                    'name' => $this->clientParticipantOne_p1->client->getFullName(),
                ],
                'team' => null,
                'user' => null,
            ],
            'coordinator' => [
                'id' => $this->coordinatorNoteOne_m1->coordinator->id,
                'program' => [
                    'id' => $this->coordinatorNoteOne_m1->coordinator->program->id,
                    'name' => $this->coordinatorNoteOne_m1->coordinator->program->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_show_unmanagedNote_notOwnedByPersonnel_404()
    {
        $firm = $this->personnel->firm;
        $otherPersonnel = new RecordOfPersonnel($firm, 'other');
        $otherPersonnel->insert($this->connection);
        
        $program = $this->coordinatorOne_p1->program;
        $otherMentor = new RecordOfCoordinator($program, $otherPersonnel, 'other');
        $otherMentor->insert($this->connection);
        
        $this->coordinatorNoteOne_m1->coordinator = $otherMentor;
        
        $this->show();
        $this->seeStatusCode(404);
    }
    public function test_show_removedNote_404()
    {
        $this->coordinatorNoteOne_m1->note->removed = true;
        
        $this->show();
        $this->seeStatusCode(404);
    }
    
    //
    protected function showAll()
    {
        $this->persistPersonnelDependency();
        
        $this->coordinatorOne_p1->program->insert($this->connection);
        $this->coordinatorTwo_p2->program->insert($this->connection);
        
        $this->coordinatorOne_p1->insert($this->connection);
        $this->coordinatorTwo_p2->insert($this->connection);
        
        $this->clientParticipantOne_p1->client->insert($this->connection);
        $this->clientParticipantOne_p1->insert($this->connection);
        
        $this->teamParticipantOne_p2->team->insert($this->connection);
        $this->teamParticipantOne_p2->insert($this->connection);
        
        $this->userParticipantOne_p1->user->insert($this->connection);
        $this->userParticipantOne_p1->insert($this->connection);
        
        $this->coordinatorNoteOne_m1->insert($this->connection);
        $this->coordinatorNoteTwo_m2->insert($this->connection);
        $this->coordinatorNoteThree_m1->insert($this->connection);
        
        $this->get($this->showAllUri, $this->personnel->token);
    }
    public function test_showAll_200()
    {
$this->disableExceptionHandling();
        $this->showAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 3,
            'list' => [
                [
                    'id' => $this->coordinatorNoteOne_m1->id,
                    'content' => $this->coordinatorNoteOne_m1->note->content,
                    'createdTime' => $this->coordinatorNoteOne_m1->note->createdTime,
                    'modifiedTime' => $this->coordinatorNoteOne_m1->note->createdTime,
                    'viewableByParticipant' => $this->coordinatorNoteOne_m1->viewableByParticipant,
                    'participant' => [
                        'id' => $this->coordinatorNoteOne_m1->participant->id,
                        'client' => [
                            'id' => $this->clientParticipantOne_p1->client->id,
                            'name' => $this->clientParticipantOne_p1->client->getFullName(),
                        ],
                        'team' => null,
                        'user' => null,
                    ],
                    'coordinator' => [
                        'id' => $this->coordinatorNoteOne_m1->coordinator->id,
                        'program' => [
                            'id' => $this->coordinatorNoteOne_m1->coordinator->program->id,
                            'name' => $this->coordinatorNoteOne_m1->coordinator->program->name,
                        ],
                    ],
                ],
                [
                    'id' => $this->coordinatorNoteTwo_m2->id,
                    'content' => $this->coordinatorNoteTwo_m2->note->content,
                    'createdTime' => $this->coordinatorNoteTwo_m2->note->createdTime,
                    'modifiedTime' => $this->coordinatorNoteTwo_m2->note->createdTime,
                    'viewableByParticipant' => $this->coordinatorNoteTwo_m2->viewableByParticipant,
                    'participant' => [
                        'id' => $this->coordinatorNoteTwo_m2->participant->id,
                        'client' => null,
                        'team' => [
                            'id' => $this->teamParticipantOne_p2->team->id,
                            'name' => $this->teamParticipantOne_p2->team->name,
                        ],
                        'user' => null,
                    ],
                    'coordinator' => [
                        'id' => $this->coordinatorNoteTwo_m2->coordinator->id,
                        'program' => [
                            'id' => $this->coordinatorNoteTwo_m2->coordinator->program->id,
                            'name' => $this->coordinatorNoteTwo_m2->coordinator->program->name,
                        ],
                    ],
                ],
                [
                    'id' => $this->coordinatorNoteThree_m1->id,
                    'content' => $this->coordinatorNoteThree_m1->note->content,
                    'createdTime' => $this->coordinatorNoteThree_m1->note->createdTime,
                    'modifiedTime' => $this->coordinatorNoteThree_m1->note->createdTime,
                    'viewableByParticipant' => $this->coordinatorNoteThree_m1->viewableByParticipant,
                    'participant' => [
                        'id' => $this->coordinatorNoteThree_m1->participant->id,
                        'client' => null,
                        'team' => null,
                        'user' => [
                            'id' => $this->userParticipantOne_p1->user->id,
                            'name' => $this->userParticipantOne_p1->user->getFullName(),
                        ],
                    ],
                    'coordinator' => [
                        'id' => $this->coordinatorNoteThree_m1->coordinator->id,
                        'program' => [
                            'id' => $this->coordinatorNoteThree_m1->coordinator->program->id,
                            'name' => $this->coordinatorNoteThree_m1->coordinator->program->name,
                        ],
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_excludeRemovedNote()
    {
        $this->coordinatorNoteOne_m1->note->removed = true;
        $this->showAll();
        
        $this->seeJsonContains(['total' => 2]);
        $this->seeJsonDoesntContains(['id' => $this->coordinatorNoteOne_m1->id]);
        $this->seeJsonContains(['id' => $this->coordinatorNoteTwo_m2->id]);
        $this->seeJsonContains(['id' => $this->coordinatorNoteThree_m1->id]);
    }
    public function test_showAll_orderDESCByModifiedTime()
    {
        $this->coordinatorNoteOne_m1->note->modifiedTime = (new DateTime('-4 months'))->format('Y-m-d H:i:s');
        $this->coordinatorNoteTwo_m2->note->modifiedTime = (new DateTime('-2 months'))->format('Y-m-d H:i:s');
        $this->coordinatorNoteThree_m1->note->modifiedTime = (new DateTime('-3 months'))->format('Y-m-d H:i:s');
        
        $this->showAllUri .= "?modifiedTimeOrder=DESC&pageSize=2";
        $this->showAll();
        
        $this->seeJsonContains(['total' => 3]);
        $this->seeJsonDoesntContains(['id' => $this->coordinatorNoteOne_m1->id]);
        $this->seeJsonContains(['id' => $this->coordinatorNoteTwo_m2->id]);
        $this->seeJsonContains(['id' => $this->coordinatorNoteThree_m1->id]);
    }
    public function test_showAll_orderASCByModifiedTime()
    {
        $this->coordinatorNoteOne_m1->note->modifiedTime = (new DateTime('-4 months'))->format('Y-m-d H:i:s');
        $this->coordinatorNoteTwo_m2->note->modifiedTime = (new DateTime('-2 months'))->format('Y-m-d H:i:s');
        $this->coordinatorNoteThree_m1->note->modifiedTime = (new DateTime('-3 months'))->format('Y-m-d H:i:s');
        
        $this->showAllUri .= "?modifiedTimeOrder=ASC&pageSize=2";
        $this->showAll();
        
        $this->seeJsonContains(['total' => 3]);
        $this->seeJsonContains(['id' => $this->coordinatorNoteOne_m1->id]);
        $this->seeJsonDoesntContains(['id' => $this->coordinatorNoteTwo_m2->id]);
        $this->seeJsonContains(['id' => $this->coordinatorNoteThree_m1->id]);
    }
    public function test_showAll_orderDESCByCreatedTime()
    {
        $this->coordinatorNoteOne_m1->note->createdTime = (new DateTime('-4 months'))->format('Y-m-d H:i:s');
        $this->coordinatorNoteTwo_m2->note->createdTime = (new DateTime('-2 months'))->format('Y-m-d H:i:s');
        $this->coordinatorNoteThree_m1->note->createdTime = (new DateTime('-3 months'))->format('Y-m-d H:i:s');
        
        $this->showAllUri .= "?createdTimeOrder=DESC&pageSize=2";
        $this->showAll();
        
        $this->seeJsonContains(['total' => 3]);
        $this->seeJsonDoesntContains(['id' => $this->coordinatorNoteOne_m1->id]);
        $this->seeJsonContains(['id' => $this->coordinatorNoteTwo_m2->id]);
        $this->seeJsonContains(['id' => $this->coordinatorNoteThree_m1->id]);
    }
    public function test_showAll_orderASCByCreatedTime()
    {
        $this->coordinatorNoteOne_m1->note->createdTime = (new DateTime('-4 months'))->format('Y-m-d H:i:s');
        $this->coordinatorNoteTwo_m2->note->createdTime = (new DateTime('-2 months'))->format('Y-m-d H:i:s');
        $this->coordinatorNoteThree_m1->note->createdTime = (new DateTime('-3 months'))->format('Y-m-d H:i:s');
        
        $this->showAllUri .= "?createdTimeOrder=ASC&pageSize=2";
        $this->showAll();
        
        $this->seeJsonContains(['total' => 3]);
        $this->seeJsonContains(['id' => $this->coordinatorNoteOne_m1->id]);
        $this->seeJsonDoesntContains(['id' => $this->coordinatorNoteTwo_m2->id]);
        $this->seeJsonContains(['id' => $this->coordinatorNoteThree_m1->id]);
    }
    public function test_showAll_useCoordinatorIdFilter()
    {
        $this->showAllUri .= "?coordinatorId={$this->coordinatorOne_p1->id}";
        $this->showAll();
        
        $this->seeJsonContains(['total' => 2]);
        $this->seeJsonContains(['id' => $this->coordinatorNoteOne_m1->id]);
        $this->seeJsonDoesntContains(['id' => $this->coordinatorNoteTwo_m2->id]);
        $this->seeJsonContains(['id' => $this->coordinatorNoteThree_m1->id]);
    }
    
}
