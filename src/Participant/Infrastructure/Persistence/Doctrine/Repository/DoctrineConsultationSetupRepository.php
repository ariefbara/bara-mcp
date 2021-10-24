<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Participant\Application\Service\Firm\Program\ConsultationSetupRepository;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\Task\Dependency\Firm\Program\ConsultationSetupRepository as InterfaceForTask;
use Participant\Domain\Model\ClientParticipant;
use Participant\Domain\Model\UserParticipant;
use Resources\Exception\RegularException;

class DoctrineConsultationSetupRepository extends EntityRepository implements ConsultationSetupRepository, InterfaceForTask
{

    public function aConsultationSetupInProgramWhereClientParticipate(
            string $firmId, string $clientId, string $programParticipationId, string $consultationSetupId): ConsultationSetup
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programParticipationId' => $programParticipationId,
            'consultationSetupId' => $consultationSetupId,
        ];

        $clientParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $clientParticipantQb->select('t_program.id')
                ->from(ClientParticipant::class, 'clientParticipant')
                ->andWhere($clientParticipantQb->expr()->eq('clientParticipant.id', ':programParticipationId'))
                ->leftJoin('clientParticipant.client', 'client')
                ->andWhere($clientParticipantQb->expr()->eq('client.id', ':clientId'))
                ->andWhere($clientParticipantQb->expr()->eq('client.firmId', ':firmId'))
                ->leftJoin('clientParticipant.participant', 'participant')
                ->leftJoin("participant.program", "t_program")
                ->setMaxResults(1);
        
        $qb = $this->createQueryBuilder('consultationSetup');
        $qb->select('consultationSetup')
                ->andWhere($qb->expr()->eq('consultationSetup.id', ':consultationSetupId'))
                ->leftJoin("consultationSetup.program", "program")
                ->andWhere($qb->expr()->in('program.id', $clientParticipantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: consultation setup not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aConsultationSetupInProgramWhereUserParticipate(
            string $userId, string $userParticipantId, string $consultationSetupId): ConsultationSetup
    {
        $params = [
            'userId' => $userId,
            'userParticipantId' => $userParticipantId,
            'consultationSetupId' => $consultationSetupId,
        ];

        $userParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $userParticipantQb->select('t_program.id')
                ->from(UserParticipant::class, 'userParticipant')
                ->andWhere($userParticipantQb->expr()->eq('userParticipant.userId', ':userId'))
                ->andWhere($userParticipantQb->expr()->eq('userParticipant.id', ':userParticipantId'))
                ->leftJoin('userParticipant.participant', 'participant')
                ->leftJoin('participant.program', 't_program')
                ->setMaxResults(1);
        
        $qb = $this->createQueryBuilder('consultationSetup');
        $qb->select('consultationSetup')
                ->andWhere($qb->expr()->eq('consultationSetup.id', ':consultationSetupId'))
                ->leftJoin("consultationSetup.program", "program")
                ->andWhere($qb->expr()->in('program.id', $userParticipantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: consultation setup not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function ofId(string $consultationSetupId): ConsultationSetup
    {
        $params = [
            "consultationSetupId" => $consultationSetupId,
        ];
        $qb = $this->createQueryBuilder("consultationSetup");
        $qb->select("consultationSetup")
                ->andWhere($qb->expr()->eq("consultationSetup.id", ":consultationSetupId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: consultation setup not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
