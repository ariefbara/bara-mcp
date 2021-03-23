<?php

namespace Firm\Domain\Model\Firm;

use Config\EventList;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Firm\Application\Service\Manager\ManageableByFirm;
use Firm\Domain\Model\AssetBelongsToFirm;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Program\ActivityType;
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
use Firm\Domain\Service\ActivityTypeDataProvider;
use Query\Domain\Model\Firm\ParticipantTypes;
use Resources\Domain\Event\CommonEvent;
use Resources\Domain\Model\EntityContainEvents;
use Resources\Exception\RegularException;
use Resources\Uuid;
use Resources\ValidationRule;
use Resources\ValidationService;

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
     * @var ParticipantTypes
     */
    protected $participantTypes;

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

    function __construct(Firm $firm, $id, ProgramData $programData)
    {
        $this->firm = $firm;
        $this->id = $id;
        $this->setName($programData->getName());
        $this->setDescription($programData->getDescription());
        $this->participantTypes = new ParticipantTypes($programData->getParticipantTypes());
        $this->strictMissionOrder = $programData->isStrictMissionOrder();
        $this->published = false;
        $this->removed = false;
    }

    public function belongsToFirm(Firm $firm): bool
    {
        return $this->firm === $firm;
    }

    public function update(ProgramData $programData): void
    {
        $this->setName($programData->getName());
        $this->setDescription($programData->getDescription());
        $this->participantTypes = new ParticipantTypes($programData->getParticipantTypes());
        $this->strictMissionOrder = $programData->isStrictMissionOrder();
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
            string $evaluationPlanId, EvaluationPlanData $evaluationPlanData, FeedbackForm $reportForm): EvaluationPlan
    {
        return new EvaluationPlan($this, $evaluationPlanId, $evaluationPlanData, $reportForm);
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

}
