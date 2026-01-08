<!DOCTYPE html>
<html lang="en">

<head>
    <title>Dashboard</title>
    @include('partials.head')
</head>

<body>
    <div class="main-container">
        @include('partials.sidebar')
        <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

        <div class="main-content">
            <div class="content-wrapper">
                  @include('partials.navbar')

                <div class="properties-header d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">{{ $formTitle }}</h1>
                    <a href="{{ route('admin.page_content.create') }}" class="btn add-property-btn">Add
                        New</a>
                </div>

                <div class="card properties-table mb-4">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0" id="certificatesTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pages as $page)
                                <tr>
                                    <td>{{ $page->id }}</td>
                                    <td>{{ $page->title }}</td>
                                    <td>
                                        @if($page->type == 'terms_and_conditions')
                                        Terms & Conditions
                                        @else
                                        Privacy Policy
                                        @endif
                                    </td>
                                    <td>{{ $page->created_at->format('d M, Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.page_content.edit', $page->id) }}"
                                            class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('admin.page_content.destroy', $page->id) }}"
                                            method="POST" style="display:inline-block">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Delete this certificate?')">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No compliance certificates found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{ $pages->links() }}

            </div>
        </div>
    </div>
    @include('partials/scripts')
</body>
{{-- INIT CKEDITOR OR TINYMCE --}} <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script> <script> CKEDITOR.replace('description'); </script>

</html>