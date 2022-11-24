<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultantTask;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\ConsultantTaskRepository;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineConsultantTaskRepository extends DoctrineEntityRepository implements ConsultantTaskRepository
{

    public function add(ConsultantTask $consultantTask): void
    {
        $this->persist($consultantTask);
    }

    public function ofId(string $id): ConsultantTask
    {
        return $this->findOneByIdOrDie($id, 'consultant task');
    }

}
