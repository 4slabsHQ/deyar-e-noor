<?php

use App\Enums\Gender;
use App\Enums\HajjSeasonStatus;
use App\Models\Company;
use App\Models\HajjSeason;
use App\Models\MaktabCategory;
use App\Models\Pilgrim;
use App\Models\User;
use App\Reports\Definitions\HajjRegistrationReportDefinition;
use App\Services\HajjSeasonService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('Super Admin');

    $this->activeYear = app(HajjSeasonService::class)->activeYear();

    HajjSeason::query()->updateOrCreate(
        ['year' => $this->activeYear],
        ['status' => HajjSeasonStatus::Active, 'activated_at' => now()],
    );
});

it('redirects the reports index to the default report', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.reports.index'))
        ->assertRedirect(route('admin.reports.show', HajjRegistrationReportDefinition::KEY));
});

it('shows hajj reports in the sidebar submenu', function () {
    $this->actingAs($this->admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Hajj Reports');
});

it('shows a dedicated report page with columns first', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.reports.show', HajjRegistrationReportDefinition::KEY))
        ->assertOk()
        ->assertSee('Hajj Registration')
        ->assertSeeInOrder(['Columns', 'Filters'])
        ->assertSee('Select all')
        ->assertSee('Save as default')
        ->assertSee('Generate')
        ->assertDontSee('Results');
});

it('loads saved column defaults for the user', function () {
    $this->admin->saveReportColumns(HajjRegistrationReportDefinition::KEY, [
        'full_name',
        'passport_no',
    ]);

    $content = $this->actingAs($this->admin)
        ->get(route('admin.reports.show', HajjRegistrationReportDefinition::KEY))
        ->assertOk()
        ->getContent();

    expect($content)->toMatch('/id="column-full_name"[\s\S]*?checked/');
    expect($content)->toMatch('/id="column-passport_no"[\s\S]*?checked/');
});

it('saves column defaults for the user', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.reports.columns.save', HajjRegistrationReportDefinition::KEY), [
            'columns' => ['full_name', 'company'],
        ])
        ->assertRedirect(route('admin.reports.show', HajjRegistrationReportDefinition::KEY))
        ->assertSessionHas('status', 'column-defaults-saved');

    expect($this->admin->fresh()->reportColumns(HajjRegistrationReportDefinition::KEY))
        ->toBe(['full_name', 'company']);
});

it('requires at least one column when saving defaults', function () {
    $this->actingAs($this->admin)
        ->from(route('admin.reports.show', HajjRegistrationReportDefinition::KEY))
        ->post(route('admin.reports.columns.save', HajjRegistrationReportDefinition::KEY), [
            'columns' => [],
        ])
        ->assertRedirect(route('admin.reports.show', HajjRegistrationReportDefinition::KEY))
        ->assertSessionHasErrors('columns');
});

it('returns not found for unknown reports', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.reports.show', 'unknown_report'))
        ->assertNotFound();
});

it('denies reports without permission', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.reports.index'))
        ->assertForbidden();
});

it('generates a report with selected columns and filters', function () {
    $company = Company::factory()->create(['name' => 'Report Company']);

    Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'company_id' => $company->id,
        'full_name' => 'Selected Column Pilgrim',
        'gender' => Gender::Male,
    ]);

    Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'Other Pilgrim',
    ]);

    $this->actingAs($this->admin)
        ->getJson(route('admin.reports.results', [
            'report' => HajjRegistrationReportDefinition::KEY,
            'columns' => ['full_name', 'company', 'gender'],
            'company_id' => $company->id,
        ]))
        ->assertOk()
        ->assertJsonStructure(['html'])
        ->assertSee('Selected Column Pilgrim', false);
});

it('exports generated report to csv', function () {
    $pilgrim = Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'CSV Export Pilgrim',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.reports.export.csv', [
            'report' => 'hajj_registration',
            'columns' => ['full_name', 'passport_no'],
            'hajj_year' => $this->activeYear,
        ]));

    $response->assertOk()
        ->assertHeader('content-type', 'text/csv; charset=UTF-8');

    expect($response->streamedContent())
        ->toContain('CSV Export Pilgrim')
        ->toContain('Full Name');
});

it('exports generated report to excel', function () {
    Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'Excel Export Pilgrim',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.reports.export.excel', [
            'report' => 'hajj_registration',
            'columns' => ['full_name'],
            'hajj_year' => $this->activeYear,
        ]));

    $response->assertOk()
        ->assertHeader('content-type', 'application/vnd.ms-excel; charset=UTF-8');

    expect($response->getContent())->toContain('Excel Export Pilgrim');
});

it('exports generated report to pdf', function () {
    Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'PDF Export Pilgrim',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.reports.export.pdf', [
            'report' => 'hajj_registration',
            'columns' => ['full_name'],
            'hajj_year' => $this->activeYear,
        ]));

    $response->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});

it('denies exports without export permission', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('reports.view');

    $this->actingAs($user)
        ->get(route('admin.reports.export.csv', [
            'report' => 'hajj_registration',
            'columns' => ['full_name'],
            'hajj_year' => $this->activeYear,
        ]))
        ->assertForbidden();
});

it('includes maktab column when selected', function () {
    $maktab = MaktabCategory::factory()->create(['name' => 'Mina Zone']);

    Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'maktab_category_id' => $maktab->id,
        'full_name' => 'Maktab Column Pilgrim',
    ]);

    $this->actingAs($this->admin)
        ->getJson(route('admin.reports.results', [
            'report' => HajjRegistrationReportDefinition::KEY,
            'columns' => ['full_name', 'maktab_category'],
        ]))
        ->assertOk()
        ->assertSee('Maktab Column Pilgrim', false)
        ->assertSee('Mina Zone', false);
});
