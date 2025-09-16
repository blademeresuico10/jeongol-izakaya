@include('admin.layouts.header')
@include('admin.layouts.sidebar')

<div class="flex-1 overflow-hidden">
    <div class="bg-white mb-4">
        <nav class="bg-white shadow-sm border-b px-6 py-4">
            <h1 class="text-2xl font-semibold text-gray-800">My Profile</h1>
        </nav>
    </div>

    <div class="container-fluid">
        <div class="p-6">
            <div class="max-w-3xl mx-auto">

                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <div class="flex items-center space-x-6">
                        @if($user->profile_picture)
                            <img id="profileImage" src="{{ asset('storage/' . $user->profile_picture) }}"
                                alt="Profile Picture" class="w-20 h-20 rounded-full object-cover">
                        @else
                            <div class="w-20 h-20 rounded-full bg-gray-400 flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        @endif
                        <div>
                            <h4 id="profileName" class="text-xl font-bold text-gray-900 ml-3">{{ $user->firstname }}
                                {{ $user->lastname }}
                            </h4>
                            <p id="profileRole" class="text-gray-600 capitalize ml-3">{{ $user->role }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
                        <h5 class="text-lg font-medium text-gray-800">Personal Information</h5>
                        <div class="flex space-x-2">
                            <button type="button"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded text-sm"
                                onclick="openPasswordModal()">Change Password</button>
                            <button type="button"
                                class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded text-sm"
                                onclick="openModal()">Edit</button>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-l font-bold text-black">Name</p>
                                <p id="displayName" class="text-base text-gray-900 mt-1">{{ $user->firstname }}
                                    {{ $user->lastname }}
                                </p>
                            </div>
                            <div>
                                <p class="text-l font-bold text-black">Username</p>
                                <p id="displayUsername" class="text-base text-gray-900 mt-1">{{ $user->username }}</p>
                            </div>
                            <div>
                                <p class="text-l font-bold text-black">Email</p>
                                <p id="displayEmail" class="text-base text-gray-900 mt-1">
                                    {{ $user->email ?: 'Not provided' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-l font-bold text-black">Contact</p>
                                <p id="displayContact" class="text-base text-gray-900 mt-1">{{ $user->contact_number }}
                                </p>
                            </div>
                            <div>
                                <p class="text-l font-bold text-black">Role</p>
                                <p id="displayRole" class="text-base text-gray-900 mt-1 capitalize">{{ $user->role }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="editModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-200 opacity-75"></div>

                <div class="bg-white rounded-lg p-6 max-w-md w-full relative z-10">
                    <form id="profileForm" action="{{ route('admin.updateprofile', $user->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="flex justify-between items-center mb-6">
                            <h5 class="text-lg font-semibold">Edit Profile</h5>
                            <button type="button" onclick="closeModal()"
                                class="text-gray-500 hover:text-gray-700 text-xl">&times;</button>
                        </div>

                        <div class="text-center mb-6">
                            @if($user->profile_picture)
                                <img id="currentImage" src="{{ asset('storage/' . $user->profile_picture) }}"
                                    alt="Profile Picture" class="w-20 h-20 rounded-full object-cover mx-auto">
                            @else
                                <div id="currentImage"
                                    class="w-20 h-20 rounded-full bg-gray-400 flex items-center justify-center mx-auto">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Change Profile Picture</label>
                            <input type="file" id="profilePic" name="profile_picture"
                                class="w-full px-3 py-2 border rounded text-sm" accept="image/*">
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                <input type="text" id="firstName" name="firstname" value="{{ $user->firstname }}"
                                    class="w-full px-3 py-2 border rounded text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                <input type="text" id="lastName" name="lastname" value="{{ $user->lastname }}"
                                    class="w-full px-3 py-2 border rounded text-sm">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                            <input type="text" id="username" name="username" value="{{ $user->username }}"
                                class="w-full px-3 py-2 border rounded text-sm">
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                            <input type="text" id="contactNumber" name="contact_number"
                                value="{{ $user->contact_number }}" class="w-full px-3 py-2 border rounded text-sm">
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="email" name="email" value="{{ $user->email }}"
                                class="w-full px-3 py-2 border rounded text-sm">
                        </div>

                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            <button type="button" onclick="closeModal()"
                                class="px-4 py-2 border rounded text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                            <button type="submit" id="saveBtn"
                                class="px-4 py-2 bg-blue-500 text-white rounded text-sm hover:bg-blue-600">Update
                                Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="changePasswordModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-200 opacity-75"></div>

                <div class="bg-white rounded-lg p-6 max-w-md w-full relative z-10">
                    <form id="changePasswordForm" action="{{ route('admin.changepassword', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="flex justify-between items-center mb-6">
                            <h5 class="text-lg font-semibold">Change Password</h5>
                            <button type="button" onclick="closePasswordModal()"
                                class="text-gray-500 hover:text-gray-700 text-xl">&times;</button>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                            <div class="relative">
                                <input type="password" id="currentPassword" name="current_password"
                                    class="w-full px-3 py-2 border rounded text-sm pr-10" required>
                                <button type="button"
                                    onclick="togglePasswordVisibility('currentPassword', 'currentPasswordEye')"
                                    class="absolute inset-y-0 right-0 px-3 flex items-center">
                                    <svg id="currentPasswordEye" class="w-4 h-4 text-gray-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                            <div class="relative">
                                <input type="password" id="newPassword" name="new_password"
                                    class="w-full px-3 py-2 border rounded text-sm pr-10" required minlength="6">
                                <button type="button"
                                    onclick="togglePasswordVisibility('newPassword', 'newPasswordEye')"
                                    class="absolute inset-y-0 right-0 px-3 flex items-center">
                                    <svg id="newPasswordEye" class="w-4 h-4 text-gray-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Password must be at least 6 characters long</p>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                            <div class="relative">
                                <input type="password" id="confirmPassword" name="new_password_confirmation"
                                    class="w-full px-3 py-2 border rounded text-sm pr-10" required>
                                <button type="button"
                                    onclick="togglePasswordVisibility('confirmPassword', 'confirmPasswordEye')"
                                    class="absolute inset-y-0 right-0 px-3 flex items-center">
                                    <svg id="confirmPasswordEye" class="w-4 h-4 text-gray-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            <button type="button" onclick="closePasswordModal()"
                                class="px-4 py-2 border rounded text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                            <button type="submit" id="changePasswordBtn"
                                class="px-4 py-2 bg-blue-500 text-white rounded text-sm hover:bg-blue-600">Change
                                Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>

        function initSessionMessages() {
            @if(session('success'))
                showToast('success', {!! json_encode(session('success')) !!});
            @endif

            @if(session('error'))
                showToast('error', {!! json_encode(session('error')) !!});
            @endif

                @if($errors->any())
                    let errorMessages = [];
                    @foreach($errors->all() as $error)
                        errorMessages.push({!! json_encode($error) !!});
                    @endforeach
                    showToast('warning', 'Validation Errors', errorMessages.join('<br>'));
                @endif
}

        function showToast(icon, title, text = null) {
            const config = {
                success: { bg: '#d4edda', color: '#155724', timer: 3000 },
                error: { bg: '#f8d7da', color: '#721c24', timer: 4000 },
                warning: { bg: '#fff3cd', color: '#856404', timer: 5000 }
            };

            Swal.fire({
                icon: icon,
                title: title,
                html: text || title,
                toast: true,
                position: 'top',
                timer: config[icon].timer,
                showConfirmButton: false,
                background: config[icon].bg,
                color: config[icon].color
            });
        }

        function openModal() {
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        document.getElementById('profilePic').addEventListener('change', function (e) {
            const file = e.target.files[0];
            const currentImage = document.getElementById('currentImage');
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (e) {
                if (currentImage.tagName === 'IMG') {
                    currentImage.src = e.target.result;
                } else {
                    const img = document.createElement('img');
                    img.id = 'currentImage';
                    img.src = e.target.result;
                    img.className = 'w-20 h-20 rounded-full object-cover mx-auto';
                    img.alt = 'Profile Picture';
                    currentImage.parentNode.replaceChild(img, currentImage);
                }
            };
            reader.readAsDataURL(file);
        });

        document.getElementById('profileForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const saveBtn = document.getElementById('saveBtn');
            const formData = new FormData(this);

            saveBtn.textContent = 'Updating...';
            saveBtn.disabled = true;

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(res => res.ok ? res.json() : res.json().then(data => Promise.reject(data)))
                .then(data => {
                    if (data.success) {
                        updateProfileDisplay();
                        closeModal();
                        showToast('success', 'Profile updated successfully!');
                    } else {
                        showToast('error', data.message || 'Failed to update profile');
                    }
                })
                .catch(err => {
                    if (err.errors) {
                        let errorMessages = Object.values(err.errors).flat();
                        showToast('warning', 'Validation Errors', errorMessages.join('<br>'));
                    } else {
                        showToast('error', err.message || 'An error occurred while updating profile');
                    }
                })
                .finally(() => {
                    saveBtn.textContent = 'Update Profile';
                    saveBtn.disabled = false;
                });
        });

        function updateProfileDisplay() {
            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;
            const fullName = `${firstName} ${lastName}`;

            document.getElementById('displayName').textContent = fullName;
            document.getElementById('profileName').textContent = fullName;
            document.getElementById('displayUsername').textContent = document.getElementById('username').value;
            document.getElementById('displayEmail').textContent = document.getElementById('email').value || 'Not provided';
            document.getElementById('displayContact').textContent = document.getElementById('contactNumber').value;

            if (document.getElementById('profilePic').files[0] && document.getElementById('profileImage')) {
                document.getElementById('profileImage').src = document.getElementById('currentImage').src;
            }
        }


        function openPasswordModal() {
            document.getElementById('changePasswordModal').classList.remove('hidden');
            setTimeout(initializePasswordValidation, 100);
        }

        function closePasswordModal() {
            document.getElementById('changePasswordModal').classList.add('hidden');
            document.getElementById('changePasswordForm').reset();

            const strengthContainer = document.getElementById('strengthContainer');
            if (strengthContainer) strengthContainer.remove();

            ['currentPassword', 'newPassword', 'confirmPassword'].forEach(id => {
                const input = document.getElementById(id);
                if (input) {
                    input.classList.remove('border-red-500', 'border-orange-500', 'border-yellow-500', 'border-green-500');
                    input.classList.add('border-gray-300');
                }
            });
        }

        function togglePasswordVisibility(inputId, eyeId) {
            const input = document.getElementById(inputId);
            const eye = document.getElementById(eyeId);
            const isPassword = input.type === 'password';

            input.type = isPassword ? 'text' : 'password';
            eye.innerHTML = isPassword
                ? `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>`
                : `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>`;
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
            if (strength >= 80) { level = 'strong'; color = 'green'; }
            else if (strength >= 60) { level = 'good'; color = 'yellow'; }
            else if (strength >= 40) { level = 'fair'; color = 'orange'; }
            else { level = 'weak'; color = 'red'; }

            return { strength, level, color, checks };
        }

        function initializePasswordValidation() {
            const newPasswordInput = document.getElementById('newPassword');
            const confirmPasswordInput = document.getElementById('confirmPassword');

            const strengthContainer = document.createElement('div');
            strengthContainer.id = 'strengthContainer';
            strengthContainer.className = 'mt-2';
            strengthContainer.innerHTML = `
        <div class="flex items-center space-x-2 mb-2">
            <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div id="strengthProgress" class="h-full transition-all duration-300 ease-in-out bg-red-500" style="width: 0%"></div>
            </div>
            <span id="strengthText" class="text-xs font-medium text-gray-500">Weak</span>
        </div>
        <div id="strengthChecks" class="text-xs text-gray-600 space-y-1"></div>
    `;

            newPasswordInput.parentNode.insertBefore(strengthContainer, newPasswordInput.nextSibling);

            newPasswordInput.addEventListener('input', function () {
                const password = this.value;

                if (password.length === 0) {
                    this.className = this.className.replace(/border-\w+-500/g, '') + ' border-gray-300';
                    strengthContainer.style.display = 'none';
                    return;
                }

                const result = calculatePasswordStrength(password);

                this.className = this.className.replace(/border-\w+-500/g, '') + ` border-${result.color}-500`;
                strengthContainer.style.display = 'block';

                const progress = document.getElementById('strengthProgress');
                const text = document.getElementById('strengthText');
                progress.style.width = `${result.strength}%`;
                progress.className = `h-full transition-all duration-300 ease-in-out bg-${result.color}-500`;
                text.textContent = result.level.charAt(0).toUpperCase() + result.level.slice(1);
                text.className = `text-xs font-medium text-${result.color}-600`;

                document.getElementById('strengthChecks').innerHTML = [
                    ['length', 'At least 8 characters'],
                    ['uppercase', 'Uppercase letter'],
                    ['lowercase', 'Lowercase letter'],
                    ['numbers', 'Number'],
                    ['special', 'Special character']
                ].map(([key, label]) => `
            <div class="flex items-center space-x-1">
                <span class="${result.checks[key] ? 'text-green-600' : 'text-red-500'}">
                    ${result.checks[key] ? '✓' : '✗'}
                </span>
                <span>${label}</span>
            </div>
        `).join('');

                if (confirmPasswordInput.value) validatePasswordMatch();
            });

            function validatePasswordMatch() {
                const newPassword = newPasswordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                confirmPasswordInput.className = confirmPasswordInput.className.replace(/border-\w+-500/g, '');

                if (!confirmPassword) {
                    confirmPasswordInput.classList.add('border-gray-300');
                    confirmPasswordInput.setCustomValidity('');
                } else if (newPassword === confirmPassword) {
                    confirmPasswordInput.classList.add('border-green-500');
                    confirmPasswordInput.setCustomValidity('');
                } else {
                    confirmPasswordInput.classList.add('border-red-500');
                    confirmPasswordInput.setCustomValidity('Passwords do not match');
                }
            }

            confirmPasswordInput.addEventListener('input', validatePasswordMatch);
        }

        document.getElementById('changePasswordForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = document.getElementById('changePasswordBtn');
            const formData = new FormData(this);

            btn.textContent = 'Changing...';
            btn.disabled = true;

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(res => res.ok ? res.json() : res.json().then(data => Promise.reject(data)))
                .then(data => {
                    if (data.success) {
                        closePasswordModal();
                        showToast('success', 'Password changed successfully!');
                    } else {
                        showToast('error', data.message || 'Failed to change password');
                    }
                })
                .catch(err => {
                    if (err.errors) {
                        let errorMessages = Object.values(err.errors).flat();
                        showToast('warning', 'Validation Errors', errorMessages.join('<br>'));
                    } else {
                        showToast('error', err.message || 'An error occurred while changing password');
                    }
                })
                .finally(() => {
                    btn.textContent = 'Change Password';
                    btn.disabled = false;
                });
        });

        initSessionMessages();
    </script>
</div>