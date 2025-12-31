@forelse($blogs as $blog)
    <tr>
        <td>{{ $blog->id }}</td>
        <td>
            @if($blog->image)
                <img src="{{ asset('storage/' . $blog->image) }}" alt="Img" width="50" class="rounded">
            @else
                <span class="badge bg-label-secondary">No Image</span>
            @endif
        </td>
        <td>{{ Str::limit($blog->title, 30) }}</td>
        <td>{{ $blog->category->name ?? 'Uncategorized' }}</td>
        <td>{{ $blog->author }}</td>
        <td>
            @if($blog->status == 'published')
                <span class="badge bg-label-success">Published</span>
            @else
                <span class="badge bg-label-warning">Draft</span>
            @endif
        </td>
        <td>{{ $blog->created_at->format('Y-m-d') }}</td>
        <td>
            <a href="{{ route('admin.blogs.edit', $blog->id) }}" class="btn btn-sm btn-info"><i
                    class='bx bx-edit-alt'></i></a>
            <button class="btn btn-sm btn-danger delete-blog" data-id="{{ $blog->id }}"><i class='bx bx-trash'></i></button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center">No blogs found.</td>
    </tr>
@endforelse