<?php

namespace Query\Domain\Task\InFirm;

use Query\Domain\Model\Firm;
use Query\Domain\Model\Firm\ITaskInFirmExecutableByManager;
use Query\Domain\Model\Firm\Team\Member;
use Query\Domain\Task\Dependency\Firm\Team\MemberRepository;

class ShowTeamMemberTask implements ITaskInFirmExecutableByManager
{

    /**
     * 
     * @var MemberRepository
     */
    protected $memberRepository;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var Member|null
     */
    public $result;

    public function __construct(MemberRepository $memberRepository, string $id)
    {
        $this->memberRepository = $memberRepository;
        $this->id = $id;
    }

    public function executeTaskInFirm(Firm $firm): void
    {
        $this->result = $this->memberRepository->aMemberOfTeamInFirm($firm->getId(), $this->id);
    }

}
