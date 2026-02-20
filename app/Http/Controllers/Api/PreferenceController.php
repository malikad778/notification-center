<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use malikad778\NotificationCenter\Models\NotificationPreference;

class PreferenceController extends Controller
{
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
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $prefs = NotificationPreference::where('user_id', $user->id)->get();
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
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'channel' => ['required', 'string'],
            'enabled' => ['boolean'],
            'frequency_limit' => ['integer', 'min:1'],
        ]);

        $user = Auth::user();
        
        $pref = NotificationPreference::updateOrCreate(
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
