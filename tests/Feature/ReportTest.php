<?php

use App\Enums\FlightDirection;
use App\Enums\Gender;
use App\Enums\HajjSeasonStatus;
use App\Enums\PackageDuration;
use App\Models\Company;
use App\Models\Flight;
use App\Models\HajjSeason;
use App\Models\MaktabCategory;
use App\Models\Package;
use App\Models\Pilgrim;
use App\Models\PilgrimDeletionLog;
use App\Models\User;
use App\Reports\Definitions\DeletedRegistrationsReportDefinition;
use App\Reports\Definitions\FlightReportDefinition;
use App\Reports\Definitions\FlightSummaryReportDefinition;
use App\Reports\Definitions\HajjRegistrationReportDefinition;
use App\Services\HajjSeasonService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

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

it('shows flight summary in the sidebar submenu', function () {
    $this->actingAs($this->admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Flight Summary');
});

it('shows flight assignment reports in the sidebar submenu', function () {
    $this->actingAs($this->admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Flight Assignment Reports');
});

it('shows deleted registrations in the sidebar submenu', function () {
    $this->actingAs($this->admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Deleted Registrations');
});

it('orders report submenu items as hajj, flight summary, flight assignment reports, then deleted registrations', function () {
    $content = $this->actingAs($this->admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->getContent();

    expect($content)->toMatch('/Hajj Reports[\s\S]*Flight Summary[\s\S]*Flight Assignment Reports[\s\S]*Deleted Registrations/');
});

it('shows a dedicated report page with columns first', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.reports.show', HajjRegistrationReportDefinition::KEY))
        ->assertOk()
        ->assertSee('Hajj Reports')
        ->assertSeeInOrder(['Columns', 'Filters'])
        ->assertSee('column-picture', false)
        ->assertSeeInOrder(['column-picture', 'column-hajj_year'], false)
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
        ->toContain('Hajj Reports')
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

it('shows detailed package labels in report results', function () {
    $package = Package::factory()->create([
        'number' => 'PKG-010',
        'name' => 'Results Package',
        'price' => 750000,
        'days' => 21,
        'duration' => PackageDuration::Long,
        'qurbani_included' => false,
    ]);

    Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'package_id' => $package->id,
        'full_name' => 'Package Column Pilgrim',
    ]);

    $response = $this->actingAs($this->admin)
        ->getJson(route('admin.reports.results', [
            'report' => HajjRegistrationReportDefinition::KEY,
            'columns' => ['full_name', 'package'],
        ]))
        ->assertOk();

    expect($response->json('html'))
        ->toContain('Package Column Pilgrim')
        ->toContain($package->registrationOptionLabel());
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

it('renders pilgrim photos when picture column is selected', function () {
    Storage::fake('public');
    Storage::disk('public')->put('pilgrims/photos/report-photo.jpg', 'photo-bytes');

    Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'Photo Column Pilgrim',
        'photo_path' => 'pilgrims/photos/report-photo.jpg',
    ]);

    $response = $this->actingAs($this->admin)
        ->getJson(route('admin.reports.results', [
            'report' => HajjRegistrationReportDefinition::KEY,
            'columns' => ['picture', 'full_name'],
        ]))
        ->assertOk();

    expect($response->json('html'))
        ->toContain('report-pilgrim-photo')
        ->toContain('storage/pilgrims/photos/report-photo.jpg')
        ->toContain('Photo Column Pilgrim');
});

it('excludes picture column from excel export', function () {
    Storage::fake('public');
    Storage::disk('public')->put('pilgrims/photos/export-photo.jpg', 'photo-bytes');

    Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'Picture Export Pilgrim',
        'photo_path' => 'pilgrims/photos/export-photo.jpg',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.reports.export.excel', [
            'report' => 'hajj_registration',
            'columns' => ['picture', 'full_name'],
            'hajj_year' => $this->activeYear,
        ]));

    $content = $response->getContent();

    expect($content)
        ->toContain('Picture Export Pilgrim')
        ->not->toContain('Picture</th>')
        ->not->toContain('storage/pilgrims/photos/export-photo.jpg');
});

