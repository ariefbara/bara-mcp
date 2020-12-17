<?php

namespace Tests\Controllers\User\ProgramRegistration;

use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfProgramsProfileForm;
use Tests\Controllers\RecordPreparation\Firm\Program\Registrant\RecordOfRegistrantProfile;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProfileForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class ProfileControllerTest extends ProgramRegistrationTestCase
{
    protected $profileUri;
    protected $profileOne;
    protected $profileTwo_removed;
    protected $programsProfileForm;
    protected $submitInput = [
        "stringFieldRecords" => [],
        "integerFieldRecords" => [],
        "textAreaFieldRecords" => [],
        "attachmentFieldRecords" => [],
        "singleSelectFieldRecords" => [],
        "multiSelectFieldRecords" => [],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->profileUri = $this->programRegistrationUri . "/{$this->programRegistration->id}/profiles";
        
        $this->connection->table("Form")->truncate();
        $this->connection->table("ProfileForm")->truncate();
        $this->connection->table("ProgramsProfileForm")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("RegistrantProfile")->truncate();
        
        $registrant = $this->programRegistration->registrant;
        $program = $registrant->program;
        $firm = $program->firm;
        
        $form = new RecordOfForm(0);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        
        $profileForm = new RecordOfProfileForm($firm, $form);
        $this->connection->table("ProfileForm")->insert($profileForm->toArrayForDbEntry());
        
        $this->programsProfileForm = new RecordOfProgramsProfileForm($program, $profileForm, 0);
        $programsProfileFormOne = new RecordOfProgramsProfileForm($program, $profileForm, 1);
        $programsProfileFormTwo = new RecordOfProgramsProfileForm($program, $profileForm, 2);
        $this->connection->table("ProgramsProfileForm")->insert($this->programsProfileForm->toArrayForDbEntry());
        $this->connection->table("ProgramsProfileForm")->insert($programsProfileFormOne->toArrayForDbEntry());
        $this->connection->table("ProgramsProfileForm")->insert($programsProfileFormTwo->toArrayForDbEntry());
        
        $formRecordOne = new RecordOfFormRecord($form, 1);
        $formRecordTwo = new RecordOfFormRecord($form, 2);
        $this->connection->table("FormRecord")->insert($formRecordOne->toArrayForDbEntry());
        $this->connection->table("FormRecord")->insert($formRecordTwo->toArrayForDbEntry());
        
        $this->profileOne = new RecordOfRegistrantProfile($registrant, $programsProfileFormOne, $formRecordOne);
        $this->profileTwo_removed = new RecordOfRegistrantProfile($registrant, $programsProfileFormTwo, $formRecordTwo);
        $this->profileTwo_removed->removed = true;
        $this->connection->table("RegistrantProfile")->insert($this->profileOne->toArrayForDbEntry());
        $this->connection->table("RegistrantProfile")->insert($this->profileTwo_removed->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Form")->truncate();
        $this->connection->table("ProfileForm")->truncate();
        $this->connection->table("ProgramsProfileForm")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("RegistrantProfile")->truncate();
    }
    
    public function test_submit_200()
    {
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("RegistrantProfile")->truncate();
        
        $response = [
            "submitTime" => (new \DateTimeImmutable())->format("Y-m-d H:i:s"),
            "programsProfileForm" => [
                "id" => $this->programsProfileForm->id,
                "profileForm" => [
                    "id" => $this->programsProfileForm->profileForm->id,
                    "name" => $this->programsProfileForm->profileForm->form->name,
                ],
            ],
        ];
        
        $uri = $this->profileUri . "/{$this->programsProfileForm->id}";
        $this->put($uri, $this->submitInput, $this->programRegistration->user->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
        
        $registrantProfileEntry = [
            "Registrant_id" => $this->programRegistration->registrant->id,
            "ProgramsProfileForm_id" => $this->programsProfileForm->id,
            "removed" => false
        ];
        $this->seeInDatabase("RegistrantProfile", $registrantProfileEntry);
        
        $formRecordEntry = [
            "submitTime" => (new \DateTimeImmutable())->format("Y-m-d H:i:s"),
            "Form_id" => $this->programsProfileForm->profileForm->form->id,
        ];
        $this->seeInDatabase("FormRecord", $formRecordEntry);
    }
    
    public function test_remove_200()
    {
        $uri = $this->profileUri . "/{$this->profileOne->programsProfileForm->id}";
        $this->delete($uri, [], $this->programRegistration->user->token)
                ->seeStatusCode(200);
        
        $registrantProfileEntry = [
            "id" => $this->profileOne->id,
            "removed" => true,
        ];
        $this->seeInDatabase("RegistrantProfile", $registrantProfileEntry);
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->profileOne->id,
            "programsProfileForm" => [
                "id" => $this->profileOne->programsProfileForm->id,
                "profileForm" => [
                    "id" => $this->profileOne->programsProfileForm->profileForm->id,
                    "name" => $this->profileOne->programsProfileForm->profileForm->form->name,
                ],
            ],
            "submitTime" => $this->profileOne->formRecord->submitTime,
            "stringFieldRecords" => [],
            "integerFieldRecords" => [],
            "textAreaFieldRecords" => [],
            "attachmentFieldRecords" => [],
            "singleSelectFieldRecords" => [],
            "multiSelectFieldRecords" => [],
        ];
        $uri = $this->profileUri . "/{$this->profileOne->programsProfileForm->id}";
        $this->get($uri, $this->programRegistration->user->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    
    public function test_showAll_scenario_expectedResult()
    {
        $response = [
            "total" => 1,
            "list" => [
                [
                    "id" => $this->profileOne->id,
                    "programsProfileForm" => [
                        "id" => $this->profileOne->programsProfileForm->id,
                        "profileForm" => [
                            "id" => $this->profileOne->programsProfileForm->profileForm->id,
                            "name" => $this->profileOne->programsProfileForm->profileForm->form->name,
                        ],
                    ],
                    "submitTime" => $this->profileOne->formRecord->submitTime,
                ],
            ],
        ];
        $this->get($this->profileUri, $this->programRegistration->user->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
