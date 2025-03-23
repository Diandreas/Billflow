<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture {{ $bill->reference }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.4;
            color: #333;
        }

        .container {
            padding: 20px;
        }

        .header {
            margin-bottom: 40px;
        }

        .company-info {
            float: left;
            width: 60%;
        }

        .bill-info {
            float: right;
            text-align: right;
            width: 40%;
        }

        .client-info {
            clear: both;
            padding-top: 40px;
            margin-bottom: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
        }

        .amounts {
            float: right;
            width: 300px;
            margin-top: 20px;
        }

        .amounts table {
            width: 100%;
        }

        .amounts td {
            border: none;
            padding: 5px;
        }

        .amounts td:last-child {
            text-align: right;
        }

        .total {
            font-weight: bold;
            font-size: 16px;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            text-align: center;
        }

        .logo {
            max-width: 200px;
            max-height: 100px;
            margin-bottom: 20px;
            object-fit: contain;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- En-tête -->
    <div class="header">
        <div class="company-info">
            @if($settings->logo_path)
                <img src="{{ $settings->logo_real_path ?? Storage::path($settings->logo_path) }}" alt="Logo" class="logo">
            @endif
            <h1>{{ $settings->company_name }}</h1>
            <p>
                {!! nl2br(e($settings->address)) !!}<br>
                Tel: {{ $settings->phone }}<br>
                Email: {{ $settings->email }}<br>
                Site: {{ $settings->website }}
            </p>
        </div>

        <div class="bill-info">
            <h2>FACTURE</h2>
            <p>
                N° : {{ $bill->reference }}<br>
                Date : {{ $bill->date->format('d/m/Y') }}<br>
            </p>
        </div>
    </div>

    <!-- Informations client -->
    <div class="client-info">
        <h3>Facturer à:</h3>
        <p>
            {{ $bill->client->name }}<br>
            {!! nl2br(e($bill->client->address)) !!}<br>
            @if($bill->client->email)
                Email: {{ $bill->client->email }}
            @endif
        </p>
    </div>

    <!-- Tableau des produits -->
    <table>
        <thead>
        <tr>
            <th>Description</th>
            <th style="text-align: right">Prix unitaire</th>
            <th style="text-align: right">Quantité</th>
            <th style="text-align: right">Total HT</th>
        </tr>
        </thead>
        <tbody>
        @foreach($bill->products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td style="text-align: right">{{ number_format($product->pivot->unit_price, 0, ',', ' ') }} FCFA</td>
                <td style="text-align: right">{{ $product->pivot->quantity }}</td>
                <td style="text-align: right">{{ number_format($product->pivot->total, 0, ',', ' ') }} FCFA</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <!-- Totaux -->
    <div class="amounts">
        <table>
            <tr>
                <td>Total HT:</td>
                <td>{{ number_format($bill->total - $bill->tax_amount, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td>TVA ({{ $bill->tax_rate }}%):</td>
                <td>{{ number_format($bill->tax_amount, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr class="total">
                <td>Total TTC:</td>
                <td>{{ number_format($bill->total, 0, ',', ' ') }} FCFA</td>
            </tr>
        </table>
    </div>

    <!-- Pied de page -->
    <div class="footer">
        <p>
            {{ $settings->company_name }} - SIRET: {{ $settings->siret }}
        </p>
    </div>
</div>
</body>
</html>
