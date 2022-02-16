<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\Firm\Program\Mission\LearningMaterial\RecordOfLearningAttachment;
use Tests\Controllers\RecordPreparation\Firm\Program\Mission\RecordOfLearningMaterial;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFirmFileInfo;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\RecordOfFirm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFileInfo;

class LearningMaterialControllerTest extends ExtendedManagerTestCase
{
    protected $programOne;
    protected $missionOne;
    protected $missionTwo;
    
    protected $learningMaterialOne_m1;
    protected $learningMaterialTwo_m1;
    protected $learningMaterialThree_m2;
    
    protected $firmFileInfoOne;
    protected $firmFileInfoTwo;
    
    protected $learningAttachmentOne_lm1;
    protected $learningAttachmentTwo_lm2;
    
    protected $learningMaterialRequest;
    
    protected $otherFirm;
    
    protected $showAllUri;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('LearningMaterial')->truncate();
        $this->connection->table('FileInfo')->truncate();
        $this->connection->table('FirmFileInfo')->truncate();
        $this->connection->table('LearningAttachment')->truncate();
        
        $firm = $this->manager->firm;
        
        $this->programOne = new RecordOfProgram($firm, '1');
        
        $this->missionOne = new RecordOfMission($this->programOne, null, '1', null);
        $this->missionTwo = new RecordOfMission($this->programOne, null, '2', null);
        
        $this->learningMaterialOne_m1 = new RecordOfLearningMaterial($this->missionOne, '1');
        $this->learningMaterialTwo_m1 = new RecordOfLearningMaterial($this->missionOne, '2');
        $this->learningMaterialThree_m2 = new RecordOfLearningMaterial($this->missionTwo, '3');
        
        $fileInfoOne = new RecordOfFileInfo('1');
        $fileInfoTwo = new RecordOfFileInfo('2');
        
        $this->firmFileInfoOne = new RecordOfFirmFileInfo($firm, $fileInfoOne);
        $this->firmFileInfoTwo = new RecordOfFirmFileInfo($firm, $fileInfoTwo);
        
        $this->learningAttachmentOne_lm1 = new RecordOfLearningAttachment($this->learningMaterialOne_m1, $this->firmFileInfoOne, '1');
        $this->learningAttachmentTwo_lm2 = new RecordOfLearningAttachment($this->learningMaterialOne_m1, $this->firmFileInfoTwo, '2');
        
        $this->learningMaterialRequest = [
            'name' => 'new learning material name',
            'content' => 'new learning material content',
            'attachmentFileIdList' => [
                $this->firmFileInfoOne->id,
                $this->firmFileInfoTwo->id,
            ],
        ];
        
        $this->otherFirm = new RecordOfFirm('other');
        
