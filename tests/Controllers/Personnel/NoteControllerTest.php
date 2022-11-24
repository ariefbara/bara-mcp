<?php

namespace Tests\Controllers\Personnel;

use DateTime;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfConsultantNote;
use Tests\Controllers\RecordPreparation\Firm\Program\Coordinator\RecordOfCoordinatorNote;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfDedicatedMentor;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfParticipantNote;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\RecordOfUser;
use Tests\Controllers\RecordPreparation\Shared\RecordOfNote;
use Tests\Controllers\RecordPreparation\User\RecordOfUserParticipant;

class NoteControllerTest extends PersonnelTestCase
{
    protected $consultantOne_pm1;
    protected $ownConsultantOne_pm1;
    protected $ownConsultantTwo_pm2;
    protected $ownConsultantThree_pm3;
    

    protected $ownCoordinatorOne_p1;
    protected $ownCoordinatorTwo_p2;
    protected $ownCoordinatorThree_p3;
    protected $coordinatorOne_p2;


    protected $clientParticipantOne_pt1;
    protected $teamParticipantTwo_pt2;
    protected $userParticipantThree_pt3;
    
    protected $dedicatedMentor;

    protected $consultantNoteOne_pt1;
    protected $coordinatorNoteOne_pt2;
    protected $participantNoteOne_pt3;
    
    protected $viewTaskListInCoordinatedProgramsUri;
    protected $viewTaskListInConsultedProgramsUri;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('User')->truncate();
        //
        $this->connection->table('Program')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Coordinator')->truncate();
        //
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        //
        $this->connection->table('DedicatedMentor')->truncate();
        //
        $this->connection->table('Note')->truncate();
        $this->connection->table('ConsultantNote')->truncate();
        $this->connection->table('CoordinatorNote')->truncate();
        $this->connection->table('ParticipantNote')->truncate();
        
        $firm = $this->personnel->firm;
        
        $programOne = new RecordOfProgram($firm, 1);
        $programTwo = new RecordOfProgram($firm, 2);
        $programThree = new RecordOfProgram($firm, 3);
        
        $personnelOne = new RecordOfPersonnel($firm, 1);
        $personnelTwo = new RecordOfPersonnel($firm, 2);
        
        $this->consultantOne_pm1 = new RecordOfConsultant($programOne, $personnelOne, 1);
        $this->ownConsultantOne_pm1 = new RecordOfConsultant($programOne, $this->personnel, 'own-1');
        $this->ownConsultantTwo_pm2 = new RecordOfConsultant($programTwo, $this->personnel, 'own-2');
        $this->ownConsultantThree_pm3 = new RecordOfConsultant($programThree, $this->personnel, 'own-3');
        
        $this->coordinatorOne_p2 = new RecordOfCoordinator($programTwo, $personnelTwo, 1);
        $this->ownCoordinatorOne_p1 = new RecordOfCoordinator($programOne, $this->personnel, 'own-1');
        $this->ownCoordinatorTwo_p2 = new RecordOfCoordinator($programTwo, $this->personnel, 'own-2');
        $this->ownCoordinatorThree_p3 = new RecordOfCoordinator($programThree, $this->personnel, 'own-3');
        
        $clientOne = new RecordOfClient($firm, 1);
        
        $teamOne = new RecordOfTeam($firm, $clientOne, 1);
        
        $userOne = new RecordOfUser(1);
        
        $participantOne_pm1 = new RecordOfParticipant($programOne, 1);
        $participantTwo_pm2 = new RecordOfParticipant($programTwo, 2);
        $participantThree_pm3 = new RecordOfParticipant($programThree, 3);
        
        $this->clientParticipantOne_pt1 = new RecordOfClientParticipant($clientOne, $participantOne_pm1);
        
        $this->teamParticipantTwo_pt2 = new RecordOfTeamProgramParticipation($teamOne, $participantTwo_pm2);
        
        $this->userParticipantThree_pt3 = new RecordOfUserParticipant($userOne, $participantThree_pm3);
        
        $this->dedicatedMentor = new RecordOfDedicatedMentor($participantTwo_pm2, $this->ownConsultantTwo_pm2, 99);
        
        $noteOne = new RecordOfNote(1);
        $noteTwo = new RecordOfNote(2);
        $noteThree = new RecordOfNote(3);
        
        $this->consultantNoteOne_pt1 = new RecordOfConsultantNote($noteOne, $this->consultantOne_pm1, $this->clientParticipantOne_pt1->participant);
        
