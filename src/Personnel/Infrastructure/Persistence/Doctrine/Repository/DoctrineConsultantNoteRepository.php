<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultantNote;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\ConsultantNoteRepository;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineConsultantNoteRepository extends DoctrineEntityRepository implements ConsultantNoteRepository
{

    public function add(ConsultantNote $consultantNote): void
    {
        $this->persist($consultantNote);
    }

    public function ofId(string $id): ConsultantNote
    {
        $params = [
            'id' => $id,
        ];
        $qb = $this->createQueryBuilder('consultantNote');
        $qb->select('consultantNote')
                ->andWhere($qb->expr()->eq('consultantNote.id', ':id'))
                ->leftJoin('consultantNote.note', 'note')
                ->andWhere($qb->expr()->eq('note.removed', 'false'))
                ->setMaxResults(1)
                ->setParameters($params);
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('consultant note not found');
        }
    }

}
