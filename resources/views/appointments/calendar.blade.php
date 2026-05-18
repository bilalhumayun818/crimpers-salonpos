@extends('layouts.app')
@section('title', 'Appointment Calendar')

@section('content')
<style>
/* ── Page header ── */
.cal-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;}
.cal-title{font-size:1.5rem;font-weight:800;color:#0f172a;letter-spacing:-.02em;margin-bottom:3px;}
.cal-sub{font-size:.85rem;color:#64748b;}
.header-actions{display:flex;gap:10px;}
.btn-outline{padding:9px 18px;border:1.5px solid #F7DF79;background:#fff;border-radius:10px;color:#c9a800;font-size:.85rem;font-weight:600;cursor:pointer;text-decoration:none;font-family:'Outfit',sans-serif;transition:.2s;}
.btn-outline:hover{background:#fffdf0;}
.btn-solid{padding:9px 18px;border:none;background:linear-gradient(135deg,#F7DF79,#c9a800);border-radius:10px;color:#18181b;font-size:.85rem;font-weight:600;cursor:pointer;font-family:'Outfit',sans-serif;transition:.2s;box-shadow:0 3px 10px rgba(247,223,121,.25);}
.btn-solid:hover{transform:translateY(-1px);box-shadow:0 5px 16px rgba(247,223,121,.35);}

/* ── Layout ── */
.cal-layout{display:flex;gap:20px;align-items:flex-start;}

/* ── Sidebar ── */
.cal-sidebar{width:220px;min-width:220px;display:flex;flex-direction:column;gap:16px;}
.sidebar-panel{background:#fff;border:1px solid #f0e8a0;border-radius:14px;padding:18px;box-shadow:0 1px 4px rgba(0,0,0,.04);}
.sidebar-panel-title{font-size:.78rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:14px;}
.staff-item{display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:9px;cursor:pointer;transition:.15s;margin-bottom:2px;}
.staff-item:hover{background:#fffdf0;}
.staff-item input[type=checkbox]{width:15px;height:15px;accent-color:#c9a800;cursor:pointer;}
.staff-name{font-size:.85rem;font-weight:500;color:#374151;}
.staff-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0;}

.legend-item{display:flex;align-items:center;gap:8px;margin-bottom:8px;}
.legend-dot{width:10px;height:10px;border-radius:3px;flex-shrink:0;}
.legend-label{font-size:.8rem;color:#64748b;font-weight:500;}

/* ── Calendar container ── */
.cal-main{flex:1;min-width:0;}
.cal-wrap{background:#fff;border:1px solid #f0e8a0;border-radius:16px;padding:20px;box-shadow:0 1px 6px rgba(0,0,0,.05);overflow:hidden;}

/* ── FullCalendar overrides ── */
.fc{font-family:'Outfit',sans-serif !important;}
.fc .fc-toolbar-title{font-size:1.1rem !important;font-weight:700 !important;color:#1e293b !important;}
.fc .fc-button{background:#fff !important;border:1.5px solid #e2e8f0 !important;color:#374151 !important;font-family:'Outfit',sans-serif !important;font-weight:600 !important;font-size:.8rem !important;border-radius:8px !important;padding:6px 12px !important;box-shadow:none !important;transition:.2s !important;}
.fc .fc-button:hover{background:#fffdf0 !important;border-color:#F7DF79 !important;color:#c9a800 !important;}
.fc .fc-button-active,.fc .fc-button-primary:not(:disabled).fc-button-active{background:#c9a800 !important;border-color:#c9a800 !important;color:#fff !important;}
.fc .fc-button-primary:focus{box-shadow:none !important;}
.fc .fc-col-header-cell{background:#f8fafc !important;border-color:#f1f5f9 !important;}
.fc .fc-col-header-cell-cushion{font-size:.8rem !important;font-weight:700 !important;color:#64748b !important;text-transform:uppercase !important;letter-spacing:.05em !important;padding:10px 4px !important;text-decoration:none !important;}
.fc .fc-timegrid-slot-label{font-size:.75rem !important;color:#94a3b8 !important;font-weight:500 !important;}
.fc .fc-daygrid-day-number{font-size:.85rem !important;font-weight:600 !important;color:#374151 !important;text-decoration:none !important;}
.fc .fc-daygrid-day.fc-day-today,.fc .fc-timegrid-col.fc-day-today{background:rgba(247,223,121,.04) !important;}
.fc .fc-timegrid-now-indicator-line{border-color:#F7DF79 !important;border-width:2px !important;}
.fc .fc-timegrid-now-indicator-arrow{border-top-color:#F7DF79 !important;border-bottom-color:#F7DF79 !important;}
.fc-event{background:transparent !important;border:none !important;padding:0 !important;box-shadow:none !important;}
.fc .fc-scrollgrid{border-color:#f1f5f9 !important;}
.fc td,.fc th{border-color:#f1f5f9 !important;}
.fc .fc-toolbar{margin-bottom:16px !important;}

/* ── Modal ── */
.modal-bg{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(4px);z-index:500;align-items:center;justify-content:center;padding:20px;}
.modal-card{background:#fff;border-radius:18px;width:100%;max-width:520px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.15);}
.modal-head{padding:22px 26px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;background:#fff;z-index:1;}
.modal-head-title{font-size:1.1rem;font-weight:700;color:#1e293b;}
.modal-close{width:32px;height:32px;border-radius:8px;border:none;background:#f1f5f9;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#64748b;transition:.2s;}
.modal-close:hover{background:#fee2e2;color:#dc2626;}
.modal-body{padding:24px 26px;}
.modal-foot{padding:16px 26px;border-top:1px solid #f1f5f9;display:flex;gap:10px;justify-content:flex-end;}

/* ── Form fields ── */
.f-row{margin-bottom:18px;}
.f-row-2{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:18px;}
.f-label{display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:7px;letter-spacing:.01em;}
.f-input{width:100%;padding:10px 13px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:.875rem;font-family:'Outfit',sans-serif;color:#1e293b;background:#fafafa;outline:none;transition:.2s;}
.f-input:focus{border-color:#F7DF79;background:#fff;box-shadow:0 0 0 3px rgba(247,223,121,.1);}
.f-input::placeholder{color:#9ca3af;}
textarea.f-input{resize:vertical;min-height:80px;}
</style>

{{-- Page header --}}
<div class="cal-header">
    <div>
        <div class="cal-title">Appointment Calendar</div>
        <div class="cal-sub">Manage and schedule all appointments</div>
    </div>
    <div class="header-actions">
        <a href="{{ route('appointments.index') }}" class="btn-outline">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:5px;"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
            List View
        </a>
        <a href="{{ route('appointments.create') }}" class="btn-solid">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:5px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Appointment
        </a>
    </div>
</div>

{{-- Layout --}}
<div class="cal-layout">

    {{-- Sidebar --}}
    <div class="cal-sidebar">
        <div class="sidebar-panel">
            <div class="sidebar-panel-title">Staff Members</div>
            @php $colors = ['#F7DF79','#3b82f6','#f59e0b','#ec4899','#8b5cf6','#06b6d4']; @endphp
            @foreach($staffMembers as $i => $staff)
            <div class="staff-item">
                <input type="checkbox" value="{{ $staff->id }}" checked onchange="filterStaff()" id="staff_{{ $staff->id }}">
                <span class="staff-dot" style="background:{{ $colors[$i % count($colors)] }}"></span>
                <label class="staff-name" for="staff_{{ $staff->id }}" style="cursor:pointer;">{{ $staff->name }}</label>
            </div>
            @endforeach
        </div>

        <div class="sidebar-panel">
            <div class="sidebar-panel-title">Status Legend</div>
            <div class="legend-item"><span class="legend-dot" style="background:#3b82f6"></span><span class="legend-label">Scheduled</span></div>
            <div class="legend-item"><span class="legend-dot" style="background:#F7DF79"></span><span class="legend-label">Confirmed</span></div>
            <div class="legend-item"><span class="legend-dot" style="background:#8b5cf6"></span><span class="legend-label">Completed</span></div>
            <div class="legend-item"><span class="legend-dot" style="background:#ef4444"></span><span class="legend-label">Cancelled</span></div>
        </div>
    </div>

    {{-- Calendar --}}
    <div class="cal-main">
        <div class="cal-wrap">
            <div id="calendar"></div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
let calendar;
document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('calendar');
    calendar = new FullCalendar.Calendar(el, {
        initialView: 'timeGridWeek',
        height: 'calc(100vh - 220px)',
        headerToolbar: { left:'prev,next today', center:'title', right:'dayGridMonth,timeGridWeek,timeGridDay' },
        events: '{{ route("appointments.events") }}',
        editable: true,
        nowIndicator: true,
        slotMinTime: '{{ $openingTime }}',
        slotMaxTime: '{{ $calendarMaxTime }}',
        allDaySlot: false,
        slotEventOverlap: true,
        dayMaxEvents: true,
        eventTimeFormat: { hour:'numeric', minute:'2-digit', meridiem:'short' },
        eventContent: function(arg) {
            let status = arg.event.extendedProps.status || 'scheduled';
            let staff = arg.event.extendedProps.staff || 'Unassigned';
            let customer = arg.event.extendedProps.customer || 'Guest';
            let service = arg.event.extendedProps.service || 'Service';
            
            // Get base colors based on status
            let colors = {
                arrived:   { bg: '#f0fdf4', border: '#22c55e', text: '#166534' },
                late:      { bg: '#fef9c3', border: '#eab308', text: '#854d0e' },
                discarded: { bg: '#fef2f2', border: '#ef4444', text: '#991b1b' },
                completed: { bg: '#faf5ff', border: '#a855f7', text: '#6b21a8' },
                cancelled: { bg: '#f8fafc', border: '#64748b', text: '#334155' },
                scheduled: { bg: '#eff6ff', border: '#3b82f6', text: '#1e40af' }
            };
            
            let scheme = colors[status] || colors.scheduled;
            
            let el = document.createElement('div');
            el.className = 'fc-custom-event-card';
            el.style.display = 'flex';
            el.style.flexDirection = 'column';
            el.style.height = '100%';
            el.style.width = '100%';
            el.style.padding = '4px 6px 4px 8px';
            el.style.borderRadius = '6px';
            el.style.background = scheme.bg;
            el.style.borderLeft = `3.5px solid ${scheme.border}`;
            el.style.color = scheme.text;
            el.style.overflow = 'hidden';
            el.style.fontSize = '0.75rem';
            el.style.lineHeight = '1.25';
            el.style.gap = '1px';
            el.style.boxShadow = '0 1px 3px rgba(0,0,0,0.06)';
            
            el.innerHTML = `
                <div style="font-size:0.58rem; font-weight:800; opacity:0.85; text-transform:uppercase; letter-spacing:0.02em;">${status}</div>
                <div style="font-weight:700; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; margin-bottom:1px;" title="${customer}">${customer}</div>
                <div style="font-size:0.68rem; font-weight:600; opacity:0.9; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="${service}">${service}</div>
                <div style="font-size:0.62rem; opacity:0.75; display:flex; align-items:center; gap:3px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="Staff: ${staff}">
                    <svg width="8" height="8" fill="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    <span>${staff}</span>
                </div>
            `;
            return { domNodes: [el] };
        },
        eventDrop: function(info) { updateTime(info.event, info.revert); },
        eventResize: function(info) { updateTime(info.event, info.revert); },
        eventClick: function(info) {
            window.location.href = '{{ url("/appointments") }}/' + info.event.id + '/edit';
        }
    });
    calendar.render();
});


function filterStaff() {
    const checked = Array.from(document.querySelectorAll('.staff-item input:checked')).map(i => i.value);
    calendar.getEvents().forEach(ev => {
        const show = !ev.extendedProps.staff_id || checked.includes(String(ev.extendedProps.staff_id));
        ev.setProp('display', show ? 'auto' : 'none');
    });
}

function updateTime(event, revert) {
    fetch('{{ url("/appointments") }}/' + event.id + '/update-time', {
        method: 'PATCH',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ start: event.start.toISOString(), end: event.end ? event.end.toISOString() : null })
    })
    .then(r => r.json())
    .then(d => { if (d.error) { alert(d.error); revert(); } })
    .catch(() => revert());
}
</script>
@endsection
