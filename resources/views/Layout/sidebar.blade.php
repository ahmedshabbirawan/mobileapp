<div id="sidebar" class="sidebar responsive ace-save-state">
        <script type="text/javascript">
            try{ace.settings.loadState('sidebar')}catch(e){}
        </script>

        <ul class="nav nav-list">

            <!--------------- Dashboard-------------------->
            <li class="{{request()->is('dashboard*') ? 'active' : '' }}">
                <a href="{{route('dashboard')}}">
                    <i class="menu-icon fa fa-desktop"></i>
                    <span class="menu-text"> Dashboard </span>
                </a>

                <b class="arrow"></b>
            </li>
            <!---------------End Dashboard-------------------->








             <!--------------- Logo -------------------->
             <li class="{{request()->is('logo*') ? 'active open' : '' }}">
                <a href="#" class="dropdown-toggle ">
                    <i class="menu-icon ace-icon glyphicon glyphicon-picture "></i>
                    <span class="menu-text">
                        Logo
                    </span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>
                <b class="arrow"></b>
                <ul class="submenu">
                    @can('logo.read')
                    @endcan
                    <li class="{{request()->is('post/logo/list*') ? 'active' : '' }}">
                        <a href="{{ route('post.logo.list') }}"><i class="menu-icon fa fa-caret-right"></i>List</a>
                        <b class="arrow"></b>
                    </li>
                    

                    @can('logo.create')
                    @endcan
                    <li class="{{request()->is('post/logo/create*') ? 'active' : '' }}">
                        <a href="{{ route('post.logo.create') }}"><i class="menu-icon fa fa-caret-right"></i>Add</a>
                        <b class="arrow"></b>
                    </li>
                    
                </ul>
            </li>
            <!--------------- End Products-------------------->

        
            <!--------------- Sale Start -------------------->
            <li style="display: none;" class="{{request()->is('sale/order/*') || request()->is('sale/sale-return/*') ? 'active open' : '' }}">
                <a href="#" class="dropdown-toggle ">
                    <i class="menu-icon ace-icon glyphicon glyphicon-qrcode "></i>
                    <span class="menu-text">
                        Sale
                    </span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>
                <b class="arrow"></b>
                <ul class="submenu">
                
                    <li class="{{request()->is('sale/order/*') ? 'active' : '' }}">
                        <a href=""><i class="menu-icon fa fa-caret-right"></i>Sale</a>
                        <b class="arrow"></b>
                    </li>
                </ul>
            </li> <!---------------Sale End-------------------->


            
            
           

                        <!--------------- Category -------------------->
                        <li class="{{request()->is('term/logo_category*') ? 'active open' : '' }}">
                <a href="#" class="dropdown-toggle ">
                    <i class="menu-icon ace-icon glyphicon glyphicon-tag "></i>
                    <span class="menu-text">
                        Category
                    </span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>
                <b class="arrow"></b>
                <ul class="submenu">
                    @can('logo.read')
                    @endcan
                    <li class="{{request()->is('term/logo_category/list*') ? 'active' : '' }}">
                        <a href="{{ route('term.logo_category.list') }}"><i class="menu-icon fa fa-caret-right"></i>List</a>
                        <b class="arrow"></b>
                    </li>
                    

                    @can('logo.create')
                    @endcan
                    <li class="{{request()->is('term/logo_category/create*') ? 'active' : '' }}">
                        <a href="{{ route('term.logo_category.create') }}"><i class="menu-icon fa fa-caret-right"></i>Add</a>
                        <b class="arrow"></b>
                    </li>
                    
                </ul>
            </li>
            <!--------------- End Category -------------------->

           

            <!--------------- User -------------------->
            <li class="{{request()->is('user*') ? 'active open' : '' }}">
                <a href="#" class="dropdown-toggle ">
                    <i class="menu-icon fa fa-user"></i>
                    <span class="menu-text">
                        Users
                    </span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <b class="arrow"></b>

                <ul class="submenu">
                    <li class="{{request()->is('user/list*') ? 'active' : '' }}">
                        <a href="{{ route('user.list') }}"><i class="menu-icon fa fa-caret-right"></i>List</a><b class="arrow"></b>
                    </li>
                    <li class="{{request()->is('user/create*') ? 'active' : '' }}">
                        <a href="{{ route('user.create') }}"><i class="menu-icon fa fa-caret-right"></i>Add New</a><b class="arrow"></b>
                    </li>
                    @can('customer.read')
                    
                    @endcan
                    @can('customer.create')
                    
                    @endcan
                </ul>
            </li>
            <!--------------- End User -------------------->


        






            


          


            

            <!--------------- Settings -------------------->
            <li style="display: none;" class="{{request()->is('Settings*') ? 'active open' : '' }}">
                <a href="#" class="dropdown-toggle ">
                    <i class="menu-icon ace-icon fa fa-cogs"></i>
                    <span class="menu-text">
                        Settings
                    </span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <b class="arrow"></b>

                <ul class="submenu">
                    @can('warehouse.read')
                    <li class="{{request()->is('Settings/warehouse*') ? 'active' : '' }}">
                        <a href="{{ route('Settings.warehouse.index') }}"><i class="menu-icon fa fa-caret-right"></i>Warehouses</a><b class="arrow"></b>
                    </li>
                    @endcan
                    @can('locagtion.read')
                    <li class="{{request()->is('Settings/location*') ? 'active' : '' }}">
                        <a href="{{ route('Settings.location.index') }}"><i class="menu-icon fa fa-caret-right"></i>Locations</a><b class="arrow"></b>
                    </li>
                    @endcan
                    @can('uom.read')
                    <li class="{{request()->is('Settings/uom*') ? 'active' : '' }}">
                        <a href="{{ route('Settings.uom.index') }}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            UOM
                        </a>
                        <b class="arrow"></b>
                    </li>
                    @endcan
                </ul>
            </li>
            <!--------------- End Settings-------------------->


           
           

           

           

             <!--------------- Reports -------------------->
             <li style="display: none;" class="{{request()->is('reports*') ? 'active open' : '' }} ">
                <a href="#" class="dropdown-toggle ">
                    <i class="menu-icon fa fa-bar-chart-o "></i>
                    <span class="menu-text">
                        Reports
                    </span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>
                <b class="arrow"></b>
                <ul class="submenu">
                    
                    <li class="{{request()->is('report*') ? 'active' : '' }}">
                        <a href="{{ route('report.sale.all') }}"><i class="menu-icon fa fa-caret-right"></i>Sale</a><b class="arrow"></b>
                    </li>    

                    <li class="{{request()->is('report*') ? 'active' : '' }}">
                        <a href="{{ route('report.product') }}"><i class="menu-icon fa fa-caret-right"></i>Product</a><b class="arrow"></b>
                    </li>

                    <li class="{{request()->is('report*') ? 'active' : '' }}">
                        <a href="{{ route('report.product_wise') }}"><i class="menu-icon fa fa-caret-right"></i>Product Report</a><b class="arrow"></b>
                    </li>

                </ul>
            </li>
            <!--------------- End Reports-------------------->

            
        </ul><!-- /.nav-list -->

        <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
            <i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left ace-save-state" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
        </div>
</div>



