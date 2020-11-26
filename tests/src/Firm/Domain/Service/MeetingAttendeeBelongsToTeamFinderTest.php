<?php

namespace Firm\Domain\Service;

use Tests\TestBase;

class MeetingAttendeeBelongsToTeamFinderTest extends TestBase
{
    protected $meetingAttendeeRepository;
    protected $finder;
    protected $team, $teamId = "teamId", $meetingId = "meetingId";


    protected function setUp(): void
    {
        parent::setUp();
        $this->meetingAttendeeRepository = $this->buildMockOfInterface(MeetingAttendeeRepository::class);
        $this->finder = new MeetingAttendeeBelongsToTeamFinder($this->meetingAttendeeRepository);
        
        $this->team = $this->buildMockOfClass(\Firm\Domain\Model\Firm\Team::class);
        $this->team->expects($this->any())->method("getId")->willReturn($this->teamId);
    }
    
    public function test_execute_returnAttendeeFromRepository()
    {
        $this->meetingAttendeeRepository->expects($this->once())
                ->method("anAttendeeBelongsToTeamCorrespondWithMeeting")
                ->with($this->teamId, $this->meetingId);
        $this->finder->execute($this->team, $this->meetingId);
    }
}
