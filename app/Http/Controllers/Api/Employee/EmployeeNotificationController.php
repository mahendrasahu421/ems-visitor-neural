<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use App\Models\FcmDeviceToken;
use App\Models\PushNotification;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeNotificationController extends Controller
{
    protected FirebaseNotificationService $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Register or update Employee's FCM device token.
     * POST /api/v1/employee/fcm-token
     */
    public function storeFcmToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required|string',
            'device_type' => 'nullable|string|in:android,ios,web',
            'device_name' => 'nullable|string|max:191',
            'app_version' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $employee = $request->user();
        $deviceType = $request->input('device_type', 'android');
        $deviceName = $request->input('device_name', $request->header('User-Agent'));
        $appVersion = $request->input('app_version');

        $device = FcmDeviceToken::storeToken(
            $request->input('fcm_token'),
            $employee->employee_id,
            null,
            $deviceType,
            $deviceName,
            $appVersion
        );

        return response()->json([
            'success' => true,
            'message' => 'FCM Device token registered successfully.',
            'data' => [
                'device_id' => $device->id,
                'device_type' => $device->device_type,
                'device_name' => $device->device_name,
                'is_active' => $device->is_active,
                'registered_at' => $device->updated_at->toIso8601String(),
            ],
        ], 200);
    }

    /**
     * Remove / deactivate FCM device token on logout.
     * POST /api/v1/employee/fcm-token/delete
     */
    public function deleteFcmToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        FcmDeviceToken::removeToken($request->input('fcm_token'));

        return response()->json([
            'success' => true,
            'message' => 'Device token unregistered successfully.',
        ], 200);
    }

    /**
     * Get Employee's Notification History / Inbox.
     * GET /api/v1/employee/notifications
     */
    public function index(Request $request)
    {
        $employee = $request->user();
        $perPage = min(max((int) $request->input('per_page', 20), 5), 100);

        $unreadCount = PushNotification::where('employee_id', $employee->employee_id)
            ->where('is_read', false)
            ->count();

        $notifications = PushNotification::where('employee_id', $employee->employee_id)
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Notifications retrieved successfully.',
            'unread_count' => $unreadCount,
            'data' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'total' => $notifications->total(),
                'notifications' => collect($notifications->items())->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'body' => $item->body,
                        'image_url' => $item->image_url,
                        'action_url' => $item->action_url,
                        'data' => $item->data_payload,
                        'sender_type' => $item->sender_type,
                        'is_read' => (bool) $item->is_read,
                        'read_at' => $item->read_at ? $item->read_at->toIso8601String() : null,
                        'created_at' => $item->created_at ? $item->created_at->toIso8601String() : null,
                        'time_ago' => $item->created_at ? $item->created_at->diffForHumans() : '',
                    ];
                }),
            ],
        ], 200);
    }

    /**
     * Mark single notification as read.
     * POST /api/v1/employee/notifications/{id}/read
     */
    public function markAsRead(Request $request, $id)
    {
        $employee = $request->user();

        $notification = PushNotification::where('id', $id)
            ->where('employee_id', $employee->employee_id)
            ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found.',
            ], 404);
        }

        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.',
        ], 200);
    }

    /**
     * Mark all notifications as read.
     * POST /api/v1/employee/notifications/mark-all-read
     */
    public function markAllAsRead(Request $request)
    {
        $employee = $request->user();

        PushNotification::where('employee_id', $employee->employee_id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.',
        ], 200);
    }

    /**
     * Supervisor: Broadcast notification to their team members.
     * POST /api/v1/employee/supervisor/team/notify
     */
    public function sendTeamNotification(Request $request)
    {
        $supervisor = $request->user();
        if (!$supervisor->isSupervisor()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only supervisors can send team notifications.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:150',
            'body' => 'required|string|max:1000',
            'image_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->firebaseService->sendToTeam(
            $supervisor->employee_id,
            $request->input('title'),
            $request->input('body'),
            [
                'type' => 'supervisor_broadcast',
                'supervisor_name' => $supervisor->employee_name,
                'sent_at' => now()->toIso8601String(),
            ],
            [
                'image_url' => $request->input('image_url'),
                'sender_type' => 'supervisor',
                'sender_id' => $supervisor->employee_id,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Team notification sent.',
            'result' => $result,
        ], 200);
    }
}
