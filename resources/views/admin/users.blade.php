@include('admin.layouts.header')
@include('admin.layouts.sidebar')

<div id="content-wrapper" class="d-flex flex-column h-screen overflow-y-auto">
    <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <h1 class="h3 mb-0 text-gray-800">User Management</h1>
        </nav>
        <div class="container-fluid">

            <div class="mb-3">
                <a href="{{ route('admin.users') }}"
                    class="btn btn-sm {{ !request()->has('show_deleted') ? 'btn-primary' : 'btn-outline-primary' }}">
                    Active Users
                </a>
                <a href="{{ route('admin.users', ['show_deleted' => true]) }}"
                    class="btn btn-sm {{ request()->has('show_deleted') ? 'btn-secondary' : 'btn-outline-secondary' }}">
                    Deleted Users
                </a>
            </div>

            <div class="card mt-2" style="max-width: 100%;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Users {{ request()->has('show_deleted') ? '(Deleted)' : '' }}</h5>
                    @if(!request()->has('show_deleted'))
                        <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#addUserModal">Add
                            User</button>
                    @endif
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-sm text-start">
                        <thead class="thead-light">
                            <tr>
                                <th>Profile</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Contact</th>
                                <th>Address</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>
                                        @if($user->profile_picture)
                                            <img src="{{ url('file-serve/' . $user->profile_picture) }}" alt="Profile Picture"
                                                width="130" height="130" style="object-fit: cover; border-radius: 10px;">
                                        @else
                                            <span class="text-muted">No Picture</span>
                                        @endif
                                    </td>

                                    <td>{{ $user->firstname }} {{ $user->lastname }}</td>
                                    <td>
                                        <span>{{ ucfirst($user->role) }}</span>
                                    </td>
                                    <td>{{ $user->contact_number }}</td>
                                    <td>{{$user->address}}</td>
                                    <td>{{$user->username}}</td>
                                    <td>{{ $user->email }} </td>
                                    <td>
                                        @if($user->deleted_at)
                                            <span class="badge badge-danger">Deleted</span>
                                        @elseif ($user->status === 'Active')
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($user->deleted_at)
                                            <button type="button" class="btn btn-sm btn-success"
                                                onclick="showRestoreModal({{ $user->id }}, '{{ addslashes($user->firstname . ' ' . $user->lastname) }}')"
                                                title="Restore">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="showForceDeleteModal({{ $user->id }}, '{{ addslashes($user->firstname . ' ' . $user->lastname) }}')"
                                                title="Delete Permanently">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <a href="#" title="Edit" data-toggle="modal"
                                                data-target="#editUserModal{{ $user->id }}"
                                                style="all: unset; cursor: pointer;">
                                                <i class="fas fa-edit text-primary"></i>
                                            </a>
                                            @if($user->role !== 'admin')
                                                <button type="button" class="btn btn-sm btn-link p-0 ml-2"
                                                    onclick="showDeleteModal({{ $user->id }}, '{{ addslashes($user->firstname . ' ' . $user->lastname) }}')"
                                                    title="Delete" style="all: unset; cursor: pointer;">
                                                    <i class="fas fa-trash text-danger"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">
                                        {{ request()->has('show_deleted') ? 'No deleted users found' : 'No users found' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal fade" id="addUserModal" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog" role="document">
                    <form action="{{ route('storeUser') }}" method="POST" id="addUserForm"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">Add New User</h5>
                                <button type="button" class="close text-white"
                                    data-dismiss="modal"><span>&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="mt-2">Profile Picture:</label>
                                    <input type="file" name="profile_picture" class="form-control" accept="image/*">
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>First Name <span class="text-danger">*</span></label>
                                            <input type="text" name="firstname" class="form-control" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Last Name <span class="text-danger">*</span></label>
                                            <input type="text" name="lastname" class="form-control" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Role <span class="text-danger">*</span></label>
                                            <select name="role" class="form-control" required>
                                                <option value="">Select Role</option>
                                                <option value="Manager">Manager</option>
                                                <option value="Receptionist">Receptionist</option>
                                                <option value="Cashier">Cashier</option>
                                                <option value="Kitchen Staff">Kitchen Staff</option>
                                            </select>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Contact Number <span class="text-danger">*</span></label>
                                            <input type="text" name="contact_number" class="form-control" required
                                                pattern="[0-9]{10,11}"
                                                title="Please enter a valid phone number (10-11 digits)">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="mt-2">Username <span class="text-danger">*</span></label>
                                    <input type="text" name="username" class="form-control" required minlength="3"
                                        pattern="[a-zA-Z0-9_]+"
                                        title="Username must contain only letters, numbers, and underscores">
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="form-group">
                                    <label class="mt-2">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" required>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="form-group">
                                    <label class="mt-2">Address <span class="text-danger">*</span></label>
                                    <input type="address" name="address" class="form-control" required>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Password <span class="text-danger">*</span></label>
                                            <input type="password" id="password" name="password" class="form-control"
                                                required minlength="6">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Confirm Password <span class="text-danger">*</span></label>
                                            <input type="password" id="confirm_password" name="password_confirmation"
                                                class="form-control" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="status" value="1" checked class="form-check-input">
                                        <label class="form-check-label">Active</label>
                                    </div>
                                </div>

                                <div id="password-warning" class="alert alert-danger mt-2" style="display: none;">
                                    Passwords do not match
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" id="submitBtn" class="btn btn-success">Add User</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @foreach ($users as $user)
                @if(!$user->deleted_at)
                    <div class="modal fade" id="editUserModal{{ $user->id }}" data-backdrop="static" data-keyboard="false">
                        <div class="modal-dialog" role="document">
                            <form action="{{ route('admin.updateuser', $user->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">Edit User</h5>
                                        <button type="button" class="close text-white"
                                            data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body">

                                        <div class="mb-3 text-center">
                                            @if($user->profile_picture)
                                                <img src="{{ url('file-serve/' . $user->profile_picture) }}" alt="Profile Picture"
                                                    width="250" height="250"
                                                    style="object-fit: cover; border-radius: 2%; display: block; margin: 0 auto;">
                                            @else
                                                <span class="text-muted">No Picture</span>
                                            @endif
                                        </div>

                                        <label>Change Profile Picture</label>
                                        <input type="file" name="profile_picture" class="form-control mb-3">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>First Name</label>
                                                <input type="text" name="firstname" value="{{ $user->firstname }}"
                                                    class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Last Name</label>
                                                <input type="text" name="lastname" value="{{ $user->lastname }}"
                                                    class="form-control" required>
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-md-6">
                                                <label>Role</label>
                                                <select name="role" class="form-control" required>
                                                    <option value="receptionist" {{ $user->role == 'receptionist' ? 'selected' : '' }}>Receptionist</option>
                                                    <option value="cashier" {{ $user->role == 'cashier' ? 'selected' : '' }}>
                                                        Cashier</option>
                                                    <option value="kitchen-staff" {{ $user->role == 'kitchen-staff' ? 'selected' : '' }}>Kitchen Staff</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Contact Number</label>
                                                <input type="text" name="contact_number" value="{{ $user->contact_number }}"
                                                    class="form-control" required>
                                            </div>
                                        </div>

                                        <label class="mt-2">Username</label>
                                        <input type="text" name="username" value="{{ $user->username }}" class="form-control"
                                            required>

                                        <label class="mt-2">Email</label>
                                        <input type="email" name="email" value="{{ $user->email }}" class="form-control"
                                            required>

                                        <label class="mt-2">Address</label>
                                        <input type="address" name="address" value="{{ $user->address }}" class="form-control"
                                            required>

                                        <label class="mt-2">Update Password</label>
                                        <input type="password" name="password" placeholder="Leave blank to keep current"
                                            class="form-control">

                                        <div class="mt-3">
                                            <div class="form-check">
                                                <input type="checkbox" name="status" value="1" {{ $user->status === 'Active' ? 'checked' : '' }} class="form-check-input">
                                                <label class="form-check-label">Active</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Update User</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                @endif
            @endforeach

            <div class="modal fade" id="deleteConfirmModal" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h5>
                            <button type="button" class="close text-white"
                                data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete user <strong><span id="deleteItemName"></span></strong>
                                from the system?</p>
                        </div>
                        <div class="modal-footer">
                            <form id="deleteForm" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="restoreConfirmModal" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title"><i class="fas fa-undo"></i> Restore User</h5>
                            <button type="button" class="close text-white"
                                data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to restore user <strong><span
                                        id="restoreItemName"></span></strong>?</p>
                        </div>
                        <div class="modal-footer">
                            <form id="restoreForm" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success">Restore</button>
                            </form>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="forceDeleteConfirmModal" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content border-danger">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title"><i class="fas fa-exclamation-circle"></i> CRITICAL WARNING</h5>
                            <button type="button" class="close text-white"
                                data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body bg-light">
                            <div class="alert alert-danger border-danger">
                                <p class="text-center">Are you sure you want to <strong class="text-danger">permanently
                                        delete user </strong>
                                    <span class="badge badge-danger" id="forceDeleteItemName"></span> ?
                                </p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <form id="forceDeleteForm" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger text-white border-danger">Delete
                                    Permanently</button>
                            </form>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@include('admin.layouts.script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {

        window.showDeleteModal = function (id, itemName) {
            try {
                const deleteItemNameElement = document.getElementById('deleteItemName');
                const deleteFormElement = document.getElementById('deleteForm');

                if (deleteItemNameElement && deleteFormElement) {
                    deleteItemNameElement.textContent = itemName;
                    deleteFormElement.action = "{{ url('users') }}/" + id;
                    $('#deleteConfirmModal').modal('show');
                } else {
                    console.error('Delete modal elements not found');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Unable to show delete confirmation. Please refresh the page.',
                        toast: true,
                        position: 'top',
                        timer: 3000,
                        showConfirmButton: false,
                        background: '#f8d7da',
                        color: '#721c24'
                    });
                }
            } catch (error) {
                console.error('Error showing delete modal:', error);
            }
        };

        window.showRestoreModal = function (id, itemName) {
            try {
                const restoreItemNameElement = document.getElementById('restoreItemName');
                const restoreFormElement = document.getElementById('restoreForm');

                if (restoreItemNameElement && restoreFormElement) {
                    restoreItemNameElement.textContent = itemName;
                    restoreFormElement.action = "{{ url('restoreuser') }}/" + id;
                    $('#restoreConfirmModal').modal('show');
                } else {
                    console.error('Restore modal elements not found');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Unable to show restore confirmation. Please refresh the page.',
                        toast: true,
                        position: 'top',
                        timer: 3000,
                        showConfirmButton: false,
                        background: '#f8d7da',
                        color: '#721c24'
                    });
                }
            } catch (error) {
                console.error('Error showing restore modal:', error);
            }
        };

        window.showForceDeleteModal = function (id, itemName) {
            try {
                const forceDeleteItemNameElement = document.getElementById('forceDeleteItemName');
                const forceDeleteFormElement = document.getElementById('forceDeleteForm');

                if (forceDeleteItemNameElement && forceDeleteFormElement) {
                    forceDeleteItemNameElement.textContent = itemName;
                    forceDeleteFormElement.action = "{{ url('forcedeleteuser') }}/" + id;
                    $('#forceDeleteConfirmModal').modal('show');
                } else {
                    console.error('Force delete modal elements not found');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Unable to show permanent delete confirmation. Please refresh the page.',
                        toast: true,
                        position: 'top',
                        timer: 3000,
                        showConfirmButton: false,
                        background: '#f8d7da',
                        color: '#721c24'
                    });
                }
            } catch (error) {
                console.error('Error showing force delete modal:', error);
            }
        };

        // Password strength calculator
        function calculatePasswordStrength(password) {
            const checks = {
                length: password.length >= 8,
                lowercase: /[a-z]/.test(password),
                uppercase: /[A-Z]/.test(password),
                numbers: /\d/.test(password),
                special: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)
            };

            const strength = Object.values(checks).filter(Boolean).length * 20;

            let level, color;
            if (strength >= 80) { level = 'Strong'; color = 'success'; }
            else if (strength >= 60) { level = 'Good'; color = 'warning'; }
            else if (strength >= 40) { level = 'Fair'; color = 'info'; }
            else { level = 'Weak'; color = 'danger'; }

            return { strength, level, color, checks };
        }

        // Initialize password validation
        function initializePasswordValidation() {
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');

            if (!passwordInput || !confirmPasswordInput) return;

            // Create strength indicator
            const strengthContainer = $(`
                <div id="strengthContainer" class="mt-2" style="display: none;">
                    <div class="d-flex align-items-center mb-2">
                        <div class="progress flex-grow-1 mr-2" style="height: 8px;">
                            <div id="strengthProgress" class="progress-bar bg-danger" style="width: 0%"></div>
                        </div>
                        <span id="strengthText" class="badge badge-secondary">Weak</span>
                    </div>
                    <div id="strengthChecks" class="small text-muted"></div>
                </div>
            `);

            $(passwordInput).after(strengthContainer);

            // Password input validation
            $(passwordInput).on('input', function() {
                const password = this.value;

                if (password.length === 0) {
                    $('#strengthContainer').hide();
                    $(this).removeClass('is-invalid is-valid');
                    return;
                }

                const result = calculatePasswordStrength(password);
                $('#strengthContainer').show();

                // Update progress bar
                const progressBar = $('#strengthProgress');
                progressBar.css('width', `${result.strength}%`);
                progressBar.removeClass('bg-danger bg-warning bg-info bg-success');
                progressBar.addClass(`bg-${result.color}`);

                // Update strength text
                const strengthText = $('#strengthText');
                strengthText.text(result.level);
                strengthText.removeClass('badge-danger badge-warning badge-info badge-success');
                strengthText.addClass(`badge-${result.color}`);

                // Update validation visual
                $(this).removeClass('is-invalid is-valid');
                if (result.strength >= 60) {
                    $(this).addClass('is-valid');
                } else if (result.strength < 40) {
                    $(this).addClass('is-invalid');
                }

                // Update checks
                const checksHtml = [
                    ['length', 'At least 8 characters'],
                    ['uppercase', 'Uppercase letter'],
                    ['lowercase', 'Lowercase letter'],
                    ['numbers', 'Number'],
                    ['special', 'Special character']
                ].map(([key, label]) => `
                    <div class="d-flex align-items-center">
                        <span class="mr-1 ${result.checks[key] ? 'text-success' : 'text-danger'}">
                            ${result.checks[key] ? '✓' : '✗'}
                        </span>
                        <span>${label}</span>
                    </div>
                `).join('');
                $('#strengthChecks').html(checksHtml);

                // Check password match if confirm field has value
                if (confirmPasswordInput.value) {
                    validatePasswordMatch();
                }
            });

            // Confirm password validation
            function validatePasswordMatch() {
                const newPassword = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                $(confirmPasswordInput).removeClass('is-invalid is-valid');

                if (!confirmPassword) {
                    return;
                } else if (newPassword === confirmPassword) {
                    $(confirmPasswordInput).addClass('is-valid');
                    confirmPasswordInput.setCustomValidity('');
                } else {
                    $(confirmPasswordInput).addClass('is-invalid');
                    confirmPasswordInput.setCustomValidity('Passwords do not match');
                }
            }

            $(confirmPasswordInput).on('input', validatePasswordMatch);
        }

        // Input formatting
        $('input[name="contact_number"]').on('input', function () {
            this.value = this.value.replace(/\D/g, '').substring(0, 11);
        });

        $('input[name="username"]').on('input', function () {
            this.value = this.value.replace(/\s/g, '').toLowerCase();
        });

        // Block numbers in name fields
        $('input[name="firstname"], input[name="lastname"]').on('input', function () {
            this.value = this.value.replace(/[0-9]/g, '');
        });

        // Form submission
        $('#addUserForm').on('submit', function (e) {
            e.preventDefault();

            const submitButton = $('#submitBtn');
            submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Adding...');

            const formData = new FormData(this);

            $.ajax({
                url: this.action,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function (response) {
                    $('#addUserModal').modal('hide');
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'User added successfully',
                        toast: true,
                        position: 'top',
                        timer: 3000,
                        showConfirmButton: false,
                        background: '#d4edda',
                        color: '#155724'
                    });
                    
                    setTimeout(() => location.reload(), 1000);
                },
                error: function (xhr) {
                    submitButton.prop('disabled', false).html('Add User');

                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorMsg = Object.values(errors).flat().join('<br>');
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: errorMsg,
                            toast: true,
                            position: 'top',
                            timer: 4000,
                            showConfirmButton: false,
                            background: '#f8d7da',
                            color: '#721c24'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'An error occurred. Please try again.',
                            toast: true,
                            position: 'top',
                            timer: 3000,
                            showConfirmButton: false,
                            background: '#f8d7da',
                            color: '#721c24'
                        });
                    }
                }
            });
        });

        // Modal events
        $('#addUserModal').on('shown.bs.modal', function () {
            initializePasswordValidation();
            $('input[name="firstname"]').focus();
        });

        $('#addUserModal').on('hidden.bs.modal', function () {
            $('#addUserForm')[0].reset();
            $('#submitBtn').prop('disabled', false).html('Add User');
            $('#strengthContainer').remove();
            $('input').removeClass('is-invalid is-valid');
        });

        // Session messages
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: {!! json_encode(session('success')) !!},
                toast: true,
                position: 'top',
                timer: 3000,
                showConfirmButton: false,
                background: '#d4edda',
                color: '#155724'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: {!! json_encode(session('error')) !!},
                toast: true,
                position: 'top',
                timer: 4000,
                showConfirmButton: false,
                background: '#f8d7da',
                color: '#721c24'
            });
        @endif

        @if($errors->any())
            let errorMessages = [];
            @foreach($errors->all() as $error)
                errorMessages.push({!! json_encode($error) !!});
            @endforeach

            Swal.fire({
                icon: 'warning',
                title: 'Validation Errors',
                html: errorMessages.join('<br>'),
                toast: true,
                position: 'top',
                timer: 5000,
                showConfirmButton: false,
                background: '#fff3cd',
                color: '#856404'
            });
            
            $('#addUserModal').modal('show');
        @endif

        $('form[action*="updateuser"]').on('submit', function (e) {
            const submitButton = $(this).find('button[type="submit"]');
            submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
        });

    });
</script>

<style>
    .form-control.is-invalid,
    .form-check-input.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    .form-control.is-valid,
    .form-check-input.is-valid {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

    .invalid-feedback {
        display: block;
        font-size: 0.875em;
        color: #dc3545;
        margin-top: 0.25rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    .fas.fa-spinner.fa-spin {
        animation: fa-spin 1s infinite linear;
    }

    @keyframes fa-spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .modal-header.bg-success {
        background-color: #28a745 !important;
    }

    .modal-header.bg-primary {
        background-color: #007bff !important;
    }

    .modal-header.bg-danger {
        background-color: #dc3545 !important;
    }

    .form-control:focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .form-group label .text-danger {
        font-weight: bold;
    }
</style>