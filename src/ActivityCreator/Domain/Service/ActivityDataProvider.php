<?php

namespace ActivityCreator\Domain\Service;

use ActivityCreator\Domain\DependencyModel\Firm\ {
    Manager,
    Personnel\Consultant,
    Personnel\Coordinator,
    Program\Participant
};
use DateTimeImmutable;
use SplObjectStorage;

class ActivityDataProvider
{

    /**
     *
     * @var ManagerRepository
     */
    protected $managerRepository;

    /**
     *
     * @var CoordinatorRepository
     */
    protected $coordinatorRepository;

    /**
     *
     * @var ConsultantRepository
     */
    protected $consultantRepository;

    /**
     *
     * @var ParticipantRepository
     */
    protected $participantRepository;

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
     * @var DateTimeImmutable|null
     */
    protected $startTime;

    /**
     *
     * @var DateTimeImmutable|null
     */
    protected $endTime;

    /**
     *
     * @var string|null
     */
    protected $location;

    /**
     *
     * @var string|null
     */
    protected $note;

    /**
     *
     * @var SplObjectStorage
     */
    protected $invitedManagerList;

    /**
     *
     * @var SplObjectStorage
     */
    protected $invitedCoordinatorList;

    /**
     *
     * @var SplObjectStorage
     */
    protected $invitedConsultantList;

    /**
     *
     * @var SplObjectStorage
     */
    protected $invitedParticipantList;

    function getName(): ?string
    {
        return $this->name;
    }

    function getDescription(): ?string
    {
        return $this->description;
    }

    function getStartTime(): ?DateTimeImmutable
    {
        return $this->startTime;
    }

    function getEndTime(): ?DateTimeImmutable
    {
        return $this->endTime;
    }

    function getLocation(): ?string
    {
        return $this->location;
    }

    function getNote(): ?string
    {
        return $this->note;
    }

    function __construct(
            ManagerRepository $managerRepository, CoordinatorRepository $coordinatorRepository,
            ConsultantRepository $consultantRepository, ParticipantRepository $participantRepository, ?string $name,
            ?string $description, ?DateTimeImmutable $startTime, ?DateTimeImmutable $endTime, ?string $location,
            ?string $note)
    {
        $this->managerRepository = $managerRepository;
        $this->coordinatorRepository = $coordinatorRepository;
        $this->consultantRepository = $consultantRepository;
        $this->participantRepository = $participantRepository;
        $this->name = $name;
        $this->description = $description;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->location = $location;
        $this->note = $note;

        $this->invitedManagerList = new SplObjectStorage;
        $this->invitedCoordinatorList = new SplObjectStorage;
        $this->invitedConsultantList = new SplObjectStorage;
        $this->invitedParticipantList = new SplObjectStorage;
    }

    public function addManagerInvitation(string $managerId): void
    {
        $manager = $this->managerRepository->ofId($managerId);
        $this->invitedManagerList->attach($manager);
    }

    public function addCoordinatorInvitation(string $coordinatorId): void
    {
        $coordinator = $this->coordinatorRepository->ofId($coordinatorId);
        $this->invitedCoordinatorList->attach($coordinator);
    }

    public function addConsultantInvitation(string $consultantId): void
    {
        $consultant = $this->consultantRepository->ofId($consultantId);
        $this->invitedConsultantList->attach($consultant);
    }

    public function addParticipantInvitation(string $participantId): void
    {
        $participant = $this->participantRepository->ofId($participantId);
        $this->invitedParticipantList->attach($participant);
    }

    /**
     * 
     * @return Manager[]
     */
    public function iterateInvitedManagerList(): array
    {
        $managerList = [];
        foreach ($this->invitedManagerList as $manager) {
            $managerList[] = $manager;
        }
        return $managerList;
    }

    /**
     * 
     * @return Coordinator[]
     */
    public function iterateInvitedCoordinatorList(): array
    {
        $coordinatorList = [];
        foreach ($this->invitedCoordinatorList as $coordinator){
            $coordinatorList[] = $coordinator;
        }
        return $coordinatorList;
    }

    /**
     * 
     * @return Consultant[]
     */
    public function iterateInvitedConsultantList(): array
    {
        $consultantList = [];
        foreach ($this->invitedConsultantList as $consultant) {
            $consultantList[] = $consultant;
        }
        return $consultantList;
    }

    /**
     * 
     * @return Participant[]
     */
    public function iterateInvitedParticipantList(): array
    {
        $participantList = [];
        foreach ($this->invitedParticipantList as $participant) {
            $participantList[] = $participant;
        }
        return $participantList;
    }

    public function containManager(Manager $manager): bool
    {
        return $this->invitedManagerList->contains($manager);
    }

    public function containCoordinator(Coordinator $coordinator): bool
    {
        return $this->invitedCoordinatorList->contains($coordinator);
    }

    public function containConsultant(Consultant $consultant): bool
    {
        return $this->invitedConsultantList->contains($consultant);
    }

    public function containParticipant(Participant $participant): bool
    {
        return $this->invitedParticipantList->contains($participant);
    }

}
