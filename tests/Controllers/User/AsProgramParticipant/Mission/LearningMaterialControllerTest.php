<?php

namespace Tests\Controllers\User\AsProgramParticipant\Mission;

use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\Firm\Program\Mission\LearningMaterial\RecordOfLearningAttachment;
use Tests\Controllers\RecordPreparation\Firm\Program\Mission\RecordOfLearningMaterial;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFirmFileInfo;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFileInfo;
use Tests\Controllers\User\AsProgramParticipant\MissionTestCase;

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
        $fileInfoTwo = new RecordOfFileInfo('2');
        
        $firmFileInfoOne = new RecordOfFirmFileInfo($firm, $fileInfoOne);
        $firmFileInfoTwo = new RecordOfFirmFileInfo($firm, $fileInfoTwo);
        
        $this->learningAttachmentOne = new RecordOfLearningAttachment($this->learningMaterial, $firmFileInfoOne, '1');
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
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->learningMaterial->id,
                    "name" => $this->learningMaterial->name,
                ],
                [
                    "id" => $this->learningMaterialOne->id,
                    "name" => $this->learningMaterialOne->name,
                ],
            ],
        ];
        
        $this->get($this->learningMaterialUri, $this->programParticipation->user->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_inactiveParticipant_403()
    {
        $this->setInactiveParticipant();
        $this->get($this->learningMaterialUri, $this->programParticipation->user->token)
                ->seeStatusCode(403);
    }
    
    protected function show()
    {
        $this->learningAttachmentOne->firmFileInfo->insert($this->connection);
        $this->learningAttachmentTwo->firmFileInfo->insert($this->connection);
        
        $this->learningAttachmentOne->insert($this->connection);
        $this->learningAttachmentTwo->insert($this->connection);
        
        $uri = $this->learningMaterialUri . "/{$this->learningMaterial->id}";
        $this->get($uri, $this->programParticipation->user->token);
    }
    public function test_show_200()
    {
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
        $this->get($uri, $this->programParticipation->user->token)
                ->seeStatusCode(403);
    }
    public function test_show_logActivity()
    {
        $uri = $this->learningMaterialUri . "/{$this->learningMaterial->id}";
        $this->get($uri, $this->programParticipation->user->token)
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
