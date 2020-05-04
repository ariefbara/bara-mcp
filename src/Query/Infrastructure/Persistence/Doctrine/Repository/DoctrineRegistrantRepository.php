<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Firm\Application\Service\Firm\Program\ProgramCompositionId;
use Query\{
    Application\Service\Firm\Program\RegistrantRepository,
    Domain\Model\Firm\Program\Registrant
};
use Resources\{
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineRegistrantRepository extends EntityRepository implements RegistrantRepository
{

    public function all(ProgramCompositionId $programCompositionId, int $page, int $pageSize)
    {
        $parameters = [
            "programId" => $programCompositionId->getProgramId(),
            "firmId" => $programCompositionId->getFirmId(),
        ];

        $qb = $this->createQueryBuilder("registrant");
        $qb->select('registrant')
                ->leftJoin('registrant.program', 'program')
                ->andWhere($qb->expr()->eq('program.removed', "false"))
                ->andWhere($qb->expr()->eq('program.id', ":programId"))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
                ->setParameters($parameters);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(ProgramCompositionId $programCompositionId, string $registrantId): Registrant
    {
        $parameters = [
            "registrantId" => $registrantId,
            "programId" => $programCompositionId->getProgramId(),
            "firmId" => $programCompositionId->getFirmId(),
        ];

        $qb = $this->createQueryBuilder("registrant");
        $qb->select('registrant')
                ->andWhere($qb->expr()->eq('registrant.id', ":registrantId"))
                ->leftJoin('registrant.program', 'program')
                ->andWhere($qb->expr()->eq('program.removed', "false"))
                ->andWhere($qb->expr()->eq('program.id', ":programId"))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: registrant not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
