@extends('admin.layout.app')

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Products</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('products.create') }}" class="btn btn-primary">New Product</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
@if (session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
        <div class="card">
            <form action="#" method="GET">
				<div class="card-header">
					<div class="card-title">
						<button type="button" onclick="window.location.href='{{ route('products.index') }}'" class="btn btn-default btn-sm">
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
                            <th width="80"></th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>SKU</th>
                            <th width="100">Status</th>
                            <th width="100">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($products->isNotEmpty())
						@foreach($products as $product)
                        @php
                         $productImage=$product->product_images->first();

                        @endphp
						<tr>
							<td>{{ $product->id }}</td>
							<td>
                                @if(!empty($productImage->image))
                                <img src="{{ asset('uploads/products/large/'.$productImage->image) }}" class="img-thumbnail" alt="Product Image" style="width: 80px;">
                                @else
                                <img src="{{ asset('admin-asset/img/default-150x150.png') }}" class="img-thumbnail" alt="Product Image" style="width: 80px;">
                                @endif
                            </td>
							<td>{{ $product->title }}</td>
							<td>${{ $product->price }}</td>
							<td>{{ $product->qty }} left in Stock</td>
							<td>SKU-{{ $product->sku }} </td>
							<td>
								@if($product->status == 1)
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
								<a href="{{route('products.edit',$product->id)}}" class="btn btn-sm btn-info">Edit</a>
								<form action="{{ route('products.delete', $product->id) }}" method="POST" style="display:inline;">
									@csrf
									@method('DELETE')
									<button type="submit" class="btn btn-sm btn-danger" onclick="deleteProduct">
										Delete
									</button>
								</form>
							</td>
						</tr>
						@endforeach
						@else
						<tr>
							<td colspan="5" class="text-center">No Products found.</td>
						</tr>
						@endif
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
               {{$products->links()}}
            </div>
        </div>
    </div>
    <!-- /.card -->
</section>
@endsection

@section('customJs')
<script>
function deleteProduct(id) {
    var url = '{{ route("products.delete", ":id") }}';
    var newUrl = url.replace(':id', id);

    if (confirm('Are you sure you want to delete this category?')) {
        $.ajax({
            url: newUrl,
            type: "DELETE",
            data: {},
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $("button[type=submit]").prop('disabled', false);

                if (response.status === 'success') {
                    window.location.href = "route('products.index')";
                } else {
                    alert("Error deleting category.");
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
