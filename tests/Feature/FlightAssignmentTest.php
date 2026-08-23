<?php

use App\Enums\FlightAssignmentAction;
use App\Enums\FlightDirection;
use App\Enums\HajjSeasonStatus;
use App\Models\Company;
use App\Models\Flight;
use App\Models\FlightAssignmentLog;
use App\Models\HajjSeason;
use App\Models\Package;
use App\Models\Pilgrim;
use App\Models\User;
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

    $this->outboundFlight = Flight::factory()->create([
        'direction' => FlightDirection::Outbound,
    ]);

    $this->returnFlight = Flight::factory()->create([
        'direction' => FlightDirection::Return,
    ]);

    $this->otherOutboundFlight = Flight::factory()->create([
        'direction' => FlightDirection::Outbound,
    ]);
});

it('shows unified flight assignment page', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.flight-assignments.index'))
        ->assertOk()
        ->assertSee('Flight Assignment')
        ->assertSee('Flights')
        ->assertSee('Assign Hujaj')
        ->assertSee($this->outboundFlight->departure_flight_no)
        ->assertSee('Assign')
        ->assertSee('Select a flight above');
});

it('filters flights on assignment page', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.flight-assignments.index', ['direction' => FlightDirection::Return->value]))
        ->assertOk()
        ->assertSee($this->returnFlight->departure_flight_no)
        ->assertDontSee($this->outboundFlight->departure_flight_no);
});

it('renders assignment workspace on the same page when flight is selected', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.flight-assignments.index', ['flight' => $this->outboundFlight->id]))
        ->assertOk()
        ->assertSee('Assign selected')
        ->assertSee('Flight status')
        ->assertSee($this->outboundFlight->departure_flight_no);
});

it('returns assignment workspace partial for ajax requests', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.flight-assignments.workspace', $this->outboundFlight), [
            'X-Requested-With' => 'XMLHttpRequest',
        ])
        ->assertOk()
        ->assertSee('Assign selected')
        ->assertSee($this->outboundFlight->departure_flight_no)
        ->assertSee('data-results-url', false)
        ->assertSee('flight-assignment-filters-card', false)
        ->assertSee('flight-assignment-results-card', false)
        ->assertSee('flight-assignment-results', false)
        ->assertDontSee('@extends');
});

it('returns assignment results partial as json for ajax requests', function () {
    $pilgrim = Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'Results Partial Pilgrim',
    ]);

    $response = $this->actingAs($this->admin)
        ->getJson(route('admin.flight-assignments.results', $this->outboundFlight))
        ->assertOk()
        ->assertJsonStructure(['html', 'count']);

    expect($response->json('html'))
        ->toContain('Results Partial Pilgrim')
        ->toContain('flight-assignment-bulk-form')
        ->not->toContain('data-workspace-filter-form');

    expect($response->json('count'))->toBe(1);
});

it('redirects legacy show route to unified page', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.flight-assignments.show', $this->outboundFlight))
        ->assertRedirect(route('admin.flight-assignments.index', ['flight' => $this->outboundFlight->id]));
});

it('does not show assign action on flights index', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.flights.index'))
        ->assertOk()
        ->assertDontSee(route('admin.flight-assignments.show', $this->outboundFlight), false);
});

it('denies flight assignment without permission', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.flight-assignments.index'))
        ->assertForbidden();
});

it('assigns hujaj to a flight and logs the action', function () {
    $pilgrim = Pilgrim::factory()->create(['hajj_year' => $this->activeYear]);

    $this->actingAs($this->admin)
        ->post(route('admin.flight-assignments.store', $this->outboundFlight), [
            'action' => 'assign',
            'pilgrim_ids' => [$pilgrim->id],
        ])
        ->assertRedirect(route('admin.flight-assignments.index', ['flight' => $this->outboundFlight->id]))
        ->assertSessionHas('success', '1 hajji assigned.');

    expect($this->outboundFlight->fresh()->pilgrims()->count())->toBe(1)
        ->and($this->outboundFlight->pilgrims()->first()->pivot->assigned_by)->toBe($this->admin->id)
        ->and(FlightAssignmentLog::query()->count())->toBe(1)
        ->and(FlightAssignmentLog::query()->first()->action)->toBe(FlightAssignmentAction::Assigned);
});

