@vite('resources/css/app.css')
@include('admin.layouts.header')
@include('admin.layouts.sidebar')

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h1 class="h3 mb-0 text-gray-800">E-Wallet Management</h1>
            </div>
        </nav>

        <div class="flex justify-center gap-6 mt-6">
            <div class="w-2/5 border rounded-lg">
                <div class="bg-gray-200 flex justify-between items-center text-black px-4 py-2">
                    <h6 class="font-semibold">GCash</h6>
                    <button onclick="openModal('gcash')"
                        class="bg-blue-500 text-white px-3 py-1 text-sm rounded hover:bg-blue-600">
                        Add
                    </button>
                </div>

                <div class="p-1">
                    <table class="w-full text-sm border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-2 py-1">Wallet Registered Name</th>
                                <th class="border px-2 py-1">Wallet Number</th>
                                <th class="border px-2 py-1">Status</th>
                                <th class="border px-2 py-1">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ewallet_details->where('payment_method', 'gcash') as $gcash)
                                <tr>
                                    <td class="border px-2 py-1">{{ $gcash->wallet_name }}</td>
                                    <td class="border px-2 py-1">{{ $gcash->wallet_number }}</td>
                                    <td class="border px-2 py-1">
                                        <span
                                            class="px-2 py-1 rounded text-xs 
                                                                {{ $gcash->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $gcash->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="border px-2 py-1 text-center">
                                        @if($gcash->is_active)
                                            <span class="text-xs text-gray-500 italic">—</span>
                                        @else
                                            <form action="{{ route('ewallet.activate', $gcash->id) }}" method="POST"
                                                class="inline activate-form">
                                                @csrf
                                                <button type="button"
                                                    class="border bg-green-500 text-white px-2 py-1 text-xs rounded activate-btn"
                                                    data-name="{{ $gcash->wallet_name }} ({{ $gcash->wallet_number }})">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="w-2/5 border rounded-lg">
                <div class="bg-gray-200 flex justify-between items-center text-black px-4 py-2">
                    <h6 class="font-semibold">Maya</h6>
                    <button onclick="openModal('maya')"
                        class="bg-green-500 text-white px-3 py-1 text-sm rounded hover:bg-green-600">
                        Add
                    </button>
                </div>

                <div class="p-1">
                    <table class="w-full text-sm border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-2 py-1">Wallet Registered Name</th>
                                <th class="border px-2 py-1">Wallet Number</th>
                                <th class="border px-2 py-1">Status</th>
                                <th class="border px-2 py-1">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ewallet_details->where('payment_method', 'maya') as $maya)
                                <tr>
                                    <td class="border px-2 py-1">{{ $maya->wallet_name }}</td>
                                    <td class="border px-2 py-1">{{ $maya->wallet_number }}</td>
                                    <td class="border px-2 py-1">
                                        <span
                                            class="px-2 py-1 rounded text-xs 
                                                                {{ $maya->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $maya->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="border px-2 py-1 text-center">
                                        @if($maya->is_active)
                                            <!-- Active -> No button -->
                                            <span class="text-xs text-gray-500 italic">—</span>
                                        @else
                                            <!-- Inactive -> show Activate -->
                                            <form action="{{ route('ewallet.activate', $maya->id) }}" method="POST"
                                                class="inline activate-form">
                                                @csrf
                                                <button type="button"
                                                    class="border bg-green-500 text-white px-2 py-1 text-xs rounded activate-btn"
                                                    data-name="{{ $maya->wallet_name }} ({{ $maya->wallet_number }})">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="addEwalletModal" class="hidden fixed inset-0 flex items-center justify-center z-50">
            <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Add E-Wallet</h2>
                <form action="{{ route('ewallet.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                        <p id="payment_method_display"
                            class="mt-1 block w-full border rounded-lg px-3 py-2 text-sm bg-gray-100"></p>
                        <input type="hidden" id="payment_method" name="payment_method" required>
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700">Wallet Registered Name</label>
                        <input type="text" name="wallet_name" required
                            class="mt-1 block w-full border rounded-lg px-3 py-2 text-sm">
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700">Wallet Number</label>
                        <input type="text" name="wallet_number" required
                            class="mt-1 block w-full border rounded-lg px-3 py-2 text-sm">
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="is_active" class="mt-1 block w-full border rounded-lg px-3 py-2 text-sm">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" onclick="closeModal()"
                            class="px-4 py-2 text-sm bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 text-sm bg-blue-500 text-white rounded hover:bg-blue-600">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function openModal(method) {
        const modal = document.getElementById('addEwalletModal');
        document.getElementById('payment_method_display').innerText =
            method.charAt(0).toUpperCase() + method.slice(1);
        document.getElementById('payment_method').value = method;
        modal.classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('addEwalletModal').classList.add('hidden');
    }

    document.querySelectorAll(".activate-btn").forEach(button => {
        button.addEventListener("click", function () {
            let form = this.closest("form");
            let name = this.dataset.name;
            Swal.fire({
                title: `Set ${name} as active?`,
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Yes, Activate",
                cancelButtonText: "Cancel",
                confirmButtonColor: "#16a34a",
                cancelButtonColor: "#d1d5db"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    document.querySelectorAll(".deactivate-btn").forEach(button => {
        button.addEventListener("click", function () {
            let form = this.closest("form");
            let name = this.dataset.name;
            Swal.fire({
                title: `Are you sure you want to deactivate ${name}?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Deactivate",
                cancelButtonText: "Cancel",
                confirmButtonColor: "#dc2626",
                cancelButtonColor: "#d1d5db"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: "{{ session('success') }}",
            toast: true,
            position: 'top',
            timer: 2000,
            showConfirmButton: false
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: "{{ session('error') }}",
            toast: true,
            position: 'top',
            timer: 2000,
            showConfirmButton: false
        });
    @endif
</script>

@include('admin.layouts.script')