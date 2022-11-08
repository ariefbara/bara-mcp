<?php

namespace Tests\Controllers\Personnel\ProgramConsultation;

use Tests\Controllers\Personnel\ProgramConsultation\ExtendedConsultantTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfParticipantProfile;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfProgramsProfileForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProfileForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class ParticipantProfileControllerTest extends ExtendedConsultantTestCase
{
    protected $participantOne;
    protected $participantProfileOne_p1;
    protected $participantProfileTwo_p1;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('ProfileForm')->truncate();
        $this->connection->table('ProgramsProfileForm')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('ParticipantProfile')->truncate();
        
        $program = $this->consultant->program;
        $firm = $program->firm;
        
        $this->participantOne = new RecordOfParticipant($program, 1);

        $formOne = new RecordOfForm(1);
        $formTwo = new RecordOfForm(2);
        
        $profileFormOne = new RecordOfProfileForm($firm, $formOne);
        $profileFormTwo = new RecordOfProfileForm($firm, $formTwo);
        
        $programsProfileFormOne = new RecordOfProgramsProfileForm($program, $profileFormOne, 1);
        $programsProfileFormTwo = new RecordOfProgramsProfileForm($program, $profileFormTwo, 2);
        
        $formRecordOne = new RecordOfFormRecord($formOne, 1);
        $formRecordTwo = new RecordOfFormRecord($formOne, 2);
        
        $this->participantProfileOne_p1 = new RecordOfParticipantProfile($this->participantOne, $programsProfileFormOne, $formRecordOne);
        $this->participantProfileTwo_p1 = new RecordOfParticipantProfile($this->participantOne, $programsProfileFormTwo, $formRecordTwo);
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('ProfileForm')->truncate();
        $this->connection->table('ProgramsProfileForm')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('ParticipantProfile')->truncate();
    }
    
    protected function show()
    {
        $this->persistConsultantDependency();
        $this->participantOne->insert($this->connection);
        
        $this->participantProfileOne_p1->programsProfileForm->profileForm->insert($this->connection);
        $this->participantProfileOne_p1->programsProfileForm->insert($this->connection);
        $this->participantProfileOne_p1->insert($this->connection);
        
        $uri = $this->consultantUri . "/participant-profiles/{$this->participantProfileOne_p1->id}";
        $this->get($uri, $this->personnel->token);
    }
    public function test_show_200()
    {
$this->disableExceptionHandling();
        $this->show();
        $this->seeStatusCode(200);
        
        $result = [
            'id' => $this->participantProfileOne_p1->id,
            "submitTime" => $this->participantProfileOne_p1->formRecord->submitTime,
            "stringFieldRecords" => [],
            "integerFieldRecords" => [],
            "textAreaFieldRecords" => [],
            "attachmentFieldRecords" => [],
            "singleSelectFieldRecords" => [],
            "multiSelectFieldRecords" => [],
            'programsProfileForm' => [
                'id' => $this->participantProfileOne_p1->programsProfileForm->id,
                'profileForm' => [
                    'id' => $this->participantProfileOne_p1->programsProfileForm->profileForm->id,
                    'name' => $this->participantProfileOne_p1->programsProfileForm->profileForm->form->name,
                ],
            ],
        ];
        $this->seeJsonContains($result);
    }
    public function test_show_inactiveConsultant_403()
    {
        $this->consultant->active = false;
        $this->show();
        $this->seeStatusCode(403);
    }
    
    protected function showAll()
    {
        $this->persistConsultantDependency();
        $this->participantOne->insert($this->connection);
        
        $this->participantProfileOne_p1->programsProfileForm->profileForm->insert($this->connection);
        $this->participantProfileOne_p1->programsProfileForm->insert($this->connection);
        $this->participantProfileOne_p1->insert($this->connection);
        
        $this->participantProfileTwo_p1->programsProfileForm->profileForm->insert($this->connection);
        $this->participantProfileTwo_p1->programsProfileForm->insert($this->connection);
        $this->participantProfileTwo_p1->insert($this->connection);
        
        $uri = $this->consultantUri . "/participants/{$this->participantOne->id}/profiles";
        $this->get($uri, $this->personnel->token);
    }
    public function test_showAll_200()
    {
$this->disableExceptionHandling();
        $this->showAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 2,
            'list' => [
                [
                    'id' => $this->participantProfileOne_p1->id,
                    'programsProfileForm' => [
                        'id' => $this->participantProfileOne_p1->programsProfileForm->id,
                        'profileForm' => [
                            'id' => $this->participantProfileOne_p1->programsProfileForm->profileForm->id,
                            'name' => $this->participantProfileOne_p1->programsProfileForm->profileForm->form->name,
                        ],
                    ],
                ],
                [
                    'id' => $this->participantProfileTwo_p1->id,
                    'programsProfileForm' => [
                        'id' => $this->participantProfileTwo_p1->programsProfileForm->id,
                        'profileForm' => [
                            'id' => $this->participantProfileTwo_p1->programsProfileForm->profileForm->id,
                            'name' => $this->participantProfileTwo_p1->programsProfileForm->profileForm->form->name,
                        ],
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_excludeOtherParticipantProfile()
    {
        $otherParticipant = new RecordOfParticipant($this->consultant->program, 'other');
        $otherParticipant->insert($this->connection);
        $this->participantProfileOne_p1->participant = $otherParticipant;
        
        $this->showAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 1]);
        $this->seeJsonDoesntContains(['id' => $this->participantProfileOne_p1->id]);
        $this->seeJsonContains(['id' => $this->participantProfileTwo_p1->id]);
    }
    public function test_showAll_inactiveConsultant_403()
    {
        $this->consultant->active = false;
        $this->showAll();
        $this->seeStatusCode(403);
    }
}
