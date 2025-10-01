@include('admin.layouts.header')

@include('admin.layouts.sidebar')

<div id="content-wrapper" class="d-flex flex-column h-screen overflow-y-auto">
    <div id="content">

        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>

            <div class="d-sm-flex align-items-center justify-content-between">
                <h1 class="h3 mb-0 text-gray-800">Feedbacks</h1>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">User Feedback</h6>
                </div>
                <div class="card-body">
                    @if($feedbacks->count())
                        <ul class="list-unstyled">
                            @foreach($feedbacks as $feedback)
                                <li class="mb-3 p-3 border rounded shadow-sm">
                                    <p class="mb-1"><strong>Email:</strong> {{ $feedback->email }}</p>
                                    <p class="mb-1"><strong>Message:</strong> {{ $feedback->message }}</p>
                                    <p class="text-muted mb-0"><strong>Submitted At:</strong>
                                        {{ \Carbon\Carbon::parse($feedback->created_at)->format('d M Y, h:i A') }}</p>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>No feedback available.</p>
                    @endif

                </div>
            </div>
        </div>

    </div>

</div>

@include('admin.layouts.script')