<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\Firm\Personnel;
use Resources\Exception\RegularException;

class PersonnelLogin
{
    /**
     *
     * @var PersonnelRepository
     */
    protected $personnelRepository;
    
    function __construct(PersonnelRepository $personnelRepository)
    {
        $this->personnelRepository = $personnelRepository;
    }
    
    public function execute(string $firmIdentifier, string $email, string $password): Personnel
    {
        $errorDetail = 'unauthorized: invalid email or password';
        try {
            $personnel = $this->personnelRepository->ofEmail($firmIdentifier, $email);
        } catch (RegularException $ex) {
            throw RegularException::unauthorized($errorDetail);
        }
        
        if (!$personnel->passwordMatches($password)) {
            throw RegularException::unauthorized($errorDetail);
        }
        return $personnel;
    }

}
