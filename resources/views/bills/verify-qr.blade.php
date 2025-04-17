<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification de facture</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-blue-600 p-4">
                <h1 class="text-white text-xl font-bold text-center">Vérification de facture</h1>
            </div>
            
            <div class="p-4">
                <p class="text-gray-600 text-sm mb-4 text-center">
                    Scannez le QR code d'une facture pour vérifier son authenticité
                </p>
                
                <!-- Scanner QR Code -->
                <div id="reader" class="mb-4 border rounded"></div>
                
                <!-- Loader -->
                <div id="loader" class="hidden flex justify-center my-4">
                    <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                
                <!-- Résultat de la vérification -->
                <div id="result" class="hidden border rounded p-4 my-4">
                    <div id="result-valid" class="hidden">
                        <div class="flex items-center mb-2">
                            <svg class="h-6 w-6 text-green-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <h2 class="text-green-600 font-bold">Facture authentique</h2>
                        </div>
                    </div>
                    <div id="result-invalid" class="hidden">
                        <div class="flex items-center mb-2">
                            <svg class="h-6 w-6 text-red-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <h2 class="text-red-600 font-bold" id="error-message">Facture non valide</h2>
                        </div>
                    </div>
                    
                    <div id="bill-details" class="hidden mt-4">
                        <h3 class="font-bold text-gray-700 mb-2">Détails de la facture</h3>
                        <table class="w-full text-sm">
                            <tr>
                                <td class="font-semibold py-1">Référence:</td>
                                <td id="bill-reference"></td>
                            </tr>
                            <tr>
                                <td class="font-semibold py-1">Date:</td>
                                <td id="bill-date"></td>
                            </tr>
                            <tr>
                                <td class="font-semibold py-1">Client:</td>
                                <td id="bill-client"></td>
                            </tr>
                            <tr>
                                <td class="font-semibold py-1">Boutique:</td>
                                <td id="bill-shop"></td>
                            </tr>
                            <tr>
                                <td class="font-semibold py-1">Vendeur:</td>
                                <td id="bill-seller"></td>
                            </tr>
                            <tr>
                                <td class="font-semibold py-1">Montant:</td>
                                <td id="bill-total"></td>
                            </tr>
                            <tr>
                                <td class="font-semibold py-1">Statut:</td>
                                <td id="bill-status"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="flex justify-center mt-4">
                    <button id="scanAgain" class="hidden px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                        Scanner une autre facture
                    </button>
                </div>
            </div>
            
            <div class="bg-gray-100 p-4">
                <p class="text-xs text-gray-500 text-center">
                    © {{ date('Y') }} {{ config('app.name') ?? 'BillFlow' }} - Tous droits réservés
                </p>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reader = document.getElementById('reader');
            const result = document.getElementById('result');
            const resultValid = document.getElementById('result-valid');
            const resultInvalid = document.getElementById('result-invalid');
            const billDetails = document.getElementById('bill-details');
            const loader = document.getElementById('loader');
            const scanAgain = document.getElementById('scanAgain');
            const errorMessage = document.getElementById('error-message');
            
            // Configurer le scanner QR code
            let html5QrcodeScanner = new Html5QrcodeScanner(
                "reader", 
                { 
                    fps: 10, 
                    qrbox: 250,
                    rememberLastUsedCamera: true,
                }, 
                false
            );
            
            function onScanSuccess(decodedText, decodedResult) {
                // Arrêter le scanner
                html5QrcodeScanner.clear();
                
                // Afficher le loader
                loader.classList.remove('hidden');
                
                try {
                    // Essayer de parser les données JSON
                    const data = JSON.parse(decodedText);
                    
                    // Vérifier la facture sur le serveur
                    fetch('{{ route("bills.verify-qr") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ data: decodedText })
                    })
                    .then(response => response.json())
                    .then(response => {
                        // Cacher le loader
                        loader.classList.add('hidden');
                        
                        // Afficher les résultats
                        result.classList.remove('hidden');
                        scanAgain.classList.remove('hidden');
                        
                        if (response.valid) {
                            resultValid.classList.remove('hidden');
                            billDetails.classList.remove('hidden');
                            
                            // Remplir les détails de la facture
                            document.getElementById('bill-reference').textContent = response.bill.reference;
                            document.getElementById('bill-date').textContent = response.bill.date;
                            document.getElementById('bill-client').textContent = response.bill.client;
                            document.getElementById('bill-shop').textContent = response.bill.shop;
                            document.getElementById('bill-seller').textContent = response.bill.seller;
                            document.getElementById('bill-total').textContent = response.bill.total;
                            document.getElementById('bill-status').textContent = response.bill.status;
                        } else {
                            resultInvalid.classList.remove('hidden');
                            errorMessage.textContent = response.message;
                        }
                    })
                    .catch(error => {
                        // En cas d'erreur lors de la requête
                        loader.classList.add('hidden');
                        result.classList.remove('hidden');
                        resultInvalid.classList.remove('hidden');
                        scanAgain.classList.remove('hidden');
                        errorMessage.textContent = "Erreur lors de la vérification. Veuillez réessayer.";
                    });
                } catch (e) {
                    // Le QR code n'est pas au format JSON valide
                    loader.classList.add('hidden');
                    result.classList.remove('hidden');
                    resultInvalid.classList.remove('hidden');
                    scanAgain.classList.remove('hidden');
                    errorMessage.textContent = "QR code invalide. Ce n'est pas une facture.";
                }
            }
            
            function onScanFailure(error) {
                // Nous ignorons les erreurs de scan ici
            }
            
            // Démarrer le scanner
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
            
            // Bouton pour scanner à nouveau
            scanAgain.addEventListener('click', function() {
                result.classList.add('hidden');
                resultValid.classList.add('hidden');
                resultInvalid.classList.add('hidden');
                billDetails.classList.add('hidden');
                scanAgain.classList.add('hidden');
                
                // Redémarrer le scanner
                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
            });
        });
    </script>
</body>
</html> 