<?php

namespace App\Services;

use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Enregistre une action de création
     */
    public static function logCreated($model, $description = null)
    {
        return self::log('create', $model, null, $model->toArray(), $description);
    }
    
    /**
     * Enregistre une action de mise à jour
     */
    public static function logUpdated($model, $oldValues = null, $description = null)
    {
        if ($oldValues === null && method_exists($model, 'getOriginal')) {
            $oldValues = $model->getOriginal();
        }
        
        return self::log('update', $model, $oldValues, $model->toArray(), $description);
    }
    
    /**
     * Enregistre une action de suppression
     */
    public static function logDeleted($model, $description = null)
    {
        return self::log('delete', $model, $model->toArray(), null, $description);
    }
    
    /**
     * Enregistre une action de connexion
     */
    public static function logLogin($user, $description = null)
    {
        if ($description === null) {
            $description = "L'utilisateur {$user->name} s'est connecté";
        }
        
        return self::log('login', $user, null, null, $description);
    }
    
    /**
     * Enregistre une action de déconnexion
     */
    public static function logLogout($user, $description = null)
    {
        if ($description === null) {
            $description = "L'utilisateur {$user->name} s'est déconnecté";
        }
        
        return self::log('logout', $user, null, null, $description);
    }
    
    /**
     * Enregistre une action personnalisée
     */
    public static function logCustom($action, $modelType = null, $modelId = null, $description = null)
    {
        return Activity::log($action, $modelType, $modelId, null, null, $description);
    }
    
    /**
     * Méthode générale pour enregistrer une activité
     */
    protected static function log($action, $model, $oldValues = null, $newValues = null, $description = null)
    {
        $modelType = null;
        $modelId = null;
        
        if ($model) {
            $modelType = class_basename($model);
            $modelId = $model->id;
        }
        
        return Activity::log($action, $modelType, $modelId, $oldValues, $newValues, $description);
    }
} 