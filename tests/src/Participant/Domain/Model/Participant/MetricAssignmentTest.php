<?php

namespace Participant\Domain\Model\Participant;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\ {
    DependencyModel\Firm\Team,
    Model\Participant,
    Model\Participant\MetricAssignment\AssignmentField,
    Model\Participant\MetricAssignment\MetricAssignmentReport,
    Service\MetricAssignmentReportDataProvider
};
use Resources\Domain\ValueObject\DateInterval;
use Tests\TestBase;

class MetricAssignmentTest extends TestBase
{

    protected $metricAssignment;
    protected $participant;
    protected $startEndDate;
    protected $assignmentField;
    protected $team;
    protected $metricAssignmentReportId = "metricAssignmentReportId";
    protected $observationTime;
    protected $metricAssignmentReportDataProvider;
    protected $metricAssignmentReport;

    protected function setUp(): void
    {
        parent::setUp();
        $this->metricAssignment = new TestableMetricAssignment();

        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->metricAssignment->participant = $this->participant;

        $this->startEndDate = $this->buildMockOfClass(DateInterval::class);
        $this->metricAssignment->startEndDate = $this->startEndDate;

        $this->assignmentField = $this->buildMockOfClass(AssignmentField::class);
        $this->metricAssignment->assignmentFields = new ArrayCollection();
        $this->metricAssignment->assignmentFields->add($this->assignmentField);

        $this->team = $this->buildMockOfClass(Team::class);

        $this->observationTime = new DateTimeImmutable();
        $this->metricAssignmentReportDataProvider = $this->buildMockOfClass(MetricAssignmentReportDataProvider::class);
        
        $this->metricAssignmentReport = $this->buildMockOfClass(MetricAssignmentReport::class);
    }

    public function test_belongsToTeam_returnParticiantBelongsToTeamResult()
    {
        $this->participant->expects($this->once())
                ->method("belongsToTeam")
                ->with($this->team);
        $this->metricAssignment->belongsToTeam($this->team);
    }

    protected function executeSubmitReport()
    {
        $this->startEndDate->expects($this->any())
                ->method("contain")
                ->willReturn(true);
        return $this->metricAssignment->submitReport(
                        $this->metricAssignmentReportId, $this->observationTime, $this->metricAssignmentReportDataProvider);
    }
    public function test_submitReport_returnMetricAssignmentReport()
    {
        $this->assertInstanceOf(MetricAssignmentReport::class, $this->executeSubmitReport());
    }
    public function test_submitReport_askAllAssignmentFieldsToSetValueInMetricAssignmentReport()
    {
        $this->assignmentField->expects($this->once())
                ->method("setValueIn")
                ->with($this->anything(), $this->metricAssignmentReportDataProvider);
        $this->executeSubmitReport();
    }
    public function test_submitReport_containRemovedAssignmentField_preventRemovedAssignmentFieldFromSettingValue()
    {
        $this->assignmentField->expects($this->once())
                ->method("isRemoved")
                ->willReturn(true);
        $this->assignmentField->expects($this->never())
                ->method("setValueIn");
        $this->executeSubmitReport();
    }
    public function test_submitReport_observeTimeOutsideStarEndDate_forbidden()
    {
        $this->startEndDate->expects($this->once())
                ->method("contain")
                ->with($this->observationTime)
                ->willReturn(false);
        $operation = function (){
            $this->executeSubmitReport();
        };
        $errorDetail = "forbidden: observe time out of bound";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeSetActiveAssignmentFieldValuesTo()
    {
        $this->metricAssignment->setActiveAssignmentFieldValuesTo($this->metricAssignmentReport, $this->metricAssignmentReportDataProvider);
    }
    public function test_setActiveAssignmentFieldValuesTo_executeAllAssignmentFieldSetValueInMethod()
    {
        $this->assignmentField->expects($this->once())
                ->method("setValueIn")
                ->with($this->metricAssignmentReport, $this->metricAssignmentReportDataProvider);
        $this->executeSetActiveAssignmentFieldValuesTo();
    }
    
    public function test_isAllAttachedFileInfoInDataProviderManageable_returnParticipantOwnAllAttachedFileInfoResult()
    {
        $this->participant->expects($this->once())
                ->method("ownAllAttachedFileInfo")
                ->with($this->metricAssignmentReportDataProvider);
        $this->metricAssignment->isParticipantOwnAllAttachedFileInfo($this->metricAssignmentReportDataProvider);
    }

}

class TestableMetricAssignment extends MetricAssignment
{

    public $participant;
    public $id;
    public $startEndDate;
    public $assignmentFields;

    function __construct()
    {
        parent::__construct();
    }

}
