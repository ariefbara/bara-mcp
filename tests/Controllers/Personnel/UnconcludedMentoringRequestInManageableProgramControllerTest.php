<?php

namespace Tests\Controllers\Personnel;

use DateTime;
use SharedContext\Domain\ValueObject\MentoringRequestStatus;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMentoringRequest;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\RecordOfUser;
use Tests\Controllers\RecordPreparation\User\RecordOfUserParticipant;

class UnconcludedMentoringRequestInManageableProgramControllerTest extends AggregatedCoordinatorInPersonnelContextTestCase
{
    protected $clientParticipantOne;
    protected $teamParticipantTwo;
    protected $userParticipantThree;
    protected $mentoringRequestOne_p1m1;
    protected $mentoringRequestTwo_p2m2;
    protected $mentoringRequestThree_p3m1;
    protected $viewAllUri;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->viewAllUri = $this->personnelUri . "/unconcluded-mentoring-request-in-manageable-program";
        
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        
        $firm = $this->personnel->firm;
        $programOne = $this->coordinatorOne->program;
        $programTwo = $this->coordinatorTwo->program;
        
        $personnelOne = new RecordOfPersonnel($firm, 1);
        $personnelTwo = new RecordOfPersonnel($firm, 2);
        
        $participantOne = new RecordOfParticipant($programOne, 1);
        $participantTwo = new RecordOfParticipant($programTwo, 2);
        $participantThree = new RecordOfParticipant($programOne, 3);
        
        $mentorOne = new RecordOfConsultant($programOne, $personnelOne, 1);
        $mentorTwo = new RecordOfConsultant($programTwo, $personnelTwo, 2);
        
        $consultationSetupOne = new RecordOfConsultationSetup($programOne, null, null, 1);
        $consultationSetupTwo = new RecordOfConsultationSetup($programTwo, null, null, 2);
        
        $this->mentoringRequestOne_p1m1 = new RecordOfMentoringRequest($participantOne, $mentorOne, $consultationSetupOne, 1);
        $this->mentoringRequestTwo_p2m2 = new RecordOfMentoringRequest($participantTwo, $mentorTwo, $consultationSetupTwo, 2);
        $this->mentoringRequestThree_p3m1 = new RecordOfMentoringRequest($participantThree, $mentorOne, $consultationSetupOne, 3);

        $clientOne = new RecordOfClient($firm, 1);
        
        $teamOne = new RecordOfTeam($firm, $clientOne, 1);
        
        $userOne = new RecordOfUser(1);
        
        $this->clientParticipantOne = new RecordOfClientParticipant($clientOne, $participantOne);
        
        $this->teamParticipantTwo = new RecordOfTeamProgramParticipation($teamOne, $participantTwo);
        