it('includes picture column in pdf export', function () {
    Storage::fake('public');
    Storage::disk('public')->put('pilgrims/photos/pdf-photo.jpg', 'photo-bytes');

    Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'PDF Photo Pilgrim',
        'photo_path' => 'pilgrims/photos/pdf-photo.jpg',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.reports.export.pdf', [
            'report' => 'hajj_registration',
            'columns' => ['picture', 'full_name'],
            'hajj_year' => $this->activeYear,
        ]));

    $response->assertOk()
        ->assertHeader('content-type', 'application/pdf');

    expect(strlen($response->getContent()))->toBeGreaterThan(500);
});

it('includes column keys in report print data for picture rendering', function () {
    Storage::fake('public');
    Storage::disk('public')->put('pilgrims/photos/print-photo.jpg', 'photo-bytes');

    Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'Print Photo Pilgrim',
        'photo_path' => 'pilgrims/photos/print-photo.jpg',
    ]);

    $response = $this->actingAs($this->admin)
        ->getJson(route('admin.reports.results', [
            'report' => HajjRegistrationReportDefinition::KEY,
            'columns' => ['picture', 'full_name'],
        ]))
        ->assertOk();

    expect($response->json('html'))
        ->toContain('"columnKeys":["serial","picture","full_name"]')
        ->toContain('report-pilgrim-photo');
});

it('shows document columns in the hajj report column picker but not in defaults', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.reports.show', HajjRegistrationReportDefinition::KEY))
        ->assertOk();

    expect($response->getContent())
        ->toContain('column-passport_document', false)
        ->toContain('column-visa_document', false)
        ->toContain('column-ticket_document', false)
        ->not->toContain('name="columns[]" value="passport_document"', false);
});

it('renders view and download icon buttons when a document is uploaded', function () {
    Storage::fake('public');
    Storage::disk('public')->put('pilgrims/passports/report-passport.pdf', 'passport-bytes');

    Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'Document Column Pilgrim',
        'passport_path' => 'pilgrims/passports/report-passport.pdf',
    ]);

    $response = $this->actingAs($this->admin)
        ->getJson(route('admin.reports.results', [
            'report' => HajjRegistrationReportDefinition::KEY,
            'columns' => ['passport_document', 'full_name'],
        ]))
        ->assertOk();

    expect($response->json('html'))
        ->toContain('Document Column Pilgrim')
        ->toContain('report-document-actions')
        ->toContain('fas fa-eye')
        ->toContain('fas fa-download')
        ->toContain('title="View"')
        ->toContain('title="Download"')
        ->toContain('target="_blank"')
        ->toContain('download')
        ->toContain('storage/pilgrims/passports/report-passport.pdf');
});

it('renders a dash when document columns are selected but files are missing', function () {
    Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'Missing Document Pilgrim',
        'passport_path' => null,
        'visa_path' => null,
        'ticket_path' => null,
    ]);

    $response = $this->actingAs($this->admin)
        ->getJson(route('admin.reports.results', [
            'report' => HajjRegistrationReportDefinition::KEY,
            'columns' => ['passport_document', 'visa_document', 'ticket_document', 'full_name'],
        ]))
        ->assertOk();

    expect($response->json('html'))
        ->toContain('Missing Document Pilgrim')
        ->not->toContain('report-document-actions')
        ->not->toContain('fas fa-eye')
        ->not->toContain('fas fa-download');
});

it('includes document columns in excel export as yes or no', function () {
    Storage::fake('public');
    Storage::disk('public')->put('pilgrims/passports/export-passport.pdf', 'passport-bytes');

    Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'Document Export Pilgrim',
        'passport_path' => 'pilgrims/passports/export-passport.pdf',
    ]);

    Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'Missing Document Export Pilgrim',
        'passport_path' => null,
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.reports.export.excel', [
            'report' => 'hajj_registration',
            'columns' => ['passport_document', 'full_name'],
            'hajj_year' => $this->activeYear,
        ]));

    $content = $response->getContent();

    expect($content)
        ->toContain('Passport Document</th>')
        ->toContain('Document Export Pilgrim')
        ->toContain('Missing Document Export Pilgrim')
        ->toContain('>Yes</td>')
        ->toContain('>No</td>')
        ->not->toContain('storage/pilgrims/passports/export-passport.pdf');
});

