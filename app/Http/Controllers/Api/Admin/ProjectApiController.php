<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProjectApiController extends ApiController
{
    /**
     * Display a listing of the projects.
     */
    public function index(): JsonResponse
    {
        $projects = Project::latest()->get();
        return $this->success($projects);
    }

    /**
     * Store a newly created project.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_manager_id' => 'nullable|exists:employees,id',
            'team_lead_id' => 'nullable|exists:employees,id',
        ]);

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'project_manager_id' => $request->project_manager_id,
            'team_lead_id' => $request->team_lead_id,
            'created_by' => auth()->id(),
        ]);

        return $this->success($project, 'Project created successfully', 201);
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project): JsonResponse
    {
        return $this->success($project);
    }

    /**
     * Update the specified project.
     */
    public function update(Request $request, Project $project): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'project_manager_id' => 'nullable|exists:employees,id',
            'team_lead_id' => 'nullable|exists:employees,id',
        ]);

        $project->update($request->only(['name', 'description', 'project_manager_id', 'team_lead_id']));

        return $this->success($project, 'Project updated successfully');
    }

    /**
     * Remove the specified project.
     */
    public function destroy(Project $project): JsonResponse
    {
        $project->update(['deleted_by' => auth()->id()]);
        $project->delete();

        return $this->success(null, 'Project deleted successfully');
    }
}
