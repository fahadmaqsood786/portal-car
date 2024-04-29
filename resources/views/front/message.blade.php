@if(Session::has('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
	{{ Session::get('success')}}
</div>
@endif

@if(Session::has('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
	{{ Session::get('error')}}

</div>
@endif