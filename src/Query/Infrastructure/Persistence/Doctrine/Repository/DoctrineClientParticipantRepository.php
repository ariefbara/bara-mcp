<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Service\Client\AsProgramParticipant\ClientParticipantRepository;
use Query\Application\Service\Firm\Client\ProgramParticipationRepository;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineClientParticipantRepository extends EntityRepository implements ProgramParticipationRepository, ClientParticipantRepository
{

    public function all(string $firmId, string $clientId, int $page, int $pageSize, ?bool $activeStatus)
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
        ];

        $qb = $this->createQueryBuilder('programParticipation');
        $qb->select('programParticipation')
                ->leftJoin('programParticipation.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('client.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params);
        
        if (isset($activeStatus)) {
            $qb->leftJoin("programParticipation.participant", "participant")
                    ->andWhere($qb->expr()->eq("participant.active", ":activeStatus"))
                    ->setParameter("activeStatus", $activeStatus);
        }

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(string $firmId, string $clientId, string $programParticipationId): ClientParticipant
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programParticipationId' => $programParticipationId,
        ];

        $qb = $this->createQueryBuilder('programParticipation');
        $qb->select('programParticipation')
                ->andWhere($qb->expr()->eq('programParticipation.id', ':programParticipationId'))
                ->leftJoin('programParticipation.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('client.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: program participation not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aClientProgramParticipationCorrespondWithProgram(string $clientId, string $programId): ClientParticipant
    {
        $params = [
            "clientId" => $clientId,
            "programId" => $programId,
        ];
        
        $qb = $this->createQueryBuilder("clientProgramParticipation");
        $qb->select("clientProgramParticipation")
                ->leftJoin("clientProgramParticipation.client", "client")
                ->andWhere($qb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("clientProgramParticipation.participant", "programParticipation")
                ->leftJoin("programParticipation.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: client program participation not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aClientParticipant(string $firmId, string $clientId, string $participantId): ClientParticipant
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'participantId' => $participantId,
        ];
        
        $qb = $this->createQueryBuilder('clientParticipant');
        $qb->select('clientParticipant')
                ->andWhere($qb->expr()->eq('clientParticipant.id', ':participantId'))
                ->leftJoin('clientParticipant.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('client.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: client participant not found');
        }
    }

    public function aClientParticipantCorrespondWithProgram(
            string $firmId, string $clientId, string $programId): ClientParticipant
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
            "programId" => $programId,
        ];
        
        $qb = $this->createQueryBuilder("clientParticipant");
        $qb->select("clientParticipant")
                ->leftJoin("clientParticipant.client", "client")
                ->andWhere($qb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("client.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->leftJoin("clientParticipant.participant", "participant")
                ->leftJoin("participant.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: client participant not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