it('allows the same hajji on outbound and return flights', function () {
    $pilgrim = Pilgrim::factory()->create(['hajj_year' => $this->activeYear]);

    $this->outboundFlight->pilgrims()->attach($pilgrim->id, ['assigned_by' => $this->admin->id]);

    $this->actingAs($this->admin)
        ->post(route('admin.flight-assignments.store', $this->returnFlight), [
            'action' => 'assign',
            'pilgrim_ids' => [$pilgrim->id],
        ])
        ->assertRedirect(route('admin.flight-assignments.index', ['flight' => $this->returnFlight->id]))
        ->assertSessionHas('success', '1 hajji assigned.');

    expect($pilgrim->fresh()->flights)->toHaveCount(2);
});

it('rejects assigning hujaj already on another flight in the same direction', function () {
    $pilgrim = Pilgrim::factory()->create(['hajj_year' => $this->activeYear]);

    $this->otherOutboundFlight->pilgrims()->attach($pilgrim->id, ['assigned_by' => $this->admin->id]);

    $this->actingAs($this->admin)
        ->post(route('admin.flight-assignments.store', $this->outboundFlight), [
            'action' => 'assign',
            'pilgrim_ids' => [$pilgrim->id],
        ])
        ->assertRedirect(route('admin.flight-assignments.index', ['flight' => $this->outboundFlight->id]))
        ->assertSessionHas('error');

    expect(session('error'))->toContain('On '.$this->otherOutboundFlight->departure_flight_no);

    expect($this->outboundFlight->fresh()->pilgrims()->count())->toBe(0);
});

it('shows blocked hujaj without a checkbox and with a clear reason', function () {
    $pilgrim = Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'Blocked Pilgrim',
    ]);

    $this->otherOutboundFlight->pilgrims()->attach($pilgrim->id, ['assigned_by' => $this->admin->id]);

    $this->actingAs($this->admin)
        ->get(route('admin.flight-assignments.workspace', $this->outboundFlight))
        ->assertOk()
        ->assertSee('Blocked Pilgrim')
        ->assertSee('On '.$this->otherOutboundFlight->departure_flight_no)
        ->assertSee('Remove from that flight first.')
        ->assertDontSee('pilgrim-checkbox" value="'.$pilgrim->id.'"', false);
});

it('rejects removing hujaj who are not on the flight', function () {
    $pilgrim = Pilgrim::factory()->create(['hajj_year' => $this->activeYear]);

    $this->actingAs($this->admin)
        ->post(route('admin.flight-assignments.store', $this->outboundFlight), [
            'action' => 'remove',
            'pilgrim_ids' => [$pilgrim->id],
        ])
        ->assertRedirect(route('admin.flight-assignments.index', ['flight' => $this->outboundFlight->id]))
        ->assertSessionHas('error');

    expect(session('error'))->toContain('not assigned to this flight');
});

it('removes hujaj from a flight and logs the action', function () {
    $pilgrim = Pilgrim::factory()->create(['hajj_year' => $this->activeYear]);
    $this->outboundFlight->pilgrims()->attach($pilgrim->id, ['assigned_by' => $this->admin->id]);

    $this->actingAs($this->admin)
        ->post(route('admin.flight-assignments.store', $this->outboundFlight), [
            'action' => 'remove',
            'pilgrim_ids' => [$pilgrim->id],
        ])
        ->assertRedirect(route('admin.flight-assignments.index', ['flight' => $this->outboundFlight->id]))
        ->assertSessionHas('success', '1 hajji removed.');

    expect($this->outboundFlight->fresh()->pilgrims()->count())->toBe(0)
        ->and(FlightAssignmentLog::query()->where('action', FlightAssignmentAction::Removed)->count())->toBe(1);
});

