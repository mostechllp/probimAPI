<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProjectAssignmentApiController extends ApiController
{
    /**
     * Display a listing of employees and their assigned projects.
     */
    public function index(): JsonResponse
    {
        $employees = Employee::with('projects')->get();
        return $this->success($employees);
    }

    /**
     * Assign projects to an employee.
     */
    public function assign(Request $request): JsonResponse
    {
        $request->validate([
            'employee_id'   => 'required|exists:employees,id',
            'project_ids'   => 'array',
            'project_ids.*' => 'exists:projects,id',
        ]);

        $employee = Employee::find($request->employee_id);
        if (!$employee) {
            return $this->error('Employee not found', 404);
        }

        $newProjectIds = collect($request->project_ids ?? []);
        
        // Existing active projects
        $existingProjects = $employee->projects()->pluck('projects.id');

        $toDetach = $existingProjects->diff($newProjectIds);

        // Detach (Soft delete)
        if ($toDetach->isNotEmpty()) {
            \Illuminate\Support\Facades\DB::table('employee_project')
                ->where('employee_id', $employee->id)
                ->whereIn('project_id', $toDetach)
                ->whereNull('deleted_at')
                ->update([
                    'deleted_by' => auth()->id(),
                    'deleted_at' => now(),
                    'updated_at' => now(),
                ]);
        }

        // Attach or restore
        foreach ($newProjectIds as $projectId) {
            if (!$existingProjects->contains($projectId)) {
                $existingPivot = \Illuminate\Support\Facades\DB::table('employee_project')
                    ->where('employee_id', $employee->id)
                    ->where('project_id', $projectId)
                    ->first();

                if ($existingPivot) {
                    \Illuminate\Support\Facades\DB::table('employee_project')
                        ->where('id', $existingPivot->id)
                        ->update([
                            'deleted_at' => null,
                            'deleted_by' => null,
                            'assigned_by' => auth()->id(),
                            'updated_at' => now(),
                        ]);
                } else {
                    $employee->projects()->attach($projectId, [
                        'assigned_by' => auth()->id(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }

        // Reload to get fresh projects
        $employee->load('projects');

        return $this->success($employee, 'Projects assigned successfully');
    }
}
