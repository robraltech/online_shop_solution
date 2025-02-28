@extends('admin.layout.app')

@section('content')
<section class="content-header">
  <div class="container-fluid my-2">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Create Sub Category</h1>
      </div>
      <div class="col-sm-6 text-right">
        <a href="{{ route('sub-category.index') }}" class="btn btn-primary">Back</a>
      </div>
    </div>
  </div>
  <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
  <!-- Default box -->
  <div class="container-fluid">
    <form action="" name="subCategoryForm" id="subCategoryForm">
      @csrf
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-md-12">
              <div class="mb-3">
                <label for="category_id">Category</label>
                <select name="category_id" id="category_id" class="form-control" required>
                  <option value="">Select Category</option>
                  @if($categories->isNotEmpty())
                  @foreach($categories as $category)
                  <option value="{{ $category->id }}">{{ $category->name }}</option>
                  @endforeach
                  @endif
                </select>
                <p class="text-danger" id="categoryError"></p>
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-3">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Name">
                <p></p>
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-3">
                <label for="slug">Slug</label>
                <input type="text" readonly name="slug" id="slug" class="form-control" placeholder="Slug">
                <p></p>
              </div>
            </div>

            <div class="col-md-3">
              <div class="mb-3">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control">
                  <option value="1">Active</option>
                  <option value="0">Inactive</option>
                </select>
                <p></p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="pb-5 pt-3">
        <button type="submit" class="btn btn-primary">Create</button>
        <a href="subcategory.html" class="btn btn-outline-dark ml-3">Cancel</a>
      </div>
    </form>
  </div>

</section>
@endsection

@section('customJs')
<script>
  $(document).ready(function() {
    $("#subCategoryForm").submit(function(e) {
      e.preventDefault();
      var element = $(this);

      $.ajax({
        url: "{{ route('sub-category.store') }}",
        type: "POST",
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

            // Handle validation errors
            if (errors.name) {
              $("#name").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.name[0]);
            } else {
              $("#name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
            }

            if (errors.slug) {
              $("#slug").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.slug[0]);
            } else {
              $("#slug").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
            }

            if (errors.category_id) {
              $("#category_id").addClass('is-invalid');
              $("#categoryError").html(errors.category_id[0]);
            } else {
              $("#category_id").removeClass('is-invalid');
              $("#categoryError").html('');
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