it('includes document columns in print data as yes or no', function () {
    Storage::fake('public');
    Storage::disk('public')->put('pilgrims/photos/print-doc-photo.jpg', 'photo-bytes');
    Storage::disk('public')->put('pilgrims/passports/print-doc-passport.pdf', 'passport-bytes');

    Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'Print Document Pilgrim',
        'photo_path' => 'pilgrims/photos/print-doc-photo.jpg',
        'passport_path' => 'pilgrims/passports/print-doc-passport.pdf',
    ]);

    Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'Print Missing Document Pilgrim',
        'passport_path' => null,
    ]);

    $response = $this->actingAs($this->admin)
        ->getJson(route('admin.reports.results', [
            'report' => HajjRegistrationReportDefinition::KEY,
            'columns' => ['picture', 'passport_document', 'full_name'],
        ]))
        ->assertOk();

    $html = $response->json('html');

    expect($html)
        ->toContain('"columnKeys":["serial","picture","passport_document","full_name"]')
        ->toContain('"Yes","Print Document Pilgrim"')
        ->toContain('"No","Print Missing Document Pilgrim"')
        ->toContain('report-document-actions');

    expect($html)->not->toMatch('/report-print-data">\{[^<]*passports\/print-doc-passport/');
});

it('shows a dedicated flight summary report page with columns first', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.reports.show', FlightSummaryReportDefinition::KEY))
        ->assertOk()
        ->assertSee('Flight Summary')
        ->assertSeeInOrder(['Columns', 'Filters'])
        ->assertSee('column-direction', false)
        ->assertSee('column-departure_flight_no', false)
        ->assertSee('column-pilgrims_count', false)
        ->assertSee('Generate')
        ->assertDontSee('Results');
});

it('generates a flight summary report with per-flight hujaj counts and overview stats', function () {
    $outboundFlight = Flight::factory()->create([
        'direction' => FlightDirection::Outbound,
        'departure_flight_no' => 'SUM-OUT',
    ]);

    $returnFlight = Flight::factory()->create([
        'direction' => FlightDirection::Return,
        'departure_flight_no' => 'SUM-RET',
    ]);

    $assignedBoth = Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'Both Flights Pilgrim',
    ]);

    $outboundOnly = Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'Outbound Only Pilgrim',
    ]);

    Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'Unassigned Pilgrim',
    ]);

    $outboundFlight->pilgrims()->attach($assignedBoth->id, ['assigned_by' => $this->admin->id]);
    $outboundFlight->pilgrims()->attach($outboundOnly->id, ['assigned_by' => $this->admin->id]);
    $returnFlight->pilgrims()->attach($assignedBoth->id, ['assigned_by' => $this->admin->id]);

    $response = $this->actingAs($this->admin)
        ->getJson(route('admin.reports.results', [
            'report' => FlightSummaryReportDefinition::KEY,
            'columns' => ['direction', 'departure_flight_no', 'pilgrims_count'],
            'hajj_year' => $this->activeYear,
        ]))
        ->assertOk();

    $html = $response->json('html');

    expect($html)
        ->toContain('SUM-OUT')
        ->toContain('SUM-RET')
        ->toContain('Registered Hujaj')
        ->toContain('Assigned Hujaj')
        ->toContain('Unassigned Hujaj')
        ->toContain('Missing Return')
        ->toContain('Missing Outbound');
});

it('filters flight summary results by journey direction', function () {
    $outboundFlight = Flight::factory()->create([
        'direction' => FlightDirection::Outbound,
        'departure_flight_no' => 'SUM-FLT-OUT',
    ]);

    $returnFlight = Flight::factory()->create([
        'direction' => FlightDirection::Return,
        'departure_flight_no' => 'SUM-FLT-RET',
    ]);

    $pilgrim = Pilgrim::factory()->create(['hajj_year' => $this->activeYear]);
    $outboundFlight->pilgrims()->attach($pilgrim->id, ['assigned_by' => $this->admin->id]);
    $returnFlight->pilgrims()->attach($pilgrim->id, ['assigned_by' => $this->admin->id]);

    $response = $this->actingAs($this->admin)
        ->getJson(route('admin.reports.results', [
            'report' => FlightSummaryReportDefinition::KEY,
            'columns' => ['departure_flight_no', 'pilgrims_count'],
            'hajj_year' => $this->activeYear,
            'direction' => FlightDirection::Return->value,
        ]))
        ->assertOk();

    expect($response->json('html'))
        ->toContain('SUM-FLT-RET')
        ->not->toContain('SUM-FLT-OUT');
});

