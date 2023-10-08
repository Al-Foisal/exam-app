<nav class="sidebar vertical-scroll  ps-container ps-theme-default ps-active-y">
    <div class="logo d-flex justify-content-between">
        <a href="index-2.html"><img src="{{ asset($company->logo) }}" style="height: 70px;"></a>
        <div class="sidebar_close_icon d-lg-none">
            <i class="ti-close"></i>
        </div>
    </div>
    <ul id="sidebar_menu">
        <li class="mm-active">
            <a href="{{ route('dashboard') }}" aria-expanded="false">
                <div class="icon_menu">
                    <img src="{{ asset('backend/img/menu-icon/dashboard.svg') }}" alt>
                </div>
                <span>Dashboard</span>
            </a>
        </li>
        <li class>
            <a class="has-arrow" href="#" aria-expanded="false">
                <div class="icon_menu">
                    <img src="{{ asset('backend/img/menu-icon/2.svg') }}" alt>
                </div>
                <span>Users</span>
            </a>
            <ul>
                <li><a href="{{ route('admin.index') }}">All Admin List</a></li>
                <li><a href="{{ route('admin.create') }}">Create New Admin</a></li>
            </ul>
        </li>
        <li class>
            <a class="has-arrow" href="#" aria-expanded="false">
                <div class="icon_menu">
                    <img src="{{ asset('backend/img/menu-icon/2.svg') }}" alt>
                </div>
                <span>BCS</span>
            </a>
            <ul>
                <li><a href="{{ route('exam.index', ['ref' => 'BCS', 'type' => 'Preliminary']) }}">Preliminary Exam
                        List</a></li>
                <li><a href="{{ route('exam.written', ['ref' => 'BCS', 'type' => 'Written']) }}">Written Exam
                        List</a></li>
                <li><a href="{{ route('exam.writtenCreate', ['ref' => 'BCS', 'type' => 'Written']) }}">Create Written
                        Exam</a></li>
            </ul>
        </li>
        <li class>
            <a class="has-arrow" href="#" aria-expanded="false">
                <div class="icon_menu">
                    <img src="{{ asset('backend/img/menu-icon/2.svg') }}" alt>
                </div>
                <span>Bank</span>
            </a>
            <ul>
                <li><a href="{{ route('exam.index', ['ref' => 'Bank', 'type' => 'Preliminary']) }}">Preliminary Exam
                        List</a></li>
                <li><a href="{{ route('exam.create', ['ref' => 'Bank', 'type' => 'Preliminary']) }}">Create Preliminary
                        Exam</a></li>
            </ul>
        </li>
        <li class>
            <a class="has-arrow" href="#" aria-expanded="false">
                <div class="icon_menu">
                    <img src="{{ asset('backend/img/menu-icon/2.svg') }}" alt>
                </div>
                <span>Others</span>
            </a>
            <ul>
                <li><a href="{{ route('exam.create', ['ref' => 'Others', 'type' => 'Preliminary']) }}">Create
                        Preliminary
                        Exam</a></li>
                <li><a href="{{ route('admin.create') }}">Create New Admin</a></li>
            </ul>
        </li>
        <li class>
            <a class="has-arrow" href="#" aria-expanded="false">
                <div class="icon_menu">
                    <img src="{{ asset('backend/img/menu-icon/2.svg') }}" alt>
                </div>
                <span>Free</span>
            </a>
            <ul>
                <li><a href="{{ route('exam.create', ['ref' => 'Free', 'type' => 'Preliminary']) }}">Create Preliminary
                        Exam</a></li>
                <li><a href="{{ route('admin.create') }}">Create New Admin</a></li>
            </ul>
        </li>
        <li class>
            <a class="has-arrow" href="#" aria-expanded="false">
                <div class="icon_menu">
                    <img src="{{ asset('backend/img/menu-icon/2.svg') }}" alt>
                </div>
                <span>Settings</span>
            </a>
            <ul>
                <li><a href="{{ route('subject.index') }}">Subject</a></li>
                <li><a href="{{ route('topic.source.index') }}">Topic & Source</a></li>
            </ul>
        </li>
        <li class>
            <a class="has-arrow" href="#" aria-expanded="false">
                <div class="icon_menu">
                    <img src="{{ asset('backend/img/menu-icon/2.svg') }}" alt>
                </div>
                <span>Website Info</span>
            </a>
            <ul>
                <li><a href="{{ route('page.index') }}">Pages</a></li>
                <li><a href="{{ route('showCompanyInfo') }}">Company Info</a></li>
            </ul>
        </li>
    </ul>
</nav>
