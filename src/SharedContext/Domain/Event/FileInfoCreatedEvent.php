<?php

namespace SharedContext\Domain\Event;

use Resources\Application\Event\Event;

class FileInfoCreatedEvent implements Event
{

    const EVENT_NAME = 'file-info-created';

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

    protected string $bucketName;
    protected string $objectName;

    public function getBucketName(): string
    {
        return $this->bucketName;
    }

    public function getObjectName(): string
    {
        return $this->objectName;
    }

    public function __construct(string $bucketName, string $objectName)
    {
        $this->bucketName = $bucketName;
        $this->objectName = $objectName;
    }

}
