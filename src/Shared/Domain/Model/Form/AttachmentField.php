<?php

namespace Shared\Domain\Model\Form;

use Resources\{
    Domain\ValueObject\IntegerRange,
    Exception\RegularException
};
use Shared\Domain\Model\{
    Form,
    FormRecord,
    FormRecordData
};

class AttachmentField
{

    /**
     *
     * @var Form
     */
    protected $form;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var FieldVO
     */
    protected $field;

    /**
     *
     * @var IntegerRange
     */
    protected $minMaxValue;

    /**
     *
     * @var IntegerRange
     */
    protected $minMaxSize;

    /**
     *
     * @var bool
     */
    protected $removed;

    function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->field->getName();
    }

    public function getPosition(): ?string
    {
        return $this->field->getPosition();
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
        ;
    }

    public function setAttachmentFieldRecordOf(FormRecord $formRecord, FormRecordData $formRecordData): void
    {
        $fileInfoList = $formRecordData->getAttachedFileInfoListOf($this->id);

        $this->field->assertMandatoryRequirementSatisfied($fileInfoList);
        $this->assertAttachedFileCountIsWithinRange($fileInfoList);

        $formRecord->setAttachmentFieldRecord($this, $fileInfoList);
    }

    protected function assertAttachedFileCountIsWithinRange(array $fileInfoList): void
    {
        $fileInfoCount = empty(count($fileInfoList)) ? null : count($fileInfoList);
        if (!empty($fileInfoCount)) {
            if (!$this->minMaxValue->contain($fileInfoCount)) {
                $errorDetail = "bad request: attached file count for {$this->field->getName()} is out of range";
                throw RegularException::badRequest($errorDetail);
            }
        }
    }

}