        $this->coordinatorNoteOne_pt2 = new RecordOfCoordinatorNote($noteTwo, $this->coordinatorOne_p2, $this->teamParticipantTwo_pt2->participant);
        
        $this->participantNoteOne_pt3 = new RecordOfParticipantNote($noteThree, $this->userParticipantThree_pt3->participant);
        
        //
        $this->viewTaskListInCoordinatedProgramsUri = $this->personnelUri . "/notes-list-in-coordinated-programs";
        $this->viewTaskListInConsultedProgramsUri = $this->personnelUri . "/notes-list-in-consulted-programs";
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('User')->truncate();
        //
        $this->connection->table('Program')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Coordinator')->truncate();
        //
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        //
        $this->connection->table('DedicatedMentor')->truncate();
        //
        $this->connection->table('Note')->truncate();
        $this->connection->table('ConsultantNote')->truncate();
        $this->connection->table('CoordinatorNote')->truncate();
        $this->connection->table('ParticipantNote')->truncate();
    }
    
    //
    protected function viewTaskListInCoordinatedPrograms()
    {
        $this->clientParticipantOne_pt1->client->insert($this->connection);
        $this->teamParticipantTwo_pt2->team->insert($this->connection);
        $this->userParticipantThree_pt3->user->insert($this->connection);
        
        $this->clientParticipantOne_pt1->insert($this->connection);
        $this->teamParticipantTwo_pt2->insert($this->connection);
        $this->userParticipantThree_pt3->insert($this->connection);
        
        $this->ownCoordinatorOne_p1->program->insert($this->connection);
        $this->ownCoordinatorTwo_p2->program->insert($this->connection);
        $this->ownCoordinatorThree_p3->program->insert($this->connection);
        
        $this->coordinatorOne_p2->personnel->insert($this->connection);
        $this->consultantNoteOne_pt1->consultant->personnel->insert($this->connection);
        
        $this->coordinatorOne_p2->insert($this->connection);
        $this->ownCoordinatorOne_p1->insert($this->connection);
        $this->ownCoordinatorTwo_p2->insert($this->connection);
        $this->ownCoordinatorThree_p3->insert($this->connection);
        
        $this->consultantNoteOne_pt1->consultant->insert($this->connection);
        
        $this->consultantNoteOne_pt1->insert($this->connection);
        $this->coordinatorNoteOne_pt2->insert($this->connection);
        $this->participantNoteOne_pt3->insert($this->connection);
        $this->get($this->viewTaskListInCoordinatedProgramsUri, $this->personnel->token);
    }
    public function test_coordinator_200()
    {
$this->disableExceptionHandling();
        $this->viewTaskListInCoordinatedPrograms();
        $this->seeStatusCode(200);
        
        $response = [
//'printme',
            'total' => '3',
            'list' => [
                [
                    'name' => $this->consultantNoteOne_pt1->note->name,
                    'description' => $this->consultantNoteOne_pt1->note->description,
                    'modifiedTime' => $this->consultantNoteOne_pt1->note->modifiedTime,
                    'createdTime' => $this->consultantNoteOne_pt1->note->createdTime,
                    //
                    'consultantNoteId' => $this->consultantNoteOne_pt1->id,
                    'coordinatorNoteId' => null,
                    'participantNoteId' => null,
                    //
                    'personnelName' => $this->consultantNoteOne_pt1->consultant->personnel->getFullName(),
                    'participantName' => $this->clientParticipantOne_pt1->client->getFullName(),
                    //
                    'coordinatorId' => $this->ownCoordinatorOne_p1->id,
                    'programName' => $this->ownCoordinatorOne_p1->program->name,
                ],
                [
                    'name' => $this->coordinatorNoteOne_pt2->note->name,
                    'description' => $this->coordinatorNoteOne_pt2->note->description,
                    'modifiedTime' => $this->coordinatorNoteOne_pt2->note->modifiedTime,
                    'createdTime' => $this->coordinatorNoteOne_pt2->note->createdTime,
                    //
                    'consultantNoteId' => null,
                    'coordinatorNoteId' => $this->coordinatorNoteOne_pt2->id,
                    'participantNoteId' => null,
                    //
                    'personnelName' => $this->coordinatorNoteOne_pt2->coordinator->personnel->getFullName(),
                    'participantName' => $this->teamParticipantTwo_pt2->team->name,
                    //
                    'coordinatorId' => $this->ownCoordinatorTwo_p2->id,
                    'programName' => $this->ownCoordinatorTwo_p2->program->name,
                ],
                [
                    'name' => $this->participantNoteOne_pt3->note->name,
                    'description' => $this->participantNoteOne_pt3->note->description,
                    'modifiedTime' => $this->participantNoteOne_pt3->note->modifiedTime,
                    'createdTime' => $this->participantNoteOne_pt3->note->createdTime,
                    //
                    'consultantNoteId' => null,
                    'coordinatorNoteId' => null,
                    'participantNoteId' => $this->participantNoteOne_pt3->id,
                    //
                    'personnelName' => null,
                    'participantName' => $this->userParticipantThree_pt3->user->getFullName(),
                    //
                    'coordinatorId' => $this->ownCoordinatorThree_p3->id,
                    'programName' => $this->ownCoordinatorThree_p3->program->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_coordinator_allFilterAndOrder()
    {
        $programId = $this->consultantNoteOne_pt1->participant->program->id;
        $participantId = $this->consultantNoteOne_pt1->participant->id;
        $from = (new DateTime('-1 months'))->format('Y-m-d H:i:s');
        $to = (new DateTime())->format('Y-m-d H:i:s');
        $keyword = 'note';
        $source = 'consultant';
        $order = 'modified-asc';
        
        $this->viewTaskListInCoordinatedProgramsUri .=
                "?programId=$programId"
                . "&participantId=$participantId"
                . "&from=$from"
                . "&to=$to"
                . "&keyword=$keyword"
                . "&source=$source"
                . "&order=$order";
        
        $this->viewTaskListInCoordinatedPrograms();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantNoteId' => $this->consultantNoteOne_pt1->id]);
        $this->seeJsonDoesntContains(['coordinatorNoteId' => $this->coordinatorNoteOne_pt2->id]);
        $this->seeJsonDoesntContains(['participantNoteId' => $this->participantNoteOne_pt3->id]);
    }
    public function test_coordinator_programIdFilter()
    {
        $programId = $this->consultantNoteOne_pt1->participant->program->id;
        
        $this->viewTaskListInCoordinatedProgramsUri .=
                "?programId=$programId";
        
        $this->viewTaskListInCoordinatedPrograms();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantNoteId' => $this->consultantNoteOne_pt1->id]);
        $this->seeJsonDoesntContains(['coordinatorNoteId' => $this->coordinatorNoteOne_pt2->id]);
        $this->seeJsonDoesntContains(['participantNoteId' => $this->participantNoteOne_pt3->id]);
    }
    public function test_coordinator_participantIdFilter()
    {
        $participantId = $this->consultantNoteOne_pt1->participant->id;
        
        $this->viewTaskListInCoordinatedProgramsUri .=
                "?participantId=$participantId";
        
        $this->viewTaskListInCoordinatedPrograms();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantNoteId' => $this->consultantNoteOne_pt1->id]);
        $this->seeJsonDoesntContains(['coordinatorNoteId' => $this->coordinatorNoteOne_pt2->id]);
        $this->seeJsonDoesntContains(['participantNoteId' => $this->participantNoteOne_pt3->id]);
    }
    public function test_coordinator_fromFilter()
    {
        $this->coordinatorNoteOne_pt2->note->modifiedTime = (new DateTime('-1 days'))->format('Y-m-d H:i:s');
        $from = (new DateTime('-2 days'))->format('Y-m-d H:i:s');
        
        $this->viewTaskListInCoordinatedProgramsUri .=
                "?from=$from";
        
        $this->viewTaskListInCoordinatedPrograms();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['consultantNoteId' => $this->consultantNoteOne_pt1->id]);
        $this->seeJsonContains(['coordinatorNoteId' => $this->coordinatorNoteOne_pt2->id]);
        $this->seeJsonDoesntContains(['participantNoteId' => $this->participantNoteOne_pt3->id]);
    }
    public function test_coordinator_toFilter()
    {
        $this->participantNoteOne_pt3->note->modifiedTime = (new DateTime('-1 months'))->format('Y-m-d H:i:s');
        $to = (new DateTime('-2 weeks'))->format('Y-m-d H:i:s');
        
        $this->viewTaskListInCoordinatedProgramsUri .=
                "?to=$to";
        
        $this->viewTaskListInCoordinatedPrograms();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['consultantNoteId' => $this->consultantNoteOne_pt1->id]);
        $this->seeJsonDoesntContains(['coordinatorNoteId' => $this->coordinatorNoteOne_pt2->id]);
        $this->seeJsonContains(['participantNoteId' => $this->participantNoteOne_pt3->id]);
    }
    public function test_coordinator_keywordFilter_matchingName()
    {
        $this->coordinatorNoteOne_pt2->note->name = 'myspesificnames';
        $keyword = 'spesificname';
        
        $this->viewTaskListInCoordinatedProgramsUri .=
                "?keyword=$keyword";
        
        $this->viewTaskListInCoordinatedPrograms();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['consultantNoteId' => $this->consultantNoteOne_pt1->id]);
        $this->seeJsonContains(['coordinatorNoteId' => $this->coordinatorNoteOne_pt2->id]);
        $this->seeJsonDoesntContains(['participantNoteId' => $this->participantNoteOne_pt3->id]);
    }
    public function test_coordinator_keywordFilter_matchingDescription()
    {
        $this->coordinatorNoteOne_pt2->note->description = 'sadfasdfspesificdescriptionadsfadsf';
        $keyword = 'spesificdescription';
        
        $this->viewTaskListInCoordinatedProgramsUri .=
                "?keyword=$keyword";
        
        $this->viewTaskListInCoordinatedPrograms();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['consultantNoteId' => $this->consultantNoteOne_pt1->id]);
        $this->seeJsonContains(['coordinatorNoteId' => $this->coordinatorNoteOne_pt2->id]);
        $this->seeJsonDoesntContains(['participantNoteId' => $this->participantNoteOne_pt3->id]);
    }
    public function test_coordinator_sourceFilter_consultant()
    {
        $source = 'consultant';
        
        $this->viewTaskListInCoordinatedProgramsUri .=
                "?source=$source";
        
        $this->viewTaskListInCoordinatedPrograms();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantNoteId' => $this->consultantNoteOne_pt1->id]);
        $this->seeJsonDoesntContains(['coordinatorNoteId' => $this->coordinatorNoteOne_pt2->id]);
        $this->seeJsonDoesntContains(['participantNoteId' => $this->participantNoteOne_pt3->id]);
    }
    public function test_coordinator_sourceFilter_coordinator()
    {
        $source = 'coordinator';
        
        $this->viewTaskListInCoordinatedProgramsUri .=
                "?source=$source";
        
        $this->viewTaskListInCoordinatedPrograms();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['consultantNoteId' => $this->consultantNoteOne_pt1->id]);
        $this->seeJsonContains(['coordinatorNoteId' => $this->coordinatorNoteOne_pt2->id]);
        $this->seeJsonDoesntContains(['participantNoteId' => $this->participantNoteOne_pt3->id]);
    }
    public function test_coordinator_sourceFilter_participant()
    {
        $source = 'participant';
        
        $this->viewTaskListInCoordinatedProgramsUri .=
                "?source=$source";
        
        $this->viewTaskListInCoordinatedPrograms();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['consultantNoteId' => $this->consultantNoteOne_pt1->id]);
        $this->seeJsonDoesntContains(['coordinatorNoteId' => $this->coordinatorNoteOne_pt2->id]);
        $this->seeJsonContains(['participantNoteId' => $this->participantNoteOne_pt3->id]);
    }
    public function test_coordinator_setOrder_modifiedTimeAsc()
    {
        $this->consultantNoteOne_pt1->note->modifiedTime = (new DateTime('-2 months'))->format('Y-m-d H:i:s');
        $this->coordinatorNoteOne_pt2->note->modifiedTime = (new DateTime('-1 months'))->format('Y-m-d H:i:s');
        $this->participantNoteOne_pt3->note->modifiedTime = (new DateTime('-3 months'))->format('Y-m-d H:i:s');
        
        $order = 'modified-asc';
        $this->viewTaskListInCoordinatedProgramsUri .=
                "?order=$order"
                . "&pageSize=1";
        
        $this->viewTaskListInCoordinatedPrograms();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '3']);
        $this->seeJsonDoesntContains(['consultantNoteId' => $this->consultantNoteOne_pt1->id]);
        $this->seeJsonDoesntContains(['coordinatorNoteId' => $this->coordinatorNoteOne_pt2->id]);
        $this->seeJsonContains(['participantNoteId' => $this->participantNoteOne_pt3->id]);
    }
    public function test_coordinator_excludeNoteFromNonCoordinatedProgram()
    {
        $this->ownCoordinatorOne_p1->active = false;
        
        $this->viewTaskListInCoordinatedPrograms();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonDoesntContains(['consultantNoteId' => $this->consultantNoteOne_pt1->id]);
        $this->seeJsonContains(['coordinatorNoteId' => $this->coordinatorNoteOne_pt2->id]);
        $this->seeJsonContains(['participantNoteId' => $this->participantNoteOne_pt3->id]);
    }
    
    //
    protected function viewTaskListInConsultedPrograms()
    {
        $this->ownConsultantOne_pm1->program->insert($this->connection);
        $this->ownConsultantTwo_pm2->program->insert($this->connection);
        $this->ownConsultantThree_pm3->program->insert($this->connection);
        
        $this->clientParticipantOne_pt1->client->insert($this->connection);
        $this->teamParticipantTwo_pt2->team->insert($this->connection);
        $this->userParticipantThree_pt3->user->insert($this->connection);
        
        $this->clientParticipantOne_pt1->insert($this->connection);
        $this->teamParticipantTwo_pt2->insert($this->connection);
        $this->userParticipantThree_pt3->insert($this->connection);
        
        $this->coordinatorOne_p2->personnel->insert($this->connection);
        $this->consultantOne_pm1->personnel->insert($this->connection);
        
        $this->coordinatorOne_p2->insert($this->connection);
        
        $this->consultantOne_pm1->insert($this->connection);
        $this->ownConsultantOne_pm1->insert($this->connection);
        $this->ownConsultantTwo_pm2->insert($this->connection);
        $this->ownConsultantThree_pm3->insert($this->connection);
        
        $this->consultantNoteOne_pt1->insert($this->connection);
        $this->coordinatorNoteOne_pt2->insert($this->connection);
        $this->participantNoteOne_pt3->insert($this->connection);
        
        $this->get($this->viewTaskListInConsultedProgramsUri, $this->personnel->token);
    }
    public function test_consultant_200()
    {
$this->disableExceptionHandling();
        $this->viewTaskListInConsultedPrograms();
        $this->seeStatusCode(200);
        
        $response = [
//'printme',
            'total' => '3',
            'list' => [
                [
                    'name' => $this->consultantNoteOne_pt1->note->name,
                    'description' => $this->consultantNoteOne_pt1->note->description,
                    'modifiedTime' => $this->consultantNoteOne_pt1->note->modifiedTime,
                    'createdTime' => $this->consultantNoteOne_pt1->note->createdTime,
                    //
                    'consultantNoteId' => $this->consultantNoteOne_pt1->id,
                    'coordinatorNoteId' => null,
                    'participantNoteId' => null,
                    //
                    'personnelName' => $this->consultantNoteOne_pt1->consultant->personnel->getFullName(),
                    'participantName' => $this->clientParticipantOne_pt1->client->getFullName(),
                    'dedicatedMenteeId' => null,
                    //
                    'consultantId' => $this->ownConsultantOne_pm1->id,
                    'programName' => $this->ownConsultantOne_pm1->program->name,
                ],
                [
                    'name' => $this->coordinatorNoteOne_pt2->note->name,
                    'description' => $this->coordinatorNoteOne_pt2->note->description,
                    'modifiedTime' => $this->coordinatorNoteOne_pt2->note->modifiedTime,
                    'createdTime' => $this->coordinatorNoteOne_pt2->note->createdTime,
                    //
                    'consultantNoteId' => null,
                    'coordinatorNoteId' => $this->coordinatorNoteOne_pt2->id,
                    'participantNoteId' => null,
                    //
                    'personnelName' => $this->coordinatorNoteOne_pt2->coordinator->personnel->getFullName(),
                    'participantName' => $this->teamParticipantTwo_pt2->team->name,
                    'dedicatedMenteeId' => null,
                    //
                    'consultantId' => $this->ownConsultantTwo_pm2->id,
                    'programName' => $this->ownConsultantTwo_pm2->program->name,
                ],
                [
                    'name' => $this->participantNoteOne_pt3->note->name,
                    'description' => $this->participantNoteOne_pt3->note->description,
                    'modifiedTime' => $this->participantNoteOne_pt3->note->modifiedTime,
                    'createdTime' => $this->participantNoteOne_pt3->note->createdTime,
                    //
                    'consultantNoteId' => null,
                    'coordinatorNoteId' => null,
                    'participantNoteId' => $this->participantNoteOne_pt3->id,
                    //
                    'personnelName' => null,
                    'participantName' => $this->userParticipantThree_pt3->user->getFullName(),
                    'dedicatedMenteeId' => null,
                    //
                    'consultantId' => $this->ownConsultantThree_pm3->id,
                    'programName' => $this->ownConsultantThree_pm3->program->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_consultant_excludeNoteFromNonConsultedProgram()
    {
        $this->ownConsultantTwo_pm2->active = false;
        
        $this->viewTaskListInConsultedPrograms();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonContains(['consultantNoteId' => $this->consultantNoteOne_pt1->id]);
        $this->seeJsonDoesntContains(['coordinatorNoteId' => $this->coordinatorNoteOne_pt2->id]);
        $this->seeJsonContains(['participantNoteId' => $this->participantNoteOne_pt3->id]);
    }
    public function test_consultant_allFilterAndOrder()
    {
        $this->consultantNoteOne_pt1->consultant = $this->ownConsultantOne_pm1;
        
        $programId = $this->consultantNoteOne_pt1->participant->program->id;
        $participantId = $this->consultantNoteOne_pt1->participant->id;
        $from = (new DateTime('-1 months'))->format('Y-m-d H:i:s');
        $to = (new DateTime())->format('Y-m-d H:i:s');
        $keyword = 'note';
        $source = 'consultant';
        $order = 'modified-asc';
        
        $this->viewTaskListInConsultedProgramsUri .=
                "?programId=$programId"
                . "&participantId=$participantId"
                . "&noteOwnership=BOTH"
                . "&from=$from"
                . "&to=$to"
                . "&keyword=$keyword"
                . "&source=$source"
                . "&order=$order";
        
        $this->viewTaskListInConsultedPrograms();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantNoteId' => $this->consultantNoteOne_pt1->id]);
        $this->seeJsonDoesntContains(['coordinatorNoteId' => $this->coordinatorNoteOne_pt2->id]);
        $this->seeJsonDoesntContains(['participantNoteId' => $this->participantNoteOne_pt3->id]);
    }
    public function test_consultant_noteOwnership_OWN_onlyReturnOwnedConsultantNotes()
    {
        $this->consultantNoteOne_pt1->consultant = $this->ownConsultantOne_pm1;
        
        $noteOwnership = 'OWN';
        
        $this->viewTaskListInConsultedProgramsUri .=
                "?noteOwnership=$noteOwnership";
        
        $this->viewTaskListInConsultedPrograms();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantNoteId' => $this->consultantNoteOne_pt1->id]);
        $this->seeJsonDoesntContains(['coordinatorNoteId' => $this->coordinatorNoteOne_pt2->id]);
        $this->seeJsonDoesntContains(['participantNoteId' => $this->participantNoteOne_pt3->id]);
    }
    public function test_consultant_noteOwnership_MENTEE_onlyReturnNotesForDedicatedMentee()
    {
        $this->dedicatedMentor->insert($this->connection);
        
        $noteOwnership = 'MENTEE';
        
        $this->viewTaskListInConsultedProgramsUri .=
                "?noteOwnership=$noteOwnership";
        
        $this->viewTaskListInConsultedPrograms();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['consultantNoteId' => $this->consultantNoteOne_pt1->id]);
        $this->seeJsonContains(['coordinatorNoteId' => $this->coordinatorNoteOne_pt2->id]);
        $this->seeJsonDoesntContains(['participantNoteId' => $this->participantNoteOne_pt3->id]);
    }
    public function test_consultant_noteOwnership_BOTH_onlyReturnOwnedNotesOrNotesForDedicatedMentee()
    {
        $this->consultantNoteOne_pt1->consultant = $this->ownConsultantOne_pm1;
        $this->dedicatedMentor->insert($this->connection);
        
        $noteOwnership = 'BOTH';
        
        $this->viewTaskListInConsultedProgramsUri .=
                "?noteOwnership=$noteOwnership";
        
        $this->viewTaskListInConsultedPrograms();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonContains(['consultantNoteId' => $this->consultantNoteOne_pt1->id]);
        $this->seeJsonContains(['coordinatorNoteId' => $this->coordinatorNoteOne_pt2->id]);
        $this->seeJsonDoesntContains(['participantNoteId' => $this->participantNoteOne_pt3->id]);
    }
    public function test_consultant_participantIdFilter_200()
    {
        $this->viewTaskListInConsultedProgramsUri .= "?participantId={$this->clientParticipantOne_pt1->id}";
        
        $this->viewTaskListInConsultedPrograms();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantNoteId' => $this->consultantNoteOne_pt1->id]);
        $this->seeJsonDoesntContains(['coordinatorNoteId' => $this->coordinatorNoteOne_pt2->id]);
        $this->seeJsonDoesntContains(['participantNoteId' => $this->participantNoteOne_pt3->id]);
    }
}
