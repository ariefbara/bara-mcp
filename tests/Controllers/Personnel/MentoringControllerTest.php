<?php

namespace Tests\Controllers\Personnel;

use DateTime;
use DateTimeImmutable;
use SharedContext\Domain\ValueObject\DeclaredMentoringStatus;
use SharedContext\Domain\ValueObject\MentoringRequestStatus;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\MentoringSlot\RecordOfBookedMentoringSlot;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfMentoringSlot;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MentoringRequest\RecordOfNegotiatedMentoring;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfDeclaredMentoring;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMentoringRequest;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\RecordOfUser;
use Tests\Controllers\RecordPreparation\Shared\Mentoring\RecordOfMentorReport;
use Tests\Controllers\RecordPreparation\Shared\Mentoring\RecordOfParticipantReport;
use Tests\Controllers\RecordPreparation\Shared\RecordOfMentoring;
use Tests\Controllers\RecordPreparation\User\RecordOfUserParticipant;

class MentoringControllerTest extends PersonnelTestCase
{
    protected $mentoringListInCoordinatedProgramsUri;
    protected $summaryOfOwnedMentoringUri;
    protected $ownedMentoringListUri;
    //
    protected $coordinatorOne;
    protected $coordinatorTwo;
    protected $coordinatorThree;
    
    protected $consultantOne;
    protected $consultantTwo;
    protected $consultantThree;

    protected $clientParticipantOne;
    protected $teamParticipantTwo;
    protected $clientParticipantTwoA;
    protected $userParticipantThree;
    //
    protected $mentoringRequestOne;
    protected $negotiatedMentoringOne;
    protected $mentoringSlotTwo;
    protected $bookedMentoringSlotTwo;
    protected $bookedMentoringSlotTwoA;
    protected $declaredMentoringThree;
    
    protected $mentorReportOne;
    protected $mentorReportTwo;
    
    protected $participantReportOne;
    protected $participantReportTwo;


    protected function setUp(): void
    {
        parent::setUp();
        $this->mentoringListInCoordinatedProgramsUri = $this->personnelUri . "/mentoring-list-in-coordinated-programs";
        $this->summaryOfOwnedMentoringUri = $this->personnelUri . "/summary-of-owned-mentoring";
        $this->ownedMentoringListUri = $this->personnelUri . "/owned-mentoring-list";
        //
        
        $this->connection->table('Program')->truncate();
        $this->connection->table('Coordinator')->truncate();
        $this->connection->table('Consultant')->truncate();
        //
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        //
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        $this->connection->table('NegotiatedMentoring')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('BookedMentoringSlot')->truncate();
        $this->connection->table('DeclaredMentoring')->truncate();
        $this->connection->table('MentorReport')->truncate();
        $this->connection->table('ParticipantReport')->truncate();
        
        $firm = $this->personnel->firm;
        
        $programOne = new RecordOfProgram($firm, 1);
        $programTwo = new RecordOfProgram($firm, 2);
        $programThree = new RecordOfProgram($firm, 3);
        
        $this->coordinatorOne = new RecordOfCoordinator($programOne, $this->personnel, 1);
        $this->coordinatorTwo = new RecordOfCoordinator($programTwo, $this->personnel, 2);
        $this->coordinatorThree = new RecordOfCoordinator($programThree, $this->personnel, 3);
        
        $this->consultantOne = new RecordOfConsultant($programOne, $this->personnel, 1);
        $this->consultantTwo = new RecordOfConsultant($programTwo, $this->personnel, 2);
        $this->consultantThree = new RecordOfConsultant($programThree, $this->personnel, 3);
        
        //
        $clientOne = new RecordOfClient($firm, 1);
        
        $teamOne = new RecordOfTeam($firm, $clientOne, 1);
        
        $userOne = new RecordOfUser(1);
        
        $participantOne = new RecordOfParticipant($programOne, 1);
        $participantTwo = new RecordOfParticipant($programTwo, 2);
        $participantTwoA = new RecordOfParticipant($programTwo, '2A');
        $participantThree = new RecordOfParticipant($programThree, 3);
        
        $this->clientParticipantOne = new RecordOfClientParticipant($clientOne, $participantOne);
        $this->clientParticipantTwoA = new RecordOfClientParticipant($clientOne, $participantTwoA);
        
        $this->teamParticipantTwo = new RecordOfTeamProgramParticipation($teamOne, $participantTwo);
        
        $this->userParticipantThree = new RecordOfUserParticipant($userOne, $participantThree);
        
        //
        $consultationSetupOne = new RecordOfConsultationSetup($programOne, null, null, 1);
        $consultationSetupTwo = new RecordOfConsultationSetup($programTwo, null, null, 2);
        $consultationSetupThree = new RecordOfConsultationSetup($programThree, null, null, 3);
        
        $mentoringOne = new RecordOfMentoring(1);
        $mentoringTwo = new RecordOfMentoring(2);
        $mentoringTwoA = new RecordOfMentoring('2A');
        $mentoringThree = new RecordOfMentoring(3);
        
        $this->mentoringRequestOne = new RecordOfMentoringRequest($participantOne, $this->consultantOne, $consultationSetupOne, 1);
        
        $this->negotiatedMentoringOne = new RecordOfNegotiatedMentoring($this->mentoringRequestOne, $mentoringOne);
        
        $this->mentoringSlotTwo = new RecordOfMentoringSlot($this->consultantTwo, $consultationSetupTwo, 2);
        
        $this->bookedMentoringSlotTwo = new RecordOfBookedMentoringSlot($this->mentoringSlotTwo, $mentoringTwo, $participantTwo);
        $this->bookedMentoringSlotTwoA = new RecordOfBookedMentoringSlot($this->mentoringSlotTwo, $mentoringTwoA, $participantTwoA);
        
        $this->declaredMentoringThree = new RecordOfDeclaredMentoring($this->consultantThree, $participantThree, $consultationSetupThree, $mentoringThree);
        
        $this->mentorReportOne = new RecordOfMentorReport($mentoringOne, null, 1);
        $this->mentorReportTwo = new RecordOfMentorReport($mentoringTwo, null, 2);
        
        $this->participantReportOne = new RecordOfParticipantReport($mentoringOne, null, 1);
        $this->participantReportTwo = new RecordOfParticipantReport($mentoringTwo, null, 2);
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Coordinator')->truncate();
        $this->connection->table('Consultant')->truncate();
        //
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        //
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        $this->connection->table('NegotiatedMentoring')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('BookedMentoringSlot')->truncate();
        $this->connection->table('DeclaredMentoring')->truncate();
        $this->connection->table('MentorReport')->truncate();
        $this->connection->table('ParticipantReport')->truncate();
    }
    
