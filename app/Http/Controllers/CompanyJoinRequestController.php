<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyJoinRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewJoinRequestNotification;
use App\Notifications\JoinRequestStatusNotification;

class CompanyJoinRequestController extends Controller
{
    public function store(Request $request, $companyId)
    {
        $user = Auth::user();

        $company = Company::where('name', $request->name)->first();
        if (!$company) {
            return response()->json(['message' => 'Company not found.'], 404);
        }

        if (CompanyJoinRequest::where('user_id', $user->id)->where('company_id', $company->id)->where('status', 'pending')->exists()) {
            return response()->json(['message' => 'You already have a pending request.'], 400);
        }

        $joinRequest = CompanyJoinRequest::create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'status' => 'pending',
            'message' => $request->input('message'),
        ]);

        // إشعار للمدير
        $manager = $company->owner; // أو حسب علاقة المدير
        if ($manager) {
            $manager->notify(new NewJoinRequestNotification($joinRequest));
        }

        return response()->json(['message' => 'Join request sent successfully.', 'request' => $joinRequest], 201);
    }

    public function index($companyId)
    {
        $user = Auth::user();
        $company = Company::findOrFail($companyId);
        if ($company->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $requests = $company->joinRequests()->with('user')->where('status', 'pending')->get();
        return response()->json(['requests' => $requests]);
    }

    public function approve($requestId)
    {
        $user = Auth::user();
        $joinRequest = CompanyJoinRequest::findOrFail($requestId);
        $company = $joinRequest->company;
        if ($company->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if ($joinRequest->status !== 'pending') {
            return response()->json(['message' => 'Cannot approve a request that is not pending.'], 400);
        }
        $joinRequest->status = 'approved';
        $joinRequest->response_note = request('response_note');
        $joinRequest->save();
        $company->employees()->attach($joinRequest->user_id);
        // إشعار للمستخدم
        $joinRequest->user->notify(new JoinRequestStatusNotification($joinRequest));
        return response()->json(['message' => 'Request approved.']);
    }

    public function reject($requestId)
    {
        $user = Auth::user();
        $joinRequest = CompanyJoinRequest::findOrFail($requestId);
        $company = $joinRequest->company;
        if ($company->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if ($joinRequest->status !== 'pending') {
            return response()->json(['message' => 'Cannot reject a request that is not pending.'], 400);
        }
        $joinRequest->status = 'rejected';
        $joinRequest->response_note = request('response_note');
        $joinRequest->save();
        // إشعار للمستخدم
        $joinRequest->user->notify(new JoinRequestStatusNotification($joinRequest));
        return response()->json(['message' => 'Request rejected.']);
    }





    public function getCompanies()
    {
        $companies = Company::all();
        return response()->json(['companies' => $companies]);
    }

}
