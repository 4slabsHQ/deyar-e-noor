<?php

use App\Enums\Gender;
use App\Enums\HajjSeasonStatus;
use App\Enums\PackageDuration;
use App\Models\Company;
use App\Models\HajjSeason;
use App\Models\MaktabCategory;
use App\Models\Package;
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
        ->assertSee('S.No.', false)
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
        ->toContain('Hajj Registration')
        ->toContain('S.No.')
        ->toContain('Full Name');
});

it('exports generated report to excel with a custom title', function () {
    Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'Excel Export Pilgrim',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.reports.export.excel', [
            'report' => 'hajj_registration',
            'columns' => ['full_name'],
            'hajj_year' => $this->activeYear,
            'report_title' => 'Company Wise Summary',
        ]));

    $response->assertOk()
        ->assertHeader('content-type', 'application/vnd.ms-excel; charset=UTF-8');

    expect($response->getContent())->toContain('Company Wise Summary')
        ->toContain('Excel Export Pilgrim');
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

    $content = $response->getContent();

    expect($content)->toContain('Excel Export Pilgrim')
        ->toContain('S.No.')
        ->toStartWith("\xEF\xBB\xBF");
});

it('exports blank cells for null values in excel', function () {
    Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'surname' => 'RABIA',
        'given_name' => null,
        'full_name' => 'RABIA',
        'passport_no' => null,
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.reports.export.excel', [
            'report' => 'hajj_registration',
            'columns' => ['surname', 'given_name', 'passport_no'],
            'hajj_year' => $this->activeYear,
        ]));

    $content = $response->getContent();

    expect($content)->toContain('RABIA')
        ->not->toContain('—')
        ->not->toContain('â€');
});

it('exports generated report to pdf with a custom title', function () {
    Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'PDF Title Pilgrim',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.reports.export.pdf', [
            'report' => 'hajj_registration',
            'columns' => ['full_name'],
            'hajj_year' => $this->activeYear,
            'report_title' => 'Filtered Pilgrim List',
        ]));

    $response->assertOk()
        ->assertHeader('content-type', 'application/pdf');
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

it('groups report columns by registration form sections', function () {
    $content = $this->actingAs($this->admin)
        ->get(route('admin.reports.show', HajjRegistrationReportDefinition::KEY))
        ->assertOk()
        ->getContent();

    expect($content)->toMatch('/report-column-group-title">Registration<\/div>[\s\S]*report-column-group-title">Personal Details<\/div>[\s\S]*report-column-group-title">Passport &amp; Contact<\/div>[\s\S]*report-column-group-title">Mehram &amp; Waris<\/div>[\s\S]*report-column-group-title">Family &amp; Association<\/div>[\s\S]*report-column-group-title">Comments<\/div>/');
    expect($content)->toMatch('/id="column-hajj_year"[\s\S]*id="column-form_owner"[\s\S]*id="column-gender"[\s\S]*id="column-passport_no"[\s\S]*id="column-mehram_name"[\s\S]*id="column-family_code"/');
});

it('shows All as the default option on report filter selects', function () {
    Company::factory()->create(['name' => 'Filter Company']);
    Package::factory()->create(['name' => 'Filter Package']);

    $content = $this->actingAs($this->admin)
        ->get(route('admin.reports.show', HajjRegistrationReportDefinition::KEY))
        ->assertOk()
        ->getContent();

    foreach (['company_id', 'package_id', 'maktab_category_id', 'form_owner_id', 'pod_city_id', 'care_off_id', 'gender'] as $field) {
        expect($content)->toMatch('/id="'.$field.'"[\s\S]*?<option value=""[\s\S]*?selected[\s\S]*?>All<\/option>/');
    }
});

it('shows detailed package labels in report filters', function () {
    $package = Package::factory()->create([
        'number' => 'PKG-RPT',
        'name' => 'Report Package',
        'price' => 500000,
        'days' => 14,
        'duration' => PackageDuration::Short,
        'qurbani_included' => true,
    ]);

    Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'package_id' => $package->id,
    ]);

    $this->actingAs($this->admin)
        ->get(route('admin.reports.show', HajjRegistrationReportDefinition::KEY))
        ->assertOk()
        ->assertSee($package->registrationOptionLabel(), false);
});

it('includes maktab column when selected', function () {
    $maktab = MaktabCategory::factory()->create([
        'name' => 'Category C',
        'zone' => 'Zone 5',
    ]);

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
        ->assertSee('Category C (Zone 5)', false);
});
