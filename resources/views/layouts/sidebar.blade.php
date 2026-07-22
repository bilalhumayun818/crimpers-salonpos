<style>
    /* ── Sidebar palette based on #F0F2F5 ── */
    :root{
        --sb-bg:     #F0F2F5;   /* sidebar background */
        --sb-bg2:    #E8EAED;   /* slightly darker — default link bg */
        --sb-bg3:    #DDE0E5;   /* hover — darker gray */
        --sb-border: #D8DBE0;   /* borders / tree line */
        --sb-text:   #3C4048;   /* default text */
        --sb-muted:  #8A909A;   /* muted / section labels */
        --sb-active-bg: #F5EFC0; /* active — soft yellowish */
        --sb-active-text: #7A5C00; /* active text — warm dark gold */
        --sb-active-border: #D4B800; /* active border accent */
    }

    .sidebar {
        background: var(--sb-bg);
        border-right: 1px solid var(--sb-border);
        display: flex;
        flex-direction: column;
        height: 100vh;
        width: 260px;
        font-family: 'Outfit', sans-serif;
    }

    /* Logo Section */
    .sidebar-header {
        padding: 20px 16px;
        background: var(--sb-bg);
        border-bottom: 1px solid var(--sb-border);
    }
    .sidebar-logo-text {
        font-size: 1.1rem;
        font-weight: 800;
        color: #18181b;
        text-decoration: none;
        display: block;
    }
    .sidebar-logo-sub {
        display: block;
        font-size: 0.65rem;
        color: var(--sb-muted);
        font-weight: 600;
        letter-spacing: .03em;
        margin-top: 2px;
    }

    /* Section Labels */
    .sidebar-section-label {
        padding: 16px 16px 5px 16px;
        font-size: 0.6rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--sb-muted);
        letter-spacing: 0.1em;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Parent Links */
    .sidebar-link {
        display: flex;
        background: var(--sb-bg2);
        align-items: center;
        padding: 10px 12px;
        margin: 4px 10px;
        color: var(--sb-text);
        text-decoration: none;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 600;
        transition: background 0.18s, color 0.18s, box-shadow 0.18s;
        cursor: pointer;
        border: 1px solid transparent;
    }

    /* Hover — darker gray */
    .sidebar-link:hover:not(.active) {
        background: var(--sb-bg3);
        color: #18181b;
        border-color: transparent;
    }
    .sidebar-link:hover:not(.active) .sidebar-icon {
        color: #18181b;
    }

    /* Active — soft yellowish tint */
    .sidebar-link.active {
        background: var(--sb-active-bg) !important;
        color: var(--sb-active-text) !important;
        border-color: var(--sb-active-border) !important;
        box-shadow: 0 1px 6px rgba(212,184,0,.15);
    }
    .sidebar-link.active .sidebar-icon {
        color: var(--sb-active-text) !important;
    }

    /* Icon */
    .sidebar-icon {
        margin-right: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--sb-muted);
        flex-shrink: 0;
        transition: color 0.18s;
    }

    /* Chevron */
    .section-chevron {
        margin-left: auto;
        color: var(--sb-muted);
        transition: transform 0.2s, color 0.18s;
        transform: rotate(-90deg);
        flex-shrink: 0;
    }
    .nav-group.expanded .section-chevron {
        transform: rotate(0deg);
        color: var(--sb-active-text);
    }

    /* Sub-menu */
    .sub-menu {
        margin-left: 31px;
        border-left: 1.5px solid var(--sb-border);
        margin-bottom: 6px;
        padding-top: 2px;
        display: none;
    }
    .nav-group.expanded .sub-menu {
        display: block;
    }

    /* Sub-links — NO background change, only text color */
    .sub-link {
        display: block;
        padding: 7px 14px;
        margin: 2px 10px;
        color: var(--sb-muted);
        text-decoration: none;
        font-size: 0.82rem;
        font-weight: 500;
        transition: color 0.18s;
        border-radius: 9999px;
        background: transparent;
    }

    .sub-link:hover {
        color: #18181b;
        background: transparent;
    }

    /* Active sub-link — only text color changes, no background */
    .sub-link.active-sub {
        color: var(--sb-active-text);
        font-weight: 700;
        background: transparent;
    }
</style>

