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

            <li>
                <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-globe"></i>
                    <span class="nav-text">Master Data</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.countries.index') }}">Countries</a></li>
                    <li><a href="{{ route('admin.cities.index') }}">Cities</a></li>
                    <li><a href="{{ route('admin.currencies.index') }}">Currencies</a></li>
                    <li><a href="{{ route('admin.airlines.index') }}">Airlines</a></li>
                    <li><a href="{{ route('admin.hotels.index') }}">Hotels</a></li>
                    <li><a href="{{ route('admin.transporters.index') }}">Transporters</a></li>
                    <li><a href="{{ route('admin.guides.index') }}">Guides</a></li>
                    <li><a href="{{ route('admin.vendors.index') }}">Vendors</a></li>
                    <li><a href="{{ route('admin.taxes.index') }}">Taxes</a></li>
                </ul>
            </li>

            <li>
                <a href="{{ route('admin.companies.index') }}">
                    <i class="fas fa-building"></i>
                    <span class="nav-text">Company</span>
                </a>
            </li>

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
            <p><strong>Travel ERP</strong> &copy; {{ now()->year }} All Rights Reserved</p>
        </div>
    </div>
</div>