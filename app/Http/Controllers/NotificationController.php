<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Récupère les notifications de l'utilisateur authentifié.
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->paginate(10); // Récupère les 10 dernières notifications
        $unreadCount = $user->unreadNotifications->count();

        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => $unreadCount
        ]);
    }

    /**
     * Marque une notification spécifique comme lue.
     */
    public function markAsRead(Request $request, $notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true, 'unreadCount' => $user->unreadNotifications()->count()]);
        }

        return response()->json(['success' => false, 'message' => 'Notification non trouvée.'], 404);
    }

    /**
     * Marque toutes les notifications non lues comme lues.
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        return response()->json(['success' => true, 'unreadCount' => 0]);
    }

    /**
     * Récupère le nombre de notifications non lues.
     */
    public function unreadCount()
    {
        $user = Auth::user();
        return response()->json(['unreadCount' => $user->unreadNotifications->count()]);
    }
}
