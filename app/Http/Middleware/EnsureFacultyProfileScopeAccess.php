<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFacultyProfileScopeAccess
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string $scope): Response
    {
        $user = $request->user()?->loadMissing(['employeeProfile', 'facultyProfile']);

        abort_unless($user && $user->can('faculty_profiles.view'), 403);

        $allowed = match ($scope) {
            'college' => $user->canAccessCollegeFacultyProfiles(),
            'department' => $user->canAccessDepartmentFacultyProfiles(),
            default => false,
        };

        abort_unless($allowed, 403);

        return $next($request);
    }
}
