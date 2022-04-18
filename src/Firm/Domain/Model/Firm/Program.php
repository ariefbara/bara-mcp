<?php

namespace Firm\Domain\Model\Firm;

use Config\EventList;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Firm\Application\Service\Manager\ManageableByFirm;
use Firm\Domain\Model\AssetBelongsToFirm;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Program\ActivityType;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\Consultant;
use Firm\Domain\Model\Firm\Program\Coordinator;
use Firm\Domain\Model\Firm\Program\EvaluationPlan;
use Firm\Domain\Model\Firm\Program\EvaluationPlanData;
use Firm\Domain\Model\Firm\Program\Metric;
use Firm\Domain\Model\Firm\Program\MetricData;
use Firm\Domain\Model\Firm\Program\Mission;
use Firm\Domain\Model\Firm\Program\MissionData;
use Firm\Domain\Model\Firm\Program\Participant;
use Firm\Domain\Model\Firm\Program\ProgramsProfileForm;
use Firm\Domain\Model\Firm\Program\Registrant;
use Firm\Domain\Model\Firm\Program\RegistrationPhase;
use Firm\Domain\Model\Firm\Program\Sponsor;
use Firm\Domain\Model\Firm\Program\SponsorData;
use Firm\Domain\Service\ActivityTypeDataProvider;
use Query\Domain\Model\Firm\ParticipantTypes;
use Resources\Domain\Event\CommonEvent;
use Resources\Domain\Model\EntityContainEvents;
use Resources\Exception\RegularException;
use Resources\Uuid;
use Resources\ValidationRule;
use Resources\ValidationService;
use SharedContext\Domain\ValueObject\ProgramSnapshot;
use SharedContext\Domain\ValueObject\ProgramType;

