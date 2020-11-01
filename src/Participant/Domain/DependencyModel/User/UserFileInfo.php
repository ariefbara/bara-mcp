<?php

namespace Participant\Domain\DependencyModel\User;

use Participant\Domain\SharedModel\FileInfo;

class UserFileInfo
{

    /**
     *
     * @var string
     */
    protected $userId;

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
    
    protected function __construct()
    {
        
    }
    
    public function belongsToUser(string $userId): bool
    {
        return $this->userId === $userId;
    }

}
