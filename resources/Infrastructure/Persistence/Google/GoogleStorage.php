<?php

namespace Resources\Infrastructure\Persistence\Google;

use DateTime;
use Google\Cloud\Storage\StorageClient;

class GoogleStorage
{

    /**
     * 
     * @var StorageClient
     */
    protected $storage;

    public function __construct(StorageClient $storage)
    {
        $this->storage = $storage;
    }

    public function createBucket(string $bucketName, bool $enableAutoclass = true)
    {
        $bucketOptions = [
            'autoclass' => ['enabled' => $enableAutoclass],
            'location' => 'ASIA-SOUTHEAST2'
        ];
        $this->storage->createBucket($bucketName, $bucketOptions);
    }

    public function createSignedUploadForObjectInBucket(string $bucket, string $object, string $contentType): string
    {
        return $this->storage->bucket($bucket)
                        ->object($object)
                        ->beginSignedUploadSession([
                            'version' => 'v4',
                            'contentType' => $contentType,
                        ]);
//                ->signedUrl(new \DateTime('+ 6 hours'), [
//                    'method' => 'PUT',
//                    'contentType' => 'application/octet-stream',
//                    'version' => 'v4',
//                ]);
    }

    public function removeObjectInBucket(string $bucket, string $object): void
    {
        $this->storage
                ->bucket($bucket)
                ->object($object)
                ->delete();
    }

    public function createSignedDownloadForObjectInBucket(string $bucket, string $object): string
    {
        return $this->storage
                        ->bucket($bucket)
                        ->object($object)
                        ->signedUrl(new DateTime('+2 hours'), ['version' => 'v4']);
    }

}
