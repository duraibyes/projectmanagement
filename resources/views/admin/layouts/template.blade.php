<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @include('admin.layouts.common.style')
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body class="h-100" id="body-main">
    <div id="app">
        <main>
            <div class="wrapper">
                @include('admin.layouts.nav')
                @include('admin.layouts.sidemenu')
            </div>
            <section class="content" id="body_section">
                @yield('content')
            </section>
        </main>
    </div>

    @include('admin.layouts.common.script')
    @yield('add_on_script')

    <script>
        $(document).ready(function() {
            $('#example').DataTable();
        });
        var isMainMenuOpen = true;
        var hamburger = document.getElementById('list-hamburger');
        var sidemenuClose = document.getElementById('sidemenu-close');

        openOrCloseMenu = () => {
            document.getElementById('body-main').classList.toggle('sidebar-collapse')
            document.getElementById('body_section').classList.toggle('collapse-size');
        }

        hamburger.addEventListener('click', openOrCloseMenu)
        sidemenuClose.addEventListener('click', openOrCloseMenu)


        function changeStatus() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire(
                        'Deleted!',
                        'Your file has been deleted.',
                        'success'
                    )
                }
            })
        }
    </script>
    <!-- Modal -->
    <div class="modal modal-right fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
