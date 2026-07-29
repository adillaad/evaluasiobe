{{--
  Guest template.
  LANDING_LEGACY=true di .env memakai file backup di guest/backup.
--}}
@php
    $legacyLanding = filter_var(env('LANDING_LEGACY', false), FILTER_VALIDATE_BOOLEAN);
@endphp
@include($legacyLanding ? 'guest.backup.header' : 'guest.layouts.header')
@include($legacyLanding ? 'guest.backup.navbar' : 'guest.layouts.navbar')
@yield('content')
@include($legacyLanding ? 'guest.backup.footer' : 'guest.layouts.footer')
