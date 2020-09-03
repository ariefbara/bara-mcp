<?php

namespace Tests\Controllers\User;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfParticipant,
    Firm\RecordOfProgram,
    RecordOfFirm,
    User\RecordOfUserParticipant
};

class ProgramParticipationControllerTest extends ProgramParticipationTestCase
{
    protected $inactiveProgramParticipation;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $firm = new RecordOfFirm(1, 'firm-1-identifier');
        $this->connection->table('Firm')->insert($firm->toArrayForDbEntry());
        
        $program = new RecordOfProgram($firm, 1);
        $this->connection->table('Program')->insert($program->toArrayForDbEntry());
        
        $participant = new RecordOfParticipant($program, 1);
        $participant->active = false;
        $participant->note = 'quit';
        $this->connection->table('Participant')->insert($participant->toArrayForDbEntry());
        
        $this->inactiveProgramParticipation = new RecordOfUserParticipant($this->user, $participant);
        $this->connection->table('UserParticipant')->insert($this->inactiveProgramParticipation->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function test_quit_200()
    {
        $uri = $this->programParticipationUri . "/{$this->programParticipation->id}/quit";
        $this->patch($uri, [], $this->user->token)
                ->seeStatusCode(200);
        
        $participantEntry = [
            'id' => $this->programParticipation->participant->id,
            'active' => false,
            'note' => 'quit',
        ];
        $this->seeInDatabase('Participant', $participantEntry);
    }
    public function test_quit_alreadyInactive_403()
    {
        $uri = $this->programParticipationUri . "/{$this->inactiveProgramParticipation->id}/quit";
        $this->patch($uri, [], $this->user->token)
                ->seeStatusCode(403);
        
    }
    
    public function test_show_200()
    {
        $response = [
            'program' => [
                'id' => $this->programParticipation->participant->program->id,
                'name' => $this->programParticipation->participant->program->name,
                'removed' => $this->programParticipation->participant->program->removed,
                'firm' => [
                    'id' => $this->programParticipation->participant->program->firm->id,
                    'name' => $this->programParticipation->participant->program->firm->name,
                ],
            ],
            'enrolledTime' => $this->programParticipation->participant->enrolledTime,
            'active' => $this->programParticipation->participant->active,
            'note' => $this->programParticipation->participant->note,
        ];
        
        $uri = $this->programParticipationUri . "/{$this->programParticipation->id}";
        $this->get($uri, $this->user->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_showAll_200()
    {
        $response = [
            'total' => 2,
            'list' => [
                [
                    'program' => [
                        'id' => $this->programParticipation->participant->program->id,
                        'name' => $this->programParticipation->participant->program->name,
                        'removed' => $this->programParticipation->participant->program->removed,
                        'firm' => [
                            'id' => $this->programParticipation->participant->program->firm->id,
                            'name' => $this->programParticipation->participant->program->firm->name,
                        ],
                    ],
                    'enrolledTime' => $this->programParticipation->participant->enrolledTime,
                    'active' => $this->programParticipation->participant->active,
                    'note' => $this->programParticipation->participant->note,
                ],
                [
                    'program' => [
                        'id' => $this->inactiveProgramParticipation->participant->program->id,
                        'name' => $this->inactiveProgramParticipation->participant->program->name,
                        'removed' => $this->inactiveProgramParticipation->participant->program->removed,
                        'firm' => [
                            'id' => $this->inactiveProgramParticipation->participant->program->firm->id,
                            'name' => $this->inactiveProgramParticipation->participant->program->firm->name,
                        ],
                    ],
                    'enrolledTime' => $this->inactiveProgramParticipation->participant->enrolledTime,
                    'active' => $this->inactiveProgramParticipation->participant->active,
                    'note' => $this->inactiveProgramParticipation->participant->note,
                ],
            ],
        ];
        
        $this->get($this->programParticipationUri, $this->user->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
}
