@extends('admin.layout.app')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Product</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('products.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <form action="" method="post" id="productsForm" name="productsForm">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="title">Title</label>
                                            <input type="text" name="title" id="title" class="form-control"
                                                placeholder="Title" value="{{ $product->title }}">
                                            @error('title')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="slug">Slug</label>
                                            <input type="text" readonly name="slug" id="slug"
                                                class="form-control" placeholder="Slug" value="{{ $product->slug }}">
                                            @error('slug')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="description">Description</label>
                                            <textarea name="description" id="description" cols="30" rows="10" class="summernote"
                                                placeholder="Description">{{ $product->description }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Media</h2>
                                <div id="image" class="dropzone dz-clickable">
                                    <div class="dz-message needsclick">
                                        <br>Drop files here or click to upload.<br><br>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="product-gallery">
                            @if ($productImage->isNotEmpty())
                                @foreach ($productImage as $image)
                                    <div class="col-md-3" id="image-row-{{ $image->id }}">
                                        <div class="card">
                                            <input type="hidden" name="images_array[]" value="{{ $image->id }}">
                                            <img src="{{ asset('uploads/products/large/' . $image->image) }}"
                                                class="card-img-top" alt="">
                                            <div class="card-body">
                                                <a href="javascript:void(0)" onclick="deleteImage({{ $image->id }})"
                                                    class="btn btn-danger">Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif


                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Pricing</h2>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="price">Price</label>
                                            <input type="text" name="price" id="price" class="form-control"
                                                placeholder="Price" value="{{ $product->price }}">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="compare_price">Compare at Price</label>
                                            <input type="text" name="compare_price" id="compare_price"
                                                class="form-control" placeholder="Compare Price"
                                                value="{{ $product->compare_price }}">
                                            <p class="text-muted mt-3">
                                                To show a reduced price, move the product’s original price into Compare at
                                                price. Enter a lower value into Price.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Inventory</h2>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sku">SKU (Stock Keeping Unit)</label>
                                            <input type="text" name="sku" id="sku" class="form-control"
                                                placeholder="sku" value="{{ $product->sku }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="barcode">Barcode</label>
                                            <input type="text" name="barcode" id="barcode" class="form-control"
                                                placeholder="Barcode" value="{{ $product->barcode }}">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="custom-control custom-checkbox">
                                                <input type="hidden" name="track_qty" value="No">
                                                <input class="custom-control-input" type="checkbox" id="track_qty"
                                                    name="track_qty" value="Yes"
                                                    {{ $product->track_qty == 'Yes' ? 'checked' : '' }}>
                                                <label for="track_qty" class="custom-control-label">Track Quantity</label>
                                                @error('track_qty')
                                                    <p class="text-danger">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <input type="number" min="0" name="qty" id="qty"
                                                class="form-control" placeholder="Qty" value="{{ $product->qty }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Product status</h2>
                                <div class="mb-3">
                                    <select name="is_featured" id="is_featured" class="form-control">
                                        <option {{ $product->status == '0' ? 'selected' : '' }} value="0">No</option>
                                        <option {{ $product->status == '1' ? 'selected' : '' }} value="1">Yes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h2 class="h4  mb-3">Product category</h2>
                                <div class="mb-3">
                                    <label for="category">Category</label>
                                    <select name="category" id="category" class="form-control">
                                        <option value="">Select a Category</option>
                                        @if ($categories->isNotEmpty())
                                            @foreach ($categories as $category)
                                                <option {{ $product->category_id == $category->id ? 'selected' : '' }}
                                                    value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('category')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="category">Sub category</label>
                                    <select name="sub_category" id="sub_category" class="form-control">
                                        <option value="">Select a subCategory </option>
                                        @if ($subCategories->isNotEmpty())
                                            @foreach ($subCategories as $subCategory)
                                                <option
                                                    {{ $product->sub_category_id == $subCategory->id ? 'selected' : '' }}
                                                    value="{{ $subCategory->id }}">{{ $subCategory->name }}</option>
                                            @endforeach
                                        @endif

                                    </select>
                                    @error('sub_category')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Product brand</h2>
                                <div class="mb-3">
                                    <select name="brands" id="brands" class="form-control">
                                        <option value="">Select a Brands</option>
                                        @if ($brands->isNotEmpty())
                                            @foreach ($brands as $brand)
                                                <option {{ $product->brand_id == $brand->id ? 'selected' : '' }}
                                                    value="{{ $brand->id }}">{{ $brand->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('brands')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Featured product</h2>
                                <div class="mb-3">
                                    <select name="status" id="status" class="form-control">
                                        <option {{ $product->is_featured == '0' ? 'selected' : '' }} value="0">No
                                        </option>
                                        <option {{ $product->is_featured == '1' ? 'selected' : '' }} value="1">Yes
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>

            </div>
        </form>
        <!-- /.card -->
    </section>
    <!-- /.content -->

    <!-- /.content-wrapper -->
@endsection

@section('customJs')
    <script>
        $(document).ready(function() {
            // Handle product form submission
            $("#productsForm").submit(function(e) {
                e.preventDefault();
                var formArray = $(this).serializeArray();

                $.ajax({
                    url: "{{ route('products.update', $product->id) }}",
                    type: "PUT",
                    data: formArray,
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $("#submitBtn").prop("disabled", true); // Disable submit button
                    },
                    success: function(response) {
                        if (response.status === true) {
                            window.location.href = response.redirect;
                        } else {
                            var errors = response.message;

                            // Reset error states
                            $(".form-control").removeClass('is-invalid');
                            $(".text-danger").html('');

                            // Loop through errors and apply them to fields
                            $.each(errors, function(field, messages) {
                                $("#" + field).addClass('is-invalid');
                                $("#" + field + "Error").html(messages[0]);
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error("AJAX Error:", xhr.responseText);
                        alert("Something went wrong. Please try again.");
                    },
                    complete: function() {
                        $("#submitBtn").prop("disabled", false); // Re-enable submit button
                    }
                });
            });

            // Debounce function to optimize slug generation requests
            function debounce(func, delay) {
                let timer;
                return function(...args) {
                    clearTimeout(timer);
                    timer = setTimeout(() => func.apply(this, args), delay);
                };
            }

            // Auto-generate slug when title is entered (optimized with debounce)
            $("#title").on("input", debounce(function() {
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
            }, 500)); // Debounced for 500ms

            // Fetch subcategories based on selected category
            $("#category").change(function() {
                var category_id = $(this).val();

                $.ajax({
                    url: "{{ route('product-subcategories.index') }}",
                    type: "GET",
                    data: {
                        category_id: category_id
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.status === true) {
                            console.log(response);

                            // Clear previous options except the first one
                            $('#sub_category').find('option').not(':first').remove();

                            // Append new subcategories
                            $.each(response.subCategories, function(index, value) {
                                $('#sub_category').append('<option value="' + value.id +
                                    '">' + value.name + '</option>');
                            });
                        }
                    },
                    error: function() {
                        console.log("Something went wrong.");
                    }
                });
            });
        });

        // Dropzone configuration
        Dropzone.autoDiscover = false;
        const dropzone = new Dropzone("#image", {
            url: "{{ route('product-image.update') }}",
            maxFiles: 1,
            paramName: 'image',
            addRemoveLinks: true,
            acceptedFiles: "image/jpeg,image/png,image/gif",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            init: function() {
                this.on('sending', function(file, xhr, formData) {
                    formData.append("id", "{{ $product->id }}"); // Append product ID here
                });

                this.on("success", function(file, response) {
                    var html = `
                <div class="col-md-3" id="image-row-${response.image_id}">
                    <div class="card">
                        <input type="hidden" name="images_array[]" value="${response.image_id}">
                        <img src="${response.ImagePath}" class="card-img-top" alt="">
                        <div class="card-body">
                            <a href="javascript:void(0)" onclick="deleteImage(${response.image_id})" class="btn btn-danger">Delete</a>
                        </div>
                    </div>
                </div>`;
                    $("#product-gallery").append(html);
                });

                this.on("complete", function(file) {
                    this.removeFile(file);
                });
            }
        });

        // Function to delete image
        function deleteImage(id) {
            $("#image-row-" + id).remove();
            if (('Are You sure you want to delete Image ? ')) {
                $.ajax({
                    url: "{{route('product-image.destroy')}}",
                    type: 'delete',
                    data: {
                        id: id
                    },
                    success: function(response) {
                        if (response.status == true) {
                            alert(response.message);
                        } else {
                            alert(respone.message);
                        }

                    }
                });
            }
        }
    </script>
@endsection
