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
                                                <option value="Wait Staff">Wait Staff</option>
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
                                            <div class="input-group">
                                                <input type="password" id="password" name="password"
                                                    class="form-control" required minlength="6">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button"
                                                        onclick="togglePassword('password', this)" tabindex="-1">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Confirm Password <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" id="confirm_password"
                                                    name="password_confirmation" class="form-control" required>
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button"
                                                        onclick="togglePassword('confirm_password', this)"
                                                        tabindex="-1">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
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
                                                    <option value="Wait Staff" {{ $user->role == 'Wait Staff' ? 'selected' : '' }}>Wait Staff</option>
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
                                        <div class="input-group">
                                            <input type="password" name="password" id="edit_password{{ $user->id }}"
                                                placeholder="Leave blank to keep current" class="form-control" minlength="6">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button"
                                                    onclick="togglePassword('edit_password{{ $user->id }}', this)"
                                                    tabindex="-1">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
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

        function initializePasswordValidation(passwordInputId, confirmPasswordInputId = null, userId = null) {
            const passwordInput = document.getElementById(passwordInputId);
            if (!passwordInput) return;

            const confirmPasswordInput = confirmPasswordInputId ? document.getElementById(confirmPasswordInputId) : null;
            const containerId = `strengthContainer_${passwordInputId}`;
            const isEditMode = passwordInputId.includes('edit_password');

            $(`#${containerId}`).remove();

            const strengthContainer = $(`
    <div id="${containerId}" class="mt-2" style="display: none;">
        <div class="d-flex align-items-center mb-2">
            <div class="progress flex-grow-1 mr-2" style="height: 8px;">
                <div id="strengthProgress_${passwordInputId}" class="progress-bar" style="width: 0%; transition: width 0.3s ease, background-color 0.3s ease;"></div>
            </div>
            <span id="strengthText_${passwordInputId}" class="badge" style="min-width: 60px; text-align: center;">Weak</span>
        </div>
        <div id="strengthChecks_${passwordInputId}" class="small text-muted"></div>
        ${isEditMode ? `<div id="currentPasswordWarning_${passwordInputId}" class="alert alert-warning mt-2" style="display: none;">
            <i class="fas fa-exclamation-triangle"></i> This is your current password. Please enter a new password.
        </div>` : ''}
    </div>
`);

            const parentElement = $(passwordInput).closest('.input-group').length
                ? $(passwordInput).closest('.input-group')
                : $(passwordInput);

            parentElement.after(strengthContainer);

            let debouncedPasswordCheck = null;
            if (isEditMode && userId) {
                debouncedPasswordCheck = debounce(function (password) {
                    $.ajax({
                        url: "{{ route('check.current.password') }}",
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            user_id: userId,
                            password: password
                        },
                        success: function (response) {
                            if (response.is_current) {
                                $(`#currentPasswordWarning_${passwordInputId}`).show();
                                $(passwordInput).addClass('is-invalid');
                            } else {
                                $(`#currentPasswordWarning_${passwordInputId}`).hide();
                                if ($(`#currentPasswordWarning_${passwordInputId}`).is(':visible')) {
                                    $(passwordInput).removeClass('is-invalid');
                                }
                            }
                        }
                    });
                }, 500);
            }

            $(passwordInput).off('input.passwordValidation');

            $(passwordInput).on('input.passwordValidation', function () {
                const password = this.value;

                if (password.length === 0) {
                    $(`#${containerId}`).hide();
                    if (isEditMode) {
                        $(`#currentPasswordWarning_${passwordInputId}`).hide();
                    }
                    $(this).removeClass('is-invalid is-valid');
                    return;
                }

                const result = calculatePasswordStrength(password);
                $(`#${containerId}`).show();

                const progressBar = $(`#strengthProgress_${passwordInputId}`);
                progressBar.css('width', `${result.strength}%`);

                progressBar.removeClass('bg-danger bg-warning bg-info bg-success');

                progressBar.addClass(`bg-${result.color}`);

                const strengthText = $(`#strengthText_${passwordInputId}`);
                strengthText.text(result.level);

                strengthText.removeClass('badge-danger badge-warning badge-info badge-success badge-secondary');

                strengthText.addClass(`badge-${result.color}`);

                $(this).removeClass('is-invalid is-valid');
                if (result.strength < 40) {
                    $(this).addClass('is-invalid');
                } else {
                    $(this).addClass('is-valid');
                }

                const checksHtml = [
                    ['length', 'At least 8 characters'],
                    ['uppercase', 'Uppercase letter'],
                    ['lowercase', 'Lowercase letter'],
                    ['numbers', 'Number'],
                    ['special', 'Special character']
                ].map(([key, label]) => `
        <div class="d-flex align-items-center" style="line-height: 1.5;">
            <span class="mr-1 ${result.checks[key] ? 'text-success' : 'text-danger'}" style="font-weight: bold;">
                ${result.checks[key] ? '✓' : '✗'}
            </span>
            <span class="${result.checks[key] ? 'text-success' : 'text-muted'}">${label}</span>
        </div>
    `).join('');
                $(`#strengthChecks_${passwordInputId}`).html(checksHtml);

                if (isEditMode && result.strength >= 40 && debouncedPasswordCheck) {
                    debouncedPasswordCheck(password);
                }

                if (confirmPasswordInput && confirmPasswordInput.value) {
                    validatePasswordMatch();
                }
            });

            if (confirmPasswordInput) {
                $(confirmPasswordInput).off('input.passwordMatch');

                function validatePasswordMatch() {
                    const newPassword = passwordInput.value;
                    const confirmPassword = confirmPasswordInput.value;

                    $(confirmPasswordInput).removeClass('is-invalid is-valid');

                    if (!confirmPassword) {
                        confirmPasswordInput.setCustomValidity('');
                        return;
                    }

                    if (newPassword === confirmPassword) {
                        confirmPasswordInput.setCustomValidity('');
                        $(confirmPasswordInput).addClass('is-valid');
                    } else {
                        $(confirmPasswordInput).addClass('is-invalid');
                        confirmPasswordInput.setCustomValidity('Passwords do not match');
                    }
                }

                $(confirmPasswordInput).on('input.passwordMatch', validatePasswordMatch);
            }
        }

        function initializeUserFormValidation() {
            const firstnameInput = document.getElementById('firstname');
            const firstnameError = document.getElementById('firstnameError');

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

            const lastnameInput = document.getElementById('lastname');
            const lastnameError = document.getElementById('lastnameError');

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

            const usernameInput = document.getElementById('username');
            const usernameError = document.getElementById('usernameError');

            if (usernameInput && usernameError) {
                $(usernameInput).off('input.username');

                const debouncedUsernameCheck = debounce(function (value) {
                    checkAvailability('username', value, null, usernameError, usernameInput);
                }, 500);

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
                        debouncedUsernameCheck(value);
                    }
                });
            }

            const emailInput = document.getElementById('email');
            const emailError = document.getElementById('emailError');

            if (emailInput && emailError) {
                $(emailInput).off('input.email');

                const debouncedEmailCheck = debounce(function (value) {
                    checkAvailability('email', value, null, emailError, emailInput);
                }, 500);

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
                        debouncedEmailCheck(value);
                    }
                });
            }

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

        function checkAvailability(field, value, userId = null, errorElement, inputElement) {
            if (!value || value.length < 3) return;

            $.ajax({
                url: "{{ route('check.user.availability') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    field: field,
                    value: value,
                    user_id: userId
                },
                success: function (response) {
                    if (!response.available) {
                        errorElement.textContent = `This ${field} is already taken`;
                        errorElement.style.display = 'block';
                        inputElement.classList.add('is-invalid');
                    } else {
                        if (errorElement.textContent.includes('already taken')) {
                            errorElement.textContent = '';
                            errorElement.style.display = 'none';
                            inputElement.classList.remove('is-invalid');
                        }
                    }
                }
            });
        }

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        function initializeEditUserFormValidation(userId) {
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

            const usernameInput = document.getElementById('edit_username' + userId);
            const usernameError = document.getElementById('edit_usernameError' + userId);

            if (usernameInput && usernameError) {
                $(usernameInput).off('input.username');

                const debouncedUsernameCheck = debounce(function (value) {
                    checkAvailability('username', value, userId, usernameError, usernameInput);
                }, 500);

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
                        debouncedUsernameCheck(value);
                    }
                });
            }

            const emailInput = document.getElementById('edit_email' + userId);
            const emailError = document.getElementById('edit_emailError' + userId);

            if (emailInput && emailError) {
                $(emailInput).off('input.email');

                const debouncedEmailCheck = debounce(function (value) {
                    checkAvailability('email', value, userId, emailError, emailInput);
                }, 500);

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
                        debouncedEmailCheck(value);
                    }
                });
            }

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
        }



        $('[id^="editUserModal"]').on('shown.bs.modal', function () {
            const modalId = $(this).attr('id');
            const userId = modalId.replace('editUserModal', '');
            initializePasswordValidation(`edit_password${userId}`, null, userId);
            initializeEditUserFormValidation(userId);
        });

        $('[id^="editUserModal"]').on('hidden.bs.modal', function () {
            const modalId = $(this).attr('id');
            const userId = modalId.replace('editUserModal', '');
            $(`#editUserForm${userId} input, #editUserForm${userId} select`).removeClass('is-invalid is-valid');
            $(`small[id^="edit_"][id$="Error${userId}"]`).hide().text('');
            $(`[id^="strengthContainer_edit_password${userId}"]`).remove();
        });

        $('#addUserForm').on('submit', function (e) {
            e.preventDefault();

            const hasInvalidFields = $(this).find('.is-invalid').length > 0;

            if (hasInvalidFields) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please fix all validation errors before submitting.',
                    toast: true,
                    position: 'top',
                    timer: 3000,
                    showConfirmButton: false,
                    background: '#f8d7da',
                    color: '#721c24'
                });
                return false;
            }

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

        $('#addUserModal').on('shown.bs.modal', function () {
            initializePasswordValidation('password', 'confirm_password');
            initializeUserFormValidation();
            $('input[name="firstname"]').focus();
        });

        $('#addUserModal').on('hidden.bs.modal', function () {
            $('#addUserForm')[0].reset();
            $('#submitBtn').prop('disabled', false).html('Add User');
            $('[id^="strengthContainer_"]').remove();
            $('input, select').removeClass('is-invalid is-valid');
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
            e.preventDefault(); 

            const form = $(this);
            const userId = form.attr('id').replace('editUserForm', '');
            const passwordInput = $(`#edit_password${userId}`);
            const passwordValue = passwordInput.val();

            const hasInvalidFields = form.find('.is-invalid').length > 0;
            const currentPasswordWarning = $(`#currentPasswordWarning_edit_password${userId}`).is(':visible');

            if (hasInvalidFields || currentPasswordWarning) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: currentPasswordWarning
                        ? 'You cannot use your current password as the new password.'
                        : 'Please fix all validation errors before submitting.',
                    toast: true,
                    position: 'top',
                    timer: 3000,
                    showConfirmButton: false,
                    background: '#f8d7da',
                    color: '#721c24'
                });
                return false;
            }

            if (passwordValue && passwordValue.trim() !== '') {
                const submitButton = form.find('button[type="submit"]');
                submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Checking...');

                $.ajax({
                    url: "{{ route('check.current.password') }}",
                    method: 'POST',
                    async: false,
                    data: {
                        _token: '{{ csrf_token() }}',
                        user_id: userId,
                        password: passwordValue
                    },
                    success: function (response) {
                        if (response.is_current) {
                            submitButton.prop('disabled', false).html('Update User');

                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid Password',
                                text: 'You cannot use your current password as the new password.',
                                toast: true,
                                position: 'top',
                                timer: 3000,
                                showConfirmButton: false,
                                background: '#f8d7da',
                                color: '#721c24'
                            });

                            $(`#currentPasswordWarning_edit_password${userId}`).show();
                            passwordInput.addClass('is-invalid');
                        } else {
                            submitButton.html('<i class="fas fa-spinner fa-spin"></i> Updating...');
                            form.off('submit').submit();
                        }
                    },
                    error: function () {
                        submitButton.prop('disabled', false).html('Update User');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while validating the password.',
                            toast: true,
                            position: 'top',
                            timer: 3000,
                            showConfirmButton: false,
                            background: '#f8d7da',
                            color: '#721c24'
                        });
                    }
                });
                return false;
            }

            const submitButton = form.find('button[type="submit"]');
            submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
            form.off('submit').submit();
        });

    });

    function togglePassword(inputId, button) {
        const input = document.getElementById(inputId);
        const icon = button.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
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

    .input-group {
        position: relative;
    }

    .input-group .form-control.is-invalid {
        border-right: 1px solid #dc3545;
    }

    .input-group-append .btn {
        height: 38px;
        border-left: 0;
    }

    .input-group .form-control:focus {
        z-index: 1;
    }

    #strengthContainer {
        clear: both;
    }
</style>