<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>@yield('title')</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="icon" href="{{ asset('assets/images/logo1.png') }}">

<link rel="stylesheet" href="{{ asset('css/dashboard_cabang.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

@stack('styles')

</head>

<body>

<div class="layout">

    {{-- SIDEBAR --}}
    @include('layouts.sidebar')

    <main class="main">

        {{-- NAVBAR --}}
        @include('layouts.navbar')

        {{-- ISI HALAMAN --}}
        <div class="content-wrapper">
            @yield('content')
        </div>

    </main>

</div>

{{-- ✅ SCRIPT GLOBAL --}}
<script>
const sidebar = document.getElementById('sidebar');
const toggleBtn = document.getElementById('toggleBtn');

// ✅ Load state saat pertama kali halaman dibuka
if (localStorage.getItem('sidebar-collapsed') === 'true') {
  sidebar.classList.add('collapsed');
}

// ✅ Saat tombol diklik
if (toggleBtn) {
  toggleBtn.onclick = () => {
    sidebar.classList.toggle('collapsed');

    // simpan state ke localStorage
    localStorage.setItem(
      'sidebar-collapsed',
      sidebar.classList.contains('collapsed')
    );
  };
}

// ================= MENU =================
const menuTitles = document.querySelectorAll('.menu-title');

// LOAD STATE SAAT HALAMAN DIBUKA
menuTitles.forEach((title, index) => {
  let isOpen = localStorage.getItem('menu_' + index) === 'true';

  if (isOpen) {
    title.classList.add('open');

    let next = title.nextElementSibling;
    while (next && !next.classList.contains('menu-title')) {
      next.style.display = 'flex';
      next = next.nextElementSibling;
    }
  } else {
    let next = title.nextElementSibling;
    while (next && !next.classList.contains('menu-title')) {
      next.style.display = 'none';
      next = next.nextElementSibling;
    }
  }
});

// CLICK EVENT
menuTitles.forEach((title, index) => {
  title.addEventListener('click', () => {
    if (sidebar.classList.contains('collapsed')) return;

    let open = title.classList.toggle('open');

    // SIMPAN KE STORAGE
    localStorage.setItem('menu_' + index, open);

    let next = title.nextElementSibling;
    while (next && !next.classList.contains('menu-title')) {
      next.style.display = open ? 'flex' : 'none';
      next = next.nextElementSibling;
    }
  });
});

// ================= USER DROPDOWN =================
const userBtn = document.getElementById('userBtn');
const dropdownMenu = document.getElementById('dropdownMenu');

if (userBtn) {
  userBtn.addEventListener('click', e => {
    e.stopPropagation();
    dropdownMenu.classList.toggle('show');
  });
}

document.addEventListener('click', () => {
  dropdownMenu?.classList.remove('show');
});
</script>
@stack('scripts')

</body>
</html>
