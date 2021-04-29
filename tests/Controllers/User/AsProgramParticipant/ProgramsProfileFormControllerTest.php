<?php

namespace Tests\Controllers\User\AsProgramParticipant;

use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfProgramsProfileForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProfileForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;

class ProgramsProfileFormControllerTest extends AsProgramParticipantTestCase
{
    protected $programsProfileFormUri;
    protected $programsProfileFormOne;
    protected $programsProfileFormTwo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->programsProfileFormUri = $this->asProgramParticipantUri . "/programs-profile-forms";
        $this->connection->table("Form")->truncate();
        $this->connection->table("ProfileForm")->truncate();
        $this->connection->table("ProgramsProfileForm")->truncate();
        
        $program = $this->programParticipation->participant->program;
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
        $this->programsProfileFormTwo = new RecordOfProgramsProfileForm($program, $profileFormTwo, 2);
        $this->connection->table("ProgramsProfileForm")->insert($this->programsProfileFormOne->toArrayForDbEntry());
        $this->connection->table("ProgramsProfileForm")->insert($this->programsProfileFormTwo->toArrayForDbEntry());
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
        $this->get($uri, $this->programParticipation->user->token)
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
                    "disabled" => false,
                    "profileForm" => [
                        "id" => $this->programsProfileFormOne->profileForm->id,
                        "name" => $this->programsProfileFormOne->profileForm->form->name,
                    ],
                ],
                [
                    "id" => $this->programsProfileFormTwo->id,
                    "disabled" => false,
                    "profileForm" => [
                        "id" => $this->programsProfileFormTwo->profileForm->id,
                        "name" => $this->programsProfileFormTwo->profileForm->form->name,
                    ],
                ],
            ],
        ];
        
        $this->get($this->programsProfileFormUri, $this->programParticipation->user->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
