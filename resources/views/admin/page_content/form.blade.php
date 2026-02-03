{{-- resources/views/pagecontents/form.blade.php --}}

<div class="properties-header d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ $formTitle ?? 'Compliance Certificate' }}</h1>
</div>

<div class="row g-4 mb-4">
    <div class="mb-3 col-12">
        <label for="title" class="form-label">Title</label>
        <input
            type="text"
            name="title"
            id="title"
            class="form-control"
            value="{{ old('title', $pagecontent->title ?? '') }}"
            required>
    </div>

    <div class="mb-3 col-12">
        <label for="type" class="form-label">Page Type</label>
        <select name="type" id="type" class="form-select" required>
            <option value="">Select Page Type</option>
            <option value="terms_and_conditions"
                {{ old('type', $pagecontent->type ?? '') == 'terms_and_conditions' ? 'selected' : '' }}>
                Terms & Conditions
            </option>
            <option value="privacy_policy"
                {{ old('type', $pagecontent->type ?? '') == 'privacy_policy' ? 'selected' : '' }}>
                Privacy Policy
            </option>
        </select>
    </div>

    <div class="mb-3 col-12">
        <div class="mb-3 ">
            <label for="description" class="form-label">Description</label>
            <textarea
                name="description"
                id="description"
                class="form-control editor"
                rows="6"
                required>{{ old('description', $pagecontent->description ?? '') }}</textarea>
        </div>
    </div>

    {{-- INIT CKEDITOR OR TINYMCE --}}
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('description');
    </script>