<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Participant\ {
    Application\Service\Participant\MetricAssignmentRepository,
    Domain\Model\Participant\MetricAssignment
};
use Resources\Exception\RegularException;

class DoctrineMetricAssignmentRepository extends EntityRepository implements MetricAssignmentRepository
{
    
    public function ofId(string $metricAssignmentId): MetricAssignment
    {
        $metricAssignment = $this->findOneBy(["id" => $metricAssignmentId]);
        if (empty($metricAssignment)) {
            $errorDetail = "not found: metric assignment not found";
            throw RegularException::notFound($errorDetail);
        }
        return $metricAssignment;
    }

}
