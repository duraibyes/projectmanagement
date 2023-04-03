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
      
    </script>
    <!-- Modal -->
    <div class="modal modal-right fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Task Notification</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" >
                    <div class="row notifications" id="notification_content">                       
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="commonModal" tabindex="-1" aria-labelledby="commonExampleModalLabel" aria-hidden="true">
    </div>
    <script src="//js.pusher.com/3.1/pusher.min.js"></script>
    <script type="text/javascript">
        var notificationsWrapper   = $('#notification_content');
        var notifications          = notificationsWrapper
  
  
        var pusher = new Pusher("615a420aafd39e15eddc", {
          encrypted: true,
          cluster: 'ap2'
        });
  
        // Subscribe to the channel we specified in our Laravel Event
        var channel = pusher.subscribe('task-status');
  
        // Bind a function to a Event (the full Laravel class)
        var notificationsCount = 0;
        channel.bind('App\\Events\\MessageSent', function(data) {
          var existingNotifications = notifications.html();
          var newNotificationHtml = `<div class="col-sm-12">
                            <div class="noti">
                                <label for="">
                                    ${data.message}
                                </label>
                            </div>
                        </div>
          `;
          notifications.html(newNotificationHtml + existingNotifications);
  
          notificationsCount += 1;
          $('#noti_count').show();
          $('#noti_count').html(notificationsCount);
     
        });

      </script>
</body>

</html>
