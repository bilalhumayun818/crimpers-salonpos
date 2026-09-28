@extends('layouts.app')
@section('title', 'Daily Sheet Report - ' . $branch->name)

@section('content')
<style>
    :root {
        --y1: #F7DF79;
        --y2: #FBEFBC;
        --yd: #c9a800;
        --yk: #a07800;
        --ybg: #fffdf0;
    }

    /* Page Header consistent with app style */
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
        padding: 18px 24px;
        background: #fff;
        border: 1.5px solid #e9e0c0;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(199, 168, 0, 0.06);
    }
    .page-header h2 {
        font-size: 1.35rem;
        font-weight: 800;
        color: #18181b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-input {
        padding: 8px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.88rem;
        font-family: 'Outfit', sans-serif;
        outline: none;
        background: #fff;
    }
    .filter-input:focus {
        border-color: #F7DF79;
        box-shadow: 0 0 0 3px rgba(247, 223, 121, 0.2);
    }

    /* Outer Wrapper Card */
    .sheet-card {
        background: #ffffff;
        border: 1.5px solid #e9e0c0;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(199, 168, 0, 0.06);
        max-width: 1440px;
        margin: 0 auto 40px auto;
    }

    /* Paper Container inside card */
    .sheet-container {
        border: 2px solid #18181b;
        border-radius: 10px;
        padding: 20px;
        background: #fff;
    }

    /* Print styling */
    @media print {
        body { background: #fff !important; color: #000 !important; }
        .no-print, header, nav, aside, .sidebar, #appSidebar, .top-header, .page-header { display: none !important; }
        .sheet-card { border: none !important; box-shadow: none !important; margin: 0 !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }
        .sheet-container { border: 1.5px solid #000 !important; padding: 10px !important; }
        .sheet-table, .sheet-table th, .sheet-table td { border-color: #000 !important; }
        input, textarea, select { border: none !important; background: transparent !important; box-shadow: none !important; }
    }

    /* Header Section inside paper */
    .sheet-header-title {
        font-size: 1.9rem;
        font-weight: 900;
        letter-spacing: 0.05em;
        color: #18181b;
        text-transform: uppercase;
        line-height: 1;
    }

    .sheet-header-date {
        font-size: 1.7rem;
        font-weight: 800;
        color: #18181b;
        text-align: right;
    }

    /* Paper Sheet Tables */
    .sheet-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
        font-size: 0.85rem;
    }

    .sheet-table th, .sheet-table td {
        border: 1px solid #27272a;
        padding: 4px 6px;
        text-align: center;
        vertical-align: middle;
    }

    .sheet-table th {
        background: #f4f4f5;
        font-weight: 800;
        color: #18181b;
        text-transform: uppercase;
        font-size: 0.78rem;
        letter-spacing: 0.02em;
    }

    /* Input Cells */
    .sheet-input {
        width: 100%;
        border: none;
        outline: none;
        background: transparent;
        text-align: center;
        font-family: inherit;
        font-size: 0.85rem;
        font-weight: 600;
        color: #000;
        padding: 2px 4px;
    }

    .sheet-input:focus {
        background: var(--y2);
        border-radius: 4px;
    }

    .sheet-input-left {
        text-align: left;
    }

    .sheet-textarea {
        width: 100%;
        border: none;
        outline: none;
        background: transparent;
        font-family: inherit;
        font-size: 0.82rem;
        resize: vertical;
        padding: 6px;
        color: #000;
    }
    .sheet-textarea:focus {
        background: var(--ybg);
    }

    /* Labels */
    .row-label {
        font-weight: 800;
        background: #fafafa;
        color: #18181b;
        text-align: left !important;
        padding-left: 8px !important;
        white-space: nowrap;
        width: 90px;
    }

    .row-total {
        font-weight: 900;
        background: #f4f4f5;
        color: #000;
    }

    /* Column Sizing */
    .staff-col {
        min-width: 90px;
    }
    .discount-col {
        min-width: 95px;
        background: #fafafa;
    }

    .btn-action {
        padding: 9px 18px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
        border: 1px solid transparent;
        text-decoration: none;
    }
    .btn-save { background: linear-gradient(135deg, #16a34a, #15803d); color: #fff; }
    .btn-save:hover { background: #15803d; transform: translateY(-1px); }
    .btn-print { background: #18181b; color: #fff; }
    .btn-print:hover { background: #27272a; transform: translateY(-1px); }

    /* Summary labels table on bottom right */
    .summary-table {
        width: 100%;
        border-collapse: collapse;
    }
    .summary-table td {
        padding: 3px 6px;
        font-size: 0.8rem;
        border: 1px solid #27272a;
    }
    .summary-label {
        font-weight: 800;
        text-align: left;
        background: #f4f4f5;
        width: 70px;
    }
    .summary-val-td {
        text-align: right;
        font-weight: 700;
    }
</style>

{{-- Page Header --}}
<div class="page-header no-print">
    <div>
        <h2>
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="color:var(--yk);"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Daily Sheet Paper Report
        </h2>
        <span style="font-size:0.82rem; color:#64748b; font-weight:600; margin-top:2px; display:block;">
            Branch: <strong style="color:#18181b;">{{ $branch->name }}</strong> &nbsp;·&nbsp; Report Date: <strong style="color:#18181b;">{{ $formattedHeaderDate }}</strong>
        </span>
    </div>

    <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
        <form method="GET" action="{{ route('reports.daily') }}" style="display: flex; align-items: center; gap: 8px;">
            <label style="font-weight: 700; font-size: 0.8rem; color: #64748b; text-transform: uppercase;">Date:</label>
            <input type="date" name="date" value="{{ $dateStr }}" class="filter-input" style="font-weight: 700; padding: 7px 12px;" onchange="this.form.submit()">
        </form>

        <a href="{{ route('reports.daily-history') }}" class="btn-action" style="background: #e4e4e7; color: #18181b; font-weight: 700; text-decoration: none;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Daily Sheet History
        </a>

        <span id="saveStatus" style="font-size: 0.82rem; font-weight: 800; color: #16a34a; display: none;">✓ Saved</span>

        <button type="button" class="btn-action btn-save" onclick="saveReport()">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Save Daily Report
        </button>

        <button type="button" class="btn-action btn-print" onclick="window.print()">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Print Sheet
        </button>
    </div>
</div>

{{-- Top Financial & Savings Banner --}}
<div class="no-print" style="max-width: 1440px; margin: 0 auto 20px auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
    <div style="background: linear-gradient(135deg, #18181b, #27272a); border-radius: 16px; padding: 18px 22px; color: #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <div style="font-size: 0.75rem; font-weight: 700; color: #a1a1aa; text-transform: uppercase; letter-spacing: 0.05em;">Total Daily Revenue (Income)</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: #F7DF79; margin-top: 4px;">
            PKR <span id="savings_income_display">0</span>
        </div>
        <div style="font-size: 0.72rem; color: #d4d4d8; margin-top: 2px;">Gross Sales Recorded</div>
    </div>
    
    <div style="background: #fff; border: 1.5px solid #fecaca; border-radius: 16px; padding: 18px 22px; box-shadow: 0 4px 15px rgba(220,38,38,0.05);">
        <div style="font-size: 0.75rem; font-weight: 700; color: #991b1b; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; justify-content: space-between;">
            <span>Drawer Expenses</span>
            <span style="background: #fee2e2; color: #991b1b; padding: 2px 7px; border-radius: 6px; font-size: 0.65rem; font-weight: 800;">From Cash Drawer</span>
        </div>
        <div style="font-size: 1.5rem; font-weight: 900; color: #dc2626; margin-top: 4px;">
            PKR {{ number_format($drawerExpensesTotal, 2) }}
        </div>
        <div style="font-size: 0.72rem; color: #991b1b; margin-top: 2px;">Deducted directly from Cash Register</div>
    </div>

    <div style="background: #fff; border: 1.5px solid #fed7aa; border-radius: 16px; padding: 18px 22px; box-shadow: 0 4px 15px rgba(234,88,12,0.05);">
        <div style="font-size: 0.75rem; font-weight: 700; color: #9a3412; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; justify-content: space-between;">
            <span>Non-Drawer Expenses</span>
            <span style="background: #ffedd5; color: #9a3412; padding: 2px 7px; border-radius: 6px; font-size: 0.65rem; font-weight: 800;">Paid Outside</span>
        </div>
        <div style="font-size: 1.5rem; font-weight: 900; color: #ea580c; margin-top: 4px;">
            PKR {{ number_format($nonDrawerExpensesTotal, 2) }}
        </div>
        <div style="font-size: 0.72rem; color: #9a3412; margin-top: 2px;">Paid Personal / Bank / External</div>
    </div>

    <div style="background: linear-gradient(135deg, #059669, #15803d); border-radius: 16px; padding: 18px 22px; color: #fff; box-shadow: 0 4px 15px rgba(22,163,74,0.15);">
        <div style="font-size: 0.75rem; font-weight: 700; color: #a7f3d0; text-transform: uppercase; letter-spacing: 0.05em;">Net Daily Savings</div>
        <div style="font-size: 1.5rem; font-weight: 900; color: #ffffff; margin-top: 4px;">
            PKR <span id="savings_net_display">0</span>
        </div>
        <div style="font-size: 0.72rem; color: #d1fae5; margin-top: 2px;">(Total Revenue − Total Expenses)</div>
    </div>
</div>

{{-- Outer Card --}}
<div class="sheet-card">
    {{-- Inner Paper Container --}}
    <div class="sheet-container" id="dailyReportSheet">
        
        {{-- 1. Header --}}
        <div style="display: flex; justify-content: space-between; align-items: flex-end; padding-bottom: 12px; border-bottom: 2.5px solid #18181b; margin-bottom: 14px;">
            <div>
                <div class="sheet-header-title">{{ strtoupper($branch->name) }}</div>
                <div style="font-size: 0.75rem; font-weight: 700; color: #52525b; letter-spacing: 0.05em; margin-top: 2px;">DAILY BRANCH REPORT SHEET</div>
            </div>
            <div class="sheet-header-date">
                {{ $formattedHeaderDate }}
            </div>
        </div>

        @php
            $savedSales = $report->staff_sales_data ?? [];
            $savedStatus = $report->staff_status_data ?? [];
            $savedSummary = $report->summary_data ?? [];

            $maxSavedRow = 0;
            if (!empty($savedSales['rows']) && is_array($savedSales['rows'])) {
                $maxSavedRow = max(array_keys($savedSales['rows']));
            }
            $maxAutoRow = 0;
            if (!empty($autoSales['rows']) && is_array($autoSales['rows'])) {
                $maxAutoRow = max(array_keys($autoSales['rows']));
            }
            $totalSalesRows = max(20, $maxSavedRow, $maxAutoRow);
        @endphp

        {{-- 2. Main Staff / Sales Table --}}
        <div style="overflow-x: auto; margin-bottom: 14px;">
            <table class="sheet-table" id="mainSalesTable">
                <thead>
                    <tr>
                        <th class="row-label" style="text-align: center !important;">Name</th>
                        @foreach($activeStaff as $staff)
                            <th class="staff-col" data-staff-id="{{ $staff->id }}">{{ $staff->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    {{-- Deals / Customer Count Row --}}
                    <tr style="background: #f0fdf4;">
                        <td class="row-label" style="font-weight: 800; color: #166534;">Deals</td>
                        @foreach($activeStaff as $staff)
                            <td style="font-weight: 800; font-size: 0.72rem; color: #166534; text-align: center;">
                                <span id="staffDeals_{{ $staff->id }}">0 Deals</span>
                            </td>
                        @endforeach
                        <td style="font-weight: 800; font-size: 0.72rem; color: #166534; text-align: center; background: #f0fdf4;">
                            <span id="totalDealsCell">0 Deals</span>
                        </td>
                    </tr>

                    {{-- Time TA Row --}}
                    <tr>
                        <td class="row-label">Time TA</td>
                        @foreach($activeStaff as $staff)
                            @php $val = $savedSales['time_ta'][$staff->id] ?? ($autoAttendance[$staff->id] ?? ''); @endphp
                            <td>
                                <input type="text" class="sheet-input input-time-ta" data-staff-id="{{ $staff->id }}" value="{{ $val }}" placeholder="HH:mm">
                            </td>
                        @endforeach
                        <td style="background:#fafafa;"></td>
                    </tr>

                    {{-- Fine Row --}}
                    <tr>
                        <td class="row-label">Fine</td>
                        @foreach($activeStaff as $staff)
                            @php $val = $savedSales['fine'][$staff->id] ?? ''; @endphp
                            <td>
                                <input type="number" step="any" class="sheet-input input-fine calc-trigger" data-staff-id="{{ $staff->id }}" value="{{ $val }}" placeholder="0">
                            </td>
                        @endforeach
                        <td style="background:#fafafa;"></td>
                    </tr>

                    {{-- Dynamic Sales Rows (Defaults to 20, expands as needed) --}}
                    @for($r = 1; $r <= $totalSalesRows; $r++)
                    <tr class="sales-row">
                        <td class="row-label" style="text-align: center !important;">{{ $r }}</td>
                        @foreach($activeStaff as $staff)
                            @php 
                                $savedVal = $savedSales['rows'][$r][$staff->id] ?? null;
                                $val = ($savedVal !== null && $savedVal !== '') ? $savedVal : ($autoSales['rows'][$r][$staff->id] ?? ''); 
                            @endphp
                            <td>
                                <input type="number" step="any" class="sheet-input sales-cell calc-trigger" data-row="{{ $r }}" data-staff-id="{{ $staff->id }}" value="{{ $val }}">
                            </td>
                        @endforeach
                        <td style="background:#fafafa; font-size:0.72rem; color:#71717a; text-align:center; padding:1px 4px;">
                            @php $discVal = $savedSales['discounts'][$r] ?? ($autoSales['discounts'][$r] ?? ''); @endphp
                            @if($discVal) <span style="font-weight:700; color:#b45309;">-{{ $discVal }}</span> @endif
                        </td>
                    </tr>
                    @endfor

                    {{-- Total Row --}}
                    <tr class="row-total">
                        <td class="row-label" style="font-weight: 900;">Total</td>
                        @foreach($activeStaff as $staff)
                            <td id="staffTotal_{{ $staff->id }}" class="staff-sum-cell" style="font-weight: 900;">0</td>
                        @endforeach
                        <td id="totalDiscountCell" style="font-weight: 900; text-align: center; background: #f4f4f5; font-size: 0.72rem; color: #b45309;">Disc: 0</td>
                    </tr>

                    {{-- Comsn Row (Auto-calculated from staff total × commission %) --}}
                    <tr style="background: #fffbeb;">
                        <td class="row-label" style="color: #92400e; font-size: 0.72rem;">Comsn <span style="font-weight:500; font-size:0.65rem; color:#b45309;">(Auto %)</span></td>
                        @foreach($activeStaff as $staff)
                            @php $comsnVal = $savedSales['comsn'][$staff->id] ?? ''; @endphp
                            <td style="background: #fffbeb;">
                                <input type="number" step="any" class="sheet-input input-comsn calc-trigger" data-staff-id="{{ $staff->id }}" value="{{ $comsnVal }}" placeholder="0" style="color: #92400e; font-weight: 800;">
                            </td>
                        @endforeach
                        <td id="totalComsnCell" style="font-weight: 900; text-align: center; color: #92400e; background: #fef3c7;">0</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- 3. Other Expenditures + Dbt / Crd --}}
        <div style="display: grid; grid-template-columns: 1fr 280px; gap: 0; border: 1.5px solid #27272a; border-bottom: none; margin-bottom: 0;">
            <div style="border-right: 1.5px solid #27272a; padding: 8px;">
                <div style="font-weight: 800; font-size: 0.85rem; text-align: center; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.05em; background: #f4f4f5; padding: 4px;">
                    (Other Expenditures)
                </div>
                <textarea id="otherExpendituresText" class="sheet-textarea" rows="3" placeholder="Enter miscellaneous daily expenditures notes & amounts...">{{ $report->other_expenditures ?? '' }}</textarea>
            </div>
            <div style="padding: 8px;">
                <div style="font-weight: 800; font-size: 0.85rem; text-align: center; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.05em; background: #f4f4f5; padding: 4px;">
                    Dbt / Crd
                </div>
                <textarea id="dbtCrdText" class="sheet-textarea calc-trigger" rows="3" placeholder="Debit / Credit working & amount...">{{ $report->dbt_crd ?? '' }}</textarea>
            </div>
        </div>

        {{-- 4. Lower Daily Details Table (5 Main Columns) --}}
        <div style="display: grid; grid-template-columns: 1.2fr 1fr 1.6fr 1.4fr 1.1fr; border: 1.5px solid #27272a; margin-bottom: 14px;">
            
            {{-- Column 1: Expenditures --}}
            <div style="border-right: 1px solid #27272a; padding: 6px;">
                <div style="font-weight: 800; font-size: 0.8rem; background: #f4f4f5; padding: 4px; text-align: center; border-bottom: 1px solid #27272a; margin-bottom: 6px;">
                    Expenditures
                </div>
                <div style="display: flex; flex-direction: column; gap: 6px; font-size: 0.82rem;">
                    <textarea id="expendituresAmount" class="sheet-textarea calc-trigger" rows="4" placeholder="Enter expenditures & notes (e.g. Food 260+50, Tea 250)...">{{ $report->expenditures_amount ?? '' }}</textarea>
                    @if(isset($dailySystemExpenses) && $dailySystemExpenses->count() > 0)
                        <div style="font-size: 0.72rem; margin-top: 4px; border-top: 1px dashed #d4d4d8; padding-top: 4px;">
                            <span style="font-weight: 800; color: #3f3f46; display: block; margin-bottom: 2px;">System Daily Exp:</span>
                            @foreach($dailySystemExpenses as $de)
                                <div style="display: flex; justify-content: space-between; font-size: 0.7rem; padding: 1px 0;">
                                    <span style="font-weight: 700;">{{ $de->description ?: ($de->category ?: 'Daily Exp') }}:</span>
                                    <span style="font-weight: 800; color: {{ $de->deducted_from_drawer ? '#dc2626' : '#ea580c' }};">
                                        Rs. {{ number_format($de->amount) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <div style="margin-top: 6px; font-weight: 800; font-size: 0.82rem; background: #fafafa; padding: 6px; border-top: 1px solid #e4e4e7; display: flex; justify-content: space-between;">
                        <span>Total Exp:</span>
                        <span id="expTotalDisplay">0</span>
                    </div>
                </div>
            </div>

            {{-- Column 2: + Cosmetics --}}
            <div style="border-right: 1px solid #27272a; padding: 6px; display: flex; flex-direction: column;">
                <div style="font-weight: 800; font-size: 0.8rem; background: #f4f4f5; padding: 4px; text-align: center; border-bottom: 1px solid #27272a; margin-bottom: 6px;">
                    + Cosmetics
                </div>
                <textarea id="cosmeticsNotes" class="sheet-textarea calc-trigger" style="flex: 1;" placeholder="Cosmetics amounts & notes...">{{ $report->cosmetics_notes ?? '' }}</textarea>
                <div style="margin-top: 6px; font-weight: 800; font-size: 0.82rem; background: #fafafa; padding: 6px; border-top: 1px solid #e4e4e7; display: flex; justify-content: space-between;">
                    <span>Total Cos:</span>
                    <span id="cosTotalDisplay">0</span>
                </div>
            </div>

            {{-- Column 3: Staff --}}
            <div style="border-right: 1px solid #27272a; padding: 6px; display: flex; flex-direction: column;">
                <div style="font-weight: 800; font-size: 0.8rem; background: #f4f4f5; padding: 4px; text-align: center; border-bottom: 1px solid #27272a; margin-bottom: 6px;">
                    Staff
                </div>
                <table class="sheet-table" style="margin-bottom: 8px;">
                    <thead>
                        <tr>
                            <th style="font-size: 0.72rem; padding: 2px;">Staff</th>
                            <th style="font-size: 0.72rem; padding: 2px;">Time / Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activeStaff as $st)
                        @php $stVal = $savedStatus[$st->id] ?? ($autoAttendance[$st->id] ?? ''); @endphp
                        <tr>
                            <td style="font-weight: 700; text-align: left; padding: 2px 4px; font-size: 0.78rem;">{{ $st->name }}</td>
                            <td style="padding: 1px;">
                                <input type="text" class="sheet-input staff-status-input" data-staff-id="{{ $st->id }}" value="{{ $stVal }}" style="font-size: 0.78rem; padding: 1px;" placeholder="Time or Leave">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <div style="border-top: 1px dashed #a1a1aa; padding-top: 4px; margin-top: 4px; flex: 1;">
                    <label style="font-size: 0.72rem; font-weight: 800; color: #3f3f46; display: block; margin-bottom: 2px;">Staff Monetary Notes:</label>
                    <textarea id="staffNotes" class="sheet-textarea calc-trigger" rows="3" style="font-size: 0.78rem;" placeholder="e.g. 300, 300+650, 17500">{{ $report->staff_notes ?? '' }}</textarea>
                    @if(isset($staffSystemExpenses) && $staffSystemExpenses->count() > 0)
                        <div style="font-size: 0.72rem; margin-top: 4px; border-top: 1px dashed #d4d4d8; padding-top: 4px;">
                            <span style="font-weight: 800; color: #3f3f46; display: block; margin-bottom: 2px;">System Staff Exp:</span>
                            @foreach($staffSystemExpenses as $se)
                                <div style="display: flex; justify-content: space-between; font-size: 0.7rem; padding: 1px 0;">
                                    <span style="font-weight: 700;">{{ $se->staff->name ?? 'Staff' }} ({{ $se->description ?: $se->category }}):</span>
                                    <span style="font-weight: 800; color: {{ $se->deducted_from_drawer ? '#dc2626' : '#ea580c' }};">
                                        Rs. {{ number_format($se->amount) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div style="margin-top: 6px; font-weight: 800; font-size: 0.82rem; background: #fafafa; padding: 6px; border-top: 1px solid #e4e4e7; display: flex; justify-content: space-between;">
                    <span>Total Staff Monetary:</span>
                    <span id="staffMonetaryTotalDisplay">0</span>
                </div>
            </div>

            {{-- Column 4: Online Payments --}}
            <div style="border-right: 1px solid #27272a; padding: 6px; display: flex; flex-direction: column;">
                <div style="font-weight: 800; font-size: 0.8rem; background: #f4f4f5; padding: 4px; text-align: center; border-bottom: 1px solid #27272a; margin-bottom: 6px; display: flex; justify-content: space-between; align-items: center;">
                    <span>Online Payments</span>
                    <button type="button" onclick="addOnlineRow()" style="background: #18181b; color: #fff; border: none; border-radius: 4px; font-size: 0.68rem; font-weight: 800; padding: 1px 6px; cursor: pointer;">+ Row</button>
                </div>
                
                <table class="sheet-table" style="margin-bottom: 6px;">
                    <thead>
                        <tr>
                            <th style="font-size: 0.72rem; padding: 2px; width: 50%;">Card Payments</th>
                            <th style="font-size: 0.72rem; padding: 2px; width: 50%;">Bank Payments</th>
                        </tr>
                    </thead>
                    <tbody id="onlinePaymentRows">
                        @php
                            $cardEntries = !empty($report->card_hbl_entries) ? $report->card_hbl_entries : ($autoOnlinePayments['card'] ?? []);
                            $bankEntries = !empty($report->easypaisa_entries) ? $report->easypaisa_entries : ($autoOnlinePayments['bank'] ?? []);
                            $maxRows = max(10, count($cardEntries), count($bankEntries));
                        @endphp
                        @for($i = 0; $i < $maxRows; $i++)
                        <tr>
                            <td style="padding: 1px;">
                                <input type="number" step="any" class="sheet-input card-cell calc-trigger" value="{{ $cardEntries[$i] ?? '' }}" style="font-size: 0.78rem; padding: 1px;">
                            </td>
                            <td style="padding: 1px;">
                                <input type="number" step="any" class="sheet-input ep-cell calc-trigger" value="{{ $bankEntries[$i] ?? '' }}" style="font-size: 0.78rem; padding: 1px;">
                            </td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
                <div style="margin-top: auto; font-weight: 800; font-size: 0.78rem; background: #fafafa; padding: 4px; border-top: 1px solid #e4e4e7;">
                    <div style="display: flex; justify-content: space-between;"><span>Card Total:</span><span id="cardTotalDisplay">0</span></div>
                    <div style="display: flex; justify-content: space-between;"><span>Bank Total:</span><span id="epTotalDisplay">0</span></div>
                    <div style="display: flex; justify-content: space-between; border-top: 1px solid #d4d4d8; margin-top: 2px; padding-top: 2px; font-weight: 900;">
                        <span>Total O/L Pay:</span><span id="onlinePaymentTotalDisplay">0</span>
                    </div>
                </div>
            </div>

            {{-- Column 5: Cash Payments --}}
            <div style="padding: 6px; display: flex; flex-direction: column;">
                <div style="font-weight: 800; font-size: 0.8rem; background: #f4f4f5; padding: 4px; text-align: center; border-bottom: 1px solid #27272a; margin-bottom: 6px; display: flex; justify-content: space-between; align-items: center;">
                    <span>💵 Cash Payments</span>
                    <button type="button" onclick="addCashRow()" style="background: #18181b; color: #fff; border: none; border-radius: 4px; font-size: 0.68rem; font-weight: 800; padding: 1px 6px; cursor: pointer;">+ Row</button>
                </div>
                
                @php
                    $savedCashEntries = $report->cash_entries ?? null;
                    $cashRows = !empty($savedCashEntries) ? $savedCashEntries : (!empty($autoCashEntries) ? $autoCashEntries : []);
                    $cashMaxRows = max(8, count($cashRows));
                @endphp

                <table class="sheet-table" style="margin-bottom: 4px;">
                    <tbody id="cashPaymentRows">
                        @for($ci = 0; $ci < $cashMaxRows; $ci++)
                        <tr>
                            <td style="padding: 1px; text-align: center; font-size: 0.7rem; color: #71717a; width: 20px;">{{ $ci + 1 }}</td>
                            <td style="padding: 1px;">
                                <input type="number" step="any" class="sheet-input cash-cell calc-trigger" value="{{ $cashRows[$ci] ?? '' }}" style="font-size: 0.78rem; padding: 1px;" placeholder="0">
                            </td>
                        </tr>
                        @endfor
                    </tbody>
                </table>

                <div style="margin-top: auto; font-weight: 800; font-size: 0.82rem; background: #f0fdf4; padding: 5px; border-top: 1px solid #bbf7d0; border-radius: 0 0 4px 4px;">
                    <div style="display: flex; justify-content: space-between; color: #166534; font-weight: 900;">
                        <span>Cash In Hand:</span><span id="cashTotalDisplay">0</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom Section: (O/L Paid), Pending Payments, Fixed Expenses, MZN Bank & Right Side Summary --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr 1.1fr 220px; gap: 0; border: 1.5px solid #27272a;">
            
            {{-- (O/L Paid) --}}
            <div style="border-right: 1px solid #27272a; padding: 6px;">
                <div style="font-weight: 800; font-size: 0.78rem; background: #f4f4f5; padding: 4px; text-align: center; border-bottom: 1px solid #27272a; margin-bottom: 6px;">
                    (O/L Paid)
                </div>
                <textarea id="olPaidNotes" class="sheet-textarea calc-trigger" rows="4" placeholder="Amount / notes...">{{ is_array($report->ol_paid_entries ?? null) ? implode("\n", $report->ol_paid_entries) : ($report->ol_paid_entries ?? '') }}</textarea>
                <div style="margin-top: 4px; font-weight: 800; font-size: 0.8rem; background: #fafafa; padding: 4px; border-top: 1px solid #e4e4e7; display: flex; justify-content: space-between;">
                    <span>Total O/L:</span>
                    <span id="olTotalDisplay">0</span>
                </div>
            </div>

            {{-- Pending Payments --}}
            <div style="border-right: 1px solid #27272a; padding: 6px;">
                <div style="font-weight: 800; font-size: 0.78rem; background: #fff3cd; padding: 4px; text-align: center; border-bottom: 1px solid #27272a; margin-bottom: 6px; color: #856404;">
                    Pending Payments
                </div>
                @php
                    $pendingVal = is_array($report->paid_from_c3_entries ?? null)
                        ? implode("\n", $report->paid_from_c3_entries)
                        : ($report->paid_from_c3_entries ?? (!empty($autoPendingPayments) ? implode("\n", $autoPendingPayments) : ''));
                @endphp
                <textarea id="paidFromC3Notes" class="sheet-textarea calc-trigger" rows="4" placeholder="Pending payment notes & amounts (e.g. 15,000 Online Transfer Pending, 5,000 Card Pending, 3,200 Bank Transfer Pending)...">{{ $pendingVal }}</textarea>
                <div style="margin-top: 4px; font-weight: 800; font-size: 0.8rem; background: #fafafa; padding: 4px; border-top: 1px solid #e4e4e7; display: flex; justify-content: space-between;">
                    <span>Total Pending:</span>
                    <span id="c3TotalDisplay">0</span>
                </div>
            </div>

            {{-- Fixed Expenses + MZN Bank --}}
            <div style="border-right: 1px solid #27272a; padding: 6px; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="font-weight: 800; font-size: 0.78rem; background: #f4f4f5; padding: 4px; text-align: center; border-bottom: 1px solid #27272a; margin-bottom: 6px;">
                        Fixed Expenses
                    </div>
                    @if(isset($fixedSystemExpenses) && $fixedSystemExpenses->count() > 0)
                        <div style="font-size: 0.72rem; margin-bottom: 4px; max-height: 90px; overflow-y: auto;">
                            @foreach($fixedSystemExpenses as $fe)
                                <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #d4d4d8; padding: 2px 0;">
                                    <span style="font-weight: 700; text-transform: capitalize;">{{ $fe->category ?: 'Fixed' }}:</span>
                                    <span style="font-weight: 800; color: #dc2626;">Rs. {{ number_format($fe->amount) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="font-size: 0.75rem; color: #71717a; font-style: italic; padding: 4px; text-align: center;">No fixed expenses logged</div>
                    @endif
                    <div style="margin-top: 6px; font-weight: 800; font-size: 0.8rem; background: #fafafa; padding: 4px; border-top: 1px solid #e4e4e7; display: flex; justify-content: space-between;">
                        <span>Total Fix Exp:</span>
                        <span id="personalTotalDisplay">0</span>
                    </div>
                </div>

                {{-- MZN Bank at bottom left --}}
                <div style="border-top: 1.5px solid #27272a; padding-top: 6px; margin-top: 6px;">
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <label style="font-weight: 900; font-size: 0.82rem; white-space: nowrap;">MZN Bank:</label>
                        <input type="number" step="any" id="mznBankAmount" class="sheet-input sheet-input-left calc-trigger" style="border: 1px solid #27272a; border-radius: 4px; padding: 3px 6px; font-weight: 800;" value="{{ $report->mzn_bank_amount ?? '' }}" placeholder="0">
                    </div>
                </div>
            </div>

            {{-- Right-Side Final Summary Table --}}
            <div style="padding: 0;">
                <table class="summary-table">
                    <tbody>
                        <tr>
                            <td class="summary-label">Sale</td>
                            <td class="summary-val-td" id="sum_sale">0</td>
                        </tr>
                        <tr>
                            <td class="summary-label">Cos</td>
                            <td class="summary-val-td" id="sum_cos">0</td>
                        </tr>
                        <tr>
                            <td class="summary-label">Dis</td>
                            <td class="summary-val-td" id="sum_dis">0</td>
                        </tr>
                        <tr>
                            <td class="summary-label">Exp</td>
                            <td class="summary-val-td" id="sum_exp">0</td>
                        </tr>
                        <tr>
                            <td class="summary-label">O/L</td>
                            <td class="summary-val-td" id="sum_ol">0</td>
                        </tr>
                        <tr>
                            <td class="summary-label">Staff</td>
                            <td class="summary-val-td" id="sum_staff">0</td>
                        </tr>
                        <tr>
                            <td class="summary-label">Fix Exp</td>
                            <td class="summary-val-td" id="sum_prs">0</td>
                        </tr>
                        <tr>
                            <td class="summary-label">Dbt/Crd</td>
                            <td class="summary-val-td">
                                <input type="number" step="any" id="sum_dbt_crd_input" class="sheet-input calc-trigger" style="text-align: right; font-weight: 700; font-size: 0.8rem; padding: 0;" value="{{ $savedSummary['dbt_crd'] ?? '' }}" placeholder="0">
                            </td>
                        </tr>
                        <tr>
                            <td class="summary-label">Other</td>
                            <td class="summary-val-td" id="sum_other">0</td>
                        </tr>
                        <tr style="background: #f0fdf4;">
                            <td class="summary-label" style="font-weight: 900; background: #bbf7d0; color: #166534;">Savings</td>
                            <td class="summary-val-td" id="sum_savings" style="font-weight: 900; color: #166534;">0</td>
                        </tr>
                        <tr style="background: #f4f4f5;">
                            <td class="summary-label" style="font-weight: 900; background: #e4e4e7;">Balnc</td>
                            <td class="summary-val-td" style="font-weight: 900;">
                                <input type="number" step="any" id="sum_balnc_input" class="sheet-input calc-trigger" style="text-align: right; font-weight: 900; font-size: 0.85rem; padding: 0; color: #18181b;" value="{{ $savedSummary['balnc'] ?? '' }}" placeholder="0">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- System Logged Expenses Audit Section --}}
        <div style="margin-top: 16px; padding: 14px; background: #fffdfa; border: 1.5px solid #27272a; border-radius: 8px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; border-bottom: 1.5px solid #27272a; padding-bottom: 6px;">
                <div style="font-weight: 900; font-size: 0.85rem; text-transform: uppercase; color: #18181b; display: flex; align-items: center; gap: 6px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    System Logged Expenses Audit (Drawer vs Savings)
                </div>
                <div style="font-size: 0.8rem; font-weight: 800;">
                    Drawer: <span style="color:#dc2626;">PKR {{ number_format($drawerExpensesTotal, 2) }}</span> &nbsp;|&nbsp;
                    Savings (Non-Drawer): <span style="color:#ea580c;">PKR {{ number_format($nonDrawerExpensesTotal, 2) }}</span> &nbsp;|&nbsp;
                    Total System Exp: <span style="color:#18181b;">PKR {{ number_format($totalSystemExpenses, 2) }}</span>
                </div>
            </div>

            @if(isset($systemExpenses) && $systemExpenses->count() > 0)
            <div style="overflow-x: auto;">
                <table class="sheet-table" style="font-size: 0.78rem; width: 100%;">
                    <thead>
                        <tr style="background: #f4f4f5;">
                            <th style="text-align: left; padding: 5px 8px;">Description / Note</th>
                            <th style="padding: 5px 8px;">Expense Type</th>
                            <th style="padding: 5px 8px;">Category</th>
                            <th style="padding: 5px 8px;">Staff / User</th>
                            <th style="padding: 5px 8px;">Expense Source</th>
                            <th style="text-align: right; padding: 5px 8px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($systemExpenses as $exp)
                        <tr>
                            <td style="text-align: left; font-weight: 700;">{{ $exp->description ?: ($exp->category ?: 'Expense') }}</td>
                            <td style="text-transform: capitalize; font-weight: 700;">
                                <span style="background:#f4f4f5; border:1px solid #d4d4d8; padding:1px 6px; border-radius:4px;">
                                    {{ $exp->expense_type ?: 'Daily' }}
                                </span>
                            </td>
                            <td style="text-transform: capitalize; font-weight: 600;">{{ str_replace('_', ' ', $exp->category ?: $exp->expense_type) }}</td>
                            <td>{{ $exp->staff->name ?? ($exp->user->name ?? 'Admin') }}</td>
                            <td>
                                @if($exp->deducted_from_drawer)
                                    <span style="background:#fee2e2; color:#991b1b; padding:2px 8px; border-radius:6px; font-size:0.7rem; font-weight:800; border:1px solid #fca5a5;">Deducted From Drawer</span>
                                @else
                                    <span style="background:#ffedd5; color:#9a3412; padding:2px 8px; border-radius:6px; font-size:0.7rem; font-weight:800; border:1px solid #fdba74;">Non-Drawer (External)</span>
                                @endif
                            </td>
                            <td style="text-align: right; font-weight: 900; color: {{ $exp->deducted_from_drawer ? '#dc2626' : '#ea580c' }}; font-size: 0.85rem;">
                                PKR {{ number_format($exp->amount, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <div style="font-size: 0.8rem; color: #71717a; text-align: center; font-style: italic; padding: 6px;">
                    No additional system expenses logged in database for {{ $formattedHeaderDate }}.
                </div>
            @endif
        </div>

    </div>
</div>

<script>
    // Staff commission rates map
    const staffCommRates = {
        @foreach($activeStaff as $staff)
            '{{ $staff->id }}': {{ (float) ($staff->commission_per_service ?? 10) }},
        @endforeach
    };

    // Helper function to extract numerical values from text (handles "260+50", "300+650", numbers inside text)
    function parseAmountFromText(text) {
        if (!text) return 0;
        text = text.toString();
        const numbers = text.match(/\d+(\.\d+)?/g);
        if (!numbers) return 0;
        return numbers.reduce((sum, n) => sum + parseFloat(n), 0);
    }

    function calculateAllSheetTotals() {
        // 1. Calculate Staff Columns, Deals Count, Discount Totals & Commission Total
        let grandSaleTotal = 0;
        let totalDiscount = 0;
        let grandDealsCount = 0;
        let totalComsn = 0;

        const staffIds = [];
        document.querySelectorAll('th[data-staff-id]').forEach(th => {
            staffIds.push(th.dataset.staffId);
        });

        staffIds.forEach(id => {
            let staffSum = 0;
            let staffDeals = 0;
            document.querySelectorAll(`.sales-cell[data-staff-id="${id}"]`).forEach(cell => {
                if (cell && cell.value !== '' && parseFloat(cell.value) > 0) {
                    staffSum += parseFloat(cell.value) || 0;
                    staffDeals++;
                }
            });
            const sumEl = document.getElementById(`staffTotal_${id}`);
            if (sumEl) sumEl.textContent = staffSum ? staffSum.toLocaleString() : '0';

            const dealsEl = document.getElementById(`staffDeals_${id}`);
            if (dealsEl) dealsEl.textContent = `${staffDeals} Deals`;

            // Auto-calculate Commission for staff if not manually edited by user
            const comInp = document.querySelector(`.input-comsn[data-staff-id="${id}"]`);
            if (comInp) {
                if (!comInp.dataset.userEdited || comInp.dataset.userEdited === 'false') {
                    const rate = staffCommRates[id] !== undefined ? staffCommRates[id] : 10;
                    const autoComsn = Math.round(staffSum * (rate / 100));
                    comInp.value = autoComsn > 0 ? autoComsn : '';
                }
            }

            grandSaleTotal += staffSum;
            grandDealsCount += staffDeals;
        });

        const totalDealsEl = document.getElementById('totalDealsCell');
        if (totalDealsEl) totalDealsEl.textContent = `${grandDealsCount} Deals`;

        document.querySelectorAll('.sales-row').forEach(row => {
            const discSpan = row.querySelector('td:last-child span[style*="color:#b45309"]');
            if (discSpan) {
                const discText = discSpan.textContent.replace('-', '').trim();
                totalDiscount += parseFloat(discText) || 0;
            }
        });
        const totDiscEl = document.getElementById('totalDiscountCell');
        if (totDiscEl) totDiscEl.textContent = totalDiscount ? 'Disc: ' + totalDiscount.toLocaleString() : 'Disc: 0';

        document.querySelectorAll('.input-comsn').forEach(comInp => {
            if (comInp && comInp.value) {
                totalComsn += parseFloat(comInp.value) || 0;
            }
        });
        const totComsnEl = document.getElementById('totalComsnCell');
        if (totComsnEl) totComsnEl.textContent = totalComsn ? totalComsn.toLocaleString() : '0';

        // 2. Expenditures Total (Expenditures Notes + System Daily Exp)
        const expInp = document.getElementById('expendituresAmount');
        const exp = expInp ? parseAmountFromText(expInp.value) : 0;
        const dailyExpSys = {{ $dailyExpensesTotal ?? 0 }};
        const totalExp = exp + dailyExpSys;
        document.getElementById('expTotalDisplay').textContent = totalExp ? totalExp.toLocaleString() : '0';

        // 3. Cosmetics Total
        const cosmeticsTotal = parseAmountFromText(document.getElementById('cosmeticsNotes').value);
        document.getElementById('cosTotalDisplay').textContent = cosmeticsTotal ? cosmeticsTotal.toLocaleString() : '0';

        // 4. Staff Monetary Notes Total (+ System Staff Exp)
        const staffNotesVal = parseAmountFromText(document.getElementById('staffNotes').value);
        const staffExpSys = {{ $staffExpensesTotal ?? 0 }};
        const staffMonetaryTotal = staffNotesVal + staffExpSys;
        document.getElementById('staffMonetaryTotalDisplay').textContent = staffMonetaryTotal ? staffMonetaryTotal.toLocaleString() : '0';

        // 5. Payment Totals — Card | Bank | Cash
        let cardTotal = 0;
        document.querySelectorAll('.card-cell').forEach(input => {
            if (input.value) cardTotal += parseFloat(input.value) || 0;
        });
        document.getElementById('cardTotalDisplay').textContent = cardTotal ? cardTotal.toLocaleString() : '0';

        let epTotal = 0;
        document.querySelectorAll('.ep-cell').forEach(input => {
            if (input.value) epTotal += parseFloat(input.value) || 0;
        });
        document.getElementById('epTotalDisplay').textContent = epTotal ? epTotal.toLocaleString() : '0';

        const onlineTotal = cardTotal + epTotal;
        document.getElementById('onlinePaymentTotalDisplay').textContent = onlineTotal ? onlineTotal.toLocaleString() : '0';

        let cashTotal = 0;
        document.querySelectorAll('.cash-cell').forEach(input => {
            if (input.value) cashTotal += parseFloat(input.value) || 0;
        });
        const cashTotalEl = document.getElementById('cashTotalDisplay');
        if (cashTotalEl) cashTotalEl.textContent = cashTotal ? cashTotal.toLocaleString() : '0';

        // 6. Bottom Sections
        const olTotal = parseAmountFromText(document.getElementById('olPaidNotes').value);
        document.getElementById('olTotalDisplay').textContent = olTotal ? olTotal.toLocaleString() : '0';

        // Pending Payments total
        const pendingTotal = parseAmountFromText(document.getElementById('paidFromC3Notes').value);
        document.getElementById('c3TotalDisplay').textContent = pendingTotal ? pendingTotal.toLocaleString() : '0';

        // System-logged fixed expenses from DB
        const personalTotal = {{ $fixedExpensesTotal ?? 0 }};
        document.getElementById('personalTotalDisplay').textContent = personalTotal ? personalTotal.toLocaleString() : '0';

        const otherExpTotal = parseAmountFromText(document.getElementById('otherExpendituresText').value);

        // 7. Update Summary Table
        document.getElementById('sum_sale').textContent = grandSaleTotal.toLocaleString();
        document.getElementById('sum_cos').textContent = cosmeticsTotal.toLocaleString();
        document.getElementById('sum_dis').textContent = totalDiscount.toLocaleString();
        document.getElementById('sum_exp').textContent = totalExp.toLocaleString();
        document.getElementById('sum_ol').textContent = olTotal.toLocaleString();
        document.getElementById('sum_staff').textContent = staffMonetaryTotal.toLocaleString();
        document.getElementById('sum_prs').textContent = personalTotal.toLocaleString();
        document.getElementById('sum_other').textContent = otherExpTotal.toLocaleString();

        // Savings = Sale - (Exp + O/L + Staff + FixExp + Other)
        const totalAllExp = totalExp + olTotal + staffMonetaryTotal + personalTotal + otherExpTotal;
        const netDailySavings = grandSaleTotal - totalAllExp;

        const prevSavings = {{ $previousSavingsCarryforward ?? 0 }};
        const totalCumSavings = prevSavings + netDailySavings;
        const cumDispEl = document.getElementById('savings_total_cum_display');
        if (cumDispEl) cumDispEl.textContent = totalCumSavings.toLocaleString();

        const sumSavingsEl = document.getElementById('sum_savings');
        if (sumSavingsEl) sumSavingsEl.textContent = netDailySavings.toLocaleString();

        const incDispEl = document.getElementById('savings_income_display');
        if (incDispEl) incDispEl.textContent = grandSaleTotal.toLocaleString();

        const netDispEl = document.getElementById('savings_net_display');
        if (netDispEl) netDispEl.textContent = netDailySavings.toLocaleString();

        // Suggested balance = Cash in drawer
        const balncInput = document.getElementById('sum_balnc_input');
        if (!balncInput.value || balncInput.dataset.autoCalculated === 'true') {
            const suggestedBal = grandSaleTotal - (onlineTotal + totalExp + olTotal + staffMonetaryTotal + personalTotal + otherExpTotal);
            if (!balncInput.value) {
                balncInput.value = suggestedBal;
                balncInput.dataset.autoCalculated = 'true';
            }
        }
    }

    function addOnlineRow() {
        const tbody = document.getElementById('onlinePaymentRows');
        if (!tbody) return;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="padding: 1px;">
                <input type="number" step="any" class="sheet-input card-cell calc-trigger" style="font-size: 0.78rem; padding: 1px;" placeholder="0">
            </td>
            <td style="padding: 1px;">
                <input type="number" step="any" class="sheet-input ep-cell calc-trigger" style="font-size: 0.78rem; padding: 1px;" placeholder="0">
            </td>
        `;
        tbody.appendChild(tr);
    }

    function addCashRow() {
        const tbody = document.getElementById('cashPaymentRows');
        if (!tbody) return;
        const rowNum = tbody.querySelectorAll('tr').length + 1;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="padding: 1px; text-align: center; font-size: 0.7rem; color: #71717a; width: 20px;">${rowNum}</td>
            <td style="padding: 1px;">
                <input type="number" step="any" class="sheet-input cash-cell calc-trigger" style="font-size: 0.78rem; padding: 1px;" placeholder="0">
            </td>
        `;
        tbody.appendChild(tr);
    }

    // Run initial calculation on page load
    document.addEventListener('DOMContentLoaded', function() {
        calculateAllSheetTotals();
    });

    // Event listener for all calc triggers
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('input-comsn')) {
            e.target.dataset.userEdited = 'true';
        }
        const triggerClasses = ['calc-trigger','sales-cell','discount-cell','ep-cell','card-cell','input-comsn'];
        if (triggerClasses.some(c => e.target.classList.contains(c))) {
            if (e.target.id === 'sum_balnc_input') {
                e.target.dataset.autoCalculated = 'false';
            }
            calculateAllSheetTotals();
        }
    });

    // Save Daily Report Ajax
    function saveReport() {
        const staffSalesData = {
            fine: {},
            time_ta: {},
            comsn: {},
            rows: {},
            discounts: {}
        };

        document.querySelectorAll('.input-time-ta').forEach(inp => {
            staffSalesData.time_ta[inp.dataset.staffId] = inp.value;
        });
        document.querySelectorAll('.input-fine').forEach(inp => {
            staffSalesData.fine[inp.dataset.staffId] = inp.value;
        });
        document.querySelectorAll('.input-comsn').forEach(inp => {
            staffSalesData.comsn[inp.dataset.staffId] = inp.value;
        });

        const totalRowsInTable = document.querySelectorAll('.sales-row').length || 20;
        for (let r = 1; r <= totalRowsInTable; r++) {
            staffSalesData.rows[r] = {};
            document.querySelectorAll(`.sales-cell[data-row="${r}"]`).forEach(inp => {
                staffSalesData.rows[r][inp.dataset.staffId] = inp.value;
            });
            // Discounts are read-only (from invoice data) — read from span if present
            const salesRows = document.querySelectorAll('.sales-row');
            if (salesRows[r - 1]) {
                const discSpan = salesRows[r - 1].querySelector('td:last-child span');
                if (discSpan) {
                    staffSalesData.discounts[r] = discSpan.textContent.replace('-', '').trim();
                }
            }
        }

        const staffStatusData = {};
        document.querySelectorAll('.staff-status-input').forEach(inp => {
            staffStatusData[inp.dataset.staffId] = inp.value;
        });

        const epEntries = [];
        document.querySelectorAll('.ep-cell').forEach(inp => {
            if (inp.value) epEntries.push(inp.value);
        });

        const cardEntries = [];
        document.querySelectorAll('.card-cell').forEach(inp => {
            if (inp.value) cardEntries.push(inp.value);
        });

        const cashEntries = [];
        document.querySelectorAll('.cash-cell').forEach(inp => {
            if (inp.value) cashEntries.push(inp.value);
        });

        const summaryData = {
            sale: parseAmountFromText(document.getElementById('sum_sale').textContent),
            cos: parseAmountFromText(document.getElementById('sum_cos').textContent),
            dis: parseAmountFromText(document.getElementById('sum_dis').textContent),
            exp: parseAmountFromText(document.getElementById('sum_exp').textContent),
            ol: parseAmountFromText(document.getElementById('sum_ol').textContent),
            staff: parseAmountFromText(document.getElementById('sum_staff').textContent),
            prs: parseAmountFromText(document.getElementById('sum_prs').textContent),
            dbt_crd: document.getElementById('sum_dbt_crd_input').value,
            other: parseAmountFromText(document.getElementById('sum_other').textContent),
            pending: parseAmountFromText(document.getElementById('c3TotalDisplay').textContent),
            savings: parseAmountFromText(document.getElementById('sum_savings')?.textContent || '0'),
            balnc: document.getElementById('sum_balnc_input').value
        };

        const payload = {
            _token: '{{ csrf_token() }}',
            report_date: '{{ $dateStr }}',
            other_expenditures: document.getElementById('otherExpendituresText').value,
            dbt_crd: document.getElementById('dbtCrdText').value,
            expenditures_amount: document.getElementById('expendituresAmount').value,
            cosmetics_notes: document.getElementById('cosmeticsNotes').value,
            cosmetics_total: parseAmountFromText(document.getElementById('cosTotalDisplay').textContent),
            staff_notes: document.getElementById('staffNotes').value,
            staff_total_monetary: parseAmountFromText(document.getElementById('staffMonetaryTotalDisplay').textContent),
            easypaisa_entries: epEntries,
            easypaisa_total: parseAmountFromText(document.getElementById('epTotalDisplay').textContent),
            card_hbl_entries: cardEntries,
            card_hbl_total: parseAmountFromText(document.getElementById('cardTotalDisplay').textContent),
            cash_entries: cashEntries,
            cash_total: parseAmountFromText(document.getElementById('cashTotalDisplay')?.textContent || '0'),
            ol_paid_entries: document.getElementById('olPaidNotes').value.split('\n').filter(Boolean),
            ol_paid_total: parseAmountFromText(document.getElementById('olTotalDisplay').textContent),
            paid_from_c3_entries: document.getElementById('paidFromC3Notes').value.split('\n').filter(Boolean),
            paid_from_c3_total: parseAmountFromText(document.getElementById('c3TotalDisplay').textContent),
            personal_exp_total: parseAmountFromText(document.getElementById('personalTotalDisplay').textContent),
            mzn_bank_amount: parseFloat(document.getElementById('mznBankAmount').value) || 0,
            staff_sales_data: staffSalesData,
            staff_status_data: staffStatusData,
            summary_data: summaryData
        };

        fetch('{{ route("reports.daily.save") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const statusEl = document.getElementById('saveStatus');
                statusEl.style.display = 'inline';
                setTimeout(() => { statusEl.style.display = 'none'; }, 3000);
            } else {
                alert('Error saving report');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Server error while saving report.');
        });
    }

    // Initial calculation on page load is handled above in DOMContentLoaded at line 843
</script>
@endsection
