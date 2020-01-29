<!DOCTYPE html>
<html lang="en" ng-app="bsongs">
    <head>
        <title>Dashboard</title>

        <!-- BEGIN META -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="keywords" content="your,keywords">
        <meta name="description" content="Short explanation about this website">
        <!-- END META -->

        <!-- BEGIN STYLESHEETS -->
        <link href='http://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
        <link type="text/css" rel="stylesheet" href="{{ url('/public/assets/css/theme-default/bootstrap.css?1422792965')}}" />
        <link type="text/css" rel="stylesheet" href="{{ url('/public/assets/css/theme-default/font-awesome.min.css?1422529194')}}" />
        <link type="text/css" rel="stylesheet" href="{{ url('/public/assets/css/theme-default/material-design-iconic-font.min.css?1421434286')}}" />
        <link type="text/css" rel="stylesheet" href="{{ url('/public/assets/css/theme-default/libs/rickshaw/rickshaw.css?1422792967')}}" />
        <link type="text/css" rel="stylesheet" href="{{ url('/public/assets/css/theme-default/libs/morris/morris.core.css?1420463396')}}" />
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/t/bs-3.3.6/dt-1.10.11/datatables.min.css"/>
        <link type="text/css" rel="stylesheet" href="{{ url('/public/assets/css/theme-default/materialadmin.css?1425466319')}}" />
        <script>
            var baseUrl = '{{ config("app.url") }}';
        </script>

        <!-- END STYLESHEETS -->
        <script src="{{ url('/public/assets/js/libs/jquery/jquery-1.11.2.min.js')}}"></script>
        <script src="{{ url('/public/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js')}}"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.5/angular.min.js"></script>
        <script src="{{ url('/public/js/app.js')}}"></script>
        <script src="{{ url('/public/assets/js/libs/bootstrap/bootstrap.min.js')}}"></script>
        <!-- datatables -->
        <script type="text/javascript" src="https://cdn.datatables.net/t/bs-3.3.6/dt-1.10.11/datatables.min.js"></script>
        <!-- angularjs -->
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0-beta.2/angular-sanitize.js"></script>
        <script src="http://hybrid.netsolonline.com/public/js/angular-datatables.min.js"></script>
        <script src="https://angular-file-upload.appspot.com/js/ng-file-upload-shim.js"></script>
        <script src="https://angular-file-upload.appspot.com/js/ng-file-upload.js"></script>

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script type="text/javascript" src="../../assets/js/libs/utils/html5shiv.js?1403934957"></script>
        <script type="text/javascript" src="../../assets/js/libs/utils/respond.min.js?1403934956"></script>
        <![endif]-->
    </head>
    <body class="menubar-hoverable header-fixed ">

        <!-- BEGIN HEADER-->
        <header id="header" >
            <div class="headerbar">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="headerbar-left">
                    <ul class="header-nav header-nav-options">
                        <li class="header-nav-brand" >
                            <div class="brand-holder">
                                <a href="{{ url('/')}}">
                                    <span class="text-lg text-bold text-primary">Dashboard</span>
                                </a>
                            </div>
                        </li>
                        @if (!Auth::guest())
                        <li>
                            <a class="btn btn-icon-toggle menubar-toggle" data-toggle="menubar" href="javascript:void(0);">
                                <i class="fa fa-bars"></i>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
                <!-- Collect the nav links, forms, and other content for toggling -->
                @if (!Auth::guest())
                <div class="headerbar-right">
                    <ul class="header-nav header-nav-options">
                        <li>
                            <!-- Search form -->
                            <form class="navbar-search" role="search">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="headerSearch" placeholder="Enter your keyword">
                                </div>
                                <button type="submit" class="btn btn-icon-toggle ink-reaction"><i class="fa fa-search"></i></button>
                            </form>
                        </li>
                        <li class="dropdown hidden-xs">
                            <a href="javascript:void(0);" class="btn btn-icon-toggle btn-default" data-toggle="dropdown">
                                <i class="fa fa-bell"></i><sup class="badge style-danger">4</sup>
                            </a>
                            <ul class="dropdown-menu animation-expand">
                                <li class="dropdown-header">Today's messages</li>
                                <li>
                                    <a class="alert alert-callout alert-warning" href="javascript:void(0);">
                                        <img class="pull-right img-circle dropdown-avatar" src="../../assets/img/avatar2.jpg?1404026449" alt="" />
                                        <strong>Alex Anistor</strong><br/>
                                        <small>Testing functionality...</small>
                                    </a>
                                </li>
                                <li>
                                    <a class="alert alert-callout alert-info" href="javascript:void(0);">
                                        <img class="pull-right img-circle dropdown-avatar" src="../../assets/img/avatar3.jpg?1404026799" alt="" />
                                        <strong>Alicia Adell</strong><br/>
                                        <small>Reviewing last changes...</small>
                                    </a>
                                </li>
                                <li class="dropdown-header">Options</li>
                                <li><a href="../../html/pages/login.html">View all messages <span class="pull-right"><i class="fa fa-arrow-right"></i></span></a></li>
                                <li><a href="../../html/pages/login.html">Mark as read <span class="pull-right"><i class="fa fa-arrow-right"></i></span></a></li>
                            </ul><!--end .dropdown-menu -->
                        </li><!--end .dropdown -->
                        <li class="dropdown hidden-xs">
                            <a href="javascript:void(0);" class="btn btn-icon-toggle btn-default" data-toggle="dropdown">
                                <i class="fa fa-area-chart"></i>
                            </a>
                            <ul class="dropdown-menu animation-expand">
                                <li class="dropdown-header">Server load</li>
                                <li class="dropdown-progress">
                                    <a href="javascript:void(0);">
                                        <div class="dropdown-label">
                                            <span class="text-light">Server load <strong>Today</strong></span>
                                            <strong class="pull-right">93%</strong>
                                        </div>
                                        <div class="progress"><div class="progress-bar progress-bar-danger" style="width: 93%"></div></div>
                                    </a>
                                </li><!--end .dropdown-progress -->
                                <li class="dropdown-progress">
                                    <a href="javascript:void(0);">
                                        <div class="dropdown-label">
                                            <span class="text-light">Server load <strong>Yesterday</strong></span>
                                            <strong class="pull-right">30%</strong>
                                        </div>
                                        <div class="progress"><div class="progress-bar progress-bar-success" style="width: 30%"></div></div>
                                    </a>
                                </li><!--end .dropdown-progress -->
                                <li class="dropdown-progress">
                                    <a href="javascript:void(0);">
                                        <div class="dropdown-label">
                                            <span class="text-light">Server load <strong>Lastweek</strong></span>
                                            <strong class="pull-right">74%</strong>
                                        </div>
                                        <div class="progress"><div class="progress-bar progress-bar-warning" style="width: 74%"></div></div>
                                    </a>
                                </li><!--end .dropdown-progress -->
                            </ul><!--end .dropdown-menu -->
                        </li><!--end .dropdown -->
                    </ul><!--end .header-nav-options -->
                    <ul class="header-nav header-nav-profile">
                        <li class="dropdown">
                            <a class="dropdown-toggle ink-reaction" data-toggle="dropdown">
                                <img src="{{ url('/public/assets/img/avatar1.jpg?1403934956')}}" alt="" />
                                <span class="profile-info">
                                    {{ Auth::user()->name }}
                            <!-- <small>Administrator</small> -->
                                </span>
                            </a>
                            <ul class="dropdown-menu animation-dock">
                                <li class="dropdown-header">Config</li>
                                <li><a href="#">My profile</a></li>
                                <li><a href="#">My blog <span class="badge style-danger pull-right">16</span></a></li>
                                <li><a href="#">My appointments</a></li>
                                <li class="divider"></li>
                                <li><a href="#"><i class="fa fa-fw fa-lock"></i> Lock</a></li>
                                <li><a href="{{ url('/logout')}}"><i class="fa fa-fw fa-power-off text-danger"></i> Logout</a></li>
                            </ul><!--end .dropdown-menu -->
                        </li><!--end .dropdown -->
                    </ul><!--end .header-nav-profile -->
                    <ul class="header-nav header-nav-toggle">
                        <li>
                            <a class="btn btn-icon-toggle btn-default" href="#offcanvas-search" data-toggle="offcanvas" data-backdrop="false">
                                <i class="fa fa-ellipsis-v"></i>
                            </a>
                        </li>
                    </ul><!--end .header-nav-toggle -->
                </div><!--end #header-navbar-collapse -->
                @endif
            </div>
        </header>
        <!-- END HEADER-->

        <!-- BEGIN BASE-->
        <div id="base">

            <!-- BEGIN OFFCANVAS LEFT -->
            <div class="offcanvas">
            </div><!--end .offcanvas-->
            <!-- END OFFCANVAS LEFT -->

            <!-- BEGIN CONTENT-->
            <div id="content" style="padding-top:100px;">
                @yield('content')
            </div><!--end #content-->
            <!-- END CONTENT -->
            @if (!Auth::guest())
            <!-- BEGIN MENUBAR-->
            <div id="menubar" class="menubar-inverse ">
                <div class="menubar-fixed-panel">
                    <div>
                        <a class="btn btn-icon-toggle btn-default menubar-toggle" data-toggle="menubar" href="javascript:void(0);">
                            <i class="fa fa-bars"></i>
                        </a>
                    </div>
                    <!-- <div class="expanded">
                            <a href="../../html/dashboards/dashboard.html">
                                    <span class="text-lg text-bold text-primary ">MATERIAL&nbsp;ADMIN</span>
                            </a>
                    </div> -->
                </div>
                <div class="menubar-scroll-panel">

                    <!-- BEGIN MAIN MENU -->
                    <ul id="main-menu" class="gui-controls">

                        <!-- BEGIN DASHBOARD -->
                        <li>
                            <a href="{{ url('/')}}" class="active">
                                <div class="gui-icon"><i class="md md-home"></i></div>
                                <span class="title">Dashboard</span>
                            </a>
                        </li><!--end /menu-li -->
                        <!-- END DASHBOARD -->
                        <li>
                            <a class="dropmenu" href="#">
                                <div class="gui-icon"><i class="md md-web"></i></div>
                                <span class="title">Master</span>
                            </a>
                            <ul>
                                <li><a class="submenu" href="{{ url('/artist')}}"><i class="icon-file-alt"></i><span class="title"> Artist</span></a></li>
                                <li><a class="submenu" href="{{ url('/singer')}}"><i class="icon-file-alt"></i><span class="title"> Singer</span></a></li>
                                <li><a class="submenu" href="{{ url('/musicdirector') }}"><i class="icon-file-alt"></i><span class="title"> Music Director</span></a></li>
                                <li><a class="submenu" href="{{ url('/movie') }}"><i class="icon-file-alt"></i><span class="title"> Movie</span></a></li>
                                <li><a class="submenu" href="{{ url('/genere') }}"><i class="icon-file-alt"></i><span class="title"> Genere</span></a></li>
                            </ul>
                        </li>
                        <!-- BEGIN DASHBOARD -->
                        <li>
                            <a href="{{ url('/tracks')}}" >
                                <div class="gui-icon"><i class="md md-web"></i></div>
                                <span class="title">Tracks</span>
                            </a>
                        </li><!--end /menu-li -->
                        <!-- END DASHBOARD -->

                        <!-- BEGIN EMAIL -->
                        <li class="gui-folder hidden">
                            <a>
                                <div class="gui-icon"><i class="md md-email"></i></div>
                                <span class="title">Email</span>
                            </a>
                            <!--start submenu -->
                            <ul>
                                <li><a href="../../html/mail/inbox.html" ><span class="title">Inbox</span></a></li>
                                <li><a href="../../html/mail/compose.html" ><span class="title">Compose</span></a></li>
                                <li><a href="../../html/mail/reply.html" ><span class="title">Reply</span></a></li>
                                <li><a href="../../html/mail/message.html" ><span class="title">View message</span></a></li>
                            </ul><!--end /submenu -->
                        </li><!--end /menu-li -->
                        <!-- END EMAIL -->

                        <!-- BEGIN DASHBOARD -->
                        <li class="hidden">
                            <a href="../../html/layouts/builder.html" >
                                <div class="gui-icon"><i class="md md-web"></i></div>
                                <span class="title">Layouts</span>
                            </a>
                        </li><!--end /menu-li -->
                        <!-- END DASHBOARD -->

                        <!-- BEGIN UI -->
                        <li class="gui-folder hidden">
                            <a>
                                <div class="gui-icon"><i class="fa fa-puzzle-piece fa-fw"></i></div>
                                <span class="title">UI elements</span>
                            </a>
                            <!--start submenu -->
                            <ul>
                                <li><a href="../../html/ui/colors.html" ><span class="title">Colors</span></a></li>
                                <li><a href="../../html/ui/typography.html" ><span class="title">Typography</span></a></li>
                                <li><a href="../../html/ui/cards.html" ><span class="title">Cards</span></a></li>
                                <li><a href="../../html/ui/buttons.html" ><span class="title">Buttons</span></a></li>
                                <li><a href="../../html/ui/lists.html" ><span class="title">Lists</span></a></li>
                                <li><a href="../../html/ui/tabs.html" ><span class="title">Tabs</span></a></li>
                                <li><a href="../../html/ui/accordions.html" ><span class="title">Accordions</span></a></li>
                                <li><a href="../../html/ui/messages.html" ><span class="title">Messages</span></a></li>
                                <li><a href="../../html/ui/offcanvas.html" ><span class="title">Off-canvas</span></a></li>
                                <li><a href="../../html/ui/grid.html" ><span class="title">Grid</span></a></li>
                                <li class="gui-folder">
                                    <a href="javascript:void(0);">
                                        <span class="title">Icons</span>
                                    </a>
                                    <!--start submenu -->
                                    <ul>
                                        <li><a href="../../html/ui/icons/materialicons.html" ><span class="title">Material Design Icons</span></a></li>
                                        <li><a href="../../html/ui/icons/fontawesome.html" ><span class="title">Font Awesome</span></a></li>
                                        <li><a href="../../html/ui/icons/glyphicons.html" ><span class="title">Glyphicons</span></a></li>
                                        <li><a href="../../html/ui/icons/skycons.html" ><span class="title">Skycons</span></a></li>
                                    </ul><!--end /submenu -->
                                </li><!--end /menu-li -->
                            </ul><!--end /submenu -->
                        </li><!--end /menu-li -->
                        <!-- END UI -->

                        <!-- BEGIN TABLES -->
                        <li class="gui-folder hidden">
                            <a>
                                <div class="gui-icon"><i class="fa fa-table"></i></div>
                                <span class="title">Tables</span>
                            </a>
                            <!--start submenu -->
                            <ul>
                                <li><a href="../../html/tables/static.html" ><span class="title">Static Tables</span></a></li>
                                <li><a href="../../html/tables/dynamic.html" ><span class="title">Dynamic Tables</span></a></li>
                                <li><a href="../../html/tables/responsive.html" ><span class="title">Responsive Table</span></a></li>
                            </ul><!--end /submenu -->
                        </li><!--end /menu-li -->
                        <!-- END TABLES -->

                        <!-- BEGIN FORMS -->
                        <li class="gui-folder hidden">
                            <a>
                                <div class="gui-icon"><span class="glyphicon glyphicon-list-alt"></span></div>
                                <span class="title">Forms</span>
                            </a>
                            <!--start submenu -->
                            <ul>
                                <li><a href="../../html/forms/basic.html" ><span class="title">Form basic</span></a></li>
                                <li><a href="../../html/forms/advanced.html" ><span class="title">Form advanced</span></a></li>
                                <li><a href="../../html/forms/layouts.html" ><span class="title">Form layouts</span></a></li>
                                <li><a href="../../html/forms/editors.html" ><span class="title">Editors</span></a></li>
                                <li><a href="../../html/forms/validation.html" ><span class="title">Form validation</span></a></li>
                                <li><a href="../../html/forms/wizard.html" ><span class="title">Form wizard</span></a></li>
                            </ul><!--end /submenu -->
                        </li><!--end /menu-li -->
                        <!-- END FORMS -->

                        <!-- BEGIN PAGES -->
                        <li class="gui-folder hidden">
                            <a>
                                <div class="gui-icon"><i class="md md-computer"></i></div>
                                <span class="title">Pages</span>
                            </a>
                            <!--start submenu -->
                            <ul>
                                <li class="gui-folder">
                                    <a href="javascript:void(0);">
                                        <span class="title">Contacts</span>
                                    </a>
                                    <!--start submenu -->
                                    <ul>
                                        <li><a href="../../html/pages/contacts/search.html" ><span class="title">Search</span></a></li>
                                        <li><a href="../../html/pages/contacts/details.html" ><span class="title">Contact card</span></a></li>
                                        <li><a href="../../html/pages/contacts/add.html" ><span class="title">Insert contact</span></a></li>
                                    </ul><!--end /submenu -->
                                </li><!--end /menu-li -->
                                <li class="gui-folder hidden">
                                    <a href="javascript:void(0);">
                                        <span class="title">Search</span>
                                    </a>
                                    <!--start submenu -->
                                    <ul>
                                        <li><a href="../../html/pages/search/results-text.html" ><span class="title">Results - Text</span></a></li>
                                        <li><a href="../../html/pages/search/results-text-image.html" ><span class="title">Results - Text and Image</span></a></li>
                                    </ul><!--end /submenu -->
                                </li><!--end /menu-li -->
                                <li class="gui-folder">
                                    <a href="javascript:void(0);">
                                        <span class="title">Blog</span>
                                    </a>
                                    <!--start submenu -->
                                    <ul>
                                        <li><a href="../../html/pages/blog/masonry.html" ><span class="title">Blog masonry</span></a></li>
                                        <li><a href="../../html/pages/blog/list.html" ><span class="title">Blog list</span></a></li>
                                        <li><a href="../../html/pages/blog/post.html" ><span class="title">Blog post</span></a></li>
                                    </ul><!--end /submenu -->
                                </li><!--end /menu-li -->
                                <li class="gui-folder hidden">
                                    <a href="javascript:void(0);">
                                        <span class="title">Error pages</span>
                                    </a>
                                    <!--start submenu -->
                                    <ul>
                                        <li><a href="../../html/pages/404.html" ><span class="title">404 page</span></a></li>
                                        <li><a href="../../html/pages/500.html" ><span class="title">500 page</span></a></li>
                                    </ul><!--end /submenu -->
                                </li><!--end /menu-li -->
                                <li><a href="../../html/pages/profile.html" ><span class="title">User profile<span class="badge style-accent">42</span></span></a></li>
                                <li><a href="../../html/pages/invoice.html" ><span class="title">Invoice</span></a></li>
                                <li><a href="../../html/pages/calendar.html" ><span class="title">Calendar</span></a></li>
                                <li><a href="../../html/pages/pricing.html" ><span class="title">Pricing</span></a></li>
                                <li><a href="../../html/pages/timeline.html" ><span class="title">Timeline</span></a></li>
                                <li><a href="../../html/pages/maps.html" ><span class="title">Maps</span></a></li>
                                <li><a href="../../html/pages/locked.html" ><span class="title">Lock screen</span></a></li>
                                <li><a href="../../html/pages/login.html" ><span class="title">Login</span></a></li>
                                <li><a href="../../html/pages/blank.html" ><span class="title">Blank page</span></a></li>
                            </ul><!--end /submenu -->
                        </li><!--end /menu-li -->
                        <!-- END FORMS -->

                        <!-- BEGIN CHARTS -->
                        <li class="hidden">
                            <a href="../../html/charts/charts.html" >
                                <div class="gui-icon"><i class="md md-assessment"></i></div>
                                <span class="title">Charts</span>
                            </a>
                        </li><!--end /menu-li -->
                        <!-- END CHARTS -->

                        <!-- BEGIN LEVELS -->
                        <li class="gui-folder hidden">
                            <a>
                                <div class="gui-icon"><i class="fa fa-folder-open fa-fw"></i></div>
                                <span class="title">Menu levels demo</span>
                            </a>
                            <!--start submenu -->
                            <ul>
                                <li><a href="#"><span class="title">Item 1</span></a></li>
                                <li><a href="#"><span class="title">Item 1</span></a></li>
                                <li class="gui-folder">
                                    <a href="javascript:void(0);">
                                        <span class="title">Open level 2</span>
                                    </a>
                                    <!--start submenu -->
                                    <ul>
                                        <li><a href="#"><span class="title">Item 2</span></a></li>
                                        <li class="gui-folder">
                                            <a href="javascript:void(0);">
                                                <span class="title">Open level 3</span>
                                            </a>
                                            <!--start submenu -->
                                            <ul>
                                                <li><a href="#"><span class="title">Item 3</span></a></li>
                                                <li><a href="#"><span class="title">Item 3</span></a></li>
                                                <li class="gui-folder">
                                                    <a href="javascript:void(0);">
                                                        <span class="title">Open level 4</span>
                                                    </a>
                                                    <!--start submenu -->
                                                    <ul>
                                                        <li><a href="#"><span class="title">Item 4</span></a></li>
                                                        <li class="gui-folder">
                                                            <a href="javascript:void(0);">
                                                                <span class="title">Open level 5</span>
                                                            </a>
                                                            <!--start submenu -->
                                                            <ul>
                                                                <li><a href="#"><span class="title">Item 5</span></a></li>
                                                                <li><a href="#"><span class="title">Item 5</span></a></li>
                                                            </ul><!--end /submenu -->
                                                        </li><!--end /submenu-li -->
                                                    </ul><!--end /submenu -->
                                                </li><!--end /submenu-li -->
                                            </ul><!--end /submenu -->
                                        </li><!--end /submenu-li -->
                                    </ul><!--end /submenu -->
                                </li><!--end /submenu-li -->
                            </ul><!--end /submenu -->
                        </li><!--end /menu-li -->
                        <!-- END LEVELS -->

                    </ul><!--end .main-menu -->
                    <!-- END MAIN MENU -->

                    <!-- <div class="menubar-foot-panel">
                            <small class="no-linebreak hidden-folded">
                                    <span class="opacity-75">Copyright &copy; 2014</span> <strong>CodeCovers</strong>
                            </small>
                    </div> -->
                </div><!--end .menubar-scroll-panel-->
            </div><!--end #menubar-->
            <!-- END MENUBAR -->
            @endif
            <!-- BEGIN OFFCANVAS RIGHT -->

            <!-- END OFFCANVAS RIGHT -->

        </div><!--end #base-->
        <!-- END BASE -->

        <!-- BEGIN JAVASCRIPT -->
        <script src="{{ url('/public/assets/js/libs/spin.js/spin.min.js')}}"></script>
        <script src="{{ url('/public/assets/js/libs/autosize/jquery.autosize.min.js')}}"></script>
        <script src="{{ url('/public/assets/js/libs/moment/moment.min.js')}}"></script>
        <script src="{{ url('/public/assets/js/libs/flot/jquery.flot.min.js')}}"></script>
        <script src="{{ url('/public/assets/js/libs/flot/jquery.flot.time.min.js')}}"></script>
        <script src="{{ url('/public/assets/js/libs/flot/jquery.flot.resize.min.js')}}"></script>
        <script src="{{ url('/public/assets/js/libs/flot/jquery.flot.orderBars.js')}}"></script>
        <script src="{{ url('/public/assets/js/libs/flot/jquery.flot.pie.js')}}"></script>
        <script src="{{ url('/public/assets/js/libs/flot/curvedLines.js')}}"></script>
        <script src="{{ url('/public/assets/js/libs/jquery-knob/jquery.knob.min.js')}}"></script>
        <script src="{{ url('/public/assets/js/libs/sparkline/jquery.sparkline.min.js')}}"></script>
        <script src="{{ url('/public/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js')}}"></script>
        <script src="{{ url('/public/assets/js/libs/d3/d3.min.js')}}"></script>
        <script src="{{ url('/public/assets/js/libs/d3/d3.v3.js')}}"></script>
        <script src="{{ url('/public/assets/js/libs/rickshaw/rickshaw.min.js')}}"></script>
        <script src="{{ url('/public/assets/js/core/source/App.js')}}"></script>
        <script src="{{ url('/public/assets/js/core/source/AppNavigation.js')}}"></script>
        <script src="{{ url('/public/assets/js/core/source/AppOffcanvas.js')}}"></script>
        <script src="{{ url('/public/assets/js/core/source/AppCard.js')}}"></script>
        <script src="{{ url('/public/assets/js/core/source/AppForm.js')}}"></script>
        <script src="{{ url('/public/assets/js/core/source/AppNavSearch.js')}}"></script>
        <script src="{{ url('/public/assets/js/core/source/AppVendor.js')}}"></script>
        <script src="{{ url('/public/assets/js/core/demo/Demo.js')}}"></script>
        <script src="{{ url('/public/assets/js/core/demo/DemoDashboard.js')}}"></script>
        <!-- END JAVASCRIPT -->

    </body>
</html>
