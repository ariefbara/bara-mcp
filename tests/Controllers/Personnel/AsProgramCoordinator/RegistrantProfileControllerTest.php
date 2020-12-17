<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator;

use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfProgramsProfileForm;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfRegistrant;
use Tests\Controllers\RecordPreparation\Firm\Program\Registrant\RecordOfRegistrantProfile;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProfileForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class RegistrantProfileControllerTest extends AsProgramCoordinatorTestCase
{
    protected $registrant;
    protected $registrantProfileOne;
    protected $registrantProfileTwo_removed;
    
    protected function setUp(): void
    {
        parent::setUp();
        $program = $this->coordinator->program;
        
        $this->connection->table("Registrant")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("ProfileForm")->truncate();
        $this->connection->table("ProgramsProfileForm")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("RegistrantProfile")->truncate();
        
        $this->registrant = new RecordOfRegistrant($program, 0);
        $this->connection->table("Registrant")->insert($this->registrant->toArrayForDbEntry());
        
        $form = new RecordOfForm(0);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        
        $profileForm = new RecordOfProfileForm($program->firm, $form);
        $this->connection->table("ProfileForm")->insert($profileForm->toArrayForDbEntry());
        
        $programsProfileFormOne = new RecordOfProgramsProfileForm($program, $profileForm, 1);
        $programsProfileFormTwo = new RecordOfProgramsProfileForm($program, $profileForm, 2);
        $this->connection->table("ProgramsProfileForm")->insert($programsProfileFormOne->toArrayForDbEntry());
        $this->connection->table("ProgramsProfileForm")->insert($programsProfileFormTwo->toArrayForDbEntry());
        
        $formRecordOne = new RecordOfFormRecord($form, 1);
        $formRecordTwo = new RecordOfFormRecord($form, 2);
        $this->connection->table("FormRecord")->insert($formRecordOne->toArrayForDbEntry());
        $this->connection->table("FormRecord")->insert($formRecordTwo->toArrayForDbEntry());
        
        $this->registrantProfileOne = new RecordOfRegistrantProfile($this->registrant, $programsProfileFormOne, $formRecordOne);
        $this->registrantProfileTwo_removed = new RecordOfRegistrantProfile($this->registrant, $programsProfileFormTwo, $formRecordTwo);
        $this->registrantProfileTwo_removed->removed = true;
        $this->connection->table("RegistrantProfile")->insert($this->registrantProfileOne->toArrayForDbEntry());
        $this->connection->table("RegistrantProfile")->insert($this->registrantProfileTwo_removed->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Registrant")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("ProfileForm")->truncate();
        $this->connection->table("ProgramsProfileForm")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("RegistrantProfile")->truncate();
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->registrantProfileOne->id,
            "programsProfileForm" => [
                "id" => $this->registrantProfileOne->programsProfileForm->id,
                "profileForm" => [
                    "id" => $this->registrantProfileOne->programsProfileForm->profileForm->id,
                    "name" => $this->registrantProfileOne->programsProfileForm->profileForm->form->name,
                ],
            ],
            "submitTime" => $this->registrantProfileOne->formRecord->submitTime,
            "stringFieldRecords" => [],
            "integerFieldRecords" => [],
            "textAreaFieldRecords" => [],
            "attachmentFieldRecords" => [],
            "singleSelectFieldRecords" => [],
            "multiSelectFieldRecords" => [],
        ];
        $uri = $this->asProgramCoordinatorUri . "/registrant-profiles/{$this->registrantProfileOne->id}";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 1,
            "list" => [
                [
                    "id" => $this->registrantProfileOne->id,
                    "programsProfileForm" => [
                        "id" => $this->registrantProfileOne->programsProfileForm->id,
                        "profileForm" => [
                            "id" => $this->registrantProfileOne->programsProfileForm->profileForm->id,
                            "name" => $this->registrantProfileOne->programsProfileForm->profileForm->form->name,
                        ],
                    ],
                ],
            ],
        ];
        $uri = $this->asProgramCoordinatorUri . "/registrants/{$this->registrant->id}/registrant-profiles";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
}
