<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Firm\Application\Service\Manager\ProfileFormRepository;
use Firm\Domain\Model\Firm\ProfileForm;
use Resources\Exception\RegularException;
use Resources\Uuid;

class DoctrineProfileFormRepository extends EntityRepository implements ProfileFormRepository
{
    
    public function add(ProfileForm $profileForm): void
    {
        $em = $this->getEntityManager();
        $em->persist($profileForm);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function ofId(string $profileFormId): ProfileForm
    {
        $profileForm = $this->findOneBy(["id" => $profileFormId]);
        if (empty($profileForm)) {
            $errorDetail = "not found: profile form not found";
            throw RegularException::notFound($errorDetail);
        }
        return $profileForm;
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
