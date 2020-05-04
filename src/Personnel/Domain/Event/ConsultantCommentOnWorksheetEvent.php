<?php

namespace Personnel\Domain\Event;

use Client\Application\Listener\ConsultantCommentOnWorsheetEventInterface;

class ConsultantCommentOnWorksheetEvent implements ConsultantCommentOnWorsheetEventInterface
{

    const EVENT_NAME = "ConsultantCommentOnWorksheetEvent";

    /**
     *
     * @var string
     */
    protected $firmId;

    /**
     *
     * @var string
     */
    protected $personnelId;

    /**
     *
     * @var string
     */
    protected $consultantId;

    /**
     *
     * @var string
     */
    protected $consultantCommentId;

    /**
     *
     * @var string
     */
    protected $messageForParticipant;

    function getFirmId(): string
    {
        return $this->firmId;
    }

    function getPersonnelId(): string
    {
        return $this->personnelId;
    }

    function getConsultantId(): string
    {
        return $this->consultantId;
    }

    function getConsultantCommentId(): string
    {
        return $this->consultantCommentId;
    }

    function getMessageForParticipant(): string
    {
        return $this->messageForParticipant;
    }

    function __construct(string $firmId, string $personnelId, string $consultantId, string $consultantCommentId,
            string $messageForParticipant)
    {
        $this->firmId = $firmId;
        $this->personnelId = $personnelId;
        $this->consultantId = $consultantId;
        $this->consultantCommentId = $consultantCommentId;
        $this->messageForParticipant = $messageForParticipant;
    }

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

}
