<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use App\Http\Controllers\Client\ClientBaseController;
use Client\{
    Application\Service\Client\ProgramParticipation\ParticipantCommentRemove,
    Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId,
    Domain\Model\Client\ProgramParticipation\ParticipantComment
};

class ParticipantCommentController extends ClientBaseController
{

    public function remove($programParticipationId, $participantCommentId)
    {
        $service = $this->buildRemoveService();
        $programParticipationCompositionId = new ProgramParticipationCompositionId(
                $this->clientId(), $programParticipationId);
        $service->execute($programParticipationCompositionId, $participantCommentId);
        
        return $this->commandOkResponse();
    }

    protected function buildRemoveService()
    {
        $participantCommentRepository = $this->em->getRepository(ParticipantComment::class);
        return new ParticipantCommentRemove($participantCommentRepository);
    }

}
