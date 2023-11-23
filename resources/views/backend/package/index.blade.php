@extends('backend.layouts.master')
@section('title', 'All package list')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page_title_box d-flex align-items-center justify-content-between">
                <div class="page_title_left">
                    <h3 class="f_s_30 f_w_700 text_white">List of Package</h3>
                    <ol class="breadcrumb page_bradcam mb-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ $company->name }} </a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Package</a></li>
                        <li class="breadcrumb-item active">Index</li>
                    </ol>
                </div>
                <a href="{{ route('packages.createOrEdit') }}" class="white_btn3">Create Package</a>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        @foreach ($package as $item)
            <div class="col-md-6">
                <div class="card mb-3 widget-chart">
                    <div class="icon-wrapper rounded-circle" style="overflow: visible">
                        <div class="icon-wrapper-bg" style="opacity: 1;">
                            <img src="{{ asset($item->image) }}" style="height: 54px;">
                        </div>
                    </div>
                    <div class="widget-numbers">Price: <span>{{ $item->amount }}</span> BDT</div>
                    <div class="widget-subheading">{{ $item->name }}</div> <br>
                    <div class="widget-subheading">Validity: {{ $item->validity }} Days, Status:
                        {{ $item->status == 1 ? 'Active' : 'Inactive' }} <br> Package type: {{ $item->package_type }}</div>
                    <hr>
                    @if ($item->details)
                        <div class="widget-subheading">{!! $item->details !!}</div>
                    @endif

                    <div class="widget-description text-success" style="text-align: left;">
                        {{ json_encode($item->permission) }}
                        @foreach ($item->permission as $c_key => $cat)
                            @if (is_array($cat))
                                @foreach ($cat as $s_key => $sub)
                                    @if (is_array($sub))
                                        @foreach ($sub as $ch_key => $child)
                                            @if (is_array($child))
                                                @foreach ($child as $e_key => $exam)
                                                    <div>
                                                        <i class="fas fa-check-circle me-2"></i>{{ $c_key }} <i
                                                            class="fas fa-arrow-right"></i> {{ $s_key }} <i
                                                            class="fas fa-arrow-right"></i> {{ $ch_key }} <i
                                                            class="fas fa-arrow-right"></i>
                                                        {{ getPreli($e_key)->published_at->format('d F, Y') . ': ' . $exam }}
                                                    </div>
                                                @endforeach
                                            @else
                                                <div>
                                                    <i class="fas fa-check-circle me-2"></i>{{ $c_key }} <i
                                                        class="fas fa-arrow-right"></i> {{ $s_key }} <i
                                                        class="fas fa-arrow-right"></i>
                                                    {{ $ch_key . ': ' . $child }}
                                                </div>
                                            @endif
                                            {{-- <div>
                                                <i class="fas fa-check-circle me-2"></i>{{ $c_key }} <i
                                                    class="fas fa-arrow-right"></i> {{ $s_key }} <i
                                                    class="fas fa-arrow-right"></i> {{ $ch_key . ': ' . $child }}
                                            </div> --}}
                                        @endforeach
                                    @else
                                        <div>
                                            <i class="fas fa-check-circle me-1"></i>
                                            {{ $c_key }} <i class="fas fa-arrow-right"></i>
                                            {{ $s_key . ': ' . $sub }}

                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <div>
                                    <i class="fas fa-check-circle me-1"></i>{{ $c_key }} <i
                                        class="fas fa-arrow-right"></i> {{ $cat }}

                                </div>
                            @endif
                        @endforeach
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('packages.createOrEdit', $item->id) }}" class="btn btn-info btn-sm">Edit</a>
                        <form action="{{ route('packages.delete', $item->id) }}" method="post">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
@endsection
