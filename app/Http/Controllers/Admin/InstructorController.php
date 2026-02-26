<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use App\Models\Role;
use App\Mail\InstructorCredentials;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class InstructorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function addinstructor()
    {
        return view('admin.instructor.add');
    }

    public function viewinstructor()
    {
        $instructors = Instructor::orderBy('created_at', 'desc')->get();
        return view('admin.instructor.view', compact('instructors'));
    }

    public function add_instructor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'instructor_name' => 'required|string|max:255',
            'email' => 'required|email|unique:instructors',
            'contact' => 'required|string|regex:/^[0-9+().\- ]{10,15}$/',
            'password' => 'required|min:6',
            'license_number' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            toastr()->error('Validation failed', 'Error!');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Store plain password before hashing for email
        $plainPassword = $request->password;

        // Start a database transaction
        DB::beginTransaction();

        try {
            $instructor = new Instructor();
            $instructor->instructor_name = $request->instructor_name;
            $instructor->email = $request->email;
            $instructor->contact = $request->contact;
            $instructor->password = Hash::make($request->password);
            $instructor->license_number = $request->license_number;
            $instructor->is_active = $request->has('is_active');
            $instructor->save();

            $instructorRole = Role::where('name', 'instructor')->first();

            if (!$instructorRole) {
                $instructorRole = Role::create([
                    'name' => 'instructor',
                    'display_name' => 'Instructor',
                    'description' => 'Instructor role for teaching courses',
                    'is_system_role' => true,
                ]);
            }

            DB::table('model_has_roles')->insert([
                'role_id' => $instructorRole->id,
                'model_id' => $instructor->id,
                'model_type' => Instructor::class,
            ]);

            if ($request->role === 'admin') {
                $adminRole = Role::where('name', 'admin')->first();

                if ($adminRole) {
                    DB::table('model_has_roles')->insert([
                        'role_id' => $adminRole->id,
                        'model_id' => $instructor->id,
                        'model_type' => Instructor::class,
                    ]);
                }
            }

            $adminId = Auth::id();
            if ($adminId) {
                DB::table('permission_assignment_logs')->insert([
                    'admin_id' => $adminId,
                    'role_id' => $instructorRole->id,
                    'model_id' => $instructor->id,
                    'model_type' => Instructor::class,
                    'action' => 'assign',
                    'reason' => 'New instructor registration',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Send email with credentials
            try {
                Mail::to($instructor->email)->send(new InstructorCredentials($instructor, $plainPassword, false));
                Log::info('Welcome email sent to instructor: ' . $instructor->email);
            } catch (\Exception $emailError) {
                Log::error('Failed to send welcome email to instructor: ' . $instructor->email . ' - ' . $emailError->getMessage());
                // Don't fail the entire process if email fails
            }

            // Commit the transaction
            DB::commit();

            toastr()->success('Instructor created successfully and credentials sent via email!', 'Success!');
            return redirect()->route('admin.viewinstructor');
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();

            // Log the error message
            Log::error('Failed to create instructor: ' . $e->getMessage());

            toastr()->error('Adding Instructor to Database Failed', 'Error!');
            return redirect()->back();
        }
    }

    public function delete_instructor($id)
    {
        try {
            $instructor = Instructor::findOrFail($id);
            $instructor->delete();
            toastr()->success('Instructor has been deleted successfully!', 'Completed!');
        } catch (\Exception $e) {
            toastr()->error('Deleting Instructor Failed', 'Error!');
        }
        return redirect()->back();
    }

    public function edit_instructor(Instructor $instructor)
    {
        return view('admin.instructor.edit', compact('instructor'));
    }

    public function update_instructor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'instructor_name' => 'required|string|max:255',
            'email' => 'required|email|unique:instructors,email,'.$request->id,
            'contact' => 'required|string|regex:/^[0-9+().\- ]{10,15}$/',
            'password' => 'nullable|min:6',
            'license_number' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            toastr()->error('Validation failed', 'Error!');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $instructor = Instructor::findOrFail($request->id);

            // Store original values to check for changes
            $originalEmail = $instructor->email;
            $passwordChanged = $request->filled('password');
            $emailChanged = $originalEmail !== $request->email;

            // Store plain password if provided
            $plainPassword = $passwordChanged ? $request->password : null;

            // Update instructor details
            $instructor->instructor_name = $request->instructor_name;
            $instructor->email = $request->email;
            $instructor->contact = $request->contact;

            if ($passwordChanged) {
                $instructor->password = Hash::make($request->password);
            }

            $instructor->license_number = $request->license_number;
            $instructor->is_active = $request->has('is_active');
            $instructor->save();

            // Send email if password or email changed
            if ($passwordChanged || $emailChanged) {
                try {
                    // If password wasn't changed, we need a placeholder message
                    $passwordForEmail = $passwordChanged ? $plainPassword : '[Password unchanged - use your current password]';

                    Mail::to($instructor->email)->send(new InstructorCredentials($instructor, $passwordForEmail, true));
                    Log::info('Update notification email sent to instructor: ' . $instructor->email);

                    $emailMessage = $passwordChanged && $emailChanged ?
                        'Instructor updated successfully and new credentials sent via email!' :
                        ($passwordChanged ? 'Instructor updated successfully and new password sent via email!' :
                        'Instructor updated successfully and updated email notification sent!');

                    toastr()->success($emailMessage, 'Success!');
                } catch (\Exception $emailError) {
                    Log::error('Failed to send update email to instructor: ' . $instructor->email . ' - ' . $emailError->getMessage());
                    toastr()->warning('Instructor updated successfully but email notification failed to send.', 'Partial Success!');
                }
            } else {
                toastr()->success('Instructor updated successfully!', 'Success!');
            }

            return redirect()->route('admin.viewinstructor');
        } catch (\Exception $e) {
            Log::error('Failed to update instructor: ' . $e->getMessage());
            toastr()->error('Updating Instructor Failed', 'Error!');
            return redirect()->back();
        }
    }
}
