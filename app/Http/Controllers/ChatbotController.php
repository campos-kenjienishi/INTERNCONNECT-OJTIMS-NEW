<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\ChatbotAiService;

class ChatbotController extends Controller
{
    protected ChatbotAiService $chatbotService;

    public function __construct(ChatbotAiService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    /**
     * Handle incoming chatbot message
     */
    public function message(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:500',
            'history' => 'nullable|array',
        ]);

        $message = (string) $request->input('message', '');
        $history = (array) $request->input('history', []);

        // Detect user role (1 = Coordinator, 2 = Professor, 3 = Student)
        $userId = session('loginId') ?? (\Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::id() : null);
        $user = $userId ? \App\Models\User::find($userId) : null;
        $role = $user ? (string) $user->role : (session('user_role') ?? session('role') ?? $request->input('role'));

        $userRoleStr = match ((string) $role) {
            '1', 'coordinator', 'admin' => 'coordinator',
            '2', 'professor', 'faculty' => 'professor',
            '0', '3', 'student' => 'student',
            default => 'guest'
        };

        $result = $this->chatbotService->reply($message, $userRoleStr, $history);

        return response()->json([
            'success' => true,
            'reply' => $result['reply'] ?? '',
            'actions' => $result['actions'] ?? [],
            'suggestions' => $result['suggestions'] ?? [],
            'escalate' => (bool) ($result['escalate'] ?? false),
            'source' => $result['source'] ?? 'system',
            'support' => [
                'email' => ChatbotAiService::SUPPORT_EMAIL,
                'facebook' => ChatbotAiService::SUPPORT_FACEBOOK,
                'facebook_name' => ChatbotAiService::SUPPORT_FB_NAME,
            ]
        ]);
    }
}