it('shows a dedicated flight report page with columns first', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.reports.show', FlightReportDefinition::KEY))
        ->assertOk()
        ->assertSee('Flight Assignment Reports')
        ->assertSeeInOrder(['Columns', 'Filters'])
        ->assertSee('column-direction', false)
        ->assertSee('column-departure_flight_no', false)
        ->assertSee('column-given_name', false)
        ->assertSee('column-surname', false)
        ->assertSee('column-date_of_birth', false)
        ->assertSee('column-age', false)
        ->assertSee('column-passport_expiry', false)
        ->assertSee('column-care_off', false)
        ->assertSee('column-maktab_category', false)
        ->assertSee('column-form_owner', false)
        ->assertSee('column-gender', false)
        ->assertSee('Generate')
        ->assertDontSee('column-mobile', false)
        ->assertDontSee('Results');
});

it('generates a flight report for assigned hujaj', function () {
    $flight = Flight::factory()->create([
        'direction' => FlightDirection::Outbound,
        'departure_flight_no' => 'FLRPT001',
    ]);

    $pilgrim = Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'Flight Report Pilgrim',
        'passport_no' => 'FR0000001',
    ]);

    $flight->pilgrims()->attach($pilgrim->id, ['assigned_by' => $this->admin->id]);

    $response = $this->actingAs($this->admin)
        ->getJson(route('admin.reports.results', [
            'report' => FlightReportDefinition::KEY,
            'columns' => ['direction', 'departure_flight_no', 'full_name', 'passport_no'],
        ]))
        ->assertOk();

    expect($response->json('html'))
        ->toContain('S.No.')
        ->toContain('Flight Report Pilgrim')
        ->toContain('FLRPT001')
        ->toContain('Departure to Hajj');
});

it('filters flight report results by journey direction', function () {
    $outboundFlight = Flight::factory()->create([
        'direction' => FlightDirection::Outbound,
        'departure_flight_no' => 'OUT-FLT',
    ]);

    $returnFlight = Flight::factory()->create([
        'direction' => FlightDirection::Return,
        'departure_flight_no' => 'RET-FLT',
    ]);

    $outboundPilgrim = Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'Outbound Flight Pilgrim',
    ]);

    $returnPilgrim = Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'Return Flight Pilgrim',
    ]);

    $outboundFlight->pilgrims()->attach($outboundPilgrim->id, ['assigned_by' => $this->admin->id]);
    $returnFlight->pilgrims()->attach($returnPilgrim->id, ['assigned_by' => $this->admin->id]);

    $response = $this->actingAs($this->admin)
        ->getJson(route('admin.reports.results', [
            'report' => FlightReportDefinition::KEY,
            'columns' => ['departure_flight_no', 'full_name'],
            'direction' => FlightDirection::Return->value,
        ]))
        ->assertOk();

    expect($response->json('html'))
        ->toContain('Return Flight Pilgrim')
        ->toContain('RET-FLT')
        ->not->toContain('Outbound Flight Pilgrim');
});

it('exports flight report to excel', function () {
    $flight = Flight::factory()->create([
        'direction' => FlightDirection::Outbound,
    ]);

    $pilgrim = Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'Flight Excel Pilgrim',
    ]);

    $flight->pilgrims()->attach($pilgrim->id, ['assigned_by' => $this->admin->id]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.reports.export.excel', [
            'report' => FlightReportDefinition::KEY,
            'columns' => ['full_name', 'departure_flight_no'],
            'hajj_year' => $this->activeYear,
        ]));

    $response->assertOk()
        ->assertHeader('content-type', 'application/vnd.ms-excel; charset=UTF-8');

    expect($response->getContent())
        ->toContain('Flight Excel Pilgrim')
        ->toContain('Flight Assignment Reports');
});

