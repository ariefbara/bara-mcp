<?php

namespace Tests\Controllers\Personnel;

use DateTime;
use Tests\Controllers\RecordPreparation\Firm\Program\Mission\RecordOfMissionComment;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfWorksheetForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;

class MissionControllerTest extends PersonnelTestCase
{
    protected $coordinatorOne;
    protected $coordinatorFour;

    protected $mentorOne;
    protected $missionOne_p999;
    protected $missionTwo_p1;
    protected $missionThree_p1;
    protected $missionFour;
    protected $missionCommentOne_m1;
    protected $missionCommentTwo_m1;
    protected $missionCommentThree_m2;
    
    protected $viewDiscussionOverviewUri;
    protected $missionListInCoordinatedProgramUri;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Coordinator')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('MissionComment')->truncate();
        
        $this->viewDiscussionOverviewUri = $this->personnelUri . "/missions/discussion-overview";
        $this->missionListInCoordinatedProgramUri = $this->personnelUri . "/mission-list-in-coordinated-program";
        
        $firm = $this->personnel->firm;
        
        $programOne = new RecordOfProgram($firm, 1);
        $programFour = new RecordOfProgram($firm, 4);
        
        $this->coordinatorOne = new RecordOfCoordinator($programOne, $this->personnel, 1);
        $this->coordinatorFour = new RecordOfCoordinator($programFour, $this->personnel, 4);
        
        $this->mentorOne = new RecordOfConsultant($programOne, $this->personnel, 1);
        
        $formOne = new RecordOfForm(1);
        $formFour = new RecordOfForm(4);
        
        $worksheetFormOne = new RecordOfWorksheetForm($firm, $formOne);
        $worksheetFormFour = new RecordOfWorksheetForm($firm, $formFour);
        
        $this->missionOne_p999 = new RecordOfMission($this->mentor->program, null, 1, null);
        $this->missionTwo_p1 = new RecordOfMission($programOne, $worksheetFormOne, 2, null);
        $this->missionThree_p1 = new RecordOfMission($programOne, null, 3, null);
        $this->missionFour = new RecordOfMission($programFour, $worksheetFormFour, 4, null);
        
