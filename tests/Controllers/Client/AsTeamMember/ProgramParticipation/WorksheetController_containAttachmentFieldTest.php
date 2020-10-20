<?php

namespace Tests\Controllers\Client\AsTeamMember\ProgramParticipation;

use Tests\Controllers\RecordPreparation\ {
    Firm\Team\RecordOfTeamFileInfo,
    Shared\Form\RecordOfAttachmentField,
    Shared\FormRecord\AttachmentFieldRecord\RecordOfAttachedFile,
    Shared\FormRecord\RecordOfAttachmentFieldRecord,
    Shared\RecordOfFileInfo
};

class WorksheetController_containAttachmentFieldTest extends WorksheetTestCase
{
    protected $fieldOne, $fieldTwo_required, $fieldThree_removed;
    protected $fieldRecordOne, $fieldRecordThree;
    protected $fileInfoOneOne, $fileInfoOneTwo, $fileInfoOneThree, $fileInfoTwoOne, $fileInfoThreeOne;
    protected $attachedFileOneOne, $attachedFileOneThree, $attachedFileThreeOne;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->connection->table('AttachmentField')->truncate();
        $this->connection->table('AttachmentFieldRecord')->truncate();
        $this->connection->table('AttachedFile')->truncate();
        $this->connection->table('TeamFileInfo')->truncate();
        $this->connection->table('FileInfo')->truncate();
        
        $this->fieldOne = new RecordOfAttachmentField($this->form, 1);
        $this->fieldOne->minValue = 2;
        $this->fieldOne->maxValue = 2;
        
        $this->fieldTwo_required = new RecordOfAttachmentField($this->form, 2);
        $this->fieldTwo_required->mandatory = true;
        
        $this->fieldThree_removed = new RecordOfAttachmentField($this->form, 3);
        $this->fieldThree_removed->removed = true;
        
        $this->connection->table('AttachmentField')->insert($this->fieldOne->toArrayForDbEntry());
        $this->connection->table('AttachmentField')->insert($this->fieldTwo_required->toArrayForDbEntry());
        $this->connection->table('AttachmentField')->insert($this->fieldThree_removed->toArrayForDbEntry());
        
        $this->fieldRecordOne = new RecordOfAttachmentFieldRecord($this->worksheet->formRecord, $this->fieldOne, 1);
        $this->fieldRecordThree = new RecordOfAttachmentFieldRecord($this->worksheet->formRecord, $this->fieldThree_removed, 3);
        
        $this->connection->table('AttachmentFieldRecord')->insert($this->fieldRecordOne->toArrayForDbEntry());
        $this->connection->table('AttachmentFieldRecord')->insert($this->fieldRecordThree->toArrayForDbEntry());
        
        $this->fileInfoOneOne = new RecordOfFileInfo(11);
        $this->fileInfoOneTwo = new RecordOfFileInfo(12);
        $this->fileInfoOneThree = new RecordOfFileInfo(13);
        $this->fileInfoTwoOne = new RecordOfFileInfo(21);
        $this->fileInfoThreeOne = new RecordOfFileInfo(31);
        
        $this->connection->table('FileInfo')->insert($this->fileInfoOneOne->toArrayForDbEntry());
        $this->connection->table('FileInfo')->insert($this->fileInfoOneTwo->toArrayForDbEntry());
        $this->connection->table('FileInfo')->insert($this->fileInfoOneThree->toArrayForDbEntry());
        $this->connection->table('FileInfo')->insert($this->fileInfoTwoOne->toArrayForDbEntry());
        $this->connection->table('FileInfo')->insert($this->fileInfoThreeOne->toArrayForDbEntry());
        
        $team = $this->teamMember->team;
        
