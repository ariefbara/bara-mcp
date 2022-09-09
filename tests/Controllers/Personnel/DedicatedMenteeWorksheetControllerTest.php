<?php

namespace Tests\Controllers\Personnel;

use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfConsultantComment;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfDedicatedMentor;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfWorksheet;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\Worksheet\RecordOfComment;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\RecordOfWorksheetForm;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class DedicatedMenteeWorksheetControllerTest extends PersonnelTestCase
{
    protected $worksheetOne;
    protected $worksheetTwo;
    protected $clientParticipantOne;
    protected $teamParticipantTwo;
    protected $dedicatedMentorOne;
    protected $dedicatedMentorTwo;
    protected $viewAllUncommentedUri;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Team')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('DedicatedMentor')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('Worksheet')->truncate();
        $this->connection->table('Comment')->truncate();
        $this->connection->table('ConsultantComment')->truncate();
        
        $firm = $this->personnel->firm;
        
        $programOne = new RecordOfProgram($firm, '1');
        $programTwo = new RecordOfProgram($firm, '2');
        
        $mentorOne = new RecordOfConsultant($programOne, $this->personnel, '1');
        $mentorTwo = new RecordOfConsultant($programTwo, $this->personnel, '2');
        
        $participantOne = new RecordOfParticipant($programOne, '1');
        $participantTwo = new RecordOfParticipant($programTwo, '2');
        
        $clientOne = new RecordOfClient($firm, '1');
        $this->clientParticipantOne = new RecordOfClientParticipant($clientOne, $participantOne);
        
        $teamOne = new RecordOfTeam($firm, $clientOne, '1');
        $this->teamParticipantTwo = new RecordOfTeamProgramParticipation($teamOne, $participantTwo);
        
        $this->dedicatedMentorOne = new RecordOfDedicatedMentor($participantOne, $mentorOne, '1');
        $this->dedicatedMentorTwo = new RecordOfDedicatedMentor($participantTwo, $mentorTwo, '2');
        
        $formOne = new RecordOfForm('1');
        $formTwo = new RecordOfForm('2');
        
        $formRecordOne = new RecordOfFormRecord($formOne, '1');
        $formRecordTwo = new RecordOfFormRecord($formTwo, '2');
        
        $worksheetFormOne = new RecordOfWorksheetForm($firm, $formOne);
        $worksheetFormTwo = new RecordOfWorksheetForm($firm, $formTwo);
        
        $missionOne = new RecordOfMission($programOne, $worksheetFormOne, '1', null);
        $missionTwo = new RecordOfMission($programTwo, $worksheetFormTwo, '2', null);
        
        $this->worksheetOne = new RecordOfWorksheet($participantOne, $formRecordOne, $missionOne, '1');
        $this->worksheetTwo = new RecordOfWorksheet($participantTwo, $formRecordTwo, $missionTwo, '2');
        
        $this->viewAllUncommentedUri = $this->personnelUri . "/dedicated-mentee-worksheets/all-uncommented";
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Team')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('DedicatedMentor')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('Worksheet')->truncate();
        $this->connection->table('Comment')->truncate();
        $this->connection->table('ConsultantComment')->truncate();
    }
    
    protected function viewAllUncommented()
    {
        $this->dedicatedMentorOne->consultant->program->insert($this->connection);
        $this->dedicatedMentorTwo->consultant->program->insert($this->connection);
        
        $this->dedicatedMentorOne->consultant->insert($this->connection);
        $this->dedicatedMentorTwo->consultant->insert($this->connection);
        
        $this->clientParticipantOne->client->insert($this->connection);
        $this->teamParticipantTwo->team->insert($this->connection);
        
        $this->clientParticipantOne->insert($this->connection);
        $this->teamParticipantTwo->insert($this->connection);
        
        $this->dedicatedMentorOne->insert($this->connection);
        $this->dedicatedMentorTwo->insert($this->connection);
        
        $this->worksheetOne->mission->worksheetForm->form->insert($this->connection);
        $this->worksheetTwo->mission->worksheetForm->form->insert($this->connection);
        
        $this->worksheetOne->mission->worksheetForm->insert($this->connection);
        $this->worksheetTwo->mission->worksheetForm->insert($this->connection);
        
        $this->worksheetOne->mission->insert($this->connection);
        $this->worksheetTwo->mission->insert($this->connection);
        
        $this->worksheetOne->insert($this->connection);
        $this->worksheetTwo->insert($this->connection);
        
        $this->get($this->viewAllUncommentedUri, $this->personnel->token);
    }
    public function test_viewAllUncommented_200()
    {
$this->disableExceptionHandling();
        $this->viewAllUncommented();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 2,
            'list' => [
                [
                    'id' => $this->worksheetOne->id,
                    'name' => $this->worksheetOne->name,
                    'mission' => [
                        'id' => $this->worksheetOne->mission->id,
                        'name' => $this->worksheetOne->mission->name,
                    ],
                    'participant' => [
                        'id' => $this->worksheetOne->participant->id,
                        'client' => [
                            'id' => $this->clientParticipantOne->client->id,
                            'name' => $this->clientParticipantOne->client->getFullName(),
                        ],
                        'team' => null,
                        'user' => null,
                        'program' => [
                            'id' => $this->worksheetOne->participant->program->id,
                            'name' => $this->worksheetOne->participant->program->name,
                        ],
                    ],
                ],
                [
                    'id' => $this->worksheetTwo->id,
                    'name' => $this->worksheetTwo->name,
                    'mission' => [
                        'id' => $this->worksheetTwo->mission->id,
                        'name' => $this->worksheetTwo->mission->name,
                    ],
                    'participant' => [
                        'id' => $this->worksheetTwo->participant->id,
                        'client' => null,
                        'team' => [
                            'id' => $this->teamParticipantTwo->team->id,
                            'name' => $this->teamParticipantTwo->team->name,
                        ],
                        'user' => null,
                        'program' => [
                            'id' => $this->worksheetTwo->participant->program->id,
                            'name' => $this->worksheetTwo->participant->program->name,
                        ],
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewAllUncommented_excludeCommentedWorksheet()
    {
        $comment = new RecordOfComment($this->worksheetOne, '1');
        $mentorComment = new RecordOfConsultantComment($this->dedicatedMentorOne->consultant, $comment);
        $mentorComment->insert($this->connection);
        
        $this->viewAllUncommented();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 1]);
        $this->seeJsonDoesntContains(['id' => $this->worksheetOne->id]);
        $this->seeJsonContains(['id' => $this->worksheetTwo->id]);
    }
    public function test_viewAllUncommented_includeWorksheetCommentedByOtherDedicatedMentor()
    {
        $firm = $this->personnel->firm;
        $program = $this->worksheetOne->participant->program;
        
        $otherPersonnel = new RecordOfPersonnel($firm, 'other');
        $otherPersonnel->insert($this->connection);
        
        $otherConsultant = new RecordOfConsultant($program, $otherPersonnel, 'other');
        
        $dedicatedMentorThree = new RecordOfDedicatedMentor($this->worksheetOne->participant, $otherConsultant, 'other');
        $dedicatedMentorThree->insert($this->connection);
        
        $comment = new RecordOfComment($this->worksheetOne, '1');
        $mentorComment = new RecordOfConsultantComment($otherConsultant, $comment);
        $mentorComment->insert($this->connection);
        
        $this->viewAllUncommented();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 2]);
        $this->seeJsonContains(['id' => $this->worksheetOne->id]);
        $this->seeJsonContains(['id' => $this->worksheetTwo->id]);
    }
    public function test_viewAllUncommented_excludeWorksheetFromUndedicatedMentee()
    {
        $firm = $this->personnel->firm;
        $program = $this->worksheetTwo->participant->program;
        $otherPersonnel = new RecordOfPersonnel($firm, 'other');
        $otherPersonnel->insert($this->connection);
        
        $otherConsultant = new RecordOfConsultant($program, $otherPersonnel, 'other');
        
        $this->dedicatedMentorTwo->consultant = $otherConsultant;
        
        $this->viewAllUncommented();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 1]);
        $this->seeJsonContains(['id' => $this->worksheetOne->id]);
        $this->seeJsonDoesntContains(['id' => $this->worksheetTwo->id]);
    }
    public function test_viewAllUncommented_includeWorksheetFromCancelledDedicatedMentee()
    {
        $this->dedicatedMentorTwo->cancelled = true;
        
        $this->viewAllUncommented();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 1]);
        $this->seeJsonContains(['id' => $this->worksheetOne->id]);
        $this->seeJsonDoesntContains(['id' => $this->worksheetTwo->id]);
    }
    public function test_viewAllUncommented_includeWorksheetFromInactiveMentor()
    {
        $this->dedicatedMentorTwo->consultant->active = false;
        
        $this->viewAllUncommented();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 1]);
        $this->seeJsonContains(['id' => $this->worksheetOne->id]);
        $this->seeJsonDoesntContains(['id' => $this->worksheetTwo->id]);
    }
}
