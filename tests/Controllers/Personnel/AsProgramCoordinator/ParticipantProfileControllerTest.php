<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator;

use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfParticipantProfile;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfProgramsProfileForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProfileForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class ParticipantProfileControllerTest extends AsProgramCoordinatorTestCase
{
    protected $participant;
    protected $participantProfileOne;
    protected $participantProfileTwo_removed;
    
    protected function setUp(): void
    {
        parent::setUp();
        $program = $this->coordinator->program;
        
        $this->connection->table("Participant")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("ProfileForm")->truncate();
        $this->connection->table("ProgramsProfileForm")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("ParticipantProfile")->truncate();
        
        $this->participant = new RecordOfParticipant($program, 0);
        $this->connection->table("Participant")->insert($this->participant->toArrayForDbEntry());
        
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
        
        $this->participantProfileOne = new RecordOfParticipantProfile($this->participant, $programsProfileFormOne, $formRecordOne);
        $this->participantProfileTwo_removed = new RecordOfParticipantProfile($this->participant, $programsProfileFormTwo, $formRecordTwo);
        $this->participantProfileTwo_removed->removed = true;
        $this->connection->table("ParticipantProfile")->insert($this->participantProfileOne->toArrayForDbEntry());
        $this->connection->table("ParticipantProfile")->insert($this->participantProfileTwo_removed->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("ProfileForm")->truncate();
        $this->connection->table("ProgramsProfileForm")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("ParticipantProfile")->truncate();
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->participantProfileOne->id,
            "programsProfileForm" => [
                "id" => $this->participantProfileOne->programsProfileForm->id,
                "profileForm" => [
                    "id" => $this->participantProfileOne->programsProfileForm->profileForm->id,
                    "name" => $this->participantProfileOne->programsProfileForm->profileForm->form->name,
                ],
            ],
            "submitTime" => $this->participantProfileOne->formRecord->submitTime,
            "stringFieldRecords" => [],
            "integerFieldRecords" => [],
            "textAreaFieldRecords" => [],
            "attachmentFieldRecords" => [],
            "singleSelectFieldRecords" => [],
            "multiSelectFieldRecords" => [],
        ];
        $uri = $this->asProgramCoordinatorUri . "/participant-profiles/{$this->participantProfileOne->id}";
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
                    "id" => $this->participantProfileOne->id,
                    "programsProfileForm" => [
                        "id" => $this->participantProfileOne->programsProfileForm->id,
                        "profileForm" => [
                            "id" => $this->participantProfileOne->programsProfileForm->profileForm->id,
                            "name" => $this->participantProfileOne->programsProfileForm->profileForm->form->name,
                        ],
                    ],
                ],
            ],
        ];
        $uri = $this->asProgramCoordinatorUri . "/participants/{$this->participant->id}/participant-profiles";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
}
