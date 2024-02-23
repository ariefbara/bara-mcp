<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

namespace Tests\Controllers\Client\ProgramParticipation;

use Tests\Controllers\RecordPreparation\Firm\Program\Mission\RecordOfLearningMaterial;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfLearningProgress;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;

class LearningProgressControllerTest extends ExtendedClientParticipantTestCase
{
    protected $learningMaterialOne;
    //
    protected $learningProgressOne;
    protected $learningProgressTwo;
    //
    protected $learningProgressUri;
    //
    protected $submitRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('LearningMaterial')->truncate();
        $this->connection->table('LearningProgress')->truncate();
        //
        $this->learningProgressUri = $this->clientParticipantUri . "/learning-progress";
        //
        $program = $this->clientParticipant->participant->program;
        
        $missionOne = new RecordOfMission($program, null, 1, null);
        $missionTwo = new RecordOfMission($program, null, 2, null);
        
        $this->learningMaterialOne = new RecordOfLearningMaterial($missionOne, 1);
        $learningMaterialTwo = new RecordOfLearningMaterial($missionTwo, 2);
        
        $this->learningProgressOne = new RecordOfLearningProgress($this->clientParticipant->participant, $this->learningMaterialOne, 1);
        $this->learningProgressTwo = new RecordOfLearningProgress($this->clientParticipant->participant, $learningMaterialTwo, 2);
        //
        $this->submitRequest = [
            'progressMark' => 'new progress mark',
            'markAsCompleted' => true,
            'learningMaterialId' => $this->learningMaterialOne->id,
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('LearningMaterial')->truncate();
        $this->connection->table('LearningProgress')->truncate();
    }
    
    //
    protected function submit()
    {
        $this->insertClientParticipantRecord();
        
        $this->learningMaterialOne->mission->insert($this->connection);
        $this->learningMaterialOne->insert($this->connection);
        
        $this->put($this->learningProgressUri, $this->submitRequest, $this->clientParticipant->client->token);
echo $this->learningProgressUri;
echo ' || payload: ' . json_encode($this->submitRequest);
$this->seeJsonContains(['print']);
    }
    public function test_submit_200()
    {
$this->disableExceptionHandling();
        $this->submit();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains([
            'lastModifiedTime' => $this->currentTimeString(),
            'progressMark' => $this->submitRequest['progressMark'],
            'markAsCompleted' => $this->submitRequest['markAsCompleted'],
            'learningMaterial' => [
                'id' => $this->learningMaterialOne->id,
                'name' => $this->learningMaterialOne->name,
            ],
        ]);
        
        $this->seeInDatabase('LearningProgress', [
            'lastModifiedTime' => $this->currentTimeString(),
            'progressMark' => $this->submitRequest['progressMark'],
            'markAsCompleted' => $this->submitRequest['markAsCompleted'],
            'LearningMaterial_id' => $this->learningMaterialOne->id,
            'Participant_id' => $this->clientParticipant->participant->id,
        ]);
    }
    public function test_submit_alreadyHasProgressAssociateWithSameMaterial_updateExistingProgress()
    {
        $this->learningProgressOne->insert($this->connection);
        $this->submit();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains([
            'id' => $this->learningProgressOne->id,
            'lastModifiedTime' => $this->currentTimeString(),
            'progressMark' => $this->submitRequest['progressMark'],
            'markAsCompleted' => $this->submitRequest['markAsCompleted'],
        ]);
        
        $this->seeInDatabase('LearningProgress', [
            'id' => $this->learningProgressOne->id,
            'lastModifiedTime' => $this->currentTimeString(),
            'progressMark' => $this->submitRequest['progressMark'],
            'markAsCompleted' => $this->submitRequest['markAsCompleted'],
        ]);
    }
    public function test_submit_unmanageMaterial_diffProgram_403()
    {
        $this->otherProgram->insert($this->connection);
        $this->learningMaterialOne->mission->program = $this->otherProgram;
        
        $this->submit();
        $this->seeStatusCode(403);
    }
    public function test_submit_unmanageMaterial_removed_403()
    {
        $this->learningMaterialOne->removed = true;
        
        $this->submit();
        $this->seeStatusCode(403);
    }
    
