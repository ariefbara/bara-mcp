<?php

namespace Notification\Domain\Model\Firm;

use Notification\Domain\Model\Firm;
use SharedContext\Domain\Model\SharedEntity\FileInfo;

class FirmFileInfo
{

    /**
     *
     * @var Firm
     */
    protected $firm;

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

    public function __construct()
    {
        
    }

    public function getFullyQualifiedFileName(): string
    {
        return $this->fileInfo->getFullyQualifiedFileName();
    }

}
