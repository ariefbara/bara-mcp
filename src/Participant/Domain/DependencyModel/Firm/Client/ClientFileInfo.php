<?php

namespace Participant\Domain\DependencyModel\Firm\Client;

use Participant\Domain\{
    DependencyModel\Firm\Client,
    SharedModel\FileInfo
};

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
    protected $removed;

    function getFileInfo(): FileInfo
    {
        return $this->fileInfo;
    }

    protected function __construct()
    {
        
    }

    public function belongsToClient(Client $client): bool
    {
        return $this->client === $client;
    }

}