it('includes expanded hujaj columns in flight report results', function () {
    $flight = Flight::factory()->create([
        'direction' => FlightDirection::Outbound,
        'departure_flight_no' => 'HJJCOL001',
    ]);

    $pilgrim = Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'surname' => 'Khan',
        'given_name' => 'Ali',
        'full_name' => 'Khan Ali',
        'passport_no' => 'PK1234567',
        'date_of_birth' => '1985-06-15',
        'passport_expiry' => '2030-12-31',
        'gender' => Gender::Male,
    ]);

    $flight->pilgrims()->attach($pilgrim->id, ['assigned_by' => $this->admin->id]);

    $response = $this->actingAs($this->admin)
        ->getJson(route('admin.reports.results', [
            'report' => FlightReportDefinition::KEY,
            'columns' => [
                'departure_flight_no',
                'form_owner',
                'company',
                'maktab_category',
                'package',
                'gender',
                'given_name',
                'surname',
                'date_of_birth',
                'age',
                'passport_expiry',
                'care_off',
            ],
            'hajj_year' => $this->activeYear,
        ]))
        ->assertOk();

    $html = $response->json('html');

    expect($html)
        ->toContain('HJJCOL001')
        ->toContain('Khan')
        ->toContain('Ali')
        ->toContain('15 Jun 1985')
        ->toContain('31 Dec 2030')
        ->toContain($pilgrim->formOwner->name)
        ->toContain($pilgrim->company->registrationOptionLabel())
        ->toContain($pilgrim->careOff->name)
        ->toContain($pilgrim->gender->label());
});

it('filters flight report results by pod and care off', function () {
    $flight = Flight::factory()->create([
        'departure_flight_no' => 'PODFLT001',
    ]);

    $matchingPilgrim = Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'Matching POD Care Off Pilgrim',
    ]);

    $otherPilgrim = Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'Other POD Care Off Pilgrim',
    ]);

    $flight->pilgrims()->attach($matchingPilgrim->id, ['assigned_by' => $this->admin->id]);
    $flight->pilgrims()->attach($otherPilgrim->id, ['assigned_by' => $this->admin->id]);

    $response = $this->actingAs($this->admin)
        ->getJson(route('admin.reports.results', [
            'report' => FlightReportDefinition::KEY,
            'columns' => ['full_name', 'pod_city', 'care_off'],
            'hajj_year' => $this->activeYear,
            'pod_city_id' => $matchingPilgrim->pod_city_id,
            'care_off_id' => $matchingPilgrim->care_off_id,
        ]))
        ->assertOk();

    expect($response->json('html'))
        ->toContain('Matching POD Care Off Pilgrim')
        ->not->toContain('Other POD Care Off Pilgrim');
});

it('shows pod and care off filters on the flight report page', function () {
    $content = $this->actingAs($this->admin)
        ->get(route('admin.reports.show', FlightReportDefinition::KEY))
        ->assertOk()
        ->getContent();

    foreach (['pod_city_id', 'care_off_id'] as $field) {
        expect($content)->toMatch('/id="'.$field.'"[\s\S]*?<option value=""[\s\S]*?selected[\s\S]*?>All<\/option>/');
    }
});

it('generates deleted registrations report from deletion logs', function () {
    $pilgrim = Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'Deleted Report Pilgrim',
        'passport_no' => 'DR0000001',
        'family_code' => 'DYN-01-S',
    ]);

    PilgrimDeletionLog::query()->create([
        'pilgrim_id' => $pilgrim->id,
        'deleted_by' => $this->admin->id,
        'deleted_at' => now(),
        'hajj_year' => $this->activeYear,
        'full_name' => $pilgrim->full_name,
        'passport_no' => $pilgrim->passport_no,
        'family_code' => $pilgrim->family_code,
        'company_name' => 'Audit Company',
    ]);

    $response = $this->actingAs($this->admin)
        ->getJson(route('admin.reports.results', [
            'report' => DeletedRegistrationsReportDefinition::KEY,
            'columns' => ['deleted_at', 'deleted_by', 'full_name', 'passport_no', 'company'],
        ]))
        ->assertOk();

    expect($response->json('html'))
        ->toContain('Deleted Report Pilgrim')
        ->toContain('DR0000001')
        ->toContain('Audit Company')
        ->toContain($this->admin->name);
});

it('shows dedicated deleted registrations report page', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.reports.show', DeletedRegistrationsReportDefinition::KEY))
        ->assertOk()
        ->assertSee('Deleted Registrations')
        ->assertSee('column-deleted_at', false)
        ->assertSee('Deleted By');
});
