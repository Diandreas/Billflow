<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use ZipArchive;
use Carbon\Carbon;

class SystemExportImportController extends Controller
{
    /**
     * Affiche la page d'accueil de l'exportation/importation
     */
    public function index()
    {
        // Vérifier que l'utilisateur est admin
        if (!Gate::allows('admin')) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }

        // Récupérer les sauvegardes existantes
        $backups = $this->getBackupFiles();

        // Obtenir le type de base de données pour affichage
        $dbConnection = config('database.default');
        $dbType = config("database.connections.{$dbConnection}.driver");

        return view('admin.system-export-import', compact('backups', 'dbType'));
    }

    /**
     * Exporte toutes les tables de la base de données au format JSON
     */
    public function exportSystem(Request $request)
    {
        // Vérifier que l'utilisateur est admin
        if (!Gate::allows('admin')) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }

        try {
            // Identifier le type de base de données
            $dbConnection = config('database.default');
            $dbType = config("database.connections.{$dbConnection}.driver");
            
            // Obtenir toutes les tables - méthode compatible avec tous les types de base de données
            $tables = [];
            if ($dbType === 'sqlite') {
                // Pour SQLite, utiliser une requête directe pour récupérer les tables
                $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                $tables = array_map(function($table) {
                    return $table->name;
                }, $tables);
            } else {
                // Pour MySQL et autres, utiliser la méthode standard
                try {
                    $tables = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();
                } catch (\Exception $e) {
                    // Méthode alternative si getDoctrineSchemaManager n'est pas disponible
                    $tables = Schema::getAllTables();
                    
                    // Convertir l'objet de table en tableau de noms
                    if (!empty($tables) && is_array($tables) && isset($tables[0]) && is_object($tables[0])) {
                        $tableKey = 'Tables_in_' . config('database.connections.' . $dbConnection . '.database');
                        $tables = array_map(function($table) use ($tableKey) {
                            return $table->$tableKey ?? (isset($table->name) ? $table->name : null);
                        }, $tables);
                        $tables = array_filter($tables);
                    }
                }
            }
            
            // Exclure certaines tables si nécessaire
            $excludeTables = ['migrations', 'password_reset_tokens', 'failed_jobs', 'personal_access_tokens', 'sessions', 'cache', 'job_batches', 'jobs'];
            $tables = array_diff($tables, $excludeTables);
            
            // Créer un répertoire temporaire
            $timestamp = Carbon::now()->format('Y-m-d_His');
            $exportDir = storage_path('app/backups/export_' . $timestamp);
            
            if (!File::exists($exportDir)) {
                File::makeDirectory($exportDir, 0755, true);
            }
            
            // Structure pour stocker les données d'export
            $exportData = [
                'metadata' => [
                    'version' => config('app.version', '1.0'),
                    'date' => Carbon::now()->toIso8601String(),
                    'database_type' => $dbType,
                    'tables' => []
                ],
                'tables' => []
            ];
            
            // Exporter chaque table
            foreach ($tables as $table) {
                Log::info("Exportation de la table: {$table}");
                
                // Récupérer les données
                $data = DB::table($table)->get()->toArray();
                
                // Obtenir la structure de la table
                $columns = Schema::getColumnListing($table);
                $structure = [];
                
                foreach ($columns as $column) {
                    $type = DB::getSchemaBuilder()->getColumnType($table, $column);
                    $structure[$column] = $type;
                }
                
                // Ajouter à l'export
                $exportData['metadata']['tables'][$table] = [
                    'count' => count($data),
                    'structure' => $structure
                ];
                
                $exportData['tables'][$table] = $data;
                
                // Écrire dans un fichier JSON séparé pour chaque table
                $tableData = json_encode([
                    'structure' => $structure,
                    'data' => $data
                ], JSON_PRETTY_PRINT);
                
                file_put_contents("{$exportDir}/{$table}.json", $tableData);
            }
            
            // Écrire le fichier de métadonnées
            file_put_contents("{$exportDir}/metadata.json", json_encode($exportData['metadata'], JSON_PRETTY_PRINT));
            
            // Créer le fichier ZIP
            $zipFilename = "system_backup_{$dbType}_{$timestamp}.zip";
            $zipPath = storage_path('app/backups/' . $zipFilename);
            
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
                $files = File::files($exportDir);
                
                foreach ($files as $file) {
                    $zip->addFile($file, basename($file));
                }
                
                $zip->close();
                
                // Supprimer les fichiers temporaires
                File::deleteDirectory($exportDir);
                
                // Télécharger le ZIP
                return response()->download($zipPath)->deleteFileAfterSend(false);
            } else {
                throw new \Exception("Impossible de créer le fichier ZIP.");
            }
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'exportation du système: " . $e->getMessage());
            return redirect()->route('system.export-import')
                ->with('error', 'Erreur lors de l\'exportation: ' . $e->getMessage());
        }
    }
    
    /**
     * Importe les données du système à partir d'un fichier ZIP
     */
    public function importSystem(Request $request)
    {
        // Vérifier que l'utilisateur est admin
        if (!Gate::allows('admin')) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }
        
        $request->validate([
            'backup_file' => 'required|file|mimes:zip'
        ]);
        
        try {
            // Créer un répertoire temporaire pour extraire les fichiers
            $tempDir = storage_path('app/temp/import_' . time());
            if (!File::exists($tempDir)) {
                File::makeDirectory($tempDir, 0755, true);
            }
            
            // Obtenir le fichier ZIP
            $zipFile = $request->file('backup_file');
            $zipPath = $zipFile->getRealPath();
            
            // Extraire le ZIP
            $zip = new ZipArchive();
            if ($zip->open($zipPath) === TRUE) {
                $zip->extractTo($tempDir);
                $zip->close();
                
                // Vérifier le fichier de métadonnées
                if (!File::exists($tempDir . '/metadata.json')) {
                    throw new \Exception("Fichier de sauvegarde invalide: métadonnées manquantes.");
                }
                
                $metadata = json_decode(file_get_contents($tempDir . '/metadata.json'), true);
                
                // Vérifier la compatibilité des bases de données
                $currentDbType = config('database.connections.' . config('database.default') . '.driver');
                $backupDbType = $metadata['database_type'] ?? 'unknown';
                
                $metadata['current_db_type'] = $currentDbType;
                $metadata['backup_db_type'] = $backupDbType;
                
                // Confirmer avec l'utilisateur ou procéder directement
                if ($request->has('confirm') && $request->input('confirm') === 'true') {
                    return $this->processImport($tempDir, $metadata);
                } else {
                    // Stocker le chemin du répertoire temporaire en session
                    session(['import_temp_dir' => $tempDir]);
                    
                    return view('admin.system-import-confirm', [
                        'metadata' => $metadata,
                        'tables' => array_keys($metadata['tables']),
                        'databaseMismatch' => ($currentDbType !== $backupDbType && $backupDbType !== 'unknown')
                    ]);
                }
            } else {
                throw new \Exception("Impossible d'ouvrir le fichier ZIP.");
            }
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'importation du système: " . $e->getMessage());
            
            // Nettoyer le répertoire temporaire
            if (isset($tempDir) && File::exists($tempDir)) {
                File::deleteDirectory($tempDir);
            }
            
            return redirect()->route('system.export-import')
                ->with('error', 'Erreur lors de l\'importation: ' . $e->getMessage());
        }
    }
    
    /**
     * Traite l'importation après confirmation
     */
    public function confirmImport(Request $request)
    {
        // Vérifier que l'utilisateur est admin
        if (!Gate::allows('admin')) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }
        
        try {
            $tempDir = session('import_temp_dir');
            
            if (!$tempDir || !File::exists($tempDir)) {
                throw new \Exception("Session d'importation expirée ou invalide.");
            }
            
            $metadata = json_decode(file_get_contents($tempDir . '/metadata.json'), true);
            
            return $this->processImport($tempDir, $metadata);
        } catch (\Exception $e) {
            Log::error("Erreur lors de la confirmation d'importation: " . $e->getMessage());
            return redirect()->route('system.export-import')
                ->with('error', 'Erreur lors de l\'importation: ' . $e->getMessage());
        }
    }
    
    /**
     * Effectue l'importation en base de données
     */
    private function processImport($tempDir, $metadata)
    {
        try {
            // Identifier le type de base de données
            $dbConnection = config('database.default');
            $dbType = config("database.connections.{$dbConnection}.driver");
            
            // Désactiver les contraintes de clés étrangères selon le type de DB
            $this->disableForeignKeyConstraints($dbType);
            
            // Importer chaque table
            foreach ($metadata['tables'] as $table => $info) {
                // Vérifier si le fichier existe
                $tableFile = $tempDir . '/' . $table . '.json';
                if (!File::exists($tableFile)) {
                    Log::warning("Fichier manquant pour la table {$table}");
                    continue;
                }
                
                // Charger les données
                $tableData = json_decode(file_get_contents($tableFile), true);
                
                // Vider la table existante en utilisant la méthode appropriée pour le moteur DB
                $this->emptyTable($table, $dbType);
                
                // Insérer les données par lots pour éviter les problèmes de mémoire
                if (!empty($tableData['data'])) {
                    $chunks = array_chunk($tableData['data'], 100);
                    foreach ($chunks as $chunk) {
                        DB::table($table)->insert($chunk);
                    }
                }
                
                Log::info("Table {$table} importée avec succès (" . count($tableData['data']) . " enregistrements)");
            }
            
            // Réactiver les contraintes de clés étrangères
            $this->enableForeignKeyConstraints($dbType);
            
            // Nettoyer le répertoire temporaire
            File::deleteDirectory($tempDir);
            
            // Effacer la session
            session()->forget('import_temp_dir');
            
            return redirect()->route('system.export-import')
                ->with('success', 'Importation du système terminée avec succès.');
        } catch (\Exception $e) {
            // Réactiver les contraintes de clés étrangères en cas d'erreur
            $this->enableForeignKeyConstraints($dbType ?? 'mysql');
            
            Log::error("Erreur lors du traitement de l'importation: " . $e->getMessage());
            return redirect()->route('system.export-import')
                ->with('error', 'Erreur lors de l\'importation: ' . $e->getMessage());
        }
    }
    
    /**
     * Désactive les contraintes de clés étrangères selon le moteur de DB
     */
    private function disableForeignKeyConstraints($dbType)
    {
        if ($dbType === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        } elseif ($dbType === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
        } else {
            // Pour les autres moteurs de DB, utiliser la méthode générique
            Schema::disableForeignKeyConstraints();
        }
    }
    
    /**
     * Réactive les contraintes de clés étrangères selon le moteur de DB
     */
    private function enableForeignKeyConstraints($dbType)
    {
        if ($dbType === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } elseif ($dbType === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            // Pour les autres moteurs de DB, utiliser la méthode générique
            Schema::enableForeignKeyConstraints();
        }
    }
    
    /**
     * Vide une table selon le moteur de base de données
     */
    private function emptyTable($table, $dbType)
    {
        try {
            if ($dbType === 'sqlite') {
                // Pour SQLite, utiliser DELETE FROM qui est plus sûr que TRUNCATE
                DB::table($table)->delete();
            } else {
                // Pour MySQL et autres, truncate est plus rapide
                DB::table($table)->truncate();
            }
        } catch (\Exception $e) {
            Log::warning("Erreur lors du vidage de la table {$table}: " . $e->getMessage());
            // Fallback sur DELETE FROM si TRUNCATE échoue
            DB::table($table)->delete();
        }
    }
    
    /**
     * Récupère la liste des fichiers de sauvegarde
     */
    private function getBackupFiles()
    {
        $backupDir = storage_path('app/backups');
        
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
            return [];
        }
        
        $files = File::files($backupDir);
        $backups = [];
        
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
                $backups[] = [
                    'name' => basename($file),
                    'size' => File::size($file),
                    'date' => Carbon::createFromTimestamp(File::lastModified($file)),
                    'path' => $file,
                    'db_type' => $this->extractDbTypeFromFilename(basename($file))
                ];
            }
        }
        
        // Trier par date, du plus récent au plus ancien
        usort($backups, function($a, $b) {
            return $b['date']->timestamp - $a['date']->timestamp;
        });
        
        return $backups;
    }
    
    /**
     * Extrait le type de base de données du nom de fichier
     */
    private function extractDbTypeFromFilename($filename)
    {
        if (preg_match('/system_backup_(mysql|sqlite|pgsql)_/', $filename, $matches)) {
            return $matches[1];
        }
        return 'unknown';
    }
    
    /**
     * Télécharger une sauvegarde existante
     */
    public function downloadBackup($filename)
    {
        // Vérifier que l'utilisateur est admin
        if (!Gate::allows('admin')) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }
        
        $backupPath = storage_path('app/backups/' . $filename);
        
        if (!File::exists($backupPath)) {
            return redirect()->route('system.export-import')
                ->with('error', 'Le fichier de sauvegarde n\'existe pas.');
        }
        
        return response()->download($backupPath);
    }
    
    /**
     * Supprimer une sauvegarde existante
     */
    public function deleteBackup($filename)
    {
        // Vérifier que l'utilisateur est admin
        if (!Gate::allows('admin')) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }
        
        $backupPath = storage_path('app/backups/' . $filename);
        
        if (File::exists($backupPath)) {
            File::delete($backupPath);
            return redirect()->route('system.export-import')
                ->with('success', 'Sauvegarde supprimée avec succès.');
        }
        
        return redirect()->route('system.export-import')
            ->with('error', 'Le fichier de sauvegarde n\'existe pas.');
    }
} 