<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Role;
use App\Models\User;
use App\Models\Student;
use App\Models\Instructor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of announcements.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $announcements = Announcement::with('creator')
                                     ->orderBy('created_at', 'desc')
                                     ->paginate(10);
        
        return view('admin.announcements.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new announcement.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all();
        $users = User::where('is_active', true)->get();
        $students = Student::all();
        $instructors = Instructor::all();
        
        return view('admin.announcements.create', compact('roles', 'users', 'students', 'instructors'));
    }

    /**
     * Store a newly created announcement in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_active' => 'boolean',
            'attachment' => 'nullable|file|max:10240', // 10MB max
            'target_type' => 'required|in:all,role,user',
            'expires_at' => 'nullable|date|after:today',
            'roles' => 'nullable|array',
            'users' => 'nullable|array',
            'students' => 'nullable|array',
            'instructors' => 'nullable|array',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Handle file upload if attachment exists
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('announcements');
            }
            
            // Create announcement
            $announcement = Announcement::create([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'created_by' => Auth::id(),
                'is_active' => $request->has('is_active'),
                'attachment' => $attachmentPath,
                'target_type' => $validated['target_type'],
                'expires_at' => $validated['expires_at'] ?? null,
            ]);
            
            // Attach roles if target_type is role
            if ($validated['target_type'] === 'role' && isset($validated['roles'])) {
                $announcement->targetRoles()->attach($validated['roles']);
            }
            
            // Attach users if target_type is user
            if ($validated['target_type'] === 'user') {
                // Attach general users
                if (isset($validated['users'])) {
                    $announcement->targetUsers()->attach($validated['users']);
                }
                
                // Attach students
                if (isset($validated['students'])) {
                    $announcement->targetStudents()->attach($validated['students']);
                }
                
                // Attach instructors
                if (isset($validated['instructors'])) {
                    $announcement->targetInstructors()->attach($validated['instructors']);
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.announcements.index')
                            ->with('success', 'Announcement created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded file if transaction failed
            if (isset($attachmentPath) && Storage::exists($attachmentPath)) {
                Storage::delete($attachmentPath);
            }
            
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Failed to create announcement: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified announcement.
     *
     * @param  \App\Models\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function show(Announcement $announcement)
    {
        $announcement->load(['creator', 'targetRoles', 'targetUsers', 'targetStudents', 'targetInstructors']);
        
        return view('admin.announcements.show', compact('announcement'));
    }

    /**
     * Show the form for editing the specified announcement.
     *
     * @param  \App\Models\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function edit(Announcement $announcement)
    {
        $roles = Role::all();
        $users = User::where('is_active', true)->get();
        $students = Student::all();
        $instructors = Instructor::all();
        
        // Load relationships
        $announcement->load(['targetRoles', 'targetUsers', 'targetStudents', 'targetInstructors']);
        
        return view('admin.announcements.edit', compact('announcement', 'roles', 'users', 'students', 'instructors'));
    }

    /**
     * Update the specified announcement in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_active' => 'boolean',
            'attachment' => 'nullable|file|max:10240', // 10MB max
            'target_type' => 'required|in:all,role,user',
            'expires_at' => 'nullable|date|after:today',
            'roles' => 'nullable|array',
            'users' => 'nullable|array',
            'students' => 'nullable|array',
            'instructors' => 'nullable|array',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Handle file upload if attachment exists
            $attachmentPath = $announcement->attachment;
            if ($request->hasFile('attachment')) {
                // Delete old attachment
                if ($attachmentPath && Storage::exists($attachmentPath)) {
                    Storage::delete($attachmentPath);
                }
                
                $attachmentPath = $request->file('attachment')->store('announcements');
            } else if ($request->has('remove_attachment') && $attachmentPath) {
                // Remove attachment if requested
                Storage::delete($attachmentPath);
                $attachmentPath = null;
            }
            
            // Update announcement
            $announcement->update([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'is_active' => $request->has('is_active'),
                'attachment' => $attachmentPath,
                'target_type' => $validated['target_type'],
                'expires_at' => $validated['expires_at'] ?? null,
            ]);
            
            // Sync roles if target_type is role
            if ($validated['target_type'] === 'role') {
                $announcement->targetRoles()->sync($validated['roles'] ?? []);
            } else {
                $announcement->targetRoles()->detach();
            }
            
            // Sync users if target_type is user
            if ($validated['target_type'] === 'user') {
                // Sync general users
                $announcement->targetUsers()->sync($validated['users'] ?? []);
                
                // Sync students
                $announcement->targetStudents()->sync($validated['students'] ?? []);
                
                // Sync instructors
                $announcement->targetInstructors()->sync($validated['instructors'] ?? []);
            } else {
                $announcement->targetUsers()->detach();
                $announcement->targetStudents()->detach();
                $announcement->targetInstructors()->detach();
            }
            
            DB::commit();
            
            return redirect()->route('admin.announcements.index')
                            ->with('success', 'Announcement updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Failed to update announcement: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified announcement from storage.
     *
     * @param  \App\Models\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function destroy(Announcement $announcement)
    {
        try {
            // Delete attachment if exists
            if ($announcement->attachment && Storage::exists($announcement->attachment)) {
                Storage::delete($announcement->attachment);
            }
            
            $announcement->delete();
            
            return redirect()->route('admin.announcements.index')
                            ->with('success', 'Announcement deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Failed to delete announcement: ' . $e->getMessage());
        }
    }
    
    /**
     * Download announcement attachment
     *
     * @param  \App\Models\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function downloadAttachment(Announcement $announcement)
    {
        if (!$announcement->attachment || !Storage::exists($announcement->attachment)) {
            return redirect()->back()->with('error', 'Attachment not found.');
        }
        
        $filename = basename($announcement->attachment);
        return Storage::download($announcement->attachment, $filename);
    }
}