        $teamFileInfoOneOne = new RecordOfTeamFileInfo($team, $this->fileInfoOneOne);
        $teamFileInfoOneTwo = new RecordOfTeamFileInfo($team, $this->fileInfoOneTwo);
        $teamFileInfoOneThree = new RecordOfTeamFileInfo($team, $this->fileInfoOneThree);
        $teamFileInfoTwoOne = new RecordOfTeamFileInfo($team, $this->fileInfoTwoOne);
        $teamFileInfoThreeOne = new RecordOfTeamFileInfo($team, $this->fileInfoThreeOne);
        $this->connection->table('TeamFileInfo')->insert($teamFileInfoOneOne->toArrayForDbEntry());
        $this->connection->table('TeamFileInfo')->insert($teamFileInfoOneTwo->toArrayForDbEntry());
        $this->connection->table('TeamFileInfo')->insert($teamFileInfoOneThree->toArrayForDbEntry());
        $this->connection->table('TeamFileInfo')->insert($teamFileInfoTwoOne->toArrayForDbEntry());
        $this->connection->table('TeamFileInfo')->insert($teamFileInfoThreeOne->toArrayForDbEntry());
        
        $this->attachedFileOneOne = new RecordOfAttachedFile($this->fieldRecordOne, $this->fileInfoOneOne, 11);
        $this->attachedFileOneThree = new RecordOfAttachedFile($this->fieldRecordOne, $this->fileInfoOneThree, 13);
        $this->attachedFileThreeOne = new RecordOfAttachedFile($this->fieldRecordThree, $this->fileInfoThreeOne, 31);
        
        $this->connection->table('AttachedFile')->truncate();
        $this->connection->table('AttachedFile')->insert($this->attachedFileOneOne->toArrayForDbEntry());
        $this->connection->table('AttachedFile')->insert($this->attachedFileOneThree->toArrayForDbEntry());
        $this->connection->table('AttachedFile')->insert($this->attachedFileThreeOne->toArrayForDbEntry());
        
        $this->worksheetInput['attachmentFieldRecords'] = [
            [
                "fieldId" => $this->fieldOne->id,
                "fileInfoIdList" => [
                    $this->fileInfoOneOne->id,
                    $this->fileInfoOneTwo->id,
                ],
            ],
            [
                "fieldId" => $this->fieldTwo_required->id,
                "fileInfoIdList" => [
                    $this->fileInfoTwoOne->id,
                ],
            ],
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('AttachmentField')->truncate();
        $this->connection->table('AttachmentFieldRecord')->truncate();
        $this->connection->table('AttachedFile')->truncate();
        $this->connection->table('TeamFileInfo')->truncate();
        $this->connection->table('FileInfo')->truncate();
    }
    public function test_addRoot()
    {
$this->disableExceptionHandling();
        $fieldRecordOneResponse = [
            "attachmentField" => [
                "id" => $this->fieldOne->id,
                "name" => $this->fieldOne->name,
                "position" => $this->fieldOne->position,
            ],
        ];
        $fieldRecordTwoResponse = [
            "attachmentField" => [
                "id" => $this->fieldTwo_required->id,
                "name" => $this->fieldTwo_required->name,
                "position" => $this->fieldTwo_required->position,
            ],
        ];
        $attachedFileOneOneResponse = [
            "fileInfo" => [
                "id" => $this->worksheetInput['attachmentFieldRecords'][0]['fileInfoIdList'][0],
                "path" => "/".$this->fileInfoOneOne->name,
            ],
            
        ];
        $attachedFileOneTwoResponse = [
            "fileInfo" => [
                "id" => $this->worksheetInput['attachmentFieldRecords'][0]['fileInfoIdList'][1],
                "path" => "/".$this->fileInfoOneTwo->name,
            ],
            
        ];
        $attachedFileTwoOneResponse = [
            "fileInfo" => [
                "id" => $this->worksheetInput['attachmentFieldRecords'][1]['fileInfoIdList'][0],
                "path" => "/".$this->fileInfoTwoOne->name,
            ],
        ];

        $this->post($this->worksheetUri, $this->worksheetInput, $this->teamMember->client->token)
                ->seeStatusCode(201)
                ->seeJsonContains($fieldRecordOneResponse)
                ->seeJsonContains($fieldRecordTwoResponse)
                ->seeJsonContains($attachedFileOneOneResponse)
                ->seeJsonContains($attachedFileOneTwoResponse)
                ->seeJsonContains($attachedFileTwoOneResponse);

        $fieldOneRecordEntry = [
            "AttachmentField_id" => $this->fieldOne->id,
        ];
        $this->seeInDatabase('AttachmentFieldRecord', $fieldOneRecordEntry);
        
        $fieldTwoRecordEntry = [
            "AttachmentField_id" => $this->fieldTwo_required->id,
        ];
        $this->seeInDatabase('AttachmentFieldRecord', $fieldTwoRecordEntry);
        
        $attachedFileOneOneEntry = [
            "FileInfo_id" => $this->worksheetInput['attachmentFieldRecords'][0]['fileInfoIdList'][0],
        ];
        $this->seeInDatabase('AttachedFile', $attachedFileOneOneEntry);
        
        $attachedFileOneTwoEntry = [
            "FileInfo_id" => $this->worksheetInput['attachmentFieldRecords'][0]['fileInfoIdList'][1],
        ];
        $this->seeInDatabase('AttachedFile', $attachedFileOneTwoEntry);
        
        $attachedFileTwoOneEntry = [
            "FileInfo_id" => $this->worksheetInput['attachmentFieldRecords'][1]['fileInfoIdList'][0],
        ];
        $this->seeInDatabase('AttachedFile', $attachedFileTwoOneEntry);
    }
    public function test_addRoot_emptyInputForMandatoryField_error400()
    {
        $this->worksheetInput['attachmentFieldRecords'][1]['fileInfoIdList'] = [];
        $this->post($this->worksheetUri, $this->worksheetInput, $this->teamMember->client->token)
                ->seeStatusCode(400);
    }

