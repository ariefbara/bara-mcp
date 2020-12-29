<?php

namespace Tests\Controllers\Client\ProgramParticipation;

use DateTimeImmutable;
use Tests\Controllers\Client\ProgramParticipationTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfParticipantProfile;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfProgramsProfileForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProfileForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class ProfileControllerTest extends ProgramParticipationTestCase
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
        $this->profileUri = $this->programParticipationUri . "/{$this->programParticipation->id}/profiles";
        
        $this->connection->table("Form")->truncate();
        $this->connection->table("ProfileForm")->truncate();
        $this->connection->table("ProgramsProfileForm")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("ParticipantProfile")->truncate();
        
        $participant = $this->programParticipation->participant;
        $program = $participant->program;
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
        
        $this->profileOne = new RecordOfParticipantProfile($participant, $programsProfileFormOne, $formRecordOne);
        $this->profileTwo_removed = new RecordOfParticipantProfile($participant, $programsProfileFormTwo, $formRecordTwo);
        $this->profileTwo_removed->removed = true;
        $this->connection->table("ParticipantProfile")->insert($this->profileOne->toArrayForDbEntry());
        $this->connection->table("ParticipantProfile")->insert($this->profileTwo_removed->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Form")->truncate();
        $this->connection->table("ProfileForm")->truncate();
        $this->connection->table("ProgramsProfileForm")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("ParticipantProfile")->truncate();
    }
    
    public function test_submit_200()
    {
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("ParticipantProfile")->truncate();
        
        $response = [
            "submitTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
            "programsProfileForm" => [
                "id" => $this->programsProfileForm->id,
                "profileForm" => [
                    "id" => $this->programsProfileForm->profileForm->id,
                    "name" => $this->programsProfileForm->profileForm->form->name,
                ],
            ],
        ];
        
        $uri = $this->profileUri . "/{$this->programsProfileForm->id}";
        $this->put($uri, $this->submitInput, $this->programParticipation->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
        
        $participantProfileEntry = [
            "Participant_id" => $this->programParticipation->participant->id,
            "ProgramsProfileForm_id" => $this->programsProfileForm->id,
            "removed" => false
        ];
        $this->seeInDatabase("ParticipantProfile", $participantProfileEntry);
        
        $formRecordEntry = [
            "submitTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
            "Form_id" => $this->programsProfileForm->profileForm->form->id,
        ];
        $this->seeInDatabase("FormRecord", $formRecordEntry);
    }
    
    public function test_remove_200()
    {
        $uri = $this->profileUri . "/{$this->profileOne->programsProfileForm->id}";
        $this->delete($uri, [], $this->programParticipation->client->token)
                ->seeStatusCode(200);
        
        $participantProfileEntry = [
            "id" => $this->profileOne->id,
            "removed" => true,
        ];
        $this->seeInDatabase("ParticipantProfile", $participantProfileEntry);
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
        $this->get($uri, $this->programParticipation->client->token)
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
        $this->get($this->profileUri, $this->programParticipation->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