        $this->userParticipantThree = new RecordOfUserParticipant($userOne, $participantThree);
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
    }
    
    protected function viewAll()
    {
        $this->persistAggregatedCoordinatorDependency();
        
        $this->clientParticipantOne->client->insert($this->connection);
        $this->teamParticipantTwo->team->insert($this->connection);
        $this->userParticipantThree->user->insert($this->connection);
        
        $this->clientParticipantOne->insert($this->connection);
        $this->teamParticipantTwo->insert($this->connection);
        $this->userParticipantThree->insert($this->connection);
        
        $this->mentoringRequestOne_p1m1->consultationSetup->insert($this->connection);
        $this->mentoringRequestTwo_p2m2->consultationSetup->insert($this->connection);
        
        $this->mentoringRequestOne_p1m1->mentor->personnel->insert($this->connection);
        $this->mentoringRequestTwo_p2m2->mentor->personnel->insert($this->connection);
        
        $this->mentoringRequestOne_p1m1->mentor->insert($this->connection);
        $this->mentoringRequestTwo_p2m2->mentor->insert($this->connection);
        
        $this->mentoringRequestOne_p1m1->insert($this->connection);
        $this->mentoringRequestTwo_p2m2->insert($this->connection);
        $this->mentoringRequestThree_p3m1->insert($this->connection);
        
        $this->get($this->viewAllUri, $this->personnel->token);
    }
    public function test_viewAll_200_bug20230607_statusSwappedBetweenOfferedByMentorAndRequestedByParticipant()
    {
$this->disableExceptionHandling();
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '3',
            'list' => [
                [
                    'id' => $this->mentoringRequestOne_p1m1->id,
                    'requestStatus' => 'requested by participant',
                    'startTime' => $this->mentoringRequestOne_p1m1->startTime,
                    'endTime' => $this->mentoringRequestOne_p1m1->endTime,
                    'mediaType' => $this->mentoringRequestOne_p1m1->mediaType,
                    'location' => $this->mentoringRequestOne_p1m1->location,
                    'participantId' => $this->mentoringRequestOne_p1m1->participant->id,
                    'participantName' => $this->clientParticipantOne->client->getFullName(),
                    'consultantId' => $this->mentoringRequestOne_p1m1->mentor->id,
                    'consultantName' => $this->mentoringRequestOne_p1m1->mentor->personnel->getFullName(),
                    'programId' => $this->mentoringRequestOne_p1m1->participant->program->id,
                    'programName' => $this->mentoringRequestOne_p1m1->participant->program->name,
                ],
                [
                    'id' => $this->mentoringRequestTwo_p2m2->id,
                    'requestStatus' => 'requested by participant',
                    'startTime' => $this->mentoringRequestTwo_p2m2->startTime,
                    'endTime' => $this->mentoringRequestTwo_p2m2->endTime,
                    'mediaType' => $this->mentoringRequestTwo_p2m2->mediaType,
                    'location' => $this->mentoringRequestTwo_p2m2->location,
                    'participantId' => $this->mentoringRequestTwo_p2m2->participant->id,
                    'participantName' => $this->teamParticipantTwo->team->name,
                    'consultantId' => $this->mentoringRequestTwo_p2m2->mentor->id,
                    'consultantName' => $this->mentoringRequestTwo_p2m2->mentor->personnel->getFullName(),
                    'programId' => $this->mentoringRequestTwo_p2m2->participant->program->id,
                    'programName' => $this->mentoringRequestTwo_p2m2->participant->program->name,
                ],
                [
                    'id' => $this->mentoringRequestThree_p3m1->id,
                    'requestStatus' => 'requested by participant',
                    'startTime' => $this->mentoringRequestThree_p3m1->startTime,
                    'endTime' => $this->mentoringRequestThree_p3m1->endTime,
                    'mediaType' => $this->mentoringRequestThree_p3m1->mediaType,
                    'location' => $this->mentoringRequestThree_p3m1->location,
                    'participantId' => $this->mentoringRequestThree_p3m1->participant->id,
                    'participantName' => $this->userParticipantThree->user->getFullName(),
                    'consultantId' => $this->mentoringRequestThree_p3m1->mentor->id,
                    'consultantName' => $this->mentoringRequestThree_p3m1->mentor->personnel->getFullName(),
                    'programId' => $this->mentoringRequestThree_p3m1->participant->program->id,
                    'programName' => $this->mentoringRequestThree_p3m1->participant->program->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewAll_excludeConcludedMentoringRequest()
    {
        $this->mentoringRequestTwo_p2m2->requestStatus = MentoringRequestStatus::ACCEPTED_BY_PARTICIPANT;
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonContains(['id' => $this->mentoringRequestOne_p1m1->id]);
        $this->seeJsonDoesntContains(['id' => $this->mentoringRequestTwo_p2m2->id]);
        $this->seeJsonContains(['id' => $this->mentoringRequestThree_p3m1->id]);
    }
    public function test_viewAll_sortFromOlderRequest()
    {
        $this->mentoringRequestOne_p1m1->startTime = (new DateTime('-24 hours'));
        $this->mentoringRequestTwo_p2m2->startTime = (new DateTime('-72 hours'));
        $this->mentoringRequestThree_p3m1->startTime = (new DateTime('-48 hours'));
        
        $this->viewAllUri .= "?pageSize=1";
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '3']);
        $this->seeJsonDoesntContains(['id' => $this->mentoringRequestOne_p1m1->id]);
        $this->seeJsonContains(['id' => $this->mentoringRequestTwo_p2m2->id]);
        $this->seeJsonDoesntContains(['id' => $this->mentoringRequestThree_p3m1->id]);
    }
    public function test_viewAll_excludeUnmanagedMentoringRequest_belongsToParticipantOfOtherProgram()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        $this->mentoringRequestTwo_p2m2->participant->program = $otherProgram;
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonContains(['id' => $this->mentoringRequestOne_p1m1->id]);
        $this->seeJsonDoesntContains(['id' => $this->mentoringRequestTwo_p2m2->id]);
        $this->seeJsonContains(['id' => $this->mentoringRequestThree_p3m1->id]);
    }
    public function test_viewAll_excludeUnmanagedMentoringRequest_inProgramWherePersonnelNotAnActiveCoordinator()
    {
        $this->coordinatorTwo->active = false;
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonContains(['id' => $this->mentoringRequestOne_p1m1->id]);
        $this->seeJsonDoesntContains(['id' => $this->mentoringRequestTwo_p2m2->id]);
        $this->seeJsonContains(['id' => $this->mentoringRequestThree_p3m1->id]);
    }
}
