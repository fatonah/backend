@if(count($errors))
<div class="alert alert-danger">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
  <h3 class="text-danger"><i class="fa fa-check-circle"></i> Error</h3> 
  <ul>
    @foreach($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif

@if(session()->has('message'))

<div class="alert alert-success">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
  <h3 class="text-success"><i class="fa fa-check-circle"></i> Success</h3> 
  {{ session()->get('message') }}
</div>
@endif

@if (Session::has('error'))
<div class="alert alert-danger">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
  {{Session::get('error')}}
</div>
@endif

@if (Session::has('error_password'))
<div class="alert alert-danger">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">�</span> </button>
 {{Session::get('error_password')}}
</div>
@endif