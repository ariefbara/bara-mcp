<?php

namespace Personnel\Domain\Model\Firm\Personnel;

use Personnel\Domain\Model\Firm\Personnel;
use Shared\Domain\Model\FileInfo;

class PersonnelFileInfo
{

    /**
     *
     * @var Personnel
     */
    protected $personnel;

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

    function __construct(Personnel $personnel, string $id, FileInfo $fileInfo)
    {
        $this->personnel = $personnel;
        $this->id = $id;
        $this->fileInfo = $fileInfo;
        $this->removed = false;
    }

}
