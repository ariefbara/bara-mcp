<?php

namespace Query\Domain\Task\InFirm;

use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Task\Dependency\Firm\ClientRepository;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportRepository;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportSummaryFilter;
use Query\Domain\Task\InFirm\GenerateFirmEvaluationReportTranscriptTask;
use Tests\src\Query\Domain\Task\InFirm\TaskInFirmTestBase;

class GenerateFirmEvaluationReportTranscriptTaskTest extends TaskInFirmTestBase
{

    protected $clientRepository, $client;
    protected $evaluationReportRepository, $evaluationReport;
    protected $evaluationReportSummaryFilter, $clientIdList = ['client-one-id', 'client-two-id'];
    protected $clientEvaluationReportTranscriptResult;
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);

        $this->evaluationReport = $this->buildMockOfClass(EvaluationReport::class);
        $this->evaluationReportRepository = $this->buildMockOfInterface(EvaluationReportRepository::class);

        $this->evaluationReportSummaryFilter = $this->buildMockOfClass(EvaluationReportSummaryFilter::class);
        $this->evaluationReportSummaryFilter->expects($this->any())->method('getClientIdList')->willReturn($this->clientIdList);
        
        $this->clientEvaluationReportTranscriptResult = $this->buildMockOfClass(ClientEvaluationReportTranscriptResult::class);

        $this->task = new TestableGenerateFirmEvaluationReportTranscriptTask(
                $this->clientRepository, $this->evaluationReportRepository, $this->evaluationReportSummaryFilter,
                $this->clientEvaluationReportTranscriptResult);
    }
    
    protected function executeTaskInFirm()
    {
        $this->clientRepository->expects($this->any())
                ->method('allNonPaginatedActiveClientInFirm')
                ->with($this->firm, $this->clientIdList)
                ->willReturn([$this->client]);
        $this->evaluationReportRepository->expects($this->any())
                ->method('allNonPaginatedEvaluationReportsInFirm')
                ->with($this->firm, $this->evaluationReportSummaryFilter)
                ->willReturn([$this->evaluationReport]);
        $this->task->executeTaskInFirm($this->firm);
    }
    public function test_executeTaskInFirm_addTranscripTableForEachClient()
    {
        $this->clientEvaluationReportTranscriptResult->expects($this->once())
                ->method('addTranscriptTableForClient')
                ->with($this->client);
        $this->executeTaskInFirm();
    }
    public function test_executeTaskInFirm_includeAllEvaluationReportFromRepository()
    {
        $this->clientEvaluationReportTranscriptResult->expects($this->once())
                ->method('includeEvaluationReport')
                ->with($this->evaluationReport);
        $this->executeTaskInFirm();
    }

}

class TestableGenerateFirmEvaluationReportTranscriptTask extends GenerateFirmEvaluationReportTranscriptTask
{

    public $clientRepository;
    public $evaluationReportRepository;
    public $evaluationReportSummaryFilter;
    public $clientEvaluationReportTranscriptResult;

}
