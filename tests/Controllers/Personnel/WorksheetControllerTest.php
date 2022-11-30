<?php

namespace Tests\Controllers\Personnel;

use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfConsultantComment;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfDedicatedMentor;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfWorksheet;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\Worksheet\RecordOfComment;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\RecordOfWorksheetForm;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\RecordOfUser;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;
use Tests\Controllers\RecordPreparation\User\RecordOfUserParticipant;

class WorksheetControllerTest extends PersonnelTestCase
{

    protected $consultantOne;
    protected $consultantTwo;
    protected $consultantThree;
    //
    protected $coordinatorOne;
    protected $coordinatorTwo;
    protected $coordinatorThree;
    //
    protected $clientParticipantOne;
    protected $teamParticipantTwo;
    protected $userParticipantThree;
    //
    protected $dedicatedMentorThree;
    //
    protected $worksheetOne;
    protected $worksheetTwo;
    protected $worksheetThree;
    //
    protected $consultantCommentTwo;
    //
    protected $viewListInCoordinatedProgramUri;
    protected $viewListInConsultedProgramUri;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Coordinator')->truncate();
        //
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        //
        $this->connection->table('DedicatedMentor')->truncate();
        //
        $this->connection->table('Form')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Mission')->truncate();
        //
        $this->connection->table('Worksheet')->truncate();
        $this->connection->table('Comment')->truncate();
        $this->connection->table('ConsultantComment')->truncate();

        $firm = $this->personnel->firm;

        $programOne = new RecordOfProgram($firm, 1);
        $programTwo = new RecordOfProgram($firm, 2);
        $programThree = new RecordOfProgram($firm, 3);

        $this->consultantOne = new RecordOfConsultant($programOne, $this->personnel, 1);
        $this->consultantTwo = new RecordOfConsultant($programTwo, $this->personnel, 2);
        $this->consultantThree = new RecordOfConsultant($programThree, $this->personnel, 3);

        $this->coordinatorOne = new RecordOfCoordinator($programOne, $this->personnel, 1);
        $this->coordinatorTwo = new RecordOfCoordinator($programTwo, $this->personnel, 2);
        $this->coordinatorThree = new RecordOfCoordinator($programThree, $this->personnel, 3);

        $clientOne = new RecordOfClient($firm, 1);

        $teamOne = new RecordOfTeam($firm, $clientOne, 1);

        $userOne = new RecordOfUser(1);

        $participantOne = new RecordOfParticipant($programOne, 1);
        $participantTwo = new RecordOfParticipant($programTwo, 2);
        $participantThree = new RecordOfParticipant($programThree, 3);

        $this->clientParticipantOne = new RecordOfClientParticipant($clientOne, $participantOne);

        $this->teamParticipantTwo = new RecordOfTeamProgramParticipation($teamOne, $participantTwo);

        $this->userParticipantThree = new RecordOfUserParticipant($userOne, $participantThree);
        
        $this->dedicatedMentorThree = new RecordOfDedicatedMentor($participantThree, $this->consultantThree, 3);

        $formOne = new RecordOfForm(1);
        $formTwo = new RecordOfForm(2);
        $formThree = new RecordOfForm(3);

        $formRecordOne = new RecordOfFormRecord($formOne, 1);
        $formRecordTwo= new RecordOfFormRecord($formTwo, 2);
        $formRecordThree= new RecordOfFormRecord($formThree, 3);
        
        $worksheetFormOne = new RecordOfWorksheetForm($firm, $formOne);
        $worksheetFormTwo = new RecordOfWorksheetForm($firm, $formTwo);
        $worksheetFormThree = new RecordOfWorksheetForm($firm, $formThree);

        $missionOne = new RecordOfMission($programOne, $worksheetFormOne, 1, null);
        $missionTwo = new RecordOfMission($programTwo, $worksheetFormTwo, 2, null);
        $missionThree = new RecordOfMission($programThree, $worksheetFormThree, 3, null);
        
        $this->worksheetOne = new RecordOfWorksheet($participantOne, $formRecordOne, $missionOne, 1);
        $this->worksheetTwo = new RecordOfWorksheet($participantTwo, $formRecordTwo, $missionTwo, 2);
        $this->worksheetThree = new RecordOfWorksheet($participantThree, $formRecordThree, $missionThree, 3);
        
