<div class="dlabnav">
    <div class="dlabnav-scroll">
        <ul class="metismenu" id="menu">

            <li>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'mm-active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            @can('pilgrims.view')
            <li>
                <a href="{{ route('admin.pilgrims.index') }}" class="{{ request()->routeIs('admin.pilgrims.*') ? 'mm-active' : '' }}">
                    <i class="fas fa-kaaba"></i>
                    <span class="nav-text">Hajj Registration</span>
                </a>
            </li>
            @endcan

            @canany(['flights.view', 'flights.assign'])
            <li>
                <a class="has-arrow {{ request()->routeIs('admin.flights.*', 'admin.flight-assignments.*') ? 'mm-active' : '' }}"
                   href="javascript:void()"
                   aria-expanded="{{ request()->routeIs('admin.flights.*', 'admin.flight-assignments.*') ? 'true' : 'false' }}">
                    <i class="fas fa-plane"></i>
                    <span class="nav-text">Flights</span>
                </a>
                <ul aria-expanded="{{ request()->routeIs('admin.flights.*', 'admin.flight-assignments.*') ? 'true' : 'false' }}">
                    @can('flights.view')
                    <li>
                        <a href="{{ route('admin.flights.index') }}"
                           class="{{ request()->routeIs('admin.flights.*') ? 'mm-active' : '' }}">
                            Flight Schedule
                        </a>
                    </li>
                    @endcan
                    @can('flights.assign')
                    <li>
                        <a href="{{ route('admin.flight-assignments.index') }}"
                           class="{{ request()->routeIs('admin.flight-assignments.*') ? 'mm-active' : '' }}">
                            Flight Assignment
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endcanany

            @can('reports.view')
            <li>
                <a class="has-arrow {{ request()->routeIs('admin.reports.*') ? 'mm-active' : '' }}"
                   href="javascript:void()"
                   aria-expanded="{{ request()->routeIs('admin.reports.*') ? 'true' : 'false' }}">
                    <i class="fas fa-chart-bar"></i>
                    <span class="nav-text">Reports</span>
                </a>
                <ul aria-expanded="{{ request()->routeIs('admin.reports.*') ? 'true' : 'false' }}">
                    @foreach ($reportNavItems as $reportNavItem)
                        <li>
                            <a href="{{ route('admin.reports.show', $reportNavItem['key']) }}"
                               class="{{ request()->route('report') === $reportNavItem['key'] ? 'mm-active' : '' }}">
                                {{ $reportNavItem['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
            @endcan

            @can('hajj-seasons.view')
            <li>
                <a href="{{ route('admin.hajj-seasons.index') }}" class="{{ request()->routeIs('admin.hajj-seasons.*') ? 'mm-active' : '' }}">
                    <i class="fas fa-calendar-alt"></i>
                    <span class="nav-text">Hajj Seasons</span>
                </a>
            </li>
            @endcan

            @canany(['companies.view','form-owners.view','maktab-categories.view','packages.view','care-offs.view','room-types.view','mehram-relations.view','waris-relations.view','cities.view','countries.view','airlines.view','airports.view'])
            @php
                $hajjMastersActive = request()->routeIs(
                    'admin.companies.*',
                    'admin.form-owners.*',
                    'admin.packages.*',
                    'admin.maktab-categories.*',
                    'admin.care-offs.*',
                    'admin.room-types.*',
                    'admin.mehram-relations.*',
                    'admin.waris-relations.*',
                    'admin.countries.*',
                    'admin.cities.*',
                    'admin.airlines.*',
                    'admin.airports.*',
                );
            @endphp
            <li>
                <a class="has-arrow {{ $hajjMastersActive ? 'mm-active' : '' }}"
                   href="javascript:void()"
                   aria-expanded="{{ $hajjMastersActive ? 'true' : 'false' }}">
                    <i class="fas fa-database"></i>
                    <span class="nav-text">Hajj Setup</span>
                </a>
                <ul aria-expanded="{{ $hajjMastersActive ? 'true' : 'false' }}">
                    @can('companies.view')
                    <li><a href="{{ route('admin.companies.index') }}" class="{{ request()->routeIs('admin.companies.*') ? 'mm-active' : '' }}">Companies</a></li>
                    @endcan
                    @can('form-owners.view')
                    <li><a href="{{ route('admin.form-owners.index') }}" class="{{ request()->routeIs('admin.form-owners.*') ? 'mm-active' : '' }}">Form Owners</a></li>
                    @endcan
                    @can('packages.view')
                    <li><a href="{{ route('admin.packages.index') }}" class="{{ request()->routeIs('admin.packages.*') ? 'mm-active' : '' }}">Packages</a></li>
                    @endcan
                    @can('maktab-categories.view')
                    <li><a href="{{ route('admin.maktab-categories.index') }}" class="{{ request()->routeIs('admin.maktab-categories.*') ? 'mm-active' : '' }}">Maktab Categories</a></li>
                    @endcan
                    @can('care-offs.view')
                    <li><a href="{{ route('admin.care-offs.index') }}" class="{{ request()->routeIs('admin.care-offs.*') ? 'mm-active' : '' }}">Care Off</a></li>
                    @endcan
                    @can('room-types.view')
                    <li><a href="{{ route('admin.room-types.index') }}" class="{{ request()->routeIs('admin.room-types.*') ? 'mm-active' : '' }}">Room Types</a></li>
                    @endcan
                    @can('mehram-relations.view')
                    <li><a href="{{ route('admin.mehram-relations.index') }}" class="{{ request()->routeIs('admin.mehram-relations.*') ? 'mm-active' : '' }}">Mehram Relations</a></li>
                    @endcan
                    @can('waris-relations.view')
                    <li><a href="{{ route('admin.waris-relations.index') }}" class="{{ request()->routeIs('admin.waris-relations.*') ? 'mm-active' : '' }}">Waris Relations</a></li>
                    @endcan
                    @can('countries.view')
                    <li><a href="{{ route('admin.countries.index') }}" class="{{ request()->routeIs('admin.countries.*') ? 'mm-active' : '' }}">Countries</a></li>
                    @endcan
                    @can('cities.view')
                    <li><a href="{{ route('admin.cities.index') }}" class="{{ request()->routeIs('admin.cities.*') ? 'mm-active' : '' }}">Cities</a></li>
                    @endcan
                    @can('airlines.view')
                    <li><a href="{{ route('admin.airlines.index') }}" class="{{ request()->routeIs('admin.airlines.*') ? 'mm-active' : '' }}">Airlines</a></li>
                    @endcan
                    @can('airports.view')
                    <li><a href="{{ route('admin.airports.index') }}" class="{{ request()->routeIs('admin.airports.*') ? 'mm-active' : '' }}">Airports</a></li>
                    @endcan
                </ul>
            </li>
            @endcanany

            @canany(['roles.view','users.view'])
            @php
                $accessControlActive = request()->routeIs('admin.users.*', 'admin.roles.*', 'admin.permissions.*');
            @endphp
            <li>
                <a class="has-arrow {{ $accessControlActive ? 'mm-active' : '' }}"
                   href="javascript:void()"
                   aria-expanded="{{ $accessControlActive ? 'true' : 'false' }}">
                    <i class="fas fa-user-shield"></i>
                    <span class="nav-text">Access Control</span>
                </a>
                <ul aria-expanded="{{ $accessControlActive ? 'true' : 'false' }}">
                    @can('users.view')
                    <li><a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'mm-active' : '' }}">Users</a></li>
                    @endcan
                    @can('roles.view')
                    <li><a href="{{ route('admin.roles.index') }}" class="{{ request()->routeIs('admin.roles.*') ? 'mm-active' : '' }}">Roles</a></li>
                    <li><a href="{{ route('admin.permissions.index') }}" class="{{ request()->routeIs('admin.permissions.*') ? 'mm-active' : '' }}">Permissions</a></li>
                    @endcan
                </ul>
            </li>
            @endcanany

        </ul>

        <div class="copyright">
            <p><strong>{{ config('app.name') }}</strong> &copy; {{ now()->year }}</p>
        </div>
    </div>
</div>