    //
    protected function updateProgressMark()
    {
        $this->insertClientParticipantRecord();
        
        $this->learningProgressOne->learningMaterial->mission->insert($this->connection);
        $this->learningProgressOne->learningMaterial->insert($this->connection);
        $this->learningProgressOne->insert($this->connection);
        
        $uri = $this->learningProgressUri . "/{$this->learningProgressOne->id}/update-progress-mark";
        $data = [
            'progressMark' => $this->submitRequest['progressMark']
        ];
        $this->patch($uri, $data, $this->clientParticipant->client->token);
//echo $uri;
//echo ' || payload: ' . json_encode($data);
//$this->seeJsonContains(['print']);
    }
    public function test_updateProgressMark_200()
    {
$this->disableExceptionHandling();
        $this->updateProgressMark();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains([
            'id' => $this->learningProgressOne->id,
            'lastModifiedTime' => $this->currentTimeString(),
            'progressMark' => $this->submitRequest['progressMark'],
        ]);
        
        $this->seeInDatabase('LearningProgress', [
            'id' => $this->learningProgressOne->id,
            'lastModifiedTime' => $this->currentTimeString(),
            'progressMark' => $this->submitRequest['progressMark'],
        ]);
    }
    public function test_updateProgressMark_unmanagedProgress_diffParticipant_403()
    {
        $this->otherParticipant->insert($this->connection);
        $this->learningProgressOne->participant = $this->otherParticipant;
        
        $this->updateProgressMark();
        $this->seeStatusCode(403);
    }
    
    //
    protected function markCompleted()
    {
        $this->insertClientParticipantRecord();
        
        $this->learningProgressOne->learningMaterial->mission->insert($this->connection);
        $this->learningProgressOne->learningMaterial->insert($this->connection);
        $this->learningProgressOne->insert($this->connection);
        
        $uri = $this->learningProgressUri . "/{$this->learningProgressOne->id}/mark-complete";
        $this->patch($uri, [], $this->clientParticipant->client->token);
//echo $uri;
//$this->seeJsonContains(['print']);
    }
    public function test_markCompleted_200()
    {
$this->disableExceptionHandling();
        $this->markCompleted();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains([
            'id' => $this->learningProgressOne->id,
            'lastModifiedTime' => $this->currentTimeString(),
            'markAsCompleted' => true,
        ]);
        
        $this->seeInDatabase('LearningProgress', [
            'id' => $this->learningProgressOne->id,
            'lastModifiedTime' => $this->currentTimeString(),
            'markAsCompleted' => true,
        ]);
    }
    public function test_markCompleted_unmanagedProgress_diffParticipant_403()
    {
        $this->otherParticipant->insert($this->connection);
        $this->learningProgressOne->participant = $this->otherParticipant;
        
        $this->markCompleted();
        $this->seeStatusCode(403);
    }
    
    //
    protected function unmarkCompleteStatusd()
    {
        $this->insertClientParticipantRecord();
        
        $this->learningProgressOne->markAsCompleted = true;
        
        $this->learningProgressOne->learningMaterial->mission->insert($this->connection);
        $this->learningProgressOne->learningMaterial->insert($this->connection);
        $this->learningProgressOne->insert($this->connection);
        
        $uri = $this->learningProgressUri . "/{$this->learningProgressOne->id}/unmark-complete-status";
        $this->patch($uri, [], $this->clientParticipant->client->token);
//echo $uri;
//$this->seeJsonContains(['print']);
    }
    public function test_unmarkCompleteStatusd_200()
    {
$this->disableExceptionHandling();
        $this->unmarkCompleteStatusd();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains([
            'id' => $this->learningProgressOne->id,
            'lastModifiedTime' => $this->currentTimeString(),
            'markAsCompleted' => false,
        ]);
        
        $this->seeInDatabase('LearningProgress', [
            'id' => $this->learningProgressOne->id,
            'lastModifiedTime' => $this->currentTimeString(),
            'markAsCompleted' => false,
        ]);
    }
    public function test_unmarkCompleteStatusd_unmanagedProgress_diffParticipant_403()
    {
        $this->otherParticipant->insert($this->connection);
        $this->learningProgressOne->participant = $this->otherParticipant;
        
        $this->unmarkCompleteStatusd();
        $this->seeStatusCode(403);
    }
    
