<?php

namespace Firm\Domain\Service;

use Firm\Domain\Model\Firm\Program\ActivityType\ActivityParticipantData;

class ActivityTypeDataProvider
{

    /**
     *
     * @var FeedbackFormRepository
     */
    protected $feedbackFormRepository;

    /**
     *
     * @var string|null
     */
    protected $name;

    /**
     *
     * @var string|null
     */
    protected $description;

    /**
     *
     * @var array
     */
    protected $activityParticipantDataCollection;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function __construct(FeedbackFormRepository $feedbackFormRepository, ?string $name, ?string $description)
    {
        $this->feedbackFormRepository = $feedbackFormRepository;
        $this->name = $name;
        $this->description = $description;
        $this->activityParticipantDataCollection = [];
    }

    public function addActivityParticipantData(
            ?string $participantType, ?bool $canInitiate, ?bool $canAttend, ?string $feedbackFormId): void
    {
        $reportForm = empty($feedbackFormId)? null: $this->feedbackFormRepository->aFeedbackFormOfId($feedbackFormId);
        $this->activityParticipantDataCollection[$participantType] = new ActivityParticipantData(
                $participantType, $canInitiate, $canAttend, $reportForm);
    }

    /**
     * 
     * @return ActivityParticipantData
     */
    public function iterateActivityParticipantData(): array
    {
        return $this->activityParticipantDataCollection;
    }

}
