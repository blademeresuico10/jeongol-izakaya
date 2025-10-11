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
                                            <input type="text" name="firstname" id="firstname" class="form-control"
                                                required minlength="2"
                                                onkeypress="return /[a-zA-Z\s\-'\.]/i.test(event.key)">
                                            <div>
                                                <small id="firstnameError" class="text-danger text-sm"
                                                    style="display: none;"></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Last Name <span class="text-danger">*</span></label>
                                            <input type="text" name="lastname" id="lastname" class="form-control"
                                                required minlength="2"
                                                onkeypress="return /[a-zA-Z\s\-'\.]/i.test(event.key)">
                                            <div>
                                                <small id="lastnameError" class="text-danger text-sm"
                                                    style="display: none;"></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Role <span class="text-danger">*</span></label>
                                            <select name="role" id="role" class="form-control" required>
                                                <option value="">Select Role</option>
                                                <option value="Manager">Manager</option>
                                                <option value="Receptionist">Receptionist</option>
                                                <option value="Cashier">Cashier</option>
                                                <option value="Kitchen Staff">Kitchen Staff</option>
                                            </select>
                                            <div>
                                                <small id="roleError" class="text-danger text-sm"
                                                    style="display: none;"></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Contact Number <span class="text-danger">*</span></label>
                                            <input type="text" name="contact_number" id="contact_number"
                                                class="form-control" required maxlength="11" placeholder="09xxxxxxxxx"
                                                onkeypress="return /[0-9]/i.test(event.key)">
                                            <div>
                                                <small id="contactNumberError" class="text-danger text-sm"
                                                    style="display: none;"></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="mt-2">Username <span class="text-danger">*</span></label>
                                    <input type="text" name="username" id="username" class="form-control" required
                                        minlength="3" onkeypress="return /[a-zA-Z0-9_]/i.test(event.key)">
                                    <div>
                                        <small id="usernameError" class="text-danger text-sm"
                                            style="display: none;"></small>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="mt-2">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control" required>
                                    <div>
                                        <small id="emailError" class="text-danger text-sm"
                                            style="display: none;"></small>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="mt-2">Address <span class="text-danger">*</span></label>
                                    <input type="text" name="address" id="address" class="form-control" required
                                        minlength="5">
                                    <div>
                                        <small id="addressError" class="text-danger text-sm"
                                            style="display: none;"></small>
                                    </div>
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
                                id="editUserForm{{ $user->id }}" enctype="multipart/form-data">
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
                                        <input type="file" name="profile_picture" class="form-control mb-3" accept="image/*">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>First Name <span class="text-danger">*</span></label>
                                                <input type="text" name="firstname" id="edit_firstname{{ $user->id }}"
                                                    value="{{ $user->firstname }}" class="form-control" required minlength="2"
                                                    onkeypress="return /[a-zA-Z\s\-'\.]/i.test(event.key)">
                                                <div>
                                                    <small id="edit_firstnameError{{ $user->id }}" class="text-danger text-sm"
                                                        style="display: none;"></small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Last Name <span class="text-danger">*</span></label>
                                                <input type="text" name="lastname" id="edit_lastname{{ $user->id }}"
                                                    value="{{ $user->lastname }}" class="form-control" required minlength="2"
                                                    onkeypress="return /[a-zA-Z\s\-'\.]/i.test(event.key)">
                                                <div>
                                                    <small id="edit_lastnameError{{ $user->id }}" class="text-danger text-sm"
                                                        style="display: none;"></small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-md-6">
                                                <label>Role <span class="text-danger">*</span></label>
                                                <select name="role" id="edit_role{{ $user->id }}" class="form-control" required>
                                                    <option value="">Select Role</option>
                                                    <option value="Manager" {{ $user->role == 'Manager' ? 'selected' : '' }}>
                                                        Manager</option>
                                                    <option value="Receptionist" {{ $user->role == 'Receptionist' ? 'selected' : '' }}>Receptionist</option>
                                                    <option value="Cashier" {{ $user->role == 'Cashier' ? 'selected' : '' }}>
                                                        Cashier</option>
                                                    <option value="Kitchen Staff" {{ $user->role == 'Kitchen Staff' ? 'selected' : '' }}>Kitchen Staff</option>
                                                </select>
                                                <div>
                                                    <small id="edit_roleError{{ $user->id }}" class="text-danger text-sm"
                                                        style="display: none;"></small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Contact Number <span class="text-danger">*</span></label>
                                                <input type="text" name="contact_number" id="edit_contact_number{{ $user->id }}"
                                                    value="{{ $user->contact_number }}" class="form-control" required
                                                    maxlength="11" placeholder="09xxxxxxxxx"
                                                    onkeypress="return /[0-9]/i.test(event.key)">
                                                <div>
                                                    <small id="edit_contactNumberError{{ $user->id }}"
                                                        class="text-danger text-sm" style="display: none;"></small>
                                                </div>
                                            </div>
                                        </div>

                                        <label class="mt-2">Username <span class="text-danger">*</span></label>
                                        <input type="text" name="username" id="edit_username{{ $user->id }}"
                                            value="{{ $user->username }}" class="form-control" required minlength="3"
                                            onkeypress="return /[a-zA-Z0-9_]/i.test(event.key)">
                                        <div>
                                            <small id="edit_usernameError{{ $user->id }}" class="text-danger text-sm"
                                                style="display: none;"></small>
                                        </div>

                                        <label class="mt-2">Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" id="edit_email{{ $user->id }}"
                                            value="{{ $user->email }}" class="form-control" required>
                                        <div>
                                            <small id="edit_emailError{{ $user->id }}" class="text-danger text-sm"
                                                style="display: none;"></small>
                                        </div>

                                        <label class="mt-2">Address <span class="text-danger">*</span></label>
                                        <input type="text" name="address" id="edit_address{{ $user->id }}"
                                            value="{{ $user->address }}" class="form-control" required minlength="5">
                                        <div>
                                            <small id="edit_addressError{{ $user->id }}" class="text-danger text-sm"
                                                style="display: none;"></small>
                                        </div>

                                        <label class="mt-2">Update Password</label>
                                        <input type="password" name="password" id="edit_password{{ $user->id }}"
                                            placeholder="Leave blank to keep current" class="form-control" minlength="6">
                                        <div>
                                            <small id="edit_passwordError{{ $user->id }}" class="text-danger text-sm"
                                                style="display: none;"></small>
                                        </div>

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

        function capitalizeFirstLetter(str) {
            return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
        }

        function capitalizeWords(str) {
            return str.split(' ').map(word => {
                if (word.length > 0) {
                    return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
                }
                return word;
            }).join(' ');
        }

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

        function initializePasswordValidation() {
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');

            if (!passwordInput || !confirmPasswordInput) return;

            // Remove existing strength container if any
            $('#strengthContainer').remove();

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

            // Remove existing event listeners
            $(passwordInput).off('input.passwordValidation');
            $(confirmPasswordInput).off('input.passwordMatch');

            $(passwordInput).on('input.passwordValidation', function () {
                const password = this.value;

                if (password.length === 0) {
                    $('#strengthContainer').hide();
                    $(this).removeClass('is-invalid');
                    return;
                }

                const result = calculatePasswordStrength(password);
                $('#strengthContainer').show();

                const progressBar = $('#strengthProgress');
                progressBar.css('width', `${result.strength}%`);
                progressBar.removeClass('bg-danger bg-warning bg-info bg-success');
                progressBar.addClass(`bg-${result.color}`);

                const strengthText = $('#strengthText');
                strengthText.text(result.level);
                strengthText.removeClass('badge-danger badge-warning badge-info badge-success');
                strengthText.addClass(`badge-${result.color}`);

                $(this).removeClass('is-invalid');
                if (result.strength < 40) {
                    $(this).addClass('is-invalid');
                }

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

                if (confirmPasswordInput.value) {
                    validatePasswordMatch();
                }
            });

            function validatePasswordMatch() {
                const newPassword = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                $(confirmPasswordInput).removeClass('is-invalid');

                if (!confirmPassword) {
                    return;
                } else if (newPassword === confirmPassword) {
                    confirmPasswordInput.setCustomValidity('');
                } else {
                    $(confirmPasswordInput).addClass('is-invalid');
                    confirmPasswordInput.setCustomValidity('Passwords do not match');
                }
            }

            $(confirmPasswordInput).on('input.passwordMatch', validatePasswordMatch);
        }

        function initializeUserFormValidation() {
            // First Name Validation
            const firstnameInput = document.getElementById('firstname');
            const firstnameError = document.getElementById('firstnameError');

            if (firstnameInput && firstnameError) {
                $(firstnameInput).off('input.firstname');
                $(firstnameInput).on('input.firstname', function () {
                    // Remove non-allowed characters first
                    this.value = this.value.replace(/[^a-zA-Z\s\-'\.]/g, '');

                    // Capitalize first letter of each word
                    this.value = capitalizeWords(this.value);

                    const value = this.value.trim();

                    if (!value) {
                        firstnameError.textContent = '';
                        firstnameError.style.display = 'none';
                        this.classList.remove('is-invalid');
                        return;
                    }

                    if (value.length < 2) {
                        firstnameError.textContent = 'Minimum 2 characters required';
                        firstnameError.style.display = 'block';
                        this.classList.add('is-invalid');
                    } else {
                        firstnameError.textContent = '';
                        firstnameError.style.display = 'none';
                        this.classList.remove('is-invalid');
                    }
                });
            }

            // Last Name Validation
            const lastnameInput = document.getElementById('lastname');
            const lastnameError = document.getElementById('lastnameError');

            if (lastnameInput && lastnameError) {
                $(lastnameInput).off('input.lastname');
                $(lastnameInput).on('input.lastname', function () {
                    // Remove non-allowed characters first
                    this.value = this.value.replace(/[^a-zA-Z\s\-'\.]/g, '');

                    // Capitalize first letter of each word
                    this.value = capitalizeWords(this.value);

                    const value = this.value.trim();

                    if (!value) {
                        lastnameError.textContent = '';
                        lastnameError.style.display = 'none';
                        this.classList.remove('is-invalid');
                        return;
                    }

                    if (value.length < 2) {
                        lastnameError.textContent = 'Minimum 2 characters required';
                        lastnameError.style.display = 'block';
                        this.classList.add('is-invalid');
                    } else {
                        lastnameError.textContent = '';
                        lastnameError.style.display = 'none';
                        this.classList.remove('is-invalid');
                    }
                });
            }

            // Role Validation
            const roleInput = document.getElementById('role');
            const roleError = document.getElementById('roleError');

            if (roleInput && roleError) {
                $(roleInput).off('change.role');
                $(roleInput).on('change.role', function () {
                    const value = this.value;

                    if (!value) {
                        roleError.textContent = 'Please select a role';
                        roleError.style.display = 'block';
                        this.classList.add('is-invalid');
                    } else {
                        roleError.textContent = '';
                        roleError.style.display = 'none';
                        this.classList.remove('is-invalid');
                    }
                });
            }

            // Contact Number Validation
            const contactNumberInput = document.getElementById('contact_number');
            const contactNumberError = document.getElementById('contactNumberError');

            if (contactNumberInput && contactNumberError) {
                $(contactNumberInput).off('input.contact');
                $(contactNumberInput).on('input.contact', function () {
                    this.value = this.value.replace(/[^0-9]/g, '').substring(0, 11);
                    const value = this.value.trim();

                    if (!value) {
                        contactNumberError.textContent = '';
                        contactNumberError.style.display = 'none';
                        this.classList.remove('is-invalid');
                        return;
                    }

                    if (!value.startsWith('09')) {
                        contactNumberError.textContent = 'Contact number must start with 09';
                        contactNumberError.style.display = 'block';
                        this.classList.add('is-invalid');
                        return;
                    }

                    if (value.length < 11) {
                        contactNumberError.textContent = 'Contact number must be 11 digits';
                        contactNumberError.style.display = 'block';
                        this.classList.add('is-invalid');
                    } else {
                        contactNumberError.textContent = '';
                        contactNumberError.style.display = 'none';
                        this.classList.remove('is-invalid');
                    }
                });
            }

            // Username Validation
            const usernameInput = document.getElementById('username');
            const usernameError = document.getElementById('usernameError');

            if (usernameInput && usernameError) {
                $(usernameInput).off('input.username');
                $(usernameInput).on('input.username', function () {
                    this.value = this.value.replace(/[^a-zA-Z0-9_]/g, '').toLowerCase().replace(/\s/g, '');
                    const value = this.value.trim();

                    if (!value) {
                        usernameError.textContent = '';
                        usernameError.style.display = 'none';
                        this.classList.remove('is-invalid');
                        return;
                    }

                    if (value.length < 3) {
                        usernameError.textContent = 'Minimum 3 characters required';
                        usernameError.style.display = 'block';
                        this.classList.add('is-invalid');
                    } else {
                        usernameError.textContent = '';
                        usernameError.style.display = 'none';
                        this.classList.remove('is-invalid');
                    }
                });
            }

            // Email Validation
            const emailInput = document.getElementById('email');
            const emailError = document.getElementById('emailError');

            if (emailInput && emailError) {
                $(emailInput).off('input.email');
                $(emailInput).on('input.email', function () {
                    const value = this.value.trim();

                    if (!value) {
                        emailError.textContent = '';
                        emailError.style.display = 'none';
                        this.classList.remove('is-invalid');
                        return;
                    }

                    if (!value.includes('@')) {
                        emailError.textContent = 'Email must contain @';
                        emailError.style.display = 'block';
                        this.classList.add('is-invalid');
                        return;
                    }

                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value)) {
                        emailError.textContent = 'Enter a valid email address';
                        emailError.style.display = 'block';
                        this.classList.add('is-invalid');
                    } else {
                        emailError.textContent = '';
                        emailError.style.display = 'none';
                        this.classList.remove('is-invalid');
                    }
                });
            }

            // Address Validation
            const addressInput = document.getElementById('address');
            const addressError = document.getElementById('addressError');

            if (addressInput && addressError) {
                $(addressInput).off('input.address');
                $(addressInput).on('input.address', function () {
                    const value = this.value.trim();

                    if (!value) {
                        addressError.textContent = '';
                        addressError.style.display = 'none';
                        this.classList.remove('is-invalid');
                        return;
                    }

                    if (value.length < 5) {
                        addressError.textContent = 'Minimum 5 characters required';
                        addressError.style.display = 'block';
                        this.classList.add('is-invalid');
                    } else {
                        addressError.textContent = '';
                        addressError.style.display = 'none';
                        this.classList.remove('is-invalid');
                    }
                });
            }
        }

        function initializeEditUserFormValidation(userId) {
    // First Name Validation
    const firstnameInput = document.getElementById('edit_firstname' + userId);
    const firstnameError = document.getElementById('edit_firstnameError' + userId);

    if (firstnameInput && firstnameError) {
        $(firstnameInput).off('input.firstname');
        $(firstnameInput).on('input.firstname', function () {
            this.value = this.value.replace(/[^a-zA-Z\s\-'\.]/g, '');
            this.value = capitalizeWords(this.value);
            const value = this.value.trim();

            if (!value) {
                firstnameError.textContent = '';
                firstnameError.style.display = 'none';
                this.classList.remove('is-invalid');
                return;
            }

            if (value.length < 2) {
                firstnameError.textContent = 'Minimum 2 characters required';
                firstnameError.style.display = 'block';
                this.classList.add('is-invalid');
            } else {
                firstnameError.textContent = '';
                firstnameError.style.display = 'none';
                this.classList.remove('is-invalid');
            }
        });
    }

    // Last Name Validation
    const lastnameInput = document.getElementById('edit_lastname' + userId);
    const lastnameError = document.getElementById('edit_lastnameError' + userId);

    if (lastnameInput && lastnameError) {
        $(lastnameInput).off('input.lastname');
        $(lastnameInput).on('input.lastname', function () {
            this.value = this.value.replace(/[^a-zA-Z\s\-'\.]/g, '');
            this.value = capitalizeWords(this.value);
            const value = this.value.trim();

            if (!value) {
                lastnameError.textContent = '';
                lastnameError.style.display = 'none';
                this.classList.remove('is-invalid');
                return;
            }

            if (value.length < 2) {
                lastnameError.textContent = 'Minimum 2 characters required';
                lastnameError.style.display = 'block';
                this.classList.add('is-invalid');
            } else {
                lastnameError.textContent = '';
                lastnameError.style.display = 'none';
                this.classList.remove('is-invalid');
            }
        });
    }

    // Role Validation
    const roleInput = document.getElementById('edit_role' + userId);
    const roleError = document.getElementById('edit_roleError' + userId);

    if (roleInput && roleError) {
        $(roleInput).off('change.role');
        $(roleInput).on('change.role', function () {
            const value = this.value;

            if (!value) {
                roleError.textContent = 'Please select a role';
                roleError.style.display = 'block';
                this.classList.add('is-invalid');
            } else {
                roleError.textContent = '';
                roleError.style.display = 'none';
                this.classList.remove('is-invalid');
            }
        });
    }

    // Contact Number Validation
    const contactNumberInput = document.getElementById('edit_contact_number' + userId);
    const contactNumberError = document.getElementById('edit_contactNumberError' + userId);

    if (contactNumberInput && contactNumberError) {
        $(contactNumberInput).off('input.contact');
        $(contactNumberInput).on('input.contact', function () {
            this.value = this.value.replace(/[^0-9]/g, '').substring(0, 11);
            const value = this.value.trim();

            if (!value) {
                contactNumberError.textContent = '';
                contactNumberError.style.display = 'none';
                this.classList.remove('is-invalid');
                return;
            }

            if (!value.startsWith('09')) {
                contactNumberError.textContent = 'Contact number must start with 09';
                contactNumberError.style.display = 'block';
                this.classList.add('is-invalid');
                return;
            }

            if (value.length < 11) {
                contactNumberError.textContent = 'Contact number must be 11 digits';
                contactNumberError.style.display = 'block';
                this.classList.add('is-invalid');
            } else {
                contactNumberError.textContent = '';
                contactNumberError.style.display = 'none';
                this.classList.remove('is-invalid');
            }
        });
    }

    // Username Validation
    const usernameInput = document.getElementById('edit_username' + userId);
    const usernameError = document.getElementById('edit_usernameError' + userId);

    if (usernameInput && usernameError) {
        $(usernameInput).off('input.username');
        $(usernameInput).on('input.username', function () {
            this.value = this.value.replace(/[^a-zA-Z0-9_]/g, '').toLowerCase().replace(/\s/g, '');
            const value = this.value.trim();

            if (!value) {
                usernameError.textContent = '';
                usernameError.style.display = 'none';
                this.classList.remove('is-invalid');
                return;
            }

            if (value.length < 3) {
                usernameError.textContent = 'Minimum 3 characters required';
                usernameError.style.display = 'block';
                this.classList.add('is-invalid');
            } else {
                usernameError.textContent = '';
                usernameError.style.display = 'none';
                this.classList.remove('is-invalid');
            }
        });
    }

    // Email Validation
    const emailInput = document.getElementById('edit_email' + userId);
    const emailError = document.getElementById('edit_emailError' + userId);

    if (emailInput && emailError) {
        $(emailInput).off('input.email');
        $(emailInput).on('input.email', function () {
            const value = this.value.trim();

            if (!value) {
                emailError.textContent = '';
                emailError.style.display = 'none';
                this.classList.remove('is-invalid');
                return;
            }

            if (!value.includes('@')) {
                emailError.textContent = 'Email must contain @';
                emailError.style.display = 'block';
                this.classList.add('is-invalid');
                return;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                emailError.textContent = 'Enter a valid email address';
                emailError.style.display = 'block';
                this.classList.add('is-invalid');
            } else {
                emailError.textContent = '';
                emailError.style.display = 'none';
                this.classList.remove('is-invalid');
            }
        });
    }

    // Address Validation
    const addressInput = document.getElementById('edit_address' + userId);
    const addressError = document.getElementById('edit_addressError' + userId);

    if (addressInput && addressError) {
        $(addressInput).off('input.address');
        $(addressInput).on('input.address', function () {
            const value = this.value.trim();

            if (!value) {
                addressError.textContent = '';
                addressError.style.display = 'none';
                this.classList.remove('is-invalid');
                return;
            }

            if (value.length < 5) {
                addressError.textContent = 'Minimum 5 characters required';
                addressError.style.display = 'block';
                this.classList.add('is-invalid');
            } else {
                addressError.textContent = '';
                addressError.style.display = 'none';
                this.classList.remove('is-invalid');
            }
        });
    }

    // Password Validation (optional field)
    const passwordInput = document.getElementById('edit_password' + userId);
    const passwordError = document.getElementById('edit_passwordError' + userId);

    if (passwordInput && passwordError) {
        $(passwordInput).off('input.password');
        $(passwordInput).on('input.password', function () {
            const value = this.value;

            if (!value) {
                passwordError.textContent = '';
                passwordError.style.display = 'none';
                this.classList.remove('is-invalid');
                return;
            }

            if (value.length < 6) {
                passwordError.textContent = 'Minimum 6 characters required';
                passwordError.style.display = 'block';
                this.classList.add('is-invalid');
            } else {
                passwordError.textContent = '';
                passwordError.style.display = 'none';
                this.classList.remove('is-invalid');
            }
        });
    }
}

$('[id^="editUserModal"]').on('shown.bs.modal', function () {
    const modalId = $(this).attr('id');
    const userId = modalId.replace('editUserModal', '');
    initializeEditUserFormValidation(userId);
});

$('[id^="editUserModal"]').on('hidden.bs.modal', function () {
    const modalId = $(this).attr('id');
    const userId = modalId.replace('editUserModal', '');
    $(`#editUserForm${userId} input, #editUserForm${userId} select`).removeClass('is-invalid');
    $(`small[id^="edit_"][id$="Error${userId}"]`).hide().text('');
});

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
            initializeUserFormValidation();
            initializePasswordValidation();
            $('input[name="firstname"]').focus();
        });

        $('#addUserModal').on('hidden.bs.modal', function () {
            $('#addUserForm')[0].reset();
            $('#submitBtn').prop('disabled', false).html('Add User');
            $('#strengthContainer').remove();
            $('input, select').removeClass('is-invalid');
            $('small[id$="Error"]').hide().text('');
        });

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