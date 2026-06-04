<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            ['name' => 'Dashboard',           'slug' => 'dashboard',           'route' => '/dashboard',           'icon' => 'bx-grid-alt',        'status' => 'active'],
            ['name' => 'Onboarding',          'slug' => 'onboarding',          'route' => '/onboarding',          'icon' => 'bx-user-plus',       'status' => 'active'],
            ['name' => 'Employees',           'slug' => 'employees',           'route' => '/employees',           'icon' => 'bx-group',           'status' => 'active'],
            ['name' => 'Projects',            'slug' => 'projects',            'route' => '/projects',            'icon' => 'bx-briefcase',       'status' => 'active'],
            ['name' => 'Project Assignments', 'slug' => 'project-assignments', 'route' => '/project-assignments', 'icon' => 'bx-user-check',      'status' => 'active'],
            ['name' => 'Attendance',          'slug' => 'attendance',          'route' => '/attendance',          'icon' => 'bx-fingerprint',     'status' => 'active'],
            ['name' => 'Leaves',              'slug' => 'leaves',              'route' => '/leaves',              'icon' => 'bx-calendar-check',  'status' => 'active'],
            ['name' => 'Task Reports',        'slug' => 'task-reports',        'route' => '/task-reports',        'icon' => 'bx-list-ul',         'status' => 'active'],
            ['name' => 'WFH Requests',        'slug' => 'wfh-requests',        'route' => '/wfh-requests',        'icon' => 'bx-home',            'status' => 'active'],
            ['name' => 'Reports',             'slug' => 'reports',             'route' => '/reports',             'icon' => 'bx-bar-chart-alt-2', 'status' => 'active'],
            ['name' => 'Payroll',             'slug' => 'payroll',             'route' => '/payroll',             'icon' => 'bx-dollar-circle',   'status' => 'active'],
            ['name' => 'Roles',               'slug' => 'roles',               'route' => '/roles',               'icon' => 'bx-shield',          'status' => 'active'],
            ['name' => 'Organizations',       'slug' => 'organizations',       'route' => '/organizations',       'icon' => 'bx-building',        'status' => 'active'],
            ['name' => 'Agreements',          'slug' => 'agreements',          'route' => '/agreements',          'icon' => 'bx-file',            'status' => 'active'],
            ['name' => 'Settings',            'slug' => 'settings',            'route' => '/settings',            'icon' => 'bx-cog',             'status' => 'active'],
        ];

        foreach ($modules as $module) {
            DB::table('modules')->updateOrInsert(
                ['slug' => $module['slug']],  // check by slug
                $module                        // insert or update
            );
        }
    }
}