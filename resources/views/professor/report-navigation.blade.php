@foreach([
    ['/professor/home', 'fa-home', 'Dashboard'],
    ['/professor/class', 'fa-clipboard', 'Class'],
    [route('professor.requirementStatus.classes'), 'fa-clipboard-check', 'Requirement Status'],
    [route('professor.completion-certificates.index'), 'fa-certificate', 'COC Validation'],
    ['/professor/analytics', 'fa-chart-line', 'Analytics'],
    ['/reportsExpiredProf', 'fa-file-contract', 'MOA'],
] as [$navigationUrl, $navigationIcon, $navigationLabel])
    <a href="{{ url($navigationUrl) }}" class="nav-item">
        <span class="nav-icon"><i class="fa {{ $navigationIcon }}"></i></span>
        <span class="nav-label">{{ $navigationLabel }}</span>
        <span class="tooltip-label">{{ $navigationLabel }}</span>
    </a>
@endforeach
@include('partials.professor-student-report-link')
@foreach([
    ['/professor/maintain', 'fa-cogs', 'Maintenance'],
    ['/professor/evaluation', 'fa-star-half-alt', 'Evaluation'],
] as [$navigationUrl, $navigationIcon, $navigationLabel])
    <a href="{{ url($navigationUrl) }}" class="nav-item">
        <span class="nav-icon"><i class="fa {{ $navigationIcon }}"></i></span>
        <span class="nav-label">{{ $navigationLabel }}</span>
        <span class="tooltip-label">{{ $navigationLabel }}</span>
    </a>
@endforeach
