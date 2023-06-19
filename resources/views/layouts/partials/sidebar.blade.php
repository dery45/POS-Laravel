<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="" class="brand-link">
        <img src="{{ asset('images/logo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <!--<div class="image">
                <img src="{{ auth()->user()->getAvatar() }}" class="img-circle elevation-2" alt="User Image">
            </div>-->
            <div class="info">
                <a href="#" class="d-block">{{ auth()->user()->getFullname() }}</a>
            </div>
        </div>

        <nav class="mt-2">
        <style>
            .sidebar-item {
                /* Default styles for sidebar items */
            }

            .sidebar-item.active {
                /* Styles for active sidebar items */
                background-color: #252b61; /* Light purple color */
                color: #ffff; /* White text color */
                border-radius: 5px; /* Rounded corners */
                display: flex; /* Enable flexbox */
                /* ... */
}
        </style>

            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <!-- Dashboard -->
                @hasanyrole('superadmin|admin')
                <li class="nav-item sidebar-item {{ 'admin' == request()->path() ? 'active' : '' }}" >
                    <a href="{{ route('home') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                @endhasanyrole

                <!-- Categories -->
                @hasanyrole('superadmin|admin|inventory')
                <li class="nav-item nav-item sidebar-item {{ 'categories' == request()->path() ? 'active' : '' }}">
                    <a href="{{ route('categories.index') }}" class="nav-link {{ activeSegment('categories') }}">
                        <i class="nav-icon fas  fa-suitcase"></i>
                        <p>Kategori</p>
                    </a>
                </li>
                @endhasanyrole

                <!-- Products -->
                @hasanyrole('superadmin|admin|inventory')
                <li class="nav-item sidebar-item {{ 'products' == request()->path() ? 'active' : '' }}">
                    <a href="{{ route('products.index') }}" class="nav-link {{ activeSegment('products') }}">
                        <i class="nav-icon fas fa-th-large"></i>
                        <p>Produk</p>
                    </a>
                </li>
                @endhasanyrole

                <!-- Cashier -->
                @hasanyrole('superadmin|cashier')
                <li class="nav-item sidebar-item {{ 'cart' == request()->path() ? 'active' : '' }}">
                    <a href="{{ route('cart.index') }}" class="nav-link {{ activeSegment('cart') }}">
                        <i class="nav-icon fas fa-calculator"></i>
                        <p>Kasir</p>
                    </a>
                </li>
                @endhasanyrole

                <!-- Sales Orders -->
                @hasanyrole('superadmin|admin|cashier')
                <li class="nav-item sidebar-item {{ 'orders' == request()->path() ? 'active' : '' }}">
                    <a href="{{ route('orders.index') }}" class="nav-link {{ activeSegment('orders') }}">
                        <i class="nav-icon fas fa-cart-plus"></i>
                        <p>Penjualan</p>
                    </a>
                </li>
                @endhasanyrole

                <!-- Users -->
                @hasrole('superadmin')
                <li class="nav-item sidebar-item {{ 'users' == request()->path() ? 'active' : '' }}">
                    <a href="{{ route('users.index') }}" class="nav-link {{ activeSegment('users') }}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Users</p>
                    </a>
                </li>
                @endhasrole

                <!-- Customers -->
                @hasanyrole('')
                <li class="nav-item sidebar-item {{ 'customers' == request()->path() ? 'active' : '' }}">
                    <a href="{{ route('customers.index') }}" class="nav-link {{ activeSegment('customers') }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Pelanggan</p>
                    </a>
                </li>
                @endhasanyrole

                <!-- Settings -->
                @hasrole('superadmin')
                <li class="nav-item sidebar-item {{ 'settings' == request()->path() ? 'active' : '' }}">
                    <a href="{{ route('settings.index') }}" class="nav-link {{ activeSegment('settings') }}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>Pengaturan</p>
                    </a>
                </li>
                @endhasrole

                <!-- Logout -->
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="document.getElementById('logout-form').submit()">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Keluar</p>
                        <form action="{{ route('logout') }}" method="POST" id="logout-form">
                            @csrf
                        </form>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>
