<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $booking->transaction_id }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" />

    <style>
        body {
            background: #fff;
            color: #333;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            font-size: 16px;
            line-height: 24px;
            font-family: 'Public Sans', sans-serif;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header img {
            max-width: 150px;
        }

        .invoice-title {
            font-size: 2rem;
            font-weight: bold;
            color: #566a7f;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-size: 0.8rem;
            color: #888;
        }

        .info-group {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        table td {
            padding: 10px;
            vertical-align: top;
        }

        table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        table tr.item td {
            border-bottom: 1px solid #eee;
        }

        table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        @media print {
            .no-print {
                display: none;
            }

            .invoice-box {
                box-shadow: none;
                border: 0;
            }
        }
    </style>
</head>

<body>

    <div class="text-center my-4 no-print">
        <button onclick="window.print()" class="btn btn-primary me-2">Print Invoice</button>
        <button onclick="window.close()" class="btn btn-outline-secondary">Close</button>
    </div>

    <div class="invoice-box">
        <div class="header">
            <div>
                <!-- Assuming Logo Exists or using text -->
                <h2 class="fw-bold text-primary">Learnit</h2>
            </div>
            <div class="text-end">
                <div class="invoice-title">INVOICE</div>
                <div>#{{ $booking->transaction_id }}</div>
                <div>Date: {{ $booking->created_at->format('F d, Y') }}</div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <div class="section-title">Billed To:</div>
                <strong>{{ $booking->organization->name }}</strong><br>
                {{ $booking->organization->email }}<br>
                {{ $booking->organization->phone ?? '' }}<br>
                {{ $booking->organization->city ?? '' }} {{ $booking->organization->state ?? '' }}
            </div>
            <div class="col-6 text-end">
                <div class="section-title">Payment Method:</div>
                Online Payment<br>
                <strong>Status: <span class="text-uppercase">{{ $booking->payment_status }}</span></strong>
            </div>
        </div>

        <table>
            <tr class="heading">
                <td>Item Description</td>
                <td class="text-end">Price</td>
            </tr>

            <tr class="item">
                <td>
                    <strong>{{ $booking->requirement->program->title }}</strong><br>
                    <small>Trainer: {{ $booking->trainer->name ?? 'Assigned Trainer' }}</small><br>
                    <small>Training Dates:
                        {{ \Carbon\Carbon::parse($booking->requirement->schedule_start)->format('d M Y') }} -
                        {{ \Carbon\Carbon::parse($booking->requirement->schedule_end)->format('d M Y') }}</small>
                </td>
                <td class="text-end">
                    ${{ number_format($booking->amount, 2) }}
                </td>
            </tr>

            <tr class="total">
                <td></td>
                <td class="text-end">
                    Total: ${{ number_format($booking->amount, 2) }}
                </td>
            </tr>
        </table>

        <div class="mt-5 text-center text-muted small">
            <p>Thank you for your business!</p>
            <p>If you have any questions about this invoice, please contact support.</p>
        </div>
    </div>

</body>

</html>