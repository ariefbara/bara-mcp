<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Firm\Application\Listener\ProgramRepository as ProgramRepository3;
use Firm\Application\Service\Firm\ProgramRepository;
use Firm\Application\Service\Manager\ProgramRepository as InterfaceForManager;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Task\Dependency\Firm\ProgramRepository as ProgramRepository2;
use Resources\Exception\RegularException;
use Resources\Uuid;

class DoctrineProgramRepository extends EntityRepository implements ProgramRepository, InterfaceForManager, ProgramRepository2,
        ProgramRepository3
{

    public function add(Program $program): void
    {
        $em = $this->getEntityManager();
        $em->persist($program);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function ofId(string $firmId, string $programId): Program
    {
        $qb = $this->createQueryBuilder('program');
        $qb->select('program')
                ->andWhere($qb->expr()->eq('program.removed', 'false'))
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameter('programId', $programId)
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameter('firmId', $firmId)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: program not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function aProgramOfId(string $programId): Program
    {
        $program = $this->findOneBy([
            "id" => $programId,
            "removed" => false,
        ]);
        if (empty($program)) {
            $errorDetail = "not found: program not found";
            throw RegularException::notFound($errorDetail);
        }
        return $program;
    }

}
