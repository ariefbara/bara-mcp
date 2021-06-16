<?php

namespace Tests\Controllers\Personnel\AsProgramConsultant\Mission;

use Tests\Controllers\RecordPreparation\Firm\Program\Mission\RecordOfLearningMaterial;

class LearningMaterialControllerTest extends MissionTestCase
{
    protected $learningMaterialUri;
    protected $learningMaterialOne;
    protected $learningMaterialTwo;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->learningMaterialUri = $this->missionUri . "/learning-materials";
        
        $this->learningMaterialOne = new RecordOfLearningMaterial($this->mission, '1');
        $this->learningMaterialTwo = new RecordOfLearningMaterial($this->mission, '2');
        $this->connection->table('LearningMaterial')->truncate();
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('LearningMaterial')->truncate();
    }
    
    protected function executeShow()
    {
        $this->insertAggregateEntry();
        $this->learningMaterialOne->insert($this->connection);
        
        $this->learningMaterialUri .= "/{$this->learningMaterialOne->id}";
        $this->get($this->learningMaterialUri, $this->consultant->personnel->token);
    }
    public function test_show_200()
    {
        $this->executeShow();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->learningMaterialOne->id,
            'name' => $this->learningMaterialOne->name,
            'content' => $this->learningMaterialOne->content,
        ];
        $this->seeJsonContains($response);
    }
    
    protected function executeShowAll()
    {
        $this->insertAggregateEntry();
        $this->learningMaterialOne->insert($this->connection);
        $this->learningMaterialTwo->insert($this->connection);
        
        $this->get($this->learningMaterialUri, $this->consultant->personnel->token);
    }
    public function test_showAll_200()
    {
        $this->executeShowAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 2,
            'list' => [
                [
                    'id' => $this->learningMaterialOne->id,
                    'name' => $this->learningMaterialOne->name,
                ],
                [
                    'id' => $this->learningMaterialTwo->id,
                    'name' => $this->learningMaterialTwo->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
}
