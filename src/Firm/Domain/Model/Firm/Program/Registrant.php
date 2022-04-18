<?php

namespace Firm\Domain\Model\Firm\Program;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Firm\Domain\Event\ProgramRegistrationReceived;
use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Team;
use Firm\Domain\Model\User;
use Resources\DateTimeImmutableBuilder;
use Resources\Domain\Model\EntityContainEvents;
use Resources\Exception\RegularException;
use SharedContext\Domain\ValueObject\ProgramSnapshot;
use SharedContext\Domain\ValueObject\RegistrationStatus;

class Registrant extends EntityContainEvents
{

    /**
     *
     * @var Program
     */
    protected $program;
    
    /**
     * 
     * @var ProgramSnapshot
     */
    protected $programSnapshot;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     * 
     * @var RegistrationStatus
     */
    protected $status;
    
    /**
     *
     * @var DateTimeImmutable
     */
    protected $registeredTime;

    /**
     *
     * @var UserRegistrant|null
     */
    protected $userRegistrant;

    /**
     *
     * @var ClientRegistrant|null
     */
    protected $clientRegistrant;

    /**
     *
     * @var TeamRegistrant|null
     */
    protected $teamRegistrant;
    
    /**
     * 
     * @var ArrayCollection
     */
    protected $profiles;
    

    function __construct(Program $program, ProgramSnapshot $programSnapshot, string $id)
    {
        $this->program = $program;
        $this->programSnapshot = $programSnapshot;
        $this->id = $id;
        $this->status = $this->programSnapshot->generateInitialRegistrationStatus();
        $this->registeredTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $event = new ProgramRegistrationReceived($this->id, $this->status);
        $this->recordEvent($event);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function accept(): void
    {
//        $this->status = $this->status->accept();
//        $this->assertUnconcluded();
//        $this->concluded = true;
//        $this->note = 'accepted';
    }

    public function reject(): void
    {
//        $this->assertUnconcluded();
//        $this->concluded = true;
//        $this->note = 'rejected';
    }

    public function createParticipant(string $participantId): Participant
    {
        if (isset($this->userRegistrant)) {
            $participant = $this->userRegistrant->createParticipant($this->program, $participantId);
        }
        if (isset($this->clientRegistrant)) {
            $participant = $this->clientRegistrant->createParticipant($this->program, $participantId);
        }
        if (isset($this->teamRegistrant)) {
            $participant = $this->teamRegistrant->createParticipant($this->program, $participantId);
        }
        
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq("removed", false));
        foreach ($this->profiles->matching($criteria)->getIterator() as $profile) {
            $profile->transferToParticipant($participant);
        }
        
        return $participant;
    }

    public function correspondWithUser(User $user): bool
    {
        return empty($this->userRegistrant) ? false : $this->userRegistrant->userEquals($user);
    }

    public function correspondWithClient(Client $client): bool
    {
        return empty($this->clientRegistrant) ? false : $this->clientRegistrant->clientEquals($client);
    }

    public function correspondWithTeam(Team $team): bool
    {
        return isset($this->teamRegistrant) ? $this->teamRegistrant->teamEquals($team) : false;
    }

    protected function assertUnconcluded(): void
    {
        if ($this->concluded) {
            $errorDetail = "forbidden: application already concluded";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
