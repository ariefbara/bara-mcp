<?php

namespace User\Infrastructure\Persistence\Doctrine\Repository;

use User\{
    Application\Service\User\ProgramParticipation\ParticipantCommentRepository,
    Application\Service\User\ProgramParticipation\ProgramParticipationCompositionId,
    Domain\Model\User\ProgramParticipation\ParticipantComment
};
use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Resources\{
    Exception\RegularException,
    Uuid
};

class DoctrineParticipantCommentRepository extends EntityRepository implements ParticipantCommentRepository
{

    public function add(ParticipantComment $participantComment): void
    {
        $em = $this->getEntityManager();
        $em->persist($participantComment);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function ofId(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $participantCommentId): ParticipantComment
    {
        $parameters = [
            "participantCommentId" => $participantCommentId,
            "programParticipationId" => $programParticipationCompositionId->getProgramParticipationId(),
            "userId" => $programParticipationCompositionId->getUserId(),
        ];

        $qb = $this->createQueryBuilder('participantComment');
        $qb->select('participantComment')
                ->andWhere($qb->expr()->eq('participantComment.id', ":participantCommentId"))
                ->leftJoin('participantComment.programParticipation', 'programParticipation')
                ->andWhere($qb->expr()->eq('programParticipation.active', "true"))
                ->andWhere($qb->expr()->eq('programParticipation.id', ":programParticipationId"))
                ->leftJoin('programParticipation.user', 'user')
                ->andWhere($qb->expr()->eq('user.id', ":userId"))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: participant comment not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
