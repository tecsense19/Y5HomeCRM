<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Y5Home CRM') | Y5Home</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}?v={{ time() }}" />

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary: #034b25;
            --accent:  #98731e;
            --sidebar-width: 260px;
        }
        body { background: #f0f2f5; font-family: 'Segoe UI', sans-serif; }

        /* Sidebar */
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--primary);
            position: fixed;
            top: 0; left: 0;
            overflow-y: auto;
            z-index: 1000;
            transition: transform .3s;
        }
        #sidebar .brand {
            padding: 1.2rem 1.5rem;
            background: rgba(0,0,0,.2);
            color: #fff;
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: .5px;
        }
        #sidebar .brand span { color: var(--accent); }
        #sidebar .nav-link {
            color: rgba(255,255,255,.75);
            padding: .55rem 1.5rem;
            font-size: .88rem;
            transition: all .2s;
            border-left: 3px solid transparent;
        }
        #sidebar .nav-link:hover,
        #sidebar .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,.1);
            border-left-color: var(--accent);
        }
        #sidebar .nav-link i { width: 22px; }
        #sidebar .nav-section {
            color: rgba(255,255,255,.4);
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 1rem 1.5rem .3rem;
        }

        /* Topbar */
        #topbar {
            margin-left: var(--sidebar-width);
            height: 60px;
            background: #fff;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 999;
            gap: 1rem;
        }
        #topbar .page-title { font-size: 1rem; font-weight: 600; color: var(--primary); }

        /* Main content */
        #main { margin-left: var(--sidebar-width); padding: 1.5rem; }

        /* Cards */
        .stat-card {
            background: #fff;
            border-radius: 10px;
            padding: 1.25rem;
            box-shadow: 0 1px 4px rgba(0,0,0,.07);
            border-left: 4px solid var(--accent);
        }
        .stat-card .stat-value { font-size: 1.8rem; font-weight: 700; color: var(--primary); }
        .stat-card .stat-label { font-size: .78rem; color: #888; text-transform: uppercase; letter-spacing: .5px; }

        .card { border: none; border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,.07); }
        .card-header { background: #fff; border-bottom: 1px solid #f0f0f0; font-weight: 600; }

        /* Status badges */
        .badge-new            { background: #e3f2fd; color: #1565c0; }
        .badge-contacted      { background: #e8f5e9; color: #2e7d32; }
        .badge-qualified      { background: #fff3e0; color: #e65100; }
        .badge-won            { background: #e8f5e9; color: #1b5e20; }
        .badge-lost           { background: #ffebee; color: #b71c1c; }
        .badge-negotiation    { background: #f3e5f5; color: #6a1b9a; }

        /* Table */
        .table th { font-size: .78rem; text-transform: uppercase; color: #888; font-weight: 600; }
        .table td { vertical-align: middle; font-size: .88rem; }

        /* Forms */
        .form-label { font-size: .85rem; font-weight: 500; color: #444; }
        .form-control, .form-select { font-size: .88rem; border-color: #ddd; }
        .form-control:focus, .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 .15rem rgba(3,75,37,.15); }

        .btn-primary {
            --bs-btn-bg: var(--primary) !important;
            --bs-btn-border-color: var(--primary) !important;
            --bs-btn-hover-bg: #02381c !important;
            --bs-btn-hover-border-color: #02381c !important;
            --bs-btn-focus-shadow-rgb: 3,75,37 !important;
            --bs-btn-active-bg: #02381c !important;
            --bs-btn-active-border-color: #02381c !important;
            background-color: var(--primary) !important;
            border-color: var(--primary) !important;
        }
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active, .btn-primary.active {
            background-color: #02381c !important;
            border-color: #02381c !important;
        }

        .btn-outline-primary {
            --bs-btn-color: var(--primary) !important;
            --bs-btn-border-color: var(--primary) !important;
            --bs-btn-hover-bg: var(--primary) !important;
            --bs-btn-hover-border-color: var(--primary) !important;
            --bs-btn-active-bg: var(--primary) !important;
            --bs-btn-active-border-color: var(--primary) !important;
            color: var(--primary) !important;
            border-color: var(--primary) !important;
        }
        .btn-outline-primary:hover, .btn-outline-primary:focus, .btn-outline-primary:active, .btn-outline-primary.active {
            color: #fff !important;
            background-color: var(--primary) !important;
            border-color: var(--primary) !important;
        }

        a {
            color: var(--primary);
        }
        a:hover {
            color: #02381c;
        }

        /* Pipeline */
        .pipeline-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
        }
        .pipeline-dot {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .7rem;
            font-weight: 700;
            color: #888;
        }
        .pipeline-dot.active { background: var(--accent); color: #fff; }
        .pipeline-dot.done   { background: var(--primary); color: #fff; }
        .pipeline-line { flex: 1; height: 2px; background: #ddd; align-self: center; }

        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.open { transform: none; }
            #topbar, #main { margin-left: 0; }
        }
    </style>

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('styles')
</head>
<body>
@php
    $segments = request()->segments();
    $breadcrumbs = [];
    
    // Custom mapping for segment names
    $mapping = [
        'leads'              => 'Leads',
        'customers'          => 'Customers',
        'builders'           => 'Builders',
        'architects'         => 'Architects',
        'opportunities'      => 'Opportunities',
        'site-visits'        => 'Site Visits',
        'quotations'         => 'Quotations',
        'documents'          => 'Documents',
        'experience-centers' => 'Experience Centers',
        'reports'            => 'Reports',
        'users'              => 'Users',
        'profile'            => 'My Profile',
        'dashboard'          => 'Dashboard',
        'create'             => 'Create',
        'edit'               => 'Edit',
        'lead-source'        => 'Lead Source Performance',
        'experience-center'  => 'Experience Center Performance',
        'sales-pipeline'     => 'Sales Pipeline',
    ];

    if (count($segments) > 0 && $segments[0] !== 'dashboard') {
        $breadcrumbs[] = [
            'name' => 'Dashboard',
            'url'  => route('dashboard'),
            'active' => false
        ];
    }

    $builtUrl = '';
    foreach ($segments as $index => $segment) {
        $builtUrl .= '/' . $segment;
        $isLast = ($index === count($segments) - 1);
        
        $name = isset($mapping[$segment]) ? $mapping[$segment] : null;
        if (!$name) {
            if (is_numeric($segment)) {
                $name = '#' . $segment;
            } else {
                $name = htmlspecialchars(urldecode($segment));
                if (strlen($name) > 25) {
                    $name = substr($name, 0, 25) . '...';
                } else {
                    $name = ucwords(str_replace(['-', '_'], ' ', $name));
                }
            }
        }
        
        $breadcrumbs[] = [
            'name'   => $name,
            'url'    => url($builtUrl),
            'active' => $isLast
        ];
    }
@endphp

<!-- Sidebar -->
<nav id="sidebar">
    <div class="brand d-flex align-items-center justify-content-left py-3">
        <img src="{{ asset('Y5home_Technologies.webp') }}" alt="Y5Home Logo" style="max-height: 38px; width: auto; object-fit: contain; filter: brightness(0) invert(1);">
    </div>
    <ul class="nav flex-column mt-2">

        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard*') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>

        <div class="nav-section">Leads & Sales</div>
        <li class="nav-item">
            <a href="{{ route('leads.index') }}" class="nav-link {{ request()->routeIs('leads*') ? 'active' : '' }}">
                <i class="bi bi-person-lines-fill"></i> Leads
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Customers
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('builders.index') }}" class="nav-link {{ request()->routeIs('builders*') ? 'active' : '' }}">
                <i class="bi bi-building"></i> Builders
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('architects.index') }}" class="nav-link {{ request()->routeIs('architects*') ? 'active' : '' }}">
                <i class="bi bi-vector-pen"></i> Architects
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('opportunities.index') }}" class="nav-link {{ request()->routeIs('opportunities*') ? 'active' : '' }}">
                <i class="bi bi-graph-up-arrow"></i> Opportunities
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('site-visits.index') }}" class="nav-link {{ request()->routeIs('site-visits*') ? 'active' : '' }}">
                <i class="bi bi-geo-alt"></i> Site Visits
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('quotations.index') }}" class="nav-link {{ request()->routeIs('quotations*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i> Quotations
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('documents.index') }}" class="nav-link {{ request()->routeIs('documents*') ? 'active' : '' }}">
                <i class="bi bi-folder2-open"></i> Documents
            </a>
        </li>

        <div class="nav-section">Centers</div>
        <li class="nav-item">
            <a href="{{ route('experience-centers.index') }}" class="nav-link {{ request()->routeIs('experience-centers*') ? 'active' : '' }}">
                <i class="bi bi-building"></i> Experience Centers
            </a>
        </li>

        <div class="nav-section">Reports</div>
        <li class="nav-item">
            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart"></i> Reports
            </a>
        </li>

        @if(auth()->user()->isSuperAdmin())
        <div class="nav-section">Y5Home Connect</div>
        <li class="nav-item">
            <a href="{{ route('icons.index') }}" class="nav-link {{ request()->routeIs('icons*') ? 'active' : '' }}">
                <i class="bi bi-images"></i> Custom Icons
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('locations.index') }}" class="nav-link {{ request()->routeIs('locations*') ? 'active' : '' }}">
                <i class="bi bi-geo-alt"></i> Locations
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('frame-colors.index') }}" class="nav-link {{ request()->routeIs('frame-colors*') ? 'active' : '' }}">
                <i class="bi bi-palette"></i> Frame Colors
            </a>
        </li>

        <div class="nav-section">Admin</div>
        <li class="nav-item">
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users*') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i> Users
            </a>
        </li>
        @endif

        <div class="nav-section">Account</div>
        <li class="nav-item">
            <a href="{{ route('profile') }}" class="nav-link {{ request()->routeIs('profile*') ? 'active' : '' }}">
                <i class="bi bi-person-circle"></i> My Profile
            </a>
        </li>
        <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link w-100 text-start bg-transparent border-0">
                    <i class="bi bi-box-arrow-left"></i> Logout
                </button>
            </form>
        </li>
    </ul>
