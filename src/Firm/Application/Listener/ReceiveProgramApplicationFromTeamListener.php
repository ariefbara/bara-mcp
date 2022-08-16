<?php

namespace Firm\Application\Listener;

use Firm\Application\Service\FirmRepository;
use Firm\Domain\Task\InFirm\AcceptProgramApplicationFromTeam;
use Firm\Domain\Task\InFirm\AcceptProgramApplicationFromTeamPayload;
use Resources\Application\Event\Event;
use Resources\Application\Event\Listener;
use Team\Domain\Event\TeamHasAppliedToProgram;

class ReceiveProgramApplicationFromTeamListener implements Listener
{

    /**
     * 
     * @var FirmRepository
     */
    protected $firmRepository;

    /**
     * 
     * @var AcceptProgramApplicationFromTeam
     */
    protected $task;

    public function __construct(FirmRepository $firmRepository, AcceptProgramApplicationFromTeam $task)
    {
        $this->firmRepository = $firmRepository;
        $this->task = $task;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    public function execute(TeamHasAppliedToProgram $event): void
    {
        $firm = $this->firmRepository->ofId($event->getFirmId());
        $payload = new AcceptProgramApplicationFromTeamPayload($event->getTeamId(), $event->getProgramId());
        $this->task->execute($firm, $payload);
        $this->firmRepository->update();
    }

}