    public function test_addRoot_fieldRecordInputOptionNotFound_error404()
    {
        $this->worksheetInput['attachmentFieldRecords'][1]['fileInfoIdList'] = ['non-existing-file-info'];
        $this->post($this->worksheetUri, $this->worksheetInput, $this->teamMember->client->token)
                ->seeStatusCode(404);
    }
    public function test_addRoot_attachedFilesCountLessThanMinValue_error400()
    {
        $this->worksheetInput['attachmentFieldRecords'][0]['fileInfoIdList'] = [$this->fileInfoOneOne->id];
        $this->post($this->worksheetUri, $this->worksheetInput, $this->teamMember->client->token)
                ->seeStatusCode(400);
    }
    public function test_addRoot_attachedFileCountGreaterThanMaxValue_error400()
    {
        $this->worksheetInput['attachmentFieldRecords'][0]['fileInfoIdList'] = [
            $this->fileInfoOneOne->id,
            $this->fileInfoOneTwo->id,
            $this->fileInfoOneThree->id,
        ];
        $this->post($this->worksheetUri, $this->worksheetInput, $this->teamMember->client->token)
                ->seeStatusCode(400);
    }
    public function test_show()
    {
        $this->worksheet->formRecordResponse['attachmentFieldRecords'] = [
            [
                "id" => $this->fieldRecordOne->id,
                "attachmentField" => [
                    "id" => $this->fieldRecordOne->attachmentField->id,
                    "name" => $this->fieldRecordOne->attachmentField->name,
                    "position" => $this->fieldRecordOne->attachmentField->position,
                ],
                "attachedFiles" => [
                    [
                        "id" => $this->attachedFileOneOne->id,
                        "fileInfo" => [
                            "id" => $this->attachedFileOneOne->fileInfo->id,
                            "path" => "/".$this->attachedFileOneOne->fileInfo->name,
                        ],
                    ],
                    [
                        "id" => $this->attachedFileOneThree->id,
                        "fileInfo" => [
                            "id" => $this->attachedFileOneThree->fileInfo->id,
                            "path" => "/".$this->attachedFileOneThree->fileInfo->name,
                        ],
                    ],
                ],
            ],
            [
                "id" => $this->fieldRecordThree->id,
                "attachmentField" => [
                    "id" => $this->fieldRecordThree->attachmentField->id,
                    "name" => $this->fieldRecordThree->attachmentField->name,
                    "position" => $this->fieldRecordThree->attachmentField->position,
                ],
                "attachedFiles" => [
                    [
                        "id" => $this->attachedFileThreeOne->id,
                        "fileInfo" => [
                            "id" => $this->attachedFileThreeOne->fileInfo->id,
                            "path" => "/".$this->attachedFileThreeOne->fileInfo->name,
                        ],
                    ],
                ],
            ],
        ];
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->get($uri, $this->teamMember->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($this->worksheet->formRecordResponse);
    }
    
    
    
    
    public function test_update_updateExistingFieldRecord()
    {
        $fieldRecordOneResponse = [
            "id" => $this->fieldRecordOne->id,
            "attachmentField" => [
                "id" => $this->fieldOne->id,
                "name" => $this->fieldOne->name,
                "position" => $this->fieldOne->position,
            ],
        ];
        $attachedFileOneOneResponse = [
            "fileInfo" => [
                "id" => $this->worksheetInput['attachmentFieldRecords'][0]['fileInfoIdList'][0],
                "path" => "/".$this->fileInfoOneOne->name,
            ],
            
        ];
        $attachedFileOneTwoResponse = [
            "fileInfo" => [
                "id" => $this->worksheetInput['attachmentFieldRecords'][0]['fileInfoIdList'][1],
                "path" => "/".$this->fileInfoOneTwo->name,
            ],
        ];

        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->patch($uri, $this->worksheetInput, $this->teamMember->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($fieldRecordOneResponse)
                ->seeJsonContains($attachedFileOneOneResponse)
                ->seeJsonContains($attachedFileOneTwoResponse);

        $fieldOneRecordEntry = [
            "id" => $this->fieldRecordOne->id,
            "AttachmentField_id" => $this->fieldOne->id,
        ];
        $this->seeInDatabase('AttachmentFieldRecord', $fieldOneRecordEntry);
        
        $attachedFileOneOneEntry = [
            "FileInfo_id" => $this->worksheetInput['attachmentFieldRecords'][0]['fileInfoIdList'][0],
        ];
        $this->seeInDatabase('AttachedFile', $attachedFileOneOneEntry);
        
        $attachedFileOneTwoEntry = [
            "FileInfo_id" => $this->worksheetInput['attachmentFieldRecords'][0]['fileInfoIdList'][1],
        ];
        $this->seeInDatabase('AttachedFile', $attachedFileOneTwoEntry);
        
    }

    public function test_update_aFieldInFormHasNoCorrespondFieldRecordInWorksheet_addNewFieldRecord()
    {
        $fieldRecordTwoResponse = [
            "attachmentField" => [
                "id" => $this->fieldTwo_required->id,
                "name" => $this->fieldTwo_required->name,
                "position" => $this->fieldTwo_required->position,
            ],
        ];
        $attachedFileTwoOneResponse = [
            "fileInfo" => [
                "id" => $this->worksheetInput['attachmentFieldRecords'][1]['fileInfoIdList'][0],
                "path" => "/".$this->fileInfoTwoOne->name,
            ],
        ];

        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->patch($uri, $this->worksheetInput, $this->teamMember->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($fieldRecordTwoResponse)
                ->seeJsonContains($attachedFileTwoOneResponse);

        $fieldTwoRecordEntry = [
            "AttachmentField_id" => $this->fieldTwo_required->id,
        ];
        $this->seeInDatabase('AttachmentFieldRecord', $fieldTwoRecordEntry);
        
        $attachedFileTwoOneEntry = [
            "FileInfo_id" => $this->worksheetInput['attachmentFieldRecords'][1]['fileInfoIdList'][0],
        ];
        $this->seeInDatabase('AttachedFile', $attachedFileTwoOneEntry);
    }

    public function test_updateremoveFieldRecordReferToRemovedField()
    {
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->patch($uri, $this->worksheetInput, $this->teamMember->client->token)
                ->seeStatusCode(200);
        $fieldThreeRecordEntry = [
            "id" => $this->fieldRecordThree->id,
            "removed" => true,
        ];
        $this->seeInDatabase('AttachmentFieldRecord', $fieldThreeRecordEntry);
    }
    public function test_update_removeExistingAttachedFileNoLongerSelected()
    {
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->patch($uri, $this->worksheetInput, $this->teamMember->client->token)
                ->seeStatusCode(200);
        $attahedFileOneThreeEntry = [
            "id" => $this->attachedFileOneThree->id,
            "removed" => true,
        ];
        $this->seeInDatabase("AttachedFile", $attahedFileOneThreeEntry);
    }
}
