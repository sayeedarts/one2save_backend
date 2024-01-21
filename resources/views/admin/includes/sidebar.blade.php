<aside class="sidebar-left border-right bg-white shadow" id="leftSidebar" data-simplebar>
	<a href="#" class="btn collapseSidebar toggle-btn d-lg-none text-muted ml-2 mt-3" data-toggle="toggle">
		<i class="fe fe-x"><span class="sr-only"></span></i>
	</a>
	<nav class="vertnav navbar navbar-light">
		<!-- nav bar -->
		<div class="w-100 mb-4 d-flex">
			<a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="{{route('admin-dashboard')}}">
				<img src="{{ asset('public/uploads/profile/') }}/{{$settings['logo']}}" height="50px" width="100px" alt="logo" class="img-fluid">
			</a>
		</div>
		<ul class="navbar-nav flex-fill w-100 mb-2">
			<li class="nav-item w-100">
				<a class="nav-link" href="{{route('admin-dashboard')}}">
					<i class="fe fe-home fe-16"></i>
					<span class="ml-3 item-text">Dashboard</span>
				</a>
			</li>
		</ul>
		<p class="text-muted nav-heading mt-4 mb-1">
			<span>Components</span>
		</p>
		<ul class="navbar-nav flex-fill w-100 mb-2">
			<!-- Menus for Oneplace2Save -->

			<!-- Services -->
			@canany(['add-service', 'view-service'])
			<li class="nav-item dropdown">
				<a href="#service-mgmt" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link">
					<i class="fe fe-command fe-16"></i>
					<span class="ml-3 item-text">Manage Service</span>
				</a>
				<ul class="collapse list-unstyled pl-4 w-100" id="service-mgmt">
					@can('add-service')
					<li class="nav-item">
						<a class="nav-link pl-3" href="{{route('service.create')}}"><span class="ml-1 item-text">Add New</span>
						</a>
					</li>
					@endcan
					@can('view-service')
					<li class="nav-item">
						<a class="nav-link pl-3" href="{{route('service.list')}}"><span class="ml-1 item-text">View all</span></a>
					</li>
					@endcan
				</ul>
			</li>
			@endcanany

			<!-- Service Category -->
			@canany(['add-service-category', 'view-service-category'])
			<li class="nav-item dropdown">
				<a href="#service-category-mgmt" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link">
					<i class="fe fe-git-merge fe-16"></i>
					<span class="ml-3 item-text">Service Category</span>
				</a>
				<ul class="collapse list-unstyled pl-4 w-100" id="service-category-mgmt">
					@can('add-service-category')
					<li class="nav-item">
						<a class="nav-link pl-3" href="{{route('service-categories.create')}}"><span class="ml-1 item-text">Add New</span>
						</a>
					</li>
					@endcan
					@can('view-service-category')
					<li class="nav-item">
						<a class="nav-link pl-3" href="{{route('service-categories.list')}}"><span class="ml-1 item-text">View all</span></a>
					</li>
					@endcan
				</ul>
			</li>
			@endcanany

			<!-- Storage -->
			@canany(['add-storage', 'view-storage'])
			<li class="nav-item dropdown">
				<a href="#storage-mgmt" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link">
					<i class="fe fe-database fe-16"></i>
					<span class="ml-3 item-text">Manage Storages</span>
				</a>
				<ul class="collapse list-unstyled pl-4 w-100" id="storage-mgmt">
					@can('add-storage')
					<li class="nav-item">
						<a class="nav-link pl-3" href="{{route('storage.create')}}"><span class="ml-1 item-text">Add New</span>
						</a>
					</li>
					@endcan
					@can('view-storage')
					<li class="nav-item">
						<a class="nav-link pl-3" href="{{route('storage.list')}}"><span class="ml-1 item-text">View all</span></a>
					</li>
					@endcan
				</ul>
			</li>
			@endcanany

			<!-- Package -->
			@canany(['add-package', 'view-package'])
			<li class="nav-item dropdown">
				<a href="#packaging-mgmt" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link">
					<i class="fe fe-box fe-16"></i>
					<span class="ml-3 item-text">Manage Packages</span>
				</a>
				<ul class="collapse list-unstyled pl-4 w-100" id="packaging-mgmt">
					@can('add-package')
						<li class="nav-item">
							<a class="nav-link pl-3" href="{{route('packaging.create')}}"><span class="ml-1 item-text">Add New</span>
							</a>
						</li>
					@endcan
					@can('view-package')
					<li class="nav-item">
						<a class="nav-link pl-3" href="{{route('packaging.list')}}"><span class="ml-1 item-text">View all</span></a>
					</li>
					@endcan
				</ul>
			</li>
			@endcanany

			<!-- Packages -->
			@canany(['add-page', 'view-page'])
			<li class="nav-item dropdown">
				<a href="#page-mgmt" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link">
					<i class="fe fe-file fe-16"></i>
					<span class="ml-3 item-text">Manage Pages/Sections</span>
				</a>
				<ul class="collapse list-unstyled pl-4 w-100" id="page-mgmt">
					@can('add-page')
					<li class="nav-item">
						<a class="nav-link pl-3" href="{{route('page.create')}}"><span class="ml-1 item-text">Add New</span>
						</a>
					</li>
					@endcan
					@can('view-page')
					<li class="nav-item">
						<a class="nav-link pl-3" href="{{route('page.list')}}"><span class="ml-1 item-text">View all</span></a>
					</li>
					@endcan
				</ul>
			</li>
			@endcanany

			<!-- Gallery -->
			@canany(['add-gallery', 'view-gallery'])
			<li class="nav-item dropdown">
				<a href="#gallery-mgmt" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link">
					<i class="fe fe-file fe-16"></i>
					<span class="ml-3 item-text">Manage Gallery</span>
				</a>
				<ul class="collapse list-unstyled pl-4 w-100" id="gallery-mgmt">
					@can('add-gallery')
					<li class="nav-item">
						<a class="nav-link pl-3" href="{{route('gallery.create')}}"><span class="ml-1 item-text">Add New</span>
						</a>
					</li>
					@endcan
					@can('view-gallery')
					<li class="nav-item">
						<a class="nav-link pl-3" href="{{route('gallery.list')}}"><span class="ml-1 item-text">View all</span></a>
					</li>
					@endcan
				</ul>
			</li>
			@endcanany

			<!-- File -->
			@canany(['add-file', 'view-file'])
			<li class="nav-item dropdown">
				<a href="#file-manager-mgmt" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link">
					<i class="fe fe-file fe-16"></i>
					<span class="ml-3 item-text">File Manager</span>
				</a>
				<ul class="collapse list-unstyled pl-4 w-100" id="file-manager-mgmt">
					@can('add-file')
					<li class="nav-item">
						<a class="nav-link pl-3" href="{{route('file-manager.create')}}"><span class="ml-1 item-text">Add New</span>
						</a>
					</li>
					@endcan
					@can('view-file')
					<li class="nav-item">
						<a class="nav-link pl-3" href="{{route('file-manager.list')}}"><span class="ml-1 item-text">View all</span></a>
					</li>
					@endcan
				</ul>
			</li>
			@endcanany

			<!-- End of Menu Listing -->
			@canany(['view-order'])
			<li class="nav-item dropdown">
				<a href="#order-management" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link">
					<i class="fe fe-box fe-16"></i>
					<span class="ml-3 item-text">Manage Orders</span>
				</a>
				<ul class="collapse list-unstyled pl-4 w-100" id="order-management">
					<li class="nav-item">
						<a class="nav-link pl-3" href="{{route('orders.all')}}"><span class="ml-1 item-text">Orders</span>
						</a>
					</li>
				</ul>
			</li>
			@endcanany

			@canany(['view-quote'])
			<li class="nav-item dropdown">
				<a href="#quote-management" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link">
					<i class="fe fe-clipboard fe-16"></i>
					<span class="ml-3 item-text">Manage Quotes</span>
				</a>
				<ul class="collapse list-unstyled pl-4 w-100" id="quote-management">
					<li class="nav-item">
						<a class="nav-link pl-3" href="{{route('quotes.all')}}"><span class="ml-1 item-text">Quote List</span>
						</a>
					</li>
				</ul>
			</li>
			@endcanany

			@if (Auth::user()->hasRole('admin'))
			<li class="nav-item dropdown">
				<a href="#permission-management" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link">
					<i class="fe fe-key fe-16"></i>
					<span class="ml-3 item-text">Manage Permissions</span>
				</a>
				<ul class="collapse list-unstyled pl-4 w-100" id="permission-management">
					<li class="nav-item">
						<a class="nav-link pl-3" href="{{route('user.list')}}"><span class="ml-1 item-text">Manage Users</span>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link pl-3" href="{{route('role.list')}}"><span class="ml-1 item-text">Manage Roles</span>
						</a>
					</li>
				</ul>
			</li>
			@endif

			@canany(['view-template', 'edit-brand-settings'])
			<li class="nav-item dropdown">
				<a href="#site-settings" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle nav-link">
					<i class="fe fe-settings fe-16"></i>
					<span class="ml-3 item-text">Settings</span>
				</a>
				<ul class="collapse list-unstyled pl-4 w-100" id="site-settings">
					@can('edit-brand-settings')
					<li class="nav-item">
						<a class="nav-link pl-3" href="{{route('settings.add')}}"><span class="ml-1 item-text">Website Settings</span>
						</a>
					</li>
					@endcan
					@can('view-template')
					<li class="nav-item">
						<a class="nav-link pl-3" href="{{route('template.edit', 1)}}"><span class="ml-1 item-text">
							Quote Template
						</span>
						</a>
					</li>
					@endcan
				</ul>
			</li>
			@endcanany

			<li class="nav-item dropdown">
				<a href="{{ route('user.logout') }}" class="nav-link">
					<i class="fe fe-log-out fe-16"></i>
					<span class="ml-3 item-text">Logout</span>
				</a>
			</li>
	</nav>
</aside>
@section('scripts')

@endsection