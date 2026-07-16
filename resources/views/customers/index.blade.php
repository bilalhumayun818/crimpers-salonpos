@extends('layouts.app')
@section('title', 'Customers')

@section('content')
<style>
:root{--y1:#F7DF79;--y2:#FBEFBC;--yd:#c9a800;--ydark:#a07800;}

/* Header */
.pg-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;gap:16px;flex-wrap:wrap;}
.pg-title{font-size:1.45rem;font-weight:800;color:#18181b;letter-spacing:-.02em;margin-bottom:3px;}
.pg-sub{font-size:.85rem;color:#71717a;}
.header-right{display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
.search-wrap{position:relative;}
.search-wrap svg{position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#a1a1aa;pointer-events:none;}
.search-input{padding:9px 14px 9px 36px;border:1.5px solid #E8EAED;border-radius:10px;font-size:.85rem;font-family:'Outfit',sans-serif;background:#F0F2F5;color:#18181b;outline:none;transition:.2s;width:230px;}
.search-input:focus{border-color:var(--yd);background:#fff;box-shadow:0 0 0 3px rgba(199,168,0,.1);}
.btn-search{padding:9px 16px;background:#3C4048;color:#fff;border:none;border-radius:10px;font-weight:700;font-size:.82rem;cursor:pointer;font-family:'Outfit',sans-serif;transition:.2s;margin-left:6px;}
.btn-search:hover{background:#18181b;}
.btn-add{padding:9px 18px;background:linear-gradient(135deg,var(--y1),var(--yd));color:#18181b;border:none;border-radius:10px;font-weight:800;font-size:.85rem;text-decoration:none;display:inline-flex;align-items:center;gap:7px;box-shadow:0 3px 10px rgba(199,168,0,.2);transition:.2s;}
.btn-add:hover{transform:translateY(-1px);box-shadow:0 5px 16px rgba(199,168,0,.3);color:#18181b;}

/* Grid */
.cust-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:16px;}

/* ── Card ── */
.cust-card{
    background:#fff;
    border-radius:16px;
    border:1.5px solid #E8EAED;
    box-shadow:0 1px 4px rgba(0,0,0,.05);
    transition:transform .2s,box-shadow .2s,border-color .2s;
    display:flex;
    flex-direction:column;
    overflow:hidden;
}
.cust-card:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 28px rgba(0,0,0,.09);
    border-color:#c9a800;
}

/* ── Card Header ── */
.card-head{
    padding:20px 18px 16px;
    display:flex;
    align-items:center;
    gap:14px;
    border-bottom:1.5px solid #F0F2F5;
}
.card-av{
    width:52px;height:52px;border-radius:14px;
    background:linear-gradient(135deg,var(--y1),var(--yd));
    display:flex;align-items:center;justify-content:center;
    font-size:1.3rem;font-weight:800;color:#18181b;
    flex-shrink:0;overflow:hidden;
    box-shadow:0 3px 8px rgba(199,168,0,.25);
}
.card-av img{width:100%;height:100%;object-fit:cover;}
.card-head-info{min-width:0;flex:1;}
.card-name{font-size:.95rem;font-weight:800;color:#18181b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:3px;}
.card-phone{font-size:.75rem;color:#71717a;display:flex;align-items:center;gap:5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.card-phone svg{flex-shrink:0;color:#a1a1aa;}

/* ── Card Body ── */
.card-body{padding:14px 18px;flex:1;display:flex;flex-direction:column;gap:10px;}

/* Info rows */
.info-row{display:flex;align-items:center;gap:8px;font-size:.78rem;color:#52525b;}
.info-row svg{flex-shrink:0;color:#a1a1aa;}
.info-row span{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}

/* Stats */
.card-stats{display:grid;grid-template-columns:1fr 1fr;gap:8px;}
.stat-tile{background:#F4F5F7;border-radius:10px;padding:9px 12px;text-align:center;}
.stat-val{font-size:.95rem;font-weight:800;color:#18181b;line-height:1;}
.stat-lbl{font-size:.58rem;font-weight:700;color:#a1a1aa;text-transform:uppercase;letter-spacing:.06em;margin-top:3px;}

/* ── Card Footer ── */
.card-foot{
    display:flex;gap:6px;
    padding:12px 18px;
    border-top:1.5px solid #F0F2F5;
}
.btn-act{
    flex:1;padding:8px 4px;border-radius:9px;
    font-size:.73rem;font-weight:700;cursor:pointer;
    transition:.15s;text-decoration:none;
    display:flex;align-items:center;justify-content:center;gap:4px;
    border:none;font-family:'Outfit',sans-serif;
}
.btn-view{background:var(--y2);color:var(--ydark);}
.btn-view:hover{background:var(--y1);color:#18181b;}
.btn-edit{background:#F0F2F5;color:#3C4048;}
.btn-edit:hover{background:#E8EAED;color:#18181b;}
.btn-del{background:#fef2f2;color:#dc2626;}
.btn-del:hover{background:#fee2e2;}

/* Empty */
.empty-state{grid-column:1/-1;padding:70px 20px;text-align:center;background:#fff;border-radius:16px;border:2px dashed #E8EAED;}
.empty-state p{font-size:.9rem;color:#a1a1aa;margin-top:12px;}

/* Modal */
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(4px);display:none;align-items:center;justify-content:center;z-index:999;padding:20px;}
.modal-box{background:#fff;border-radius:20px;width:100%;max-width:380px;padding:28px;text-align:center;box-shadow:0 20px 50px rgba(0,0,0,.2);}
.modal-icon{width:52px;height:52px;background:#fef2f2;color:#dc2626;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;}
.modal-title{font-size:1.1rem;font-weight:800;color:#18181b;margin-bottom:6px;}
.modal-text{font-size:.85rem;color:#71717a;line-height:1.6;margin-bottom:22px;}
.modal-footer{display:flex;gap:10px;}
.btn-m{flex:1;padding:11px;border-radius:10px;font-size:.85rem;font-weight:700;cursor:pointer;transition:.2s;border:none;font-family:'Outfit',sans-serif;}
.btn-m-cancel{background:#F0F2F5;color:#52525b;}
.btn-m-cancel:hover{background:#E8EAED;}
.btn-m-confirm{background:#dc2626;color:#fff;}
.btn-m-confirm:hover{background:#b91c1c;}

.toast-wrap{position:fixed;bottom:24px;right:24px;z-index:9999;}
.toast{background:#18181b;color:#fff;padding:12px 20px;border-radius:11px;font-weight:600;font-size:.85rem;box-shadow:0 8px 20px rgba(0,0,0,.15);display:none;}

/* Spreadsheet-style customer list */
.customer-table-wrap{background:#fff;border:1.5px solid #E8EAED;border-radius:14px;overflow:auto;box-shadow:0 2px 8px rgba(0,0,0,.04);}
.customer-table{width:100%;min-width:850px;border-collapse:collapse;}
.customer-table th{background:#f8fafc;color:#71717a;text-align:left;padding:12px 14px;font-size:.66rem;font-weight:800;letter-spacing:.07em;text-transform:uppercase;border-bottom:1.5px solid #E8EAED;}
.customer-table td{padding:13px 14px;border-bottom:1px solid #f0f2f5;font-size:.8rem;color:#52525b;vertical-align:middle;}
.customer-table tr:last-child td{border-bottom:none;}.customer-table tbody tr:hover{background:#fffdf0;}
.customer-cell{display:flex;align-items:center;gap:10px;}.customer-avatar{width:36px;height:36px;border-radius:9px;background:linear-gradient(135deg,var(--y1),var(--yd));display:flex;align-items:center;justify-content:center;font-weight:800;color:#18181b;overflow:hidden;flex-shrink:0;}.customer-avatar img{width:100%;height:100%;object-fit:cover;}.customer-name{font-weight:800;color:#18181b;}.customer-email{font-size:.7rem;color:#a1a1aa;margin-top:2px;}.visits-badge{background:var(--y2);border:1px solid var(--y1);color:var(--ydark);border-radius:99px;padding:3px 9px;font-weight:800;font-size:.7rem;}.amount-cell{font-weight:800;color:var(--ydark)!important;}.table-actions{display:flex;gap:5px;justify-content:flex-end;}.table-actions .btn-act{flex:none;padding:7px 9px;}
.cust-grid{display:none;}
</style>

<div class="pg-header">
    <div>
        <div class="pg-title">Customers</div>
        <div class="pg-sub">Manage your salon's client profiles</div>
    </div>
    <div class="header-right">
        <form action="{{ route('customers.index') }}" method="GET" style="display:flex;align-items:center;gap:0;">
            <div class="search-wrap">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" class="search-input" placeholder="Search clients…">
            </div>
            <button type="submit" class="btn-search">Search</button>
        </form>
        <a href="{{ route('customers.create') }}" class="btn-add">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Customer
        </a>
    </div>
</div>

<div class="customer-table-wrap">
    <table class="customer-table">
        <thead><tr><th>#</th><th>Customer</th><th>Phone</th><th>Visits</th><th>Total Spent</th><th>Joined</th><th style="text-align:right;">Actions</th></tr></thead>
        <tbody>
        @forelse($customers as $index => $customer)
            <tr id="customer-{{ $customer->id }}">
                <td>{{ $customers->firstItem() + $index }}</td>
                <td><div class="customer-cell"><div class="customer-avatar">@if($customer->image_path)<img src="{{ asset('storage/'.$customer->image_path) }}" alt="{{ $customer->name }}">@else{{ strtoupper(substr($customer->name, 0, 1)) }}@endif</div><div><div class="customer-name">{{ $customer->name }}</div><div class="customer-email">{{ $customer->email ?: 'No email' }}</div></div></div></td>
                <td>{{ $customer->phone ?? '—' }}</td>
                <td><span class="visits-badge">{{ $customer->invoices_count }} visits</span></td>
                <td class="amount-cell">PKR {{ number_format($customer->invoices_sum_payable_amount ?? 0, 0) }}</td>
                <td>{{ $customer->created_at->format('M d, Y') }}</td>
                <td><div class="table-actions"><a href="{{ route('customers.show', $customer) }}" class="btn-act btn-view">View</a><a href="{{ route('customers.edit', $customer) }}" class="btn-act btn-edit">Edit</a><button type="button" class="btn-act btn-del" onclick="confirmDelete({{ $customer->id }}, '{{ addslashes($customer->name) }}')">Delete</button></div></td>
            </tr>
        @empty
            <tr><td colspan="7" class="empty-state"><p>No customers found — add your first one</p></td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="cust-grid">
    @forelse($customers as $customer)
    <div class="cust-card" id="customer-{{ $customer->id }}">

        {{-- Header --}}
        <div class="card-head">
            <div class="card-av">
                @if($customer->image_path)
                    <img src="{{ asset('storage/'.$customer->image_path) }}" alt="{{ $customer->name }}">
                @else
                    {{ strtoupper(substr($customer->name,0,1)) }}
                @endif
            </div>
            <div class="card-head-info">
                <div class="card-name">{{ $customer->name }}</div>
                <div class="card-phone">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l2.27-2.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                    {{ $customer->phone ?? '—' }}
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="card-body">
            @if($customer->email)
            <div class="info-row">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                <span>{{ $customer->email }}</span>
            </div>
            @endif

            <div class="card-stats">
                <div class="stat-tile">
                    <div class="stat-val">{{ $customer->invoices()->count() }}</div>
                    <div class="stat-lbl">Visits</div>
                </div>
                <div class="stat-tile">
                    <div class="stat-val" style="font-size:.78rem;">PKR {{ number_format($customer->invoices()->sum('payable_amount'), 0) }}</div>
                    <div class="stat-lbl">Total Spent</div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="card-foot">
            <a href="{{ route('customers.show',$customer) }}" class="btn-act btn-view">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                View
            </a>
            <a href="{{ route('customers.edit',$customer) }}" class="btn-act btn-edit">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit
            </a>
            <button type="button" class="btn-act btn-del" onclick="confirmDelete({{ $customer->id }},'{{ addslashes($customer->name) }}')">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                Delete
            </button>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <svg width="48" height="48" fill="none" stroke="#a1a1aa" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto;display:block;"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87m-4-12a4 4 0 010 7.75"/></svg>
        <p>No customers found — add your first one</p>
    </div>
    @endforelse
</div>

<div style="margin-top:24px;">{{ $customers->links() }}</div>

{{-- Delete modal --}}
<div class="modal-overlay" id="delModal">
    <div class="modal-box">
        <div class="modal-icon">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
        </div>
        <div class="modal-title">Delete Customer?</div>
        <div class="modal-text" id="del_msg">This action is permanent and cannot be undone.</div>
        <div class="modal-footer">
            <button class="btn-m btn-m-cancel" onclick="hideModal()">Cancel</button>
            <button class="btn-m btn-m-confirm" id="btn-confirm-del">Yes, Delete</button>
        </div>
    </div>
</div>

<div class="toast-wrap"><div class="toast" id="toast"></div></div>

<script>
let deleteId = null;
function confirmDelete(id, name) {
    deleteId = id;
    document.getElementById('del_msg').textContent = `Are you sure you want to permanently delete "${name}"?`;
    document.getElementById('delModal').style.display = 'flex';
}
function hideModal() {
    document.getElementById('delModal').style.display = 'none';
    deleteId = null;
}
document.getElementById('btn-confirm-del').addEventListener('click', async function() {
    if (!deleteId) return;
    const btn = this;
    btn.disabled = true; btn.textContent = 'Deleting…';
    let res, data;
    try {
        res = await fetch(`{{ url('customers') }}/${deleteId}`, {
            method: 'POST',
            headers: { 
                'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                'Accept': 'application/json',
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: '_method=DELETE'
        });
        data = await res.json();
    } catch (e) {
        showToast('Server error during deletion.');
        hideModal(); btn.disabled = false; btn.textContent = 'Yes, Delete';
        return;
    }
    if (data.success) {
        const card = document.getElementById(`customer-${deleteId}`);
        card.style.transition = 'opacity .3s, transform .3s';
        card.style.opacity = '0'; card.style.transform = 'scale(.95)';
        setTimeout(() => { card.remove(); if (!document.querySelector('.cust-card')) location.reload(); }, 300);
        showToast(data.message || 'Customer deleted');
    }
    hideModal(); btn.disabled = false; btn.textContent = 'Yes, Delete';
});
function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg; t.style.display = 'block';
    setTimeout(() => t.style.display = 'none', 3000);
}
</script>
@endsection
