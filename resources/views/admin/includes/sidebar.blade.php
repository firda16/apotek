<!-- Sidebar -->
<div class="sidebar" id="sidebar">
	<div class="sidebar-inner slimscroll">
		<div id="sidebar-menu" class="sidebar-menu">

			<ul>
				<li class="menu-title">
					<span>Main</span>
				</li>

				<li class="{{ route_is('dashboard') ? 'active' : '' }}">
					<a href="{{ route('dashboard') }}"><i class="fe fe-home"></i> <span>Dashboard</span></a>
				</li>

				<li class="{{ route_is('categories.*') ? 'active' : '' }}">
					<a href="{{ route('categories.index') }}"><i class="fe fe-layout"></i> <span>Categories</span></a>
				</li>

				<li class="submenu">
					<a href="#"><i class="fe fe-document"></i> <span> Products</span> <span class="menu-arrow"></span></a>
					<ul style="display: none;">
						<li><a class="{{ route_is('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">Products</a></li>
						<li><a class="{{ route_is('products.create') ? 'active' : '' }}" href="{{ route('products.create') }}">Add Product</a></li>
						<li><a class="{{ route_is('outstock') ? 'active' : '' }}" href="{{ route('outstock') }}">Out-Stock</a></li>
						<li><a class="{{ route_is('expired') ? 'active' : '' }}" href="{{ route('expired') }}">Expired</a></li>
					</ul>
				</li>

				<li class="submenu">
					<a href="#"><i class="fe fe-star-o"></i> <span> Purchase</span> <span class="menu-arrow"></span></a>
					<ul style="display: none;">
						<li><a class="{{ route_is('purchases.*') ? 'active' : '' }}" href="{{ route('purchases.index') }}">Purchase</a></li>
						<li><a class="{{ route_is('purchases.create') ? 'active' : '' }}" href="{{ route('purchases.create') }}">Add Purchase</a></li>
					</ul>
				</li>

				<li class="submenu">
					<a href="#"><i class="fe fe-activity"></i> <span> Sale</span> <span class="menu-arrow"></span></a>
					<ul style="display: none;">
						<li><a class="{{ route_is('sales.*') ? 'active' : '' }}" href="{{ route('sales.index') }}">Sales</a></li>
						<li><a class="{{ route_is('sales.create') ? 'active' : '' }}" href="{{ route('sales.create') }}">Add Sale</a></li>
					</ul>
				</li>

				<li class="submenu">
					<a href="#"><i class="fe fe-user"></i> <span> Supplier</span> <span class="menu-arrow"></span></a>
					<ul style="display: none;">
						<li><a class="{{ route_is('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}">Supplier</a></li>
						<li><a class="{{ route_is('suppliers.create') ? 'active' : '' }}" href="{{ route('suppliers.create') }}">Add Supplier</a></li>
					</ul>
				</li>

				<li class="submenu">
					<a href="#"><i class="fe fe-document"></i> <span> Reports</span> <span class="menu-arrow"></span></a>
					<ul style="display: none;">
						<li><a class="{{ route_is('sales.report') ? 'active' : '' }}" href="{{ route('sales.report') }}">Sale Report</a></li>
						<li><a class="{{ route_is('purchases.report') ? 'active' : '' }}" href="{{ route('purchases.report') }}">Purchase Report</a></li>
					</ul>
				</li>

				<li class="submenu">
					<a href="#"><i class="fe fe-lock"></i> <span> Access Control</span> <span class="menu-arrow"></span></a>
					<ul style="display: none;">
						<li><a class="{{ route_is('permissions.index') ? 'active' : '' }}" href="{{ route('permissions.index') }}">Permissions</a></li>
						<li><a class="{{ route_is('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}">Roles</a></li>
					</ul>
				</li>

				<li class="{{ route_is('users.*') ? 'active' : '' }}">
					<a href="{{ route('users.index') }}"><i class="fe fe-users"></i> <span>Users</span></a>
				</li>

				<li class="{{ route_is('profile') ? 'active' : '' }}">
					<a href="{{ route('profile') }}"><i class="fe fe-user-plus"></i> <span>Profile</span></a>
				</li>

				<li class="{{ route_is('backup.index') ? 'active' : '' }}">
					<a href="{{ route('backup.index') }}"><i class="material-icons">backup</i> <span>Backups</span></a>
				</li>

				<li class="{{ route_is('settings') ? 'active' : '' }}">
					<a href="{{ route('settings') }}">
						<i class="material-icons">settings</i> <span>Settings</span>
					</a>
				</li>
			</ul>
		</div>
	</div>
</div>
<!-- /Sidebar -->