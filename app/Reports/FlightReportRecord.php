<?php

namespace App\Reports;

use App\Models\Flight;
use App\Models\Pilgrim;
use Carbon\CarbonInterface;

readonly class FlightReportRecord
{
    public function __construct(
        public Flight $flight,
        public Pilgrim $pilgrim,
        public ?CarbonInterface $assignedAt = null,
    ) {}
}
