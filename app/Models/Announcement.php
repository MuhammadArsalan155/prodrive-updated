<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'created_by',
        'is_active',
        'attachment',
        'target_type', // 'all', 'role', 'user'
        'expires_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];
    
    protected $appends = ['attachment_url'];

    /**
     * Get the creator of the announcement
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Get the attachment URL
     */
    public function getAttachmentUrlAttribute()
    {
        return $this->attachment ? Storage::url($this->attachment) : null;
    }

    /**
     * The roles that are targeted by this announcement
     */
    public function targetRoles()
    {
        return $this->belongsToMany(Role::class, 'announcement_roles');
    }

    /**
     * The users that are targeted by this announcement
     */
    public function targetUsers()
    {
        return $this->belongsToMany(User::class, 'announcement_users');
    }
    
    /**
     * The students that are targeted by this announcement
     */
    public function targetStudents()
    {
        return $this->belongsToMany(Student::class, 'announcement_students');
    }
    
    /**
     * The instructors that are targeted by this announcement
     */
    public function targetInstructors()
    {
        return $this->belongsToMany(Instructor::class, 'announcement_instructors');
    }

    /**
     * Scope for active announcements
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>=', now());
                    });
    }
    
    /**
     * Scope for announcements visible to a specific user
     */
    public function scopeVisibleTo($query, $user)
    {
        $userClass = get_class($user);
        $userId = $user->id;
        
        return $query->where(function($q) use ($user, $userClass, $userId) {
            // Announcements for all users
            $q->where('target_type', 'all');
            
            // Announcements for specific roles
            if (method_exists($user, 'roles')) {
                $roleIds = $user->roles()->pluck('id')->toArray();
                if (!empty($roleIds)) {
                    $q->orWhereExists(function ($query) use ($roleIds) {
                        $query->select(DB::raw(1))
                              ->from('announcement_roles')
                              ->whereRaw('announcement_roles.announcement_id = announcements.id')
                              ->whereIn('announcement_roles.role_id', $roleIds);
                    });
                }
            }
            
            // Announcements for specific users
            if ($userClass == User::class) {
                $q->orWhereExists(function ($query) use ($userId) {
                    $query->select(DB::raw(1))
                          ->from('announcement_users')
                          ->whereRaw('announcement_users.announcement_id = announcements.id')
                          ->where('announcement_users.user_id', $userId);
                });
            } elseif ($userClass == Student::class) {
                $q->orWhereExists(function ($query) use ($userId) {
                    $query->select(DB::raw(1))
                          ->from('announcement_students')
                          ->whereRaw('announcement_students.announcement_id = announcements.id')
                          ->where('announcement_students.student_id', $userId);
                });
            } elseif ($userClass == Instructor::class) {
                $q->orWhereExists(function ($query) use ($userId) {
                    $query->select(DB::raw(1))
                          ->from('announcement_instructors')
                          ->whereRaw('announcement_instructors.announcement_id = announcements.id')
                          ->where('announcement_instructors.instructor_id', $userId);
                });
            }
        })->active();
    }
}