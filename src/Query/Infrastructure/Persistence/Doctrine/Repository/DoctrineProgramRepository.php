<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\ProgramRepository;
use Query\Application\Service\Manager\ProgramRepository as ProgramRepository2;
use Query\Application\Service\ProgramRepository as InterfaceForPublic;
use Query\Domain\Model\Firm\ParticipantTypes;
use Query\Domain\Model\Firm\Program;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineProgramRepository extends DoctrineEntityRepository implements ProgramRepository, InterfaceForPublic, ProgramRepository2
{

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

    public function all(string $firmId, int $page, int $pageSize, ?string $participantType, ?bool $publishOnly = true)
    {
        $qb = $this->createQueryBuilder('program');
        $qb->select('program')
                ->andWhere($qb->expr()->eq('program.removed', 'false'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameter('firmId', $firmId);
        
        if (isset($participantType)) {
            $qb->andWhere($qb->expr()->like("program.participantTypes.values", ":participantType"))
                    ->setParameter("participantType", "%$participantType%");
        }
        if ($publishOnly) {
            $qb->andWhere($qb->expr()->eq("program.published", "true"));
        }

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allProgramForUser(int $page, int $pageSize)
    {
        $qb = $this->createQueryBuilder('program');
        $qb->select('program')
                ->andWhere($qb->expr()->eq("program.published", "true"))
                ->andWhere($qb->expr()->like('program.participantTypes.values', ":participantType"))
                ->setParameter("participantType", "%".ParticipantTypes::USER_TYPE."%")
                ->andWhere($qb->expr()->eq('program.removed', 'false'));
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function aPublishedProgram(string $id): Program
    {
        $program = $this->findOneBy([
            'id' => $id,
            'published' => true,
            'removed' => false,
        ]);
        
        if (empty($program)) {
            throw RegularException::notFound('not found: program not found');
        }
        return $program;
    }

    public function allPublishedProgram(int $page, int $pageSize)
    {
        $qb = $this->createQueryBuilder('program');
        $qb->select('program')
                ->andWhere($qb->expr()->eq("program.published", "true"))
                ->andWhere($qb->expr()->eq('program.removed', 'false'));
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function aProgramOfId(string $id): Program
    {
        return $this->findOneByIdOrDie($id, 'program');
    }

}
