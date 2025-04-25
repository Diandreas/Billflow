<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture {{ $bill->reference }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            color: #333;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .logo {
            max-width: 150px;
            max-height: 80px;
        }
        .company-info {
            text-align: right;
            font-size: 10px;
        }
        .bill-info {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .bill-info h1 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 18px;
        }
        .bill-info p {
            margin: 3px 0;
        }
        .customer-info {
            margin-bottom: 15px;
            float: left;
            width: 60%;
        }
        .qr-container {
            float: right;
            width: 35%;
            text-align: right;
        }
        .qr-code {
            width: 100px;
            height: 100px;
        }
        .qr-info {
            font-size: 8px;
            color: #666;
            margin-top: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            clear: both;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .totals {
            margin-top: 15px;
            text-align: right;
            margin-bottom: 30px;
        }
        .totals h3 {
            color: #e74c3c;
            margin: 5px 0;
            font-size: 14px;
        }
        .footer-container {
            clear: both;
            position: relative;
            margin-top: 50px;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            color: #777;
        }
        .signature-area {
            margin-top: 30px;
            margin-bottom: 40px;
            display: flex;
            justify-content: space-between;
            clear: both;
        }
        .signature-box {
            width: 45%;
            text-align: center;
            border: 1px dashed #ddd;
            padding: 10px;
            min-height: 70px;
        }
        .signature-title {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 10px;
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
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }
        .barter-info {
            margin: 15px 0;
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
            text-align: center;
        }
        .barter-info p {
            margin-bottom: 10px;
            font-size: 12px;
        }
        .barter-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 12px;
            background-color: white;
        }
        .barter-table th, .barter-table td {
            padding: 5px;
            border: 1px solid #e9d5ff;
        }
        .barter-table th {
            background-color: #f3e8ff;
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
@if($bill->reprint_count > 1)
    <div class="reprint-mark">DUPLICATA</div>
@endif

<div class="header">
    <div>
        @if(isset($logo))
            <img src="{{ $logo }}" alt="Logo" class="logo">
        @endif
    </div>
    <div class="company-info">
        <h2>{{ $company ?? 'Entreprise' }}</h2>
        <p>{{ $address ?? 'Adresse non spécifiée' }}</p>
        <p>Tél: {{ $phone ?? 'Non spécifié' }}</p>
    </div>
</div>

<div class="bill-info">
    <h1>FACTURE {{ $bill->reference }}</h1>
    <p><strong>Date:</strong> {{ $bill->date->format('d/m/Y H:i') }}</p>
    <p><strong>Échéance:</strong> {{ $bill->due_date ? $bill->due_date->format('d/m/Y') : 'Non spécifiée' }}</p>
    <p><strong>Boutique:</strong> {{ $bill->shop->name }}</p>
    <p><strong>Vendeur:</strong> {{ $bill->seller->name }}</p>
    <p><strong>Statut:</strong> {{ $bill->status }}</p>
</div>

<!-- Section spéciale pour les factures de troc -->
@if(isset($bill->is_barter_bill) && $bill->is_barter_bill && isset($bill->barter))
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

<div class="clearfix">
    <div class="customer-info">
        <h3>Client</h3>
        <p><strong>Nom:</strong> {{ $bill->client->name }}</p>
        <p><strong>Adresse:</strong> {{ isset($bill->client->address) ? $bill->client->address : 'Non spécifiée' }}</p>
        <p><strong>Tél:</strong> {{ isset($bill->client->phones) && $bill->client->phones->count() > 0 ? $bill->client->phones->first()->number : 'Non spécifié' }}</p>
        <p><strong>Email:</strong> {{ isset($bill->client->email) ? $bill->client->email : 'Non spécifié' }}</p>
    </div>

    <div class="qr-container">
        @if(isset($qrCode))
            <img src="data:image/png;base64,{{ $qrCode }}" class="qr-code">
            <div class="qr-info">
                Scanner pour vérifier l'authenticité
            </div>
        @else
            <div style="width: 100px; height: 100px; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center;">
                <span style="font-size: 9px; color: #999;">QR code non disponible</span>
            </div>
        @endif
    </div>
</div>

<!-- Tableau des produits adapté selon le type de facture -->
@if(isset($bill->is_barter_bill) && $bill->is_barter_bill)
    <table>
        <thead>
        <tr>
            <th>Description</th>
            <th>Type</th>
            <th>Montant</th>
        </tr>
        </thead>
        <tbody>
        @foreach($bill->items as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>
                    @if(isset($item->is_barter_item) && $item->is_barter_item)
                        Produit échangé
                    @else
                        Paiement complémentaire
                    @endif
                </td>
                <td>{{ number_format($item->total, 0) }} XAF</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <table>
        <thead>
        <tr>
            <th>Produit</th>
            <th>Description</th>
            <th>Quantité</th>
            <th>Prix unitaire</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($bill->items as $item)
            <tr>
                <td>{{ $item->product ? $item->product->name : ($item->name ?? 'Paiement complémentaire') }}</td>
                <td>{{ $item->product ? ($item->product->description ?? '') : ($item->description ?? '') }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->unit_price, 0) }} XAF</td>
                <td>{{ number_format($item->total, 0) }} XAF</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif

<!-- Totaux adaptés selon le type de facture -->
@if(isset($bill->is_barter_bill) && $bill->is_barter_bill)
    <div class="totals">
        <h3>Montant du paiement complémentaire: {{ number_format($bill->total, 0) }} XAF</h3>
        <p class="text-sm" style="color: #666;">
            {{ $bill->barter && $bill->barter->additional_payment > 0 ? 'À payer par le client' : 'À rembourser au client' }}
        </p>
    </div>
@else
    <div class="totals">
        <p>Sous-total: {{ number_format($bill->total - $bill->tax_amount, 0) }} XAF</p>
        <p>TVA ({{ $bill->tax_rate }}%): {{ number_format($bill->tax_amount, 0) }} XAF</p>
        <h3>Total: {{ number_format($bill->total, 0) }} XAF</h3>
    </div>
@endif

<div class="signature-area">
    <div class="signature-box">
        <p class="signature-title">Signature Client</p>
        @if(isset($bill->signature_path) && $bill->signature_path)
            <img src="{{ Storage::url($bill->signature_path) }}" height="60">
        @endif
    </div>
    <div class="signature-box">
        <p class="signature-title">Signature Vendeur</p>
        <p style="font-size: 9px">{{ $bill->seller->name }}</p>
    </div>
</div>

<div class="footer-container">
    <div class="footer">
        <p>Nous vous remercions pour votre confiance.</p>
        <p>Cette facture constitue une preuve d'achat et peut être exigée pour tout service après-vente.</p>
    </div>

    <div class="print-info">
        Imprimé le {{ now()->format('d/m/Y H:i') }}
        @if($bill->reprint_count > 1)
            (Réimpression #{{ $bill->reprint_count - 1 }})
        @endif
    </div>
</div>
</body>
</html>
