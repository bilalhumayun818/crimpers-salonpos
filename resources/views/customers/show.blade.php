@extends('layouts.app')
@section('title', $customer->name)
@section('content')
<style>
:root{--y1:#F7DF79;--y2:#FBEFBC;--yd:#c9a800;--ydark:#a07800;--ybg:#fffdf0;}
.show-wrap{max-width:1020px;margin:0 auto;}

/* ── Header ── */
.show-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;gap:16px;flex-wrap:wrap;}
.back-btn{width:36px;height:36px;border-radius:9px;border:1.5px solid #e4e4e7;background:#fff;display:flex;align-items:center;justify-content:center;color:#71717a;text-decoration:none;transition:.2s;flex-shrink:0;}
.back-btn:hover{border-color:var(--y1);color:var(--ydark);background:var(--ybg);}
.hdr-actions{display:flex;gap:8px;}
.btn-edit{padding:8px 16px;background:var(--y2);color:var(--ydark);border:1.5px solid var(--y1);border-radius:9px;text-decoration:none;font-weight:700;font-size:.82rem;display:inline-flex;align-items:center;gap:5px;transition:.15s;}
.btn-edit:hover{background:var(--y1);}
.btn-del{padding:8px 16px;background:#fef2f2;color:#dc2626;border:1.5px solid #fecaca;border-radius:9px;font-weight:700;font-size:.82rem;cursor:pointer;font-family:'Outfit',sans-serif;display:inline-flex;align-items:center;gap:5px;transition:.15s;}
.btn-del:hover{background:#fee2e2;}

/* ── Hero card ── */
.hero-card{background:#fff;border:1.5px solid #f0e8a0;border-radius:18px;overflow:hidden;margin-bottom:18px;box-shadow:0 2px 12px rgba(0,0,0,.05);}
.hero-banner{height:140px;background:linear-gradient(135deg,var(--y2) 0%,var(--y1) 100%);position:relative;overflow:hidden;}
.hero-banner::after{content:'';position:absolute;inset:0;background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23c9a800' fill-opacity='0.08'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");}
.hero-banner-img{width:100%;height:100%;object-fit:cover;cursor:zoom-in;transition:transform .3s;}
.hero-banner-img:hover{transform:scale(1.03);}

.hero-body{padding:28px;display:flex;align-items:center;gap:24px;position:relative;}
.hero-avatar{width:110px;height:110px;border-radius:50%;border:4px solid #fff;box-shadow:0 10px 25px rgba(0,0,0,0.1);overflow:hidden;background:var(--y2);flex-shrink:0;cursor:zoom-in;margin-top:-60px;}
.hero-avatar img{width:100%;height:100%;object-fit:cover;}
.hero-avatar .av-init{width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:2.5rem;font-weight:800;color:var(--ydark);}
.hero-info{flex:1;}
.hero-name{font-size:2rem;font-weight:800;color:#18181b;letter-spacing:-.03em;margin-bottom:6px;line-height:1.1;}
.hero-meta{display:flex;gap:18px;flex-wrap:wrap;align-items:center;}
.hero-meta-item{display:flex;align-items:center;gap:6px;font-size:.85rem;color:#64748b;font-weight:500;}
.social-header-list{display:flex;gap:8px;margin-top:10px;flex-wrap:wrap;}
.social-tag{padding:4px 12px;background:var(--ybg);border:1.5px solid #f0e8a0;border-radius:99px;font-size:.75rem;font-weight:700;color:var(--ydark);}

/* ── Stats row ── */
.stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:18px;}
.stat-card{background:#fff;border:1.5px solid #f0e8a0;border-radius:13px;padding:14px 16px;box-shadow:0 1px 4px rgba(0,0,0,.04);transition:.2s;}
.stat-card:hover{border-color:var(--y1);box-shadow:0 3px 12px rgba(247,223,121,.2);}
.stat-icon{width:34px;height:34px;border-radius:9px;background:var(--y2);display:flex;align-items:center;justify-content:center;color:var(--ydark);margin-bottom:10px;}
.stat-val{font-size:1.3rem;font-weight:800;color:#18181b;line-height:1;margin-bottom:3px;}
.stat-lbl{font-size:.65rem;font-weight:700;color:#a1a1aa;text-transform:uppercase;letter-spacing:.08em;}

/* ── Content grid ── */
.content-grid{display:grid;grid-template-columns:1fr 340px;gap:16px;}

/* ── Panel ── */
.panel{background:#fff;border:1.5px solid #f0e8a0;border-radius:14px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.04);margin-bottom:14px;}
.panel-head{padding:14px 18px;border-bottom:1px solid #f4f4f5;display:flex;align-items:center;justify-content:space-between;}
.panel-title{font-size:.85rem;font-weight:800;color:#18181b;display:flex;align-items:center;gap:7px;}
.panel-icon{width:26px;height:26px;border-radius:7px;background:var(--y2);display:flex;align-items:center;justify-content:center;color:var(--ydark);}
.panel-count{font-size:.72rem;color:#a1a1aa;}
.panel-body{padding:0 18px;}

/* ── Info rows ── */
.info-row{display:flex;justify-content:space-between;align-items:center;padding:11px 0;border-bottom:1px solid #f4f4f5;}
.info-row:last-child{border-bottom:none;}
.info-lbl{font-size:.75rem;font-weight:600;color:#a1a1aa;text-transform:uppercase;letter-spacing:.07em;}
.info-val{font-size:.85rem;font-weight:600;color:#18181b;text-align:right;max-width:200px;}

/* ── Activity rows ── */
.act-row{display:flex;align-items:center;gap:11px;padding:10px 0;border-bottom:1px solid #f4f4f5;}
.act-row:last-child{border-bottom:none;}
.act-icon{width:32px;height:32px;border-radius:8px;background:var(--ybg);display:flex;align-items:center;justify-content:center;color:var(--ydark);flex-shrink:0;}
.act-info{flex:1;min-width:0;}
.act-name{font-size:.82rem;font-weight:600;color:#18181b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.act-meta{font-size:.7rem;color:#a1a1aa;margin-top:1px;}
.act-right{text-align:right;flex-shrink:0;}
.act-val{font-size:.82rem;font-weight:700;color:var(--ydark);}
.act-time{font-size:.68rem;color:#a1a1aa;margin-top:1px;}

.status-pill{display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:99px;font-size:.65rem;font-weight:700;}
.sp-confirmed{background:var(--y2);color:var(--ydark);}
.sp-completed{background:#f3e8ff;color:#7c3aed;}
.sp-cancelled{background:#fee2e2;color:#b91c1c;}
.sp-scheduled{background:#dbeafe;color:#1d4ed8;}

/* ── Social tags ── */
.social-list{display:flex;flex-wrap:wrap;gap:7px;padding:14px 0;}
.social-tag{padding:4px 12px;background:var(--ybg);border:1px solid #f0e8a0;border-radius:99px;font-size:.75rem;font-weight:700;color:var(--ydark);}

/* ── Notes ── */
.notes-box{padding:14px 0;font-size:.85rem;color:#52525b;line-height:1.6;}

.empty-msg{padding:24px;text-align:center;color:#a1a1aa;font-size:.82rem;}

/* ── Modals ── */
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(4px);display:none;align-items:center;justify-content:center;z-index:999;padding:20px;}
.modal-box{background:#fff;border-radius:18px;width:100%;max-width:380px;padding:26px;text-align:center;box-shadow:0 20px 50px rgba(0,0,0,.2);}
.modal-icon{width:50px;height:50px;background:#fef2f2;color:#dc2626;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;}
.modal-title{font-size:1.05rem;font-weight:800;color:#18181b;margin-bottom:6px;}
.modal-text{font-size:.82rem;color:#71717a;line-height:1.6;margin-bottom:20px;}
.modal-footer{display:flex;gap:10px;}
.btn-m{flex:1;padding:10px;border-radius:9px;font-size:.85rem;font-weight:700;cursor:pointer;transition:.2s;border:none;font-family:'Outfit',sans-serif;}
.btn-m-cancel{background:#f4f4f5;color:#52525b;}
.btn-m-cancel:hover{background:#e4e4e7;}
.btn-m-confirm{background:#dc2626;color:#fff;}
.btn-m-confirm:hover{background:#b91c1c;transform:translateY(-1px);}

.img-modal{position:fixed;inset:0;background:rgba(0,0,0,.88);backdrop-filter:blur(8px);display:none;align-items:center;justify-content:center;z-index:2000;padding:20px;cursor:zoom-out;}
.img-modal-content{max-width:88%;max-height:88vh;border-radius:16px;box-shadow:0 25px 60px rgba(0,0,0,.5);object-fit:contain;}
</style>

<div class="show-wrap">

    {{-- Header --}}
    <div class="show-header">
        <div style="display:flex;align-items:center;gap:10px;">
            <a href="{{ route('customers.index') }}" class="back-btn">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            </a>
            <div>
                <div style="font-size:1.1rem;font-weight:800;color:#18181b;">{{ $customer->name }}</div>
                <div style="font-size:.78rem;color:#a1a1aa;">Customer Profile</div>
            </div>
        </div>
        <div class="hdr-actions">
            <a href="{{ route('customers.edit',$customer) }}" class="btn-edit">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit Profile
            </a>
            <button type="button" class="btn-del" onclick="showDelModal()">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                Delete
            </button>
        </div>
    </div>

    {{-- Hero card --}}
    <div class="hero-card">
        <div class="hero-banner"></div>
        <div class="hero-body">
            <div class="hero-avatar" onclick="@if($customer->image_path) previewImage('{{ asset('storage/'.$customer->image_path) }}') @endif">
                @if($customer->image_path)
                    <img src="{{ asset('storage/'.$customer->image_path) }}" alt="{{ $customer->name }}">
                @else
                    <div class="av-init">{{ strtoupper(substr($customer->name,0,1)) }}</div>
                @endif
            </div>
            <div class="hero-info">
                <div class="hero-name">{{ $customer->name }}</div>
                <div class="hero-meta">
                    @if($customer->phone)
                    <div class="hero-meta-item">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l2.27-2.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                        {{ $customer->phone }}
                    </div>
                    @endif
                    @if($customer->email)
                    <div class="hero-meta-item">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ $customer->email }}
                    </div>
                    @endif
                    <div class="hero-meta-item">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Since {{ $customer->created_at->format('M Y') }}
                    </div>
                </div>
                
                @if($customer->social_media && is_array($customer->social_media))
                <div class="social-header-list">
                    @foreach($customer->social_media as $id)
                    <span class="social-tag">@ {{ $id }}</span>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
            <div class="stat-val">{{ $customer->appointments()->count() }}</div>
            <div class="stat-lbl">Appointments</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
            <div class="stat-val">{{ $customer->invoices()->count() }}</div>
            <div class="stat-lbl">Invoices</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div>
            <div class="stat-val" style="font-size:1rem;">PKR {{ number_format($customer->invoices()->sum('payable_amount'),0) }}</div>
            <div class="stat-lbl">Total Spent</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div>
            <div class="stat-val">{{ $lastVisit ? $lastVisit->format('M d, Y') : 'Never' }}</div>
            <div class="stat-lbl">Last Visit</div>
        </div>
    </div>

    {{-- Content grid --}}
    <div class="content-grid">

        {{-- Left --}}
        <div>
            {{-- Appointments --}}
            <div class="panel">
                <div class="panel-head">
                    <div class="panel-title">
                        <div class="panel-icon"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                        Appointment History
                    </div>
                    <span class="panel-count">{{ $appointments->count() }} total</span>
                </div>
                <div class="panel-body">
                    @forelse($appointments as $appt)
                    <div class="act-row">
                        <div class="act-icon"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                        <div class="act-info">
                            <div class="act-name">{{ $appt->service->name }}</div>
                            <div class="act-meta">{{ $appt->appointment_date->format('M d, Y') }} &middot; {{ $appt->start_time->format('g:i A') }}</div>
                        </div>
                        <div class="act-right">
                            <span class="status-pill sp-{{ $appt->status }}">{{ ucfirst($appt->status) }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="empty-msg">No appointments yet</div>
                    @endforelse
                </div>
            </div>

            {{-- Invoices --}}
            <div class="panel">
                <div class="panel-head">
                    <div class="panel-title">
                        <div class="panel-icon"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                        Invoice History
                    </div>
                    <span class="panel-count">{{ $invoices->count() }} total</span>
                </div>
                <div class="panel-body">
                    @forelse($invoices as $inv)
                    <div class="act-row">
                        <div class="act-icon"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                        <div class="act-info">
                            <div class="act-name">{{ $inv->invoice_no }}</div>
                            <div class="act-meta" style="color: #64748b; font-weight: 500; margin: 3px 0; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $inv->items ? implode(', ', $inv->items->pluck('custom_name')->toArray()) : '' }}">
                                @if($inv->items && $inv->items->count())
                                    {{ implode(', ', $inv->items->pluck('custom_name')->toArray()) }}
                                @else
                                    No items listed
                                @endif
                            </div>
                            <div class="act-meta">{{ ucfirst($inv->payment_method) }} &middot; {{ $inv->created_at->format('M d, Y') }}</div>
                        </div>
                        <div class="act-right">
                            <div class="act-val">PKR {{ number_format($inv->payable_amount,0) }}</div>
                            <div class="act-time">{{ $inv->created_at->format('g:i A') }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="empty-msg">No invoices yet</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right sidebar --}}
        <div>
            {{-- Profile details --}}
            <div class="panel">
                <div class="panel-head">
                    <div class="panel-title">
                        <div class="panel-icon"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
                        Profile Details
                    </div>
                </div>
                <div class="panel-body">
                    <div class="info-row"><span class="info-lbl">Phone</span><span class="info-val">{{ $customer->phone ?? '—' }}</span></div>
                    <div class="info-row"><span class="info-lbl">Email</span><span class="info-val" style="font-size:.78rem;">{{ $customer->email ?? '—' }}</span></div>
                    <div class="info-row"><span class="info-lbl">Birthday</span><span class="info-val">{{ $customer->birthday ? $customer->birthday->format('M d, Y') : '—' }}</span></div>
                    <div class="info-row"><span class="info-lbl">Membership</span><span class="info-val">{{ $customer->membership_type ?? 'None' }}</span></div>
                    <div class="info-row"><span class="info-lbl">Prepaid Credit</span><span class="info-val" style="color:var(--ydark);font-weight:800;">PKR {{ number_format($customer->prepaid_credit ?? 0,2) }}</span></div>
                </div>
            </div>



            {{-- Notes --}}
            @if($customer->notes)
            <div class="panel">
                <div class="panel-head">
                    <div class="panel-title">
                        <div class="panel-icon"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></div>
                        Notes
                    </div>
                </div>
                <div class="panel-body">
                    <div class="notes-box">{{ $customer->notes }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Delete modal --}}
<div class="modal-overlay" id="delModal">
    <div class="modal-box">
        <div class="modal-icon">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
        </div>
        <div class="modal-title">Delete Customer?</div>
        <div class="modal-text">Are you sure you want to permanently delete <strong>{{ $customer->name }}</strong>? This cannot be undone.</div>
        <div class="modal-footer">
            <button class="btn-m btn-m-cancel" onclick="hideDelModal()">Cancel</button>
            <button class="btn-m btn-m-confirm" onclick="document.getElementById('del-form').submit()">Yes, Delete</button>
        </div>
    </div>
</div>
<form id="del-form" method="POST" action="{{ route('customers.destroy',$customer) }}" style="display:none;">
    @csrf @method('DELETE')
</form>

{{-- Image zoom modal --}}
<div class="img-modal" id="imgModal" onclick="this.style.display='none'">
    <img src="" alt="" class="img-modal-content" id="modalImg">
</div>

<script>
function showDelModal(){ document.getElementById('delModal').style.display='flex'; }
function hideDelModal(){ document.getElementById('delModal').style.display='none'; }
function previewImage(src){ document.getElementById('modalImg').src=src; document.getElementById('imgModal').style.display='flex'; }
</script>
@endsection
