<?php

namespace Tests\Controllers\Personnel;

use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfConsultantComment;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfWorksheet;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\Worksheet\RecordOfComment;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\RecordOfWorksheetForm;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\RecordOfUser;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;
use Tests\Controllers\RecordPreparation\User\RecordOfUserParticipant;

class UncommentedWorksheetListInCoordinatedProgramsControllerTest extends AggregatedCoordinatorInPersonnelContextTestCase
{
    protected $viewAllUri;
    protected $clientParticipantOne_prog1;
    protected $teamParticipantTwo_prog2;
    protected $userParticipantThree_prog1;
    protected $worksheetOne_p1;
    protected $worksheetTwo_p2;
    protected $worksheetThree_p3;
    protected $consultantCommentOne;

    protected function setUp(): void
    {
        parent::setUp();
        $this->viewAllUri = $this->personnelUri . "/uncommented-worksheet-list-in-coordinated-programs";
        
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('Worksheet')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Comment')->truncate();
        $this->connection->table('ConsultantComment')->truncate();
        
        $firm = $this->personnel->firm;
        $programOne = $this->coordinatorOne->program;
        $programTwo = $this->coordinatorTwo->program;

        $clientOne = new RecordOfClient($firm, 1);

        $teamOne = new RecordOfTeam($firm, $clientOne, 1);

        $userOne = new RecordOfUser(1);

        $participantOne = new RecordOfParticipant($programOne, 1);
        $this->clientParticipantOne_prog1 = new RecordOfClientParticipant($clientOne, $participantOne);

        $participantTwo = new RecordOfParticipant($programTwo, 2);
        $this->teamParticipantTwo_prog2 = new RecordOfTeamProgramParticipation($teamOne, $participantTwo);

        $participantThree = new RecordOfParticipant($programOne, 3);
        $this->userParticipantThree_prog1 = new RecordOfUserParticipant($userOne, $participantThree);

        $formOne = new RecordOfForm(1);
        $worksheetFormOne = new RecordOfWorksheetForm($firm, $formOne);
        $formTwo = new RecordOfForm(2);
        $worksheetFormTwo = new RecordOfWorksheetForm($firm, $formTwo);

        $missionOne = new RecordOfMission($programOne, $worksheetFormOne, 1, null);
        $missionTwo = new RecordOfMission($programTwo, $worksheetFormTwo, 2, null);

        $formRecordOne = new RecordOfFormRecord($formOne, 1);
        $this->worksheetOne_p1 = new RecordOfWorksheet($participantOne, $formRecordOne, $missionOne, 1);
        $formRecordTwo = new RecordOfFormRecord($formTwo, 2);
        $this->worksheetTwo_p2 = new RecordOfWorksheet($participantTwo, $formRecordTwo, $missionTwo, 2);
        $formRecordThree = new RecordOfFormRecord($formOne, 3);
        $this->worksheetThree_p3 = new RecordOfWorksheet($participantThree, $formRecordThree, $missionOne, 3);

        
        $personnelOne = new RecordOfPersonnel($firm, 1);
        
        $consultantOne = new RecordOfConsultant($programTwo, $personnelOne, 1);

        $commentOne = new RecordOfComment($this->worksheetTwo_p2, 1);
        $this->consultantCommentOne = new RecordOfConsultantComment($consultantOne, $commentOne);
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('Worksheet')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Comment')->truncate();
        $this->connection->table('ConsultantComment')->truncate();
    }
    
