<!DOCTYPE html>
    <!--
    This is a starter template page. Use this page to start your new project from
    scratch. This page gets rid of all links and provides the needed markup only.
    -->
    <html ng-app="bsongs">
    <head>
        <meta charset="UTF-8">
        <title>Bestsongs.pk| Dashboard</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <!-- Bootstrap 3.3.2 -->
        <link href="{{ asset('/public/bower_components/admin-lte/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- Font Awesome Icons -->
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="http://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- notiny -->
        <link href="{{ asset('/public/angularjs-notify/angular-ui-notification.css')}}" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="{{ asset('/public/bower_components/admin-lte/dist/css/AdminLTE.min.css')}}" rel="stylesheet" type="text/css" />
        <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
              page. However, you can choose any other skin. Make sure you
              apply the skin class to the body tag so the changes take effect.
        -->
        <link href="{{ asset('/public/bower_components/admin-lte/dist/css/skins/skin-blue.min.css')}}" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
        <!-- jQuery 2.1.3 -->
        <script src="{{ asset ('/public/bower_components/admin-lte/plugins/jQuery/jQuery-2.2.3.min.js') }}"></script>
        
        <!-- Bootstrap 3.3.2 JS -->
        <script src="{{ asset ('/public/bower_components/admin-lte/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>

        <script>
            var baseUrl = '{{ config("app.url") }}';
        </script>
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.5/angular.min.js"></script>
        <script src="{{ url('/public/js/app.js')}}"></script>
        <!-- datatables -->
        <script type="text/javascript" src="https://cdn.datatables.net/t/bs-3.3.6/dt-1.10.11/datatables.min.js"></script>
        <!-- angularjs -->
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0-beta.2/angular-sanitize.js"></script>
        <!-- notiny -->
        <script src="{{ asset('/public/angularjs-notify/angular-ui-notification.js')}}"></script>
        
        <script src="{{ url('/public/js/fileuploader/ng-file-upload-shim.js')}}"></script>
        <script src="{{ url('/public/js/fileuploader/ng-file-upload.js')}}"></script>

        <script src="{{ asset ('/public/js/angular-datatables.min.js') }}"></script>
    </head>
    <body class="skin-blue {{{ (Auth::guest()) ? 'sidebar-collapse' : '' }}}"  >
    <div class="wrapper">

        <!-- Main Header -->
        <header class="main-header">

            <!-- Logo -->

            <a href="/" class="logo">Bestsongs.pk</a>

            <!-- Header Navbar -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle {{{ (Auth::guest()) ? 'hidden' : '' }}}" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                </a>
                <!-- Navbar Right Menu -->
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <!-- Messages: style can be found in dropdown.less-->
                        <li class="dropdown messages-menu hidden">
                            <!-- Menu toggle button -->
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-envelope-o"></i>
                                <span class="label label-success">4</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header">You have 4 messages</li>
                                <li>
                                    <!-- inner menu: contains the messages -->
                                    <ul class="menu">
                                        <li><!-- start message -->
                                            <a href="#">
                                                <div class="pull-left">
                                                    <!-- User Image -->
                                                    <img src="{{ asset('/public/bower_components/admin-lte/dist/img/Logo-bestsongs-NEW-web.jpg') }}" class="img-circle" alt="User Image"/>
                                                </div>
                                                <!-- Message title and timestamp -->
                                                <h4>
                                                    Support Team
                                                    <small><i class="fa fa-clock-o"></i> 5 mins</small>
                                                </h4>
                                                <!-- The message -->
                                                <p>Why not buy a new awesome theme?</p>
                                            </a>
                                        </li><!-- end message -->
                                    </ul><!-- /.menu -->
                                </li>
                                <li class="footer"><a href="#">See All Messages</a></li>
                            </ul>
                        </li><!-- /.messages-menu -->

                        <!-- Notifications Menu -->
                        <li class="dropdown notifications-menu hidden">
                            <!-- Menu toggle button -->
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-bell-o"></i>
                                <span class="label label-warning">10</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header">You have 10 notifications</li>
                                <li>
                                    <!-- Inner Menu: contains the notifications -->
                                    <ul class="menu">
                                        <li><!-- start notification -->
                                            <a href="#">
                                                <i class="fa fa-users text-aqua"></i> 5 new members joined today
                                            </a>
                                        </li><!-- end notification -->
                                    </ul>
                                </li>
                                <li class="footer"><a href="#">View all</a></li>
                            </ul>
                        </li>
                        <!-- Tasks Menu -->
                        <li class="dropdown tasks-menu hidden">
                            <!-- Menu Toggle Button -->
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-flag-o"></i>
                                <span class="label label-danger">9</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header">You have 9 tasks</li>
                                <li>
                                    <!-- Inner menu: contains the tasks -->
                                    <ul class="menu">
                                        <li><!-- Task item -->
                                            <a href="#">
                                                <!-- Task title and progress text -->
                                                <h3>
                                                    Design some buttons
                                                    <small class="pull-right">20%</small>
                                                </h3>
                                                <!-- The progress bar -->
                                                <div class="progress xs">
                                                    <!-- Change the css width attribute to simulate progress -->
                                                    <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                        <span class="sr-only">20% Complete</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li><!-- end task item -->
                                    </ul>
                                </li>
                                <li class="footer">
                                    <a href="#">View all tasks</a>
                                </li>
                            </ul>
                        </li>
                        <!-- User Account Menu -->
                        <li class="dropdown user user-menu hidden">
                            <!-- Menu Toggle Button -->
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <!-- The user image in the navbar-->

                                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                <span class="hidden-xs">Bestsongs</span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- The user image in the menu -->
                                <li class="user-header">
                                    <img src="{{ asset('/public/bower_components/admin-lte/dist/img/Logo-bestsongs-NEW-web.jpg') }}" class="img-circle" alt="User Image" />
                                    <p>
                                        Alexander Pierce - Web Developer
                                        <small>Member since Nov. 2012</small>
                                    </p>
                                </li>
                                <!-- Menu Body -->
                                <li class="user-body">
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Followers</a>
                                    </div>
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Sales</a>
                                    </div>
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Friends</a>
                                    </div>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a href="#" class="btn btn-default btn-flat">Profile</a>
                                    </div>
                                    <div class="pull-right">
                                        <a href="#" class="btn btn-default btn-flat">Sign out</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">

            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">

                <!-- Sidebar user panel (optional) -->
                @if (!Auth::guest())
                <div class="user-panel">
                    <div class="pull-left image">
                        <img src="{{ asset('/public/bower_components/admin-lte/dist/img/Logo-bestsongs-NEW-web.jpg') }}" class="img-circle" alt="User Image" />
                    </div>
                    <div class="pull-left info">
                        <p>{{ Auth::user()->name}}</p>
                        <!-- Status -->
                        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                    </div>
                </div>
                @endif
                <!-- search form (Optional) -->
                <form action="#" method="get" class="sidebar-form hidden">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="Search..."/>
          <span class="input-group-btn">
            <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
          </span>
                    </div>
                </form>
                <!-- /.search form -->
                <!-- Sidebar Menu -->
                <ul class="sidebar-menu">
                  @if (!Auth::guest())
                  
                    <li class="header"></li>
                    <li><a href="{{ url('/movie') }}">Upload Movie/Album</a></li>
                    <li><a href="{{ url('/tracks') }}"><span>Update Movie/Album</span></a></li>
                    <li><a href="{{ url('/video') }}"><span>Gupshup</span></a></li>
                    <li><a href="{{ url('/carousel') }}"><span>Carousel</span></a></li>
                    
                    <li><a href="{{ url('/mapping') }}"><span>Playlist</span></a></li>
                    <li><a href="{{ url('/tracks/bulk') }}"><span>Bulk</span></a></li>
                    <li class="treeview">
                        <a href="#"><span>Master</span> <i class="fa fa-angle-left pull-right"></i></a>
                        <ul class="treeview-menu">
                            <li><a href="{{ url('/artist') }}">Artists</a></li>
                            <li><a href="{{ url('/singer')}}">Singer</a></li>
                            <li><a href="{{ url('/genere') }}">Genere</a></li>
                            <li><a href="{{ url('/musicdirector') }}">Music Director</a></li>
                        </ul>
                    </li>
                    
                    
                      <li><a href="{{ url('/users') }}">Users</a></li>
                    
                    <li><a href="{{ url('/profile') }}"><span>Profile</span></a></li>
                    
                    <li><a href="https://bestsongs.pk/wp-cron-run.php?wp-insert" target="_blank"><span>Update - Insert</span></a></li>
                    <li><a href="https://bestsongs.pk/wp-cron-run.php?wp-update" target="_blank"><span>Update - Update</span></a></li>
                    
                    <li><a href="{{ url('/logout') }}"><span>Logout</span></a></li>
                   @endif 
                </ul><!-- /.sidebar-menu -->

            </section>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    {{{ isset($pageTitle) ? $pageTitle : "Dashboard" }}}
                    <!-- <small>Optional description</small> -->
                </h1>
                <ol class="breadcrumb hidden">
                    <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                    <li class="active">Here</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <!-- Your Page Content Here -->
                @yield('content')
            </section><!-- /.content -->
        </div><!-- /.content-wrapper -->

        <!-- Main Footer -->
        <footer class="main-footer">
            <!-- To the right -->
            <div class="pull-right hidden-xs">

            </div>
            <!-- Default to the left -->
            <strong>Copyright © 2016 <a href="https://bestsongs.pk">bestsongs</a>.</strong> All rights reserved.
        </footer>

    </div><!-- ./wrapper -->

    <!-- REQUIRED JS SCRIPTS -->

    <!-- AdminLTE App -->
    <script src="{{ asset ('/public/bower_components/admin-lte/dist/js/app.min.js') }}" type="text/javascript"></script>

    <!-- Optionally, you can add Slimscroll and FastClick plugins.
          Both of these plugins are recommended to enhance the
          user experience -->
    </body>
</html>