    protected function mentoringListInCoordinatedPrograms()
    {
        $this->coordinatorOne->program->insert($this->connection);
        $this->coordinatorTwo->program->insert($this->connection);
        $this->coordinatorThree->program->insert($this->connection);
        
        $this->coordinatorOne->insert($this->connection);
        $this->coordinatorTwo->insert($this->connection);
        $this->coordinatorThree->insert($this->connection);
        
        $this->consultantOne->insert($this->connection);
        $this->consultantTwo->insert($this->connection);
        $this->consultantThree->insert($this->connection);
        //
        
        $this->clientParticipantOne->client->insert($this->connection);
        $this->teamParticipantTwo->team->insert($this->connection);
        $this->userParticipantThree->user->insert($this->connection);
        
        $this->clientParticipantOne->insert($this->connection);
        $this->clientParticipantTwoA->insert($this->connection);
        $this->teamParticipantTwo->insert($this->connection);
        $this->userParticipantThree->insert($this->connection);
        
        //
        $this->mentoringRequestOne->consultationSetup->insert($this->connection);
        $this->mentoringSlotTwo->consultationSetup->insert($this->connection);
        $this->declaredMentoringThree->consultationSetup->insert($this->connection);
        
        $this->mentoringRequestOne->insert($this->connection);
        $this->negotiatedMentoringOne->insert($this->connection);
        
        $this->mentoringSlotTwo->insert($this->connection);
        $this->bookedMentoringSlotTwo->insert($this->connection);
        $this->bookedMentoringSlotTwoA->insert($this->connection);
        
        $this->declaredMentoringThree->insert($this->connection);
        
        $this->mentorReportOne->insert($this->connection);
        $this->mentorReportTwo->insert($this->connection);
        
        $this->participantReportOne->insert($this->connection);
        $this->participantReportTwo->insert($this->connection);
        
        $this->get($this->mentoringListInCoordinatedProgramsUri, $this->personnel->token);
//echo $this->mentoringListInCoordinatedProgramsUri;
//$this->seeJsonContains(['print']);
    }
    public function test_mentoringListInCoordinatedPrograms_200()
    {
$this->disableExceptionHandling();
        $this->mentoringListInCoordinatedPrograms();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '5',
            'list' => [
                [
                    'mentoringRequestId' => $this->mentoringRequestOne->id,
                            'mentoringRequestStatus' => "{$this->mentoringRequestOne->requestStatus}",
                    'bookedMentoringSlotId' => null,
                    'declaredMentoringId' => null,
                            'declaredMentoringStatus' => null,
                    'participantId' => $this->mentoringRequestOne->participant->id,
                            'participantName' => $this->clientParticipantOne->client->getFullName(),
                        'reportSubmitted' => '1',
                    'mentoringSlotId' => null,
                        'capacity' => null,
                        'totalBooking' => null,
                        'totalSubmittedReport' => null,
                    'startTime' => $this->mentoringRequestOne->startTime,
                    'endTime' => $this->mentoringRequestOne->endTime,
                    'consultantId' => $this->consultantOne->id,
                        'consultantName' => $this->consultantOne->personnel->getFullName(),
                    'coordinatorId' => $this->coordinatorOne->id,
                        'programId' => $this->coordinatorOne->program->id,
                        'programName' => $this->coordinatorOne->program->name,
                ],
                [
                    'mentoringRequestId' => null,
                            'mentoringRequestStatus' => null,
                    'bookedMentoringSlotId' => $this->bookedMentoringSlotTwo->id,
                    'declaredMentoringId' => null,
                            'declaredMentoringStatus' => null,
                    'participantId' => $this->bookedMentoringSlotTwo->participant->id,
                            'participantName' => $this->teamParticipantTwo->team->name,
                        'reportSubmitted' => '1',
                    'mentoringSlotId' => null,
                        'capacity' => null,
                        'totalBooking' => null,
                        'totalSubmittedReport' => null,
                    'startTime' => $this->bookedMentoringSlotTwo->mentoringSlot->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->bookedMentoringSlotTwo->mentoringSlot->endTime->format('Y-m-d H:i:s'),
                    'consultantId' => $this->consultantTwo->id,
                        'consultantName' => $this->consultantTwo->personnel->getFullName(),
                    'coordinatorId' => $this->coordinatorTwo->id,
                        'programId' => $this->coordinatorTwo->program->id,
                        'programName' => $this->coordinatorTwo->program->name,
                ],
                [
                    'mentoringRequestId' => null,
                            'mentoringRequestStatus' => null,
                    'bookedMentoringSlotId' => $this->bookedMentoringSlotTwoA->id,
                    'declaredMentoringId' => null,
                            'declaredMentoringStatus' => null,
                    'participantId' => $this->bookedMentoringSlotTwoA->participant->id,
                            'participantName' => $this->clientParticipantTwoA->client->getFullName(),
                        'reportSubmitted' => '0',
                    'mentoringSlotId' => null,
                        'capacity' => null,
                        'totalBooking' => null,
                        'totalSubmittedReport' => null,
                    'startTime' => $this->bookedMentoringSlotTwoA->mentoringSlot->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->bookedMentoringSlotTwoA->mentoringSlot->endTime->format('Y-m-d H:i:s'),
                    'consultantId' => $this->consultantTwo->id,
                        'consultantName' => $this->consultantTwo->personnel->getFullName(),
                    'coordinatorId' => $this->coordinatorTwo->id,
                        'programId' => $this->coordinatorTwo->program->id,
                        'programName' => $this->coordinatorTwo->program->name,
                ],
                [
                    'mentoringRequestId' => null,
                            'mentoringRequestStatus' => null,
                    'bookedMentoringSlotId' => null,
                    'declaredMentoringId' => null,
                            'declaredMentoringStatus' => null,
                    'participantId' => null,
                            'participantName' => null,
                        'reportSubmitted' => null,
                    'mentoringSlotId' => $this->mentoringSlotTwo->id,
                        'capacity' => "{$this->mentoringSlotTwo->capacity}",
                        'totalBooking' => '2',
                        'totalSubmittedReport' => '2',
                    'startTime' => $this->mentoringSlotTwo->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->mentoringSlotTwo->endTime->format('Y-m-d H:i:s'),
                    'consultantId' => $this->mentoringSlotTwo->consultant->id,
                        'consultantName' => $this->mentoringSlotTwo->consultant->personnel->getFullName(),
                    'coordinatorId' => $this->coordinatorTwo->id,
                        'programId' => $this->coordinatorTwo->program->id,
                        'programName' => $this->coordinatorTwo->program->name,
                ],
                [
                    'mentoringRequestId' => null,
                            'mentoringRequestStatus' => null,
                    'bookedMentoringSlotId' => null,
                    'declaredMentoringId' => $this->declaredMentoringThree->id,
                            'declaredMentoringStatus' => "{$this->declaredMentoringThree->declaredStatus}",
                    'participantId' => $this->declaredMentoringThree->participant->id,
                            'participantName' => $this->userParticipantThree->user->getFullName(),
                        'reportSubmitted' => '0',
                    'mentoringSlotId' => null,
                        'capacity' => null,
                        'totalBooking' => null,
                        'totalSubmittedReport' => null,
                    'startTime' => $this->declaredMentoringThree->startTime,
                    'endTime' => $this->declaredMentoringThree->endTime,
                    'consultantId' => $this->declaredMentoringThree->mentor->id,
                        'consultantName' => $this->declaredMentoringThree->mentor->personnel->getFullName(),
                    'coordinatorId' => $this->coordinatorThree->id,
                        'programId' => $this->coordinatorThree->program->id,
                        'programName' => $this->coordinatorThree->program->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_mentoringListInCoordinatedPrograms_fromCoordinatorDashboard()
    {
        $from = (new DateTime())->format('Y-m-d H:i:s');
        $this->mentoringListInCoordinatedProgramsUri .= 
                "?from=$from"
                . "&typeList[]=mentoring-request"
                . "&status=negotiating";
        
        $this->mentoringListInCoordinatedPrograms();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['mentoringRequestId' => $this->mentoringRequestOne->id]);
        $this->seeJsonDoesntContains(['bookedMentoringSlotId' => $this->bookedMentoringSlotTwo->id]);
        $this->seeJsonDoesntContains(['bookedMentoringSlotId' => $this->bookedMentoringSlotTwoA->id]);
        $this->seeJsonDoesntContains(['declaredMentoringId' => $this->declaredMentoringThree->id]);
        $this->seeJsonDoesntContains(['mentoringSlotId' => $this->mentoringSlotTwo->id]);
    }
    public function test_mentoringListInCoordinatedPrograms_fromParticipantPage()
    {
        $to = (new DateTime())->format('Y-m-d H:i:s');
        $this->mentoringSlotTwo->startTime = new DateTimeImmutable('-25 hours');
        $this->mentoringSlotTwo->endTime = new DateTimeImmutable('-24 hours');
        $this->mentoringListInCoordinatedProgramsUri .= 
                "?participantId={$this->teamParticipantTwo->id}"
                . "&to=$to";
        
        $this->mentoringListInCoordinatedPrograms();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['mentoringRequestId' => $this->mentoringRequestOne->id]);
        $this->seeJsonContains(['bookedMentoringSlotId' => $this->bookedMentoringSlotTwo->id]);
        $this->seeJsonDoesntContains(['bookedMentoringSlotId' => $this->bookedMentoringSlotTwoA->id]);
        $this->seeJsonDoesntContains(['declaredMentoringId' => $this->declaredMentoringThree->id]);
        $this->seeJsonDoesntContains(['mentoringSlotId' => $this->mentoringSlotTwo->id]);
    }
    public function test_mentoringListInCoordinatedPrograms_fromLeftMenuDefault_tabUpcomingDefault()
    {
        $from = (new DateTime())->format('Y-m-d H:i:s');
        $this->mentoringListInCoordinatedProgramsUri .= 
                "?from=$from"
                . "&typeList[]=mentoring-request"
                . "&status=negotiating";
        
        $this->mentoringListInCoordinatedPrograms();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['mentoringRequestId' => $this->mentoringRequestOne->id]);
        $this->seeJsonDoesntContains(['bookedMentoringSlotId' => $this->bookedMentoringSlotTwo->id]);
        $this->seeJsonDoesntContains(['bookedMentoringSlotId' => $this->bookedMentoringSlotTwoA->id]);
        $this->seeJsonDoesntContains(['declaredMentoringId' => $this->declaredMentoringThree->id]);
        $this->seeJsonDoesntContains(['mentoringSlotId' => $this->mentoringSlotTwo->id]);
    }
    public function test_mentoringListInCoordinatedPrograms_fromLeftMenu_tabPastDefault()
    {
$this->disableExceptionHandling();
        //will show all past confirmed mentoring with incomplete report
        $this->mentoringSlotTwo->startTime = new DateTimeImmutable('-25 hours');
        $this->mentoringSlotTwo->endTime = new DateTimeImmutable('-24 hours');
        
        $to = (new DateTime())->format('Y-m-d H:i:s');
        $this->mentoringListInCoordinatedProgramsUri .= 
                "?to=$to"
                . "&status=confirmed"
                . "&reportSubmitted=false";
        
        $this->mentoringListInCoordinatedPrograms();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonDoesntContains(['mentoringRequestId' => $this->mentoringRequestOne->id]);
        $this->seeJsonDoesntContains(['bookedMentoringSlotId' => $this->bookedMentoringSlotTwo->id]);
        $this->seeJsonContains(['bookedMentoringSlotId' => $this->bookedMentoringSlotTwoA->id]);
        $this->seeJsonDoesntContains(['declaredMentoringId' => $this->declaredMentoringThree->id]);
        $this->seeJsonContains(['mentoringSlotId' => $this->mentoringSlotTwo->id]);
    }
    public function test_mentoringListInCoordinatedPrograms_allFilter()
    {
        $from = (new DateTime('-2 months'))->format('Y-m-d H:i:s');
        $to = (new DateTime('+2 months'))->format('Y-m-d H:i:s');
        $this->mentoringListInCoordinatedProgramsUri .= 
                "?to=$to"
                . "&from=$from"
                . "&programId={$this->coordinatorOne->program->id}"
                . "&participantId={$this->clientParticipantOne->participant->id}"
                . "&status=negotiating"
                . "&reportSubmitted=true"
                . "&order=start-time-asc";
        
        $this->mentoringListInCoordinatedPrograms();
        $this->seeStatusCode(200);
        
//$this->seeJsonContains(['print']);
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['mentoringRequestId' => $this->mentoringRequestOne->id]);
        $this->seeJsonDoesntContains(['bookedMentoringSlotId' => $this->bookedMentoringSlotTwo->id]);
        $this->seeJsonDoesntContains(['bookedMentoringSlotId' => $this->bookedMentoringSlotTwoA->id]);
        $this->seeJsonDoesntContains(['declaredMentoringId' => $this->declaredMentoringThree->id]);
        $this->seeJsonDoesntContains(['mentoringSlotId' => $this->mentoringSlotTwo->id]);
    }
    
    //
    protected function summaryOfOwnedMentoring()
    {
        $this->consultantOne->program->insert($this->connection);
        $this->consultantTwo->program->insert($this->connection);
        $this->consultantThree->program->insert($this->connection);

        $this->consultantOne->insert($this->connection);
        $this->consultantTwo->insert($this->connection);
        $this->consultantThree->insert($this->connection);
        //
        
        $this->clientParticipantOne->client->insert($this->connection);
        $this->teamParticipantTwo->team->insert($this->connection);
        $this->userParticipantThree->user->insert($this->connection);
        
        $this->clientParticipantOne->insert($this->connection);
        $this->clientParticipantTwoA->insert($this->connection);
        $this->teamParticipantTwo->insert($this->connection);
        $this->userParticipantThree->insert($this->connection);
        
        //
        $this->mentoringRequestOne->consultationSetup->insert($this->connection);
        $this->mentoringSlotTwo->consultationSetup->insert($this->connection);
        $this->declaredMentoringThree->consultationSetup->insert($this->connection);
        
        $this->mentoringRequestOne->insert($this->connection);
        $this->negotiatedMentoringOne->insert($this->connection);
        
        $this->mentoringSlotTwo->insert($this->connection);
        $this->bookedMentoringSlotTwo->insert($this->connection);
        $this->bookedMentoringSlotTwoA->insert($this->connection);
        
        $this->declaredMentoringThree->insert($this->connection);
        
        $this->mentorReportOne->insert($this->connection);
        $this->mentorReportTwo->insert($this->connection);
        
        $this->get($this->summaryOfOwnedMentoringUri, $this->personnel->token);
//echo $this->summaryOfOwnedMentoringUri;
//$this->seeJsonContains(['print']);
    }
    public function test_summaryOfOwnedMentoring_200()
    {
$this->disableExceptionHandling();
        $this->summaryOfOwnedMentoring();
        $this->seeStatusCode(200);
        
        $response = [
            'availableSlot' => '1',
            'ongoingMentoringRequest' => '1',
            'confirmedMentoring' => '1',
            'completedMentoring' => '0',
            'incompleteReportMentoring' => '1',
        ];
        $this->seeJsonContains($response);
    }
    public function test_summaryOfOwnedMentoring_slot_noBookedSlot()
    {
        $this->bookedMentoringSlotTwo->cancelled = true;
        $this->bookedMentoringSlotTwoA->cancelled = true;
        
        $this->summaryOfOwnedMentoring();
        $this->seeStatusCode(200);
        
        $response = [
            'availableSlot' => '1',
            'ongoingMentoringRequest' => '1',
            'confirmedMentoring' => '0',
            'completedMentoring' => '0',
            'incompleteReportMentoring' => '1',
        ];
        $this->seeJsonContains($response);
    }
    public function test_summaryOfOwnedMentoring_slot_allSlotBooked()
    {
        $this->mentoringSlotTwo->capacity = 2;
        $this->summaryOfOwnedMentoring();
        $this->seeStatusCode(200);
        
        $response = [
            'availableSlot' => '0',
            'ongoingMentoringRequest' => '1',
            'confirmedMentoring' => '1',
            'completedMentoring' => '0',
            'incompleteReportMentoring' => '1',
        ];
        $this->seeJsonContains($response);
    }
    public function test_summaryOfOwnedMentoring_slot_pastSlot()
    {
        $this->mentoringSlotTwo->startTime = new DateTimeImmutable('-25 hours');
        $this->mentoringSlotTwo->endTime = new DateTimeImmutable('-24 hours');
        $this->summaryOfOwnedMentoring();
        $this->seeStatusCode(200);
        
        $response = [
            'availableSlot' => '0',
            'ongoingMentoringRequest' => '1',
            'confirmedMentoring' => '0',
            'completedMentoring' => '0',
            'incompleteReportMentoring' => '2',
        ];
        $this->seeJsonContains($response);
    }
    public function test_summaryOfOwnedMentoring_slot_pastSlotWithCompleteReport()
    {
        $mentorReportTwoA = new RecordOfMentorReport($this->bookedMentoringSlotTwoA->mentoring, null, '2a');
        $mentorReportTwoA->insert($this->connection);
        
        $this->mentoringSlotTwo->startTime = new DateTimeImmutable('-25 hours');
        $this->mentoringSlotTwo->endTime = new DateTimeImmutable('-24 hours');
        
        $this->summaryOfOwnedMentoring();
        $this->seeStatusCode(200);
        
        $response = [
            'availableSlot' => '0',
            'ongoingMentoringRequest' => '1',
            'confirmedMentoring' => '0',
            'completedMentoring' => '1',
            'incompleteReportMentoring' => '1',
        ];
        $this->seeJsonContains($response);
    }
    public function test_summaryOfOwnedMentoring_mentoringRequest_approved()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::APPROVED_BY_MENTOR;
        $this->summaryOfOwnedMentoring();
        $this->seeStatusCode(200);
        
        $response = [
            'availableSlot' => '1',
            'ongoingMentoringRequest' => '0',
            'confirmedMentoring' => '2',
            'completedMentoring' => '0',
            'incompleteReportMentoring' => '1',
        ];
        $this->seeJsonContains($response);
    }
    public function test_summaryOfOwnedMentoring_mentoringRequest_accepted()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::ACCEPTED_BY_PARTICIPANT;
        $this->summaryOfOwnedMentoring();
        $this->seeStatusCode(200);
        
        $response = [
            'availableSlot' => '1',
            'ongoingMentoringRequest' => '0',
            'confirmedMentoring' => '2',
            'completedMentoring' => '0',
            'incompleteReportMentoring' => '1',
        ];
        $this->seeJsonContains($response);
    }
    public function test_summaryOfOwnedMentoring_mentoringRequest_pastOngoing()
    {
        $this->mentoringRequestOne->startTime = (new DateTime('-25 hours'))->format('Y-m-d H:i:s');
        $this->mentoringRequestOne->endTime = (new DateTime('-24 hours'))->format('Y-m-d H:i:s');
        $this->summaryOfOwnedMentoring();
        $this->seeStatusCode(200);
        
        $response = [
            'availableSlot' => '1',
            'ongoingMentoringRequest' => '1',
            'confirmedMentoring' => '1',
            'completedMentoring' => '0',
            'incompleteReportMentoring' => '1',
        ];
        $this->seeJsonContains($response);
    }
    public function test_summaryOfOwnedMentoring_mentoringRequest_pastConfirmed()
    {
        $otherMentoring = new RecordOfMentoring('other');
        $otherMentoring->insert($this->connection);
        $this->mentorReportOne->mentoring = $otherMentoring;

        $this->mentoringRequestOne->startTime = (new DateTime('-25 hours'))->format('Y-m-d H:i:s');
        $this->mentoringRequestOne->endTime = (new DateTime('-24 hours'))->format('Y-m-d H:i:s');
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::APPROVED_BY_MENTOR;
        
        $this->summaryOfOwnedMentoring();
        $this->seeStatusCode(200);
        
        $response = [
            'availableSlot' => '1',
            'ongoingMentoringRequest' => '0',
            'confirmedMentoring' => '1',
            'completedMentoring' => '0',
            'incompleteReportMentoring' => '2',
        ];
        $this->seeJsonContains($response);
    }
    public function test_summaryOfOwnedMentoring_mentoringRequest_pastConfirmedAndReportSubmitted()
    {
        $this->mentoringRequestOne->startTime = (new DateTime('-25 hours'))->format('Y-m-d H:i:s');
        $this->mentoringRequestOne->endTime = (new DateTime('-24 hours'))->format('Y-m-d H:i:s');
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::APPROVED_BY_MENTOR;
        
        $this->summaryOfOwnedMentoring();
        $this->seeStatusCode(200);
        
        $response = [
            'availableSlot' => '1',
            'ongoingMentoringRequest' => '0',
            'confirmedMentoring' => '1',
            'completedMentoring' => '1',
            'incompleteReportMentoring' => '1',
        ];
        $this->seeJsonContains($response);
    }
    public function test_summaryOfOwnedMentoring_declaredMentoring_ReportSubmitted()
    {
        $this->mentorReportTwo->mentoring = $this->declaredMentoringThree->mentoring;
        
        $this->summaryOfOwnedMentoring();
        $this->seeStatusCode(200);
        
        $response = [
            'availableSlot' => '1',
            'ongoingMentoringRequest' => '1',
            'confirmedMentoring' => '1',
            'completedMentoring' => '1',
            'incompleteReportMentoring' => '0',
        ];
        $this->seeJsonContains($response);
    }
    
    protected function ownedMentoringList()
    {
        $this->consultantOne->program->insert($this->connection);
        $this->consultantTwo->program->insert($this->connection);
        $this->consultantThree->program->insert($this->connection);

        $this->consultantOne->insert($this->connection);
        $this->consultantTwo->insert($this->connection);
        $this->consultantThree->insert($this->connection);
        //
        
        $this->clientParticipantOne->client->insert($this->connection);
        $this->teamParticipantTwo->team->insert($this->connection);
        $this->userParticipantThree->user->insert($this->connection);
        
        $this->clientParticipantOne->insert($this->connection);
        $this->clientParticipantTwoA->insert($this->connection);
        $this->teamParticipantTwo->insert($this->connection);
        $this->userParticipantThree->insert($this->connection);
        
        //
        $this->mentoringRequestOne->consultationSetup->insert($this->connection);
        $this->mentoringSlotTwo->consultationSetup->insert($this->connection);
        $this->declaredMentoringThree->consultationSetup->insert($this->connection);
        
        $this->mentoringRequestOne->insert($this->connection);
        $this->negotiatedMentoringOne->insert($this->connection);
        
        $this->mentoringSlotTwo->insert($this->connection);
        $this->bookedMentoringSlotTwo->insert($this->connection);
        $this->bookedMentoringSlotTwoA->insert($this->connection);
        
        $this->declaredMentoringThree->insert($this->connection);
        
//        $this->mentorReportOne->insert($this->connection);
//        $this->mentorReportTwo->insert($this->connection);
        //
        $this->get($this->ownedMentoringListUri, $this->personnel->token);
echo $this->ownedMentoringListUri;
//$this->seeJsonContains(['print']);
    }
    public function test_ownedMentoringList_200()
    {
$this->disableExceptionHandling();
        $this->ownedMentoringList();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '3',
            'list' => [
                [
                    'mentoringRequestId' => $this->mentoringRequestOne->id,
                    'mentoringRequestStatus' => MentoringRequestStatus::DISPLAY_VALUE[$this->mentoringRequestOne->requestStatus],
                    'declaredMentoringId' => null,
                    'declaredMentoringStatus' => null,
                    'participantId' => $this->mentoringRequestOne->participant->id,
                    'participantName' => $this->clientParticipantOne->client->getFullName(),
                    'reportSubmitted' => '0',
                    'mentoringSlotId' => null,
                    'capacity' => null,
                    'totalBooking' => null,
                    'totalSubmittedReport' => null,
                    'startTime' => $this->mentoringRequestOne->startTime,
                    'endTime' => $this->mentoringRequestOne->endTime,
                    'consultantId' => $this->consultantOne->id,
                    'programId' => $this->consultantOne->program->id,
                    'programName' => $this->consultantOne->program->name,
                ],
                [
                    'mentoringRequestId' => null,
                    'mentoringRequestStatus' => null,
                    'declaredMentoringId' => null,
                    'declaredMentoringStatus' => null,
                    'participantId' => null,
                    'participantName' => null,
                    'reportSubmitted' => null,
                    'mentoringSlotId' => $this->mentoringSlotTwo->id,
                    'capacity' => strval($this->mentoringSlotTwo->capacity),
                    'totalBooking' => '2',
                    'totalSubmittedReport' => '0',
                    'startTime' => $this->mentoringSlotTwo->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->mentoringSlotTwo->endTime->format('Y-m-d H:i:s'),
                    'consultantId' => $this->consultantTwo->id,
                    'programId' => $this->consultantTwo->program->id,
                    'programName' => $this->consultantTwo->program->name,
                ],
                [
                    'mentoringRequestId' => null,
                    'mentoringRequestStatus' => null,
                    'declaredMentoringId' => $this->declaredMentoringThree->id,
                    'declaredMentoringStatus' => DeclaredMentoringStatus::DISPLAY_VALUES[$this->declaredMentoringThree->declaredStatus],
                    'participantId' => $this->declaredMentoringThree->participant->id,
                    'participantName' => $this->userParticipantThree->user->getFullName(),
                    'reportSubmitted' => '0',
                    'mentoringSlotId' => null,
                    'capacity' => null,
                    'totalBooking' => null,
                    'totalSubmittedReport' => null,
                    'startTime' => $this->declaredMentoringThree->startTime,
                    'endTime' => $this->declaredMentoringThree->endTime,
                    'consultantId' => $this->declaredMentoringThree->mentor->id,
                    'programId' => $this->declaredMentoringThree->mentor->program->id,
                    'programName' => $this->declaredMentoringThree->mentor->program->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_ownedMentoringList_allFilter_200()
    {
        $from = (new DateTime('-2 months'))->format('Y-m-d H:i:s');
        $to = (new DateTime('+2 months'))->format('Y-m-d H:i:s');
        $this->ownedMentoringListUri .= ""
                . "?from=$from"
                . "&to=$to"
                . "&programId={$this->mentoringRequestOne->participant->program->id}"
                . "&participantId={$this->mentoringRequestOne->participant->id}"
                . "&reportSubmitted=false"
                . "&status=negotiating"
                . "&typeList[]=mentoring-request"
                . "&typeList[]=mentoring-slot"
                . "&order=start-time-asc";
                
        $this->ownedMentoringList();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['mentoringRequestId' => $this->mentoringRequestOne->id]);
        $this->seeJsonDoesntContains(['mentoringSlotId' => $this->mentoringSlotTwo->id]);
        $this->seeJsonDoesntContains(['decaredMentoringId' => $this->declaredMentoringThree->id]);
    }
    public function test_ownedMentoringList_availableSlot()
    {
        $this->mentoringSlotTwo->startTime = new DateTime('+24 hours');
        $this->mentoringSlotTwo->endTime = new DateTime('+25 hours');
        
        $from = (new DateTime(''))->format('Y-m-d H:i:s');
        $this->ownedMentoringListUri .= ""
                . "?from=$from"
                . "&status=negotiating"
                . "&typeList[]=mentoring-slot";
                
        $this->ownedMentoringList();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['mentoringRequestId' => $this->mentoringRequestOne->id]);
        $this->seeJsonContains(['mentoringSlotId' => $this->mentoringSlotTwo->id]);
        $this->seeJsonDoesntContains(['declaredMentoringId' => $this->declaredMentoringThree->id]);
    }
    public function test_ownedMentoringList_mentoringRequest()
    {
        $this->mentoringRequestOne->startTime = (new DateTime('+24 hours'))->format('Y-m-d H:i:s');
        $this->mentoringRequestOne->endTime = (new DateTime('+25 hours'))->format('Y-m-d H:i:s');
        
        $this->ownedMentoringListUri .= ""
                . "?status=negotiating"
                . "&typeList[]=mentoring-request";
                
        $this->ownedMentoringList();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['mentoringRequestId' => $this->mentoringRequestOne->id]);
        $this->seeJsonDoesntContains(['mentoringSlotId' => $this->mentoringSlotTwo->id]);
        $this->seeJsonDoesntContains(['declaredMentoringId' => $this->declaredMentoringThree->id]);
    }
    public function test_ownedMentoringList_confirmedMentoring()
    {
        $this->mentoringRequestOne->startTime = (new DateTime('+24 hours'))->format('Y-m-d H:i:s');
        $this->mentoringRequestOne->endTime = (new DateTime('+25 hours'))->format('Y-m-d H:i:s');
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::APPROVED_BY_MENTOR;
        $this->mentoringSlotTwo->startTime = new DateTime('+24 hours');
        $this->mentoringSlotTwo->endTime = new DateTime('+25 hours');
        
        $from = (new DateTime(''))->format('Y-m-d H:i:s');
        $this->ownedMentoringListUri .= ""
                . "?from=$from"
                . "&status=confirmed"
                . "&typeList[]=mentoring-request"
                . "&typeList[]=mentoring-slot";
                
        $this->ownedMentoringList();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['mentoringRequestId' => $this->mentoringRequestOne->id]);
        $this->seeJsonDoesntContains(['mentoringSlotId' => $this->mentoringSlotTwo->id]);
        $this->seeJsonDoesntContains(['declaredMentoringId' => $this->declaredMentoringThree->id]);
    }
    public function test_ownedMentoringList_completedMentoring()
    {
        $this->mentoringRequestOne->startTime = (new DateTime('-25 hours'))->format('Y-m-d H:i:s');
        $this->mentoringRequestOne->endTime = (new DateTime('-24 hours'))->format('Y-m-d H:i:s');
        
        $this->mentorReportOne->insert($this->connection);
        
        $to = (new DateTime(''))->format('Y-m-d H:i:s');
        $this->ownedMentoringListUri .= ""
                . "?to=$to"
                . "&reportSubmitted=true";
                
        $this->ownedMentoringList();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['mentoringRequestId' => $this->mentoringRequestOne->id]);
        $this->seeJsonDoesntContains(['mentoringSlotId' => $this->mentoringSlotTwo->id]);
        $this->seeJsonDoesntContains(['declaredMentoringId' => $this->declaredMentoringThree->id]);
    }
    public function test_ownedMentoringList_completedMentoring_mentoringSlot()
    {
        $this->mentoringSlotTwo->startTime = new DateTime('-25 hours');
        $this->mentoringSlotTwo->endTime = new DateTime('-24 hours');
        
        $this->mentorReportOne->mentoring = $this->bookedMentoringSlotTwoA->mentoring;
        $this->mentorReportOne->insert($this->connection);
        $this->mentorReportTwo->insert($this->connection);
        
        $to = (new DateTime(''))->format('Y-m-d H:i:s');
        $this->ownedMentoringListUri .= ""
                . "?to=$to"
                . "&reportSubmitted=true";
                
        $this->ownedMentoringList();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['mentoringRequestId' => $this->mentoringRequestOne->id]);
        $this->seeJsonContains(['mentoringSlotId' => $this->mentoringSlotTwo->id]);
        $this->seeJsonDoesntContains(['declaredMentoringId' => $this->declaredMentoringThree->id]);
    }
    public function test_ownedMentoringList_incompleteReportMentoring()
    {
        $this->mentoringSlotTwo->startTime = new DateTime('-25 hours');
        $this->mentoringSlotTwo->endTime = new DateTime('-24 hours');
        $this->declaredMentoringThree->declaredStatus = DeclaredMentoringStatus::APPROVED_BY_MENTOR;
        
        $this->mentorReportTwo->insert($this->connection);
        
        $to = (new DateTime(''))->format('Y-m-d H:i:s');
        $this->ownedMentoringListUri .= ""
                . "?to=$to"
                . "&status=confirmed"
                . "&reportSubmitted=false";
                
        $this->ownedMentoringList();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonDoesntContains(['mentoringRequestId' => $this->mentoringRequestOne->id]);
        $this->seeJsonContains(['mentoringSlotId' => $this->mentoringSlotTwo->id]);
        $this->seeJsonContains(['declaredMentoringId' => $this->declaredMentoringThree->id]);
    }
    public function test_ownedMentoringList_incompleteReportMentoring_bug20221207_shouldExcludeCompletedMentoringSlot()
    {
        $this->mentoringRequestOne->startTime = (new DateTime('-25 hours'))->format('Y-m-d H:i:s');
        $this->mentoringRequestOne->endTime = (new DateTime('-24 hours'))->format('Y-m-d H:i:s');
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::APPROVED_BY_MENTOR;
        $this->mentoringSlotTwo->startTime = new DateTime('-25 hours');
        $this->mentoringSlotTwo->endTime = new DateTime('-24 hours');
        $this->declaredMentoringThree->declaredStatus = DeclaredMentoringStatus::APPROVED_BY_MENTOR;
        
        $this->mentorReportOne->mentoring = $this->bookedMentoringSlotTwoA->mentoring;
        
        $this->mentorReportOne->insert($this->connection);
        $this->mentorReportTwo->insert($this->connection);
        
        $to = (new DateTime(''))->format('Y-m-d H:i:s');
        $this->ownedMentoringListUri .= ""
                . "?to=$to"
                . "&status=confirmed"
                . "&reportSubmitted=false";
                
        $this->ownedMentoringList();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonContains(['mentoringRequestId' => $this->mentoringRequestOne->id]);
        $this->seeJsonDoesntContains(['mentoringSlotId' => $this->mentoringSlotTwo->id]);
        $this->seeJsonContains(['declaredMentoringId' => $this->declaredMentoringThree->id]);
    }
    public function test_ownedMentoringList_incompleteReportMentoring_bug20221207_shouldExcludeEmptyMentoringSlot()
    {
        $this->mentoringRequestOne->startTime = (new DateTime('-25 hours'))->format('Y-m-d H:i:s');
        $this->mentoringRequestOne->endTime = (new DateTime('-24 hours'))->format('Y-m-d H:i:s');
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::APPROVED_BY_MENTOR;
        $this->mentoringSlotTwo->startTime = new DateTime('-25 hours');
        $this->mentoringSlotTwo->endTime = new DateTime('-24 hours');
        $this->declaredMentoringThree->declaredStatus = DeclaredMentoringStatus::APPROVED_BY_MENTOR;
        
        $this->bookedMentoringSlotTwo->cancelled = true;
        $this->bookedMentoringSlotTwoA->cancelled = true;
        
        $to = (new DateTime(''))->format('Y-m-d H:i:s');
        $this->ownedMentoringListUri .= ""
                . "?to=$to"
                . "&status=confirmed"
                . "&reportSubmitted=false";
                
        $this->ownedMentoringList();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonContains(['mentoringRequestId' => $this->mentoringRequestOne->id]);
        $this->seeJsonDoesntContains(['mentoringSlotId' => $this->mentoringSlotTwo->id]);
        $this->seeJsonContains(['declaredMentoringId' => $this->declaredMentoringThree->id]);
    }
}
