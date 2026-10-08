<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskNoteController extends Controller
{
    
    public function addNote(Request $request, $taskId)
    {
        try {
            $request->validate([
                'note' => 'required|string',
                'attachment' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,gif,doc,docx,xls,xlsx',
            ]);

            $task = Task::findOrFail($taskId);
            $user = Auth::user();

            // Check if the user is assigned to the task
            if (!$task->assignedUsers()->where('users.id', $user->id)->exists()) {
                return response()->json([
                    'message' => 'Unauthorized - You must be assigned to this task to add notes'
                ], 403);
            }

            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('task_notes_attachments', 'public');
            }

            
            $note = $task->notes()->create([
                'user_id' => $user->id,
                'note' => $request->note,
                'attachment' => $attachmentPath,
            ]);

            // Load the user relationship for the response
            $note->load('user');

            return response()->json([
                'message' => 'Note added successfully',
                'note' => [
                    'id' => $note->id,
                    'note' => $note->note,
                    'user' => $note->user,
                    'attachment' => $note->attachment,
                    'attachment_url' => $note->attachment_url,
                    'created_at' => $note->created_at,
                ]
            ], 201);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Task not found',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to add note',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // جلب بيانات ملاحظة واحدة
    public function getNote($noteId)
    {
        $note = TaskNote::with('user')->findOrFail($noteId);
        return response()->json([
            'id' => $note->id,
            'note' => $note->note,
            'user' => $note->user,
            'attachment' => $note->attachment,
            'attachment_url' => $note->attachment_url,
            'created_at' => $note->created_at,
        ]);
    }

    // جلب جميع ملاحظات مهمة معينة
    public function getNotesForTask($taskId)
    {
        $notes = TaskNote::with('user')->where('task_id', $taskId)->orderBy('created_at', 'desc')->get();
        return response()->json($notes->map(function($note) {
            return [
                'id' => $note->id,
                'note' => $note->note,
                'user' => $note->user,
                'attachment' => $note->attachment,
                'attachment_url' => $note->attachment_url,
                'created_at' => $note->created_at,
            ];
        }));
    }

    // حذف ملاحظة مع حذف المرفق إن وجد
    public function deleteNote($noteId)
    {
        $note = TaskNote::findOrFail($noteId);
        $user = Auth::user();
        if ($note->user_id !== $user->id) {
            return response()->json(['message' => 'You are not authorized to delete this note.'], 403);
        }
        if ($note->attachment) {
            Storage::disk('public')->delete($note->attachment);
        }
        $note->delete();
        return response()->json(['message' => 'Note and attachment (if any) deleted successfully.']);
    }

    
    public function updateNote(Request $request, $noteId)
    {
        $note = TaskNote::findOrFail($noteId);
        $user = Auth::user();
        if ($note->user_id !== $user->id) {
            return response()->json(['message' => 'You are not authorized to edit this note.'], 403);
        }
        $request->validate([
            'note' => 'sometimes|string',
            'attachment' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,gif,doc,docx,xls,xlsx',
        ]);
        
        if ($request->filled('note')) {
            $note->note = $request->note;
        }
        // إذا تم رفع مرفق جديد، احذف القديم وضع الجديد
        if ($request->hasFile('attachment')) {
            if ($note->attachment) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($note->attachment);
            }
            $note->attachment = $request->file('attachment')->store('task_notes_attachments', 'public');
        }
        $note->save();
        $note->load('user');
        return response()->json([
            'message' => 'Note updated successfully.',
            'note' => [
                'id' => $note->id,
                'note' => $note->note,
                'user' => $note->user,
                'attachment' => $note->attachment,
                'attachment_url' => $note->attachment_url,
                'created_at' => $note->created_at,
            ]
        ]);
    }
}