<aside id="appSidebar" class="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('admin.index') }}" class="sidebar-logo-text">The Crimpers</a>
        <span class="sidebar-logo-sub">POS &amp; Management</span>
    </div>

    <nav class="sidebar-nav" style="flex: 1; overflow-y: auto; padding-top: 10px;">


        @if(auth()->user()->hasPermission('dashboard', 'view'))
        <a href="{{ route('admin.index') }}" class="sidebar-link {{ request()->routeIs('admin.index') ? 'active' : '' }}">
            <span class="sidebar-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg></span>
            <span class="link-text">Dashboard</span>
        </a>
        @endif


        @if(auth()->user()->hasPermission('customers', 'view') || auth()->user()->hasPermission('suppliers', 'view'))
        <div class="nav-group">
            <div class="sidebar-link {{ request()->routeIs('customers.*', 'suppliers.*') ? 'active' : '' }}">
                <span class="sidebar-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg></span>
                <span class="link-text">Contacts</span>
                <svg class="section-chevron" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
            </div>
            <div class="sub-menu">
                @if(auth()->user()->hasPermission('customers', 'view'))
                <a href="{{ route('customers.index') }}" class="sub-link {{ request()->routeIs('customers.index') ? 'active-sub' : '' }}">All Clients</a>
                @endif
                @if(auth()->user()->hasPermission('customers', 'create'))
                <a href="{{ route('customers.create') }}" class="sub-link {{ request()->routeIs('customers.create') ? 'active-sub' : '' }}">Add New Client</a>
                @endif
                @if(auth()->user()->hasPermission('suppliers', 'view'))
                <a href="{{ route('suppliers.index') }}" class="sub-link {{ request()->routeIs('suppliers.index') ? 'active-sub' : '' }}">All Suppliers</a>
                @endif
                @if(auth()->user()->hasPermission('suppliers', 'create'))
                <a href="{{ route('suppliers.create') }}" class="sub-link {{ request()->routeIs('suppliers.create') ? 'active-sub' : '' }}">Add Supplier</a>
                @endif
            </div>
        </div>
        @endif

        @if(auth()->user()->hasPermission('inventory', 'view'))
        <div class="nav-group">
            <div class="sidebar-link {{ request()->routeIs('products.*', 'inventory.*') ? 'active' : '' }}">
                <span class="sidebar-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></span>
                <span class="link-text">Products</span>
                <svg class="section-chevron" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
            </div>
            <div class="sub-menu">
                @if(auth()->user()->hasPermission('inventory', 'view'))
                <a href="{{ route('products.index') }}" class="sub-link {{ request()->routeIs('products.index') ? 'active-sub' : '' }}">All Products</a>
                <a href="{{ route('inventory.dashboard') }}" class="sub-link {{ request()->routeIs('inventory.*') ? 'active-sub' : '' }}">Inventory Tracking</a>
                @endif
                @if(auth()->user()->hasPermission('inventory', 'create'))
                <a href="{{ route('products.create') }}" class="sub-link {{ request()->routeIs('products.create') ? 'active-sub' : '' }}">Add Product</a>
                @endif
            </div>
        </div>
        @endif

        @if(auth()->user()->hasPermission('inventory', 'view'))
        <div class="nav-group">
            <div class="sidebar-link {{ request()->routeIs('services.*', 'packages.*') ? 'active' : '' }}">
                <span class="sidebar-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg></span>
                <span class="link-text">Services</span>
                <svg class="section-chevron" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
            </div>
            <div class="sub-menu">
                <a href="{{ route('services.index') }}" class="sub-link {{ request()->routeIs('services.index') ? 'active-sub' : '' }}">All Services</a>
                <a href="{{ route('services.create') }}" class="sub-link {{ request()->routeIs('services.create') ? 'active-sub' : '' }}">Add Service</a>
                <a href="{{ route('packages.index') }}" class="sub-link {{ request()->routeIs('packages.index') ? 'active-sub' : '' }}">Service Packages</a>
                <a href="{{ route('packages.create') }}" class="sub-link {{ request()->routeIs('packages.create') ? 'active-sub' : '' }}">Add Packages</a>
            </div>
        </div>
        @endif




        @if(auth()->user()->hasPermission('pos', 'access') || auth()->user()->hasPermission('sales', 'view') || auth()->user()->hasPermission('appointments', 'view') || auth()->user()->hasPermission('purchases', 'view') || auth()->user()->hasPermission('reconciliation', 'access') || auth()->user()->hasPermission('reconciliation', 'view'))
        <div class="nav-group">
            <div class="sidebar-link {{ request()->routeIs('pos.*', 'invoices.*', 'appointments.*', 'reconciliation.*', 'purchases.*') ? 'active' : '' }}">
                <span class="sidebar-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg></span>
                <span class="link-text">Sell</span>
                <svg class="section-chevron" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
            </div>
            <div class="sub-menu">
                @if(auth()->user()->hasPermission('pos', 'access'))
                <a href="{{ route('pos.index') }}" class="sub-link {{ request()->routeIs('pos.*') ? 'active-sub' : '' }}">POS Terminal</a>
                @endif
                @if(auth()->user()->hasPermission('history', 'access'))
                <a href="{{ route('invoices.index') }}" class="sub-link {{ request()->routeIs('invoices.*') ? 'active-sub' : '' }}">History</a>
                @endif
                @if(auth()->user()->hasPermission('appointments', 'view'))
                <a href="{{ route('appointments.index') }}" class="sub-link {{ request()->routeIs('appointments.*') ? 'active-sub' : '' }}">Appointments</a>
                @endif

            </div>
        </div>
        @endif
        @if(auth()->user()->hasPermission('sales', 'view'))
        <div class="nav-group">
            <div class="sidebar-link {{ request()->routeIs('reconciliation.*', 'expenses.*') ? 'active' : '' }}">
                <span class="sidebar-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></span>
                <span class="link-text">Expenses & Cash</span>
                <svg class="section-chevron" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
            </div>
            <div class="sub-menu">
                <a href="{{ route('expenses.create') }}" class="sub-link {{ request()->routeIs('expenses.*') ? 'active-sub' : '' }}">Add Expense</a>
                <a href="{{ route('reconciliation.index') }}" class="sub-link {{ request()->routeIs('reconciliation.*') ? 'active-sub' : '' }}">Cash Reconciliation</a>
            </div>
        </div>
        @endif

        @if(auth()->user()->hasPermission('reports', 'view'))
        <div class="nav-group">
            <div class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <span class="sidebar-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></span>
                <span class="link-text">Reports</span>
                <svg class="section-chevron" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
            </div>
            <div class="sub-menu">
                <a href="{{ route('reports.index') }}" class="sub-link {{ request()->routeIs('reports.index') ? 'active-sub' : '' }}">Business Intelligence</a>
                <a href="{{ route('reports.pos') }}" class="sub-link {{ request()->routeIs('reports.pos') ? 'active-sub' : '' }}">POS Sales Report</a>
                <a href="{{ route('reports.staff') }}" class="sub-link {{ request()->routeIs('reports.staff') ? 'active-sub' : '' }}">Staff Details Report</a>
                <a href="{{ route('reports.attendance') }}" class="sub-link {{ request()->routeIs('reports.attendance') ? 'active-sub' : '' }}">Staff Attendance Report</a>
                <a href="{{ route('reports.salary') }}" class="sub-link {{ request()->routeIs('reports.salary') ? 'active-sub' : '' }}">Staff Salary Report</a>
                <a href="{{ route('reports.customer-purchases') }}" class="sub-link {{ request()->routeIs('reports.customer-purchases') ? 'active-sub' : '' }}">Customer Purchases</a>
                <a href="{{ route('reports.employee-customers') }}" class="sub-link {{ request()->routeIs('reports.employee-customers') ? 'active-sub' : '' }}">Employee-Customer Link</a>
                @if(auth()->user()->hasPermission('inventory', 'view'))
                <a href="{{ route('inventory.stock-report') }}" class="sub-link {{ request()->routeIs('inventory.stock-report') ? 'active-sub' : '' }}">Stock Report</a>
                <a href="{{ route('inventory.usage-report') }}" class="sub-link {{ request()->routeIs('inventory.usage-report') ? 'active-sub' : '' }}">Usage Report</a>
                @endif
            </div>
        </div>
        @endif

        @if(auth()->user()->hasPermission('admin', 'all') || auth()->user()->hasPermission('business', 'view'))
        <div class="nav-group">
            <div class="sidebar-link {{ request()->routeIs('staff-roles.index', 'business-settings.*') ? 'active' : '' }}">
                <span class="sidebar-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></span>
                <span class="link-text">Business</span>
                <svg class="section-chevron" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
            </div>
            <div class="sub-menu">
                <a href="{{ route('staff-roles.index') }}" class="sub-link {{ request()->routeIs('staff-roles.index') ? 'active-sub' : '' }}">Role Management</a>
                <a href="{{ route('business-settings.index') }}" class="sub-link {{ request()->routeIs('business-settings.*') ? 'active-sub' : '' }}">Business Settings</a>
            </div>
        </div>
        @endif

        @if(auth()->user()->hasPermission('staff', 'view'))
        <div class="nav-group">
            <div class="sidebar-link {{ request()->routeIs('staff.*', 'staff-hrms.*', 'staff.salary-dashboard') ? 'active' : '' }}">
                <span class="sidebar-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg></span>
                <span class="link-text">Employee Manage</span>
                <svg class="section-chevron" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
            </div>
            <div class="sub-menu">
                <a href="{{ route('staff.index') }}" class="sub-link {{ request()->routeIs('staff.index', 'staff.show', 'staff.edit') ? 'active-sub' : '' }}">All Employees</a>
                
                @if(auth()->user()->hasPermission('staff', 'create'))
                <a href="{{ route('staff.create') }}" class="sub-link {{ request()->routeIs('staff.create') ? 'active-sub' : '' }}">Add New Employee</a>
                @endif
                
                @if(auth()->user()->hasPermission('staff', 'attendance'))
                <a href="{{ route('staff.attendance-all') }}" class="sub-link {{ request()->routeIs('staff.attendance*') ? 'active-sub' : '' }}">Attendance Log</a>
                @endif

                <a href="{{ route('staff.salary-dashboard') }}" class="sub-link {{ request()->routeIs('staff.salary-dashboard') ? 'active-sub' : '' }}">Salary & Performance</a>
                <a href="{{ route('staff.hrms') }}" class="sub-link {{ request()->routeIs('staff.hrms*') ? 'active-sub' : '' }}">Staff HRMS</a>
            </div>
        </div>
        @endif

    </nav>

    @auth
    <div style="padding:12px 14px;border-top:1px solid #D8DBE0;background:#F0F2F5;flex-shrink:0;">
        <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:#E8EAED;border:1px solid #D8DBE0;border-radius:12px;">
            <div style="width:34px;height:34px;border-radius:9px;background:#3C4048;color:#fff;font-weight:800;font-size:.82rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;letter-spacing:.01em;">
                {{ strtoupper(substr(auth()->user()->name,0,1)) }}
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:.8rem;font-weight:700;color:#18181b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ auth()->user()->name }}</div>
                <div style="font-size:.6rem;color:#8A909A;text-transform:uppercase;font-weight:700;letter-spacing:.06em;">{{ ucfirst(auth()->user()->role ?? 'Admin') }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="flex-shrink:0;">
                @csrf
                <button type="submit" title="Logout"
                    style="width:32px;height:32px;border-radius:8px;background:#fef2f2;border:1.5px solid #fecaca;color:#dc2626;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .18s;flex-shrink:0;"
                    onmouseenter="this.style.background='#fee2e2';this.style.borderColor='#f87171';this.style.transform='scale(1.05)'"
                    onmouseleave="this.style.background='#fef2f2';this.style.borderColor='#fecaca';this.style.transform='scale(1)'">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </button>
            </form>
        </div>
    </div>
    @endauth
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navGroups = document.querySelectorAll('.nav-group');

        navGroups.forEach(group => {
            const parentLink = group.querySelector('.sidebar-link');
            const subMenu = group.querySelector('.sub-menu');

            if (parentLink && subMenu) {
                // Determine if this group should be open initially
                const isActive = parentLink.classList.contains('active') || subMenu.querySelector('.active-sub');
                if (isActive) {
                    group.classList.add('expanded');
                }

                // Toggle logic
                parentLink.addEventListener('click', function(e) {
                    group.classList.toggle('expanded');
                });
            }
        });
    });
</script>
