@extends('backend.layouts.master')
@section('title', request()->id ? 'Update package' : 'Create new package')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page_title_box d-flex align-items-center justify-content-between">
                <div class="page_title_left">
                    <h3 class="f_s_30 f_w_700 text_white">{{ request()->id ? 'Update ' : 'Create new' }} package</h3>
                    <ol class="breadcrumb page_bradcam mb-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ $company->name }} </a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Package</a></li>
                        <li class="breadcrumb-item active">{{ request()->id ? 'Update' : 'Create' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">

        <div class="col-lg-12">
            <div class="white_card card_height_100 mb_30">
                <div class="white_card_body">
                    <div class="card-body">
                        <form
                            action="{{ request()->id ? route('packages.storeOrUpdate', request()->id) : route('packages.storeOrUpdate') }}"
                            method="post" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="" value="{{ request()->ref }}">

                            <div class="form-group mb-3">
                                <label class="form-label">Package Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" placeholder="Enter package name"
                                    value="{{ $package->name ?? 'a' }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">What student will learn<span class="text-danger">*</span></label>
                                <textarea name="details" class="form-control summernote" rows="10" required>{!! $package->details ?? 'a' !!}</textarea>
                            </div>
                            {{-- {{ json_encode($package->permission['BCS']) }} --}}
                            <div class="card mb-3">
                                <div class="card-header">
                                    Permission
                                </div>
                                <div class="card-body">
                                    <div class="card p-3 mb-3">
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" name="permission[BCS]" value="true"
                                                class="form-check-input" id="BCS"
                                                {{ isset($package) && multiKeyExists($package->permission, 'BCS') ? 'checked' : '' }}>
                                            <label class="form-label form-check-label" for="BCS">BCS</label>
                                            <div class="form-check">
                                                <input type="checkbox" name="permission[BCS][Preliminary]" value="true"
                                                    {{ isset($package) && isset($package->permission['BCS']) && multiKeyExists($package->permission['BCS'], 'Preliminary') ? 'checked' : '' }}
                                                    class="form-check-input" id="BCS_Preliminary">
                                                <label class="form-label form-check-label"
                                                    for="BCS_Preliminary">Preliminary</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" name="permission[BCS][Written]" value="true"
                                                    {{ isset($package) && isset($package->permission['BCS']) && multiKeyExists($package->permission['BCS'], 'Written') ? 'checked' : '' }}
                                                    class="form-check-input" id="BCS_Written">
                                                <label class="form-label form-check-label" for="BCS_Written">Written</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card p-3 mb-3">
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" name="permission[Bank]" value="true"
                                                class="form-check-input" id="Bank"
                                                {{ isset($package) && multiKeyExists($package->permission, 'Bank') ? 'checked' : '' }}>
                                            <label class="form-label form-check-label" for="Bank">Bank</label>
                                            <div class="form-check">
                                                <input type="checkbox" name="permission[Bank][Preliminary]" value="true"
                                                    {{ isset($package) && isset($package->permission['Bank']) && multiKeyExists($package->permission['Bank'], 'Preliminary') ? 'checked' : '' }}
                                                    class="form-check-input" id="Bank_Preliminary">
                                                <label class="form-label form-check-label"
                                                    for="Bank_Preliminary">Preliminary</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" name="permission[Bank][Written]" value="true"
                                                    {{ isset($package) && isset($package->permission['Bank']) && multiKeyExists($package->permission['Bank'], 'Written') ? 'checked' : '' }}
                                                    class="form-check-input" id="Bank_Written">
                                                <label class="form-label form-check-label"
                                                    for="Bank_Written">Written</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card p-3 mb-3">
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" name="permission[Others]" value="true"
                                                class="form-check-input" id="Others"
                                                {{ isset($package) && multiKeyExists($package->permission, 'Others') ? 'checked' : '' }}>
                                            <label class="form-label form-check-label" for="Others">Others</label>
                                            <div class="form-check">
                                                <input type="checkbox" name="permission[Others][Primary]"
                                                    value="true"{{ isset($package) && isset($package->permission['Others']) && multiKeyExists($package->permission['Others'], 'Primary') ? 'checked' : '' }}
                                                    class="form-check-input" id="Others_Primary">
                                                <label class="form-label form-check-label"
                                                    for="Others_Primary">Primary</label>

                                                <div class="form-check">
                                                    <input type="checkbox" name="permission[Others][Primary][Preliminary]"
                                                        {{ isset($package) && isset($package->permission['Others']['Primary']) && multiKeyExists($package->permission['Others']['Primary'], 'Preliminary') ? 'checked' : '' }}
                                                        value="true" class="form-check-input"
                                                        id="Others_primary_Preliminary">
                                                    <label class="form-label form-check-label"
                                                        for="Others_primary_Preliminary">Preliminary</label>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="form-check">
                                                <input type="checkbox" name="permission[Others][11_20_Grade]"
                                                    {{ isset($package) && isset($package->permission['Others']) && multiKeyExists($package->permission['Others'], '11_20_Grade') ? 'checked' : '' }}
                                                    value="true" class="form-check-input" id="Others_11 to 20 Grade">
                                                <label class="form-label form-check-label" for="Others_11 to 20 Grade">11
                                                    to
                                                    20 Grade</label>

                                                <div class="form-check">
                                                    <input type="checkbox"
                                                        name="permission[Others][11_20_Grade][Preliminary]" value="true"
                                                        class="form-check-input"
                                                        id="Others_Others_11 to 20 Grade_Preliminary"
                                                        {{ isset($package) && isset($package->permission['Others']['11_20_Grade']) && multiKeyExists($package->permission['Others']['11_20_Grade'], 'Preliminary') ? 'checked' : '' }}>
                                                    <label class="form-label form-check-label"
                                                        for="Others_Others_11 to 20 Grade_Preliminary">Preliminary</label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="checkbox" name="permission[Others][11_20_Grade][Written]"
                                                        value="true" class="form-check-input"
                                                        id="Others_Others_11 to 20 Grade_Written"
                                                        {{ isset($package) && isset($package->permission['Others']['11_20_Grade']) && multiKeyExists($package->permission['Others']['11_20_Grade'], 'Written') ? 'checked' : '' }}>
                                                    <label class="form-label form-check-label"
                                                        for="Others_Others_11 to 20 Grade_Written">Written</label>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="form-check">
                                                <input type="checkbox" name="permission[Others][Non_Cadre]"
                                                    value="true" class="form-check-input" id="Others_Non-Cadre"
                                                    {{ isset($package) && isset($package->permission['Others']) && multiKeyExists($package->permission['Others'], 'Non_Cadre') ? 'checked' : '' }}>
                                                <label class="form-label form-check-label"
                                                    for="Others_Non-Cadre">Non-Cadre</label>

                                                <div class="form-check">
                                                    <input type="checkbox"
                                                        name="permission[Others][Non_Cadre][Preliminary]" value="true"
                                                        class="form-check-input" id="Others_Non-Cadre_Preliminary"
                                                        {{ isset($package) && isset($package->permission['Others']['Non_Cadre']) && multiKeyExists($package->permission['Others']['Non_Cadre'], 'Preliminary') ? 'checked' : '' }}>
                                                    <label class="form-label form-check-label"
                                                        for="Others_Non-Cadre_Preliminary">Preliminary</label>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="form-check">
                                                <input type="checkbox" name="permission[Others][Job_Solution]"
                                                    value="true" class="form-check-input" id="Others_Job Solution"
                                                    {{ isset($package) && isset($package->permission['Others']) && multiKeyExists($package->permission['Others'], 'Job_Solution') ? 'checked' : '' }}>
                                                <label class="form-label form-check-label" for="Others_Job Solution">Job
                                                    Solution</label>

                                                <div class="form-check">
                                                    <input type="checkbox"
                                                        name="permission[Others][Job_Solution][Preliminary]"
                                                        value="true" class="form-check-input"
                                                        id="Others_Job Solution_Preliminary"
                                                        {{ isset($package) && isset($package->permission['Others']['Job_Solution']) && multiKeyExists($package->permission['Others']['Job_Solution'], 'Preliminary') ? 'checked' : '' }}>
                                                    <label class="form-label form-check-label"
                                                        for="Others_Job Solution_Preliminary">Preliminary</label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="checkbox"
                                                        name="permission[Others][Job_Solution][Written]" value="true"
                                                        class="form-check-input" id="Others_Job Solution_Written"
                                                        {{ isset($package) && isset($package->permission['Others']['Job_Solution']) && multiKeyExists($package->permission['Others']['Job_Solution'], 'Written') ? 'checked' : '' }}>
                                                    <label class="form-label form-check-label"
                                                        for="Others_Job Solution_Written">Written</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Package amount<span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="amount"
                                        placeholder="Enter package amount" value="{{ $package->amount ?? '1' }}"
                                        required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Package validity<span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="validity"
                                        placeholder="Enter package validity" value="{{ $package->validity ?? '1' }}"
                                        required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label" for="inputAddress">Status <span
                                            class="text-danger">*</span></label>
                                    <select name="status" class="form-control" required>
                                        <option value="">Select status</option>
                                        <option value="0">Inactive</option>
                                        <option value="1" selected>Active</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Package image</label>
                                    <input type="file" class="form-control" name="image">
                                    @if (isset($package) && $package->image)
                                        <img src="{{ asset($package->image) }}" style="height:54px;">
                                    @endif
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('#subjects').on('change', function() {
                var subjects = $(this).val();
                var url = $(this).data('url');

                $.ajax({
                    type: 'post',
                    url: url,
                    dataType: 'json',
                    data: {
                        subjects
                    },
                    success: function(data) {
                        $('#topics').empty();
                        $.each(data, function(key, value) {
                            $('#topics').append(
                                '<option value="' + value.id + '">' + value.topic +
                                ' - (' + value.source + ')</option>'
                            )
                        });
                    }
                });
            });
        });
    </script>
@endsection