it('bulk assigns only assignable hujaj when select all is used', function () {
    foreach (range(1, 3) as $index) {
        Pilgrim::query()->create([
            'hajj_year' => $this->activeYear,
            'full_name' => "Bulk Pilgrim {$index}",
            'passport_no' => "BP000000{$index}",
            'family_code' => "BLK-0{$index}-S",
        ]);
    }

    $blocked = Pilgrim::query()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'Already Assigned Pilgrim',
        'passport_no' => 'BP0000099',
        'family_code' => 'BLK-99-S',
    ]);

    $this->otherOutboundFlight->pilgrims()->attach($blocked->id, ['assigned_by' => $this->admin->id]);

    $this->actingAs($this->admin)
        ->post(route('admin.flight-assignments.store', $this->outboundFlight), [
            'action' => 'assign',
            'select_all' => true,
            'assignment_status' => 'not_on_flight',
        ])
        ->assertRedirect(route('admin.flight-assignments.index', [
            'flight' => $this->outboundFlight->id,
            'assignment_status' => 'not_on_flight',
        ]))
        ->assertSessionHas('success', '3 hujaj assigned.');

    expect($this->outboundFlight->fresh()->pilgrims()->count())->toBe(3);
});

it('shows only active season hujaj in workspace', function () {
    Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'Active Season Pilgrim',
    ]);

    Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear - 1,
        'full_name' => 'Archived Season Pilgrim',
    ]);

    $this->actingAs($this->admin)
        ->get(route('admin.flight-assignments.workspace', $this->outboundFlight))
        ->assertOk()
        ->assertSee('Active Season Pilgrim')
        ->assertDontSee('Archived Season Pilgrim');
});

it('filters hujaj list by company in workspace', function () {
    $matching = Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'Matching Company Pilgrim',
    ]);

    Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'Other Company Pilgrim',
    ]);

    $this->actingAs($this->admin)
        ->get(route('admin.flight-assignments.workspace', [
            'flight' => $this->outboundFlight,
            'company_id' => $matching->company_id,
        ]))
        ->assertOk()
        ->assertSee('Matching Company Pilgrim')
        ->assertDontSee('Other Company Pilgrim');
});

it('updates hujaj count on flights index after assignment', function () {
    $pilgrim = Pilgrim::factory()->create(['hajj_year' => $this->activeYear]);

    $this->actingAs($this->admin)
        ->post(route('admin.flight-assignments.store', $this->outboundFlight), [
            'action' => 'assign',
            'pilgrim_ids' => [$pilgrim->id],
        ]);

    $this->actingAs($this->admin)
        ->get(route('admin.flights.index'))
        ->assertOk()
        ->assertSee('1');
});

it('shows serial numbers and report-style filters in assignment workspace', function () {
    $company = Company::factory()->create([
        'name' => 'Filter Test Co',
        'munazzam_code' => 'MUN-99',
    ]);

    $package = Package::factory()->create([
        'number' => 'PKG-001',
        'name' => 'Economy Package',
        'is_active' => true,
    ]);

    Pilgrim::factory()->create([
        'hajj_year' => $this->activeYear,
        'full_name' => 'Serial Row Pilgrim',
        'company_id' => $company->id,
        'package_id' => $package->id,
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.flight-assignments.workspace', $this->outboundFlight))
        ->assertOk()
        ->assertSee('S.No.')
        ->assertSee('Hujaj')
        ->assertSee('1 rows', false)
        ->assertSee('Serial Row Pilgrim')
        ->assertSee('Filter Test Co (MUN-99)', false)
        ->assertSee($package->registrationOptionLabel(), false)
        ->assertSee('js-searchable-select', false);

    expect($response->getContent())->toMatch('/<td[^>]*class="[^"]*flight-serial-column[^"]*"[^>]*>\s*1\s*<\/td>/');
});
