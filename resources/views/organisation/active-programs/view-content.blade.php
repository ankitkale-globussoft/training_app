@extends('layouts.master', ['panel' => 'organisation'])
@section('title', 'Program Content')

@section('content')
    <div class="container-xxl container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">
                    <span class="text-muted fw-light">Active Programs /</span> {{ $booking->requirement->program->title }}
                </h4>
                <p class="text-muted mb-0">
                    <i class="bx bx-user me-1"></i> Trainer: {{ $booking->trainer->name }}
                </p>
            </div>
            <a href="{{ route('org.active-programs.index') }}" class="btn btn-label-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back
            </a>
        </div>

        <div class="nav-align-top mb-4">
            <ul class="nav nav-tabs nav-fill" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-video" aria-controls="navs-video" aria-selected="true">
                        <i class="bx bx-video me-1"></i> Video
                        <span
                            class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-primary ms-1">{{ $counts['video'] }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pdf"
                        aria-controls="navs-pdf" aria-selected="false">
                        <i class="bx bxs-file-pdf me-1"></i> PDF
                        <span
                            class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-danger ms-1">{{ $counts['pdf'] }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-text"
                        aria-controls="navs-text" aria-selected="false">
                        <i class="bx bx-file me-1"></i> Text
                        <span
                            class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-info ms-1">{{ $counts['text'] }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-link"
                        aria-controls="navs-link" aria-selected="false">
                        <i class="bx bx-link me-1"></i> Links
                        <span
                            class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-success ms-1">{{ $counts['link'] }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-meeting"
                        aria-controls="navs-meeting" aria-selected="false">
                        <i class="bx bx-video-recording me-1"></i> Meetings
                        <span
                            class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-warning ms-1">{{ $counts['meeting'] }}</span>
                    </button>
                </li>
            </ul>
            <div class="tab-content">
                <!-- Video Logic -->
                <div class="tab-pane fade show active" id="navs-video" role="tabpanel">
                    @if($groupedContent['video']->isEmpty())
                        <p class="text-center text-muted my-4">No video content added.</p>
                    @else
                        <div class="row g-4">
                            @foreach($groupedContent['video'] as $content)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $content->title }}</h5>
                                            <p class="card-text text-muted small">{{ Str::limit($content->description, 100) }}</p>
                                            <a href="{{ $content->file_url }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bx bx-play me-1"></i> Watch Video
                                            </a>
                                        </div>
                                        <div class="card-footer bg-label-secondary py-2">
                                            <small class="text-muted">Added {{ $content->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- PDF Logic -->
                <div class="tab-pane fade" id="navs-pdf" role="tabpanel">
                    @if($groupedContent['pdf']->isEmpty())
                        <p class="text-center text-muted my-4">No PDF documents added.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($groupedContent['pdf'] as $content)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $content->title }}</h6>
                                        <small class="text-muted">{{ Str::limit($content->description, 50) }}</small>
                                    </div>
                                    <a href="{{ $content->file_url }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                        <i class="bx bxs-file-pdf me-1"></i> Download/View
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <!-- Text Logic -->
                <div class="tab-pane fade" id="navs-text" role="tabpanel">
                    @if($groupedContent['text']->isEmpty())
                        <p class="text-center text-muted my-4">No text content added.</p>
                    @else
                        <div class="accordion" id="accordionText">
                            @foreach($groupedContent['text'] as $index => $content)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $index }}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse{{ $index }}" aria-expanded="false"
                                            aria-controls="collapse{{ $index }}">
                                            {{ $content->title }} <small
                                                class="text-muted ms-2 ps-2 border-start">{{ $content->created_at->format('d M Y') }}</small>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $index }}" class="accordion-collapse collapse"
                                        aria-labelledby="heading{{ $index }}" data-bs-parent="#accordionText">
                                        <div class="accordion-body">
                                            @if($content->description)
                                                <p class="fw-semibold text-muted">{{ $content->description }}</p>
                                            @endif
                                            <div class="text-content-body">
                                                {!! nl2br(e($content->text_content)) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Link Logic -->
                <div class="tab-pane fade" id="navs-link" role="tabpanel">
                    @if($groupedContent['link']->isEmpty())
                        <p class="text-center text-muted my-4">No external links added.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($groupedContent['link'] as $content)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $content->title }}</h6>
                                        <small class="text-muted">{{ $content->description }}</small>
                                        <br>
                                        <a href="{{ $content->external_url }}" target="_blank"
                                            class="small">{{ Str::limit($content->external_url, 60) }}</a>
                                    </div>
                                    <a href="{{ $content->external_url }}" target="_blank" class="btn btn-icon btn-outline-success">
                                        <i class="bx bx-link-external"></i>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <!-- Meeting Logic -->
                <div class="tab-pane fade" id="navs-meeting" role="tabpanel">
                    @if($groupedContent['meeting']->isEmpty())
                        <p class="text-center text-muted my-4">No meetings scheduled.</p>
                    @else
                        <div class="row g-4">
                            @foreach($groupedContent['meeting'] as $content)
                                <div class="col-md-6">
                                    <div class="card bg-label-warning text-dark">
                                        <div class="card-body">
                                            <h5 class="card-title text-dark">{{ $content->title }}</h5>
                                            <p class="card-text small">{{ $content->description }}</p>
                                            <a href="{{ $content->external_url }}" target="_blank" class="btn btn-dark btn-sm">Join
                                                Meeting</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection