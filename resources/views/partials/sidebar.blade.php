<div class="dlabnav">
    <div class="dlabnav-scroll">
        <ul class="metismenu" id="menu">

            <li>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'mm-active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            @canany(['branches.view','departments.view','employees.view'])
            <li>
                <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-building"></i>
                    <span class="nav-text">Organization</span>
                </a>
                <ul aria-expanded="false">
                    @can('branches.view')
                    <li><a href="{{ route('admin.branches.index') }}">Branches</a></li>
                    @endcan
                    @can('departments.view')
                    <li><a href="{{ route('admin.departments.index') }}">Departments</a></li>
                    @endcan
                    @can('employees.view')
                    <li><a href="{{ route('admin.employees.index') }}">Employees</a></li>
                    @endcan
                </ul>
            </li>
            @endcanany

            @canany(['leads.view','channels.view','campaigns.view','lead-statuses.view','qualified-statuses.view','services.view','sub-services.view'])
            <li>
                <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-chart-line"></i>
                    <span class="nav-text">CRM</span>
                </a>
                <ul aria-expanded="false">
                    @can('leads.view')
                    <li><a href="{{ route('admin.leads.dashboard') }}">Lead Dashboard</a></li>
                    <li><a href="{{ route('admin.leads.index') }}">Leads</a></li>
                    @endcan
                    @can('channels.view')
                    <li><a href="{{ route('admin.channels.index') }}">Channels</a></li>
                    @endcan
                    @can('campaigns.view')
                    <li><a href="{{ route('admin.campaigns.index') }}">Campaigns</a></li>
                    @endcan
                    @can('lead-statuses.view')
                    <li><a href="{{ route('admin.lead-statuses.index') }}">Lead Statuses</a></li>
                    @endcan
                    @can('qualified-statuses.view')
                    <li><a href="{{ route('admin.qualified-statuses.index') }}">Qualified Statuses</a></li>
                    @endcan
                    @can('services.view')
                    <li><a href="{{ route('admin.services.index') }}">Services</a></li>
                    @endcan
                    @can('sub-services.view')
                    <li><a href="{{ route('admin.sub-services.index') }}">Sub Services</a></li>
                    @endcan
                </ul>
            </li>
            @endcanany

            @canany(['customers.view','suppliers.view'])
            <li>
                <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-users"></i>
                    <span class="nav-text">Parties</span>
                </a>
                <ul aria-expanded="false">
                    @can('customers.view')
                    <li><a href="{{ route('admin.customers.index') }}">Customers</a></li>
                    @endcan
                    @can('suppliers.view')
                    <li><a href="{{ route('admin.suppliers.index') }}">Suppliers</a></li>
                    @endcan
                </ul>
            </li>
            @endcanany

            @canany(['countries.view','cities.view','currencies.view','airlines.view','hotels.view','transporters.view','guides.view','vendors.view','taxes.view'])
            <li>
                <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-globe"></i>
                    <span class="nav-text">Master Data</span>
                </a>
                <ul aria-expanded="false">
                    @can('countries.view')
                    <li><a href="{{ route('admin.countries.index') }}">Countries</a></li>
                    @endcan
                    @can('cities.view')
                    <li><a href="{{ route('admin.cities.index') }}">Cities</a></li>
                    @endcan
                    @can('currencies.view')
                    <li><a href="{{ route('admin.currencies.index') }}">Currencies</a></li>
                    @endcan
                    @can('airlines.view')
                    <li><a href="{{ route('admin.airlines.index') }}">Airlines</a></li>
                    @endcan
                    @can('hotels.view')
                    <li><a href="{{ route('admin.hotels.index') }}">Hotels</a></li>
                    @endcan
                    @can('transporters.view')
                    <li><a href="{{ route('admin.transporters.index') }}">Transporters</a></li>
                    @endcan
                    @can('guides.view')
                    <li><a href="{{ route('admin.guides.index') }}">Guides</a></li>
                    @endcan
                    @can('vendors.view')
                    <li><a href="{{ route('admin.vendors.index') }}">Vendors</a></li>
                    @endcan
                    @can('taxes.view')
                    <li><a href="{{ route('admin.taxes.index') }}">Taxes</a></li>
                    @endcan
                </ul>
            </li>
            @endcanany

            @can('companies.view')
            <li>
                <a href="{{ route('admin.companies.index') }}">
                    <i class="fas fa-building"></i>
                    <span class="nav-text">Company</span>
                </a>
            </li>
            @endcan

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

        </ul>

        <div class="copyright">
            <p><strong>{{ config('app.name') }}</strong> &copy; {{ now()->year }}</p>
        </div>
    </div>
</div>