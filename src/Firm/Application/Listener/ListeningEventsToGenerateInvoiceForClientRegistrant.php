<?php

namespace Firm\Application\Listener;

use Config\EventList;
use Firm\Application\Service\ExecuteResponsiveTask;
use Firm\Domain\Event\ProgramRegistrationReceived;
use Firm\Domain\Task\Responsive\GenerateInvoiceForClientRegistrant;
use Resources\Application\Event\Event;
use Resources\Application\Event\Listener;
use Resources\Domain\Event\CommonEvent;
use SharedContext\Domain\ValueObject\RegistrationStatus;

class ListeningEventsToGenerateInvoiceForClientRegistrant implements Listener
{

    /**
     * 
     * @var ExecuteResponsiveTask
     */
    protected $executeResponsiveTaskService;

    /**
     * 
     * @var GenerateInvoiceForClientRegistrant
     */
    protected $generateInvoiceForClientRegistrant;

    /**
     * 
     * @var CommonEvent
     */
    protected $clientRegistrantAdded;

    /**
     * 
     * @var ProgramRegistrationReceived
     */
    protected $programRegistrationReceived;

    public function __construct(
            ExecuteResponsiveTask $executeResponsiveTaskService,
            GenerateInvoiceForClientRegistrant $generateInvoiceForClientRegistrant)
    {
        $this->executeResponsiveTaskService = $executeResponsiveTaskService;
        $this->generateInvoiceForClientRegistrant = $generateInvoiceForClientRegistrant;
    }

    public function handle(Event $event): void
    {
        if ($event->getName() === EventList::CLIENT_REGISTRANT_CREATED) {
            $this->clientRegistrantAdded = $event;
        } elseif ($event->getName() === EventList::PROGRAM_REGISTRATION_RECEIVED) {
            $this->programRegistrationReceived = $event;
        }
        if ($this->clientRegistrantAdded && $this->programRegistrationReceived) {
            $this->execute();
        }
    }

    public function execute(): void
    {
        $toProccessRegistrationStatus = new RegistrationStatus(RegistrationStatus::SETTLEMENT_REQUIRED);
        if ($this->programRegistrationReceived->getRegistrationStatus()->sameValueAs($toProccessRegistrationStatus)) {
            $clientRegistrantId = $this->clientRegistrantAdded->getId();
            $this->executeResponsiveTaskService->execute($this->generateInvoiceForClientRegistrant, $clientRegistrantId);
        }
    }

}
