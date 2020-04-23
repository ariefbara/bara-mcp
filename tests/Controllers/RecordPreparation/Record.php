<?php

namespace Tests\Controllers\RecordPreparation;

interface Record
{
    public function toArrayForDbEntry();
}
