<a class="proj-card" href="{{ route('projects.show', $project) }}" data-tags="{{ $project->tags }}">
    <div class="ph"><img src="{{ $project->cover_url }}" alt="{{ $project->name }} — {{ $project->location }}" loading="lazy" decoding="async"></div>
    <div class="meta"><div><h3>{{ $project->name }}</h3><span class="loc"><i class="fa-solid fa-location-dot"></i>{{ $project->location }}</span></div><span class="cat">{{ $project->category }}</span></div>
</a>
