<?php

namespace Client\Domain\Model\Client;

use Client\Domain\DependencyModel\Firm\BioForm;
use Client\Domain\Model\Client;
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class ClientBio
{

    /**
     * 
     * @var Client
     */
    protected $client;

    /**
     * 
     * @var BioForm
     */
    protected $bioForm;

    /**
     * 
     * @var FormRecord
     */
    protected $formRecord;

    /**
     * 
     * @var bool
     */
    protected $removed;

    public function __construct(Client $client, string $id, BioForm $bioForm, FormRecordData $formRecordData)
    {
        $this->client = $client;
        $this->bioForm = $bioForm;
        $this->formRecord = $bioForm->createFormRecord($id, $formRecordData);
        $this->removed = false;
    }
    protected function assertUnremoved(): void
    {
        if ($this->removed) {
            $errorDetail = "forbidden: bio already removed";
            throw RegularException::forbidden($errorDetail);
        }
    }
    
    public function update(FormRecordData $formRecordData): void
    {
        $this->assertUnremoved();
        $this->formRecord->update($formRecordData);
    }
    
    public function remove(): void
    {
        $this->assertUnremoved();
        $this->removed = true;
    }
    
    public function isActiveBioCorrespondWithForm(BioForm $bioForm): bool
    {
        return !$this->removed && $this->bioForm === $bioForm;
    }
    
    public function belongsToClient(Client $client): bool
    {
        return $this->client === $client;
    }

}
