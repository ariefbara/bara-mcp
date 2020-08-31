<?php

namespace Client\Domain\Model\Client;

use SharedContext\Domain\ {
    Model\SharedEntity\FileInfo,
    Model\SharedEntity\FileInfoData,
    Service\UploadFile
};
use Client\Domain\Model\Client;

class ClientFileInfo
{

    /**
     *
     * @var Client
     */
    protected $client;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var FileInfo
     */
    protected $fileInfo;

    /**
     *
     * @var bool
     */
    protected $removed = false;

    function __construct(Client $client, string $id, FileInfoData $fileInfoData)
    {
        $this->client = $client;
        $this->id = $id;
        $this->fileInfo = new FileInfo($id, $fileInfoData);
        $this->removed = false;
    }
    
    public function uploadContents(UploadFile $uploadFile, $contents): void
    {
        $uploadFile->execute($this->fileInfo, $contents);
    }

}
