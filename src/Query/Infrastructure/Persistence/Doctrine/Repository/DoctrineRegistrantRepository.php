<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\Program\RegistrantRepository;
use Query\Domain\Model\Firm\Client\ClientRegistrant;
use Query\Domain\Model\Firm\Program\Registrant;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineRegistrantRepository extends EntityRepository implements RegistrantRepository
{

    public function all(string $firmId, string $programId, int $page, int $pageSize, ?bool $concludedStatus)
    {
        $parameters = [
            "firmId" => $firmId,
            "programId" => $programId,
        ];

        $qb = $this->createQueryBuilder("registrant");
        $qb->select('registrant')
                ->leftJoin('registrant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ":programId"))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
                ->addOrderBy('registrant.registeredTime', 'ASC')
                ->setParameters($parameters);

        if (isset($concludedStatus)) {
            $qb->andWhere($qb->expr()->eq("registrant.concluded", ":concludedStatus"))
                    ->setParameter("concludedStatus", $concludedStatus);
        }
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(string $firmId, string $programId, string $registrantId): Registrant
    {
        $parameters = [
            "firmId" => $firmId,
            "programId" => $programId,
            "registrantId" => $registrantId,
        ];

        $qb = $this->createQueryBuilder("registrant");
        $qb->select('registrant')
                ->andWhere($qb->expr()->eq('registrant.id', ":registrantId"))
                ->leftJoin('registrant.program', 'program')
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
