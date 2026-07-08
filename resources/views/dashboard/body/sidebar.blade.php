
<div class="iq-sidebar sidebar-default ">
    <div class="iq-sidebar-logo d-flex align-items-center justify-content-between">
        <a href="{{ route('dashboard') }}" class="header-logo">
            <img src="{{ asset('assets/images/logo.png') }}" class="img-fluid rounded-normal light-logo" alt="logo"><h5 class="logo-title light-logo ml-3">bondoo</h5>
        </a>
        <div class="iq-menu-bt-sidebar ml-0">
            <x-heroicon-o-bars-3 class="wrapper-menu w-8 h-8" />
        </div>
    </div>
    <div class="data-scrollbar" data-scroll="1">
        <nav class="iq-sidebar-menu">
            <ul id="iq-sidebar-toggle" class="iq-menu">
                <li class="{{ Request::is('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="svg-icon">
                        <x-heroicon-o-home class="w-6 h-6" />
                        <span class="ml-4">Dashboards</span>
                    </a>
                </li>

                @if (auth()->user()->can('pos.menu'))
                    <li class="{{ Request::is('pos*') ? 'active' : '' }}">
                        <a href="{{ route('pos.index') }}" class="svg-icon">
                            <x-heroicon-o-shopping-cart class="w-6 h-6" />
                            <span class="ml-3">POS</span>
                            </a>
                            </li>
                @endif

                <hr>

                @if (auth()->user()->can('orders.menu'))
                    <li class="{{ Request::is(['orders*', 'pending/due*']) ? 'active' : '' }}">
                        <a href="#orders" class="{{ Request::is(['orders*', 'pending/due*']) ? '' : 'collapsed' }}" data-toggle="collapse" aria-expanded="{{ Request::is(['orders*', 'pending/due*']) ? 'true' : 'false' }}">
                            <x-heroicon-o-shopping-bag class="w-6 h-6" />
                            <span class="ml-3">Orders</span>
                            <x-heroicon-o-chevron-right class="w-4 h-4 iq-arrow-right arrow-active" />
                            </a>
                            <ul id="orders" class="iq-submenu collapse {{ Request::is(['orders*', 'pending/due*']) ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">

                            <li class="{{ Request::is('orders/pending*') ? 'active' : '' }}">
                                <a href="{{ route('order.pendingOrders') }}">
                                    <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Pending Orders</span>
                                    </a>
                                    </li>
                                    <li class="{{ Request::is('orders/complete*') ? 'active' : '' }}">
                                        <a href="{{ route('order.completeOrders') }}">
                                    <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Complete Orders</span>
                                    </a>
                                    </li>
                                    <li class="{{ Request::is('pending/due*') ? 'active' : '' }}">
                                        <a href="{{ route('order.pendingDue') }}">
                                    <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Pending Due</span>
                                    </a>
                                    </li>

                                    </ul>
                                    </li>
                @endif

                @if (auth()->user()->can('product.menu'))
                    <li class="{{ Request::is(['products*', 'categories*']) ? 'active' : '' }}">
                        <a href="#products" class="{{ Request::is(['products*', 'categories*']) ? '' : 'collapsed' }}" data-toggle="collapse" aria-expanded="{{ Request::is(['products*', 'categories*']) ? 'true' : 'false' }}">
                            <x-heroicon-o-archive-box class="w-6 h-6" />
                            <span class="ml-3">Products</span>
                            <x-heroicon-o-chevron-right class="w-4 h-4 iq-arrow-right arrow-active" />
                            </a>
                            <ul id="products" class="iq-submenu collapse {{ Request::is(['products*', 'categories*']) ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">
                                <li class="{{ Request::is(['products']) ? 'active' : '' }}">
                                    <a href="{{ route('products.index') }}">
                                        <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Products</span>
                                        </a>
                                        </li>
                                        <li class="{{ Request::is(['products/create']) ? 'active' : '' }}">
                                            <a href="{{ route('products.create') }}">
                                        <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Add Product</span>
                                        </a>
                                        </li>
                                        <li class="{{ Request::is(['categories*']) ? 'active' : '' }}">
                                            <a href="{{ route('categories.index') }}">
                                        <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Categories</span>
                                        </a>
                                        </li>
                                        </ul>
                                        </li>
                @endif

                <hr>

                @if (auth()->user()->can('employee.menu'))
                    <li class="{{ Request::is('employees*') ? 'active' : '' }}">
                        <a href="{{ route('employees.index') }}" class="svg-icon">
                            <x-heroicon-o-user-group class="w-6 h-6" />
                            <span class="ml-3">Employees</span>
                            </a>
                            </li>
                @endif

                @if (auth()->user()->can('customer.menu'))
                    <li class="{{ Request::is('customers*') ? 'active' : '' }}">
                        <a href="{{ route('customers.index') }}" class="svg-icon">
                            <x-heroicon-o-user-group class="w-6 h-6" />
                            <span class="ml-3">Customers</span>
                            </a>
                            </li>
                @endif

                @if (auth()->user()->can('supplier.menu'))
                    <li class="{{ Request::is('suppliers*') ? 'active' : '' }}">
                        <a href="{{ route('suppliers.index') }}" class="svg-icon">
                            <x-heroicon-o-user-group class="w-6 h-6" />
                            <span class="ml-3">Suppliers</span>
                            </a>
                            </li>
                @endif

                @if (auth()->user()->can('salary.menu'))
                    <li class="{{ Request::is(['advance-salary*', 'pay-salary*']) ? 'active' : '' }}">
                        <a href="#advance-salary" class="{{ Request::is(['advance-salary*', 'pay-salary*']) ? '' : 'collapsed' }}" data-toggle="collapse" aria-expanded="{{ Request::is(['advance-salary*', 'pay-salary*']) ? 'true' : 'false' }}">
                        <x-heroicon-o-banknotes class="w-6 h-6" />
                        <span class="ml-3">Salary</span>
                        <x-heroicon-o-chevron-right class="w-4 h-4 iq-arrow-right arrow-active" />
                        </a>
                        <ul id="advance-salary" class="iq-submenu collapse {{ Request::is(['advance-salary*', 'pay-salary*']) ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">

                            <li class="{{ Request::is(['advance-salary', 'advance-salary/*/edit']) ? 'active' : '' }}">
                                <a href="{{ route('advance-salary.index') }}">
                                    <x-heroicon-o-arrow-right class="w-4 h-4" /><span>All Advance Salary</span>
                                    </a>
                                    </li>
                                    <li class="{{ Request::is('advance-salary/create*') ? 'active' : '' }}">
                                        <a href="{{ route('advance-salary.create') }}">
                                    <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Create Advance Salary</span>
                                    </a>
                                    </li>
                                    <li class="{{ Request::is('pay-salary') ? 'active' : '' }}">
                                        <a href="{{ route('pay-salary.index') }}">
                                    <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Pay Salary</span>
                                    </a>
                                    </li>
                                    <li class="{{ Request::is('pay-salary/history*') ? 'active' : '' }}">
                                        <a href="{{ route('pay-salary.payHistory') }}">
                                    <x-heroicon-o-arrow-right class="w-4 h-4" /><span>History Pay Salary</span>
                                    </a>
                                    </li>
                                    </ul>
                                    </li>
                @endif

                @if (auth()->user()->can('attendance.menu'))
                    <li class="{{ Request::is(['attendance*']) ? 'active' : '' }}">
                        <a href="#attendance" class="{{ Request::is(['attendance*']) ? '' : 'collapsed' }}" data-toggle="collapse" aria-expanded="{{ Request::is(['attendance*']) ? 'true' : 'false' }}">
                            <x-heroicon-o-calendar-days class="w-6 h-6" />
                            <span class="ml-3">Attendance</span>
                            <x-heroicon-o-chevron-right class="w-4 h-4 iq-arrow-right arrow-active" />
                            </a>
                            <ul id="attendance" class="iq-submenu collapse {{ Request::is(['attendance*']) ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">

                                <li class="{{ Request::is(['attendance']) ? 'active' : '' }}">
                                    <a href="{{ route('attendance.index') }}">
                                        <x-heroicon-o-arrow-right class="w-4 h-4" /><span>All Attendance</span>
                                        </a>
                                        </li>
                                        <li class="{{ Request::is('attendance/create') ? 'active' : '' }}">
                                            <a href="{{ route('attendance.create') }}">
                                                <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Create Attendance</span>
                                        </a>
                                        </li>
                                        </ul>
                                        </li>
                @endif
                <li class="{{ Request::is(['purchases*']) ? 'active' : '' }}">
                    <a href="#purchases" class="{{ Request::is(['purchases*']) ? '' : 'collapsed' }}" data-toggle="collapse" aria-expanded="{{ Request::is(['purchases*']) ? 'true' : 'false' }}">
                        <x-heroicon-o-shopping-bag class="w-6 h-6" />
                        <span class="ml-3">Purchases (PO)</span>
                        <x-heroicon-o-chevron-right class="w-4 h-4 iq-arrow-right arrow-active" />
                    </a>
                    <ul id="purchases" class="iq-submenu collapse {{ Request::is(['purchases*']) ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">
                        <li class="{{ Request::is('purchases') ? 'active' : '' }}">
                            <a href="{{ route('purchases.index') }}">
                                <x-heroicon-o-arrow-right class="w-4 h-4" /><span>All Purchases</span>
                            </a>
                        </li>
                        <li class="{{ Request::is('purchases/create') ? 'active' : '' }}">
                            <a href="{{ route('purchases.create') }}">
                                <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Create PO</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="{{ Request::is(['adjustments*']) ? 'active' : '' }}">
                    <a href="#adjustments" class="{{ Request::is(['adjustments*']) ? '' : 'collapsed' }}" data-toggle="collapse" aria-expanded="{{ Request::is(['adjustments*']) ? 'true' : 'false' }}">
                        <x-heroicon-o-adjustments-vertical class="w-6 h-6" />
                        <span class="ml-3">Stock Opname</span>
                        <x-heroicon-o-chevron-right class="w-4 h-4 iq-arrow-right arrow-active" />
                    </a>
                    <ul id="adjustments" class="iq-submenu collapse {{ Request::is(['adjustments*']) ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">
                        <li class="{{ Request::is('adjustments') ? 'active' : '' }}">
                            <a href="{{ route('adjustments.index') }}">
                                <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Adjustment Logs</span>
                            </a>
                        </li>
                        <li class="{{ Request::is('adjustments/create') ? 'active' : '' }}">
                            <a href="{{ route('adjustments.create') }}">
                                <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Record Opname</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="{{ Request::is(['returns*']) ? 'active' : '' }}">
                    <a href="#returns" class="{{ Request::is(['returns*']) ? '' : 'collapsed' }}" data-toggle="collapse" aria-expanded="{{ Request::is(['returns*']) ? 'true' : 'false' }}">
                        <x-heroicon-o-arrow-uturn-left class="w-6 h-6" />
                        <span class="ml-3">Returns (Retur)</span>
                        <x-heroicon-o-chevron-right class="w-4 h-4 iq-arrow-right arrow-active" />
                    </a>
                    <ul id="returns" class="iq-submenu collapse {{ Request::is(['returns*']) ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">
                        <li class="{{ Request::is('returns') ? 'active' : '' }}">
                            <a href="{{ route('returns.index') }}">
                                <x-heroicon-o-arrow-right class="w-4 h-4" /><span>All Returns</span>
                            </a>
                        </li>
                        <li class="{{ Request::is('returns/create') ? 'active' : '' }}">
                            <a href="{{ route('returns.create') }}">
                                <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Log Return</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="{{ Request::is(['reports*']) ? 'active' : '' }}">
                    <a href="{{ route('reports.index') }}" class="svg-icon">
                        <x-heroicon-o-document-chart-bar class="w-6 h-6" />
                        <span class="ml-3">Reports</span>
                    </a>
                </li>

                <hr>

                @if (auth()->user()->can('roles.menu'))
                    <li class="{{ Request::is(['permission*', 'role*']) ? 'active' : '' }}">
                        <a href="#permission" class="{{ Request::is(['permission*', 'role*']) ? '' : 'collapsed' }}" data-toggle="collapse" aria-expanded="{{ Request::is(['permission*', 'role*']) ? 'true' : 'false' }}">
                            <x-heroicon-o-key class="w-6 h-6" />
                            <span class="ml-3">Role & Permission</span>
                            <x-heroicon-o-chevron-right class="w-4 h-4 iq-arrow-right arrow-active" />
                            </a>
                            <ul id="permission" class="iq-submenu collapse {{ Request::is(['permission*', 'role*']) ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">
                                <li class="{{ Request::is(['permission', 'permission/create', 'permission/edit/*']) ? 'active' : '' }}">
                                    <a href="{{ route('permission.index') }}">
                                        <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Permissions</span>
                                        </a>
                                        </li>
                                        <li class="{{ Request::is(['role', 'role/create', 'role/edit/*']) ? 'active' : '' }}">
                                            <a href="{{ route('role.index') }}">
                                        <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Roles</span>
                                        </a>
                                        </li>
                                        <li class="{{ Request::is(['role/permission*']) ? 'active' : '' }}">
                                            <a href="{{ route('rolePermission.index') }}">
                                        <x-heroicon-o-arrow-right class="w-4 h-4" /><span>Role in Permissions</span>
                                        </a>
                                        </li>
                                        </ul>
                                        </li>
                @endif

                @if (auth()->user()->can('user.menu'))
                    <li class="{{ Request::is('users*') ? 'active' : '' }}">
                        <a href="{{ route('users.index') }}" class="svg-icon">
                            <x-heroicon-o-users class="w-6 h-6" />
                            <span class="ml-3">Users</span>
                            </a>
                            </li>
                @endif

                @if (auth()->user()->can('database.menu'))
                    <li class="{{ Request::is('database/backup*') ? 'active' : '' }}">
                        <a href="{{ route('backup.index') }}" class="svg-icon">
                            <x-heroicon-o-circle-stack class="w-6 h-6" />
                            <span class="ml-3">Backup Database</span>
                            </a>
                            </li>
                @endif

                <li class="{{ Request::is('help*') ? 'active' : '' }}">
                    <a href="{{ route('help.index') }}" class="svg-icon">
                        <x-heroicon-o-question-mark-circle class="w-6 h-6" />
                        <span class="ml-3">Help</span>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="p-3"></div>
    </div>
</div>
