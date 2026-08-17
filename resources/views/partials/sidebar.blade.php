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

            @can('flights.view')
            <li>
                <a href="{{ route('admin.flights.index') }}" class="{{ request()->routeIs('admin.flights.*') ? 'mm-active' : '' }}">
                    <i class="fas fa-plane"></i>
                    <span class="nav-text">Flights</span>
                </a>
            </li>
            @endcan

            @canany(['companies.view','form-owners.view','maktab-categories.view','packages.view','care-offs.view','room-types.view','mehram-relations.view','waris-relations.view','cities.view','countries.view','airlines.view','airports.view'])
            <li>
                <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-mosque"></i>
                    <span class="nav-text">Hajj Masters</span>
                </a>
                <ul aria-expanded="false">
                    @can('companies.view')
                    <li><a href="{{ route('admin.companies.index') }}">Companies</a></li>
                    @endcan
                    @can('form-owners.view')
                    <li><a href="{{ route('admin.form-owners.index') }}">Form Owners</a></li>
                    @endcan
                    @can('maktab-categories.view')
                    <li><a href="{{ route('admin.maktab-categories.index') }}">Maktab Categories</a></li>
                    @endcan
                    @can('packages.view')
                    <li><a href="{{ route('admin.packages.index') }}">Packages</a></li>
                    @endcan
                    @can('care-offs.view')
                    <li><a href="{{ route('admin.care-offs.index') }}">Care Off</a></li>
                    @endcan
                    @can('room-types.view')
                    <li><a href="{{ route('admin.room-types.index') }}">Room Types</a></li>
                    @endcan
                    @can('mehram-relations.view')
                    <li><a href="{{ route('admin.mehram-relations.index') }}">Mehram Relations</a></li>
                    @endcan
                    @can('waris-relations.view')
                    <li><a href="{{ route('admin.waris-relations.index') }}">Waris Relations</a></li>
                    @endcan
                    @can('cities.view')
                    <li><a href="{{ route('admin.cities.index') }}">Cities</a></li>
                    @endcan
                    @can('countries.view')
                    <li><a href="{{ route('admin.countries.index') }}">Countries</a></li>
                    @endcan
                    @can('airlines.view')
                    <li><a href="{{ route('admin.airlines.index') }}">Airlines</a></li>
                    @endcan
                    @can('airports.view')
                    <li><a href="{{ route('admin.airports.index') }}">Airports</a></li>
                    @endcan
                </ul>
            </li>
            @endcanany

            @canany(['roles.view','users.view'])
            <li>
                <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-user-shield"></i>
                    <span class="nav-text">Access Control</span>
                </a>
                <ul aria-expanded="false">
                    @can('users.view')
                    <li><a href="{{ route('admin.users.index') }}">Users</a></li>
                    @endcan
                    @can('roles.view')
                    <li><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                    <li><a href="{{ route('admin.permissions.index') }}">Permissions</a></li>
                    @endcan
                </ul>
            </li>
            @endcanany

            @can('hajj-seasons.view')
            <li>
                <a href="{{ route('admin.hajj-seasons.index') }}" class="{{ request()->routeIs('admin.hajj-seasons.*') ? 'mm-active' : '' }}">
                    <i class="fas fa-calendar-alt"></i>
                    <span class="nav-text">Hajj Seasons</span>
                </a>
            </li>
            @endcan

        </ul>

        <div class="copyright">
            <p><strong>{{ config('app.name') }}</strong> &copy; {{ now()->year }}</p>
        </div>
    </div>
</div>
