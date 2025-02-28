@extends('admin.layout.app')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid my-2">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Edit Brand</h1>
      </div>
      <div class="col-sm-6 text-right">
        <a href="{{route('brands.index')}}" class="btn btn-primary">Back</a>
      </div>
    </div>
  </div>
  <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
  <!-- Default box -->
  <div class="container-fluid">
    <form action="#" method="post" id="UpdateBrandsForm" name="UpdateBrandsForm">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Name" value="{{ $brand->name }}">
                <p class="text-danger" id="nameError"></p>
              </div>

            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="email">Slug</label>
                <input type="text" readonly name="slug" id="slug" class="form-control" placeholder="Slug" value="{{ $brand->slug }}">
                <p class="text-danger" id="slugError"></p>
              </div>
            </div>

            <div class="col-md-3">
              <div class="mb-3">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control">
                  <option {{($brand->status==1) ? 'selected' : ''}} value="1">Active</option>
                  <option {{($brand->status==0) ? 'selected' : ''}} value="0">Inactive</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="pb-5 pt-3">
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{route('brands.index')}}" class="btn btn-outline-dark ml-3">Cancel</a>
      </div>
    </form>
  </div>
  <!-- /.card -->
</section>
@endsection

@section('customJs')
<script>
  $(document).ready(function() {
    $("#UpdateBrandsForm").submit(function(e) {
      e.preventDefault();
      var element = $(this);

      $.ajax({
        url: "{{ route('brands.update',$brand->id) }}",
        type: "Put",
        data: element.serialize(),
        dataType: "json",
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          if (response.status === true) {
            window.location.href = response.redirect;
          } else {
            var errors = response.message;


            if (errors.name) {
              $("#name").addClass('is-invalid');
              $("#nameError").html(errors.name[0]);
            } else {
              $("#name").removeClass('is-invalid');
              $("#nameError").html('');
            }
            if (errors.slug) {
              $("#slug").addClass('is-invalid');
              $("#slugError").html(errors.slug[0]);
            } else {
              $("#slug").removeClass('is-invalid');
              $("#slugError").html('');
            }
          }
        },
        error: function(xhr, status, error) {
          console.error("AJAX Error:", xhr.responseText);
          alert("Something went wrong. Please try again.");
        }
      });
    });

    // Auto-generate slug when name is entered
    $("#name").on("input", function() {
      var element = $(this);

      $.ajax({
        url: "{{ route('getSlug') }}",
        type: "GET",
        data: {
          name: element.val()
        },
        dataType: "json",
        success: function(response) {
          if (response.status === true) {
            $("#slug").val(response.slug.toLowerCase());
          }
        },
        error: function() {
          console.log("Error generating slug");
        }
      });
    });
  });
  var element = $(this);

  $.ajax({
    url: "{{ route('getSlug') }}",
    type: "GET",
    data: {
      name: element.val()
    },
    dataType: "json",
    success: function(response) {
      if (response.status === true) {
        $("#slug").val(response.slug.toLowerCase());
      }
    },
    error: function() {
      console.log("Error generating slug");
    }
  });
</script>
@endsection