<?php

namespace Participant\Domain\Model;

use DateTimeImmutable;
use Doctrine\Common\Collections\ {
    ArrayCollection,
    Criteria
};
use Participant\Domain\ {
    DependencyModel\Firm\Program,
    DependencyModel\Firm\Program\Consultant,
    DependencyModel\Firm\Program\ConsultationSetup,
    DependencyModel\Firm\Program\Mission,
    Model\Participant\ConsultationRequest,
    Model\Participant\ConsultationSession,
    Model\Participant\Worksheet
};
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class Participant
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
     * @var bool
     */
    protected $active = true;

    /**
     *
     * @var string||null
     */
    protected $note;

    /**
     *
     * @var ArrayCollection
     */
    protected $consultationRequests;

    /**
     *
     * @var ArrayCollection
     */
    protected $consultationSessions;

    public function quit(): void
    {
        if (!$this->active) {
            $errorDetail = 'forbidden: participant already inactive';
            throw RegularException::forbidden($errorDetail);
        }
        $this->active = false;
        $this->note = 'quit';
    }

    public function proposeConsultation(
            string $consultationRequestId, ConsultationSetup $consultationSetup, Consultant $consultant,
            DateTimeImmutable $startTime): ConsultationRequest
    {
        if (!$consultationSetup->programEquals($this->program)) {
            $errorDetail = 'forbidden: consultation setup from different program';
            throw RegularException::forbidden($errorDetail);
        }

        if (!$consultant->programEquals($this->program)) {
            $errorDetail = 'forbidden: consultant from different program';
            throw RegularException::forbidden($errorDetail);
        }

        $consultationRequest = new ConsultationRequest($this, $consultationRequestId, $consultationSetup, $consultant,
                $startTime);

        $this->assertNoProposedConsultationRequestInCollectionConflictedWith($consultationRequest);
        $this->assertNoConsultationSessioninCollectionConflictedWithConsultationRequest($consultationRequest);

        return $consultationRequest;
    }

    public function reproposeConsultationRequest(
            string $consultationRequestId, DateTimeImmutable $startTime): void
    {
        $consultationRequest = $this->getConsultationRequestOrDie($consultationRequestId);
        $consultationRequest->rePropose($startTime);

        $this->assertNoProposedConsultationRequestInCollectionConflictedWith($consultationRequest);
        $this->assertNoConsultationSessioninCollectionConflictedWithConsultationRequest($consultationRequest);
    }

    public function acceptConsultationRequest(string $consultationRequestId, string $consultationSessionId): void
    {
        $consultationRequest = $this->getConsultationRequestOrDie($consultationRequestId);

        $this->assertNoProposedConsultationRequestInCollectionConflictedWith($consultationRequest);
        $this->assertNoConsultationSessioninCollectionConflictedWithConsultationRequest($consultationRequest);

        $consultationRequest->accept();

        $consultationSession = $consultationRequest->createConsultationSession($consultationSessionId);
        $this->consultationSessions->add($consultationSession);
    }

    public function createRootWorksheet(string $worksheetId, string $name, Mission $mission,
            FormRecordData $formRecordData): Worksheet
    {
        $this->assertActive();
        if (!$mission->programEquals($this->program)) {
            $errorDetail = "forbidden: can only access mission in same program";
            throw RegularException::forbidden($errorDetail);
        }
        return Worksheet::createRootWorksheet($this, $worksheetId, $name, $mission, $formRecordData);
    }

    public function submitBranchWorksheet(
            Worksheet $parentWorksheet, string $worksheetId, string $worksheetName, Mission $mission,
            FormRecordData $formRecordData): Worksheet
    {
        $this->assertActive();
        $this->assertOwnAsset($parentWorksheet);
        return $parentWorksheet->createBranchWorksheet($worksheetId, $worksheetName, $mission, $formRecordData);
    }
    
    public function updateWorksheet(Worksheet $worksheet, string $name, FormRecordData $formRecordData): void
    {
        $this->assertActive();
        $this->assertOwnAsset($worksheet);
        $worksheet->update($name, $formRecordData);
    }

    public function isActiveParticipantOfProgram(Program $program): bool
    {
        return $this->active && $this->program === $program;
    }

    protected function getConsultationRequestOrDie(string $consultationRequestId): ConsultationRequest
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('id', $consultationRequestId));
        $consultationRequest = $this->consultationRequests->matching($criteria)->first();
        if (empty($consultationRequest)) {
            $errorDetail = "not found: consultation request not found";
            throw RegularException::notFound($errorDetail);
        }
        return $consultationRequest;
    }

    protected function assertNoProposedConsultationRequestInCollectionConflictedWith(
            ConsultationRequest $consultationRequest): void
    {
        $p = function (ConsultationRequest $otherConsultationRequest) use ($consultationRequest) {
            return $otherConsultationRequest->isProposedConsultationRequestConflictedWith($consultationRequest);
        };
        if (!empty($this->consultationRequests->filter($p)->count())) {
            $errorDetail = "conflict: requested time already occupied by your other consultation request waiting for consultant response";
            throw RegularException::conflict($errorDetail);
        }
    }

    protected function assertNoConsultationSessioninCollectionConflictedWithConsultationRequest(
            ConsultationRequest $consultationRequest): void
    {
        $p = function (ConsultationSession $consultationSession) use ($consultationRequest) {
            return $consultationSession->conflictedWithConsultationRequest($consultationRequest);
        };
        if (!empty($this->consultationSessions->filter($p)->count())) {
            $errorDetail = "conflict: requested time already occupied by your other consultation session";
            throw RegularException::conflict($errorDetail);
        }
    }
    
    protected function assertActive(): void
    {
        if (!$this->active) {
            $errorDetail = "forbidden: only active program participant can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }
    
    protected function assertOwnAsset(AssetBelongsToParticipantInterface $asset): void
    {
        if (!$asset->belongsTo($this)) {
            $errorDetail = "forbidden: unable to manage asset of other participant";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
