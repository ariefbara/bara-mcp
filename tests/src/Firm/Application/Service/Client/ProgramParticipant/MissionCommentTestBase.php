<?php

namespace Tests\src\Firm\Application\Service\Client\ProgramParticipant;

use Firm\Application\Service\Firm\Program\Mission\MissionCommentRepository;
use Firm\Domain\Model\Firm\Program\Mission\MissionComment;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;
use PHPUnit\Framework\MockObject\MockObject;

class MissionCommentTestBase extends ProgramParticipantTestBase
{

    /**
     * 
     * @var MockObject
     */
    protected $missionCommentRepository;

    /**
     * 
     * @var MockObject
     */
    protected $missionComment;
    protected $missionCommentId = 'mission-comment-id';
    protected $missionCommentNextId = 'next-mission-comment-id';
    protected $missionCommentData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->missionComment = $this->buildMockOfClass(MissionComment::class);
        $this->missionCommentRepository = $this->buildMockOfInterface(MissionCommentRepository::class);
        $this->missionCommentRepository->expects($this->any())
                ->method('ofId')
                ->with($this->missionCommentId)
                ->willReturn($this->missionComment);
        $this->missionCommentRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->missionCommentNextId);

        $this->missionCommentData = $this->buildMockOfClass(MissionCommentData::class);
    }

}
