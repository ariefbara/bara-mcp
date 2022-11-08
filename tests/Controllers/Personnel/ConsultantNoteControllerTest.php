<?php

namespace Tests\Controllers\Personnel;

use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfConsultantNote;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\RecordOfUser;
use Tests\Controllers\RecordPreparation\Shared\RecordOfNote;
use Tests\Controllers\RecordPreparation\User\RecordOfUserParticipant;

class ConsultantNoteControllerTest extends ExtendedPersonnelTestCase
{
    protected $showAllUri;
    //
    protected $mentorOne_p1;
    protected $mentorTwo_p2;
    
    protected $clientParticipantOne_p1;
    protected $teamParticipantOne_p2;
    protected $userParticipantOne_p1;
    
    protected $consultantNoteOne_m1;
    protected $consultantNoteTwo_m2;
    protected $consultantNoteThree_m1;
    //
    protected $submitNoteRequest;
    protected $updateNoteRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->showAllUri = $this->personnelUri . "/consultant-notes";
        //
        $this->connection->table('Program')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        $this->connection->table('Note')->truncate();
        $this->connection->table('ConsultantNote')->truncate();
        
        //
        $firm = $this->personnel->firm;
        
        $programOne = new RecordOfProgram($firm, 1);
        $programTwo = new RecordOfProgram($firm, 2);
        
        $this->mentorOne_p1 = new RecordOfConsultant($programOne, $this->personnel, 1);
        $this->mentorTwo_p2 = new RecordOfConsultant($programTwo, $this->personnel, 2);
        
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
        
