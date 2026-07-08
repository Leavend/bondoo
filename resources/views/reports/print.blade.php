<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report - {{ strtoupper($type) }}</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: #1d1d1f;
            margin: 40px;
            font-size: 14px;
            line-height: 1.5;
        }
        .header {
            margin-bottom: 30px;
            border-bottom: 2px solid #f5f5f7;
            padding-bottom: 20px;
        }
        h2 {
            margin: 0 0 5px 0;
            font-weight: 700;
            color: #000;
        }
        .meta {
            color: #86868b;
            font-size: 13px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #f5f5f7;
            font-weight: 600;
            text-align: left;
            padding: 10px 12px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #86868b;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #f5f5f7;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 11px;
            color: #86868b;
        }
        .no-print-btn {
            background: #0071e3;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 13px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            margin-bottom: 20px;
        }
        @media print {
            .no-print-btn {
                display: none;
            }
            body {
                margin: 20px;
            }
        }
    </style>
</head>
<body>
    <button class="no-print-btn" onclick="window.print()">Print to PDF</button>

    <div class="header">
        <h2>BONDOO RETAIL REPORT: {{ strtoupper($type) }}</h2>
        <div class="meta">
            Period: {{ Carbon\Carbon::parse($start_date)->format('d M Y') }} - {{ Carbon\Carbon::parse($end_date)->format('d M Y') }}<br>
            Generated at: {{ now()->format('d M Y H:i WITA') }}
        </div>
    </div>

    <table>
        <thead>
            @if ($type === 'sales')
                <tr>
                    <th>No.</th>
                    <th>Invoice No</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Payment Type</th>
                    <th class="text-right">Sub Total</th>
                    <th class="text-right">Total</th>
                    <th class="text-right">Paid</th>
                    <th class="text-right">Due (Piutang)</th>
                </tr>
            @elseif ($type === 'purchases')
                <tr>
                    <th>No.</th>
                    <th>PO Number</th>
                    <th>Date</th>
                    <th>Supplier</th>
                    <th>Status</th>
                    <th class="text-right">Sub Total</th>
                    <th class="text-right">Total</th>
                    <th class="text-right">Paid</th>
                    <th class="text-right">Due (Hutang)</th>
                </tr>
            @elseif ($type === 'adjustments')
                <tr>
                    <th>No.</th>
                    <th>Date</th>
                    <th>Product Code</th>
                    <th>Product Name</th>
                    <th>Correction Type</th>
                    <th>Discrepancy Qty</th>
                    <th>Auditor</th>
                </tr>
            @else
                <tr>
                    <th>No.</th>
                    <th>Date</th>
                    <th>Return Type</th>
                    <th>Ref Number</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th class="text-right">Refund Amount</th>
                </tr>
            @endif
        </thead>
        <tbody>
            @forelse($data as $idx => $row)
                @if ($type === 'sales')
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td><strong>{{ $row->invoice_no }}</strong></td>
                        <td>{{ $row->order_date->format('d-m-Y H:i') }}</td>
                        <td>{{ $row->customer->name ?? 'Walk-in' }}</td>
                        <td>{{ $row->payment_type }}</td>
                        <td class="text-right">Rp. {{ number_format($row->sub_total, 0, ',', '.') }}</td>
                        <td class="text-right">Rp. {{ number_format($row->total, 0, ',', '.') }}</td>
                        <td class="text-right">Rp. {{ number_format($row->pay_amount, 0, ',', '.') }}</td>
                        <td class="text-right">Rp. {{ number_format($row->due_amount, 0, ',', '.') }}</td>
                    </tr>
                @elseif ($type === 'purchases')
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td><strong>{{ $row->purchase_no }}</strong></td>
                        <td>{{ $row->purchase_date->format('d-m-Y') }}</td>
                        <td>{{ $row->supplier->name }}</td>
                        <td>{{ strtoupper($row->purchase_status) }}</td>
                        <td class="text-right">Rp. {{ number_format($row->sub_total, 0, ',', '.') }}</td>
                        <td class="text-right">Rp. {{ number_format($row->total, 0, ',', '.') }}</td>
                        <td class="text-right">Rp. {{ number_format($row->pay_amount, 0, ',', '.') }}</td>
                        <td class="text-right">Rp. {{ number_format($row->due_amount, 0, ',', '.') }}</td>
                    </tr>
                @elseif ($type === 'adjustments')
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>{{ $row->created_at->format('d-m-Y H:i') }}</td>
                        <td>{{ $row->product->code }}</td>
                        <td><strong>{{ $row->product->name }}</strong></td>
                        <td>{{ $row->type === 'addition' ? '+ Stock In' : '- Stock Out' }}</td>
                        <td>{{ $row->quantity }}</td>
                        <td>{{ $row->user->name ?? 'System' }}</td>
                    </tr>
                @else
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>{{ $row->created_at->format('d-m-Y H:i') }}</td>
                        <td>{{ $row->type === 'sales' ? 'Sales Return' : 'Purchase Return' }}</td>
                        <td><strong>{{ $row->reference_no }}</strong></td>
                        <td>{{ $row->product->name }}</td>
                        <td>{{ $row->quantity }}</td>
                        <td class="text-right">Rp. {{ number_format($row->refund_amount, 0, ',', '.') }}</td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; color: #86868b; padding: 30px;">
                        No records found for the selected period.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Bondoo POS &copy; {{ date('Y') }} - All rights reserved.
    </div>
</body>
</html>
