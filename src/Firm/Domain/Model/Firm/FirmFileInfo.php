<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\Firm;
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\SharedEntity\FileInfo;
use SharedContext\Domain\Model\SharedEntity\FileInfoData;
use SharedContext\Domain\Service\CanBeSavedInStorage;

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
    
    public function assertUsableInFirm(Firm $firm): void
    {
        if ($this->firm !== $firm) {
            throw RegularException::forbidden("forbidden: unable to use file, either doesn't exist or doesn't belongs to your firm");
        }
    }

}
