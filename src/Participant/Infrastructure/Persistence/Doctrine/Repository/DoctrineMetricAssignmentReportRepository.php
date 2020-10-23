<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Participant\ {
    Application\Service\Participant\MetricAssignment\MetricAssignmentReportRepository,
    Domain\Model\Participant\MetricAssignment\MetricAssignmentReport
};
use Resources\ {
    Exception\RegularException,
    Uuid
};

class DoctrineMetricAssignmentReportRepository extends EntityRepository implements MetricAssignmentReportRepository
{
    
    public function add(MetricAssignmentReport $metricAssignmentReport): void
    {
        $em = $this->getEntityManager();
        $em->persist($metricAssignmentReport);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

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
