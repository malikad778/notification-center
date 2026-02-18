<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use malikad778\NotificationCenter\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Notification Center API",
 *      description="API for managing notifications and preferences"
 * )
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="API Server"
 * )
 * @OA\Tag(
 *     name="Notifications",
 *     description="API Endpoints of Notifications"
 * )
 * @OA\Tag(
 *     name="Preferences",
 *     description="API Endpoints of Preferences"
 * )
 */
class NotificationController extends Controller
{
    /**
     * Get list of notifications for the authenticated user.
     * 
     * @OA\Get(
     *      path="/notifications",
     *      tags={"Notifications"},
     *      summary="List notifications",
     *      @OA\Parameter(
     *          name="unread",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="boolean")
     *      ),
     *      @OA\Parameter(
     *          name="priority",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="string", enum={"urgent", "high", "normal", "low"})
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $query = Notification::where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->orderByDesc('created_at');

        if ($request->has('unread') || $request->has('priority')) {
            $query->where(function ($q) use ($request) {
                 if ($request->has('unread')) {
                     $q->whereNull('read_at');
                 }
                 
                 if ($request->has('priority')) {
                     $q->where('data->priority', $request->query('priority'));
                 }
            });
        }

        $notifications = $query->paginate(20);

        return response()->json($notifications);
    }

    public function markAsRead(string $id): JsonResponse
    {
        $user = Auth::user();
        
        $notification = Notification::where('id', $id)
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->firstOrFail();

        $notification->update(['read_at' => now()]);

        return response()->json(['message' => 'Marked as read']);
    }

    public function markAllRead(): JsonResponse
    {
        $user = Auth::user();

        Notification::where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'All marked as read']);
    }

    /**
     * Get user notification preferences.
     * 
     * @OA\Get(
     *      path="/notifications/preferences",
     *      tags={"Preferences"},
     *      summary="Get user preferences",
     *      @OA\Response(
     *          response=200,
     *          description="User preferences"
     *      )
     * )
     */
    public function getPreferences(): JsonResponse
    {
        $user = Auth::user();
        // Assuming relationship exists or we query manually
        $prefs = \malikad778\NotificationCenter\Models\NotificationPreference::where('user_id', $user->id)->get();
        return response()->json($prefs);
    }

    /**
     * Update user notification preferences.
     * 
     * @OA\Put(
     *      path="/notifications/preferences",
     *      tags={"Preferences"},
     *      summary="Update user preferences",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="channel", type="string", example="email"),
     *              @OA\Property(property="enabled", type="boolean", example=true),
     *              @OA\Property(property="frequency_limit", type="integer", example=10)
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Preference updated"
     *      )
     * )
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        $request->validate([
            'channel' => ['required', 'string'],
            'enabled' => ['boolean'],
            'frequency_limit' => ['integer', 'min:1'],
        ]);

        $user = Auth::user();
        
        $pref = \malikad778\NotificationCenter\Models\NotificationPreference::updateOrCreate(
            [
                'user_id' => $user->id,
                'channel' => $request->input('channel'),
            ],
            [
                'enabled' => $request->input('enabled', true),
                'frequency_limit' => $request->input('frequency_limit'),
            ]
        );

        return response()->json($pref);
    }
}
