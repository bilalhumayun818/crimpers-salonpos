<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'The Crimpers') }} — @yield('title', 'Dashboard')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Outfit', sans-serif;
            background: #f3f4f6;
            color: #111827;
        }

        /* ─── Layout shell ─── */
        .app-shell {
            display: flex;
            min-height: 100vh;
        }

        /* ══════════════════════════════════════
           SIDEBAR
        ══════════════════════════════════════ */
        .sidebar {
            width: 240px;
            min-height: 100vh;
            background: #F0F2F5;
            border-right: 1px solid #D8DBE0;
            box-shadow: 2px 0 12px rgba(0,0,0,.06);
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0; top: 0; bottom: 0;
            z-index: 50;
            overflow-y: auto;
            overflow-x: hidden;
            transition: width .25s cubic-bezier(.4,0,.2,1), transform .25s cubic-bezier(.4,0,.2,1);
        }

        .sidebar.collapsed { width: 0; border-right: none; overflow: hidden; }

        /* Mobile: sidebar slides in as a full-height drawer */
        @media(max-width:768px){
            .sidebar {
                width: 260px !important;
                transform: translateX(-100%);
                box-shadow: none;
                z-index: 200;
            }
            .sidebar.mobile-open {
                transform: translateX(0);
                box-shadow: 4px 0 24px rgba(0,0,0,.18);
            }
        }

        /* Overlay backdrop for mobile drawer */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.45);
            z-index: 199;
            backdrop-filter: blur(2px);
        }
        .sidebar-backdrop.visible { display: block; }

        /* ── Header ── */
        .sidebar-header {
            background: #F0F2F5;
            border-bottom: 1px solid #D8DBE0;
            padding: 20px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 68px;
            flex-shrink: 0;
        }

        .sidebar-logo-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #18181b;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,.15);
        }

        .sidebar-logo-text-wrap {
            min-width: 0;
            overflow: hidden;
            transition: opacity .2s, width .25s;
            white-space: nowrap;
        }

        .sidebar.collapsed .sidebar-logo-text-wrap { opacity: 0; width: 0; pointer-events: none; }

        .sidebar-logo-text {
            font-size: 1rem;
            font-weight: 800;
            color: #18181b;
            line-height: 1.2;
            text-decoration: none;
            display: block;
            letter-spacing: -.02em;
        }

        .sidebar-logo-sub {
            font-size: 0.58rem;
            color: #a1a1aa;
            font-weight: 500;
            letter-spacing: .02em;
        }

        /* ── Nav ── */
        .sidebar-nav { flex: 1; padding: 10px 12px 20px; }

        /* ── Section labels ── */
        .sidebar-section-label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            border-radius: 8px;
            padding: 10px 8px 4px;
            transition: background .15s;
            user-select: none;
            margin-top: 8px;
        }

        .sidebar-section-label:hover { background: #fafafa; }

        .label-text {
            font-size: 0.6rem;
            font-weight: 700;
            color: #d4d4d8;
            text-transform: uppercase;
            letter-spacing: .12em;
            transition: color .15s, opacity .2s;
        }

        .sidebar-section-label.open .label-text { color: #a1a1aa; }
        .sidebar.collapsed .label-text { opacity: 0; pointer-events: none; }

        .section-chevron {
            flex-shrink: 0;
            color: #d4d4d8;
            transition: transform .22s ease, opacity .2s;
        }

        .sidebar-section-label.open .section-chevron { transform: rotate(180deg); color: #a1a1aa; }
        .sidebar.collapsed .section-chevron { opacity: 0; }

        .nav-section { overflow: hidden; max-height: 0; transition: max-height .3s cubic-bezier(.4,0,.2,1); }
        .nav-section.open { max-height: 800px; }

        /* ── Nav links ── */
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 10px;
            border-radius: 10px;
            color: #71717a;
            font-size: 0.83rem;
            font-weight: 500;
            text-decoration: none;
            transition: all .18s cubic-bezier(.4,0,.2,1);
            margin-bottom: 2px;
            position: relative;
            white-space: nowrap;
            overflow: hidden;
        }

        /* Hover — darker gray */
        .sidebar-link:hover {
            background: #DDE0E5;
            color: #18181b;
            transform: translateX(2px);
        }

        .sidebar-link:hover .sidebar-icon {
            color: #18181b;
            background: transparent;
        }

        /* Active — soft yellowish */
        .sidebar-link.active {
            background: #F5EFC0;
            color: #7A5C00;
            font-weight: 700;
            box-shadow: 0 1px 6px rgba(212,184,0,.15);
            border: 1px solid #D4B800;
        }

        .sidebar-link.active::before { display: none; }

        .sidebar-link.active .sidebar-icon {
            background: transparent;
            color: #7A5C00;
        }

        /* Icon box */
        .sidebar-icon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: transparent;
            color: #8A909A;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: background .18s, color .18s;
        }

        .link-text { transition: opacity .2s; overflow: hidden; }
        .sidebar.collapsed .link-text { opacity: 0; width: 0; }
        .sidebar.collapsed .sidebar-link { justify-content: center; padding: 9px 0; transform: none !important; }
        .sidebar.collapsed .sidebar-link .sidebar-icon { margin: 0 auto; }

        /* ══════════════════════════════════════
           TOP HEADER
        ══════════════════════════════════════ */
        .top-header {
            height: 56px;
            background: #F0F2F5;
            border-bottom: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 22px;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 40;
            box-shadow: 0 1px 0 #D8DBE0;
        }

        .top-header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Page title */
        .top-header-page-title {
            font-size: .875rem;
            font-weight: 700;
            color: #3C4048;
            letter-spacing: -.01em;
        }

        /* Sidebar toggle */
        .sidebar-toggle-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #E8EAED;
            border: none;
            color: #5C6370;
            cursor: pointer;
            transition: background .15s, color .15s;
            flex-shrink: 0;
        }

        .sidebar-toggle-btn:hover {
            background: #DDE0E5;
            color: #18181b;
        }

        .top-header-divider {
            width: 1px;
            height: 20px;
            background: #D8DBE0;
            flex-shrink: 0;
        }

        .top-header-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Generic top-bar chip */
        .top-header-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 13px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            font-family: 'Outfit', sans-serif;
            cursor: pointer;
            transition: background .15s, color .15s;
            border: none;
            text-decoration: none;
            color: #5C6370;
            background: #E8EAED;
        }

        .top-header-btn:hover {
            background: #DDE0E5;
            color: #18181b;
        }

        /* POS button — yellow accent, no border */
        .top-header-btn.pos-btn {
            background: #F5EFC0;
            color: #7A5C00;
            font-weight: 700;
            border: none;
        }

        .top-header-btn.pos-btn:hover {
            background: #EDE580;
            color: #5A4200;
            transform: translateY(-1px);
        }

        /* Date chip */
        .top-header-date {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.78rem;
            font-weight: 600;
            color: #5C6370;
            background: #E8EAED;
            border: none;
        }

        /* User chip */
        .top-header-user {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 4px 12px 4px 4px;
            border-radius: 99px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #3C4048;
            background: #E8EAED;
            border: none;
            transition: background .15s;
        }

        .top-header-user:hover {
            background: #DDE0E5;
        }

        .top-header-avatar {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: #3C4048;
            color: #fff;
            font-weight: 800;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* ══════════════════════════════════════
           MAIN CONTENT
        ══════════════════════════════════════ */
        .main-content {
            flex: 1;
            margin-left: 240px;
            min-height: 100vh;
            background: #F4F5F7;
            display: flex;
            flex-direction: column;
            transition: margin-left .25s cubic-bezier(.4,0,.2,1);
        }

        .main-content.sidebar-collapsed { margin-left: 0; }

        /* Mobile: content always full width */
        @media(max-width:768px){
            .main-content { margin-left: 0 !important; }
        }

        .main-body {
            flex: 1 1 auto;
            padding: 28px;
            min-height: 0;
        }

        @media(max-width:640px){
            .main-body { padding: 16px; }
        }

        .main-footer {
            padding: 16px;
            text-align: center;
            font-size: 0.72rem;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            margin-top: auto;
        }

        /* ══════════════════════════════════════
           SHARED COMPONENTS
        ══════════════════════════════════════ */
        .card {
            background: #ffffff;
            border: 1px solid #dcfce7;
            border-radius: 16px;
            box-shadow: 0 1px 6px rgba(34,197,94,.06);
        }

        .btn-primary {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: #ffffff;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: all .25s;
            font-family: 'Outfit', sans-serif;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(34,197,94,.35);
        }

        .btn-primary:disabled {
            opacity: .5;
            cursor: not-allowed;
            transform: none;
        }

        .badge-green { background:#dcfce7; color:#15803d; border-radius:99px; padding:2px 10px; font-size:.72rem; font-weight:600; }
        .badge-blue  { background:#dbeafe; color:#1d4ed8; border-radius:99px; padding:2px 10px; font-size:.72rem; font-weight:600; }
        .badge-amber { background:#fef3c7; color:#92400e; border-radius:99px; padding:2px 10px; font-size:.72rem; font-weight:600; }
        .badge-red   { background:#fee2e2; color:#b91c1c; border-radius:99px; padding:2px 10px; font-size:.72rem; font-weight:600; }

        .gradient-text {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* ── Tables ── */
        table { width: 100%; border-collapse: collapse; }
        table thead tr { background: #f0fdf4; }
        table thead th { color: #15803d; font-size: .72rem; text-transform: uppercase; letter-spacing: .06em; padding: 14px 20px; font-weight: 700; text-align: left; }
        table tbody tr { border-bottom: 1px solid #f0fdf4; transition: background .15s; }
        table tbody tr:hover { background: #f9fffe; }
        table tbody td { padding: 14px 20px; font-size: .875rem; }

        /* ── Inputs ── */
        input[type=text], input[type=email], input[type=password],
        input[type=number], input[type=tel], select, textarea {
            border: 1px solid #f0e8a0;
            border-radius: 10px;
            background: #f9fafb;
            color: #1e293b;
            font-family: 'Outfit', sans-serif;
            transition: border-color .2s, box-shadow .2s;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #F7DF79;
            box-shadow: 0 0 0 3px rgba(247,223,121,.2);
            background: #ffffff;
        }

        /* ── Branch Switcher ── */
        .branch-switcher-wrap {
            margin-right: 12px;
            position: relative;
            display: flex;
            align-items: center;
        }

        .branch-select {
            appearance: none;
            background: #fff;
            border: 1.5px solid #F0F2F5;
            border-radius: 99px;
            padding: 6px 36px 6px 14px;
            font-size: 0.78rem;
            font-weight: 700;
            color: #475569;
            cursor: pointer;
            transition: all .2s;
            box-shadow: 0 2px 6px rgba(0,0,0,.03);
            font-family: inherit;
        }

        .branch-select:hover {
            border-color: #F7DF79;
            color: #1e293b;
            box-shadow: 0 4px 10px rgba(0,0,0,.06);
        }

        .branch-switcher-icon {
            position: absolute;
            right: 12px;
            pointer-events: none;
            color: #94a3b8;
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #f5f6fa; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
        /* Hide POS button on mobile — not usable on small screens */
        @media(max-width:768px){
            #pos-header-btn { display: none !important; }
            .top-header-date { display: none; }
            .branch-switcher-wrap { display: none; }
            .top-header-user span { display: none; }
            .top-header-user { padding: 4px; border-radius: 50%; }
        }

        /* ─── Mobile Restriction: hide navigation chrome ─── */
        @media(max-width: 768px) {
            /* Hide sidebar entirely */
            #appSidebar,
            .sidebar {
                display: none !important;
            }
            /* Hide sidebar toggle button */
            #sidebarToggle {
                display: none !important;
            }
            /* Hide backdrop */
            #sidebar-backdrop {
                display: none !important;
            }
            /* Hide panel navigation links (View All, View) */
            .panel-link {
                display: none;
            }
        }
    </style>
</head>
<body>
<div id="sidebar-backdrop" class="sidebar-backdrop"></div>
<div class="app-shell">

    @include('layouts.sidebar')

    <div class="main-content" id="mainContent">

        {{-- Top Header --}}
        <header class="top-header">
            <div class="top-header-left">
                {{-- Sidebar collapse toggle --}}
                <button class="sidebar-toggle-btn" id="sidebarToggle" title="Toggle sidebar" aria-label="Toggle sidebar">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <path d="M9 3v18"/>
                        <path d="M14 9l-2 3 2 3"/>
                    </svg>
                </button>
                <div class="top-header-divider"></div>
                <span class="top-header-page-title">@yield('title', 'Dashboard')</span>
            </div>

            <div class="top-header-right">
                {{-- Branch Switcher / Location Badge --}}
                @if(auth()->check())
                    @if(auth()->user()->role === 'admin' || is_null(auth()->user()->branch_id))
                        {{-- Global Switcher for Admins/Global Users --}}
                        <div class="branch-switcher-wrap">
                            <form action="{{ route('branch.switch') }}" method="POST" id="branchSwitchForm">
                                @csrf
                                <select name="branch_id" class="branch-select" onchange="document.getElementById('branchSwitchForm').submit()">
                                    @foreach(\App\Models\Branch::where('is_active', true)->get() as $branch)
                                        <option value="{{ $branch->id }}" {{ session('current_branch_id', 1) == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }} Branch
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                            <div class="branch-switcher-icon">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                            </div>
                        </div>
                    @else
                        {{-- Strict Location Badge for Locked Staff --}}
                        <div class="top-header-date" style="background:#f1f5f9; color:#64748b; border:1px solid #e2e8f0; margin-right:8px;">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            {{ auth()->user()->branch->name ?? 'Assigned Branch' }}
                        </div>
                    @endif
                @endif

                {{-- Date chip --}}
                <div class="top-header-date">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    {{ now()->format('M j, Y') }}
                </div>

                {{-- POS button: hidden on mobile (POS not suitable for small screens) --}}
                <a href="{{ route('pos.index') }}" class="top-header-btn pos-btn" id="pos-header-btn">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    POS Terminal
                </a>

                @auth
                <div class="top-header-user">
                    <div class="top-header-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <span>{{ auth()->user()->name }}</span>
                </div>
                @endauth
            </div>
        </header>

        <div class="main-body">
            @if($errors->any())
                <div style="background: #fee2e2; border: 1px solid #ef4444; color: #b91c1c; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif
            
            @if(session('success'))
                <div style="background: #dcfce7; border: 1px solid #22c55e; color: #15803d; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div style="background: #fee2e2; border: 1px solid #ef4444; color: #b91c1c; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

</div>

<script>
(function () {
    const sidebar    = document.getElementById('appSidebar');
    const mainContent = document.getElementById('mainContent');
    const toggleBtn  = document.getElementById('sidebarToggle');
    const backdrop   = document.getElementById('sidebar-backdrop');
    const isMobile   = () => window.innerWidth <= 768;

    // ── Mobile drawer toggle ──────────────────────────────────────
    function openMobileDrawer() {
        sidebar.classList.add('mobile-open');
        backdrop.classList.add('visible');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileDrawer() {
        sidebar.classList.remove('mobile-open');
        backdrop.classList.remove('visible');
        document.body.style.overflow = '';
    }

    backdrop.addEventListener('click', closeMobileDrawer);

    // ── Sidebar collapse (desktop) ──────────────────────────────
    const COLLAPSED_KEY = 'sm_sidebar_collapsed';

    function applySidebarState(collapsed, animate) {
        if (!animate) {
            sidebar.style.transition    = 'none';
            mainContent.style.transition = 'none';
        }
        sidebar.classList.toggle('collapsed', collapsed);
        mainContent.classList.toggle('sidebar-collapsed', collapsed);
        if (!animate) {
            sidebar.offsetHeight;
            sidebar.style.transition    = '';
            mainContent.style.transition = '';
        }
    }

    // Restore desktop state on load (no animation)
    if (!isMobile()) {
        const isCollapsed = localStorage.getItem(COLLAPSED_KEY) === '1';
        applySidebarState(isCollapsed, false);
    }

    toggleBtn.addEventListener('click', function () {
        if (isMobile()) {
            // Mobile: toggle drawer
            if (sidebar.classList.contains('mobile-open')) {
                closeMobileDrawer();
            } else {
                openMobileDrawer();
            }
        } else {
            // Desktop: collapse/expand sidebar
            const nowCollapsed = !sidebar.classList.contains('collapsed');
            applySidebarState(nowCollapsed, true);
            localStorage.setItem(COLLAPSED_KEY, nowCollapsed ? '1' : '0');
        }
    });

    // Close mobile drawer when a nav link is clicked
    sidebar.querySelectorAll('.sidebar-link').forEach(function(link) {
        link.addEventListener('click', function() {
            if (isMobile()) closeMobileDrawer();
        });
    });

    // Handle resize: close mobile drawer if resizing to desktop
    window.addEventListener('resize', function() {
        if (!isMobile()) {
            closeMobileDrawer();
            const isCollapsed = localStorage.getItem(COLLAPSED_KEY) === '1';
            applySidebarState(isCollapsed, false);
        }
    });

    // ── Section collapse (accordion) ──────────────────────────────
    const SECTIONS_KEY = 'sm_sections_open';

    function getOpenSections() {
        try {
            const stored = localStorage.getItem(SECTIONS_KEY);
            return stored ? JSON.parse(stored) : { admin: true, operations: true, hr: true, inventory: true, reports: true, whatsapp: true, promotions: true };
        } catch { return { admin: true, operations: true, hr: true, inventory: true, reports: true, whatsapp: true, promotions: true }; }
    }

    function saveOpenSections(map) {
        localStorage.setItem(SECTIONS_KEY, JSON.stringify(map));
    }

    const openMap = getOpenSections();
    const allLabels = document.querySelectorAll('.sidebar-section-label');

    // Determine which section is currently active (has an active link)
    let activeSection = null;
    allLabels.forEach(function(label) {
        const section = label.dataset.section;
        const body = document.getElementById('section-' + section);
        if (body && body.querySelector('.sidebar-link.active')) {
            activeSection = section;
        }
    });

    // Apply initial state — no transition
    allLabels.forEach(function(label) {
        const section = label.dataset.section;
        const body = document.getElementById('section-' + section);
        if (!body) return;

        body.style.transition = 'none';
        // Open if: stored as open, OR contains the active link
        const shouldOpen = openMap[section] === true || section === activeSection;
        if (shouldOpen) {
            body.classList.add('open');
            label.classList.add('open');
            openMap[section] = true;
        } else {
            body.classList.remove('open');
            label.classList.remove('open');
        }
        body.offsetHeight;
        body.style.transition = '';
    });

    // Click handler — accordion: close others, open clicked
    allLabels.forEach(function(label) {
        const section = label.dataset.section;
        const body = document.getElementById('section-' + section);
        if (!body) return;

        label.addEventListener('click', function () {
            const isOpen = body.classList.contains('open');

            if (isOpen) {
                // Close this one
                body.classList.remove('open');
                label.classList.remove('open');
                openMap[section] = false;
            } else {
                // Close all others first
                allLabels.forEach(function(otherLabel) {
                    const otherSection = otherLabel.dataset.section;
                    const otherBody = document.getElementById('section-' + otherSection);
                    if (otherSection !== section && otherBody) {
                        otherBody.classList.remove('open');
                        otherLabel.classList.remove('open');
                        openMap[otherSection] = false;
                    }
                });
                // Open this one
                body.classList.add('open');
                label.classList.add('open');
                openMap[section] = true;
            }

            saveOpenSections(openMap);
        });
    });
})();
</script>

<div id="global-appointment-alert" style="display:none; position:fixed; top:20px; right:24px; background:#fff; border-left:4px solid #F7DF79; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.15); padding:16px 20px; z-index:9999; max-width:350px; animation: slide-in-right 0.3s ease-out;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
        <div>
            <div style="font-size:0.8rem; font-weight:700; color:#c9a800; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Client Arriving Now</div>
            <div id="ga-client-name" style="font-size:1.1rem; font-weight:700; color:#1e293b; margin-bottom:2px;">Name</div>
            <div id="ga-client-details" style="font-size:0.8rem; color:#64748b; margin-bottom:4px;">Service · Staff</div>
            <div id="ga-client-time" style="font-size:0.85rem; font-weight:600; color:#c9a800; margin-bottom:12px;">Time</div>
        </div>
        <button onclick="dismissGlobalAlert()" style="background:none; border:none; cursor:pointer; color:#94a3b8; padding:2px;"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <div style="display:flex; gap:8px;">
        <button id="ga-btn-arrived" style="flex:1; padding:8px 0; border:none; border-radius:8px; background:#dcfce7; color:#166534; font-weight:600; font-size:0.8rem; cursor:pointer; font-family:'Outfit',sans-serif; transition:.2s;">Arrived</button>
        <button id="ga-btn-late" style="flex:1; padding:8px 0; border:none; border-radius:8px; background:#fef9c3; color:#854d0e; font-weight:600; font-size:0.8rem; cursor:pointer; font-family:'Outfit',sans-serif; transition:.2s;">Late</button>
        <button id="ga-btn-discard" style="flex:1; padding:8px 0; border:none; border-radius:8px; background:#fee2e2; color:#991b1b; font-weight:600; font-size:0.8rem; cursor:pointer; font-family:'Outfit',sans-serif; transition:.2s;">No Show</button>
    </div>
</div>

<style>
@keyframes slide-in-right { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
@keyframes glow-arriving { 0% { box-shadow: 0 0 0 0 rgba(247, 223, 121, 0.7); } 70% { box-shadow: 0 0 0 10px rgba(247, 223, 121, 0); } 100% { box-shadow: 0 0 0 0 rgba(247, 223, 121, 0); } }
@keyframes glow-discarded { 0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.5); } 70% { box-shadow: 0 0 0 12px rgba(239, 68, 68, 0); } 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); } }
.row-arriving { animation: glow-arriving 2s infinite; background:#fffdf0 !important; }
.row-discarded { animation: glow-discarded 2s infinite; background:#fef2f2 !important; }
</style>

<script>
let activeAlertAppointmentId = null;

function dismissGlobalAlert() {
    document.getElementById('global-appointment-alert').style.display = 'none';
}

function updateGlobalStatus(status) {
    if (!activeAlertAppointmentId) return;
    
    fetch(`{{ url('appointments') }}/${activeAlertAppointmentId}/update-status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: status })
    }).then(r => r.json()).then(data => {
        if (data.success) {
            dismissGlobalAlert();
            
            // Redirect to edit page for Arrived or Late, otherwise just refresh
            if (status === 'arrived' || status === 'late') {
                window.location.href = `{{ url('appointments') }}/${activeAlertAppointmentId}/edit`;
            } else {
                if (window.location.pathname.includes('/appointments') || window.location.pathname === '/' || window.location.pathname.includes('/admin')) {
                    window.location.reload();
                }
            }
        }
    });
}

document.getElementById('ga-btn-arrived').addEventListener('click', () => updateGlobalStatus('arrived'));
document.getElementById('ga-btn-late').addEventListener('click', () => updateGlobalStatus('late'));
document.getElementById('ga-btn-discard').addEventListener('click', () => updateGlobalStatus('discarded'));

function pollArrivingAppointments() {
    fetch('{{ url("/appointments/due-now") }}', {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => {
        if (!r.ok) throw new Error('Network error');
        return r.json();
    })
    .then(appointments => {
        // Remove old glows
        document.querySelectorAll('.row-arriving').forEach(el => el.classList.remove('row-arriving'));

        if (appointments && appointments.length > 0) {
            // Use the first arriving appointment for the popup notification
            const appt = appointments[0];
            
            activeAlertAppointmentId = appt.id;
            document.getElementById('ga-client-name').innerText = appt.customer_name;
            document.getElementById('ga-client-details').innerText = `${appt.service} · ${appt.staff}`;
            document.getElementById('ga-client-time').innerText = appt.start_time;
            document.getElementById('global-appointment-alert').style.display = 'block';

            // Glow all rows corresponding to arriving appointments
            appointments.forEach(a => {
                const row = document.getElementById('appt-row-' + a.id);
                if (row) row.classList.add('row-arriving');
            });
        } else {
            dismissGlobalAlert();
            activeAlertAppointmentId = null;
        }
    })
    .catch(err => console.log('Polling error', err));
}

// Poll every 30 seconds
setInterval(pollArrivingAppointments, 30000);
// Initial poll
setTimeout(pollArrivingAppointments, 2000);
</script>

@stack('scripts')
</body>
</html>
