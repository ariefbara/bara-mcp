<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\{
    Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId,
    Application\Service\Client\ProgramParticipation\WorksheetRepository,
    Domain\Model\Client\ProgramParticipation\Worksheet
};
use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Resources\{
    Exception\RegularException,
    Uuid
};

class DoctrineWorksheetRepository extends EntityRepository implements WorksheetRepository
{

    public function add(Worksheet $worksheet): void
    {
        $em = $this->getEntityManager();
        $em->persist($worksheet);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function ofId(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $worksheetId): Worksheet
    {
        $parameters = [
            "worksheetId" => $worksheetId,
            "programParticipationId" => $programParticipationCompositionId->getProgramParticipationId(),
            "clientId" => $programParticipationCompositionId->getClientId(),
        ];
        $qb = $this->createQueryBuilder('worksheet');
        $qb->select("worksheet")
                ->andWhere($qb->expr()->eq('worksheet.removed', "false"))
                ->andWhere($qb->expr()->eq('worksheet.id', ":worksheetId"))
                ->leftJoin("worksheet.programParticipation", "programParticipation")
                ->andWhere($qb->expr()->eq('programParticipation.active', "true"))
                ->andWhere($qb->expr()->eq('programParticipation.id', ":programParticipationId"))
                ->leftJoin("programParticipation.client", "client")
                ->andWhere($qb->expr()->eq('client.id', ":clientId"))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: worksheet not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
