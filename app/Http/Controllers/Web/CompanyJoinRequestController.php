<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CompanyJoinRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyJoinRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = CompanyJoinRequest::query()
            ->with(['company', 'user']);

        // فلترة حسب اسم الشركة
        if ($request->filled('company')) {
            $term = $request->string('company');
            $query->whereHas('company', function ($q) use ($term) {
                $q->where('name', 'LIKE', "%{$term}%");
            });
        }

        // فلترة حسب حالة الطلب
        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $request->status);
        }

        $requests = $query->latest()->paginate(15)->withQueryString();

        return view('join_requests.index', [
            'requests' => $requests,
            'filters' => [
                'company' => $request->input('company', ''),
                'status' => $request->input('status', ''),
            ],
        ]);
    }
}
