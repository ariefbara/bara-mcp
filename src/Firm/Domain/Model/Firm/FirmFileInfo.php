<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\Firm;
use SharedContext\Domain\ {
    Model\SharedEntity\FileInfo,
    Model\SharedEntity\FileInfoData,
    Service\CanBeSavedInStorage
};

class FirmFileInfo implements CanBeSavedInStorage
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

    public function __construct(Firm $firm, string $id, FileInfoData $fileInfoData)
    {
        $this->firm = $firm;
        $this->id = $id;
        $this->fileInfo = new FileInfo($id, $fileInfoData);
        $this->removed = false;
    }

    public function getFullyQualifiedFileName(): string
    {
        return $this->fileInfo->getFullyQualifiedFileName();
    }

}
