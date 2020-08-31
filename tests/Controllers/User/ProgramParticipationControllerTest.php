<?php

namespace Tests\Controllers\User;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfParticipant,
    Firm\Program\RecordOfUserParticipant,
    Firm\RecordOfProgram,
    RecordOfFirm
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
        
        $participant = new RecordOfParticipant(1);
        $participant->active = false;
        $participant->note = 'quit';
        $this->connection->table('Participant')->insert($participant->toArrayForDbEntry());
        
        $this->inactiveProgramParticipation = new RecordOfUserParticipant($program, $this->user, $participant);
        $this->connection->table('UserParticipant')->insert($this->inactiveProgramParticipation->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function test_quit_200()
    {
        $uri = $this->programParticipationUri . "/{$this->programParticipation->program->firm->id}/{$this->programParticipation->program->id}/quit";
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
        $uri = $this->programParticipationUri . "/{$this->inactiveProgramParticipation->program->firm->id}/{$this->inactiveProgramParticipation->program->id}/quit";
        $this->patch($uri, [], $this->user->token)
                ->seeStatusCode(403);
        
    }
    
    public function test_show_200()
    {
        $response = [
            'program' => [
                'id' => $this->programParticipation->program->id,
                'name' => $this->programParticipation->program->name,
                'removed' => $this->programParticipation->program->removed,
                'firm' => [
                    'id' => $this->programParticipation->program->firm->id,
                    'name' => $this->programParticipation->program->firm->name,
                ],
            ],
            'enrolledTime' => $this->programParticipation->participant->enrolledTime,
            'active' => $this->programParticipation->participant->active,
            'note' => $this->programParticipation->participant->note,
        ];
        
        $uri = $this->programParticipationUri . "/{$this->programParticipation->program->firm->id}/{$this->programParticipation->program->id}";
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
                        'id' => $this->programParticipation->program->id,
                        'name' => $this->programParticipation->program->name,
                        'removed' => $this->programParticipation->program->removed,
                        'firm' => [
                            'id' => $this->programParticipation->program->firm->id,
                            'name' => $this->programParticipation->program->firm->name,
                        ],
                    ],
                    'enrolledTime' => $this->programParticipation->participant->enrolledTime,
                    'active' => $this->programParticipation->participant->active,
                    'note' => $this->programParticipation->participant->note,
                ],
                [
                    'program' => [
                        'id' => $this->inactiveProgramParticipation->program->id,
                        'name' => $this->inactiveProgramParticipation->program->name,
                        'removed' => $this->inactiveProgramParticipation->program->removed,
                        'firm' => [
                            'id' => $this->inactiveProgramParticipation->program->firm->id,
                            'name' => $this->inactiveProgramParticipation->program->firm->name,
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
