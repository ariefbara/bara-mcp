<?php

namespace Resources\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Resources\Exception\RegularException;
use Resources\Uuid;

class DoctrineEntityRepository extends EntityRepository
{

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }
    
    protected function persist($entity): void
    {
        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();
    }
    
    protected function findOneByIdOrDie(string $id, string $entityName)
    {
        $entity = $this->findOneBy([
            "id" => $id,
        ]);
        if (empty($entity)) {
            $errorDetail = "not found: $entityName not found";
            throw RegularException::notFound($errorDetail);
        }
        return $entity;
    }

}
