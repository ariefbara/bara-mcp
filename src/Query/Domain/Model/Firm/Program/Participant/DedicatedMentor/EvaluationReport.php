<?php

namespace Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;

use DateTimeImmutable;
use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\Program\EvaluationPlan;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;
use Query\Domain\Model\Shared\Form\AttachmentField;
use Query\Domain\Model\Shared\Form\IContainFieldRecord;
use Query\Domain\Model\Shared\Form\IntegerField;
use Query\Domain\Model\Shared\Form\MultiSelectField;
use Query\Domain\Model\Shared\Form\SingleSelectField;
use Query\Domain\Model\Shared\Form\StringField;
use Query\Domain\Model\Shared\Form\TextAreaField;
use Query\Domain\Model\Shared\FormRecord;
use Query\Domain\Model\Shared\FormRecord\AttachmentFieldRecord;
use Query\Domain\Model\Shared\FormRecord\IntegerFieldRecord;
use Query\Domain\Model\Shared\FormRecord\MultiSelectFieldRecord;
use Query\Domain\Model\Shared\FormRecord\SingleSelectFieldRecord;
use Query\Domain\Model\Shared\FormRecord\StringFieldRecord;
use Query\Domain\Model\Shared\FormRecord\TextAreaFieldRecord;

class EvaluationReport implements IContainFieldRecord
{

    /**
     * 
     * @var DedicatedMentor
     */
    protected $dedicatedMentor;

    /**
     * 
     * @var EvaluationPlan
     */
    protected $evaluationPlan;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var DateTimeImmutable
     */
    protected $modifiedTime;

    /**
     * 
     * @var bool
     */
    protected $cancelled;

    /**
     * 
     * @var FormRecord
     */
    protected $formRecord;

    public function getDedicatedMentor(): DedicatedMentor
    {
        return $this->dedicatedMentor;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEvaluationPlan(): EvaluationPlan
    {
        return $this->evaluationPlan;
    }

    public function getFormRecord(): FormRecord
    {
        return $this->formRecord;
    }

    public function getModifiedTimeString(): string
    {
        return $this->modifiedTime->format('Y-m-d H:i:s');
    }

    public function isCancelled(): bool
    {
        return $this->cancelled;
    }

    protected function __construct()
    {
        
    }

    function getSubmitTimeString(): ?string
    {
        return $this->formRecord->getSubmitTimeString();
    }

    /**
     * 
     * @return IntegerFieldRecord[]
     */
    function getUnremovedIntegerFieldRecords()
    {
        return $this->formRecord->getUnremovedIntegerFieldRecords();
    }

    /**
     * 
     * @return StringFieldRecord[]
     */
    function getUnremovedStringFieldRecords()
    {
        return $this->formRecord->getUnremovedStringFieldRecords();
    }

    /**
     * 
     * @return TextAreaFieldRecord[]
     */
    function getUnremovedTextAreaFieldRecords()
    {
        return $this->formRecord->getUnremovedTextAreaFieldRecords();
    }

    /**
     * 
     * @return SingleSelectFieldRecord[]
     */
    function getUnremovedSingleSelectFieldRecords()
    {
        return $this->formRecord->getUnremovedSingleSelectFieldRecords();
    }

    /**
     * 
     * @return MultiSelectFieldRecord[]
     */
    function getUnremovedMultiSelectFieldRecords()
    {
        return $this->formRecord->getUnremovedMultiSelectFieldRecords();
    }

    /**
     * 
     * @return AttachmentFieldRecord[]
     */
    function getUnremovedAttachmentFieldRecords()
    {
        return $this->formRecord->getUnremovedAttachmentFieldRecords();
    }
    
    public function evaluationPlanEquals(EvaluationPlan $evaluationPlan): bool
    {
        return $this->evaluationPlan === $evaluationPlan;
    }
        
    public function getListOfClientPlusTeamName(): array
    {
        return $this->dedicatedMentor->getListOfClientPlusTeamName();
    }
    
    public function getMentorName(): string
    {
        return $this->dedicatedMentor->getMentorName();
    }
    
    public function getMentorPlusTeamName(): string
    {
        return $this->dedicatedMentor->getMentorPlusTeamName();
    }

    public function correspondWithClient(Client $client): bool
    {
        return $this->dedicatedMentor->correspondWithClient($client);
    }
    
    public function getFileInfoListOfAttachmentFieldRecordCorrespondWith(AttachmentField $attachmentField): ?string
    {
        return $this->formRecord->getFileInfoListOfAttachmentFieldRecordCorrespondWith($attachmentField);
    }

    public function getIntegerFieldRecordValueCorrespondWith(IntegerField $integerField): ?int
    {
        return $this->formRecord->getIntegerFieldRecordValueCorrespondWith($integerField);
    }

    public function getListOfMultiSelectFieldRecordSelectedOptionNameCorrespondWith(MultiSelectField $multiSelectField): ?string
    {
        return $this->formRecord->getListOfMultiSelectFieldRecordSelectedOptionNameCorrespondWith($multiSelectField);
    }

    public function getSingleSelectFieldRecordSelectedOptionNameCorrespondWith(SingleSelectField $singleSelectField): ?string
    {
        return $this->formRecord->getSingleSelectFieldRecordSelectedOptionNameCorrespondWith($singleSelectField);
    }

    public function getStringFieldRecordValueCorrespondWith(StringField $stringField): ?string
    {
        return $this->formRecord->getStringFieldRecordValueCorrespondWith($stringField);
    }

    public function getTextAreaFieldRecordValueCorrespondWith(TextAreaField $textAreaField): ?string
    {
        return $this->formRecord->getTextAreaFieldRecordValueCorrespondWith($textAreaField);
    }
    
    public function getParticipantName(): string
    {
        return $this->dedicatedMentor->getParticipantName();
    }
    
    public function getParticipant(): Participant
    {
        return $this->dedicatedMentor->getParticipant();
    }
    
    public function correspondWithParticipant(Participant $participant): bool
    {
        return $this->dedicatedMentor->correspondWithParticipant($participant);
    }

}
