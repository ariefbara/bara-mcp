<?php

namespace App\Http\Controllers\Personnel;

use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport;
use Query\Domain\Task\Dependency\PaginationFilter;
use Query\Domain\Task\Personnel\ViewUnreviewedMetricReportListInCoordinatedProgram;
use Query\Domain\Task\Personnel\ViewUnreviewedMetricReportListInCoordinatedProgramPayload;

class UnreviewedMetricReportListInCoordinatedProgramController extends PersonnelBaseController
{

    public function viewAll()
    {
        $metricAssignmentReportRepository = $this->em->getRepository(MetricAssignmentReport::class);
        $task = new ViewUnreviewedMetricReportListInCoordinatedProgram($metricAssignmentReportRepository);

        $paginationFilter = new PaginationFilter($this->getPage(), $this->getPageSize());
        $payload = new ViewUnreviewedMetricReportListInCoordinatedProgramPayload($paginationFilter);

        $this->executePersonalQueryTask($task, $payload);
        
        return $this->listQueryResponse($payload->result);
    }

}
