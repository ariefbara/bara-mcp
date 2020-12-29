<?php

namespace Notification\Domain\Model\Firm\Team;

use Notification\Domain\ {
    Model\Firm\Team,
    SharedModel\CanSendPersonalizeMail,
    SharedModel\ContainNotification
};
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class TeamProgramParticipationTest extends TestBase
{

    protected $teamProgramParticipation;
    protected $team;
    protected $programParticipation;
    protected $mailGenerator;
    protected $mailMessage, $modifiedMailMessage;
    protected $excludedMember;
    protected $notification;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teamProgramParticipation = new TestableTeamProgramParticipation();

        $this->team = $this->buildMockOfClass(Team::class);
        $this->teamProgramParticipation->team = $this->team;

        $this->mailGenerator = $this->buildMockOfInterface(CanSendPersonalizeMail::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
        $this->modifiedMailMessage = $this->buildMockOfClass(MailMessage::class);
        $this->excludedMember = $this->buildMockOfClass(Member::class);

        $this->notification = $this->buildMockOfInterface(ContainNotification::class);
    }

    public function test_getTeamFullName_returnTeamGetFullNameResult()
    {
        $this->team->expects($this->once())
                ->method("getName");
        $this->teamProgramParticipation->getTeamName();
    }

    protected function executeRegisterTeamAsMailRecipient()
    {
        $this->mailMessage->expects($this->any())
                ->method("prependUrlPath")
                ->willReturn($this->modifiedMailMessage);

        $this->teamProgramParticipation->registerTeamMembersAsMailRecipient(
                $this->mailGenerator, $this->mailMessage, $this->excludedMember);
    }

    public function test_registerTeamAsMailRecipient_prependTeamProgramParticipationToMailMessage()
    {
        $this->mailMessage->expects($this->once())
                ->method("prependUrlPath")
                ->with("/participation/{$this->teamProgramParticipation->id}");
        $this->executeRegisterTeamAsMailRecipient();
    }

    public function test_registerTeamAsMailRecipient_registerTeamAsMailRecipient()
    {
        $this->team->expects($this->once())
                ->method("registerAllActiveMembersAsMailRecipient")
                ->with($this->mailGenerator, $this->identicalTo($this->modifiedMailMessage), $this->excludedMember);
        $this->executeRegisterTeamAsMailRecipient();
    }

    public function test_registerTeamAsNotificationRecipient_addTeamAsNotificationRecipient()
    {
        $this->team->expects($this->once())
                ->method("registerAllActiveMembersAsNotificationRecipient")
                ->with($this->notification, $this->excludedMember);
        $this->teamProgramParticipation->registerTeamMembersAsNotificationRecipient($this->notification, $this->excludedMember);
    }

}

class TestableTeamProgramParticipation extends TeamProgramParticipation
{

    public $team;
    public $id;
    public $programParticipation;

    function __construct()
    {
        parent::__construct();
    }

}
