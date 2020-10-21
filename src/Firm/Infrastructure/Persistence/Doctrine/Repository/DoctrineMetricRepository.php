<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Firm\ {
    Application\Service\Firm\Program\MetricRepository,
    Domain\Model\Firm\Program\Metric
};
use Resources\ {
    Exception\RegularException,
    Uuid
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

    public function add(Metric $metric): void
    {
        $em = $this->getEntityManager();
        $em->persist($metric);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
