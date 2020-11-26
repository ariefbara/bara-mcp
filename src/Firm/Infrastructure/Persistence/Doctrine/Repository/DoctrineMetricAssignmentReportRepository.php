<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Firm\{
    Application\Service\Coordinator\MetricAssignmentReportRepository,
    Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport
};
use Resources\Exception\RegularException;

class DoctrineMetricAssignmentReportRepository extends EntityRepository implements MetricAssignmentReportRepository
{

    public function ofId(string $metricAssignmentReportId): MetricAssignmentReport
    {
        $metricAssignmentReport = $this->findOneBy(["id" => $metricAssignmentReportId]);
        if (empty($metricAssignmentReport)) {
            $errorDetail = "not found: metric assignment report not found";
            throw RegularException::notFound($errorDetail);
        }
        return $metricAssignmentReport;
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