        $this->consultantNoteOne_m1 = new RecordOfConsultantNote($noteOne, $this->mentorOne_p1, $this->clientParticipantOne_p1->participant);
        $this->consultantNoteTwo_m2 = new RecordOfConsultantNote($noteTwo, $this->mentorTwo_p2, $this->teamParticipantOne_p2->participant);
        $this->consultantNoteThree_m1 = new RecordOfConsultantNote($noteThree, $this->mentorOne_p1, $this->userParticipantOne_p1->participant);
        
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
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        $this->connection->table('Note')->truncate();
        $this->connection->table('ConsultantNote')->truncate();
    }
    
    //
    protected function submit()
    {
        $this->persistPersonnelDependency();
        
        $this->mentorOne_p1->program->insert($this->connection);
        $this->mentorOne_p1->insert($this->connection);
        
        $this->clientParticipantOne_p1->client->insert($this->connection);
        $this->clientParticipantOne_p1->insert($this->connection);
        
        $uri = $this->personnelUri . "/mentors/{$this->mentorOne_p1->id}/consultant-notes";
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
            'consultant' => [
                'id' => $this->mentorOne_p1->id,
                'program' => [
                    'id' => $this->mentorOne_p1->program->id,
                    'name' => $this->mentorOne_p1->program->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
        
        $consultantNoteEntry = [
            'Consultant_id' => $this->mentorOne_p1->id,
            'Participant_id' => $this->clientParticipantOne_p1->participant->id,
            'viewableByParticipant' => $this->submitNoteRequest['viewableByParticipant'],
        ];
        $this->seeInDatabase('ConsultantNote', $consultantNoteEntry);
        
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
        $this->mentorOne_p1->active = false;
        
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
        
        $this->mentorOne_p1->program->insert($this->connection);
        $this->mentorOne_p1->insert($this->connection);
        
        $this->consultantNoteOne_m1->insert($this->connection);
        
        $uri = $this->personnelUri . "/mentors/{$this->mentorOne_p1->id}/consultant-notes/{$this->consultantNoteOne_m1->id}/update";
        $this->patch($uri, $this->updateNoteRequest, $this->personnel->token);
    }
    public function test_update_200()
    {
$this->disableExceptionHandling();
        $this->update();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->consultantNoteOne_m1->id,
            'content' => $this->updateNoteRequest['content'],
            'createdTime' => $this->consultantNoteOne_m1->note->createdTime,
            'modifiedTime' => $this->currentTimeString(),
            'viewableByParticipant' => $this->consultantNoteOne_m1->viewableByParticipant,
            'participant' => [
                'id' => $this->consultantNoteOne_m1->participant->id,
                'client' => [
                    'id' => $this->clientParticipantOne_p1->client->id,
                    'name' => $this->clientParticipantOne_p1->client->getFullName(),
                ],
                'team' => null,
                'user' => null,
            ],
            'consultant' => [
                'id' => $this->consultantNoteOne_m1->consultant->id,
                'program' => [
                    'id' => $this->consultantNoteOne_m1->consultant->program->id,
                    'name' => $this->consultantNoteOne_m1->consultant->program->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
        
        $noteEntry = [
            'id' => $this->consultantNoteOne_m1->id,
            'content' => $this->updateNoteRequest['content'],
            'createdTime' => $this->consultantNoteOne_m1->note->createdTime,
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
        $this->updateNoteRequest['content'] = $this->consultantNoteOne_m1->note->content;
        
        $this->update();
        $this->seeStatusCode(200);
        
        $noteEntry = [
            'id' => $this->consultantNoteOne_m1->note->id,
            'modifiedTime' => $this->consultantNoteOne_m1->note->modifiedTime,
        ];
    }
    public function test_update_unmanagedNote_inactiveMentor_403()
    {
        $this->consultantNoteOne_m1->consultant->active = false;
        
        $this->update();
        $this->seeStatusCode(403);
    }
    public function test_update_unmanagedNote_notOwnedByMentor_403()
    {
        $program = $this->mentorOne_p1->program;
        $otherMentor = new RecordOfConsultant($program, $this->personnel, 'other');
        $otherMentor->insert($this->connection);
        
        $this->consultantNoteOne_m1->consultant = $otherMentor;
        
        $this->update();
        $this->seeStatusCode(403);
    }
    public function test_update_removedNote_404()
    {
        $this->consultantNoteOne_m1->note->removed = true;
        
        $this->update();
        $this->seeStatusCode(404);
    }
    
    //
    protected function hideFromParticipant()
    {
        $this->persistPersonnelDependency();
        
        $this->clientParticipantOne_p1->client->insert($this->connection);
        $this->clientParticipantOne_p1->insert($this->connection);
        
        $this->mentorOne_p1->program->insert($this->connection);
        $this->mentorOne_p1->insert($this->connection);
        
        $this->consultantNoteOne_m1->insert($this->connection);
        
        $uri = $this->personnelUri . "/mentors/{$this->mentorOne_p1->id}/consultant-notes/{$this->consultantNoteOne_m1->id}/hide-from-participant";
        $this->patch($uri, [], $this->personnel->token);
    }
    public function test_hideFromParticipant_200()
    {
$this->disableExceptionHandling();
        $this->hideFromParticipant();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->consultantNoteOne_m1->id,
            'content' => $this->consultantNoteOne_m1->note->content,
            'createdTime' => $this->consultantNoteOne_m1->note->createdTime,
            'modifiedTime' => $this->consultantNoteOne_m1->note->createdTime,
            'viewableByParticipant' => false,
            'participant' => [
                'id' => $this->consultantNoteOne_m1->participant->id,
                'client' => [
                    'id' => $this->clientParticipantOne_p1->client->id,
                    'name' => $this->clientParticipantOne_p1->client->getFullName(),
                ],
                'team' => null,
                'user' => null,
            ],
            'consultant' => [
                'id' => $this->consultantNoteOne_m1->consultant->id,
                'program' => [
                    'id' => $this->consultantNoteOne_m1->consultant->program->id,
                    'name' => $this->consultantNoteOne_m1->consultant->program->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
        
        $consultantNoteEntry = [
            'id' => $this->consultantNoteOne_m1->id,
            'viewableByParticipant' => false,
        ];
        $this->seeInDatabase('ConsultantNote', $consultantNoteEntry);
    }
    public function test_hideFromParticipant_unmanagedNote_inactiveMentor_403()
    {
        $this->consultantNoteOne_m1->consultant->active = false;
        
        $this->hideFromParticipant();
        $this->seeStatusCode(403);
    }
    public function test_hideFromParticipant_unmanagedNote_notOwnedByMentor_403()
    {
        $program = $this->mentorOne_p1->program;
        $otherMentor = new RecordOfConsultant($program, $this->personnel, 'other');
        $otherMentor->insert($this->connection);
        
        $this->consultantNoteOne_m1->consultant = $otherMentor;
        
        $this->hideFromParticipant();
        $this->seeStatusCode(403);
    }
    public function test_hideFromParticipant_removedNote_404()
    {
        $this->consultantNoteOne_m1->note->removed = true;
        
        $this->hideFromParticipant();
        $this->seeStatusCode(404);
    }
    
    //
    protected function showToParticipant()
    {
        $this->persistPersonnelDependency();
        
        $this->clientParticipantOne_p1->client->insert($this->connection);
        $this->clientParticipantOne_p1->insert($this->connection);
        
        $this->mentorOne_p1->program->insert($this->connection);
        $this->mentorOne_p1->insert($this->connection);
        
        $this->consultantNoteOne_m1->viewableByParticipant = false;
        $this->consultantNoteOne_m1->insert($this->connection);
        
        $uri = $this->personnelUri . "/mentors/{$this->mentorOne_p1->id}/consultant-notes/{$this->consultantNoteOne_m1->id}/show-to-participant";
        $this->patch($uri, [], $this->personnel->token);
    }
    public function test_showToParticipant_200()
    {
$this->disableExceptionHandling();
        $this->showToParticipant();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->consultantNoteOne_m1->id,
            'content' => $this->consultantNoteOne_m1->note->content,
            'createdTime' => $this->consultantNoteOne_m1->note->createdTime,
            'modifiedTime' => $this->consultantNoteOne_m1->note->createdTime,
            'viewableByParticipant' => true,
            'participant' => [
                'id' => $this->consultantNoteOne_m1->participant->id,
                'client' => [
                    'id' => $this->clientParticipantOne_p1->client->id,
                    'name' => $this->clientParticipantOne_p1->client->getFullName(),
                ],
                'team' => null,
                'user' => null,
            ],
            'consultant' => [
                'id' => $this->consultantNoteOne_m1->consultant->id,
                'program' => [
                    'id' => $this->consultantNoteOne_m1->consultant->program->id,
                    'name' => $this->consultantNoteOne_m1->consultant->program->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
        
        $consultantNoteEntry = [
            'id' => $this->consultantNoteOne_m1->id,
            'viewableByParticipant' => true,
        ];
        $this->seeInDatabase('ConsultantNote', $consultantNoteEntry);
    }
    public function test_showToParticipant_unmanagedNote_inactiveMentor_403()
    {
        $this->consultantNoteOne_m1->consultant->active = false;
        
        $this->showToParticipant();
        $this->seeStatusCode(403);
    }
    public function test_showToParticipant_unmanagedNote_notOwnedByMentor_403()
    {
        $program = $this->mentorOne_p1->program;
        $otherMentor = new RecordOfConsultant($program, $this->personnel, 'other');
        $otherMentor->insert($this->connection);
        
        $this->consultantNoteOne_m1->consultant = $otherMentor;
        
        $this->showToParticipant();
        $this->seeStatusCode(403);
    }
    public function test_showToParticipant_removedNote_404()
    {
        $this->consultantNoteOne_m1->note->removed = true;
        
        $this->showToParticipant();
        $this->seeStatusCode(404);
    }
    
    //
    protected function remove()
    {
        $this->persistPersonnelDependency();
        
        $this->clientParticipantOne_p1->client->insert($this->connection);
        $this->clientParticipantOne_p1->insert($this->connection);
        
        $this->mentorOne_p1->program->insert($this->connection);
        $this->mentorOne_p1->insert($this->connection);
        
        $this->consultantNoteOne_m1->insert($this->connection);
        
        $uri = $this->personnelUri . "/mentors/{$this->mentorOne_p1->id}/consultant-notes/{$this->consultantNoteOne_m1->id}";
        $this->delete($uri, [], $this->personnel->token);
    }
    public function test_remove_200()
    {
$this->disableExceptionHandling();
        $this->remove();
        $this->seeStatusCode(200);
        
        $noteEntry = [
            'id' => $this->consultantNoteOne_m1->note->id,
            'removed' => true
        ];
        $this->seeInDatabase('Note', $noteEntry);
    }
    public function test_remove_unmanagedNote_inactiveMentor_403()
    {
        $this->consultantNoteOne_m1->consultant->active = false;
        
        $this->remove();
        $this->seeStatusCode(403);
    }
    public function test_remove_unmanagedNote_notOwnedByMentor_403()
    {
        $program = $this->mentorOne_p1->program;
        $otherMentor = new RecordOfConsultant($program, $this->personnel, 'other');
        $otherMentor->insert($this->connection);
        
        $this->consultantNoteOne_m1->consultant = $otherMentor;
        
        $this->remove();
        $this->seeStatusCode(403);
    }
    public function test_remove_removedNote_404()
    {
        $this->consultantNoteOne_m1->note->removed = true;
        
        $this->remove();
        $this->seeStatusCode(404);
    }
    
    //
    protected function show()
    {
        $this->persistPersonnelDependency();
        
        $this->clientParticipantOne_p1->client->insert($this->connection);
        $this->clientParticipantOne_p1->insert($this->connection);
        
        $this->mentorOne_p1->program->insert($this->connection);
        $this->mentorOne_p1->insert($this->connection);
        
        $this->consultantNoteOne_m1->insert($this->connection);
        
        $uri = $this->personnelUri . "/consultant-notes/{$this->consultantNoteOne_m1->id}";
        $this->get($uri, $this->personnel->token);
    }
    public function test_show_200()
    {
$this->disableExceptionHandling();
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->consultantNoteOne_m1->id,
            'content' => $this->consultantNoteOne_m1->note->content,
            'createdTime' => $this->consultantNoteOne_m1->note->createdTime,
            'modifiedTime' => $this->consultantNoteOne_m1->note->createdTime,
            'viewableByParticipant' => $this->consultantNoteOne_m1->viewableByParticipant,
            'participant' => [
                'id' => $this->consultantNoteOne_m1->participant->id,
                'client' => [
                    'id' => $this->clientParticipantOne_p1->client->id,
                    'name' => $this->clientParticipantOne_p1->client->getFullName(),
                ],
                'team' => null,
                'user' => null,
            ],
            'consultant' => [
                'id' => $this->consultantNoteOne_m1->consultant->id,
                'program' => [
                    'id' => $this->consultantNoteOne_m1->consultant->program->id,
                    'name' => $this->consultantNoteOne_m1->consultant->program->name,
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
        
        $program = $this->mentorOne_p1->program;
        $otherMentor = new RecordOfConsultant($program, $otherPersonnel, 'other');
        $otherMentor->insert($this->connection);
        
        $this->consultantNoteOne_m1->consultant = $otherMentor;
        
        $this->show();
        $this->seeStatusCode(404);
    }
    public function test_show_removedNote_404()
    {
        $this->consultantNoteOne_m1->note->removed = true;
        
        $this->show();
        $this->seeStatusCode(404);
    }
    
    //
    protected function showAll()
    {
        $this->persistPersonnelDependency();
        
        $this->mentorOne_p1->program->insert($this->connection);
        $this->mentorTwo_p2->program->insert($this->connection);
        
        $this->mentorOne_p1->insert($this->connection);
        $this->mentorTwo_p2->insert($this->connection);
        
        $this->clientParticipantOne_p1->client->insert($this->connection);
        $this->clientParticipantOne_p1->insert($this->connection);
        
        $this->teamParticipantOne_p2->team->insert($this->connection);
        $this->teamParticipantOne_p2->insert($this->connection);
        
        $this->userParticipantOne_p1->user->insert($this->connection);
        $this->userParticipantOne_p1->insert($this->connection);
        
        $this->consultantNoteOne_m1->insert($this->connection);
        $this->consultantNoteTwo_m2->insert($this->connection);
        $this->consultantNoteThree_m1->insert($this->connection);
        
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
                    'id' => $this->consultantNoteOne_m1->id,
                    'content' => $this->consultantNoteOne_m1->note->content,
                    'createdTime' => $this->consultantNoteOne_m1->note->createdTime,
                    'modifiedTime' => $this->consultantNoteOne_m1->note->createdTime,
                    'viewableByParticipant' => $this->consultantNoteOne_m1->viewableByParticipant,
                    'participant' => [
                        'id' => $this->consultantNoteOne_m1->participant->id,
                        'client' => [
                            'id' => $this->clientParticipantOne_p1->client->id,
                            'name' => $this->clientParticipantOne_p1->client->getFullName(),
                        ],
                        'team' => null,
                        'user' => null,
                    ],
                    'consultant' => [
                        'id' => $this->consultantNoteOne_m1->consultant->id,
                        'program' => [
                            'id' => $this->consultantNoteOne_m1->consultant->program->id,
                            'name' => $this->consultantNoteOne_m1->consultant->program->name,
                        ],
                    ],
                ],
                [
                    'id' => $this->consultantNoteTwo_m2->id,
                    'content' => $this->consultantNoteTwo_m2->note->content,
                    'createdTime' => $this->consultantNoteTwo_m2->note->createdTime,
                    'modifiedTime' => $this->consultantNoteTwo_m2->note->createdTime,
                    'viewableByParticipant' => $this->consultantNoteTwo_m2->viewableByParticipant,
                    'participant' => [
                        'id' => $this->consultantNoteTwo_m2->participant->id,
                        'client' => null,
                        'team' => [
                            'id' => $this->teamParticipantOne_p2->team->id,
                            'name' => $this->teamParticipantOne_p2->team->name,
                        ],
                        'user' => null,
                    ],
                    'consultant' => [
                        'id' => $this->consultantNoteTwo_m2->consultant->id,
                        'program' => [
                            'id' => $this->consultantNoteTwo_m2->consultant->program->id,
                            'name' => $this->consultantNoteTwo_m2->consultant->program->name,
                        ],
                    ],
                ],
                [
                    'id' => $this->consultantNoteThree_m1->id,
                    'content' => $this->consultantNoteThree_m1->note->content,
                    'createdTime' => $this->consultantNoteThree_m1->note->createdTime,
                    'modifiedTime' => $this->consultantNoteThree_m1->note->createdTime,
                    'viewableByParticipant' => $this->consultantNoteThree_m1->viewableByParticipant,
                    'participant' => [
                        'id' => $this->consultantNoteThree_m1->participant->id,
                        'client' => null,
                        'team' => null,
                        'user' => [
                            'id' => $this->userParticipantOne_p1->user->id,
                            'name' => $this->userParticipantOne_p1->user->getFullName(),
                        ],
                    ],
                    'consultant' => [
                        'id' => $this->consultantNoteThree_m1->consultant->id,
                        'program' => [
                            'id' => $this->consultantNoteThree_m1->consultant->program->id,
                            'name' => $this->consultantNoteThree_m1->consultant->program->name,
                        ],
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_excludeRemovedNote()
    {
        $this->consultantNoteOne_m1->note->removed = true;
        $this->showAll();
        
        $this->seeJsonContains(['total' => 2]);
        $this->seeJsonDoesntContains(['id' => $this->consultantNoteOne_m1->id]);
        $this->seeJsonContains(['id' => $this->consultantNoteTwo_m2->id]);
        $this->seeJsonContains(['id' => $this->consultantNoteThree_m1->id]);
    }
    public function test_showAll_orderDESCByModifiedTime()
    {
        $this->consultantNoteOne_m1->note->modifiedTime = (new \DateTime('-4 months'))->format('Y-m-d H:i:s');
        $this->consultantNoteTwo_m2->note->modifiedTime = (new \DateTime('-2 months'))->format('Y-m-d H:i:s');
        $this->consultantNoteThree_m1->note->modifiedTime = (new \DateTime('-3 months'))->format('Y-m-d H:i:s');
        
        $this->showAllUri .= "?modifiedTimeOrder=DESC&pageSize=2";
        $this->showAll();
        
        $this->seeJsonContains(['total' => 3]);
        $this->seeJsonDoesntContains(['id' => $this->consultantNoteOne_m1->id]);
        $this->seeJsonContains(['id' => $this->consultantNoteTwo_m2->id]);
        $this->seeJsonContains(['id' => $this->consultantNoteThree_m1->id]);
    }
    public function test_showAll_orderASCByModifiedTime()
    {
        $this->consultantNoteOne_m1->note->modifiedTime = (new \DateTime('-4 months'))->format('Y-m-d H:i:s');
        $this->consultantNoteTwo_m2->note->modifiedTime = (new \DateTime('-2 months'))->format('Y-m-d H:i:s');
        $this->consultantNoteThree_m1->note->modifiedTime = (new \DateTime('-3 months'))->format('Y-m-d H:i:s');
        
        $this->showAllUri .= "?modifiedTimeOrder=ASC&pageSize=2";
        $this->showAll();
        
        $this->seeJsonContains(['total' => 3]);
        $this->seeJsonContains(['id' => $this->consultantNoteOne_m1->id]);
        $this->seeJsonDoesntContains(['id' => $this->consultantNoteTwo_m2->id]);
        $this->seeJsonContains(['id' => $this->consultantNoteThree_m1->id]);
    }
    public function test_showAll_orderDESCByCreatedTime()
    {
        $this->consultantNoteOne_m1->note->createdTime = (new \DateTime('-4 months'))->format('Y-m-d H:i:s');
        $this->consultantNoteTwo_m2->note->createdTime = (new \DateTime('-2 months'))->format('Y-m-d H:i:s');
        $this->consultantNoteThree_m1->note->createdTime = (new \DateTime('-3 months'))->format('Y-m-d H:i:s');
        
        $this->showAllUri .= "?createdTimeOrder=DESC&pageSize=2";
        $this->showAll();
        
        $this->seeJsonContains(['total' => 3]);
        $this->seeJsonDoesntContains(['id' => $this->consultantNoteOne_m1->id]);
        $this->seeJsonContains(['id' => $this->consultantNoteTwo_m2->id]);
        $this->seeJsonContains(['id' => $this->consultantNoteThree_m1->id]);
    }
    public function test_showAll_orderASCByCreatedTime()
    {
        $this->consultantNoteOne_m1->note->createdTime = (new \DateTime('-4 months'))->format('Y-m-d H:i:s');
        $this->consultantNoteTwo_m2->note->createdTime = (new \DateTime('-2 months'))->format('Y-m-d H:i:s');
        $this->consultantNoteThree_m1->note->createdTime = (new \DateTime('-3 months'))->format('Y-m-d H:i:s');
        
        $this->showAllUri .= "?createdTimeOrder=ASC&pageSize=2";
        $this->showAll();
        
        $this->seeJsonContains(['total' => 3]);
        $this->seeJsonContains(['id' => $this->consultantNoteOne_m1->id]);
        $this->seeJsonDoesntContains(['id' => $this->consultantNoteTwo_m2->id]);
        $this->seeJsonContains(['id' => $this->consultantNoteThree_m1->id]);
    }
    public function test_showAll_useConsultantIdFilter()
    {
        $this->showAllUri .= "?consultantId={$this->mentorOne_p1->id}";
        $this->showAll();
        
        $this->seeJsonContains(['total' => 2]);
        $this->seeJsonContains(['id' => $this->consultantNoteOne_m1->id]);
        $this->seeJsonDoesntContains(['id' => $this->consultantNoteTwo_m2->id]);
        $this->seeJsonContains(['id' => $this->consultantNoteThree_m1->id]);
    }
    
}
