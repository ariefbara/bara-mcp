<?php

namespace Tests\Controllers\Client\AsProgramParticipant\Mission;

use DateTimeImmutable;
use Tests\Controllers\Client\AsProgramParticipant\MissionTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\Mission\LearningMaterial\RecordOfLearningAttachment;
use Tests\Controllers\RecordPreparation\Firm\Program\Mission\RecordOfLearningMaterial;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFirmFileInfo;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFileInfo;

class LearningMaterialControllerTest extends MissionTestCase
{
    protected $learningMaterialUri;
    protected $learningMaterial;
    protected $learningMaterialOne;
    
    protected $learningAttachmentOne;
    protected $learningAttachmentTwo;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->learningMaterialUri = $this->missionUri . "/{$this->mission->id}/learning-materials";
        
        $this->connection->table("LearningMaterial")->truncate();
        $this->connection->table("FileInfo")->truncate();
        $this->connection->table("FirmFileInfo")->truncate();
        $this->connection->table("LearningAttachment")->truncate();
        $this->connection->table("ActivityLog")->truncate();
        $this->connection->table("ViewLearningMaterialActivityLog")->truncate();
        
        $firm = $this->mission->program->firm;
        
        $this->learningMaterial = new RecordOfLearningMaterial($this->mission, 0);
        $this->learningMaterialOne = new RecordOfLearningMaterial($this->mission, 1);
        $this->connection->table("LearningMaterial")->insert($this->learningMaterial->toArrayForDbEntry());
        $this->connection->table("LearningMaterial")->insert($this->learningMaterialOne->toArrayForDbEntry());
        
        $fileInfoOne = new RecordOfFileInfo('1');
        $fileInfoOne->bucketName = 'firm-main-identifier';
        $fileInfoOne->objectName = 'video.mp4';
        $fileInfoOne->contentType = 'video/mp4';
        $fileInfoTwo = new RecordOfFileInfo('2');
        $fileInfoTwo->bucketName = 'firm-main-identifier';
        $fileInfoTwo->objectName = 'article.pdf';
        $fileInfoTwo->contentType = 'application/pdf';
        
        $firmFileInfoOne = new RecordOfFirmFileInfo($firm, $fileInfoOne);
        $firmFileInfoTwo = new RecordOfFirmFileInfo($firm, $fileInfoTwo);
        
        $this->learningAttachmentOne = new RecordOfLearningAttachment($this->learningMaterial, $firmFileInfoOne, '1');
        $this->learningAttachmentOne->disabled = true;
        $this->learningAttachmentTwo = new RecordOfLearningAttachment($this->learningMaterial, $firmFileInfoTwo, '2');
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("LearningMaterial")->truncate();
        $this->connection->table("FileInfo")->truncate();
        $this->connection->table("FirmFileInfo")->truncate();
        $this->connection->table("LearningAttachment")->truncate();
        $this->connection->table("ActivityLog")->truncate();
        $this->connection->table("ViewLearningMaterialActivityLog")->truncate();
    }
    
    protected function showAll()
    {
$this->disableExceptionHandling();
        $this->learningAttachmentOne->firmFileInfo->insert($this->connection);
        $this->learningAttachmentTwo->firmFileInfo->insert($this->connection);
        
        $this->learningAttachmentOne->insert($this->connection);
        $this->learningAttachmentTwo->insert($this->connection);
        
        $this->get($this->learningMaterialUri, $this->programParticipation->client->token);
//$this->printApiSpesifiation($this->learningMaterialUri);
    }
    public function test_showAll_200()
    {
        $this->showAll();
        $this->seeStatusCode(200);
        $this->seeJsonContains([
            'total' => 2,
            'list' => [
                [
                    'id' => $this->learningMaterial->id,
                    'name' => $this->learningMaterial->name,
                    'learningAttachments' => [
                        [
                            'id' => $this->learningAttachmentTwo->id,
                            'firmFileInfo' => [
                                'id' => $this->learningAttachmentTwo->firmFileInfo->fileInfo->id,
                                'contentType' => $this->learningAttachmentTwo->firmFileInfo->fileInfo->contentType,
                            ],
                        ],
                    ],
                ],
                [
                    'id' => $this->learningMaterialOne->id,
                    'name' => $this->learningMaterialOne->name,
                    'learningAttachments' => [],
                ],
            ],
        ]);
    }
    public function test_showAll_inactiveParticipant_403()
    {
        $this->setInactiveParticipant();
        $this->get($this->learningMaterialUri, $this->programParticipation->client->token)
                ->seeStatusCode(403);
    }
    
    protected function show()
    {
        
        $this->learningAttachmentOne->firmFileInfo->insert($this->connection);
        $this->learningAttachmentTwo->firmFileInfo->insert($this->connection);
        
        $this->learningAttachmentOne->insert($this->connection);
        $this->learningAttachmentTwo->insert($this->connection);
        
        $uri = $this->learningMaterialUri . "/{$this->learningMaterial->id}";
        $this->get($uri, $this->programParticipation->client->token);
$this->printApiSpesifiation($uri);
    }
    public function test_show_200()
    {
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            "id" => $this->learningMaterial->id,
            "name" => $this->learningMaterial->name,
            "content" => $this->learningMaterial->content,
        ];
        $this->seeJsonContains($response);
    }
    public function test_show_200_excludeDisabledLearningAttachment()
    {
        $this->learningAttachmentTwo->disabled = true;
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            "id" => $this->learningMaterial->id,
            "name" => $this->learningMaterial->name,
            "content" => $this->learningMaterial->content,
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
    public function test_show_inactiveParticipant_403()
    {
        $this->setInactiveParticipant();
        $uri = $this->learningMaterialUri . "/{$this->learningMaterial->id}";
        $this->get($uri, $this->programParticipation->client->token)
                ->seeStatusCode(403);
    }
    public function test_show_logActivity()
    {
        $uri = $this->learningMaterialUri . "/{$this->learningMaterial->id}";
        $this->get($uri, $this->programParticipation->client->token)
                ->seeStatusCode(200);
        
        $activityLogEntry = [
            "message" => "accessed learning material",
            "occuredTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
        ];
        $this->seeInDatabase("ActivityLog", $activityLogEntry);
        
        $viewLearningMaterialActivityLogEntry = [
            "Participant_id" => $this->programParticipation->participant->id,
            "LearningMaterial_id" => $this->learningMaterial->id,
        ];
        $this->seeInDatabase("ViewLearningMaterialActivityLog", $viewLearningMaterialActivityLogEntry);
    }
}
