<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    /**
     * Get announcements for the current user
     * 
     * @return \Illuminate\Http\Response
     */
    public function getAnnouncements()
    {
        $user = Auth::user();
        $announcements = Announcement::visibleTo($user)
                                    ->orderBy('created_at', 'desc')
                                    ->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $announcements
        ]);
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
            return response()->json([
                'status' => 'error',
                'message' => 'Attachment not found.'
            ], 404);
        }
        
        $filename = basename($announcement->attachment);
        return Storage::download($announcement->attachment, $filename);
    }
}