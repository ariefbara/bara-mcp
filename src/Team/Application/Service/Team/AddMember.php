<?php

namespace Team\Application\Service\Team;

use Team\Application\Service\ClientRepository;

class AddMember
{

    /**
     *
     * @var MemberRepository
     */
    protected $memberRepository;

    /**
     *
     * @var ClientRepository
     */
    protected $clientRepository;

    public function __construct(MemberRepository $memberRepository, ClientRepository $clientRepository)
    {
        $this->memberRepository = $memberRepository;
        $this->clientRepository = $clientRepository;
    }

    public function execute(
            string $firmId, string $teamId, string $clientId, string $clientIdToBeAddedAsMember, bool $anAdmin,
            ?string $memberPosition): string
    {
        $client = $this->clientRepository->ofId($firmId, $clientIdToBeAddedAsMember);
        $memberId = $this->memberRepository->aMemberCorrespondWithClient($firmId, $teamId, $clientId)
                ->addTeamMember($client, $anAdmin, $memberPosition);
        $this->memberRepository->update();
        return $memberId;
    }

}
