<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\FcmDeviceToken;
use App\Models\PushNotification;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminNotificationController extends Controller
{
    protected FirebaseNotificationService $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Store Web Admin's FCM token from browser.
     * POST /admin/fcm-token
     */
    public function storeWebFcmToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required|string',
            'device_name' => 'nullable|string|max:191',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = Auth::id();
        $device = FcmDeviceToken::storeToken(
            $request->input('fcm_token'),
            null,
            $userId,
            'web',
            $request->input('device_name', $request->header('User-Agent', 'Web Browser'))
        );

        return response()->json([
            'success' => true,
            'message' => 'Web push notification token registered successfully.',
            'data' => [
                'id' => $device->id,
                'is_active' => $device->is_active,
            ],
        ]);
    }

    /**
     * Notifications management page.
     * GET /admin/notifications
     */
    public function index(Request $request)
    {
        $deviceStats = [
            'total' => FcmDeviceToken::where('is_active', true)->count(),
            'android' => FcmDeviceToken::where('device_type', 'android')->where('is_active', true)->count(),
            'web' => FcmDeviceToken::where('device_type', 'web')->where('is_active', true)->count(),
            'ios' => FcmDeviceToken::where('device_type', 'ios')->where('is_active', true)->count(),
        ];

        $employees = Employee::where('status', 'Active')->select('employee_id', 'employee_name', 'employee_email')->orderBy('employee_name')->get();

        $notifications = PushNotification::with(['employee:employee_id,employee_name', 'user:id,name'])
            ->orderBy('id', 'desc')
            ->paginate(15);

        $firebaseConfig = [
            'apiKey' => config('firebase.api_key'),
            'authDomain' => config('firebase.auth_domain'),
            'projectId' => config('firebase.project_id'),
            'storageBucket' => config('firebase.storage_bucket'),
            'messagingSenderId' => config('firebase.messaging_sender_id'),
            'appId' => config('firebase.app_id_web'),
            'measurementId' => config('firebase.measurement_id'),
        ];

        return view('admin.notifications.index', compact('deviceStats', 'employees', 'notifications', 'firebaseConfig'));
    }

    /**
     * Send push notification from admin panel.
     * POST /admin/notifications/send
     */
    public function send(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'body' => 'required|string|max:1000',
            'target_type' => 'required|string|in:all_employees,single_employee,all_web,team',
            'employee_id' => 'required_if:target_type,single_employee|nullable|integer',
            'action_url' => 'nullable|url',
            'image_url' => 'nullable|url',
        ]);

        $title = $request->input('title');
        $body = $request->input('body');
        $targetType = $request->input('target_type');
        $options = [
            'action_url' => $request->input('action_url'),
            'image_url' => $request->input('image_url'),
            'sender_type' => 'admin',
            'sender_id' => Auth::id(),
        ];

        $data = [
            'source' => 'admin_portal',
            'sent_at' => now()->toIso8601String(),
            'action_url' => $request->input('action_url', ''),
        ];

        if ($targetType === 'all_employees') {
            $result = $this->firebaseService->broadcastToAllEmployees($title, $body, $data, $options);
        } elseif ($targetType === 'single_employee') {
            $employeeId = (int) $request->input('employee_id');
            $result = $this->firebaseService->sendToEmployee($employeeId, $title, $body, $data, $options);
        } elseif ($targetType === 'all_web') {
            $result = $this->firebaseService->broadcastToAllWebUsers($title, $body, $data, $options);
        } else {
            $result = [
                'success' => false,
                'message' => 'Invalid target type.',
            ];
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result);
        }

        return redirect()->back()->with('success', $result['message'] ?? 'Notification processed.');
    }
}
