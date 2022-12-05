<?php

namespace Tests\Controllers\Personnel;

use SharedContext\Domain\ValueObject\MentoringRequestStatus;
use SharedContext\Domain\ValueObject\RegistrationStatus;
use SharedContext\Domain\ValueObject\TaskReportReviewStatus;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfConsultantComment;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfConsultantTask;
use Tests\Controllers\RecordPreparation\Firm\Program\Coordinator\RecordOfCoordinatorTask;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\RecordOfMetricAssignmentReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMentoringRequest;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMetricAssignment;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfTask;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfWorksheet;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\Task\RecordOfTaskReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\Worksheet\RecordOfComment;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfRegistrant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfWorksheetForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class CoordinatorDashboardSummaryControllerTest extends AggregatedCoordinatorInPersonnelContextTestCase
{

    protected $viewUri;
    //
    protected $registrantOne_prog1;
    protected $registrantTwo_prog2;
    protected $registrantThree_prog1;
    //
    protected $participantOne_prog1;
    protected $participantTwo_prog2;
    protected $participantThree_prog1;
    //
    protected $worksheetOne_pt1;
    protected $worksheetTwo_pt2;
    protected $worksheetThree_pt3;
    //
    protected $mentoringRequestOne_pt1;
    protected $mentoringRequestTwo_pt2;
    protected $mentoringRequestThree_pt3;
    //
    protected $metricReportOne_pt1;
    protected $metricReportTwo_pt2;
    protected $metricReportThree_pt3;
    //
    protected $coordinatorTaskOne;
    protected $coordinatorTaskTwo;
    protected $consultantTaskTwoA;
    protected $taskReportTwo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->viewUri = $this->personnelUri . "/coordinator-dashboard-summary";
        //
        $this->connection->table('Registrant')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('Worksheet')->truncate();
        $this->connection->table('Comment')->truncate();
        $this->connection->table('ConsultantComment')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        $this->connection->table('MetricAssignment')->truncate();
        $this->connection->table('MetricAssignmentReport')->truncate();
        //
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Task')->truncate();
        $this->connection->table('CoordinatorTask')->truncate();
        $this->connection->table('ConsultantTask')->truncate();
        $this->connection->table('TaskReport')->truncate();
        //
        $firm = $this->personnel->firm;
        $programOne = $this->coordinatorOne->program;
        $programTwo = $this->coordinatorTwo->program;
        //
        $this->registrantOne_prog1 = new RecordOfRegistrant($programOne, 1);
        $this->registrantTwo_prog2 = new RecordOfRegistrant($programTwo, 2);
        $this->registrantThree_prog1 = new RecordOfRegistrant($programOne, 3);
        //
        $this->participantOne_prog1 = new RecordOfParticipant($programOne, 1);
        $this->participantTwo_prog2 = new RecordOfParticipant($programTwo, 2);
        $this->participantThree_prog1 = new RecordOfParticipant($programOne, 3);

        $formOne = new RecordOfForm(1);
        
        $formRecordOne = new RecordOfFormRecord($formOne, 1);
        $formRecordTwo = new RecordOfFormRecord($formOne, 2);
        $formRecordThree = new RecordOfFormRecord($formOne, 3);
        
        $worksheetFormOne = new RecordOfWorksheetForm($firm, $formOne);
        
        $missionOne = new RecordOfMission($programOne, $worksheetFormOne, 1, null);
        $missionTwo = new RecordOfMission($programTwo, $worksheetFormOne, 2, null);

        $this->worksheetOne_pt1 = new RecordOfWorksheet($this->participantOne_prog1, $formRecordOne, $missionOne, 1);
        $this->worksheetTwo_pt2 = new RecordOfWorksheet($this->participantTwo_prog2, $formRecordTwo, $missionTwo, 2);
        $this->worksheetThree_pt3 = new RecordOfWorksheet($this->participantThree_prog1, $formRecordThree, $missionOne, 3);
        //
        $mentorOne = new RecordOfConsultant($programOne, $this->personnel, 1);
        $mentorTwo = new RecordOfConsultant($programTwo, $this->personnel, 2);
        
        $consultationSetupOne = new RecordOfConsultationSetup($programOne, null, null, 1);
        $consultationSetupTwo = new RecordOfConsultationSetup($programTwo, null, null, 2);
        
        $this->mentoringRequestOne_pt1 = new RecordOfMentoringRequest($this->participantOne_prog1, $mentorOne, $consultationSetupOne, 1);
        $this->mentoringRequestTwo_pt2 = new RecordOfMentoringRequest($this->participantTwo_prog2, $mentorTwo, $consultationSetupTwo, 2);
        $this->mentoringRequestThree_pt3 = new RecordOfMentoringRequest($this->participantThree_prog1, $mentorOne, $consultationSetupOne, 3);
        //
        $metricAssignmentOne = new RecordOfMetricAssignment($this->participantOne_prog1, 1);
        $metricAssignmentTwo = new RecordOfMetricAssignment($this->participantTwo_prog2, 2);
        $metricAssignmentThree = new RecordOfMetricAssignment($this->participantThree_prog1, 3);
        
        $this->metricReportOne_pt1 = new RecordOfMetricAssignmentReport($metricAssignmentOne, 1);
        $this->metricReportTwo_pt2 = new RecordOfMetricAssignmentReport($metricAssignmentTwo, 2);
        $this->metricReportThree_pt3 = new RecordOfMetricAssignmentReport($metricAssignmentThree, 3);
        //
        $taskOne = new RecordOfTask($this->participantOne_prog1, 1);
        $taskTwo = new RecordOfTask($this->participantTwo_prog2, 2);
        $taskTwoA = new RecordOfTask($this->participantTwo_prog2, '2a');
        
        
        $this->coordinatorTaskOne = new RecordOfCoordinatorTask($this->coordinatorOne, $taskOne);
        $this->coordinatorTaskTwo = new RecordOfCoordinatorTask($this->coordinatorTwo, $taskTwo);
        $this->consultantTaskTwoA = new RecordOfConsultantTask($mentorTwo, $taskTwoA);
        
        $this->taskReportTwo = new RecordOfTaskReport($taskTwo, 2);
        
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Registrant')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('Worksheet')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        $this->connection->table('MetricAssignment')->truncate();
        $this->connection->table('MetricAssignmentReport')->truncate();
        $this->connection->table('Comment')->truncate();
        $this->connection->table('ConsultantComment')->truncate();
        
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Task')->truncate();
        $this->connection->table('CoordinatorTask')->truncate();
        $this->connection->table('ConsultantTask')->truncate();
        $this->connection->table('TaskReport')->truncate();
    }
    
    protected function view()
    {
        $this->persistAggregatedCoordinatorDependency();
        //
        $this->registrantOne_prog1->insert($this->connection);
        $this->registrantTwo_prog2->insert($this->connection);
        $this->registrantThree_prog1->insert($this->connection);
        //
        $this->participantOne_prog1->insert($this->connection);
        $this->participantTwo_prog2->insert($this->connection);
        $this->participantThree_prog1->insert($this->connection);
        //
        $this->worksheetOne_pt1->mission->worksheetForm->form->insert($this->connection);
        $this->worksheetOne_pt1->mission->worksheetForm->insert($this->connection);
        
        $this->worksheetOne_pt1->mission->insert($this->connection);
        $this->worksheetTwo_pt2->mission->insert($this->connection);
        
        $this->worksheetOne_pt1->insert($this->connection);
        $this->worksheetTwo_pt2->insert($this->connection);
        $this->worksheetThree_pt3->insert($this->connection);
        //
        $this->mentoringRequestOne_pt1->consultationSetup->insert($this->connection);
        $this->mentoringRequestTwo_pt2->consultationSetup->insert($this->connection);
        
        $this->mentoringRequestOne_pt1->mentor->insert($this->connection);
        $this->mentoringRequestTwo_pt2->mentor->insert($this->connection);
        
        $this->mentoringRequestOne_pt1->insert($this->connection);
        $this->mentoringRequestTwo_pt2->insert($this->connection);
        $this->mentoringRequestThree_pt3->insert($this->connection);
        //
        $this->metricReportOne_pt1->metricAssignment->insert($this->connection);
        $this->metricReportTwo_pt2->metricAssignment->insert($this->connection);
        $this->metricReportThree_pt3->metricAssignment->insert($this->connection);
        
        $this->metricReportOne_pt1->insert($this->connection);
        $this->metricReportTwo_pt2->insert($this->connection);
        $this->metricReportThree_pt3->insert($this->connection);
        //
        $this->coordinatorTaskOne->insert($this->connection);
        $this->coordinatorTaskTwo->insert($this->connection);
        $this->consultantTaskTwoA->insert($this->connection);
        
        $this->taskReportTwo->insert($this->connection);
        //
        $this->get($this->viewUri, $this->personnel->token);
echo $this->viewUri;
$this->seeJsonContains(['print']);
    }
    public function test_view_200()
    {
$this->disableExceptionHandling();
        $this->view();
        $this->seeStatusCode(200);
        
        $response = [
            'newApplicantCount' => '3',
            'uncommentedWorksheetCount' => '3',
            'unconcludedMentoringRequestCount' => '3',
            'unreviewedMetricReportCount' => '3',
            'incompleteTask' => '3',
        ];
        $this->seeJsonContains($response);
    }
    //
    public function test_view_newApplicant_excludeConculdedRegistrant()
    {
        $this->registrantTwo_prog2->status = RegistrationStatus::REJECTED;
        $this->view();
        $this->seeStatusCode(200);
        
        $response = [ 'newApplicantCount' => '2' ];
        $this->seeJsonContains($response);
    }
    public function test_view_newApplicant_excludeRegistrantOfUnmangedProgram_inactiveCoordinator()
    {
        $this->coordinatorTwo->active = false;
        
        $this->view();
        $this->seeStatusCode(200);
        
        $response = [ 'newApplicantCount' => '2' ];
        $this->seeJsonContains($response);
    }
    public function test_view_newApplicant_excludeRegistrantOfUnmangedProgram_noCoordinatorAssignment()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        $this->registrantTwo_prog2->program = $otherProgram;
        
        $this->view();
        $this->seeStatusCode(200);
        
        $response = [ 'newApplicantCount' => '2' ];
        $this->seeJsonContains($response);
    }
    //
    public function test_view_uncommentedWorksheet_excludeCommentedWorksheet()
    {
        $consultant = new RecordOfConsultant($this->coordinatorOne->program, $this->personnel, 'other');
        $consultant->insert($this->connection);
        $comment = new RecordOfComment($this->worksheetTwo_pt2, 'other');
        $consultantComment = new RecordOfConsultantComment($consultant, $comment);
        $consultantComment->insert($this->connection);
        
        $this->view();
        $this->seeStatusCode(200);
        
        $response = [ 'uncommentedWorksheetCount' => '2' ];
        $this->seeJsonContains($response);
    }
    public function test_view_uncommentedWorksheet_excludeRemovedWorksheet()
    {
        $this->worksheetThree_pt3->removed = true;
        
        $this->view();
        $this->seeStatusCode(200);
        
        $response = [ 'uncommentedWorksheetCount' => '2' ];
        $this->seeJsonContains($response);
    }
    public function test_view_uncommentedWorksheet_excludeWorksheetFromInactiveParticipant()
    {
        $this->worksheetOne_pt1->participant->active = false;
        
        $this->view();
        $this->seeStatusCode(200);
        
        $response = [ 'uncommentedWorksheetCount' => '2' ];
        $this->seeJsonContains($response);
    }
    public function test_view_uncommentedWorksheet_excludeWorksheetInUnmanagedProgram_inactiveCoordinator()
    {
        $this->coordinatorTwo->active = false;
        
        $this->view();
        $this->seeStatusCode(200);
        
        $response = [ 'uncommentedWorksheetCount' => '2' ];
        $this->seeJsonContains($response);
    }
    public function test_view_uncommentedWorksheet_excludeWorksheetInUnmanagedProgram_noCoordinatorAssignment()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        $this->worksheetTwo_pt2->participant->program = $otherProgram;
        
        $this->view();
        $this->seeStatusCode(200);
        
        $response = [ 'uncommentedWorksheetCount' => '2' ];
        $this->seeJsonContains($response);
    }
    //
    public function test_view_unconcludedMentoringRequest_excludeConcludedMentoringRequest()
    {
        $this->mentoringRequestOne_pt1->requestStatus = MentoringRequestStatus::ACCEPTED_BY_PARTICIPANT;
        
        $this->view();
        $this->seeStatusCode(200);
        
        $response = [ 'unconcludedMentoringRequestCount' => '2' ];
        $this->seeJsonContains($response);
    }
    public function test_view_unconcludedMentoringRequest_excludeMentoringRequestFromInactiveParticipant()
    {
        $this->mentoringRequestOne_pt1->participant->active = false;
        
        $this->view();
        $this->seeStatusCode(200);
        
        $response = [ 'unconcludedMentoringRequestCount' => '2' ];
        $this->seeJsonContains($response);
    }
    public function test_view_unconcludedMentoringRequest_excludeMentoringRequestInUnmanagedProgram_inactiveCoordinator()
    {
        $this->coordinatorTwo->active = false;
        
        $this->view();
        $this->seeStatusCode(200);
        
        $response = [ 'unconcludedMentoringRequestCount' => '2' ];
        $this->seeJsonContains($response);
    }
    public function test_view_unconcludedMentoringRequest_excludeMentoringRequestInUnmanagedProgram_noCoordinatorAssignment()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        $this->mentoringRequestTwo_pt2->participant->program = $otherProgram;
        
        $this->view();
        $this->seeStatusCode(200);
        
        $response = [ 'unconcludedMentoringRequestCount' => '2' ];
        $this->seeJsonContains($response);
    }
    //
    public function test_view_unreviewedMetric_excludeReviewedMetricAssignmentReport()
    {
        $this->metricReportOne_pt1->approved = false;
        
        $this->view();
        $this->seeStatusCode(200);
        
        $response = [ 'unreviewedMetricReportCount' => '2' ];
        $this->seeJsonContains($response);
    }
    public function test_view_unreviewedMetric_excludeRemovedMetricAssignmentReport()
    {
        $this->metricReportOne_pt1->removed = true;
        
        $this->view();
        $this->seeStatusCode(200);
        
        $response = [ 'unreviewedMetricReportCount' => '2' ];
        $this->seeJsonContains($response);
    }
    public function test_view_unreviewedMetric_excludeMetricAssignmentReportFromInactiveParticipant()
    {
        $this->metricReportOne_pt1->metricAssignment->participant->active = false;
        
        $this->view();
        $this->seeStatusCode(200);
        
        $response = [ 'unreviewedMetricReportCount' => '2' ];
        $this->seeJsonContains($response);
    }
    public function test_view_unreviewedMetric_excludeMetricAssignmentReportInUnmanagedProgram_inactiveCoordinator()
    {
        $this->coordinatorTwo->active = false;
        
        $this->view();
        $this->seeStatusCode(200);
        
        $response = [ 'unreviewedMetricReportCount' => '2' ];
        $this->seeJsonContains($response);
    }
    public function test_view_unreviewedMetric_excludeMetricAssignmentReportInUnmanagedProgram_noCoordinatorAssignment()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        $this->metricReportThree_pt3->metricAssignment->participant->program = $otherProgram;
        
        $this->view();
        $this->seeStatusCode(200);
        
        $response = [ 'unreviewedMetricReportCount' => '2' ];
        $this->seeJsonContains($response);
    }
    //
    public function test_view_task_excludeCancelledTask()
    {
        $this->coordinatorTaskOne->task->cancelled = true;
        $this->view();
        $this->seeJsonContains(['incompleteTask' => '2']);
    }
    public function test_view_task_excludeTaskInNonCoordinatedProgram()
    {
        $this->coordinatorOne->active = false;
        $this->view();
        $this->seeJsonContains(['incompleteTask' => '2']);
    }
    public function test_view_task_excludeCompletedTask()
    {
        $this->taskReportTwo->reviewStatus = TaskReportReviewStatus::APPROVED;
        $this->view();
        $this->seeJsonContains(['incompleteTask' => '2']);
    }
}
