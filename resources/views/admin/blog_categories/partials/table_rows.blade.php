@forelse($categories as $category)
    <tr>
        <td>{{ $category->id }}</td>
        <td>{{ $category->name }}</td>
        <td>{{ $category->slug }}</td>
        <td>
            @if($category->status == 'active')
                <span class="badge bg-label-success">Active</span>
            @else
                <span class="badge bg-label-secondary">Inactive</span>
            @endif
        </td>
        <td>{{ $category->created_at->format('Y-m-d') }}</td>
        <td>
            <button class="btn btn-sm btn-info edit-category" data-id="{{ $category->id }}"><i
                    class='bx bx-edit-alt'></i></button>
            <button class="btn btn-sm btn-danger delete-category" data-id="{{ $category->id }}"><i
                    class='bx bx-trash'></i></button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center">No categories found.</td>
    </tr>
@endforelse