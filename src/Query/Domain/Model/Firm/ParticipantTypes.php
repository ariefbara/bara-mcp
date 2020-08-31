<?php

namespace Query\Domain\Model\Firm;

use Resources\Exception\RegularException;

class ParticipantTypes
{

    const USER_TYPE = 'user';
    const CLIENT_TYPE = 'client';
    const TEAM_TYPE = 'team';

    /**
     *
     * @var array
     */
    protected $values = [];

    public function getValues(): array
    {
        return $this->values;
    }

    public function __construct(array $types)
    {
        $validTypes = [self::USER_TYPE, self::CLIENT_TYPE, self::TEAM_TYPE];
        if (!empty(array_diff($types, $validTypes))) {
            $errorDetail = 'forbidden: unrecognized participat type';
            throw RegularException::forbidden($errorDetail);
        }
        $this->values = $types;
    }

    public function hasType(string $type): bool
    {
        return in_array($type, $this->values);
    }

}
