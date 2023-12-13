@extends('backend.layouts.master')
@section('title', request()->id ? 'Update package' : 'Create new package')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page_title_box d-flex align-items-center justify-content-between">
                <div class="page_title_left">
                    <h3 class="f_s_30 f_w_700 text_white">{{ request()->id ? 'Update ' : 'Create new' }} package</h3>
                    <ol class="breadcrumb page_bradcam mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ $company->name }} </a></li>
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
                                                @foreach ($bcs_preliminary as $bcs_pre)
                                                    <div class="form-check">
                                                        <input type="checkbox"
                                                            name="permission[BCS][Preliminary][{{ $bcs_pre->id }}]"
                                                            value="true"
                                                            {{ isset($package) && isset($package->permission['BCS']['Preliminary']) && multiKeyExists($package->permission['BCS']['Preliminary'], $bcs_pre->id) ? 'checked' : '' }}
                                                            class="form-check-input"
                                                            id="BCS_Preliminary{{ $bcs_pre->id }}">
                                                        <label class="form-label form-check-label"
                                                            for="BCS_Preliminary{{ $bcs_pre->id }}">{{ $bcs_pre->published_at->format('d-m-Y') }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" name="permission[BCS][Written]" value="true"
                                                    {{ isset($package) && isset($package->permission['BCS']) && multiKeyExists($package->permission['BCS'], 'Written') ? 'checked' : '' }}
                                                    class="form-check-input" id="BCS_Written">
                                                <label class="form-label form-check-label" for="BCS_Written">Written</label>
                                                @foreach ($bcs_written as $bcs_pre)
                                                    <div class="form-check">
                                                        <input type="checkbox"
                                                            name="permission[BCS][Written][{{ $bcs_pre->id }}]"
                                                            value="true"
                                                            {{ isset($package) && isset($package->permission['BCS']['Written']) && multiKeyExists($package->permission['BCS']['Written'], $bcs_pre->id) ? 'checked' : '' }}
                                                            class="form-check-input" id="BCS_Written{{ $bcs_pre->id }}">
                                                        <label class="form-label form-check-label"
                                                            for="BCS_Written{{ $bcs_pre->id }}">{{ $bcs_pre->published_at->format('d-m-Y') }}</label>
                                                    </div>
                                                @endforeach
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
                                                @foreach ($bank_preliminary as $bank_pre)
                                                    <div class="form-check">
                                                        <input type="checkbox"
                                                            name="permission[Bank][Preliminary][{{ $bank_pre->id }}]"
                                                            value="true"
                                                            {{ isset($package) && isset($package->permission['Bank']['Preliminary']) && multiKeyExists($package->permission['Bank']['Preliminary'], $bank_pre->id) ? 'checked' : '' }}
                                                            class="form-check-input"
                                                            id="Bank_Preliminary{{ $bank_pre->id }}">
                                                        <label class="form-label form-check-label"
                                                            for="Bank_Preliminary{{ $bank_pre->id }}">{{ $bank_pre->published_at->format('d-m-Y') }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" name="permission[Bank][Written]" value="true"
                                                    {{ isset($package) && isset($package->permission['Bank']) && multiKeyExists($package->permission['Bank'], 'Written') ? 'checked' : '' }}
                                                    class="form-check-input" id="Bank_Written">
                                                <label class="form-label form-check-label"
                                                    for="Bank_Written">Written</label>
                                                @foreach ($bank_written as $bank_pre)
                                                    <div class="form-check">
                                                        <input type="checkbox"
                                                            name="permission[Bank][Written][{{ $bank_pre->id }}]"
                                                            value="true"
                                                            {{ isset($package) && isset($package->permission['Bank']['Written']) && multiKeyExists($package->permission['Bank']['Written'], $bank_pre->id) ? 'checked' : '' }}
                                                            class="form-check-input"
                                                            id="Bank_Written{{ $bank_pre->id }}">
                                                        <label class="form-label form-check-label"
                                                            for="Bank_Written{{ $bank_pre->id }}">{{ $bank_pre->published_at->format('d-m-Y') }}</label>
                                                    </div>
                                                @endforeach
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
                                                    @foreach ($primary_preliminary as $primary_pre)
                                                        <div class="form-check">
                                                            <input type="checkbox"
                                                                name="permission[Others][Primary][Preliminary][{{ $primary_pre->id }}]"
                                                                value="true"
                                                                {{ isset($package) && isset($package->permission['Others']['Primary']['Preliminary']) && multiKeyExists($package->permission['Others']['Primary']['Preliminary'], $primary_pre->id) ? 'checked' : '' }}
                                                                class="form-check-input"
                                                                id="Others_primary_Preliminary{{ $primary_pre->id }}">
                                                            <label class="form-label form-check-label"
                                                                for="Others_primary_Preliminary{{ $primary_pre->id }}">{{ $primary_pre->published_at->format('d-m-Y') }}</label>
                                                        </div>
                                                    @endforeach
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

                                                    @foreach ($grade_preliminary as $grade_pre)
                                                        <div class="form-check">
                                                            <input type="checkbox"
                                                                name="permission[Others][11_20_Grade][Preliminary][{{ $grade_pre->id }}]"
                                                                value="true"
                                                                {{ isset($package) && isset($package->permission['Others']['11_20_Grade']['Preliminary']) && multiKeyExists($package->permission['Others']['11_20_Grade']['Preliminary'], $grade_pre->id) ? 'checked' : '' }}
                                                                class="form-check-input"
                                                                id="Others_11_20_Grade_Preliminary{{ $grade_pre->id }}">
                                                            <label class="form-label form-check-label"
                                                                for="Others_11_20_Grade_Preliminary{{ $grade_pre->id }}">{{ $grade_pre->published_at->format('d-m-Y') }}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="form-check">
                                                    <input type="checkbox" name="permission[Others][11_20_Grade][Written]"
                                                        value="true" class="form-check-input"
                                                        id="Others_Others_11 to 20 Grade_Written"
                                                        {{ isset($package) && isset($package->permission['Others']['11_20_Grade']) && multiKeyExists($package->permission['Others']['11_20_Grade'], 'Written') ? 'checked' : '' }}>
                                                    <label class="form-label form-check-label"
                                                        for="Others_Others_11 to 20 Grade_Written">Written</label>

                                                    @foreach ($grade_written as $grade_pre)
                                                        <div class="form-check">
                                                            <input type="checkbox"
                                                                name="permission[Others][11_20_Grade][Written][{{ $grade_pre->id }}]"
                                                                value="true"
                                                                {{ isset($package) && isset($package->permission['Others']['11_20_Grade']['Written']) && multiKeyExists($package->permission['Others']['11_20_Grade']['Written'], $grade_pre->id) ? 'checked' : '' }}
                                                                class="form-check-input"
                                                                id="Others_11_20_Grade_Written{{ $grade_pre->id }}">
                                                            <label class="form-label form-check-label"
                                                                for="Others_11_20_Grade_Written{{ $grade_pre->id }}">{{ $grade_pre->published_at->format('d-m-Y') }}</label>
                                                        </div>
                                                    @endforeach
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

                                                    @foreach ($non_cader_preliminary as $non_cader_pre)
                                                        <div class="form-check">
                                                            <input type="checkbox"
                                                                name="permission[Others][Non_Cadre][Preliminary][{{ $non_cader_pre->id }}]"
                                                                value="true"
                                                                {{ isset($package) && isset($package->permission['Others']['Non_Cadre']['Preliminary']) && multiKeyExists($package->permission['Others']['Non_Cadre']['Preliminary'], $non_cader_pre->id) ? 'checked' : '' }}
                                                                class="form-check-input"
                                                                id="Others_Non_Cadre_Preliminary{{ $non_cader_pre->id }}">
                                                            <label class="form-label form-check-label"
                                                                for="Others_Non_Cadre_Preliminary{{ $non_cader_pre->id }}">{{ $non_cader_pre->published_at->format('d-m-Y') }}</label>
                                                        </div>
                                                    @endforeach
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
                                                    @foreach ($job_preliminary as $job_pre)
                                                        <div class="form-check">
                                                            <input type="checkbox"
                                                                name="permission[Others][Job_Solution][Preliminary][{{ $job_pre->id }}]"
                                                                value="true"
                                                                {{ isset($package) && isset($package->permission['Others']['Job_Solution']['Preliminary']) && multiKeyExists($package->permission['Others']['Job_Solution']['Preliminary'], $job_pre->id) ? 'checked' : '' }}
                                                                class="form-check-input"
                                                                id="Others_Job_Solution_Preliminary{{ $job_pre->id }}">
                                                            <label class="form-label form-check-label"
                                                                for="Others_Job_Solution_Preliminary{{ $job_pre->id }}">{{ $job_pre->published_at->format('d-m-Y') }}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="form-check">
                                                    <input type="checkbox"
                                                        name="permission[Others][Job_Solution][Written]" value="true"
                                                        class="form-check-input" id="Others_Job Solution_Written"
                                                        {{ isset($package) && isset($package->permission['Others']['Job_Solution']) && multiKeyExists($package->permission['Others']['Job_Solution'], 'Written') ? 'checked' : '' }}>
                                                    <label class="form-label form-check-label"
                                                        for="Others_Job Solution_Written">Written</label>
                                                    @foreach ($job_written as $job_pre)
                                                        <div class="form-check">
                                                            <input type="checkbox"
                                                                name="permission[Others][Job_Solution][Written][{{ $job_pre->id }}]"
                                                                value="true"
                                                                {{ isset($package) && isset($package->permission['Others']['Job_Solution']['Written']) && multiKeyExists($package->permission['Others']['Job_Solution']['Written'], $job_pre->id) ? 'checked' : '' }}
                                                                class="form-check-input"
                                                                id="Others_Job_Solution_Written{{ $job_pre->id }}">
                                                            <label class="form-label form-check-label"
                                                                for="Others_Job_Solution_Written{{ $job_pre->id }}">{{ $job_pre->published_at->format('d-m-Y') }}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card p-3 mb-3">
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" name="permission[Material]" value="true"
                                                class="form-check-input" id="Material"
                                                {{ isset($package) && multiKeyExists($package->permission, 'Material') ? 'checked' : '' }}>
                                            <label class="form-label form-check-label" for="Material">Material</label>
                                            <div class="form-check">
                                                <input type="checkbox" name="permission[Material][BCS]" value="true"
                                                    {{ isset($package) && isset($package->permission['Material']) && multiKeyExists($package->permission['Material'], 'BCS') ? 'checked' : '' }}
                                                    class="form-check-input" id="Material_BCS">
                                                <label class="form-label form-check-label" for="Material_BCS">BCS</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" name="permission[Material][Bank]" value="true"
                                                    {{ isset($package) && isset($package->permission['Material']) && multiKeyExists($package->permission['Material'], 'Bank') ? 'checked' : '' }}
                                                    class="form-check-input" id="Material_Bank">
                                                <label class="form-label form-check-label"
                                                    for="Material_Bank">Bank</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" name="permission[Material][Recent]" value="true"
                                                    {{ isset($package) && isset($package->permission['Material']) && multiKeyExists($package->permission['Material'], 'Recent') ? 'checked' : '' }}
                                                    class="form-check-input" id="Material_Recent">
                                                <label class="form-label form-check-label"
                                                    for="Material_Recent">Recent</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" name="permission[Material][Record_Class]"
                                                    value="true"
                                                    {{ isset($package) && isset($package->permission['Material']) && multiKeyExists($package->permission['Material'], 'Record_Class') ? 'checked' : '' }}
                                                    class="form-check-input" id="Material_Record_Class">
                                                <label class="form-label form-check-label"
                                                    for="Material_Record_Class">Record Class</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Package amount<span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="amount"
                                        placeholder="Enter package amount" value="{{ $package->amount ?? '1' }}"
                                        required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Package validity (days)<span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="validity"
                                        placeholder="Enter package validity" value="{{ $package->validity ?? '1' }}"
                                        required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="inputAddress">Status <span
                                            class="text-danger">*</span></label>
                                    <select name="status" class="form-control" required>
                                        <option value="">Select status</option>
                                        <option value="0"
                                            {{ isset($package) && $package->status == 0 ? 'selected' : '' }}>Inactive
                                        </option>
                                        <option value="1"
                                            {{ isset($package) && $package->status == 1 ? 'selected' : '' }}>
                                            Active</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="inputAddress">Package Type <span
                                            class="text-danger">*</span></label>
                                    <select name="type" class="form-control" required>
                                        <option value="">Select type</option>
                                        <option value="1"
                                            {{ isset($package) && $package->type == 1 ? 'selected' : '' }}>Course
                                            Base(Package without any limited exam)
                                        </option>
                                        <option value="2"
                                            {{ isset($package) && $package->type == 2 ? 'selected' : '' }}>
                                            Exam Base(Package with limited exam)</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
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
