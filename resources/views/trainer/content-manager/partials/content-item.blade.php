<div class="col-12 mb-3">
    <div class="card border shadow-none">
        <div class="card-body p-3">
            <div class="d-flex align-items-start">
                <div class="avatar avatar-md me-3">
                    <span class="avatar-initial rounded {{ $content->type_badge }}">
                        <i class="bx {{ $content->type_icon }}"></i>
                    </span>
                </div>
                <div class="d-flex flex-column flex-grow-1 overflow-hidden">
                    <h6 class="mb-1 text-truncate">{{ $content->title }}</h6>
                    <small class="text-muted mb-2">
                        {{ $content->created_at->format('d M, Y h:i A') }}
                        @if($content->module)
                            • <span class="badge bg-label-secondary">{{ $content->module->title }}</span>
                        @endif
                    </small>
                    <p class="mb-2 text-truncate" style="max-width: 90%;">{{ Str::limit($content->description, 120) }}
                    </p>

                    <div class="d-flex gap-2">
                        @if($content->is_visible_to_org)
                            <span class="badge bg-label-primary" title="Visible to Organization" style="font-size: 0.7rem;">
                                <i class="bx bx-building me-1"></i> Org
                            </span>
                        @endif
                        @if($content->is_visible_to_candidates)
                            <span class="badge bg-label-info" title="Visible to Candidates" style="font-size: 0.7rem;">
                                <i class="bx bx-group me-1"></i> Student
                            </span>
                        @endif

                        <!-- View Logic -->
                        @if($content->content_type == 'link' || $content->content_type == 'meeting')
                            <a href="{{ $content->external_url }}" target="_blank"
                                class="btn btn-xs btn-label-secondary ms-2">
                                <i class="bx bx-link-external"></i> Open
                            </a>
                        @elseif($content->file_path)
                            <a href="{{ $content->file_url }}" target="_blank" class="btn btn-xs btn-label-secondary ms-2">
                                <i class="bx bx-download"></i> View
                            </a>
                        @endif
                    </div>
                </div>
                <div class="dropdown">
                    <button class="btn p-0" type="button" id="contentAction{{ $content->content_id }}"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end"
                        aria-labelledby="contentAction{{ $content->content_id }}">
                        <a class="dropdown-item"
                            href="{{ url('trainer/content-manager/content/' . $content->content_id . '/edit') }}">
                            <i class="bx bx-edit-alt me-1"></i> Edit
                        </a>
                        <a class="dropdown-item text-danger delete-content" href="javascript:void(0);"
                            data-id="{{ $content->content_id }}">
                            <i class="bx bx-trash me-1"></i> Delete
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>