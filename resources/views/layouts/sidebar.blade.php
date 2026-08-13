<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
            @if(company_logo())
                <img src="{{ company_logo() }}" alt="logo" height="32" style="object-fit: contain;">
            @else
                <i class="bx bx-dumbbell fs-3 me-1"></i>
            @endif
            <span class="app-brand-text demo menu-text fw-bold ms-2">{{ company_name() }}</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm d-flex align-items-center justify-content-center"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-smile"></i>
                <div class="text-truncate" data-i18n="Dashboards">Dashboards</div>
            </a>
        </li>

        @canany(['roles.view', 'staff.view'])
            <li
                class="menu-item {{ request()->is('admin/roles_with_filter') || request()->is('admin/add_role') || request()->is('admin/edit_role/*') || request()->is('admin/staff') || request()->is('admin/add_staff') || request()->is('admin/edit_staff/*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-lock-alt"></i>
                    <div class="text-truncate">Access Control</div>
                </a>

                <ul class="menu-sub">
                    @can('roles.view')
                        <li
                            class="menu-item {{ request()->is('admin/roles_with_filter') || request()->is('admin/add_role') || request()->is('admin/edit_role/*') ? 'active' : '' }}">
                            <a href="{{ route('admin.roles.index') }}" class="menu-link">
                                <div class="text-truncate">Role Master</div>
                            </a>
                        </li>
                    @endcan
                    @can('staff.view')
                        <li
                            class="menu-item {{ request()->is('admin/staff') || request()->is('admin/add_staff') || request()->is('admin/edit_staff/*') ? 'active' : '' }}">
                            <a href="{{ route('admin.staff.index') }}" class="menu-link">
                                <div class="text-truncate">Staff</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @can('customers.view')
            <li class="menu-item {{ request()->is('admin/customers') ? 'active' : '' }}">
                <a href="{{ route('admin.customers.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-user"></i>
                    <div class="text-truncate">Customers</div>
                </a>
            </li>
        @endcan

        @canany(['lead-sources.view', 'lead-stages.view', 'lead-requirements.view', 'lost-reasons.view', 'followups.view', 'leads.view'])
            <li class="menu-item {{ request()->is('admin/lead-sources*') || request()->is('admin/lead-stages*') || request()->is('admin/lead-requirements*') || request()->is('admin/lost-reasons*') || request()->is('admin/followups*') || request()->is('admin/leads*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-target-lock"></i>
                    <div class="text-truncate">Leads Management</div>
                </a>
                <ul class="menu-sub">
                    @can('lead-sources.view')
                        <li class="menu-item {{ request()->is('admin/lead-sources*') ? 'active' : '' }}">
                            <a href="{{ route('admin.lead-sources.index') }}" class="menu-link">
                                <div class="text-truncate">Lead Sources</div>
                            </a>
                        </li>
                    @endcan
                    @can('lead-stages.view')
                        <li class="menu-item {{ request()->is('admin/lead-stages*') ? 'active' : '' }}">
                            <a href="{{ route('admin.lead-stages.index') }}" class="menu-link">
                                <div class="text-truncate">Lead Stages</div>
                            </a>
                        </li>
                    @endcan
                    @can('lead-requirements.view')
                        <li class="menu-item {{ request()->is('admin/lead-requirements*') ? 'active' : '' }}">
                            <a href="{{ route('admin.lead-requirements.index') }}" class="menu-link">
                                <div class="text-truncate">Lead Requirements</div>
                            </a>
                        </li>
                    @endcan
                    @can('lost-reasons.view')
                        <li class="menu-item {{ request()->is('admin/lost-reasons*') ? 'active' : '' }}">
                            <a href="{{ route('admin.lost-reasons.index') }}" class="menu-link">
                                <div class="text-truncate">Lost Reasons</div>
                            </a>
                        </li>
                    @endcan
                    @can('followups.view')
                        <li class="menu-item {{ request()->is('admin/followups*') ? 'active' : '' }}">
                            <a href="{{ route('admin.followups.index') }}" class="menu-link">
                                <div class="text-truncate">Follow-ups</div>
                            </a>
                        </li>
                    @endcan
                    @can('leads.view')
                        <li class="menu-item {{ request()->is('admin/leads*') ? 'active' : '' }}">
                            <a href="{{ route('admin.leads.index') }}" class="menu-link">
                                <div class="text-truncate">Leads</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @canany(['general-settings.view', 'referral-settings.view'])
            <li class="menu-item {{ request()->is('admin/settings/*') ? 'active' : '' }}">
                <a href="{{ route('admin.settings.general') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-cog"></i>
                    <div class="text-truncate">Settings</div>
                </a>

                {{-- <ul class="menu-sub">
                    @can('general-settings.view')
                        <li class="menu-item {{ request()->is('admin/settings/general') ? 'active' : '' }}">
                            <a href="{{ route('admin.settings.general') }}" class="menu-link">
                                <div class="text-truncate">General Settings</div>
                            </a>
                        </li>
                    @endcan
                    @can('referral-settings.view')
                        <li class="menu-item {{ request()->is('admin/settings/referral') ? 'active' : '' }}">
                            <a href="{{ route('admin.settings.referral') }}" class="menu-link">
                                <div class="text-truncate">Referral Settings</div>
                            </a>
                        </li>
                    @endcan
                </ul> --}}
            </li>
        @endcanany

    </ul>
</aside>
