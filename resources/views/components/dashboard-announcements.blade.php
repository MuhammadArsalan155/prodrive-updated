@php
// Check which guard is being used and get the appropriate user
if (Auth::guard('instructor')->check()) {
    $authUser = Auth::guard('instructor')->user();
    $announcements = \App\Models\Announcement::visibleTo($authUser)->orderBy('created_at', 'desc')->take(5)->get();
}elseif (Auth::guard('student')->check()) {
    $authUser = Auth::guard('student')->user();
    $announcements = \App\Models\Announcement::visibleTo($authUser)->orderBy('created_at', 'desc')->take(5)->get();
}
 elseif (Auth::check()) {
    $authUser = Auth::user();
    $announcements = \App\Models\Announcement::visibleTo($authUser)->orderBy('created_at', 'desc')->take(5)->get();
} else {
    $announcements = collect(); // Empty collection if no authenticated user
}
@endphp

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-bullhorn mr-2"></i>Announcements
        </h6>
    </div>
    <div class="card-body">
        @forelse($announcements as $announcement)
            <div class="alert alert-light border-left-info shadow-sm mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="w-100">
                        <strong>{{ $announcement->title }}</strong>
                        <div class="announcement-content mb-2 text-gray-800">
                            {!! $announcement->content !!}
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                {{ $announcement->created_at->format('M d, Y') }}
                            </small>
                            
                            @if($announcement->attachment)
                                <a href="{{ route('announcements.download', $announcement) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-paperclip mr-1"></i>
                                    {{ basename($announcement->attachment) }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-3">
                <i class="fas fa-comment-slash mr-2"></i>
                No current announcements
            </div>
        @endforelse
        
        @if($announcements->count() > 3)
            <div class="text-center mt-2">
                <button class="btn btn-sm btn-outline-primary" type="button" id="showMoreAnnouncements">
                    <span class="show-text">Show All Announcements</span>
                    <span class="hide-text d-none">Show Less</span>
                </button>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .announcement-content {
        max-height: 100px;
        overflow-y: auto;
    }
    
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    
    .alert-light {
        background-color: #fdfdfe;
    }
    
    /* Initially hide announcements beyond the first 3 */
    .card-body .alert:nth-child(n+4) {
        display: none;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $("#showMoreAnnouncements").click(function() {
            $(".card-body .alert:nth-child(n+4)").toggle();
            $(this).find('.show-text, .hide-text').toggleClass('d-none');
        });
    });
</script>
@endpush