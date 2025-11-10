@extends('layouts.template')

@section('title', 'Warehouse Grid')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/grid.css') }}?v={{ time() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="warehouse-id" content="{{ $warehouse->id }}">
@endsection

@section('content')

    <div class="container">
        <h2>{{ $warehouse->warehouse_name }}</h2>

        <!-- Create Section Button -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSectionModal">
            Create Section
        </button>

        <!-- Modal for Creating Section -->
        <div class="modal fade" id="createSectionModal" tabindex="-1" aria-labelledby="createSectionModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createSectionModalLabel">Create New Section</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" class="form-control" id="sectionName" placeholder="Enter section name">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="createSectionBtn">Create</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Display Sections with Products -->
        <div class="section-list mt-4">
            @foreach ($warehouse->sections as $section)
                <div class="section-container" data-section-id="{{ $section->id }}">
                    <div class="section-header">
                        <h3 class="section-name">{{ $section->section_name }}</h3>
                        <div class="section-actions">
                            <button class="btn-add-product" data-section-id="{{ $section->id }}" data-bs-toggle="modal"
                                data-bs-target="#createProductModal">
                                + Add Product
                            </button>
                            <button class="delete-section-btn" data-section-id="{{ $section->id }}">×</button>
                        </div>
                    </div>

                    <div class="product-list" data-section-id="{{ $section->id }}">
                        @foreach ($section->products as $product)
                            <div class="product-item" data-product-id="{{ $product->id }}">
                                <div class="product-info">
                                    <span class="product-name">{{ $product->product_name }}</span>
                                    @if ($product->sku)
                                        <span class="product-sku">SKU: {{ $product->sku }}</span>
                                    @endif
                                    <span class="product-quantity">Qty: {{ $product->quantity }}</span>
                                </div>
                                <button class="delete-product-btn" data-section-id="{{ $section->id }}"
                                    data-product-id="{{ $product->id }}">×</button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Modal for Creating Product -->
        <div class="modal fade" id="createProductModal" tabindex="-1" aria-labelledby="createProductModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createProductModalLabel">Add New Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="productSectionId">
                        <div class="mb-3">
                            <label for="productName" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="productName" placeholder="Enter product name">
                        </div>
                        <div class="mb-3">
                            <label for="productSku" class="form-label">SKU (Optional)</label>
                            <input type="text" class="form-control" id="productSku" placeholder="Enter SKU">
                        </div>
                        <div class="mb-3">
                            <label for="productQuantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="productQuantity" placeholder="Enter quantity"
                                value="0" min="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="createProductBtn">Add Product</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection

@section('scripts')
    <script src="{{ asset('js/grid.js') }}?v={{ time() }}"></script>
@endsection
