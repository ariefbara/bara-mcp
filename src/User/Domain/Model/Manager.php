<?php

namespace User\Domain\Model;

use Resources\ {
    Domain\ValueObject\Password,
    Exception\RegularException
};
use SharedContext\Domain\Model\SharedEntity\FileInfoData;
use User\Domain\Model\Manager\ManagerFileInfo;

class Manager
{

    /**
     *
     * @var string
     */
    protected $firmId;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var string
     */
    protected $email;

    /**
     *
     * @var Password
     */
    protected $password;

    /**
     *
     * @var string||null
     */
    protected $phone;

    /**
     *
     * @var bool
     */
    protected $removed = false;
    
    protected function __construct()
    {
        
    }
    
    public function saveFileInfo(string $managerFileInfoId, FileInfoData $fileInfoData): ManagerFileInfo
    {
        if ($this->removed) {
            $errorDetail = "forbidden: only active manage can make this request";
            throw RegularException::forbidden($errorDetail);
        }
        return new ManagerFileInfo($this, $managerFileInfoId, $fileInfoData);
    }
}
