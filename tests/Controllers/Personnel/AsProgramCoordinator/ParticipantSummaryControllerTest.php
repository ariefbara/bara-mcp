<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator;

use Tests\Controllers\RecordPreparation\ {
    Firm\Client\RecordOfClientParticipant,
    Firm\Program\Participant\Worksheet\RecordOfCompletedMission,
    Firm\Program\RecordOfMission,
    Firm\Program\RecordOfParticipant,
    Firm\RecordOfClient,
    Firm\RecordOfTeam,
    Firm\RecordOfWorksheetForm,
    Firm\Team\RecordOfTeamProgramParticipation,
    RecordOfUser,
    Shared\RecordOfForm,
    User\RecordOfUserParticipant
};

class ParticipantSummaryControllerTest extends AsProgramCoordinatorTestCase
{
    protected $participantSummaryUri;
    protected $participant_client;
    protected $participantOne_team;
    protected $participantTwo_clientinactive;
    protected $participantThree_user;
    protected $clientParticipant;
    protected $teamParticipant;
    protected $clientParticipant_inactive;
    protected $userParticipant;
    protected $mission;
    protected $missionOne;
    protected $missionTwo;
    protected $completedMission_00;
    protected $completedMission_01;
    protected $completedMission_02;
    protected $completedMission_20;
    protected $completedMission_30;
    protected $completedMission_32;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participantSummaryUri = $this->asProgramCoordinatorUri . "/participant-summary";
        
        $this->connection->table("Client")->truncate();
        $this->connection->table("User")->truncate();
        $this->connection->table("Team")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("WorksheetForm")->truncate();
        $this->connection->table("Mission")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("TeamParticipant")->truncate();
        $this->connection->table("UserParticipant")->truncate();
        $this->connection->table("CompletedMission")->truncate();
        
        $program = $this->coordinator->program;
        $firm = $program->firm;
        
        $this->participant_client = new RecordOfParticipant($program, 0);
        $this->participantOne_team = new RecordOfParticipant($program, 1);
        $this->participantTwo_clientinactive = new RecordOfParticipant($program, 2);
        $this->participantTwo_clientinactive->active = false;
        $this->participantThree_user = new RecordOfParticipant($program, 3);
        $this->connection->table("Participant")->insert($this->participant_client->toArrayForDbEntry());
        $this->connection->table("Participant")->insert($this->participantOne_team->toArrayForDbEntry());
        $this->connection->table("Participant")->insert($this->participantTwo_clientinactive->toArrayForDbEntry());
        $this->connection->table("Participant")->insert($this->participantThree_user->toArrayForDbEntry());
        
        $client = new RecordOfClient($firm, 0);
        $clientOne = new RecordOfClient($firm, 1);
        $this->connection->table("Client")->insert($client->toArrayForDbEntry());
        $this->connection->table("Client")->insert($clientOne->toArrayForDbEntry());
        
        $team = new RecordOfTeam($firm, $clientOne, 0);
        $this->connection->table("Team")->insert($team->toArrayForDbEntry());
        
        $user = new RecordOfUser(0);
        $this->connection->table("User")->insert($user->toArrayForDbEntry());
        
        $this->clientParticipant = new RecordOfClientParticipant($client, $this->participant_client);
        $this->clientParticipant_inactive = new RecordOfClientParticipant($clientOne, $this->participantTwo_clientinactive);
        $this->connection->table("ClientParticipant")->insert($this->clientParticipant->toArrayForDbEntry());
        $this->connection->table("ClientParticipant")->insert($this->clientParticipant_inactive->toArrayForDbEntry());
        
        $this->teamParticipant = new RecordOfTeamProgramParticipation($team, $this->participantOne_team);
        $this->connection->table("TeamParticipant")->insert($this->teamParticipant->toArrayForDbEntry());
        
        $this->userParticipant = new RecordOfUserParticipant($user, $this->participantThree_user);
        $this->connection->table("UserParticipant")->insert($this->userParticipant->toArrayForDbEntry());
        
        $form = new RecordOfForm(0);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        
        $worksheetForm = new RecordOfWorksheetForm($firm, $form);
        $this->connection->table("WorksheetForm")->insert($worksheetForm->toArrayForDbEntry());
        
        $this->mission = new RecordOfMission($program, $worksheetForm, 0, null);
        $this->missionOne = new RecordOfMission($program, $worksheetForm, 1, null);
        $this->missionTwo = new RecordOfMission($program, $worksheetForm, 2, null);
        $this->connection->table("Mission")->insert($this->mission->toArrayForDbEntry());
        $this->connection->table("Mission")->insert($this->missionOne->toArrayForDbEntry());
        $this->connection->table("Mission")->insert($this->missionTwo->toArrayForDbEntry());
        
