@extends('layouts.app')

@section('title', 'Signature de facture')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Signature de facture</h1>
            
            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-2">Facture #{{ $bill->reference }}</h2>
                <div class="bg-gray-50 p-4 rounded border">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Client:</p>
                            <p class="font-medium">{{ $bill->client->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Date:</p>
                            <p class="font-medium">{{ $bill->date->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Montant:</p>
                            <p class="font-medium">{{ number_format($bill->total, 0) }} XAF</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Vendeur:</p>
                            <p class="font-medium">{{ $bill->seller->name }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-2">Signature du client</h2>
                <p class="text-sm text-gray-600 mb-4">
                    Veuillez signer dans le cadre ci-dessous pour confirmer l'exactitude de cette facture.
                </p>
                
                <div class="border rounded-lg p-2 bg-white">
                    <canvas id="signature-pad" class="w-full border rounded" height="200"></canvas>
                </div>
                
                <div class="flex mt-2 space-x-2">
                    <button id="clear-button" class="px-3 py-1 bg-gray-200 hover:bg-gray-300 text-sm rounded transition">
                        Effacer
                    </button>
                </div>
            </div>
            
            <div class="flex justify-between mt-8">
                <a href="{{ route('bills.show', $bill) }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded transition">
                    Annuler
                </a>
                <button id="save-button" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                    Enregistrer la signature
                </button>
            </div>
        </div>
    </div>
</div>

@if($bill->signature_path)
<div class="container mx-auto px-4 py-3 max-w-3xl">
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    Cette facture a déjà une signature. L'enregistrement d'une nouvelle signature remplacera l'ancienne.
                </p>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('signature-pad');
        const clearButton = document.getElementById('clear-button');
        const saveButton = document.getElementById('save-button');
        
        // Ajuster la largeur du canvas pour qu'elle corresponde à son conteneur
        canvas.width = canvas.parentElement.clientWidth - 20;
        
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)'
        });
        
        // Effacer la signature
        clearButton.addEventListener('click', function() {
            signaturePad.clear();
        });
        
        // Sauvegarder la signature
        saveButton.addEventListener('click', function() {
            if (signaturePad.isEmpty()) {
                alert('Veuillez signer avant de sauvegarder.');
                return;
            }
            
            // Désactiver le bouton pendant la sauvegarde
            saveButton.disabled = true;
            saveButton.textContent = 'Sauvegarde en cours...';
            saveButton.classList.add('opacity-70');
            
            // Récupérer l'image de la signature en base64
            const signatureData = signaturePad.toDataURL();
            
            // Envoyer la signature au serveur
            fetch('{{ route("bills.signature", $bill) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ signature: signatureData })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Rediriger vers la page de la facture
                    window.location.href = '{{ route("bills.show", $bill) }}';
                } else {
                    alert('Une erreur est survenue lors de la sauvegarde de la signature.');
                    saveButton.disabled = false;
                    saveButton.textContent = 'Enregistrer la signature';
                    saveButton.classList.remove('opacity-70');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors de la sauvegarde de la signature.');
                saveButton.disabled = false;
                saveButton.textContent = 'Enregistrer la signature';
                saveButton.classList.remove('opacity-70');
            });
        });
        
        // Ajuster la taille du canvas lors du redimensionnement
        window.addEventListener('resize', function() {
            const data = signaturePad.toData();
            canvas.width = canvas.parentElement.clientWidth - 20;
            signaturePad.fromData(data);
        });
    });
</script>
@endpush 