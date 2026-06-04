<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Employee;
use App\Models\Offboarding;
use App\Models\OffboardingChecklist;
use App\Models\EmployeeAsset;
use App\Models\OffboardingInterview;
use App\Models\OffboardingSettlement;
use App\Models\OffboardingLetter;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OffboardingApiController extends ApiController
{
    /**
     * Display a listing of offboardings.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Offboarding::with(['employee', 'checklists', 'assets']);

        // Depending on role, filter the query.
        $user = $request->user();
        if ($user->type !== 'admin' && $user->role?->name !== 'HR Manager') {
            // Only show offboardings for employees this user manages
            if ($user->employee) {
                $query->whereHas('employee', function ($q) use ($user) {
                    $q->where('reporting_manager_id', $user->employee->id);
                });
            } else {
                return $this->error('Unauthorized access', 403);
            }
        }

        $offboardings = $query->paginate(15);
        return $this->success($offboardings);
    }

    /**
     * Initiate offboarding
     */
    public function initiate(Request $request): JsonResponse
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'last_working_day' => 'nullable|date',
            'separation_type' => 'nullable|string',
            'notice_period_days' => 'nullable|integer',
            'notice_start_date' => 'nullable|date',
            'visa_sponsorship' => 'nullable|string',
            'nationality' => 'nullable|string',
            'reason_for_leaving' => 'nullable|string',
            'is_draft' => 'boolean'
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        
        // Check authorization
        if (!$this->isAuthorized($request->user(), $employee)) {
            return $this->error('Unauthorized access', 403);
        }

        $offboarding = Offboarding::updateOrCreate(
            ['employee_id' => $employee->id],
            [
                'status' => $request->is_draft ? 'draft' : 'pending_visa',
                'last_working_day' => $request->last_working_day,
                'separation_type' => $request->separation_type,
                'notice_period_days' => $request->notice_period_days,
                'notice_start_date' => $request->notice_start_date,
                'visa_sponsorship' => $request->visa_sponsorship,
                'nationality' => $request->nationality,
                'reason_for_leaving' => $request->reason_for_leaving,
            ]
        );

        return $this->success($offboarding, 'Offboarding initiated successfully');
    }

    /**
     * Display the specified offboarding.
     */
    public function show(Request $request, $id): JsonResponse
    {
        $offboarding = Offboarding::with([
            'employee', 
            'checklists', 
            'assets', 
            'interview', 
            'settlement', 
            'letters'
        ])->findOrFail($id);

        if (!$this->isAuthorized($request->user(), $offboarding->employee)) {
            return $this->error('Unauthorized access', 403);
        }

        return $this->success($offboarding);
    }

    /**
     * Update Visa Status
     */
    public function updateVisaStatus(Request $request, $id): JsonResponse
    {
        $offboarding = Offboarding::findOrFail($id);
        
        if (!$this->isAuthorized($request->user(), $offboarding->employee)) {
            return $this->error('Unauthorized access', 403);
        }

        // Logic to update visa cancellation checklist items
        $tasks = $request->input('tasks', []); // array of { id, status }
        foreach ($tasks as $task) {
            OffboardingChecklist::where('id', $task['id'])
                ->where('offboarding_id', $offboarding->id)
                ->where('category', 'visa_cancellation')
                ->update(['status' => $task['status']]);
        }

        // Optionally progress status
        $offboarding->update(['status' => 'pending_checklist']);

        return $this->success($offboarding->load('checklists'), 'Visa status updated');
    }

    /**
     * Update General Checklists
     */
    public function updateChecklist(Request $request, $id): JsonResponse
    {
        $offboarding = Offboarding::findOrFail($id);
        
        if (!$this->isAuthorized($request->user(), $offboarding->employee)) {
            return $this->error('Unauthorized access', 403);
        }

        $tasks = $request->input('tasks', []); // array of { id, status }
        foreach ($tasks as $task) {
            OffboardingChecklist::where('id', $task['id'])
                ->where('offboarding_id', $offboarding->id)
                ->update(['status' => $task['status']]);
        }

        return $this->success($offboarding->load('checklists'), 'Checklist updated');
    }

    /**
     * Update Assets Return
     */
    public function updateAssets(Request $request, $id): JsonResponse
    {
        $offboarding = Offboarding::findOrFail($id);
        
        if (!$this->isAuthorized($request->user(), $offboarding->employee)) {
            return $this->error('Unauthorized access', 403);
        }

        $assets = $request->input('assets', []); // array of { id, status, condition }
        foreach ($assets as $asset) {
            EmployeeAsset::where('id', $asset['id'])
                ->where('offboarding_id', $offboarding->id)
                ->update([
                    'status' => $asset['status'],
                    'condition' => $asset['condition'] ?? null
                ]);
        }

        return $this->success($offboarding->load('assets'), 'Assets updated');
    }

    /**
     * Submit Exit Interview
     */
    public function submitInterview(Request $request, $id): JsonResponse
    {
        $offboarding = Offboarding::findOrFail($id);
        
        if (!$this->isAuthorized($request->user(), $offboarding->employee)) {
            return $this->error('Unauthorized access', 403);
        }

        $data = $request->only([
            'interviewer', 'interview_date', 'interview_mode',
            'overall_satisfaction', 'primary_reason', 'work_life_rating',
            'manager_relationship_rating', 'enjoyed_most', 'areas_for_improvement',
            'would_recommend'
        ]);

        $interview = OffboardingInterview::updateOrCreate(
            ['offboarding_id' => $offboarding->id],
            $data
        );

        return $this->success($interview, 'Exit interview submitted successfully');
    }

    /**
     * Update Settlement
     */
    public function updateSettlement(Request $request, $id): JsonResponse
    {
        $offboarding = Offboarding::findOrFail($id);
        
        if (!$this->isAuthorized($request->user(), $offboarding->employee)) {
            return $this->error('Unauthorized access', 403);
        }

        $data = $request->only(['total_payable', 'total_deductions', 'net_payable', 'status', 'remarks']);

        $settlement = OffboardingSettlement::updateOrCreate(
            ['offboarding_id' => $offboarding->id],
            $data
        );

        return $this->success($settlement, 'Settlement updated successfully');
    }

    /**
     * Generate Letters
     */
    public function generateLetters(Request $request, $id): JsonResponse
    {
        $offboarding = Offboarding::findOrFail($id);
        
        if (!$this->isAuthorized($request->user(), $offboarding->employee)) {
            return $this->error('Unauthorized access', 403);
        }

        // Simplistic logic for letter generation tracking
        $data = $request->only(['letter_type', 'document_path', 'status']);
        
        $letter = OffboardingLetter::updateOrCreate(
            ['offboarding_id' => $offboarding->id, 'letter_type' => $data['letter_type']],
            $data
        );

        return $this->success($letter, 'Letter record updated successfully');
    }

    /**
     * Check if user is authorized to manage this offboarding.
     */
    private function isAuthorized($user, $employee): bool
    {
        if ($user->type === 'admin' || $user->role?->name === 'HR Manager') {
            return true;
        }

        if ($user->employee && $employee->reporting_manager_id === $user->employee->id) {
            return true;
        }

        return false;
    }
}