        $this->completedMission_00 = new RecordOfCompletedMission($this->participant_client, $this->mission, "00");
        $this->completedMission_00->completedTime = (new \DateTime("-48 hours"))->format("Y-m-d H:i:s");
        $this->completedMission_01 = new RecordOfCompletedMission($this->participant_client, $this->missionOne, "01");
        $this->completedMission_01->completedTime = (new \DateTime("-12 hours"))->format("Y-m-d H:i:s");
        $this->completedMission_02 = new RecordOfCompletedMission($this->participant_client, $this->missionTwo, "02");
        $this->completedMission_02->completedTime = (new \DateTime("-24 hours"))->format("Y-m-d H:i:s");
        $this->completedMission_20 = new RecordOfCompletedMission($this->participantTwo_clientinactive, $this->mission, "20");
        $this->completedMission_20->completedTime = (new \DateTime("-24 hours"))->format("Y-m-d H:i:s");
        $this->completedMission_30 = new RecordOfCompletedMission($this->participantThree_user, $this->mission, "30");
        $this->completedMission_30->completedTime = (new \DateTime("-24 hours"))->format("Y-m-d H:i:s");
        $this->completedMission_32 = new RecordOfCompletedMission($this->participantThree_user, $this->missionTwo, "32");
        $this->completedMission_32->completedTime = (new \DateTime("-48 hours"))->format("Y-m-d H:i:s");
        $this->connection->table("CompletedMission")->insert($this->completedMission_00->toArrayForDbEntry());
        $this->connection->table("CompletedMission")->insert($this->completedMission_01->toArrayForDbEntry());
        $this->connection->table("CompletedMission")->insert($this->completedMission_02->toArrayForDbEntry());
        $this->connection->table("CompletedMission")->insert($this->completedMission_20->toArrayForDbEntry());
        $this->connection->table("CompletedMission")->insert($this->completedMission_30->toArrayForDbEntry());
        $this->connection->table("CompletedMission")->insert($this->completedMission_32->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Client")->truncate();
        $this->connection->table("User")->truncate();
        $this->connection->table("Team")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("WorksheetForm")->truncate();
        $this->connection->table("Mission")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("TeamParticipant")->truncate();
        $this->connection->table("UserParticipant")->truncate();
        $this->connection->table("CompletedMission")->truncate();
    }
    
    public function test_showAll_200()
    {
        $totalResponse = [
            "total" => 3,
        ];
        $participantResponse = [
            "id" => $this->participant_client->id,
            "name" => $this->clientParticipant->client->getFullName(),
            "totalCompletedMission" => "3",
            "totalMission" => "3",
            "lastCompletedTime" => $this->completedMission_01->completedTime,
            "lastMissionId" => $this->missionOne->id,
            "lastMissionName" => $this->missionOne->name,
        ];
        $participantOneResponse = [
            "id" => $this->participantOne_team->id,
            "name" => $this->teamParticipant->team->name,
            "totalCompletedMission" => null,
            "totalMission" => "3",
            "lastCompletedTime" => null,
            "lastMissionId" => null,
            "lastMissionName" => null,
        ];
        $participantThreeResponse = [
            "id" => $this->participantThree_user->id,
            "name" => $this->userParticipant->user->getFullName(),
            "totalCompletedMission" => "2",
            "totalMission" => "3",
            "lastCompletedTime" => $this->completedMission_30->completedTime,
            "lastMissionId" => $this->mission->id,
            "lastMissionName" => $this->mission->name,
        ];
        
        $this->get($this->participantSummaryUri, $this->coordinator->personnel->token)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($participantResponse)
                ->seeJsonContains($participantOneResponse)
                ->seeJsonContains($participantThreeResponse)
                ->seeStatusCode(200);
    }
    public function test_showAll_paginationSet()
    {
        $response = [
            "total" => 3,
            "list" => [
                [
                    "id" => $this->participantOne_team->id,
                    "name" => $this->teamParticipant->team->name,
                    "totalCompletedMission" => null,
                    "totalMission" => "3",
                    "lastCompletedTime" => null,
                    "lastMissionId" => null,
                    "lastMissionName" => null,
                ],
            ],
        ];
        $uri = $this->participantSummaryUri . "?page=2&pageSize=2";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_inactiveCoordinator_403()
    {
        $this->get($this->participantSummaryUri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(403);
    }
}
