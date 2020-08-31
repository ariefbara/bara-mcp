<?php

namespace Shared\Domain\Model;

use Doctrine\Common\Collections\ {
    ArrayCollection,
    Criteria
};

class Form
{

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
    protected $description = null;

    /**
     *
     * @var ArrayCollection
     */
    protected $stringFields;

    /**
     *
     * @var ArrayCollection
     */
    protected $integerFields;

    /**
     *
     * @var ArrayCollection
     */
    protected $textAreaFields;

    /**
     *
     * @var ArrayCollection
     */
    protected $attachmentFields;

    /**
     *
     * @var ArrayCollection
     */
    protected $singleSelectFields;

    /**
     *
     * @var ArrayCollection
     */
    protected $multiSelectFields;

    protected function __construct()
    {
        ;
    }

    public function setFieldRecordsOf(FormRecord $formRecord, FormRecordData $formRecordData): void
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('removed', false));
        foreach ($this->stringFields->matching($criteria)->getIterator() as $stringField) {
            $stringField->setStringFieldRecordOf($formRecord, $formRecordData);
        }
        foreach ($this->integerFields->matching($criteria)->getIterator() as $integerField) {
            $integerField->setIntegerFieldRecordOf($formRecord, $formRecordData);
        }
        foreach ($this->textAreaFields->matching($criteria)->getIterator() as $textAreaField) {
            $textAreaField->setTextAreaFieldRecordOf($formRecord, $formRecordData);
        }
        foreach ($this->singleSelectFields->matching($criteria)->getIterator() as $singleSelectField) {
            $singleSelectField->setSingleSelectFieldRecordOf($formRecord, $formRecordData);
        }
        foreach ($this->multiSelectFields->matching($criteria)->getIterator() as $multiSelectField) {
            $multiSelectField->setMultiSelectFieldRecordOf($formRecord, $formRecordData);
        }
        foreach ($this->attachmentFields->matching($criteria)->getIterator() as $attachmentField) {
            $attachmentField->setAttachmentFieldRecordOf($formRecord, $formRecordData);
        }
    }

}
