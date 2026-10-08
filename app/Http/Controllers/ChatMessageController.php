<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatMessageController extends Controller
{
    // إرسال رسالة
    public function sendMessage(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'message' => 'required|string',
        ]);

        $user = Auth::user();
        $project = Project::findOrFail($request->project_id);

        // تحقق أن المستخدم عضو في فريق العمل أو مدير المشروع
        $isTeamMember = $project->teamMembers()->where('users.id', $user->id)->exists();
        $isManager = $project->project_manager_id == $user->id;
        if (!($isTeamMember || $isManager)) {
            return response()->json(['message' => 'Unauthorized: Only project team members or the project manager can send messages.'], 403);
        }

        $chatMessage = ChatMessage::create([
            'project_id' => $project->id,
            'sender_id' => $user->id,
            'message' => $request->message,
            'sent_at' => now(),
        ]);

        return response()->json(['message' => 'Message sent', 'data' => $chatMessage], 201);
    }

    // جلب رسائل مشروع
    public function getProjectMessages($projectId)
    {
        $messages = ChatMessage::where('project_id', $projectId)
            ->orderBy('sent_at', 'asc')
            ->with('sender:id,name')
            ->select('id', 'sender_id', 'message', 'sent_at')
            ->get()
            ->map(function ($message) {
                return [
                    'sender_id' => $message->sender_id,
                    'sender_name' => $message->sender->name,
                    'message' => $message->message,
                    'sent_at' => $message->sent_at
                ];
            });

        return response()->json(['messages' => $messages]);
    }

    // جلب رسائل مستخدم في مشروع معين
    public function getUserMessages($userId, $projectId)
    {
        $messages = ChatMessage::where('sender_id', $userId)
            ->where('project_id', $projectId)
            ->orderBy('sent_at', 'desc')
            ->select('message', 'sent_at')
            ->get();

        return response()->json(['messages' => $messages]);
    }

    // حذف رسالة
    public function deleteMessage($id)
    {
        $user = Auth::user();
        $message = ChatMessage::findOrFail($id);
        $project = $message->project;

        $isManager = $project->project_manager_id == $user->id;
        $isSender = $message->sender_id == $user->id;

        if (!($isManager || $isSender)) {
            return response()->json(['message' => 'Unauthorized: Only the sender or the project manager can delete this message.'], 403);
        }

        $message->delete();
        return response()->json(['message' => 'Message deleted successfully.']);
    }

    // تعديل رسالة
    public function updateMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $message = ChatMessage::findOrFail($id);

        // تحقق أن المستخدم هو صاحب الرسالة فقط
        if ($message->sender_id != $user->id) {
            return response()->json(['message' => 'Unauthorized: Only the sender can edit this message.'], 403);
        }

        $message->update([
            'message' => $request->message,
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Message updated successfully',
            'data' => [
                'id' => $message->id,
                'message' => $message->message,
                'sent_at' => $message->sent_at,
                'updated_at' => $message->updated_at
            ]
        ], 200);
    }
} 