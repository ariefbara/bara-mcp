<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Firm\Application\Service\Manager\ClientCVFormRepository;
use Firm\Domain\Model\Firm\ClientCVForm;
use Resources\Exception\RegularException;

class DoctrineClientCVFormRepository extends EntityRepository implements ClientCVFormRepository
{
    
    public function ofId(string $clientCVFormId): ClientCVForm
    {
        $clientCVForm = $this->findOneBy([
            "id" => $clientCVFormId,
        ]);
        if (empty($clientCVForm)) {
            $errorDetail = "not found: client cv form not found";
            throw RegularException::notFound($errorDetail);
        }
        return $clientCVForm;
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
