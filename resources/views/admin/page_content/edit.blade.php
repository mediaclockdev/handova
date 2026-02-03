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
              @include('partials.navbar')
            <div class="content-wrapper">
                <form action="{{ route('admin.page_content.update', $pagecontent->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @include('admin.page_content.form')

                    <div class="d-flex justify-content-between align-items-center">
                        <button type="reset" class="btn btn-clear-all">Clear All</button>
                        <button type="submit" class="btn btn-add-property">Update Contents</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('partials.scripts')
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</html>