class Program extends EntityContainEvents implements AssetBelongsToFirm, ManageableByFirm
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
     * @var int|null
     */
    protected $price;
    
    /**
     * 
     * @var bool
     */
    protected $autoAccept;

    /**
     *
     * @var ParticipantTypes
     */
    protected $participantTypes;
    
    /**
     * 
     * @var ProgramType
     */
    protected $programType;

    /**
     *
     * @var bool
     */
    protected $published = false;

    /**
     * 
     * @var bool
     */
    protected $strictMissionOrder;
    
    
    /**
     *
     * @var bool
     */
    protected $removed = false;
    /**
     * 
     * @var FirmFileInfo|null
     */
    protected $illustration;

    /**
     *
     * @var ArrayCollection
     */
    protected $coordinators;

    /**
     *
     * @var ArrayCollection
     */
    protected $consultants;

    /**
     *
     * @var ArrayCollection
     */
    protected $participants;

    /**
     *
     * @var ArrayCollection
     */
    protected $registrants;

    /**
     * 
     * @var ArrayCollection
     */
    protected $assignedProfileForms;
    
    /**
     *
     * @var ArrayCollection
     */
    protected $registrationPhases;

    protected function setName(string $name)
    {
        $errorDetail = 'bad request: program name is required';
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($name, $errorDetail);
        $this->name = $name;
    }

    protected function setDescription(?string $description)
    {
        $this->description = $description;
    }
    
    protected function setIllustration(?FirmFileInfo $illustration): void
    {
        if (isset($illustration)) {
            $illustration->assertUsableInFirm($this->firm);
        }
        $this->illustration = $illustration;
    }
    protected function setPrice(?int $price): void
    {
        $this->price = $price;
    }
    protected function setAutoAccept(?bool $autoAccept): void
    {
        $this->autoAccept = $autoAccept;
    }

    function __construct(Firm $firm, $id, ProgramData $programData)
    {
        $this->firm = $firm;
        $this->id = $id;
        $this->setName($programData->getName());
        $this->setDescription($programData->getDescription());
        $this->setIllustration($programData->getIllustration());
        $this->participantTypes = new ParticipantTypes($programData->getParticipantTypes());
        $this->programType = new ProgramType($programData->getProgramType());
        $this->strictMissionOrder = $programData->isStrictMissionOrder();
        $this->published = false;
        $this->removed = false;
        $this->setPrice($programData->getPrice());
        $this->setAutoAccept($programData->getAutoAccept());
    }

    public function belongsToFirm(Firm $firm): bool
    {
        return $this->firm === $firm;
    }

    public function update(ProgramData $programData): void
    {
        $this->setName($programData->getName());
        $this->setDescription($programData->getDescription());
        $this->setIllustration($programData->getIllustration());
        $this->participantTypes = new ParticipantTypes($programData->getParticipantTypes());
        $this->programType = new ProgramType($programData->getProgramType());
        $this->strictMissionOrder = $programData->isStrictMissionOrder();
        $this->setPrice($programData->getPrice());
        $this->setAutoAccept($programData->getAutoAccept());
    }

    public function publish(): void
    {
        $this->published = true;
    }

    public function remove(): void
    {
        if ($this->published) {
            $errorDetail = "forbidden: can only remove unpublished program";
            throw RegularException::forbidden($errorDetail);
        }
        $this->removed = true;
    }

    public function assignPersonnelAsConsultant(Personnel $personnel): string
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('personnel', $personnel));
        $consultant = $this->consultants->matching($criteria)->first();

        if (!empty($consultant)) {
            $consultant->enable();
        } else {
            $id = Uuid::generateUuid4();
            $consultant = new Consultant($this, $id, $personnel);
            $this->consultants->add($consultant);
        }
        return $consultant->getId();
    }

    public function assignPersonnelAsCoordinator(Personnel $personnel): string
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('personnel', $personnel));
        $coordinator = $this->coordinators->matching($criteria)->first();

        if (!empty($coordinator)) {
            $coordinator->enable();
        } else {
            $id = Uuid::generateUuid4();
            $coordinator = new Coordinator($this, $id, $personnel);
            $this->coordinators->add($coordinator);
        }
        return $coordinator->getId();
    }

    public function acceptRegistrant(string $registrantId): void
    {
        $registrant = $this->findRegistrantOrDie($registrantId);
        $registrant->accept();

        if (!empty($participant = $this->findParticipantCorrespondToRegistrant($registrant))) {
            $participant->reenroll();
        } else {
            $participantId = Uuid::generateUuid4();
            $participant = $registrant->createParticipant($participantId);
            $this->participants->add($participant);
        }

        $event = new CommonEvent(EventList::REGISTRANT_ACCEPTED, $participant->getId());
        $this->recordEvent($event);
    }

    public function addMetric(string $metricId, MetricData $metricData): Metric
    {
        return new Metric($this, $metricId, $metricData);
    }

    public function createEvaluationPlan(
            string $evaluationPlanId, EvaluationPlanData $evaluationPlanData, FeedbackForm $reportForm,
            ?Mission $mission): EvaluationPlan
    {
        return new EvaluationPlan($this, $evaluationPlanId, $evaluationPlanData, $reportForm, $mission);
    }

    protected function findRegistrantOrDie(string $registrantId): Registrant
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('id', $registrantId));
        $registrant = $this->registrants->matching($criteria)->first();
        if (empty($registrant)) {
            $errorDetail = 'not found: registrant not found';
            throw RegularException::notFound($errorDetail);
        }
        return $registrant;
    }

    protected function findParticipantCorrespondToRegistrant(Registrant $registrant): ?Participant
    {
        $p = function (Participant $participant) use ($registrant) {
            return $participant->correspondWithRegistrant($registrant);
        };
        $participant = $this->participants->filter($p)->first();
        return empty($participant) ? null : $participant;
    }

    public function createActivityType(string $activityTypeId, ActivityTypeDataProvider $activityTypeDataProvider): ActivityType
    {
        return new ActivityType($this, $activityTypeId, $activityTypeDataProvider);
    }

    public function assignProfileForm(ProfileForm $profileForm): string
    {
        $p = function (ProgramsProfileForm $assignedProfileForm) use ($profileForm) {
            return $assignedProfileForm->correspondWithProfileForm($profileForm);
        };
        if (!empty($assignedProfileForm = $this->assignedProfileForms->filter($p)->first())) {
            $assignedProfileForm->enable();
        } else {
            $id = Uuid::generateUuid4();
            $assignedProfileForm = new ProgramsProfileForm($this, $id, $profileForm);
            $this->assignedProfileForms->add($assignedProfileForm);
        }
        return $assignedProfileForm->getId();
    }

    public function createRootMission(string $missionId, WorksheetForm $worksheetForm, MissionData $missionData): Mission
    {
        return new Mission($this, $missionId, $worksheetForm, $missionData);
    }

    public function isManageableByFirm(Firm $firm): bool
    {
        return $this->firm === $firm;
    }
    
    public function inviteAllActiveParticipantsToMeeting(Meeting $meeting): void
    {
        $p = function (Participant $participant) {
            return $participant->isActive();
        };
        foreach ($this->participants->filter($p)->getIterator() as $participant) {
            $participant->inviteToMeeting($meeting);
        }
    }
    
    public function createSponsor(string $sponsorId, SponsorData $sponsorData): Sponsor
    {
        return new Sponsor($this, $sponsorId, $sponsorData);
    }
    
    public function assertFileUsable(FirmFileInfo $firmFileInfo): void
    {
        $firmFileInfo->assertUsableInFirm($this->firm);
    }
    
    public function assertCanAcceptParticipantOfType(string $type): void
    {
        if (!$this->participantTypes->hasType($type)) {
            throw RegularException::forbidden("forbidden: {$type} in not accomodate in this program");
        }
    }
    
    public function assertUsableInFirm(Firm $firm): void
    {
        if (!$this->published) {
            throw RegularException::forbidden('forbidden: unable to use unpublished program');
        }
        if ($this->removed) {
            throw RegularException::forbidden('forbidden: unable to use removed program');
        }
        if ($this->firm !== $firm) {
            throw RegularException::forbidden('forbidden: can only owned program');
        }
    }
    
    public function assertAccessibleInFirm(Firm $firm): void
    {
        if ($this->firm !== $firm) {
            throw RegularException::forbidden('forbidden: can only access entity belongs to firm');
        }
    }
    
    public function executeTask(IProgramTask $task, $payload): void
    {
        if ($this->removed) {
            throw RegularException::forbidden('unable to access removed program');
        }
        $task->execute($this, $payload);
    }
    
    public function receiveApplication(IProgramApplicant $applicant): void
    {
        if (!$this->published) {
            throw RegularException::forbidden('unpublished program unable to accept application');
        }
        $p = function(RegistrationPhase $registrationPhase){
            return $registrationPhase->isOpen();
        };
        if (empty($this->registrationPhases->filter($p)->count())) {
            throw RegularException::forbidden('no open registration phase');
        }
        if (!$this->participantTypes->hasType($applicant->getUserType())) {
            throw RegularException::forbidden("applicant of type {$applicant->getUserType()} is unsupported");
        }
        $applicant->assertBelongsInFirm($this->firm);
        $id = Uuid::generateUuid4();
        if ($this->autoAccept && empty($this->price)) {
            $participant = new Participant($this, $id);
            $this->participants->add($participant);
            $this->aggregateEventsFromBranch($participant);
        } else {
            $registrant = new Registrant($this, new ProgramSnapshot($this->price, $this->autoAccept), $id);
            $this->registrants->add($registrant);
            $this->aggregateEventsFromBranch($registrant);
        }
    }
    
}
