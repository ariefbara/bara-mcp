<?php

namespace Firm\Domain\Model\Firm\Program;

use Config\EventList;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Model\Firm\IProgramApplicant;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\Registrant\RegistrantInvoice;
use Firm\Domain\Model\Firm\Team;
use Firm\Domain\Model\User;
use Resources\DateTimeImmutableBuilder;
use Resources\Domain\Event\CommonEvent;
use Resources\Domain\Model\EntityContainEvents;
use Resources\Exception\RegularException;
use Resources\Uuid;
use SharedContext\Domain\Model\Invoice;
use SharedContext\Domain\Task\Dependency\InvoiceParameter;
use SharedContext\Domain\Task\Dependency\PaymentGateway;
use SharedContext\Domain\ValueObject\CustomerInfo;
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

    /**
     * 
     * @var RegistrantInvoice
     */
    protected $registrantInvoice;

    function __construct(Program $program, ProgramSnapshot $programSnapshot, string $id)
    {
        $this->program = $program;
        $this->programSnapshot = $programSnapshot;
        $this->id = $id;
        $this->status = $this->programSnapshot->generateInitialRegistrationStatus();
        $this->registeredTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        
        $this->recordEvent(new CommonEvent($this->status->getEmittedEvent(), $this->id));
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function accept(): void
    {
        $this->status = $this->status->accept($this->programSnapshot->getPrice());
        $event = new CommonEvent($this->status->getEmittedEvent(), $this->id);
        $this->recordEvent($event);
    }

    public function reject(): void
    {
        $this->status = $this->status->reject();
    }

    public function assertManageableInProgram(Program $program): void
    {
        if ($this->program !== $program) {
            throw RegularException::forbidden('unmanageable registrant, can only manage registrant of same program');
        }
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
        if ($this->status->isConcluded()) {
            $errorDetail = "forbidden: application already concluded";
            throw RegularException::forbidden($errorDetail);
        }
    }

    public function generateInvoice(PaymentGateway $paymentGateway, CustomerInfo $customerInfo): void
    {
        $amount = $this->programSnapshot->getPrice();
        $description = 'tagihan pendaftaran program';
        $duration = 7 * 24 * 60 * 60;
        $itemInfo = $this->programSnapshot->generateItemInfo();
        $invoiceParameter = new InvoiceParameter($this->id, $amount, $description, $duration, $customerInfo, $itemInfo);
        $paymentLink = $paymentGateway->generateInvoiceLink($invoiceParameter);
        $invoice = new Invoice($this->id, DateTimeImmutableBuilder::buildYmdHisAccuracy('+7 days'), $paymentLink);
        $this->registrantInvoice = new RegistrantInvoice($this, $this->id, $invoice);
    }

    public function settleInvoicePayment(IProgramApplicant $applicant): void
    {
        if (empty($this->registrantInvoice)) {
            throw RegularException::forbidden('no invoice found');
        }
        $this->status = $this->status->settle();
        $this->registrantInvoice->settle();
        
        $participantId = Uuid::generateUuid4();
        $participant = new Participant($this->program, $participantId);
        $applicant->addProgramParticipation($participantId, $participant);
    }
    
    public function addApplicantAsParticipant(IProgramApplicant $applicant): void
    {
        $participantId = Uuid::generateUuid4();
        $participant = new Participant($this->program, $participantId);
        $applicant->addProgramParticipation($participantId, $participant);
    }
    
    public function isUnconcludedRegistrationInProgram(Program $program): bool
    {
        return !$this->status->isConcluded() && $this->program === $program;
    }

}
