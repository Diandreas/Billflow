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
    
    <div class="clearfix">
        <div class="customer-info">
            <h3>Client</h3>
            <p><strong>Nom:</strong> {{ $bill->client->name }}</p>
            <p><strong>Adresse:</strong> {{ isset($bill->client->address) ? $bill->client->address : 'Non spécifiée' }}</p>
            <p><strong>Tél:</strong> {{ isset($bill->client->phones) && $bill->client->phones->count() > 0 ? $bill->client->phones->first()->number : 'Non spécifié' }}</p>
            <p><strong>Email:</strong> {{ isset($bill->client->email) ? $bill->client->email : 'Non spécifié' }}</p>
        </div>
        
        <div class="qr-container">
            <img src="data:image/png;base64,{{ $qrCode }}" class="qr-code">
            <div class="qr-info">
                Scanner pour vérifier l'authenticité
            </div>
        </div>
    </div>
    
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
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->product->description ?? '' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->unit_price, 0) }} XAF</td>
                <td>{{ number_format($item->total, 0) }} XAF</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="totals">
        <p>Sous-total: {{ number_format($bill->total - $bill->tax_amount, 0) }} XAF</p>
        <p>TVA ({{ $bill->tax_rate }}%): {{ number_format($bill->tax_amount, 0) }} XAF</p>
        <h3>Total: {{ number_format($bill->total, 0) }} XAF</h3>
    </div>
    
    <div class="signature-area">
        <div class="signature-box">
            <p class="signature-title">Signature Client</p>
            @if($bill->signature_path)
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