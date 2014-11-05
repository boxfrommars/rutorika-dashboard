@if (Session::has('flash_notification'))
<div class="alert alert-{{ Session::get('flash_notification.level') }}">
    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
    {{ Session::get('flash_notification.message') }}
</div>
@endif