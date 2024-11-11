@extends('layouts.admin')
@section('content')

<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Categories</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('admin.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">Categories</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <form class="form-search" method="GET" action="{{ route('admin.brands') }}">
                        <fieldset class="name">
                            <input type="text" placeholder="Search here..." name="name" value="{{ request('name') }}" tabindex="2" aria-required="true">
                        </fieldset>
                        <div class="button-submit">
                            <button type="submit"><i class="icon-search"></i></button>
                        </div>
                    </form>
                </div>
                <a class="tf-button style-1 w208" href="#"><i class="icon-plus"></i>Add new</a>
            </div>
            <div class="wg-table table-all-user">
                <div class="table-responsive">
                    @if(Session::has('status'))
                    <p id="statusMessage" class="alert alert-success">{{ Session::get('status') }}</p>
                    @endif

                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Products</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td class="pname">
                                    <div class="image">
                                        <img src="{{ asset('uploads/brands')}}/{{$category->image}}" alt="{{ $category->name }}" class="image">
                                    </div>
                                    <div class="name">
                                        <a href="#" class="body-title-2">{{ $category->name }}</a>
                                    </div>
                                </td>
                                <td>{{ $category->slug }}</td>
                                <td><a href="#" target="_blank">{{ $category->products ? $category->products->count() : 0 }}</a></td>

                                <td>
                                    <div class="list-icon-function">
                                        <a href="#">
                                            <div class="item edit">
                                                <i class="icon-edit-3"></i>
                                            </div>
                                        </a>
                                        <form class="delete-form" action="#" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="item text-danger delete">
                                                <i class="icon-trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="divider"></div>
                <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                    {{ $categories->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(function() {
        $('.delete').on('click', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            swal({
                title: "Are you sure?",
                text: "You want to delete this record?",
                icon: "warning",
                buttons: ["No", "Yes"],
                dangerMode: true,
            }).then(function(result) {
                if (result) {
                    form.submit();
                }
            });
        });
    });
</script>

<script>
    $(function() {
        // Automatically hide the status message after 5 seconds
        setTimeout(function() {
            $('#statusMessage').fadeOut('slow');
        }, 5000);

        $('.delete').on('click', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            swal({
                title: "Are you sure?",
                text: "You want to delete this record?",
                icon: "warning",
                buttons: ["No", "Yes"],
                dangerMode: true,
            }).then(function(result) {
                if (result) {
                    form.submit();
                }
            });
        });
    });
</script>

@endpush