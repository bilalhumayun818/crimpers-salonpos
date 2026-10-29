<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #2563eb;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px 12px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .total-row {
            background-color: #e3f2fd !important;
            font-weight: bold;
        }
        .summary {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .summary-item {
            text-align: center;
            padding: 10px;
            background: white;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .summary-item .value {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
        }
        .summary-item .label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sales Report</h1>
        <p>Generated on {{ now()->format('F d, Y \a\t H:i') }}</p>
        @if(request('period'))
            <p>Period: {{ ucwords(str_replace('_', ' ', request('period'))) }}</p>
        @elseif(request('date_from') && request('date_to'))
            <p>From: {{ request('date_from') }} To: {{ request('date_to') }}</p>
        @else
            <p>All Time Report</p>
        @endif
    </div>

    <div class="summary">
        <h3 style="margin: 0 0 15px 0; color: #333;">Summary Statistics</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="value">{{ $invoices->count() }}</div>
                <div class="label">Total Invoices</div>
            </div>
            <div class="summary-item">
                <div class="value">PKR {{ number_format($invoices->sum(fn ($invoice) => (float) $invoice->payable_amount + (float) ($invoice->extra_amount ?? 0)), 2) }}</div>
                <div class="label">Total Sales</div>
            </div>
            <div class="summary-item">
                <div class="value">PKR {{ $invoices->count() > 0 ? number_format($invoices->sum(fn ($invoice) => (float) $invoice->payable_amount + (float) ($invoice->extra_amount ?? 0)) / $invoices->count(), 2) : '0.00' }}</div>
                <div class="label">Average Sale</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Total Amount</th>
                <th>Payment Method</th>
                <th>Status</th>
                <th>Items</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
            <tr>
                <td>{{ $invoice->invoice_no }}</td>
                <td>{{ $invoice->created_at->format('M d, Y H:i') }}</td>
                <td>{{ $invoice->customer ? $invoice->customer->name : ($invoice->user->name ?? 'Walk-in') }}</td>
                <td>PKR {{ number_format($invoice->payable_amount + (float) ($invoice->extra_amount ?? 0), 2) }}
                    @if((float) ($invoice->extra_amount ?? 0) > 0)<small>(includes PKR {{ number_format($invoice->extra_amount, 2) }} extra)</small>@endif
                </td>
                <td>{{ ucfirst($invoice->payment_method) }}</td>
                <td>{{ ucfirst($invoice->status) }}</td>
                <td>{{ $invoice->items->count() }} items</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3" style="text-align: right; font-weight: bold;">TOTAL:</td>
                <td style="font-weight: bold;">PKR {{ number_format($invoices->sum(fn ($invoice) => (float) $invoice->payable_amount + (float) ($invoice->extra_amount ?? 0)), 2) }}</td>
                <td colspan="3"></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
