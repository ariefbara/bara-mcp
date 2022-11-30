<?php

namespace Tests\Controllers\Personnel;

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
        
echo $this->mentoringListInCoordinatedProgramsUri;
        $this->get($this->mentoringListInCoordinatedProgramsUri, $this->personnel->token);
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
        $from = (new \DateTime())->format('Y-m-d H:i:s');
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
        $to = (new \DateTime())->format('Y-m-d H:i:s');
        $this->mentoringSlotTwo->startTime = new \DateTimeImmutable('-2 days');
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
        $from = (new \DateTime())->format('Y-m-d H:i:s');
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
        $this->mentoringSlotTwo->startTime = new \DateTimeImmutable('-2 days');
        
        $to = (new \DateTime())->format('Y-m-d H:i:s');
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
        $from = (new \DateTime('-2 months'))->format('Y-m-d H:i:s');
        $to = (new \DateTime('+2 months'))->format('Y-m-d H:i:s');
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
}
