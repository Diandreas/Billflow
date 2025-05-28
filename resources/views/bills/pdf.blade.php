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

        .barter-info {
            clear: both;
            margin-top: 10px;
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f3e8ff;
            border: 1px solid #d8b4fe;
            border-radius: 5px;
        }

        .barter-info h3 {
            color: #7e22ce;
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .barter-info p {
            margin-bottom: 10px;
            font-size: 12px;
        }

        .barter-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 12px;
        }

        .barter-table td {
            padding: 5px;
            border: none;
        }

        .barter-table tr:last-child td {
            font-weight: bold;
        }

        .green-text {
            color: #16a34a;
        }

        .red-text {
            color: #dc2626;
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
                N° {{ $bill->reference }}<br>
                Date: {{ $bill->date->format('d/m/Y') }}<br>
                Boutique : {{ $bill->shop->name }}<br>
                Vendeur : {{ $bill->seller->name }}<br>
                Statut : {{ $bill->status }}
            </p>
        </div>
    </div>

    <!-- Section spéciale pour les factures de troc -->
    @if($bill->is_barter_bill && $bill->barter)
        <div class="barter-info">
            <h3>Facture liée à un Troc</h3>
            <p>
                Cette facture a été générée automatiquement suite au troc <strong>{{ $bill->barter->reference }}</strong> du {{ $bill->barter->created_at->format('d/m/Y') }}.
                Elle représente le paiement complémentaire résultant de la différence de valeur entre les articles échangés.
            </p>

            <table class="barter-table">
                <tr>
                    <td style="width: 50%;"><strong>Valeur donnée par le client:</strong></td>
                    <td style="text-align: right;">{{ number_format($bill->barter->value_given, 0, ',', ' ') }} FCFA</td>
                </tr>
                <tr>
                    <td><strong>Valeur reçue par le client:</strong></td>
                    <td style="text-align: right;">{{ number_format($bill->barter->value_received, 0, ',', ' ') }} FCFA</td>
                </tr>
                <tr>
                    <td><strong>Différence:</strong></td>
                    <td style="text-align: right; {{ $bill->barter->additional_payment > 0 ? 'color: #16a34a;' : 'color: #dc2626;' }}">
                        {{ number_format(abs($bill->barter->additional_payment), 0, ',', ' ') }} FCFA
                        ({{ $bill->barter->additional_payment > 0 ? 'Client vers boutique' : 'Boutique vers client' }})
                    </td>
                </tr>
            </table>
        </div>
    @endif

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
    @if($bill->is_barter_bill)
        <table>
            <thead>
            <tr>
                <th>Description</th>
                <th style="text-align: right">Montant</th>
                <th style="text-align: right">Type</th>
            </tr>
            </thead>
            <tbody>
            @foreach($bill->items as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td style="text-align: right">{{ number_format($item->total, 0, ',', ' ') }} FCFA</td>
                    <td style="text-align: right">
                        @if(isset($item->is_barter_item) && $item->is_barter_item)
                            Produit échangé
                        @else
                            Paiement complémentaire
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
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
                    <td>{{ $item->product ? $item->product->name : ($item->name ?? 'Paiement complémentaire') }}</td>
                    <td style="text-align: right">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                    <td style="text-align: right">{{ $item->quantity }}</td>
                    <td style="text-align: right">{{ number_format($item->total, 0, ',', ' ') }} FCFA</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

    <!-- Totaux -->
    <div class="amounts clearfix">
        @if($bill->is_barter_bill)
            <table>
                <tr class="total">
                    <td>{{ $bill->barter && $bill->barter->additional_payment > 0 ? 'Paiement complémentaire:' : 'Remboursement:' }}</td>
                    <td>{{ number_format($bill->total, 0, ',', ' ') }} FCFA</td>
                </tr>
            </table>
        @else
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
        @endif
    </div>

    <!-- QR Code -->
    <div style="position: absolute; bottom: 20px; right: 20px; text-align: center;">
        @php
            try {
                $qrCodeData = App::make(\App\Http\Controllers\BillController::class)->generateQrCode($bill);
            } catch (\Exception $e) {
                $qrCodeData = null;
            }
        @endphp

        @if($qrCodeData)
            <img src="data:image/png;base64,{{ $qrCodeData }}" style="width: 100px; height: 100px;">
            <p style="font-size: 8px; margin-top: 5px;">Scannez pour vérifier l'authenticité</p>
        @else
            <div style="width: 100px; height: 100px; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center;">
                <span style="font-size: 8px; color: #999;">QR Code non disponible</span>
            </div>
        @endif
    </div>

    <!-- Pied de page -->
    <div class="footer-container">
        <div class="footer">
            <p>
                {{ $settings->company_name ?? 'Entreprise' }} 
                {{ isset($settings->siret) && $settings->siret ? '- SIRET: ' . $settings->siret : '' }}
                {{ isset($settings->tax_number) && $settings->tax_number ? '- N° Contribuable: ' . $settings->tax_number : '' }}
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