        $this->showAllUri = $this->managerUri . "/learning-materials";
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('LearningMaterial')->truncate();
        $this->connection->table('FileInfo')->truncate();
        $this->connection->table('FirmFileInfo')->truncate();
        $this->connection->table('LearningAttachment')->truncate();
    }
    
    protected function add()
    {
        $this->persistManagerDependency();
        
        $this->missionOne->program->insert($this->connection);
        $this->missionOne->insert($this->connection);
        
        $this->firmFileInfoOne->insert($this->connection);
        $this->firmFileInfoTwo->insert($this->connection);
        
        $uri = $this->managerUri . "/missions/{$this->missionOne->id}/learning-materials";
        $this->post($uri, $this->learningMaterialRequest, $this->manager->token);
    }
    public function test_add_201()
    {
        $this->add();
        $this->seeStatusCode(201);
        
        $response = [
            'name' => $this->learningMaterialRequest['name'],
            'content' => $this->learningMaterialRequest['content'],
            'removed' => false,
        ];
        $this->seeJsonContains($response);
        
        $learningMaterialRecord = [
            'Mission_id' => $this->missionOne->id,
            'name' => $this->learningMaterialRequest['name'],
            'content' => $this->learningMaterialRequest['content'],
            'removed' => false,
        ];
        $this->seeInDatabase('LearningMaterial', $learningMaterialRecord);
    }
    public function test_add_aggreateLearningAttachment()
    {
        $this->add();
        $this->seeStatusCode(201);
        
        $attachmentOneResponse = [
            'disabled' => false,
            'firmFileInfo' => [
                'id' => $this->firmFileInfoOne->id,
                'path' => $this->firmFileInfoOne->fileInfo->getFullyPath(),
            ],
        ];
        $this->seeJsonContains($attachmentOneResponse);
        
        $attachmentTwoResponse = [
            'disabled' => false,
            'firmFileInfo' => [
                'id' => $this->firmFileInfoTwo->id,
                'path' => $this->firmFileInfoTwo->fileInfo->getFullyPath(),
            ],
        ];
        $this->seeJsonContains($attachmentTwoResponse);
        
        $attachmentOneRecord = [
            'FirmFileInfo_id' => $this->firmFileInfoOne->id,
            'disabled' => false,
        ];
        $this->seeInDatabase('LearningAttachment', $attachmentOneRecord);
        
        $attachmentTwoRecord = [
            'FirmFileInfo_id' => $this->firmFileInfoTwo->id,
            'disabled' => false,
        ];
        $this->seeInDatabase('LearningAttachment', $attachmentTwoRecord);
    }
    public function test_add_emptyName_400()
    {
        $this->learningMaterialRequest['name'] = '';
        $this->add();
        $this->seeStatusCode(400);
    }
    public function test_add_unaccessibleMission_403()
    {
        $this->otherFirm->insert($this->connection);
        $this->missionOne->program->firm = $this->otherFirm;
        
        $this->add();
        $this->seeStatusCode(403);
    }
    public function test_add_unusableFirmFileInfo_belongsToOtherFirm()
    {
        $this->otherFirm->insert($this->connection);
        $this->firmFileInfoOne->firm = $this->otherFirm;
        
        $this->add();
        $this->seeStatusCode(403);
    }
    public function test_add_inactiveManager_401()
    {
        $this->manager->removed = true;
        
        $this->add();
        $this->seeStatusCode(401);
    }
    
    protected function update()
    {
        $this->persistManagerDependency();
        
        $this->learningMaterialOne_m1->mission->program->insert($this->connection);
        $this->learningMaterialOne_m1->mission->insert($this->connection);
        $this->learningMaterialOne_m1->insert($this->connection);
        
        $this->learningAttachmentOne_lm1->firmFileInfo->insert($this->connection);
        $this->learningAttachmentOne_lm1->insert($this->connection);
        
        $this->firmFileInfoTwo->insert($this->connection);
        
        $uri = $this->managerUri . "/learning-materials/{$this->learningMaterialOne_m1->id}";
        $this->patch($uri, $this->learningMaterialRequest, $this->manager->token);
    }
    public function test_update_200()
    {
        $this->update();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->learningMaterialOne_m1->id,
            'name' => $this->learningMaterialRequest['name'],
            'content' => $this->learningMaterialRequest['content'],
            'removed' => false,
        ];
        $this->seeJsonContains($response);
        
        $learningMaterialRecord = [
            'id' => $this->learningMaterialOne_m1->id,
            'Mission_id' => $this->missionOne->id,
            'name' => $this->learningMaterialRequest['name'],
            'content' => $this->learningMaterialRequest['content'],
            'removed' => false,
        ];
        $this->seeInDatabase('LearningMaterial', $learningMaterialRecord);
    }
    public function test_update_aggregateNewlearningAttachment_200()
    {
        $this->update();
        $this->seeStatusCode(200);
        
        $attachmentTwoResponse = [
            'disabled' => false,
            'firmFileInfo' => [
                'id' => $this->firmFileInfoTwo->id,
                'path' => $this->firmFileInfoTwo->fileInfo->getFullyPath(),
            ],
        ];
        $this->seeJsonContains($attachmentTwoResponse);
        
        $attachmentTwoRecord = [
            'LearningMaterial_id' => $this->learningMaterialOne_m1->id,
            'FirmFileInfo_id' => $this->firmFileInfoTwo->id,
            'disabled' => false,
        ];
        $this->seeInDatabase('LearningAttachment', $attachmentTwoRecord);
    }
    public function test_update_disbleUnusedAttachment_200()
    {
        $this->learningMaterialRequest['attachmentFileIdList'] = [$this->firmFileInfoTwo->id];
        $this->update();
        $this->seeStatusCode(200);
        
        $attachmentOneRecord = [
            'id' => $this->learningAttachmentOne_lm1->id,
            'disabled' => true,
        ];
        $this->seeInDatabase('LearningAttachment', $attachmentOneRecord);
    }
    public function test_update_keepOldAttachment_200()
    {
        $this->update();
        $this->seeStatusCode(200);
        
        $attachmentOneResponse = [
            'id' => $this->learningAttachmentOne_lm1->id,
            'disabled' => false,
        ];
        $this->seeJsonContains($attachmentOneResponse);
        
        $attachmentOneRecord = [
            'id' => $this->learningAttachmentOne_lm1->id,
            'disabled' => false,
        ];
        $this->seeInDatabase('LearningAttachment', $attachmentOneRecord);
    }
    public function test_update_enableOldAttachment_200()
    {
        $this->learningAttachmentOne_lm1->disabled = true;
        
        $this->update();
        $this->seeStatusCode(200);
        
        $attachmentOneResponse = [
            'id' => $this->learningAttachmentOne_lm1->id,
            'disabled' => false,
        ];
        $this->seeJsonContains($attachmentOneResponse);
        
        $attachmentOneRecord = [
            'id' => $this->learningAttachmentOne_lm1->id,
            'disabled' => false,
        ];
        $this->seeInDatabase('LearningAttachment', $attachmentOneRecord);
    }
    public function test_update_emptyName_400()
    {
        $this->learningMaterialRequest['name'] = '';
        
        $this->update();
        $this->seeStatusCode(400);
    }
    public function test_update_inaccessibleLearningMaterial_403()
    {
        $this->otherFirm->insert($this->connection);
        $this->learningMaterialOne_m1->mission->program->firm = $this->otherFirm;
        
        $this->update();
        $this->seeStatusCode(403);
    }
    public function test_update_inaccessibleFirmFileInfo_403()
    {
        $this->otherFirm->insert($this->connection);
        $this->firmFileInfoTwo->firm = $this->otherFirm;
        
        $this->update();
        $this->seeStatusCode(403);
    }
    public function test_update_inactiveManager_401()
    {
        $this->manager->removed = true;
        $this->update();
        $this->seeStatusCode(401);
    }
    
    protected function remove()
    {
        $this->persistManagerDependency();
        
        $this->learningMaterialOne_m1->mission->program->insert($this->connection);
        $this->learningMaterialOne_m1->mission->insert($this->connection);
        $this->learningMaterialOne_m1->insert($this->connection);
        
        $uri = $this->managerUri . "/learning-materials/{$this->learningMaterialOne_m1->id}";
        $this->delete($uri, [], $this->manager->token);
    }
    public function test_remove_200()
    {
        $this->remove();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->learningMaterialOne_m1->id,
            'removed' => true,
        ];
        $this->seeJsonContains($response);
        
        $learningMaterialRecord = [
            'id' => $this->learningMaterialOne_m1->id,
            'removed' => true,
        ];
        $this->seeInDatabase('LearningMaterial', $learningMaterialRecord);
    }
    public function test_remove_inaccessibleLearningMaterial_403()
    {
        $this->otherFirm->insert($this->connection);
        $this->learningMaterialOne_m1->mission->program->firm = $this->otherFirm;
        
        $this->remove();
        $this->seeStatusCode(403);
    }
    public function test_remove_inactiveManager_401()
    {
        $this->manager->removed = true;
        
        $this->remove();
        $this->seeStatusCode(401);
    }
    
    protected function show()
    {
        $this->persistManagerDependency();
        
        $this->learningMaterialOne_m1->mission->program->insert($this->connection);
        $this->learningMaterialOne_m1->mission->insert($this->connection);
        $this->learningMaterialOne_m1->insert($this->connection);
        
        $this->learningAttachmentOne_lm1->firmFileInfo->insert($this->connection);
        $this->learningAttachmentTwo_lm2->firmFileInfo->insert($this->connection);
        
        $this->learningAttachmentOne_lm1->insert($this->connection);
        $this->learningAttachmentTwo_lm2->insert($this->connection);
        
        $uri = $this->managerUri . "/learning-materials/{$this->learningMaterialOne_m1->id}";
        $this->get($uri, $this->manager->token);
    }
    public function test_show_200()
    {
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->learningMaterialOne_m1->id,
            'name' => $this->learningMaterialOne_m1->name,
            'content' => $this->learningMaterialOne_m1->content,
            'removed' => false,
            'learningAttachments' => [
                [
                    'id' => $this->learningAttachmentOne_lm1->id,
                    'disabled' => $this->learningAttachmentOne_lm1->disabled,
                    'firmFileInfo' => [
                        'id' => $this->learningAttachmentOne_lm1->firmFileInfo->id,
                        'path' => $this->learningAttachmentOne_lm1->firmFileInfo->fileInfo->getFullyPath(),
                    ],
                ],
                [
                    'id' => $this->learningAttachmentTwo_lm2->id,
                    'disabled' => $this->learningAttachmentTwo_lm2->disabled,
                    'firmFileInfo' => [
                        'id' => $this->learningAttachmentTwo_lm2->firmFileInfo->id,
                        'path' => $this->learningAttachmentTwo_lm2->firmFileInfo->fileInfo->getFullyPath(),
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_show_excludeInactiveAttachment_200()
    {
        $this->learningAttachmentOne_lm1->disabled = true;
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->learningMaterialOne_m1->id,
            'name' => $this->learningMaterialOne_m1->name,
            'content' => $this->learningMaterialOne_m1->content,
            'removed' => false,
            'learningAttachments' => [
                [
                    'id' => $this->learningAttachmentTwo_lm2->id,
                    'disabled' => $this->learningAttachmentTwo_lm2->disabled,
                    'firmFileInfo' => [
                        'id' => $this->learningAttachmentTwo_lm2->firmFileInfo->id,
                        'path' => $this->learningAttachmentTwo_lm2->firmFileInfo->fileInfo->getFullyPath(),
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_show_inactiveManager_401()
    {
        $this->manager->removed = true;
        $this->show();
        $this->seeStatusCode(401);
    }
    
    protected function showAll()
    {
        $this->persistManagerDependency();
        
        $this->learningMaterialOne_m1->mission->program->insert($this->connection);
        
        $this->learningMaterialOne_m1->mission->insert($this->connection);
        $this->learningMaterialThree_m2->mission->insert($this->connection);
        
        $this->learningMaterialOne_m1->insert($this->connection);
        $this->learningMaterialTwo_m1->insert($this->connection);
        $this->learningMaterialThree_m2->insert($this->connection);
        
        $this->get($this->showAllUri, $this->manager->token);
    }
    public function test_showAll_200()
    {
        $this->showAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 3,
            'list' => [
                [
                    'id' => $this->learningMaterialOne_m1->id,
                    'name' => $this->learningMaterialOne_m1->name,
                    'removed' => $this->learningMaterialOne_m1->removed,
                ],
                [
                    'id' => $this->learningMaterialTwo_m1->id,
                    'name' => $this->learningMaterialTwo_m1->name,
                    'removed' => $this->learningMaterialTwo_m1->removed,
                ],
                [
                    'id' => $this->learningMaterialThree_m2->id,
                    'name' => $this->learningMaterialThree_m2->name,
                    'removed' => $this->learningMaterialThree_m2->removed,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_show_missionFilterApplied_200_onlyShowLearningMaterialFromSameMission()
    {
        $this->showAllUri .= "?missionId={$this->missionOne->id}";
        
        $this->showAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 2,
            'list' => [
                [
                    'id' => $this->learningMaterialOne_m1->id,
                    'name' => $this->learningMaterialOne_m1->name,
                    'removed' => $this->learningMaterialOne_m1->removed,
                ],
                [
                    'id' => $this->learningMaterialTwo_m1->id,
                    'name' => $this->learningMaterialTwo_m1->name,
                    'removed' => $this->learningMaterialTwo_m1->removed,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_inactiveManager_401()
    {
        $this->manager->removed = true;
        $this->showAll();
        $this->seeStatusCode(401);
    }
}
