<?php

namespace App\Reports;

use App\Reports\Contracts\ReportDefinition;
use App\Reports\Definitions\DeletedRegistrationsReportDefinition;
use App\Reports\Definitions\FlightReportDefinition;
use App\Reports\Definitions\FlightSummaryReportDefinition;
use App\Reports\Definitions\HajjRegistrationReportDefinition;
use InvalidArgumentException;

class ReportRegistry
{
    /** @var array<string, ReportDefinition> */
    private array $definitions;

    public function __construct()
    {
        $this->definitions = [
            HajjRegistrationReportDefinition::KEY => app(HajjRegistrationReportDefinition::class),
            FlightSummaryReportDefinition::KEY => app(FlightSummaryReportDefinition::class),
            FlightReportDefinition::KEY => app(FlightReportDefinition::class),
            DeletedRegistrationsReportDefinition::KEY => app(DeletedRegistrationsReportDefinition::class),
        ];
    }

    /** @return list<ReportDefinition> */
    public function all(): array
    {
        return array_values($this->definitions);
    }

    /** @return array<string, string> */
    public function options(): array
    {
        return collect($this->definitions)
            ->mapWithKeys(fn (ReportDefinition $definition): array => [
                $definition->key() => $definition->label(),
            ])
            ->all();
    }

    public function get(string $key): ReportDefinition
    {
        if (! isset($this->definitions[$key])) {
            throw new InvalidArgumentException("Unknown report type [{$key}].");
        }

        return $this->definitions[$key];
    }

    /** @return list<array{key: string, label: string}> */
    public function navItems(): array
    {
        return collect($this->all())
            ->map(fn (ReportDefinition $definition): array => [
                'key' => $definition->key(),
                'label' => $definition->label(),
            ])
            ->values()
            ->all();
    }

    public function defaultKey(): string
    {
        return HajjRegistrationReportDefinition::KEY;
    }
}
