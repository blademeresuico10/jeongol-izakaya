@props([
    'method' => 'gcash', 
    'data' => [] 
])

@php
    use App\Models\EwalletDetail;
    $ewalletDetail = EwalletDetail::getActivePaymentMethod($method);
@endphp

<div id="tab-{{ $method }}" class="tab-content {{ $method === 'maya' ? 'hidden' : '' }}">
    @if($ewalletDetail)
    <input type="hidden" class="ewallet-id" name="ewallet_id" value="{{ $ewalletDetail->id }}" data-method="{{ $method }}">
    
    <div class="mb-2">
        <label class="block text-sm font-medium">
            {{ ucfirst($method) }} Wallet:
        </label>
        <p class="ml-20 text-sm">{{ $ewalletDetail->wallet_name }}</p>
        <div class="ml-20 flex items-center space-x-2">
            <p class="text-sm text-blue-600">{{ $ewalletDetail->wallet_number }}</p>
            <button 
                type="button"
                onclick="copyToClipboard('{{ $ewalletDetail->wallet_number }}')" 
                class="text-gray-400 hover:text-gray-600 cursor-pointer transition-colors duration-200 p-1"
                title="Copy to clipboard"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="14" height="14" x="8" y="8" rx="2" ry="2"/>
                    <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>
                </svg>
            </button>
        </div>
    </div>
    @else
    <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
        <p class="text-sm text-yellow-800">No {{ ucfirst($method) }} account configured. Please contact admin.</p>
    </div>
    @endif
    
    <label class="block text-sm font-medium">
       Your {{ ucfirst($method) }} Account Number
    </label>
    <input type="tel" 
        name="registered_number"
        pattern="[0-9]{11}"      
        maxlength="11"
        inputmode="numeric"       
        class="{{ $method }}-number w-full border rounded px-3 py-2 mb-1" 
        placeholder="09XXXXXXXXX"
        value="{{ $data['registered_number'] ?? '' }}" 
        required>
    <span class="error text-red-500 text-sm mb-2 hidden"></span>

    <label class="block text-sm font-medium">
        {{ ucfirst($method) }} Registered Name
    </label>
    <input type="text" 
        name="registered_name"
        class="registered-name w-full border rounded px-3 py-2 mb-1" 
        placeholder="Full Name"
        value="{{ $data['registered_name'] ?? '' }}" 
        required>
    <span class="error text-red-500 text-sm hidden"></span>

    <label class="block text-sm font-medium">Amount</label>
    <input type="number" 
        name="amount"
        class="amount w-full border rounded px-3 py-2 mb-1" 
        placeholder="Enter amount"
        value="{{ $data['amount'] ?? '' }}" 
        readonly> 
    <span class="error text-red-500 text-sm hidden"></span>

    <label class="block text-sm font-medium">Upload Proof of Payment</label>
    @if(!empty($data['proof']))
    <div class="mb-3">
        <a href="{{ asset('storage/' . $data['proof']) }}" target="_blank"
           class="text-blue-500 underline">View Proof</a>
    </div>
    @else
    <input type="file" name="proof" class="proof w-full border rounded px-3 py-2" accept="image/*" required>
    @endif
    
    <div id="toast-{{ $method }}" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-black bg-opacity-75 text-white px-6 py-3 rounded-md shadow-lg opacity-0 transition-opacity duration-300 pointer-events-none z-50">
        Phone number copied!
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('#tab-{{ $method }}');
    if (!form) return;
    
    const inputs = form.querySelectorAll('input');

    inputs.forEach(input => {
        let error = input.nextElementSibling;
        if (!error || !error.classList.contains('error')) {
            error = document.createElement('span');
            error.classList.add('error', 'text-red-500', 'text-sm', 'hidden');
            input.insertAdjacentElement('afterend', error);
        }

        input.addEventListener('input', () => {
            const value = input.value.trim();

            if (value === '') {
                error.textContent = '';
                error.classList.add('hidden');
                return;
            }

            let showError = false;
            if (input.type === 'tel') {
                const regex = /^[0-9]+$/;
                if (!regex.test(value)) {
                    error.textContent = 'Must be a valid number';
                    showError = true;
                } else if (value.length !== 11) {
                    error.textContent = 'Must be 11 digits';
                    showError = true;
                }
            } else if (!input.checkValidity()) {
                error.textContent = 'Invalid input';
                showError = true;
            }

            if (showError) {
                error.classList.remove('hidden');
            } else {
                error.textContent = '';
                error.classList.add('hidden');
            }
        });
    });
});

function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(function() {
            showToast('{{ $method }}');
        }).catch(function(err) {
            console.error('Failed to copy: ', err);
            fallbackCopyTextToClipboard(text, '{{ $method }}');
        });
    } else {
        fallbackCopyTextToClipboard(text, '{{ $method }}');
    }
}

function fallbackCopyTextToClipboard(text, method) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.cssText = "position: fixed; top: 0; left: 0;";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        const successful = document.execCommand('copy');
        if (successful) showToast(method);
    } catch (err) {
        console.error('Fallback copy failed', err);
    }

    document.body.removeChild(textArea);
}

function showToast(method) {
    const toast = document.getElementById('toast-' + method);
    if (!toast) return;
    
    toast.classList.remove('opacity-0');
    toast.classList.add('opacity-100');
    
    setTimeout(() => {
        toast.classList.remove('opacity-100');
        toast.classList.add('opacity-0');
    }, 2000);
}
</script>