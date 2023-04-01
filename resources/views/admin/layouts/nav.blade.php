<nav class="navbar navbar-expand main-header bg-primary px-3">
    <ul class="navbar list-unstyled my-0">
        <li class="nav-item" id="list-hamburger">
            <i class="bi bi-list h2 text-white"></i>
        </li>
    </ul>
    <ul class="navbar-nav ms-auto">
        <li class="nav-item profile-dropdown position-relative">
            <a href="javascript:void(0)" class="nav-link p-3">
                <i class="bi bi-person-circle"></i>
            </a>
            <ul class="profile-dropdown-menu">
                <li class="nav-item">
                    <a class="dropdown-item" href="#">Change Password</a>
                </li>
                <li class="nav-item">

                    <a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                                  document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>

                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link p-3" data-bs-toggle="modal" data-bs-target="#exampleModal">
                <i class="bi bi-bell"></i>
            </a>
        </li>
    </ul>
</nav>
