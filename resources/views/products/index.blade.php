@extends('layouts.app')

@push('style')
    <link href="{{ asset('select2/select2.min.css') }}" rel="stylesheet"/>
@endpush

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>


    <div class="card">
        <form action="{{ route('product.index') }}" method="get" class="card-header">
            <div class="form-row justify-content-between">

                <div class="col-md-2">
                    <input type="text" name="title" value="{{ \Request::get('title') ?? '' }}"
                           placeholder="Product Title" class="form-control">
                </div>

                <div class="col-md-2">

                    <select name="variantId" id="variantId" class="form-control select2">

                        @foreach($variants as $variant)

                            <option value="" selected disabled hidden>select one</option>

                            <optgroup label="{{ $variant->title }}">
                                @foreach($variant->productVariants as $productVariant)
                                    <option
                                        value="{{ $productVariant->id }}" {{ (\Request::get('variantId') == $productVariant->id) ? 'selected' : '' }}>{{ $productVariant->variant }}</option>
                                @endforeach
                            </optgroup>

                        @endforeach

                    </select>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" name="price"  value="{{ \Request::get('price') ?? '' }}" aria-label="price_from" placeholder="500-600"
                               class="form-control">
{{--                        <input type="text" name="price_to" aria-label="Last name" placeholder="To" class="form-control">--}}
                    </div>
                </div>

                <div class="col-md-2">
                    <input type="date" name="date" value="{{ \Request::get('date') ?? '' }}" placeholder="Date"
                           class="form-control">
                </div>

                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-response">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Variant</th>
                        <th width="150px">Action</th>
                    </tr>
                    </thead>

                    <tbody>


                    @forelse($products as $key=>$product)

                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $product->title }} <br> Created at :
                                {{ \Carbon\Carbon::parse($product->created_at)->format('d-M-Y') }}</td>
                            <td>{!! $product->description !!}</td>
                            <td>
                                <dl class="row mb-0" style="height: 80px; overflow: hidden" class="variant{{ $key }}">
                                    @foreach($product->productVariants as $productVariant)
                                        <dt class="col-sm-3 pb-0">

                                            {{ $productVariant->variantDetails->title ?? '' }}
                                            / {{ $productVariant->variant }}

                                        </dt>

                                        <dd class="col-sm-9">
                                            <dl class="row mb-0">
                                                <dt class="col-sm-4 pb-0">Price
                                                    : {{ number_format(($productVariant->productVariantPrice->price ?? '0.00'),2) }}</dt>
                                                <dd class="col-sm-8 pb-0">InStock
                                                    : {{ number_format(($productVariant->productVariantPrice->stock ?? '0.00'),2) }}</dd>
                                            </dl>
                                        </dd>

                                    @endforeach

                                </dl>
                                <button onclick="$('.variant{{ $key }}').toggleClass('h-auto')" class="btn btn-sm btn-link">Show
                                    more
                                </button>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('product.edit', $product->id) }}" class="btn btn-success">Edit</a>
                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No data Found</td>
                        </tr>
                    @endforelse

                    </tbody>

                </table>
            </div>

        </div>

        <div class="card-footer">
            <div class="row justify-content-between">

                <div class="col-md-6">
                    <p>Showing {{ $products->firstItem() ?? '' }} to {{ $products->lastItem() }} out
                        of {{ $products->total() }}</p>
                </div>
                <div class="col-md-2">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script src="{{ asset('select2/select2.min.js') }}"></script>
    <script>
        // In your Javascript (external .js resource or <script> tag)
        $(document).ready(function () {
            $('.select2').select2();
        });
    </script>
@endpush
