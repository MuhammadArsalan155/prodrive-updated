<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Mail\ManagerCredentials;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ManagerController extends Controller
{
    /**
     * Display a listing of all managers.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $managers = User::whereHas('roles', function ($query) {
            $query->where('name', 'manager');
        })
            ->latest()
            ->paginate(10);

        return view('admin.manager.view', compact('managers'));
    }

    /**
     * Show the form for creating a new manager.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.manager.add');
    }

    /**
     * Store a newly created manager in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'is_active' => 'sometimes|boolean',
        ]);

        // Store plain password before hashing for email
        $plainPassword = $validated['password'];

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'is_active' => $request->has('is_active') ? 1 : 0,
                'email_verified_at' => now(),
            ]);

            $managerRole = Role::where('name', 'manager')->first();

            if (!$managerRole) {
                $managerRole = Role::create([
                    'name' => 'manager',
                    'display_name' => 'Manager',
                    'description' => 'System manager role',
                    'is_system_role' => true,
                ]);
            }

            DB::table('model_has_roles')->insert([
                'role_id' => $managerRole->id,
                'model_id' => $user->id,
                'model_type' => User::class,
            ]);

            $adminId = Auth::id();
            if ($adminId) {
                DB::table('permission_assignment_logs')->insert([
                    'admin_id' => $adminId,
                    'role_id' => $managerRole->id,
                    'model_id' => $user->id,
                    'model_type' => User::class,
                    'action' => 'assign',
                    'reason' => 'New manager registration',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Send welcome email with credentials
            try {
                Mail::to($user->email)->send(new ManagerCredentials($user, $plainPassword, false));
                Log::info('Welcome email sent to manager: ' . $user->email);
            } catch (\Exception $emailError) {
                Log::error('Failed to send welcome email to manager: ' . $user->email . ' - ' . $emailError->getMessage());
                // Don't fail the entire process if email fails
            }

            DB::commit();

            // Success message with email notification
            $message = 'Manager created successfully and credentials sent via email!';
            return redirect()->route('admin.managers.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to create manager: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Failed to create manager: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified manager.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $manager = User::findOrFail($id);

        if (!$manager->hasRole('manager')) {
            return redirect()->route('admin.managers.index')->with('error', 'This user is not a manager.');
        }

        return view('admin.manager.edit', compact('manager'));
    }

    /**
     * Update the specified manager in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $manager = User::findOrFail($id);

        // Check if user has manager role
        if (!$manager->hasRole('manager')) {
            return redirect()->route('admin.managers.index')->with('error', 'This user is not a manager.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($manager->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'sometimes|boolean',
        ]);

        // Store original values to check for changes
        $originalEmail = $manager->email;
        $passwordChanged = !empty($validated['password']);
        $emailChanged = $originalEmail !== $validated['email'];

        // Store plain password if provided
        $plainPassword = $passwordChanged ? $validated['password'] : null;

        DB::beginTransaction();
        try {
            $managerData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'is_active' => $request->has('is_active') ? 1 : 0,
            ];

            // Only update password if provided
            if ($passwordChanged) {
                $managerData['password'] = Hash::make($validated['password']);
            }

            $manager->update($managerData);

            // Send email if password or email changed
            if ($passwordChanged || $emailChanged) {
                try {
                    // If password wasn't changed, we need a placeholder message
                    $passwordForEmail = $passwordChanged ? $plainPassword : '[Password unchanged - use your current password]';

                    Mail::to($manager->email)->send(new ManagerCredentials($manager, $passwordForEmail, true));
                    Log::info('Update notification email sent to manager: ' . $manager->email);

                    $emailMessage = $passwordChanged && $emailChanged ?
                        'Manager updated successfully and new credentials sent via email!' :
                        ($passwordChanged ? 'Manager updated successfully and new password sent via email!' :
                        'Manager updated successfully and updated email notification sent!');

                    $successMessage = $emailMessage;
                } catch (\Exception $emailError) {
                    Log::error('Failed to send update email to manager: ' . $manager->email . ' - ' . $emailError->getMessage());
                    $successMessage = 'Manager updated successfully but email notification failed to send.';
                }
            } else {
                $successMessage = 'Manager updated successfully!';
            }

            DB::commit();
            return redirect()->route('admin.managers.index')->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to update manager: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Failed to update manager: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified manager from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $manager = User::findOrFail($id);

        if (!$manager->hasRole('manager')) {
            return redirect()->route('admin.managers.index')->with('error', 'This user is not a manager.');
        }

        DB::beginTransaction();
        try {
            $managerRole = Role::where('name', 'manager')->first();

            if ($managerRole) {
                $adminId = Auth::id();
                if ($adminId) {
                    DB::table('permission_assignment_logs')->insert([
                        'admin_id' => $adminId,
                        'role_id' => $managerRole->id,
                        'model_id' => $manager->id,
                        'model_type' => User::class,
                        'action' => 'remove',
                        'reason' => 'Manager deleted',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('model_has_roles')->where('role_id', $managerRole->id)->where('model_id', $manager->id)->where('model_type', User::class)->delete();
            }

            $manager->delete();

            DB::commit();
            return redirect()->route('admin.managers.index')->with('success', 'Manager deleted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Failed to delete manager: ' . $e->getMessage());
        }
    }
}
