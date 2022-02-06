<?php

namespace Tests\Controllers\Personnel\AsProgramConsultant\Mission;

use Tests\Controllers\RecordPreparation\Firm\Program\Mission\LearningMaterial\RecordOfLearningAttachment;
use Tests\Controllers\RecordPreparation\Firm\Program\Mission\RecordOfLearningMaterial;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFirmFileInfo;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFileInfo;

class LearningMaterialControllerTest extends MissionTestCase
{
    protected $learningMaterialUri;
    protected $learningMaterialOne;
    protected $learningMaterialTwo;
    
    protected $learningAttachmentOne;
    protected $learningAttachmentTwo;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table("LearningMaterial")->truncate();
        $this->connection->table("FileInfo")->truncate();
        $this->connection->table("FirmFileInfo")->truncate();
        $this->connection->table("LearningAttachment")->truncate();
        
        $firm = $this->mission->program->firm;
        
        $this->learningMaterialUri = $this->missionUri . "/learning-materials";
        
        $this->learningMaterialOne = new RecordOfLearningMaterial($this->mission, '1');
        $this->learningMaterialTwo = new RecordOfLearningMaterial($this->mission, '2');
        
        $fileInfoOne = new RecordOfFileInfo('1');
        $fileInfoTwo = new RecordOfFileInfo('2');
        
        $firmFileInfoOne = new RecordOfFirmFileInfo($firm, $fileInfoOne);
        $firmFileInfoTwo = new RecordOfFirmFileInfo($firm, $fileInfoTwo);
        
        $this->learningAttachmentOne = new RecordOfLearningAttachment($this->learningMaterialOne, $firmFileInfoOne, '1');
        $this->learningAttachmentTwo = new RecordOfLearningAttachment($this->learningMaterialOne, $firmFileInfoTwo, '2');
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("LearningMaterial")->truncate();
        $this->connection->table("FileInfo")->truncate();
        $this->connection->table("FirmFileInfo")->truncate();
        $this->connection->table("LearningAttachment")->truncate();
    }
    
    protected function show()
    {
        $this->insertAggregateEntry();
        $this->learningMaterialOne->insert($this->connection);
        
        $this->learningAttachmentOne->firmFileInfo->insert($this->connection);
        $this->learningAttachmentTwo->firmFileInfo->insert($this->connection);
        
        $this->learningAttachmentOne->insert($this->connection);
        $this->learningAttachmentTwo->insert($this->connection);
        
        $this->learningMaterialUri .= "/{$this->learningMaterialOne->id}";
echo $this->learningMaterialUri;
        $this->get($this->learningMaterialUri, $this->consultant->personnel->token);
    }
    public function test_show_200()
    {
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            "id" => $this->learningMaterialOne->id,
            "name" => $this->learningMaterialOne->name,
            "content" => $this->learningMaterialOne->content,
            'learningAttachments' => [
                [
                    'id' => $this->learningAttachmentOne->id,
                    'firmFileInfo' => [
                        'id' => $this->learningAttachmentOne->firmFileInfo->id,
                        'path' => $this->learningAttachmentOne->firmFileInfo->fileInfo->getFullyPath(),
                    ],
                ],
                [
                    'id' => $this->learningAttachmentTwo->id,
                    'firmFileInfo' => [
                        'id' => $this->learningAttachmentTwo->firmFileInfo->id,
                        'path' => $this->learningAttachmentTwo->firmFileInfo->fileInfo->getFullyPath(),
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_show_200_excludeDisabledLearningAttachment()
    {
        $this->learningAttachmentTwo->disabled = true;
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            "id" => $this->learningMaterialOne->id,
            "name" => $this->learningMaterialOne->name,
            "content" => $this->learningMaterialOne->content,
            'learningAttachments' => [
                [
                    'id' => $this->learningAttachmentOne->id,
                    'firmFileInfo' => [
                        'id' => $this->learningAttachmentOne->firmFileInfo->id,
                        'path' => $this->learningAttachmentOne->firmFileInfo->fileInfo->getFullyPath(),
                    ],
                ],
            ],
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
