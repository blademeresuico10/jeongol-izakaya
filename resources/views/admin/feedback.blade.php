@include('admin.layouts.header')
@include('admin.layouts.sidebar')

<div id="content-wrapper" class="d-flex flex-column h-screen overflow-y-auto">
    <div id="content">

        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>
            <h1 class="h3 mb-0 text-gray-800">Feedbacks</h1>
        </nav>

        <div class="container-fluid">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">User Feedback</h6>
                    <form id="searchForm" class="d-flex" style="max-width: 300px;">
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="form-control form-control-sm me-2" placeholder="Search messages...">
                        <button type="submit" class="btn btn-sm btn-primary">Search</button>
                    </form>
                </div>

                <div class="card-body" style="max-height: 500px; overflow-y: auto;" id="feedback-list">
                    @if($feedbacks->count())
                        <ul class="list-unstyled">
                            @foreach($feedbacks as $feedback)
                                <li class="mb-3 p-3 border rounded shadow-sm bg-white">
                                    <p class="mb-1"><strong>Message:</strong> {{ $feedback->message }}</p>
                                    <p class="text-muted mb-0">
                                        <strong>Submitted At:</strong> {{ $feedback->created_at->format('d M Y, h:i A') }}
                                    </p>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-center text-muted">No feedback available.</p>
                    @endif
                </div>
                @if($feedbacks->hasPages())
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $feedbacks->onEachSide(1)->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@include('admin.layouts.script')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.querySelector('input[name="search"]');
        const feedbackList = document.getElementById('feedback-list');

        searchInput.addEventListener('input', function () {
            const query = this.value;

            fetch(`{{ route('admin.feedback') }}?search=${query}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(response => response.json())
                .then(data => {
                    feedbackList.innerHTML = data.feedbacks;
                });
        });

        document.getElementById('searchForm').addEventListener('submit', function (e) {
            e.preventDefault();
        });
    });
</script>