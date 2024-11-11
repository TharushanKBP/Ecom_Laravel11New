@extends('layouts.admin')
@section('content')

<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap10 mb-27">
            <h3>Brand Information</h3>
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
                    <a href="{{ route('admin.brand.add') }}">
                        <div class="text-tiny">Brands</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">New Brand</div>
                </li>
            </ul>
        </div>

        <!-- New Brand Form -->
        <div class="wg-box">
            <form class="form-new-product form-style-1" action="{{ route('admin.brand.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Brand Name Field -->
                <fieldset class="name">
                    <div class="body-title">Brand Name <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Brand name" name="name" value="{{ old('name') }}" aria-label="Brand name" required>
                </fieldset>
                @error('name')
                <span class="alert alert-danger text-center">{{ $message }}</span>
                @enderror

                <!-- Brand Slug Field -->
                <fieldset class="name">
                    <div class="body-title">Brand Slug <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Brand Slug" name="slug" value="{{ old('slug') }}" aria-label="Brand slug" required>
                </fieldset>
                @error('slug')
                <span class="alert alert-danger text-center">{{ $message }}</span>
                @enderror

                <!-- Image Upload -->
                <fieldset>
                    <div class="body-title">Upload Image <span class="tf-color-1">*</span></div>
                    <div class="upload-image flex-grow">
                        <div class="item" id="imagepreview" style="display:none">
                            <img src="" class="effect8" alt="Preview Image">
                        </div>
                        <div id="upload-file" class="item up-load">
                            <label class="uploadfile" for="myFile">
                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>
                                <span class="body-text">Drop your images here or select <span class="tf-color">click to browse</span></span>
                                <input type="file" id="myFile" name="image" accept="image/*" aria-label="Brand image" required>
                            </label>
                        </div>
                    </div>
                </fieldset>
                @error('image')
                <span class="alert alert-danger text-center">{{ $message }}</span>
                @enderror

                <!-- Submit Button -->
                <div class="bot">
                    <button class="tf-button w208" type="submit">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(function() {
        // Image Preview
        $("#myFile").on("change", function(e) {
            const [file] = this.files;
            if (file) {
                // Validate file size (2MB limit)
                if (file.size > 2048 * 1024) {
                    alert("File size should not exceed 2MB.");
                    $(this).val(''); // Clear the input
                    return;
                }

                // Validate file type (PNG, JPG, JPEG)
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!validTypes.includes(file.type)) {
                    alert("Invalid file type. Please upload a PNG, JPG, or JPEG image.");
                    $(this).val(''); // Clear the input
                    return;
                }

                $("#imagepreview img").attr('src', URL.createObjectURL(file));
                $("#imagepreview").show();
            }
        });

        // Slug Generation
        $("input[name='name']").on("input", function() {
            $("input[name='slug']").val(stringToSlug($(this).val()));
        });
    });

    // Convert Text to Slug
    function stringToSlug(text) {
        return text.toLowerCase()
            .replace(/[^\w ]+/g, "")
            .replace(/ +/g, "-");
    }
</script>
@endpush