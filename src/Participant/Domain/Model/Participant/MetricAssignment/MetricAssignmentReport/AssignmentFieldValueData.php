<?php

namespace Participant\Domain\Model\Participant\MetricAssignment\MetricAssignmentReport;

use Participant\Domain\SharedModel\FileInfo;

class AssignmentFieldValueData
{

    /**
     *
     * @var float|null
     */
    protected $value;

    /**
     *
     * @var string|null
     */
    protected $note;

    /**
     *
     * @var FileInfo|null
     */
    protected $attachedFileInfo;

    function getValue(): ?float
    {
        return $this->value;
    }

    function getNote(): ?string
    {
        return $this->note;
    }

    function getAttachedFileInfo(): ?FileInfo
    {
        return $this->attachedFileInfo;
    }

    function __construct(?float $value, ?string $note, ?FileInfo $attachedFileInfo)
    {
        $this->value = $value;
        $this->note = $note;
        $this->attachedFileInfo = $attachedFileInfo;
    }

}