        $commentTwo = new RecordOfComment($this->worksheetTwo, 2);
        
        $this->consultantCommentTwo = new RecordOfConsultantComment($this->consultantTwo, $commentTwo);
        //
        $this->viewListInCoordinatedProgramUri = $this->personnelUri . "/worksheet-list-in-coordinated-programs";
        $this->viewListInConsultedProgramUri = $this->personnelUri . "/worksheet-list-in-consulted-programs";
    }

    protected function tearDown(): void
    {
//        parent::tearDown();
//        $this->connection->table('Program')->truncate();
//        $this->connection->table('Consultant')->truncate();
//        $this->connection->table('Coordinator')->truncate();
//        //
//        $this->connection->table('Client')->truncate();
//        $this->connection->table('Team')->truncate();
//        $this->connection->table('User')->truncate();
//        $this->connection->table('Participant')->truncate();
//        $this->connection->table('ClientParticipant')->truncate();
//        $this->connection->table('TeamParticipant')->truncate();
//        $this->connection->table('UserParticipant')->truncate();
//        //
//        $this->connection->table('DedicatedMentor')->truncate();
//        //
//        $this->connection->table('Form')->truncate();
//        $this->connection->table('FormRecord')->truncate();
//        $this->connection->table('WorksheetForm')->truncate();
//        $this->connection->table('Mission')->truncate();
//        //
//        $this->connection->table('Worksheet')->truncate();
//        $this->connection->table('Comment')->truncate();
//        $this->connection->table('ConsultantComment')->truncate();
    }
    
    protected function viewListInCoordinatedProgram()
    {
        $this->coordinatorOne->program->insert($this->connection);
        $this->coordinatorTwo->program->insert($this->connection);
        $this->coordinatorThree->program->insert($this->connection);
        
        $this->coordinatorOne->insert($this->connection);
        $this->coordinatorTwo->insert($this->connection);
        $this->coordinatorThree->insert($this->connection);
        //
        $this->clientParticipantOne->client->insert($this->connection);
        $this->teamParticipantTwo->team->insert($this->connection);
        $this->userParticipantThree->user->insert($this->connection);
        
        $this->clientParticipantOne->insert($this->connection);
        $this->teamParticipantTwo->insert($this->connection);
        $this->userParticipantThree->insert($this->connection);
        
        $this->worksheetOne->mission->worksheetForm->form->insert($this->connection);
        $this->worksheetTwo->mission->worksheetForm->form->insert($this->connection);
        $this->worksheetThree->mission->worksheetForm->form->insert($this->connection);
        
        $this->worksheetOne->mission->worksheetForm->insert($this->connection);
        $this->worksheetTwo->mission->worksheetForm->insert($this->connection);
        $this->worksheetThree->mission->worksheetForm->insert($this->connection);
        
        $this->worksheetOne->mission->insert($this->connection);
        $this->worksheetTwo->mission->insert($this->connection);
        $this->worksheetThree->mission->insert($this->connection);
        
        $this->worksheetOne->insert($this->connection);
        $this->worksheetTwo->insert($this->connection);
        $this->worksheetThree->insert($this->connection);
        
        $this->consultantCommentTwo->consultant->insert($this->connection);
        $this->consultantCommentTwo->insert($this->connection);
        
//echo $this->viewListInCoordinatedProgramUri;
        $this->get($this->viewListInCoordinatedProgramUri, $this->personnel->token);
    }
    public function test_viewListInCoordinatedProgram_200()
    {
$this->disableExceptionHandling();
        $this->viewListInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '3',
            'list' => [
                [
                    'id' => $this->worksheetOne->id,
                    'name' => $this->worksheetOne->name,
                    'submitTime' => $this->worksheetOne->formRecord->submitTime,
                    'isReviewed' => '0',
                    'missionId' => $this->worksheetOne->mission->id,
                    'missionName' => $this->worksheetOne->mission->name,
                    //
                    'participantId' => $this->worksheetOne->participant->id,
                    'participantName' => $this->clientParticipantOne->client->getFullName(),
                    //
                    'coordinatorId' => $this->coordinatorOne->id,
                    'programId' => $this->coordinatorOne->program->id,
                    'programName' => $this->coordinatorOne->program->name,
                ],
                [
                    'id' => $this->worksheetTwo->id,
                    'name' => $this->worksheetTwo->name,
                    'submitTime' => $this->worksheetTwo->formRecord->submitTime,
                    'isReviewed' => '1',
                    'missionId' => $this->worksheetTwo->mission->id,
                    'missionName' => $this->worksheetTwo->mission->name,
                    //
                    'participantId' => $this->worksheetTwo->participant->id,
                    'participantName' => $this->teamParticipantTwo->team->name,
                    //
                    'coordinatorId' => $this->coordinatorTwo->id,
                    'programId' => $this->coordinatorTwo->program->id,
                    'programName' => $this->coordinatorTwo->program->name,
                ],
                [
                    'id' => $this->worksheetThree->id,
                    'name' => $this->worksheetThree->name,
                    'submitTime' => $this->worksheetThree->formRecord->submitTime,
                    'isReviewed' => '0',
                    'missionId' => $this->worksheetThree->mission->id,
                    'missionName' => $this->worksheetThree->mission->name,
                    //
                    'participantId' => $this->worksheetThree->participant->id,
                    'participantName' => $this->userParticipantThree->user->getFullName(),
                    //
                    'coordinatorId' => $this->coordinatorThree->id,
                    'programId' => $this->coordinatorThree->program->id,
                    'programName' => $this->coordinatorThree->program->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewListInCoordinatedProgram_fromLeftMenu()
    {
        $this->viewListInCoordinatedProgram();
        $this->seeStatusCode(200);
//$this->seeJsonContains(['print']);
        
        $this->seeJsonContains(['total' => '3']);
        $this->seeJsonContains(['id' => $this->worksheetOne->id]);
        $this->seeJsonContains(['id' => $this->worksheetTwo->id]);
        $this->seeJsonContains(['id' => $this->worksheetThree->id]);
    }
    public function test_viewListInCoordinatedProgram_fromParticipantPage()
    {
        $this->viewListInCoordinatedProgramUri .= "?participantId={$this->clientParticipantOne->participant->id}";
        $this->viewListInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['id' => $this->worksheetOne->id]);
        $this->seeJsonDoesntContains(['id' => $this->worksheetTwo->id]);
        $this->seeJsonDoesntContains(['id' => $this->worksheetThree->id]);
    }
    
    protected function viewListInConsultedProgram()
    {
        $this->consultantOne->program->insert($this->connection);
        $this->consultantTwo->program->insert($this->connection);
        $this->consultantThree->program->insert($this->connection);
        
        $this->consultantOne->insert($this->connection);
        $this->consultantTwo->insert($this->connection);
        $this->consultantThree->insert($this->connection);
        //
        $this->clientParticipantOne->client->insert($this->connection);
        $this->teamParticipantTwo->team->insert($this->connection);
        $this->userParticipantThree->user->insert($this->connection);
        
        $this->clientParticipantOne->insert($this->connection);
        $this->teamParticipantTwo->insert($this->connection);
        $this->userParticipantThree->insert($this->connection);
        
        $this->dedicatedMentorThree->insert($this->connection);
        
        $this->worksheetOne->mission->worksheetForm->form->insert($this->connection);
        $this->worksheetTwo->mission->worksheetForm->form->insert($this->connection);
        $this->worksheetThree->mission->worksheetForm->form->insert($this->connection);
        
        $this->worksheetOne->mission->worksheetForm->insert($this->connection);
        $this->worksheetTwo->mission->worksheetForm->insert($this->connection);
        $this->worksheetThree->mission->worksheetForm->insert($this->connection);
        
        $this->worksheetOne->mission->insert($this->connection);
        $this->worksheetTwo->mission->insert($this->connection);
        $this->worksheetThree->mission->insert($this->connection);
        
        $this->worksheetOne->insert($this->connection);
        $this->worksheetTwo->insert($this->connection);
        $this->worksheetThree->insert($this->connection);
        
        $this->consultantCommentTwo->insert($this->connection);
//echo $this->viewListInConsultedProgramUri;
        $this->get($this->viewListInConsultedProgramUri, $this->personnel->token);
    }
    public function test_viewListInConsultedProgram_200()
    {
$this->disableExceptionHandling();
        $this->viewListInConsultedProgram();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '3',
            'list' => [
                [
                    'id' => $this->worksheetOne->id,
                    'name' => $this->worksheetOne->name,
                    'submitTime' => $this->worksheetOne->formRecord->submitTime,
                    'isReviewed' => '0',
                    'missionId' => $this->worksheetOne->mission->id,
                    'missionName' => $this->worksheetOne->mission->name,
                    //
                    'participantId' => $this->worksheetOne->participant->id,
                    'participantName' => $this->clientParticipantOne->client->getFullName(),
                    'aDedicatedMentee' => '0',
                    //
                    'consultantId' => $this->consultantOne->id,
                    'programId' => $this->consultantOne->program->id,
                    'programName' => $this->consultantOne->program->name,
                ],
                [
                    'id' => $this->worksheetTwo->id,
                    'name' => $this->worksheetTwo->name,
                    'submitTime' => $this->worksheetTwo->formRecord->submitTime,
                    'isReviewed' => '1',
                    'missionId' => $this->worksheetTwo->mission->id,
                    'missionName' => $this->worksheetTwo->mission->name,
                    //
                    'participantId' => $this->worksheetTwo->participant->id,
                    'participantName' => $this->teamParticipantTwo->team->name,
                    'aDedicatedMentee' => '0',
                    //
                    'consultantId' => $this->consultantTwo->id,
                    'programId' => $this->consultantTwo->program->id,
                    'programName' => $this->consultantTwo->program->name,
                ],
                [
                    'id' => $this->worksheetThree->id,
                    'name' => $this->worksheetThree->name,
                    'submitTime' => $this->worksheetThree->formRecord->submitTime,
                    'isReviewed' => '0',
                    'missionId' => $this->worksheetThree->mission->id,
                    'missionName' => $this->worksheetThree->mission->name,
                    //
                    'participantId' => $this->worksheetThree->participant->id,
                    'participantName' => $this->userParticipantThree->user->getFullName(),
                    'aDedicatedMentee' => '1',
                    //
                    'consultantId' => $this->consultantThree->id,
                    'programId' => $this->consultantThree->program->id,
                    'programName' => $this->consultantThree->program->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewListInConsultedProgram_fromLeftMenu()
    {
        $this->viewListInConsultedProgramUri .= "?onlyDedicatedMentee=true";
        
        $this->viewListInConsultedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['id' => $this->worksheetOne->id]);
        $this->seeJsonDoesntContains(['id' => $this->worksheetTwo->id]);
        $this->seeJsonContains(['id' => $this->worksheetThree->id]);
    }
    public function test_viewListInConsultedProgram_fromParticipantPage()
    {
        $this->viewListInConsultedProgramUri .= "?participantId={$this->clientParticipantOne->participant->id}";
        
        $this->viewListInConsultedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['id' => $this->worksheetOne->id]);
        $this->seeJsonDoesntContains(['id' => $this->worksheetTwo->id]);
        $this->seeJsonDoesntContains(['id' => $this->worksheetThree->id]);
    }
    public function test_viewListInConsultedProgram_fromMentorDashboard()
    {
$this->disableExceptionHandling();
        $this->viewListInConsultedProgramUri .= 
                "?onlyDedicatedMentee=true"
                . "&reviewedStatus=false";
        
        $this->viewListInConsultedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['id' => $this->worksheetOne->id]);
        $this->seeJsonDoesntContains(['id' => $this->worksheetTwo->id]);
        $this->seeJsonContains(['id' => $this->worksheetThree->id]);
    }
    public function test_viewListInConsultedProgram_fullFilter_200()
    {
//$this->disableExceptionHandling();
        $this->viewListInConsultedProgramUri .= 
                "?onlyDedicatedMentee=false"
                . "&programId={$this->consultantOne->program->id}"
                . "&participantId={$this->clientParticipantOne->participant->id}"
                . "&missionId={$this->worksheetOne->mission->id}"
                . "&order=submit-asc"
                . "&reviewedStatus=false";
        
        $this->viewListInConsultedProgram();
        $this->seeStatusCode(200);
//$this->seeJsonContains(['print']);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['id' => $this->worksheetOne->id]);
        $this->seeJsonDoesntContains(['id' => $this->worksheetTwo->id]);
        $this->seeJsonDoesntContains(['id' => $this->worksheetThree->id]);
    }

}
