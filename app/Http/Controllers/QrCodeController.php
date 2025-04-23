<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Bill;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class QrCodeController extends Controller
{
    /**
     * Affiche la page de vérification de QR code
     */
    public function showVerifier()
    {
        return view('qrcodes.verify');
    }

    /**
     * Affiche la page de génération de QR codes
     */
    public function showGenerator()
    {
        return view('qrcodes.generator');
    }

    /**
     * Génère un QR code dynamiquement en fonction des paramètres
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'data' => 'required|string',
            'size' => 'nullable|integer|min:50|max:500',
            'style' => 'nullable|in:square,dot,round',
            'color' => 'nullable|string',
            'backgroundColor' => 'nullable|string',
            'format' => 'nullable|in:png,svg,eps',
            'margin' => 'nullable|integer|min:0|max:10',
            'withLogo' => 'nullable|boolean',
        ]);

        try {
            $size = $validated['size'] ?? 200;
            $style = $validated['style'] ?? 'square';
            $format = $validated['format'] ?? 'png';
            $margin = $validated['margin'] ?? 1;
            
            // Initialiser le générateur QR
            $qrCode = QrCode::format($format)
                ->size($size)
                ->margin($margin)
                ->encoding('UTF-8')
                ->style($style);
            
            // Appliquer les couleurs si elles sont spécifiées
            if (isset($validated['color'])) {
                list($r, $g, $b) = sscanf($validated['color'], "#%02x%02x%02x");
                $qrCode->color($r ?? 0, $g ?? 0, $b ?? 0);
            }
            
            if (isset($validated['backgroundColor'])) {
                list($r, $g, $b) = sscanf($validated['backgroundColor'], "#%02x%02x%02x");
                $qrCode->backgroundColor($r ?? 255, $g ?? 255, $b ?? 255);
            }
            
            // Ajouter un logo si demandé
            if (isset($validated['withLogo']) && $validated['withLogo']) {
                $settings = Setting::first();
                if ($settings && $settings->logo_path) {
                    $logoPath = storage_path('app/public/' . $settings->logo_path);
                    if (file_exists($logoPath)) {
                        $qrCode->merge($logoPath, 0.3, true);
                    }
                }
            }
            
            // Générer le QR code
            $result = $qrCode->generate($validated['data']);
            
            $response = response($result)->header('Content-Type', 'image/' . ($format == 'png' ? 'png' : 'svg+xml'));
            
            if ($request->has('download')) {
                $response->header('Content-Disposition', 'attachment; filename="qrcode.' . $format . '"');
            }
            
            return $response;
            
        } catch (\Exception $e) {
            Log::error('Erreur de génération de QR code: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur de génération du QR code'], 500);
        }
    }

    /**
     * Vérifie l'authenticité d'une facture par QR code
     */
    public function verifyBill(Request $request)
    {
        $validated = $request->validate([
            'data' => 'required|json',
        ]);
        
        try {
            $data = json_decode($request->data, true);
            
            if (!isset($data['reference'])) {
                return response()->json([
                    'valid' => false,
                    'message' => 'QR code invalide ou données manquantes'
                ]);
            }
            
            $bill = Bill::where('reference', $data['reference'])->first();
            
            if (!$bill) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Facture non trouvée'
                ]);
            }
            
            // S'assurer que les relations sont chargées
            $bill->load(['client', 'shop', 'seller']);
            
            // Vérifier si les données du QR code correspondent aux données de la facture
            $isValid = 
                $data['date'] == $bill->date->format('Y-m-d H:i:s') &&
                $data['total'] == $bill->total &&
                $data['client'] == $bill->client->name &&
                $data['shop'] == $bill->shop->name &&
                $data['seller'] == $bill->seller->name;
            
            return response()->json([
                'valid' => $isValid,
                'message' => $isValid ? 'Facture authentique' : 'Données de facture non concordantes',
                'bill' => $isValid ? [
                    'reference' => $bill->reference,
                    'date' => $bill->date->format('d/m/Y H:i'),
                    'total' => number_format($bill->total, 2) . ' XAF',
                    'client' => $bill->client->name,
                    'shop' => $bill->shop->name,
                    'seller' => $bill->seller->name,
                    'status' => $bill->status,
                ] : null
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur de vérification de QR code: ' . $e->getMessage());
            return response()->json([
                'valid' => false,
                'message' => 'Erreur lors de la vérification'
            ], 500);
        }
    }

    /**
     * Génère un QR code pour une URL personnalisée liée à l'application
     */
    public function generateUrl($type, $id)
    {
        $url = '';
        $size = 200;
        
        switch ($type) {
            case 'bill':
                $bill = Bill::find($id);
                if (!$bill) {
                    abort(404);
                }
                $url = route('bills.show', $bill);
                break;
                
            case 'verify':
                $bill = Bill::find($id);
                if (!$bill) {
                    abort(404);
                }
                $url = route('qrcodes.verify');
                break;
                
            default:
                abort(404);
        }
        
        $settings = Setting::first();
        $qrCode = QrCode::format('png')
            ->size($size)
            ->margin(1);
            
        // Utiliser les couleurs de l'entreprise si définies
        if (isset($settings) && isset($settings->primary_color)) {
            list($r, $g, $b) = sscanf($settings->primary_color, "#%02x%02x%02x");
            $qrCode->color($r ?? 0, $g ?? 0, $b ?? 0);
            
            if (isset($settings->secondary_color)) {
                list($r, $g, $b) = sscanf($settings->secondary_color, "#%02x%02x%02x");
                $qrCode->backgroundColor($r ?? 255, $g ?? 255, $b ?? 255);
            }
        }
        
        return response($qrCode->generate($url))
            ->header('Content-Type', 'image/png');
    }
} 