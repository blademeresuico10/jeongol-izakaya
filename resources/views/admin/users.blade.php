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

            <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog">
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
                    <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" role="dialog">
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

            <div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog">
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

            <div class="modal fade" id="restoreConfirmModal" tabindex="-1" role="dialog">
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

            <div class="modal fade" id="forceDeleteConfirmModal" tabindex="-1" role="dialog">
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
                        showConfirmButton: false
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
                        showConfirmButton: false
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
                        showConfirmButton: false
                    });
                }
            } catch (error) {
                console.error('Error showing force delete modal:', error);
            }
        };

        function validateField(field, errorMessage = '') {
            const $field = $(field);
            const $feedback = $field.siblings('.invalid-feedback');

            if (!field.checkValidity() || errorMessage) {
                $field.removeClass('is-valid').addClass('is-invalid');
                $feedback.text(errorMessage || field.validationMessage);
                return false;
            } else {
                $field.removeClass('is-invalid').addClass('is-valid');
                $feedback.text('');
                return true;
            }
        }

        function clearValidation() {
            $('#addUserForm .form-control, #addUserForm .form-check-input').removeClass('is-invalid is-valid');
            $('#addUserForm .invalid-feedback').text('');
            $('#password-warning').hide();
            $('#submitBtn').prop('disabled', false);
        }

        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const warning = document.getElementById('password-warning');
        const submitBtn = document.getElementById('submitBtn');

        function checkPasswordMatch() {
            if (!password || !confirmPassword) return true;

            if (!password.value || !confirmPassword.value) {
                warning.style.display = 'none';
                submitBtn.disabled = false;
                return true;
            }

            if (password.value !== confirmPassword.value) {
                warning.style.display = 'block';
                validateField(confirmPassword, 'Passwords do not match');
                submitBtn.disabled = true;
                return false;
            } else {
                warning.style.display = 'none';
                validateField(password);
                validateField(confirmPassword);
                submitBtn.disabled = false;
                return true;
            }
        }

        if (password && confirmPassword) {
            password.addEventListener('input', checkPasswordMatch);
            confirmPassword.addEventListener('input', checkPasswordMatch);
        }

        // Real-time validation for form fields
        $('#addUserForm input, #addUserForm select').on('blur input', function () {
            validateField(this);

            // Special validation for passwords
            if (this.name === 'password' || this.name === 'password_confirmation') {
                checkPasswordMatch();
            }

            // Special validation for contact number (max 11 digits as per your backend)
            if (this.name === 'contact_number') {
                this.value = this.value.replace(/\D/g, '');
                if (this.value.length > 11) {
                    this.value = this.value.substring(0, 11);
                    validateField(this, 'Contact number cannot exceed 11 digits');
                }
            }

            // Special validation for username
            if (this.name === 'username') {
                this.value = this.value.replace(/\s/g, '').toLowerCase();
                if (this.value.length > 0 && !/^[a-zA-Z0-9_]+$/.test(this.value)) {
                    validateField(this, 'Username can only contain letters, numbers, and underscores');
                }
            }

            // Special validation for names (firstname, lastname)
            if (this.name === 'firstname' || this.name === 'lastname') {
                if (this.value.length > 0 && !/^[a-zA-Z\s]+$/.test(this.value)) {
                    validateField(this, 'Name can only contain letters and spaces');
                }
            }

            // Password minimum length validation (8 characters as per your backend)
            if (this.name === 'password' && this.value.length > 0 && this.value.length < 8) {
                validateField(this, 'Password must be at least 8 characters');
            }
        });

        // Enhanced form submission with validation
        $('#addUserForm').on('submit', function (e) {
            e.preventDefault();

            let isValid = true;
            const form = this;

            // Validate all required fields
            $(form).find('[required]').each(function () {
                if (!validateField(this)) {
                    isValid = false;
                }
            });

            // Check password match
            if (!checkPasswordMatch()) {
                isValid = false;
            }

            // Additional custom validations
            const contactNumber = form.contact_number.value;
            if (contactNumber && contactNumber.length > 11) {
                validateField(form.contact_number, 'Contact number cannot exceed 11 digits');
                isValid = false;
            }

            const username = form.username.value;
            if (username && !/^[a-zA-Z0-9_]+$/.test(username)) {
                validateField(form.username, 'Username can only contain letters, numbers, and underscores');
                isValid = false;
            }

            const firstname = form.firstname.value;
            if (firstname && !/^[a-zA-Z\s]+$/.test(firstname)) {
                validateField(form.firstname, 'First name can only contain letters and spaces');
                isValid = false;
            }

            const lastname = form.lastname.value;
            if (lastname && !/^[a-zA-Z\s]+$/.test(lastname)) {
                validateField(form.lastname, 'Last name can only contain letters and spaces');
                isValid = false;
            }

            // Password minimum length check (matching your backend requirement)
            const passwordField = form.password;
            if (passwordField && passwordField.value && passwordField.value.length < 8) {
                validateField(passwordField, 'Password must be at least 8 characters');
                isValid = false;
            }

            if (!isValid) {
                // Scroll to first invalid field
                const firstInvalid = $(form).find('.is-invalid').first();
                if (firstInvalid.length) {
                    firstInvalid.focus();
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please fix the highlighted errors before submitting.',
                    toast: true,
                    position: 'top',
                    timer: 3000,
                    showConfirmButton: false
                });
                return false;
            }

            // If validation passes, submit the form via AJAX
            const submitButton = $('#submitBtn');
            submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Adding...');

            // Submit form via AJAX
            const formData = new FormData(form);

            $.ajax({
                url: form.action,
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
                        title: 'Success!',
                        text: response.message || 'User added successfully.',
                        toast: true,
                        position: 'top',
                        timer: 3000,
                        showConfirmButton: false
                    });
                    // Reload the page to show the new user
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                },
                error: function (xhr) {
                    submitButton.prop('disabled', false).html('Add User');

                    if (xhr.status === 422) {
                        // Handle validation errors from server
                        const errors = xhr.responseJSON.errors;

                        // Clear previous validations
                        $(form).find('.is-invalid').removeClass('is-invalid');
                        $(form).find('.invalid-feedback').text('');

                        // Show server validation errors
                        $.each(errors, function (field, messages) {
                            const $field = $(form).find(`[name="${field}"]`);
                            const $feedback = $field.siblings('.invalid-feedback');

                            $field.addClass('is-invalid');
                            $feedback.text(messages[0]);
                        });

                        // Focus on first error field
                        const firstErrorField = $(form).find('.is-invalid').first();
                        if (firstErrorField.length) {
                            firstErrorField.focus();
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Failed',
                            text: 'Please check the highlighted fields and try again.',
                            toast: true,
                            position: 'top',
                            timer: 4000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'An error occurred. Please try again.',
                            toast: true,
                            position: 'top',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    }
                }
            });
        });

        // Handle server-side validation errors display
        @if ($errors->any())
            $('#addUserModal').modal('show');
        @endif

        // Modal reset functionality
        $('#addUserModal').on('hidden.bs.modal', function () {
            try {
                $(this).find('form')[0].reset();
                clearValidation();
            } catch (error) {
                console.error('Error resetting form:', error);
            }
        });

        // Session message handling
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
            @endif

        // Update user form submission handling
        $('form[action*="updateuser"]').on('submit', function (e) {
            const submitButton = $(this).find('button[type="submit"]');
            submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');

            setTimeout(function () {
                submitButton.prop('disabled', false).html('Update User');
            }, 3000);
        });

        // Modal focus management
        $('#addUserModal').on('shown.bs.modal', function () {
            $('input[name="firstname"]').focus();
        });

        // Input formatting for contact numbers (legacy support)
        $('input[name="contact"], input[name="contact_number"]').on('input', function () {
            this.value = this.value.replace(/\D/g, '');
        });

        // Username formatting (legacy support)  
        $('input[name="username"]').on('input', function () {
            this.value = this.value.replace(/\s/g, '').toLowerCase();
        });

    });
</script>

<style>
    /* Enhanced validation styles */
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

    /* Loading spinner styles */
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

    /* Enhanced modal styles */
    .modal-header.bg-success {
        background-color: #28a745 !important;
    }

    .modal-header.bg-primary {
        background-color: #007bff !important;
    }

    .modal-header.bg-danger {
        background-color: #dc3545 !important;
    }

    /* Focus states */
    .form-control:focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    /* Required field indicator */
    .form-group label .text-danger {
        font-weight: bold;
    }
</style>