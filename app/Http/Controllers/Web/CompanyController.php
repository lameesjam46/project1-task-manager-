<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::with(['employees'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = $request->query('search');
                $query->where('name', 'LIKE', "{$term}%");
            })
            ->latest()
            ->paginate(15);

        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        // Only managers who are active and not linked to ANY company in any way
        // Exclusions:
        // 1) users linked via pivot employee_company (users.companies)
        // 2) users linked via users.company_id (direct FK)
        // 3) users who are owners of companies via companies.user_id
        $ownersSubquery = \App\Models\Company::query()->whereNotNull('user_id')->pluck('user_id');

        $users = User::query()
            ->where('role', 'manager')
            ->where('active', true)
            ->whereDoesntHave('companies')
            ->whereNull('company_id')
            ->whereNotIn('id', $ownersSubquery)
            ->orderBy('name')
            ->get();
        return view('companies.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:companies,name',
            'user_id' => 'required|exists:users,id',
        ]);

        // Diagnostics: log selected user and checks
        $u = User::withCount('companies')->find($request->user_id);
        $isOwnerDiag = $u ? Company::where('user_id', $u->id)->exists() : null;
        Log::debug('Company manager selection diagnostics', [
            'selected_user_id' => $request->user_id,
            'exists' => (bool) $u,
            'role' => $u->role ?? null,
            'active' => $u->active ?? null,
            'companies_count' => $u->companies_count ?? null,
            'company_id' => $u->company_id ?? null,
            'is_owner_of_company' => $isOwnerDiag,
        ]);

        // Enforce: selected user must be an active manager and not linked to ANY company (same logic as create())
        $ownersSub = Company::query()->whereNotNull('user_id')->select('user_id');
        $eligible = User::query()
            ->whereKey($request->user_id)
            ->where('role', 'manager')
            ->where('active', true)
            ->whereDoesntHave('companies')
            ->whereNull('company_id')
            ->whereNotIn('id', $ownersSub)
            ->exists();

        Log::debug('Company manager eligibility result', [
            'eligible' => $eligible,
        ]);

        if (!$eligible) {
            return back()
                ->withErrors(['user_id' => 'يجب اختيار مدير بنوع الدور (Manager) وغير منتمي لأي شركة.'])
                ->withInput();
        }

        $company = Company::create([
            'name' => $request->name,
            'user_id' => $request->user_id,
        ]);

        return redirect()->route('companies.index')->with('success', 'تم إنشاء الشركة بنجاح!');
    }

    public function show(Company $company)
    {
        $company->load(['employees', 'projects', 'owner']);
        return view('companies.show', compact('company'));
    }

    public function edit(Company $company)
    {
        // Match create() eligibility, but keep current company's owner selectable
        $ownersSub = Company::query()->whereNotNull('user_id')->select('user_id');

        $users = User::query()
            ->where('role', 'manager')
            ->where('active', true)
            ->where(function ($q) use ($company, $ownersSub) {
                $q->where(function ($q2) use ($ownersSub) {
                    $q2->whereDoesntHave('companies')
                        ->whereNull('company_id')
                        ->whereNotIn('id', $ownersSub);
                })
                // Always include the current owner to avoid excluding the existing selection
                ->orWhere('id', $company->user_id);
            })
            ->orderBy('name')
            ->get();

        return view('companies.edit', compact('company', 'users'));
    }

    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:companies,name,' . $company->id,
            'user_id' => 'required|exists:users,id',
        ]);

        $company->update([
            'name' => $request->name,
            'user_id' => $request->user_id,
        ]);

        return redirect()->route('companies.index')->with('success', 'تم تحديث الشركة بنجاح!');
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('companies.index')->with('success', 'تم حذف الشركة بنجاح!');
    }
} 