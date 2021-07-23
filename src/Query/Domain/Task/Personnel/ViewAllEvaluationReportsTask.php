<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\IViewAssetBelongsToPersonnelTask;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportRepository;

class ViewAllEvaluationReportsTask implements IViewAssetBelongsToPersonnelTask
{

    /**
     * 
     * @var EvaluationReportRepository
     */
    protected $evaluationReportRepository;

    /**
     * 
     * @var ViewAllEvaluationReportsPayload
     */
    protected $payload;

    public function __construct(
            EvaluationReportRepository $evaluationReportRepository, ViewAllEvaluationReportsPayload $payload)
    {
        $this->evaluationReportRepository = $evaluationReportRepository;
        $this->payload = $payload;
    }

    public function viewAssetBelongsToPersonnel(string $personnelId): array
    {
        return $this->evaluationReportRepository->allEvaluationReportsBelongsToPersonnel(
                $personnelId, $this->payload->getProgramId(), $this->payload->getPage(), $this->payload->getPageSize(),
                $this->payload->getEvaluationReportFilter());
    }

}
