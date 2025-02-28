@extends('admin.layout.app')

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid my-2">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Brand</h1>
      </div>
      <div class="col-sm-6 text-right">
        <a href="{{ route('brands.create') }}" class="btn btn-primary">New Brand</a>
      </div>
    </div>
  </div>
</section>
@if (session('success'))
<div class="alert alert-success">
  {{ session('success') }}
</div>
@endif
<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <div class="card">
      <form action="#" method="GET">
        <div class="card-header">
          <div class="card-title">
            <button type="button" onclick="window.location.href='{{ route('brands.index') }}'" class="btn btn-default btn-sm">
              Reset
            </button>
          </div>
          <div class="card-tools">
            <div class="input-group" style="width: 250px;">
              <input value="{{Request::get('keyword')}}" type="text" name="keyword" class="form-control float-right" placeholder="Search">
              <div class="input-group-append">
                <button type="submit" class="btn btn-default">
                  <i class="fas fa-search"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
          <thead>
            <tr>
              <th width="60">ID</th>
              <th>Name</th>
              <th>Slug</th>
              <th width="100">Status</th>
              <th width="150">Action</th>
            </tr>
          </thead>
          <tbody>
            @if($brands->isNotEmpty())
            @foreach($brands as $brand)
            <tr>
              <td>{{ $brand->id }}</td>
              <td>{{ $brand->name }}</td>
              <td>{{ $brand->slug }}</td>
              <td>
                @if($brand->status == 1)
                <svg class="text-success h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                @else
                <svg class="text-danger h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                @endif
              </td>
              <td>
                <a href="{{route('brands.edit',$brand->id)}}" class="btn btn-sm btn-info">Edit</a>
                <button type="button" class="btn btn-sm btn-danger" onclick="deletebrands({{ $brand->id }})">
                  Delete
                </button>
                </form>
              </td>
            </tr>
            @endforeach
            @else
            <tr>
              <td colspan="5" class="text-center">No categories found.</td>
            </tr>
            @endif
          </tbody>
        </table>
      </div>

      <div class="card-footer clearfix">
        {{ $brands->links() }}
      </div>
    </div>
  </div>
</section>
@endsection

@section('customJs')
<script>
function deletebrands(id) {
    var url = '{{ route("brands.delete", ":id") }}';
    var newUrl = url.replace(':id', id);

    if (confirm('Are you sure you want to delete this brand?')) {
        $.ajax({
            url: newUrl,
            type: "POST",
            data: {
                _method: "DELETE",
                _token: $('meta[name="csrf-token"]').attr('content') 
            },
            dataType: "json",
            success: function(response) {
                if (response.status) {
                    window.location.href=response.redirect;
                } else {
                    alert("Error deleting brand.");
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                alert("Something went wrong. Please try again.");
            }
        });
    }
}

</script>
@endsection