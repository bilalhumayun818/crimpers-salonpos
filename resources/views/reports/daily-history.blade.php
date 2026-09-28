@extends('layouts.app')
@section('title', 'Daily Report Sheet History')

@section('content')
<style>
    .dh-card {
        background: #ffffff;
        border: 1.5px solid #27272a;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .dh-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #27272a;
    }
    .dh-title {
        font-size: 1.35rem;
        font-weight: 900;
        color: #18181b;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .dh-btn {
        background: #18181b;
        color: #ffffff;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.85rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background 0.18s;
    }
    .dh-btn:hover {
        background: #27272a;
        color: #ffffff;
    }
    .dh-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
    }
    .dh-table th {
        background: #f4f4f5;
        color: #27272a;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.05em;
        padding: 10px 12px;
        border: 1px solid #e4e4e7;
        text-align: left;
    }
    .dh-table td {
        padding: 10px 12px;
        border: 1px solid #e4e4e7;
        color: #18181b;
        vertical-align: middle;
    }
    .dh-table tr:hover {
        background: #fafafa;
    }
    .dh-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 6px;
        font-weight: 800;
        font-size: 0.75rem;
    }
    .dh-badge-savings {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
    .dh-badge-c3 {
        background: #ffedd5;
        color: #9a3412;
        border: 1px solid #fdba74;
    }
</style>

<div class="dh-card">
    <div class="dh-header">
        <div class="dh-title">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Daily Paper Reports History
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <a href="{{ route('reports.daily') }}" class="dh-btn">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
                Today's Daily Sheet
            </a>
        </div>
    </div>

    @if($reports->count() > 0)
    <div style="overflow-x: auto;">
        <table class="dh-table">
            <thead>
                <tr>
                    <th>Report Date</th>
                    <th>Branch</th>
                    <th style="text-align: right;">Gross Sale</th>
                    <th style="text-align: right;">Daily Exp</th>
                    <th style="text-align: right;">Staff Exp</th>
                    <th style="text-align: right;">Fix Exp</th>
                    <th style="text-align: right;">Savings</th>
                    <th style="text-align: center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $rpt)
                @php
                    $sData = $rpt->summary_data ?? [];
                    $sale = (float)($sData['sale'] ?? 0);
                    $exp = (float)($sData['exp'] ?? 0);
                    $staff = (float)($sData['staff'] ?? $rpt->staff_total_monetary ?? 0);
                    $prs = (float)($sData['prs'] ?? $rpt->personal_exp_total ?? 0);
                    $savings = (float)($sData['savings'] ?? ($sale - ($exp + $staff + $prs)));
                    $dateObj = \Carbon\Carbon::parse($rpt->report_date);
                @endphp
                <tr>
                    <td style="font-weight: 800;">
                        {{ $dateObj->format('d-m-Y') }}
                        <span style="font-size: 0.75rem; color: #71717a; margin-left: 4px;">({{ $dateObj->format('D') }})</span>
                    </td>
                    <td style="font-weight: 700;">{{ $rpt->branch->name ?? 'Main Branch' }}</td>
                    <td style="text-align: right; font-weight: 800; color: #166534;">PKR {{ number_format($sale) }}</td>
                    <td style="text-align: right; font-weight: 700; color: #dc2626;">PKR {{ number_format($exp) }}</td>
                    <td style="text-align: right; font-weight: 700;">PKR {{ number_format($staff) }}</td>
                    <td style="text-align: right; font-weight: 700;">PKR {{ number_format($prs) }}</td>
                    <td style="text-align: right;">
                        <span class="dh-badge dh-badge-savings">PKR {{ number_format($savings) }}</span>
                    </td>
                    <td style="text-align: center;">
                        <a href="{{ route('reports.daily', ['date' => $dateObj->format('Y-m-d')]) }}" class="dh-btn" style="padding: 4px 10px; font-size: 0.78rem;">
                            Open Sheet
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 16px;">
        {{ $reports->links() }}
    </div>
    @else
        <div style="text-align: center; padding: 40px; color: #71717a; font-style: italic;">
            No saved daily paper report sheets found. Open today's sheet and click "Save Daily Sheet" to store reports.
        </div>
    @endif
</div>
@endsection
