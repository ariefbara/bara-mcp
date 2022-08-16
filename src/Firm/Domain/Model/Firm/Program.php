<?php

namespace Firm\Domain\Model\Firm;

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
use Firm\Domain\Model\Firm\Program\Sponsor;
use Firm\Domain\Model\Firm\Program\SponsorData;
use Firm\Domain\Service\ActivityTypeDataProvider;
use Query\Domain\Model\Firm\ParticipantTypes;
use Resources\Domain\Model\EntityContainEvents;
use Resources\Exception\RegularException;
use Resources\Uuid;
use Resources\ValidationRule;
use Resources\ValidationService;
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

    protected function assertPublished(): void
    {
        if (!$this->published) {
            throw RegularException::forbidden('program not published yet');
        }
    }

    public function executeTask(IProgramTask $task, $payload): void
    {
        if ($this->removed) {
            throw RegularException::forbidden('unable to access removed program');
        }
        $task->execute($this, $payload);
    }

    public function receiveApplication(string $participantId, string $applicantType): Participant
    {
        $this->assertPublished();
        if (!$this->participantTypes->hasType($applicantType)) {
            throw RegularException::forbidden('applicant type not supported');
        }
        return new Participant($this, $participantId, $this->autoAccept, $this->price);
    }

    public function createActiveParticipant(string $participantId, string $participantType): Participant
    {
        $this->assertPublished();
        if (!$this->participantTypes->hasType($participantType)) {
            throw RegularException::forbidden('participant type not supported');
        }
        return new Participant($this, $participantId, $programAutoAccept = true, $programPrice = null);
    }

}
