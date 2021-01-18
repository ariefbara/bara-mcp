<?php

namespace Client\Domain\Model\Client;

use Client\Domain\DependencyModel\Firm\ClientCVForm;
use Client\Domain\Model\Client;
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class ClientCV
{

    /**
     * 
     * @var Client
     */
    protected $client;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var ClientCVForm
     */
    protected $clientCVForm;

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

    public function __construct(Client $client, string $id, ClientCVForm $clientCVForm, FormRecordData $formRecordData)
    {
        $this->client = $client;
        $this->id = $id;
        $this->clientCVForm = $clientCVForm;
        $this->formRecord = $clientCVForm->createFormRecord($id, $formRecordData);
        $this->removed = false;
    }
    protected function assertUnremoved(): void
    {
        if ($this->removed) {
            $errorDetail = "forbidden: CV already removed";
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
    
    public function isActiveCVCorrespondWithClientCVForm(ClientCVForm $clientCVForm): bool
    {
        return !$this->removed && $this->clientCVForm === $clientCVForm;
    }
    
    public function belongsToClient(Client $client): bool
    {
        return $this->client === $client;
    }

}
