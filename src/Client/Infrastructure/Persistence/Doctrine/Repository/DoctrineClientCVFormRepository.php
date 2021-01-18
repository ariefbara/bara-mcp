<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\Application\Service\Client\ClientCVFormRepository;
use Client\Domain\DependencyModel\Firm\ClientCVForm;
use Doctrine\ORM\EntityRepository;
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

}