    protected function viewAll()
    {
        $this->persistAggregatedCoordinatorDependency();
        
        $this->clientParticipantOne_prog1->client->insert($this->connection);
        $this->teamParticipantTwo_prog2->team->insert($this->connection);
        $this->userParticipantThree_prog1->user->insert($this->connection);
        
        $this->clientParticipantOne_prog1->insert($this->connection);
        $this->teamParticipantTwo_prog2->insert($this->connection);
        $this->userParticipantThree_prog1->insert($this->connection);
        
        $this->worksheetOne_p1->mission->worksheetForm->form->insert($this->connection);
        $this->worksheetTwo_p2->mission->worksheetForm->form->insert($this->connection);
        
        $this->worksheetOne_p1->mission->worksheetForm->insert($this->connection);
        $this->worksheetTwo_p2->mission->worksheetForm->insert($this->connection);
        
        $this->worksheetOne_p1->mission->insert($this->connection);
        $this->worksheetTwo_p2->mission->insert($this->connection);
        
        $this->worksheetOne_p1->insert($this->connection);
        $this->worksheetTwo_p2->insert($this->connection);
        $this->worksheetThree_p3->insert($this->connection);
        
        $this->get($this->viewAllUri, $this->personnel->token);
    }
    public function test_viewAll_200()
    {
$this->disableExceptionHandling();
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '3',
            'list' => [
                [
                    'id' => $this->worksheetOne_p1->id,
                    'name' => $this->worksheetOne_p1->name,
                    'submitTime' => $this->worksheetOne_p1->formRecord->submitTime,
                    'participantId' => $this->worksheetOne_p1->participant->id,
                    'participantName' => $this->clientParticipantOne_prog1->client->getFullName(),
                    'missionId' => $this->worksheetOne_p1->mission->id,
                    'missionName' => $this->worksheetOne_p1->mission->name,
                    'coordinatorId' => $this->coordinatorOne->id,
                    'programId' => $this->worksheetOne_p1->participant->program->id,
                    'programName' => $this->worksheetOne_p1->participant->program->name,
                ],
                [
                    'id' => $this->worksheetTwo_p2->id,
                    'name' => $this->worksheetTwo_p2->name,
                    'submitTime' => $this->worksheetTwo_p2->formRecord->submitTime,
                    'participantId' => $this->worksheetTwo_p2->participant->id,
                    'participantName' => $this->teamParticipantTwo_prog2->team->name,
                    'missionId' => $this->worksheetTwo_p2->mission->id,
                    'missionName' => $this->worksheetTwo_p2->mission->name,
                    'coordinatorId' => $this->coordinatorTwo->id,
                    'programId' => $this->worksheetTwo_p2->participant->program->id,
                    'programName' => $this->worksheetTwo_p2->participant->program->name,
                ],
                [
                    'id' => $this->worksheetThree_p3->id,
                    'name' => $this->worksheetThree_p3->name,
                    'submitTime' => $this->worksheetThree_p3->formRecord->submitTime,
                    'participantId' => $this->worksheetThree_p3->participant->id,
                    'participantName' => $this->userParticipantThree_prog1->user->getFullName(),
                    'missionId' => $this->worksheetThree_p3->mission->id,
                    'missionName' => $this->worksheetThree_p3->mission->name,
                    'coordinatorId' => $this->coordinatorOne->id,
                    'programId' => $this->worksheetThree_p3->participant->program->id,
                    'programName' => $this->worksheetThree_p3->participant->program->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewAll_excludeWorksheetCommentedByMentor_200()
    {
        $consultantOne = new RecordOfConsultant($this->coordinatorOne->program, $this->personnel, 1);
        $consultantOne->insert($this->connection);
        $commentOne = new RecordOfComment($this->worksheetTwo_p2, 1);
        $consultantComment = new RecordOfConsultantComment($consultantOne, $commentOne);
        $consultantComment->insert($this->connection);
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonContains(['id' => $this->worksheetOne_p1->id]);
        $this->seeJsonDoesntContains(['id' => $this->worksheetTwo_p2->id]);
        $this->seeJsonContains(['id' => $this->worksheetThree_p3->id]);
    }
    public function test_viewAll_includeWorksheetCommentedByParticipantOnly_200()
    {
        $commentOne = new RecordOfComment($this->worksheetTwo_p2, 1);
        $commentOne->insert($this->connection);
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '3']);
        $this->seeJsonContains(['id' => $this->worksheetOne_p1->id]);
        $this->seeJsonContains(['id' => $this->worksheetTwo_p2->id]);
        $this->seeJsonContains(['id' => $this->worksheetThree_p3->id]);
    }
    public function test_viewAll_includeUnmanagedWorksheet_removed_200()
    {
        $this->worksheetThree_p3->removed = true;
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonContains(['id' => $this->worksheetOne_p1->id]);
        $this->seeJsonContains(['id' => $this->worksheetTwo_p2->id]);
        $this->seeJsonDoesntContains(['id' => $this->worksheetThree_p3->id]);
    }
    public function test_viewAll_includeUnmanagedWorksheet_belongsToInactiveParticipant_200()
    {
        $this->worksheetThree_p3->participant->active = false;
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonContains(['id' => $this->worksheetOne_p1->id]);
        $this->seeJsonContains(['id' => $this->worksheetTwo_p2->id]);
        $this->seeJsonDoesntContains(['id' => $this->worksheetThree_p3->id]);
    }
    public function test_viewAll_includeUnmanagedWorksheet_inUncoordinatedProgram_200()
    {
        $this->coordinatorTwo->active = false;
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonContains(['id' => $this->worksheetOne_p1->id]);
        $this->seeJsonDoesntContains(['id' => $this->worksheetTwo_p2->id]);
        $this->seeJsonContains(['id' => $this->worksheetThree_p3->id]);
    }

}
