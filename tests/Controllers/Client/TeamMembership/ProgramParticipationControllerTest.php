<?php

namespace Tests\Controllers\Client\TeamMembership;

use Tests\Controllers\RecordPreparation\Firm\ {
    Program\RecordOfParticipant,
    RecordOfProgram,
    Team\RecordOfTeamProgramParticipation
};

class ProgramParticipationControllerTest extends ProgramParticipationTestCase
{
    protected $inactiveProgramParticipation;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $team = $this->teamMembership->team;
        $firm = $team->firm;
        
        $program = new RecordOfProgram($firm, 1);
        $this->connection->table('Program')->insert($program->toArrayForDbEntry());
        
        $participant = new RecordOfParticipant($program, 1);
        $participant->active = false;
        $participant->note = 'quit';
        $this->connection->table('Participant')->insert($participant->toArrayForDbEntry());
        
        
        $this->inactiveProgramParticipation = new RecordOfTeamProgramParticipation($team, $participant);
        $this->connection->table('TeamParticipant')->insert($this->inactiveProgramParticipation->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function test_quit_200()
    {
        $uri = $this->programParticipationUri . "/{$this->programParticipation->id}/quit";
        $this->patch($uri, [], $this->teamMembership->client->token)
            ->seeStatusCode(200);
        
        $participantEntry = [
            "id" => $this->programParticipation->participant->id,
            "active" => false,
            "note" => 'quit',
        ];
        $this->seeInDatabase('Participant', $participantEntry);
    }
    public function test_quit_alreadyInactive_403()
    {
        $uri = $this->programParticipationUri . "/{$this->inactiveProgramParticipation->id}/quit";
        $this->patch($uri, [], $this->teamMembership->client->token)
            ->seeStatusCode(403);
    }
    public function test_quit_inactiveMember_403()
    {
        $this->setTeamMembershipInactive();
        
        $uri = $this->programParticipationUri . "/{$this->programParticipation->id}/quit";
        $this->patch($uri, [], $this->teamMembership->client->token)
            ->seeStatusCode(403);
    }
    
    public function test_show()
    {
        $response = [
            "id" => $this->programParticipation->id,
            "program" => [
                "id" => $this->programParticipation->participant->program->id,
                "name" => $this->programParticipation->participant->program->name,
                "removed" => $this->programParticipation->participant->program->removed,
            ],
            "enrolledTime" => $this->programParticipation->participant->enrolledTime,
            "active" => $this->programParticipation->participant->active,
            "note" => $this->programParticipation->participant->note,
        ];
        $uri = $this->programParticipationUri . "/{$this->programParticipation->id}";
        $this->get($uri, $this->teamMembership->client->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    public function test_show_inactiveMember_403()
    {
        $this->setTeamMembershipInactive();
        
        $uri = $this->programParticipationUri . "/{$this->programParticipation->id}";
        $this->get($uri, $this->teamMembership->client->token)
            ->seeStatusCode(403);
    }
    
    public function test_showAll()
    {
        $response = [
            "total" => 2, 
            "list" => [
                [
                    "id" => $this->programParticipation->id,
                    "program" => [
                        "id" => $this->programParticipation->participant->program->id,
                        "name" => $this->programParticipation->participant->program->name,
                        "removed" => $this->programParticipation->participant->program->removed,
                    ],
                    "enrolledTime" => $this->programParticipation->participant->enrolledTime,
                    "active" => $this->programParticipation->participant->active,
                    "note" => $this->programParticipation->participant->note,
                ],
                [
                    "id" => $this->inactiveProgramParticipation->id,
                    "program" => [
                        "id" => $this->inactiveProgramParticipation->participant->program->id,
                        "name" => $this->inactiveProgramParticipation->participant->program->name,
                        "removed" => $this->inactiveProgramParticipation->participant->program->removed,
                    ],
                    "enrolledTime" => $this->inactiveProgramParticipation->participant->enrolledTime,
                    "active" => $this->inactiveProgramParticipation->participant->active,
                    "note" => $this->inactiveProgramParticipation->participant->note,
                ],
            ],
        ];
        $this->get($this->programParticipationUri, $this->teamMembership->client->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    public function test_showAll_inactiveMember_403()
    {
        $this->setTeamMembershipInactive();
        
        $this->get($this->programParticipationUri, $this->teamMembership->client->token)
            ->seeStatusCode(403);
    }
}
