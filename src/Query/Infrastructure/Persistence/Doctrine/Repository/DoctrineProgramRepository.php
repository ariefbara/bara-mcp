<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\ProgramRepository,
    Domain\Model\Firm\ParticipantTypes,
    Domain\Model\Firm\Program,
    Domain\Service\Firm\ProgramRepository as InterfaceForDomainService
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineProgramRepository extends EntityRepository implements ProgramRepository, InterfaceForDomainService
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

    public function all(string $firmId, int $page, int $pageSize, ?string $participantType)
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

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allProgramForUser(int $page, int $pageSize)
    {
        $qb = $this->createQueryBuilder('program');
        $qb->select('program')
                ->andWhere($qb->expr()->like('program.participantTypes.values', ":participantType"))
                ->setParameter("participantType", "%".ParticipantTypes::USER_TYPE."%")
                ->andWhere($qb->expr()->eq('program.removed', 'false'));
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allProgramViewableByClient(string $firmId, int $page, int $pageSize)
    {
        $clientType = "%" . ParticipantTypes::CLIENT_TYPE . "%";
        $teamType = "%". ParticipantTypes::TEAM_TYPE . "%";
        
        $qb = $this->createQueryBuilder('program');
        $qb->select('program')
                ->andWhere($qb->expr()->eq('program.removed', 'false'))
                ->andWhere($qb->expr()->orX(
                        $qb->expr()->like('program.participantTypes.values', "'$clientType'"),
                        $qb->expr()->like('program.participantTypes.values', "'$teamType'")
                ))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameter('firmId', $firmId);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
