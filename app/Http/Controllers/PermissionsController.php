<?php

namespace App\Http\Controllers;

use App\Services\PermissionGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PermissionsController extends Controller
{
    /**
     * @var PermissionGeneratorService
     */
    protected $permissionService;

    /**
     * Constructor
     */
    public function __construct(PermissionGeneratorService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Generate permissions from routes
     */
    public function generatePermissions()
    {
        try {
            // Generate permissions
            $permissions = $this->permissionService->generatePermissionsFromRoutes();

            // Redirect back with success message
            return redirect()->back()->with('success', 'Permissions generated successfully. ' . 
                count($permissions) . ' permissions created or updated.');
        } catch (\Exception $e) {
            // Log the error
            Log::error('Permission generation failed: ' . $e->getMessage());

            // Redirect back with error
            return redirect()->back()->with('error', 'Failed to generate permissions. ' . $e->getMessage());
        }
    }
}
