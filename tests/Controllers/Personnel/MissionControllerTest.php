<?php

namespace Tests\Controllers\Personnel;

use Tests\Controllers\RecordPreparation\Firm\Program\Mission\RecordOfMissionComment;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;

class MissionControllerTest extends PersonnelTestCase
{

    protected $mentorOne;
    protected $missionOne_p999;
    protected $missionTwo_p1;
    protected $missionThree_p1;
    protected $missionCommentOne_m1;
    protected $missionCommentTwo_m1;
    protected $missionCommentThree_m2;
    protected $viewDiscussionOverviewUri;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('MissionComment')->truncate();
        
        $this->viewDiscussionOverviewUri = $this->personnelUri . "/missions/discussion-overview";
        
        $firm = $this->personnel->firm;
        
        $programOne = new RecordOfProgram($firm, 1);
        
        $this->mentorOne = new RecordOfConsultant($programOne, $this->personnel, 1);
        
        $this->missionOne_p999 = new RecordOfMission($this->mentor->program, null, 1, null);
        $this->missionTwo_p1 = new RecordOfMission($programOne, null, 2, null);
        $this->missionThree_p1 = new RecordOfMission($programOne, null, 3, null);
        
        $this->missionCommentOne_m1 = new RecordOfMissionComment($this->missionOne_p999, null, 1);
        $this->missionCommentOne_m1->modifiedTime = (new \DateTime('-12 hours'))->format('Y-m-d H:i:s');
        $this->missionCommentTwo_m1 = new RecordOfMissionComment($this->missionOne_p999, null, 2);
        $this->missionCommentThree_m2 = new RecordOfMissionComment($this->missionTwo_p1, null, 3);
    }
    protected function tearDown(): void
    {
        parent::tearDown();
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
                    'lastActivity' => $this->missionCommentThree_m2->modifiedTime,
                    'message' => $this->missionCommentThree_m2->message,
                    'numberOfPost' => '1',
                ],
                [
                    'id' => $this->missionThree_p1->id,
                    'name' => $this->missionThree_p1->name,
                    'programId' => $this->missionThree_p1->program->id,
                    'programName' => $this->missionThree_p1->program->name,
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
        $this->missionCommentTwo_m1->modifiedTime = (new \DateTime('-6 hours'))->format('Y-m-d H:i:s');
        
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
        $this->missionCommentThree_m2->modifiedTime = (new \DateTime('-4 hours'))->format('Y-m-d H:i:s');
        
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

}
