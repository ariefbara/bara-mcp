<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\{
    Model\Firm\Personnel,
    Model\Firm\Program,
    Service\MetricAssignmentDataProvider
};
use Resources\Exception\RegularException;

class Coordinator
{

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Personnel
     */
    protected $personnel;

    /**
     *
     * @var bool
     */
    protected $removed;

    function getPersonnel(): Personnel
    {
        return $this->personnel;
    }

    function getId(): string
    {
        return $this->id;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    function __construct(Program $program, $id, Personnel $personnel)
    {
        $this->program = $program;
        $this->id = $id;
        $this->personnel = $personnel;
        $this->removed = false;
    }

    public function remove(): void
    {
        $this->removed = true;
    }

    public function reassign(): void
    {
        $this->removed = false;
    }

    public function assignMetricsToParticipant(
            Participant $participant, MetricAssignmentDataProvider $metricAssignmentDataCollector): void
    {
        $this->assertActive();
        $this->assertAssetBelongsProgram($participant);
        $participant->assignMetrics($metricAssignmentDataCollector);
    }

    protected function assertActive()
    {
        if ($this->removed) {
            $errorDetail = "forbidden: only active coordinator can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }
    
    protected function assertAssetBelongsProgram(AssetInProgram $asset): void
    {
        if (!$asset->belongsToProgram($this->program)) {
            $errorDetail = "forbidden: unable to manage asset of other program";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
