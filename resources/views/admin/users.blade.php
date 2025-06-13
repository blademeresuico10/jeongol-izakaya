@include('admin.layouts.header')
@include('admin.layouts.sidebar')

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <h1 class="h3 mb-0 text-gray-800">User Management</h1>
        </nav>

        <!-- Begin Page Content -->
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card mt-2" style="max-width: 100%;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Users</h6>
                    <a href="{{ url('adduser') }}" class="btn btn-sm btn-success">Add User</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        @if ($users->count())
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Role</th>
                                        <th>Contact</th>
                                        <th>Status</th>
                                        <th>Actions</th>

                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>{{ $user->firstname }} {{ $user->lastname }}</td>
                                            <td>{{ ucfirst($user->role) }}</td>
                                            <td>{{ $user->contact_number }}</td>
                                            <td>
                                                @if ($user->status ?? true)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle">
                                                <!-- Functional Pencil Icon -->
                                                <a href="#" title="Modify" data-toggle="modal" data-target="#editUserModal{{ $user->id }}" style="all: unset; cursor: pointer;">
                                                    <i class="fas fa-edit text-primary"></i>
                                                </a>
                                            </td>
                                        </tr>

                                        <!-- Edit User Modal -->
                                        <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <form action="{{ route('admin.updateuser', $user->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit User</h5>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <label>Name</label>
                                                            <input type="text" name="name" value="{{ $user->firstname }} {{ $user->lastname }}" class="form-control">

                                                            <label>Role</label>
                                                            <select name="role" class="form-control">
                                                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                                                <option value="receptionist" {{ $user->role == 'receptionist' ? 'selected' : '' }}>Receptionist</option>
                                                                <option value="cashier" {{ $user->role == 'cashier' ? 'selected' : '' }}>Cashier</option>
                                                                \<option value="kitchen-staff" {{ $user->role == 'kitchen-staff' ? 'selected' : '' }}>Kitchen Staff</option>

                                                            </select>
                                                            <label>Password</label>
                                                            <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
     

                                                            <label>Status</label><br>
                                                            <input type="checkbox" name="status" {{ $user->status ? 'checked' : '' }}> Active
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary">Update</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>


                            </table>
                        @else
                            <p class="text-center">No users found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->

    </div>
</div>

@include('admin.layouts.script')


