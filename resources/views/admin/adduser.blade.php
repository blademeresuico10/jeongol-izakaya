@include('admin.layouts.header')

<!-- Sidebar -->
@include('admin.layouts.sidebar')
<!-- End of Sidebar -->

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>
            <div class="d-sm-flex align-items-center justify-content-between">
                <h1 class="h3 mb-0 text-gray-800"></h1>
            </div>
        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">
            <div class="row justify-content-center mt-4">
                <div class="col-lg-4">
                    <div class="card shadow rounded" style="padding: 0.5rem 0;">
                        <div class="card-body py-1 px-4 ">
                            <h5 class="mb-4 text-center fw-bold" style="font-size: 1.7rem; color: black">Add User</h5>

                            <!-- Password match warning -->
                            <div id="password-warning" class="alert alert-danger py-1" style="display:none; font-size: 0.9rem;">
                                Passwords do not match.
                            </div>

                            <form action="{{ route('storeUser') }}" method="POST">
                                @csrf

                                <div class="mb-2">
                                    <label for="firstname">Firstname</label>
                                    <input type="text" id="firstname" name="firstname" class="form-control form-control-sm" required>
                                </div>

                                <div class="mb-2">
                                    <label for="lastname">Lastname</label>
                                    <input type="text" id="lastname" name="lastname" class="form-control form-control-sm" required>
                                </div>

                                <div class="mb-2">
                                    <label for="role">Role</label>
                                    <select id="role" name="role" class="form-control form-control-sm" required>
                                        <option value="" disabled selected>Select Role</option>
                                        <option value="admin">Admin</option>
                                        <option value="receptionist">Receptionist</option>
                                        <option value="cashier">Cashier</option>
                                        <option value="kitchen-staff">Kitchen Staff</option>
                                    </select>
                                </div>

                                <div class="mb-2">
                                    <label for="contact">Contact</label>
                                    <input type="text" id="contact" name="contact" class="form-control form-control-sm" required>
                                </div>

                                <div class="mb-2">
                                    <label for="username">Username</label>
                                    <input type="text" id="username" name="username" class="form-control form-control-sm" required>
                                </div>

                                <div class="mb-2">
                                    <label for="password">Password</label>
                                    <input type="password" id="password" name="password" class="form-control form-control-sm" required>
                                </div>

                                <div class="mb-3">
                                    <label for="confirm_password">Confirm Password</label>
                                    <input type="password" id="confirm_password" name="password_confirmation" class="form-control form-control-sm" required>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <button type="submit" class="btn btn-sm btn-primary" id="submitBtn">Add</button>
                                    <a href="{{ url('users') }}" class="btn btn-sm btn-secondary">Back</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

</div>
<!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Bootstrap core JavaScript-->

<script>
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    const warning = document.getElementById('password-warning');
    const submitBtn = document.getElementById('submitBtn');

    function checkPasswordMatch() {
        if (password.value !== confirmPassword.value) {
            warning.style.display = 'block';
            submitBtn.disabled = true;
        } else {
            warning.style.display = 'none';
            submitBtn.disabled = false;
        }
    }

    password.addEventListener('input', checkPasswordMatch);
    confirmPassword.addEventListener('input', checkPasswordMatch);
</script>

@include('admin.layouts.script')