        $this->missionCommentOne_m1 = new RecordOfMissionComment($this->missionOne_p999, null, 1);
        $this->missionCommentOne_m1->modifiedTime = (new DateTime('-12 hours'))->format('Y-m-d H:i:s');
        $this->missionCommentTwo_m1 = new RecordOfMissionComment($this->missionOne_p999, null, 2);
        $this->missionCommentThree_m2 = new RecordOfMissionComment($this->missionTwo_p1, null, 3);
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Coordinator')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('MissionComment')->truncate();
    }
    
    protected function viewDiscussionOverview()
    {
        $this->mentor->program->insert($this->connection);
        $this->mentorOne->program->insert($this->connection);
        
        $this->mentor->insert($this->connection);
        $this->mentorOne->insert($this->connection);
        
        $this->missionOne_p999->insert($this->connection);
        $this->missionTwo_p1->insert($this->connection);
        $this->missionThree_p1->insert($this->connection);
        
        $this->missionCommentOne_m1->insert($this->connection);
        $this->missionCommentTwo_m1->insert($this->connection);
        $this->missionCommentThree_m2->insert($this->connection);
        
        $this->get($this->viewDiscussionOverviewUri, $this->personnel->token);
    }
    public function test_viewDiscussionOverview_200()
    {
$this->disableExceptionHandling();
        $this->viewDiscussionOverview();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '3',
            'list' => [
                [
                    'id' => $this->missionOne_p999->id,
                    'name' => $this->missionOne_p999->name,
                    'programId' => $this->missionOne_p999->program->id,
                    'programConsultationId' => $this->mentor->id,
                    'programName' => $this->missionOne_p999->program->name,
                    'lastActivity' => $this->missionCommentOne_m1->modifiedTime,
                    'message' => $this->missionCommentOne_m1->message,
                    'numberOfPost' => '2',
                ],
                [
                    'id' => $this->missionTwo_p1->id,
                    'name' => $this->missionTwo_p1->name,
                    'programId' => $this->missionTwo_p1->program->id,
                    'programName' => $this->missionTwo_p1->program->name,
                    'programConsultationId' => $this->mentorOne->id,
                    'lastActivity' => $this->missionCommentThree_m2->modifiedTime,
                    'message' => $this->missionCommentThree_m2->message,
                    'numberOfPost' => '1',
                ],
                [
                    'id' => $this->missionThree_p1->id,
                    'name' => $this->missionThree_p1->name,
                    'programId' => $this->missionThree_p1->program->id,
                    'programName' => $this->missionThree_p1->program->name,
                    'programConsultationId' => $this->mentorOne->id,
                    'lastActivity' => null,
                    'message' => null,
                    'numberOfPost' => null,
                ],
            ]
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewDiscussionOverview_snapLastModifiedMessage()
    {
        $this->missionCommentTwo_m1->modifiedTime = (new DateTime('-6 hours'))->format('Y-m-d H:i:s');
        
        $this->viewDiscussionOverview();
        $this->seeStatusCode(200);
        $this->seeJsonContains(['total' => '3']);
        $this->seeJsonContains([
            'id' => $this->missionOne_p999->id,
            'message' => $this->missionCommentTwo_m1->message,
            'lastActivity' => $this->missionCommentTwo_m1->modifiedTime,
        ]);
        $this->seeJsonContains(['id' => $this->missionTwo_p1->id]);
        $this->seeJsonContains(['id' => $this->missionThree_p1->id]);
    }
    public function test_viewDiscussionOverview_pagination_orderByLastActivityDesc()
    {
        $this->viewDiscussionOverviewUri .= "?pageSize=1";
        $this->missionCommentThree_m2->modifiedTime = (new DateTime('-4 hours'))->format('Y-m-d H:i:s');
        
        $this->viewDiscussionOverview();
        $this->seeStatusCode(200);
        $this->seeJsonContains(['total' => '3']);
        $this->seeJsonDoesntContains(['id' => $this->missionOne_p999->id]);
        $this->seeJsonContains(['id' => $this->missionTwo_p1->id]);
        $this->seeJsonDoesntContains(['id' => $this->missionThree_p1->id]);
    }
    public function test_viewDiscussionOverview_excludeMissionOfUnauthorizedProgram()
    {
        $this->mentorOne->active = false;
        
        $this->viewDiscussionOverview();
        $this->seeStatusCode(200);
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['id' => $this->missionOne_p999->id]);
        $this->seeJsonDoesntContains(['id' => $this->missionTwo_p1->id]);
        $this->seeJsonDoesntContains(['id' => $this->missionThree_p1->id]);
    }
    
    protected function missionListInCoordinatedProgram()
    {
        $this->coordinatorOne->program->insert($this->connection);
        $this->coordinatorFour->program->insert($this->connection);
        
        $this->coordinatorOne->insert($this->connection);
        $this->coordinatorFour->insert($this->connection);
        
        $this->missionFour->worksheetForm->form->insert($this->connection);
        $this->missionTwo_p1->worksheetForm->form->insert($this->connection);
        
        $this->missionFour->worksheetForm->insert($this->connection);
        $this->missionTwo_p1->worksheetForm->insert($this->connection);
        
        $this->missionFour->insert($this->connection);
        $this->missionTwo_p1->insert($this->connection);
        
// echo $this->missionListInCoordinatedProgramUri;
        $this->get($this->missionListInCoordinatedProgramUri, $this->personnel->token);
    }
    public function test_missionListInCoordinatedProgram_200()
    {
$this->disableExceptionHandling();
        $this->missionListInCoordinatedProgram();
        $this->seeStatusCode(200);
        
// $this->seeJsonContains(['print']);
        $this->seeJsonContains([
            'id' => $this->missionTwo_p1->id,
            'name' => $this->missionTwo_p1->name,
            'formName' => $this->missionTwo_p1->worksheetForm->form->name,
        ]);
        $this->seeJsonContains([
            'id' => $this->missionFour->id,
            'name' => $this->missionFour->name,
            'formName' => $this->missionFour->worksheetForm->form->name,
        ]);
    }
    public function test_missionListInCoordinatedProgram_excludeInaccessibleMission_fromNonCoordinatedProgram()
    {
        $this->missionOne_p999->program->insert($this->connection);
        $this->missionOne_p999->insert($this->connection);
        
        $this->missionListInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['id' => $this->missionTwo_p1->id]);
        $this->seeJsonContains(['id' => $this->missionFour->id]);
        $this->seeJsonDoesntContains(['id' => $this->missionOne_p999->id]);
    }

}
