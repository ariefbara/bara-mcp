<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Participant\Domain\ {
    Service\LocalFileInfoRepository,
    SharedModel\FileInfo
};
use Resources\Exception\RegularException;

class DoctrineFileInfoRepository extends EntityRepository implements LocalFileInfoRepository
{
    
    public function ofId(string $fileInfoId): FileInfo
    {
        $fileInfo = $this->findOneBy(["id" => $fileInfoId]);
        if (empty($fileInfo)) {
            $errorDetail = "not found: file info not found";
            throw RegularException::notFound($errorDetail);
        }
        return $fileInfo;
    }

}
