@foreach(($navAnnouncements ?? collect()) as $announcement)
    <div class="alert alert-info d-flex gap-2 align-items-start" role="alert">
        <i class="bi bi-megaphone mt-1"></i>
        <div>
            <strong>{{ $announcement->title }}</strong>
            <div class="small">{{ $announcement->body }}</div>
        </div>
    </div>
@endforeach
