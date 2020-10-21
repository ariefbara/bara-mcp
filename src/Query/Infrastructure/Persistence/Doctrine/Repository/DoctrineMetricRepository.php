<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\Program\MetricRepository,
    Domain\Model\Firm\Program\Metric
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineMetricRepository extends EntityRepository implements MetricRepository
{
    
    public function aMetricInProgram(string $programId, string $metricId): Metric
    {
        $params = [
            "programId" => $programId,
            "metricId" => $metricId,
        ];
        
        $qb = $this->createQueryBuilder("metric");
        $qb->select("metric")
                ->andWhere($qb->expr()->eq("metric.id", ":metricId"))
                ->leftJoin("metric.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: metric not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allMetricsInProgram(string $programId, int $page, int $pageSize)
    {
        $params = [
            "programId" => $programId,
        ];
        
        $qb = $this->createQueryBuilder("metric");
        $qb->select("metric")
                ->leftJoin("metric.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
