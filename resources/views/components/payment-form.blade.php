@props([
    'method' => 'gcash', 
    'readonly' => false,
    'data' => [] 
])

<div id="tab-{{ $method }}" class="tab-content {{ $method === 'maya' ? 'hidden' : '' }}">

    <label class="block mb-2 text-sm font-medium">
        {{ ucfirst($method) }} Number
    </label>
    <input type="number" 
        class="{{ $method }}-number w-full border rounded px-3 py-2 mb-3" 
        placeholder="09XXXXXXXXX"
        value="{{ $data['number'] ?? '' }}"
        @if($readonly) readonly @endif >

    <label class="block mb-2 text-sm font-medium">
        {{ ucfirst($method) }} Registered Name
    </label>
    <input type="text" 
        class="registered-name w-full border rounded px-3 py-2 mb-3" 
        placeholder="Full Name"
        value="{{ $data['registered_name'] ?? '' }}"
        @if($readonly) readonly @endif >

    <label class="block mb-2 text-sm font-medium">Amount</label>
    <input type="number" 
        class="amount w-full border rounded px-3 py-2 mb-3" 
        placeholder="Enter amount"
        value="{{ $data['amount'] ?? '' }}"
        @if($readonly) readonly @endif >

    <label class="block mb-2 text-sm font-medium">Upload Proof of Payment</label>
    @if($readonly && !empty($data['proof']))
        <div class="mb-3">
            <a href="{{ asset('storage/' . $data['proof']) }}" target="_blank"
               class="text-blue-500 underline">View Proof</a>
        </div>
    @else
        <input type="file" name="proof" 
            class="proof w-full border rounded px-3 py-2 mb-3">
    @endif

    <label class="block mb-2 text-sm font-medium">Reference Number</label>
    <input type="text" 
        class="ref-no w-full border rounded px-3 py-2 mb-3" 
        placeholder="Enter reference number"
        value="{{ $data['ref_no'] ?? '' }}"
        @if($readonly) readonly @endif >
</div>
