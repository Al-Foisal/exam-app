@extends('backend.layouts.master')
@section('title', 'All ' . request()->ref . ' withdrawal request list')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page_title_box d-flex align-items-center justify-content-between">
                <div class="page_title_left">
                    <h3 class="f_s_30 f_w_700 text_white">List of
                        {{ request()->ref }} withdrawal request</h3>
                    <ol class="breadcrumb page_bradcam mb-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ $company->name }} </a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Wallet Withdrawal Request</a></li>
                        <li class="breadcrumb-item active">{{ request()->ref }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="white_card card_height_100 mb_30">
                <div class="white_card_body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    @if (request()->ref === 'Paid' || request()->ref === 'Declined')
                                        @else
                                    <th scope="col">Action</th>
                                    @endif
                                    <th scope="col">Teacher Name</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Requested</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        @if (request()->ref === 'Paid' || request()->ref === 'Declined')
                                        @else
                                            <td>
                                                <div class="d-flex justify-content-around">
                                                    @if (request()->ref === 'Pending')
                                                        <form
                                                            action="{{ route('teacher.updateWithdrawalRequest', $item->id) }}"
                                                            method="post">
                                                            @csrf
                                                            <input type="hidden" name="type" value="Accepted">
                                                            <button type="submit"
                                                                class="btn btn-outline-primary btn-sm mr-2"
                                                                onclick="return confirm('Are you sure?')">
                                                                Accept
                                                            </button>
                                                        </form>
                                                        <form
                                                            action="{{ route('teacher.updateWithdrawalRequest', $item->id) }}"
                                                            method="post">
                                                            @csrf
                                                            <input type="hidden" name="type" value="Declined">
                                                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                                                onclick="return confirm('Are you sure?')">
                                                                Decline
                                                            </button>
                                                        </form>
                                                    @elseif (request()->ref === 'Accepted')
                                                        <form
                                                            action="{{ route('teacher.updateWithdrawalRequest', $item->id) }}"
                                                            method="post">
                                                            @csrf
                                                            <input type="hidden" name="type" value="Paid">
                                                            <button type="submit"
                                                                class="btn btn-outline-primary btn-sm mr-2"
                                                                onclick="return confirm('Are you sure?')">
                                                                Paid
                                                            </button>
                                                        </form>
                                                        <form
                                                            action="{{ route('teacher.updateWithdrawalRequest', $item->id) }}"
                                                            method="post">
                                                            @csrf
                                                            <input type="hidden" name="type" value="Declined">
                                                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                                                onclick="return confirm('Are you sure?')">
                                                                Decline
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        @endif
                                        <td>{{ $item->wallet->user->name . ' - ' . $item->wallet->user->phone ?? '' }}</td>
                                        <td>{{ $item->amount }}</td>
                                        @if (request()->ref !== 'Pending')
                                            <td>{{ $item->updated_at->format('d F, Y h:i A') }}</td>
                                        @else
                                            <td>{{ $item->created_at->format('d F, Y h:i A') }}</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $data->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
