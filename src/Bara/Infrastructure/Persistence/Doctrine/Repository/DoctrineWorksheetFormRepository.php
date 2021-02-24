<?php

namespace Bara\Infrastructure\Persistence\Doctrine\Repository;

use Bara\Application\Service\WorksheetFormRepository;
use Bara\Domain\Model\WorksheetForm;
use Doctrine\ORM\EntityRepository;
use Resources\Exception\RegularException;
use Resources\Uuid;

class DoctrineWorksheetFormRepository extends EntityRepository implements WorksheetFormRepository
{
    
    public function add(WorksheetForm $worksheetForm): void
    {
        $em = $this->getEntityManager();
        $em->persist($worksheetForm);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function ofId(string $worksheetFormId): WorksheetForm
    {
        $worksheetForm = $this->findOneBy(['id' => $worksheetFormId]);
        if (empty($worksheetForm)) {
            throw RegularException::notFound('not found: worksheet form not found');
        }
        return $worksheetForm;
    }

}