    //
    protected function viewDetail()
    {
        $this->insertClientParticipantRecord();
        
        $this->learningProgressOne->learningMaterial->mission->insert($this->connection);
        $this->learningProgressOne->learningMaterial->insert($this->connection);
        $this->learningProgressOne->insert($this->connection);
        
        $uri = $this->learningProgressUri . "/{$this->learningProgressOne->id}";
        $this->get($uri, $this->clientParticipant->client->token);
//echo $uri;
//$this->seeJsonContains(['print']);
    }
    public function test_viewDetail_200()
    {
$this->disableExceptionHandling();
        $this->viewDetail();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains([
            'id' => $this->learningProgressOne->id,
            'lastModifiedTime' => $this->learningProgressOne->lastModifiedTime,
            'progressMark' => $this->learningProgressOne->progressMark,
            'markAsCompleted' => $this->learningProgressOne->markAsCompleted,
            'learningMaterial' => [
                'id' => $this->learningProgressOne->learningMaterial->id,
                'name' => $this->learningProgressOne->learningMaterial->name,
            ],
        ]);
    }
    public function test_viewDetail_unmanagedProgress_404()
    {
        $this->otherParticipant->insert($this->connection);
        $this->learningProgressOne->participant = $this->otherParticipant;
        
        $this->viewDetail();
        $this->seeStatusCode(404);
    }
    
    //
    protected function viewList()
    {
        $this->insertClientParticipantRecord();
        
        $this->learningProgressOne->learningMaterial->mission->insert($this->connection);
        $this->learningProgressOne->learningMaterial->insert($this->connection);
        $this->learningProgressOne->insert($this->connection);
        
        $this->learningProgressTwo->learningMaterial->mission->insert($this->connection);
        $this->learningProgressTwo->learningMaterial->insert($this->connection);
        $this->learningProgressTwo->insert($this->connection);
        
        $this->get($this->learningProgressUri, $this->clientParticipant->client->token);
//echo $this->learningProgressUri;
//$this->seeJsonContains(['print']);
    }
    public function test_viewList_200()
    {
$this->disableExceptionHandling();
        $this->viewList();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains([
            'list' => [
                [
                    'id' => $this->learningProgressOne->id,
                    'lastModifiedTime' => $this->learningProgressOne->lastModifiedTime,
                    'progressMark' => $this->learningProgressOne->progressMark,
                    'markAsCompleted' => $this->learningProgressOne->markAsCompleted,
                    'learningMaterial' => [
                        'id' => $this->learningProgressOne->learningMaterial->id,
                        'name' => $this->learningProgressOne->learningMaterial->name,
                    ],
                ],
                [
                    'id' => $this->learningProgressTwo->id,
                    'lastModifiedTime' => $this->learningProgressTwo->lastModifiedTime,
                    'progressMark' => $this->learningProgressTwo->progressMark,
                    'markAsCompleted' => $this->learningProgressTwo->markAsCompleted,
                    'learningMaterial' => [
                        'id' => $this->learningProgressTwo->learningMaterial->id,
                        'name' => $this->learningProgressTwo->learningMaterial->name,
                    ],
                ],
            ],
            'total' => 2,
            
        ]);
    }
    public function test_viewList_excludeUnmanageProgress_404()
    {
        $this->otherParticipant->insert($this->connection);
        $this->learningProgressOne->participant = $this->otherParticipant;
        
        $this->viewList();
        $this->seeJsonContains(['total' => 1]);
        $this->seeJsonDoesntContains(['id' => $this->learningProgressOne->id]);
        $this->seeJsonContains(['id' => $this->learningProgressTwo->id]);
    }
    
}
