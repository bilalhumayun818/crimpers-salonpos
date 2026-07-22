<!DOCTYPE html>
<html>
<head>
    <title>Invoice #{{ $invoice->invoice_no }}</title>
    <style>
        @media print { body { margin:0; padding:0; width:80mm; } .no-print { display:none; } }
        body { font-family:'Courier New',Courier,monospace; width:80mm; margin:0 auto; padding:10px; font-size:12px; color:#000; }
        .center { text-align:center; }
        .right  { text-align:right; }
        .divider { border-top:1px dashed #000; margin:8px 0; }
        .row { display:flex; justify-content:space-between; margin-bottom:4px; }
        .bold { font-weight:bold; }
        .total { font-size:15px; font-weight:bold; }
    </style>
</head>
<body onload="window.print()">
    <div class="center" style="margin-bottom:12px;">
        <div style="font-size:18px;font-weight:bold;">THE CRIMPERS</div>
        <div>123 Beauty Lane, Glamour City</div>
        <div>Tel: +1 234 567 890</div>
    </div>

    <div class="divider"></div>

    <div class="row"><span>Invoice: #{{ $invoice->invoice_no }}</span><span>{{ $invoice->created_at->format('d/m/Y') }}</span></div>
    <div class="row"><span>Payment: {{ strtoupper($invoice->payment_method) }}</span></div>

    <div class="divider"></div>

    @foreach($invoice->items as $item)
    <div class="row">
        <span style="flex:2;">{{ $item->itemizable->name }} x{{ $item->quantity }}</span>
        <span style="flex:1;" class="right">PKR {{ number_format($item->subtotal, 2) }}</span>
    </div>
    @endforeach

    <div class="divider"></div>

    <div class="row"><span>Subtotal</span><span>PKR {{ number_format($invoice->total_amount, 2) }}</span></div>
    @if(floatval($invoice->tax) > 0)
    <div class="row"><span>Tax (5%)</span><span>PKR {{ number_format($invoice->tax, 2) }}</span></div>
    @endif
    @if(floatval($invoice->discount) > 0)
    <div class="row"><span>Discount</span><span>-PKR {{ number_format($invoice->discount, 2) }}</span></div>
    @endif

    @php
        $derivedPreviousBalance = floatval($invoice->payable_amount) - (floatval($invoice->total_amount) - floatval($invoice->discount));
    @endphp
    @if($derivedPreviousBalance > 0.001)
    <div class="row"><span>Prev. Balance</span><span>+PKR {{ number_format($derivedPreviousBalance, 2) }}</span></div>
    @endif

    <div class="divider"></div>

    <div class="row total"><span>TOTAL</span><span>PKR {{ number_format($invoice->payable_amount, 2) }}</span></div>

    <div class="divider"></div>

    <div class="center" style="margin-top:12px;font-size:11px;">
        <div>Thank you for your visit!</div>
        <div>Follow us @TheCrimpers</div>
    </div>

    <div class="no-print" style="margin-top:16px;text-align:center;">
        <button onclick="window.close()" style="padding:8px 20px;cursor:pointer;border:1px solid #ccc;border-radius:6px;">Close</button>
    </div>
</body>
</html>
