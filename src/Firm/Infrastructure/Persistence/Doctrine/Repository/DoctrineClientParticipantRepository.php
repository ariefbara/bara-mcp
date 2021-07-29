<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Firm\Application\Service\Client\ProgramParticipant\ClientParticipantRepository;
use Firm\Domain\Model\Firm\Program\ClientParticipant;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineClientParticipantRepository extends DoctrineEntityRepository implements ClientParticipantRepository
{
    
    public function aClientParticipantCorrespondWithProgram(string $firmId, string $clientId, string $programId): ClientParticipant
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
                ->leftJoin("clientParticipant.participant", "participant")
                ->leftJoin("participant.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: program participant not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aClientParticipantBelongsToClient(string $firmId, string $clientId, string $participantId): ClientParticipant
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
            "participantId" => $participantId,
        ];
        
        $qb = $this->createQueryBuilder("clientParticipant");
        $qb->select("clientParticipant")
                ->andWhere($qb->expr()->eq("clientParticipant.id", ":participantId"))
                ->leftJoin("clientParticipant.client", "client")
                ->andWhere($qb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("client.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: program participant not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
