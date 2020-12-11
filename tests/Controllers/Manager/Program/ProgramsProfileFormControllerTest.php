<?php

namespace Tests\Controllers\Manager\Program;

use Tests\Controllers\Manager\ProgramTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfProgramsProfileForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProfileForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;

class ProgramsProfileFormControllerTest extends ProgramTestCase
{
    protected $programsProfileFormUri;
    protected $programsProfileFormOne;
    protected $programsProfileFormTwo_disable;
    protected $profileForm;
    protected $assignInput;

    protected function setUp(): void
    {
        parent::setUp();
        $this->programsProfileFormUri = $this->programUri . "/{$this->program->id}/programs-profile-forms";
        $this->connection->table("Form")->truncate();
        $this->connection->table("ProfileForm")->truncate();
        $this->connection->table("ProgramsProfileForm")->truncate();
        
        $form = new RecordOfForm(0);
        $formOne = new RecordOfForm(1);
        $formTwo = new RecordOfForm(2);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        $this->connection->table("Form")->insert($formOne->toArrayForDbEntry());
        $this->connection->table("Form")->insert($formTwo->toArrayForDbEntry());
        
        $this->profileForm = new RecordOfProfileForm($this->firm, $form);
        $profileFormOne = new RecordOfProfileForm($this->firm, $formOne);
        $profileFormTwo = new RecordOfProfileForm($this->firm, $formTwo);
        $this->connection->table("ProfileForm")->insert($this->profileForm->toArrayForDbEntry());
        $this->connection->table("ProfileForm")->insert($profileFormOne->toArrayForDbEntry());
        $this->connection->table("ProfileForm")->insert($profileFormTwo->toArrayForDbEntry());
        
        $this->programsProfileFormOne = new RecordOfProgramsProfileForm($this->program, $profileFormOne, 1);
        $this->programsProfileFormTwo_disable = new RecordOfProgramsProfileForm($this->program, $profileFormTwo, 2);
        $this->programsProfileFormTwo_disable->disabled = true;
        $this->connection->table("ProgramsProfileForm")->insert($this->programsProfileFormOne->toArrayForDbEntry());
        $this->connection->table("ProgramsProfileForm")->insert($this->programsProfileFormTwo_disable->toArrayForDbEntry());
        
        $this->assignInput = [
            "profileFormId" => $this->profileForm->id,
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Form")->truncate();
        $this->connection->table("ProfileForm")->truncate();
        $this->connection->table("ProgramsProfileForm")->truncate();
    }
    
    public function test_assign_200()
    {
        $profileFormResponse = [
            "id" => $this->profileForm->id,
            "name" => $this->profileForm->form->name,
            "disabled" => false,
        ];
        
        $this->post($this->programsProfileFormUri, $this->assignInput, $this->manager->token)
                ->seeJsonContains($profileFormResponse)
                ->seeStatusCode(200);
        
        $programsProfileFormEntry = [
            "Program_id" => $this->program->id,
            "ProfileForm_id" => $this->profileForm->id,
            "disabled" => false,
        ];
        $this->seeInDatabase("ProgramsProfileForm", $programsProfileFormEntry);
    }
    
    public function test_disable_200()
    {
        $profileFormResponse = [
            "id" => $this->programsProfileFormOne->id,
            "disabled" => true,
        ];
        
        $uri = $this->programsProfileFormUri . "/{$this->programsProfileFormOne->id}";
        $this->delete($uri, [], $this->manager->token)
                ->seeJsonContains($profileFormResponse)
                ->seeStatusCode(200);
        
        $programsProfileFormEntry = [
            "id" => $this->programsProfileFormOne->id,
            "disabled" => true,
        ];
        $this->seeInDatabase("ProgramsProfileForm", $programsProfileFormEntry);
        $this->seeInDatabase("ProgramsProfileForm", $programsProfileFormEntry);
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->programsProfileFormOne->id,
            "disabled" => false,
            "profileForm" => [
                "id" => $this->programsProfileFormOne->profileForm->id,
                "name" => $this->programsProfileFormOne->profileForm->form->name,
                "description" => $this->programsProfileFormOne->profileForm->form->description,
                "stringFields" => [],
                "integerFields" => [],
                "textAreaFields" => [],
                "singleSelectFields" => [],
                "multiSelectFields" => [],
                "attachmentFields" => [],
                
            ],
        ];
        $uri = $this->programsProfileFormUri . "/{$this->programsProfileFormOne->id}";
        $this->get($uri, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
        
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->programsProfileFormOne->id,
                    "disabled" => $this->programsProfileFormOne->disabled,
                    "profileForm" => [
                        "id" => $this->programsProfileFormOne->profileForm->id,
                        "name" => $this->programsProfileFormOne->profileForm->form->name,
                    ],
                ],
                [
                    "id" => $this->programsProfileFormTwo_disable->id,
                    "disabled" => $this->programsProfileFormTwo_disable->disabled,
                    "profileForm" => [
                        "id" => $this->programsProfileFormTwo_disable->profileForm->id,
                        "name" => $this->programsProfileFormTwo_disable->profileForm->form->name,
                    ],
                ],
            ],
        ];
        
        $this->get($this->programsProfileFormUri, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
