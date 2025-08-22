@props([
    'method' => 'gcash', 
    'data' => [] 
])

<div id="tab-{{ $method }}" class="tab-content {{ $method === 'maya' ? 'hidden' : '' }}">

    <label class="block mb-2 text-sm font-medium">
        {{ ucfirst($method) }} Number
    </label>
    <input type="tel" 
        pattern="[0-9]{11}"      
        maxlength="11"
        inputmode="numeric"       
        class="{{ $method }}-number w-full border rounded px-3 py-2 mb-1" 
        placeholder="09XXXXXXXXX"
        value="{{ $data['number'] ?? '' }}" 
        required>
    <span class="error text-red-500 text-sm mb-2 hidden"></span>

    <label class="block mb-2 text-sm font-medium">
        {{ ucfirst($method) }} Registered Name
    </label>
    <input type="text" 
        class="registered-name w-full border rounded px-3 py-2 mb-1" 
        placeholder="Full Name"
        value="{{ $data['registered_name'] ?? '' }}" 
        required>
    <span class="error text-red-500 text-sm mb-2 hidden"></span>

    <label class="block mb-2 text-sm font-medium">Amount</label>
    <input type="number" 
        class="amount w-full border rounded px-3 py-2 mb-1" 
        placeholder="Enter amount"
        value="{{ $data['amount'] ?? '' }}" 
        readonly> 
    <span class="error text-red-500 text-sm mb-2 hidden"></span>

    <label class="block mb-2 text-sm font-medium">Upload Proof of Payment</label>
    @if(!empty($data['proof']))
        <div class="mb-3">
            <a href="{{ asset('storage/' . $data['proof']) }}" target="_blank"
               class="text-blue-500 underline">View Proof</a>
        </div>
    @else
        <input type="file" name="proof" 
            class="proof w-full border rounded px-3 py-2 mb-1"
            required>
        <span class="error text-red-500 text-sm mb-2 hidden"></span>
    @endif

    <label class="block mb-2 text-sm font-medium">Reference Number</label>
    <input type="text" 
        class="ref-no w-full border rounded px-3 py-2 mb-1" 
        placeholder="Enter 13-digit reference number"
        value="{{ $data['ref_no'] ?? '' }}" 
        pattern="[0-9]{13}" 
        maxlength="13" 
        required>
    <span class="error text-red-500 text-sm mb-2 hidden"></span>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('#tab-{{ $method }}');
    const inputs = form.querySelectorAll('input');

    inputs.forEach(input => {
        let error = input.nextElementSibling;
        if (!error || !error.classList.contains('inline-error')) {
            error = document.createElement('span');
            error.classList.add('inline-error', 'text-red-500', 'text-sm', 'hidden');
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
                }
            } else if (input.classList.contains('ref-no')) {
                if (!/^[0-9]{13}$/.test(value)) {
                    error.textContent = 'Reference must be 13 digits';
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

</script>
