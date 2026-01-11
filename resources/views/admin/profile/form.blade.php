<div class="properties-header d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ $formTitle }}</h1>
</div>

<div class="profile-upload-container text-center mb-4">
    <div class="profile-pic-wrapper">
        <img id="profilePreview"
            src="{{ $user->profile_picture
                ? asset('public/' . $user->profile_picture)
                : 'https://img.freepik.com/free-vector/user-circles-set_78370-4704.jpg?semt=ais_hybrid&w=740&q=80' }}"
            class="profile-pic" alt="Profile Picture">

        <label for="profile_picture" class="profile-edit-icon">
            <i class="bi bi-pencil-fill"></i>
        </label>

        <!-- Existing image path -->
        <input type="hidden" name="existing_profile_picture" value="{{ $user->profile_picture }}">

        <!-- Upload -->
        <input type="file" id="profile_picture" name="profile_picture" class="d-none" accept="image/*"
            onchange="loadProfilePreview(event)">
    </div>

    @error('profile_picture')
        <div class="text-danger small mt-2">{{ $message }}</div>
    @enderror
</div>


<div class="row g-4 mb-4">
    <div class="col-md-6">
        <label class="form-label">First Name</label>
        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
            value="{{ old('first_name', $user->first_name ?? '') }}">
        @error('first_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Last Name</label>
        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
            value="{{ old('last_name', $user->last_name ?? '') }}">
        @error('last_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <label class="form-label">Email Address</label>
        <!-- NOTE: use name="email" to match controller -->
        <input readonly type="email" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" name="email"
            class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email ?? '') }}">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Phone Number</label>
        <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '')" id="phone" name="phone"
            class="form-control @error('phone') is-invalid @enderror"
            value="{{ old('phone', $user->phone ?? ($user->phone ?? '')) }}">
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<script>
    function loadProfilePreview(event) {
        const img = document.getElementById('profilePreview');
        img.src = URL.createObjectURL(event.target.files[0]);
        img.onload = () => URL.revokeObjectURL(img.src);
    }
</script>
