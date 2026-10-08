<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EmployeeController extends Controller
{
    public function connectCompanyToUser($companyId)
    {
        $user = Auth::user();

        // Optionally, check if the company exists
        $company = Company::findOrFail($companyId);

        // Store the relationship in the employee_company table
        $user->companies()->syncWithoutDetaching([$company->id]);

        return response()->json([
            'message' => 'Company connected to user successfully.',
            'user' => $user
        ], 200);
    }

    public function getEmployeesByCompany($companyId)
    {
        try {
            // Get the authenticated user
            $user = Auth::user();
            
            // Get the company
            $company = Company::findOrFail($companyId);
            
            // Check if the authenticated user is the manager of this company
            if ($company->user_id !== $user->id) {
                return response()->json([
                    'message' => 'Unauthorized - You can only view employees of your own company'
                ], 403);
            }
            
            // Get all users that belong to the company through the employee_company table
            $users = $company->employees()
                ->select('users.*')
                ->get();

            return response()->json([
                'message' => 'Company users retrieved successfully',
                'users' => $users
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Company not found',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve company users',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
