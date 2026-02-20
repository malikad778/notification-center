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
     * Trigger a notification send from an external system.
     * 
     * @OA\Post(
     *      path="/notifications/send",
     *      tags={"Notifications"},
     *      summary="Send notification"
     * )
     */
    public function send(\App\Http\Requests\SendNotificationRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        $payload = new \malikad778\NotificationCenter\DTOs\NotificationPayload(
            title: $validated['title'],
            body: $validated['body'],
            data: $validated['data'] ?? [],
            actionUrl: $validated['action_url'] ?? null,
            imageUrl: $validated['image_url'] ?? null,
        );

        foreach ($validated['users'] as $userId) {
            $user = \App\Models\User::find($userId);
            if ($user) {
                \malikad778\NotificationCenter\Facades\NotificationCenter::send($user, $payload)
                    ->via($validated['channels'] ?? [])
                    ->dispatch();
            }
        }

        return response()->json(['message' => 'Notification dispatched']);
    }

    /**
     * Trigger a bulk notification send.
     * 
     * @OA\Post(
     *      path="/notifications/bulk",
     *      tags={"Notifications"},
     *      summary="Send bulk notification"
     * )
     */
    public function sendBulk(\App\Http\Requests\SendNotificationRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        $payload = new \malikad778\NotificationCenter\DTOs\NotificationPayload(
            title: $validated['title'],
            body: $validated['body'],
            data: $validated['data'] ?? [],
            actionUrl: $validated['action_url'] ?? null,
            imageUrl: $validated['image_url'] ?? null,
        );

        $users = \App\Models\User::whereIn('id', $validated['users'])->get()->all();

        \malikad778\NotificationCenter\Facades\NotificationCenter::sendBulk($users, $payload, $validated['channels'] ?? []);

        return response()->json(['message' => 'Bulk notification dispatched']);
    }
}
