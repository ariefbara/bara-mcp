<?php

namespace Tests\Controllers\User\AsProgramRegistrant;

use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfProgramsProfileForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProfileForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;

class ProgramsProfileFormControllerTest extends AsProgramRegistrantTestCase
{
    protected $programsProfileFormUri;
    protected $programsProfileFormOne;
    protected $programsProfileFormTwo_disabled;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->programsProfileFormUri = $this->asProgramRegistrantUri . "/programs-profile-forms";
        
        $this->connection->table("Form")->truncate();
        $this->connection->table("ProfileForm")->truncate();
        $this->connection->table("ProgramsProfileForm")->truncate();
        
        $program = $this->programRegistration->registrant->program;
        $firm = $program->firm;

        $formOne = new RecordOfForm(1);
        $formTwo = new RecordOfForm(2);
        $this->connection->table("Form")->insert($formOne->toArrayForDbEntry());
        $this->connection->table("Form")->insert($formTwo->toArrayForDbEntry());
        
        $profileFormOne = new RecordOfProfileForm($firm, $formOne);
        $profileFormTwo = new RecordOfProfileForm($firm, $formTwo);
        $this->connection->table("ProfileForm")->insert($profileFormOne->toArrayForDbEntry());
        $this->connection->table("ProfileForm")->insert($profileFormTwo->toArrayForDbEntry());
        
        $this->programsProfileFormOne = new RecordOfProgramsProfileForm($program, $profileFormOne, 1);
        $this->programsProfileFormTwo_disabled = new RecordOfProgramsProfileForm($program, $profileFormTwo, 2);
        $this->programsProfileFormTwo_disabled->disabled = true;
        $this->connection->table("ProgramsProfileForm")->insert($this->programsProfileFormOne->toArrayForDbEntry());
        $this->connection->table("ProgramsProfileForm")->insert($this->programsProfileFormTwo_disabled->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Form")->truncate();
        $this->connection->table("ProfileForm")->truncate();
        $this->connection->table("ProgramsProfileForm")->truncate();
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->programsProfileFormOne->id,
            "profileForm" => [
                "id" => $this->programsProfileFormOne->profileForm->id,
                "name" => $this->programsProfileFormOne->profileForm->form->name,
                "description" => $this->programsProfileFormOne->profileForm->form->description,
                "stringFields" => [],
                "integerFields" => [],
                "textAreaFields" => [],
                "attachmentFields" => [],
                "singleSelectFields" => [],
                "multiSelectFields" => [],
            ],
        ];
        
        $uri = $this->programsProfileFormUri . "/{$this->programsProfileFormOne->id}";
        $this->get($uri, $this->programRegistration->user->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_show_concludedRegistrant_403()
    {
        $this->setRegistrationConcluded();
        $uri = $this->programsProfileFormUri . "/{$this->programsProfileFormOne->id}";
        $this->get($uri, $this->programRegistration->user->token)
                ->seeStatusCode(403);
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 1,
            "list" => [
                [
                    "id" => $this->programsProfileFormOne->id,
                    "profileForm" => [
                        "id" => $this->programsProfileFormOne->profileForm->id,
                        "name" => $this->programsProfileFormOne->profileForm->form->name,
                    ],
                ],
            ],
        ];
        
        $this->get($this->programsProfileFormUri, $this->programRegistration->user->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_concludedRegistration_403()
    {
        $this->setRegistrationConcluded();
        $this->get($this->programsProfileFormUri, $this->programRegistration->user->token)
                ->seeStatusCode(403);
    }
}
