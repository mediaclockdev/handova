 <div class="header">
     <div class="d-flex align-items-center w-100">
         <!-- Hamburger Menu Button for mobile -->
         <button class="btn btn-outline-secondary d-md-none me-3" id="sidebar-toggle-btn">
             <i class="bi bi-list"></i>
         </button>

         <!-- Smaller Searchbar -->
         {{-- <form class="d-flex me-3 search-form">
             <div class="input-group">
                 <input type="text" class="form-control" placeholder="Search..." aria-label="Search">
                 <span class="input-group-text"><i class="bi bi-search"></i></span>
             </div>
         </form> --}}
     </div>

     <div class="d-flex align-items-center flex-shrink-0">
         <!-- Notification Dropdown -->
         {{-- <div class="dropdown notifications-dropdown">
             <a href="#" class="icon-btn dropdown-toggle" role="button" data-bs-toggle="dropdown"
                 aria-expanded="false">
                 <i class="bi bi-bell"></i>
                 <span class="badge rounded-pill bg-danger">3</span>
             </a>
             <ul class="dropdown-menu dropdown-menu-end">
                 <li>
                     <h6 class="dropdown-header">New Notifications</h6>
                 </li>
                 <li><a class="dropdown-item" href="#">
                         <i class="bi bi-info-circle"></i> You have a new issue report
                     </a></li>
                 <li><a class="dropdown-item" href="#">
                         <i class="bi bi-check-circle"></i> A service request has been resolved
                     </a></li>
                 <li>
                     <hr class="dropdown-divider">
                 </li>
                 <li><a class="dropdown-item text-center" href="#">View all notifications</a></li>
             </ul>
         </div> --}}

         <!-- Profile Dropdown -->
         <div class="dropdown ms-3">
             <div class="profile-info d-none d-sm-flex dropdown-toggle" role="button" data-bs-toggle="dropdown"
                 aria-expanded="false">

                 <img class="profile-pic"
                     src="{{ auth()->user()->profile_picture
                         ? asset(auth()->user()->profile_picture)
                         : 'https://img.freepik.com/free-vector/user-circles-set_78370-4704.jpg?semt=ais_hybrid&w=740&q=80' }}"
                     alt="{{ auth()->user()->name }}">

                 <div>
                     <div class="font-weight-bold">{{ auth()->user()->name }}</div>
                     <div class="text-sm">ID: {{ auth()->user()->id }}</div>
                 </div>
             </div>
             <ul class="dropdown-menu dropdown-menu-end">
                 <li>
                     <a class="dropdown-item" href="{{ url('admin/profile') }}">
                         <i class="bi bi-person-circle"></i> My Profile
                     </a>
                 </li>
                 <li>
                     <hr class="dropdown-divider">
                 </li>
                 <li>
    <form action="{{ route('logout') }}" method="POST" id="logout-form">
        @csrf
        <button type="submit" class="dropdown-item" id="logout-btns">
            <i class="bi bi-box-arrow-right"></i> Logout
        </button>
    </form>
</li>



             </ul>
         </div>
     </div>
 </div>
