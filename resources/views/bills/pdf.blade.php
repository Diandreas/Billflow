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
            margin-bottom: 40px;
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

        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }
        
        .footer-container {
            clear: both;
            position: relative;
            margin-top: 70px;
        }

        .footer {
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
        
        .print-info {
            font-size: 8px;
            text-align: right;
            margin-top: 10px;
            color: #999;
        }
        
        .reprint-mark {
            position: absolute;
            top: 40%;
            left: 30%;
            transform: rotate(-45deg);
            font-size: 60px;
            color: rgba(231, 76, 60, 0.15);
            z-index: -1;
        }
    </style>
</head>
<body>
@if(isset($bill->reprint_count) && $bill->reprint_count > 1)
<div class="reprint-mark">DUPLICATA</div>
@endif

<div class="container">
    <!-- En-tête -->
    <div class="header">
        <div class="company-info">
            @if(isset($settings->logo_path) && $settings->logo_path)
                <img src="{{ $settings->logo_real_path ?? Storage::path($settings->logo_path) }}" alt="Logo" class="logo">
            @endif
            <h1>{{ $settings->company_name ?? 'Entreprise' }}</h1>
            <p>
                {!! isset($settings->address) ? nl2br(e($settings->address)) : 'Adresse non spécifiée' !!}<br>
                Tel: {{ $settings->phone ?? 'Non spécifié' }}<br>
                Email: {{ $settings->email ?? 'Non spécifié' }}<br>
                Site: {{ $settings->website ?? 'Non spécifié' }}
            </p>
        </div>

        <div class="bill-info">
            <h2>FACTURE</h2>
            <p>
                N° : {{ $bill->reference }}<br>
                Date : {{ $bill->date->format('d/m/Y') }}<br>
                @if($bill->due_date)
                Échéance : {{ $bill->due_date->format('d/m/Y') }}<br>
                @endif
                Boutique : {{ $bill->shop->name }}<br>
                Vendeur : {{ $bill->seller->name }}<br>
                Statut : {{ $bill->status }}
            </p>
        </div>
    </div>

    <!-- Informations client -->
    <div class="client-info">
        <h3>Facturer à:</h3>
        <p>
            {{ $bill->client->name }}<br>
            {!! isset($bill->client->address) ? nl2br(e($bill->client->address)) : 'Adresse non spécifiée' !!}<br>
            Tél: {{ isset($bill->client->phones) && $bill->client->phones->count() > 0 ? $bill->client->phones->first()->number : 'Non spécifié' }}<br>
            @if(isset($bill->client->email) && $bill->client->email)
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
        @foreach($bill->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td style="text-align: right">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                <td style="text-align: right">{{ $item->quantity }}</td>
                <td style="text-align: right">{{ number_format($item->total, 0, ',', ' ') }} FCFA</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <!-- Totaux -->
    <div class="amounts clearfix">
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

    <!-- QR Code -->
    @if(isset($qrCode) && $qrCode)
    <div style="position: absolute; top: 90px; right: 30px; text-align: center;">
        <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code" style="width: 100px; height: 100px; max-width: 100px; max-height: 100px;">
        <p style="font-size: 9px; margin-top: 5px;">Scanner pour vérifier l'authenticité</p>
    </div>
    @else
    <div style="position: absolute; top: 90px; right: 30px; text-align: center; width: 100px; height: 100px; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center;">
        <p style="font-size: 9px; color: #999;">QR code non disponible</p>
    </div>
    @endif

    <!-- Pied de page -->
    <div class="footer-container">
        <div class="footer">
            <p>
                {{ $settings->company_name ?? 'Entreprise' }} - {{ isset($settings->siret) && $settings->siret ? 'SIRET: ' . $settings->siret : '' }}
            </p>
            <p>Nous vous remercions pour votre confiance.</p>
            <p>Cette facture constitue une preuve d'achat et peut être exigée pour tout service après-vente.</p>
        </div>
        
        <div class="print-info">
            Imprimé le {{ now()->format('d/m/Y H:i') }} 
            @if(isset($bill->reprint_count) && $bill->reprint_count > 1)
            (Réimpression #{{ $bill->reprint_count - 1 }})
            @endif
        </div>
    </div>
</div>
</body>
</html>
