<?php

namespace App\Http\Middleware;

use App\Models\Activity;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class LogUserActivity
{
    /**
     * Traite une requête entrante et enregistre l'activité utilisateur.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ne pas enregistrer les requêtes pour les actifs (images, css, js, etc.)
        if ($this->shouldIgnore($request)) {
            return $next($request);
        }
        
        $user = Auth::user();
        
        // Si l'utilisateur est authentifié, enregistrez l'activité
        if ($user) {
            $routeName = $request->route() ? $request->route()->getName() : null;
            $method = $request->method();
            $path = $request->path();
            
            // Déterminer l'action en fonction de la méthode HTTP
            $action = match($method) {
                'GET' => 'view',
                'POST' => 'create',
                'PUT', 'PATCH' => 'update',
                'DELETE' => 'delete',
                default => 'access'
            };
            
            // Déterminer l'entité concernée à partir de la route
            $modelType = null;
            $modelId = null;
            
            if ($routeName) {
                // Extraire le type de modèle de la route (ex: clients.show -> Client)
                $parts = explode('.', $routeName);
                if (count($parts) >= 1) {
                    $modelType = ucfirst(rtrim($parts[0], 's')); // Convertir 'clients' en 'Client'
                }
                
                // Essayer de récupérer l'ID du modèle à partir des paramètres de route
                if (count($parts) >= 2 && in_array($parts[1], ['show', 'edit', 'update', 'destroy'])) {
                    foreach ($request->route()->parameters() as $param) {
                        if (is_object($param) && method_exists($param, 'getKey')) {
                            $modelId = $param->getKey();
                            break;
                        } elseif (is_numeric($param)) {
                            $modelId = $param;
                            break;
                        }
                    }
                }
            }
            
            // Traitement spécial pour les routes de création (store)
            if ($routeName && strpos($routeName, '.store') !== false) {
                // Pour les routes de store, nous savons que c'est une création mais nous n'avons pas encore l'ID
                $action = 'create';
                // Pour les factures, on peut récupérer le type d'entité
                if (strpos($routeName, 'bills.store') !== false) {
                    $modelType = 'Bill';
                }
            }
            
            // Créer une description basée sur la route et la méthode
            $description = "Utilisateur a {$action} ";
            if ($modelType) {
                $description .= $modelType;
                if ($modelId) {
                    $description .= " #{$modelId}";
                }
            } else {
                $description .= "page {$path}";
            }
            
            // Enregistrer l'activité
            Activity::create([
                'user_id' => $user->id,
                'action' => $action,
                'model_type' => $modelType,
                'model_id' => $modelId,
                'description' => $description,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'device' => $this->detectDevice($request->userAgent())
            ]);
        }
        
        return $next($request);
    }
    
    /**
     * Détermine si la requête doit être ignorée pour la journalisation
     */
    protected function shouldIgnore(Request $request): bool
    {
        // Ignorer les requêtes pour les actifs (assets)
        $path = $request->path();
        if (preg_match('/(\.js|\.css|\.ico|\.png|\.jpg|\.jpeg|\.gif|\.svg|\.woff|\.woff2|\.ttf|\.eot|\.map)$/i', $path)) {
            return true;
        }
        
        // Ignorer les requêtes AJAX sauf les requêtes POST importantes
        if ($request->ajax() && !$request->isMethod('POST')) {
            // Ne pas ignorer certaines requêtes AJAX POST importantes comme les mises à jour d'état
            $routeName = $request->route() ? $request->route()->getName() : null;
            $importantAjaxRoutes = [
                'bills.update-status', 
                'bills.signature', 
                'bills.approve'
            ];
            
            if ($request->isMethod('POST') && $routeName && in_array($routeName, $importantAjaxRoutes)) {
                return false;
            }
            
            return true;
        }
        
        // Ignorer les requêtes d'API
        if (str_starts_with($path, 'api/')) {
            return true;
        }
        
        // Ignorer les requêtes pour les routes spécifiques
        $ignoredRoutes = [
            'login', 'logout', 'register', 'password.request', 'password.reset',
            '_debugbar', '_ignition', 'activities', 'activities.show', 'activities.export'
        ];
        
        $routeName = $request->route() ? $request->route()->getName() : null;
        if ($routeName && in_array($routeName, $ignoredRoutes)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Détecte le type d'appareil à partir de l'user agent
     */
    protected function detectDevice($userAgent)
    {
        if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($userAgent, 0, 4))) {
            return 'mobile';
        }
        
        if (preg_match('/tablet|ipad|playbook|silk/i', $userAgent)) {
            return 'tablet';
        }
        
        return 'desktop';
    }
}
