{{--
    Right side of the top bar on every page: the notification bell and who is signed in
    (a faculty member's program under the name, everyone else's email).
    Usage: @include('partials.topnav-user') — Program Head pages pass ['switcher' => true]
    to show the program switcher first.
--}}
@if($switcher ?? false)
    @include('partials.program-switcher')
@endif
@include('partials.notification-bell')
<div class="user-info">
    <div class="user-text">
        <div class="user-name">{{ auth()->user()->name }}</div>
        <div class="user-email">{{ auth()->user()->topnav_subtitle }}</div>
    </div>
    <div class="user-avatar">{{ auth()->user()->initials }}</div>
</div>