</nav>

<!-- Topbar -->
<div id="topbar">
    <button class="btn btn-sm d-md-none" onclick="document.getElementById('sidebar').classList.toggle('open')">
        <i class="bi bi-list fs-5"></i>
    </button>
    <span class="page-title">@yield('page-title', 'Dashboard')</span>
    <div class="ms-auto d-flex align-items-center gap-3">
        @if(session()->has('impersonator_user_id'))
            <form action="{{ route('users.stop-impersonate') }}" method="POST" class="d-inline m-0">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm py-1 px-2 d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                    <i class="bi bi-box-arrow-left"></i> Return to Admin
                </button>
            </form>
        @endif
        <span class="text-muted small">{{ auth()->user()->name }}</span>
        <span class="badge bg-secondary text-uppercase" style="font-size:.65rem">{{ str_replace('-', ' ', auth()->user()->role) }}</span>
    </div>
</div>

<!-- Main -->
<main id="main">
    <!-- Breadcrumbs -->
    @if(count($segments) > 0)
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb m-0" style="font-size: 0.82rem; --bs-breadcrumb-divider: '›';">
            @foreach($breadcrumbs as $bc)
                @if($bc['active'])
                    <li class="breadcrumb-item active text-muted text-truncate" aria-current="page" style="max-width: 250px;">{{ $bc['name'] }}</li>
                @else
                    <li class="breadcrumb-item"><a href="{{ $bc['url'] }}" class="text-decoration-none" style="color: var(--primary); font-weight: 500;">{{ $bc['name'] }}</a></li>
                @endif
            @endforeach
        </ol>
    </nav>
    @endif



    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Intercept forms with confirm in onsubmit
    document.body.addEventListener('submit', function(e) {
        const form = e.target;
        const onsubmitAttr = form.getAttribute('onsubmit');
        
        if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
            e.preventDefault();
            e.stopPropagation();
            
            // Extract the message
            let message = "Are you sure you want to proceed?";
            const match = onsubmitAttr.match(/confirm\(['"](.*?)['"]\)/);
            if (match && match[1]) {
                message = match[1];
            }
            
            // Remove the onsubmit attribute temporarily so we can submit the form programmatically
            form.removeAttribute('onsubmit');
            
            Swal.fire({
                title: 'Are you sure?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#034b25',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed',
                cancelButtonText: 'Cancel',
                showClass: {
                    popup: 'animate__animated animate__fadeInUp animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutDown animate__faster'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                } else {
                    // Put the attribute back
                    form.setAttribute('onsubmit', onsubmitAttr);
                }
            });
        }
    }, true);

    // 2. Intercept click events on links/buttons with confirm in onclick
    document.body.addEventListener('click', function(e) {
        const target = e.target.closest('[onclick]');
        if (target) {
            const onclickAttr = target.getAttribute('onclick');
            if (onclickAttr && onclickAttr.includes('confirm(')) {
                e.preventDefault();
                e.stopPropagation();
                
                let message = "Are you sure?";
                const match = onclickAttr.match(/confirm\(['"](.*?)['"]\)/);
                if (match && match[1]) {
                    message = match[1];
                }
                
                Swal.fire({
                    title: 'Confirmation Required',
                    text: message,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#034b25',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, proceed',
                    cancelButtonText: 'Cancel',
                    showClass: {
                        popup: 'animate__animated animate__zoomIn animate__faster'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__zoomOut animate__faster'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // If it's a link
                        if (target.tagName === 'A') {
                            window.location.href = target.getAttribute('href');
                        } else {
                            // If it's a button inside a form, submit the form
                            const form = target.closest('form');
                            if (form) {
                                form.submit();
                            }
                        }
                    }
                });
            }
        }
    }, true);

    // 3. Replace standard browser alerts with SweetAlert success/error messages
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: "{!! addslashes(session('success')) !!}",
            timer: 3000,
            timerProgressBar: true,
            confirmButtonColor: '#034b25',
            showClass: {
                popup: 'animate__animated animate__fadeInDown animate__faster'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp animate__faster'
            }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: "{!! addslashes(session('error')) !!}",
            timer: 5000,
            timerProgressBar: true,
            confirmButtonColor: '#034b25',
            showClass: {
                popup: 'animate__animated animate__shakeX animate__faster'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOut animate__faster'
            }
        });
    @endif
});
</script>
</body>
</html>
