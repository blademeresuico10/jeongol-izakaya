<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Cashier</title>
    @vite('resources/css/app.css')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        .menu-image-container {
            width: 100%;
            height: 70px;
            overflow: hidden;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        .menu-cards-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            width: 100px;
            justify-content: center;
            max-width: 100%;
            overflow-x: hidden;
        }

        #toast {
            transition: opacity 0.3s ease-in-out;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            #print_invoice,
            #print_invoice * {
                visibility: visible;
            }

            #print_invoice {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }

        .table-link {
            transition: transform 0.2s ease-in-out;
        }

        .table-link:hover {
            transform: scale(1.02);
        }

        .table-link:active {
            transform: scale(0.98);
        }

        .modal-enter {
            animation: modalEnter 0.3s ease-out;
        }

        .modal-exit {
            animation: modalExit 0.3s ease-in;
        }

        @keyframes modalEnter {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes modalExit {
            from {
                opacity: 1;
                transform: scale(1);
            }

            to {
                opacity: 0;
                transform: scale(0.9);
            }
        }

        .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }

        .overflow-y-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .btn-loading {
            position: relative;
            pointer-events: none;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin: -8px 0 0 -8px;
            border: 2px solid transparent;
            border-top-color: currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .table-available {
            animation: pulse-green 2s infinite;
        }

        .table-occupied {
            animation: pulse-red 2s infinite;
        }

        @keyframes pulse-green {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
            }

            50% {
                box-shadow: 0 0 0 10px rgba(34, 197, 94, 0);
            }
        }

        @keyframes pulse-red {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7);
            }

            50% {
                box-shadow: 0 0 0 10px rgba(220, 38, 38, 0);
            }
        }

        @media (max-width: 768px) {
            .table-layout {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }

            .relative.h-32.w-48 {
                height: 8rem;
                width: 10rem;
            }
        }

        @media (max-width: 480px) {
            .table-layout {
                grid-template-columns: 1fr;
            }

            .px-7 {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus {
            outline: none;
            ring: 2px;
            ring-color: #3b82f6;
            border-color: #3b82f6;
        }
    </style>
</head>

<body x-data="{ openDropdown: false }" class="relative">

    <!-- Dropdown -->
    <div x-show="openDropdown" @click.away="openDropdown = false"
        class="fixed top-16 right-14 w-48 bg-white border rounded shadow-lg z-[9999]">
        <a href="#notifications" class="block px-4 py-2 hover:bg-gray-100">Notifications</a>
        <a href="#transactions" class="block px-4 py-2 hover:bg-gray-100">Transactions</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">Logout</button>
        </form>
    </div>

    <!-- Header -->
    <header class="mt-2">
        <div class="border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-7 relative">
            <div class="flex items-center ml-5">
                <img src="{{ asset('logo/jeongol_logo.jpg') }}" alt="Jeongol Logo" class="h-13 w-20" />
            </div>
            <div>
                <button @click="openDropdown = !openDropdown"
                    class="inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300">
                    <svg class="w-8 h-8 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path
                            d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 13a8.949 8.949 0 0 1-4.951-1.488A3.987 3.987 0 0 1 9 13h2a3.987 3.987 0 0 1 3.951 3.512A8.949 8.949 0 0 1 10 18Z" />
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Tables -->
    <div id="dineInContent" class="flex justify-center m-5">
        <div class="table-layout grid lg:grid-cols-5 gap-10 justify-center">
            @foreach($tables as $table)
                @php
                    $isOccupied = in_array($table->table_number, $occupiedTables);
                    $reservationId = $table->current_reservation_id ?? '';
                @endphp
                <div class="table-link cursor-pointer" data-reservation-id="{{ $reservationId }}"
                    data-table-number="{{ $table->table_number }}" data-table-capacity="{{ $table->capacity }}"
                    data-occupied="{{ $isOccupied ? '1' : '0' }}">
                    <div class="flex justify-center mt-5">
                        <div class="relative h-32 w-48 bg-white rounded-3xl shadow-md flex items-center justify-center p-4">
                            <div class="absolute mt-2 -top-1 px-3 bg-gray-200 text-black text-xs rounded-full shadow">
                                {{ $table->capacity }} Pax
                            </div>
                            <div
                                class="absolute top-0 left-1/4 -translate-x-1/2 -translate-y-full w-14 h-2 bg-gray-200 rounded-full">
                            </div>
                            <div
                                class="absolute top-0 left-3/4 -translate-x-1/2 -translate-y-full w-14 h-2 bg-gray-200 rounded-full">
                            </div>
                            <div
                                class="absolute bottom-0 left-1/4 -translate-x-1/2 translate-y-full w-14 h-2 bg-gray-200 rounded-full">
                            </div>
                            <div
                                class="absolute bottom-0 left-3/4 -translate-x-1/2 translate-y-full w-14 h-2 bg-gray-200 rounded-full">
                            </div>

                            <div class="flex flex-col items-center mt-4">
                                <div
                                    class="w-16 h-16 rounded-full {{ $isOccupied ? 'bg-red-600' : 'bg-green-600' }} text-white flex items-center justify-center shadow">
                                    <span class="text-lg font-semibold">T-{{ $table->table_number }}</span>
                                </div>
                                @if($table->current_reservation_id && $table->remaining_seconds > 0)
                                    <span class="text-red-600 font-medium mt-2 flex items-center space-x-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6v6l4 2m0-10a9 9 0 1 0 9 9 9 9 0 0 0-9-9z" />
                                        </svg>
                                        <span class="countdown" data-seconds="{{ $table->remaining_seconds }}">--:--:--</span>
                                    </span>
                                @else
                                    <span class="text-green-600 font-medium mt-2">Available</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div id="invoice-modal" aria-hidden="true"
        class="hidden fixed top-0 left-0 right-0 z-50 flex justify-center items-center w-full h-screen bg-black bg-opacity-50">
        <div class="relative p-4 w-full max-w-lg">
            <div class="bg-white rounded-lg shadow">
                <div class="flex justify-between items-center p-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Invoice</h3>
                    <button type="button"
                        class="text-gray-500 hover:bg-gray-200 rounded-lg w-8 h-8 flex justify-center items-center"
                        onclick="closeInvoiceModal()">✕</button>

                </div>

                <div class="p-4 space-y-4">
                    <div>
                        <p><strong>Date: </strong><span id="invoice_date"></span></p>
                        <p><strong>Customer: </strong><span id="customer_name"></span></p>
                    </div>

                    <div>
                        <div class="flex justify-between font-semibold border-b pb-2">
                            <span class="flex-1">Item</span>
                            <span class="w-16 text-center">Qty</span>
                            <span class="w-20 text-right">Price</span>
                            <span class="w-24 text-right">Subtotal</span>
                        </div>
                        <div id="invoiceItemsList" class="space-y-1"></div>
                    </div>

                    <div class="flex justify-end font-bold text-lg border-t pt-2">
                        <span>Total: </span>
                        <input type="text" id="total_price" name="total_price"
                            class="bg-gray-200 border border-gray-300 text-sm rounded-lg w-32 p-2.5 ml-4 text-right"
                            readonly />
                    </div>
                    <div class="flex justify-center space-x-2">
                        <button onclick="openPrintInvoice()"
                            class="w-60 py-2.5 text-black font-bold bg-gray-200 hover:bg-gray-300 rounded-lg">
                            Print Invoice
                        </button>
                        <button onclick="openPaymentModal()"
                            class="w-60 py-2.5 text-white font-bold bg-blue-600 hover:bg-blue-700 rounded-lg">
                            Payment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="print_invoice" aria-hidden="true"
        class="hidden fixed top-0 left-0 right-0 z-50 flex justify-center items-center w-full h-screen bg-black bg-opacity-50">
        <div class="relative p-4 w-full max-w-lg">
            <div class="bg-white rounded-lg shadow">
                <div class="flex justify-between items-center p-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Invoice</h3>
                </div>

                <div class="p-4 space-y-4">
                    <div>
                        <p><strong>Date: </strong><span id="print_invoice_date"></span></p>
                        <p><strong>Customer: </strong><span id="print_customer_name"></span></p>
                    </div>

                    <div>
                        <div class="flex justify-between font-semibold border-b pb-2">
                            <span class="flex-1">Item</span>
                            <span class="w-16 text-center">Qty</span>
                            <span class="w-20 text-right">Price</span>
                            <span class="w-24 text-right">Subtotal</span>
                        </div>
                        <div id="print_invoiceItemsList" class="space-y-1"></div>
                    </div>

                    <div class="flex justify-end font-bold text-lg border-t pt-2">
                        <span>Total: </span>
                        <span id="print_total_price" class="ml-4 w-32 text-right"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="payment-modal" tabindex="-1" aria-hidden="true"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto h-full bg-black bg-opacity-50 flex justify-center items-center">
        <div class="relative w-full max-w-3xl">
            <div class="relative bg-white text-black rounded-lg shadow-lg h-[85vh] overflow-y-auto flex flex-col">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 rounded-t">
                    <h3 class="text-2xl font-semibold">Payment</h3>
                    <button type="button" onclick="closePaymentModal()"
                        class="text-gray-500 hover:bg-gray-200 hover:text-black rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M1 1l6 6m0 0l6 6M7 7l6-6M7 7L1 13" />
                        </svg>
                    </button>
                </div>

                <div class="flex-1 px-6 py-5 space-y-6">
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <h4 class="text-lg font-semibold mb-3">Customer Information</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Customer Name</label>
                                <input type="text" id="payment_customer_name"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Table Pax</label>
                                <input type="number" id="payment_pax"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="border rounded-lg p-4">
                        <h4 class="text-lg font-semibold mb-3">Order Details</h4>
                        <div class="flex justify-between font-semibold border-b pb-2 text-sm">
                            <span class="flex-1">Item</span>
                            <span class="w-16 text-center">Qty</span>
                            <span class="w-20 text-right">Price</span>
                            <span class="w-24 text-right">Subtotal</span>
                            <span class="w-24 text-right">Mga Sabad</span>
                        </div>
                        <div id="paymentItemsList" class="space-y-1 text-sm mt-2"></div>
                    </div>

                    <div class="border rounded-lg p-4 bg-gray-50">
                        <h4 class="text-lg font-semibold mb-3">Payment Summary</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between text-lg font-bold border-t pt-2">
                                <span>Total Amount:</span>
                                <span id="payment_total" class="font-mono text-blue-600">₱0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end gap-4 px-6 py-4 border-t border-gray-200">
                    <button onclick="closePaymentModal()" type="button"
                        class="px-6 py-2.5 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg font-semibold text-sm">
                        Cancel
                    </button>
                    <button onclick="submitPayment()" type="button"
                        class="px-8 py-2.5 text-white bg-blue-600 hover:bg-blue-700 rounded-lg font-semibold text-sm">
                        Process Payment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div id="toast"
        class="fixed top-5 right-5 z-50 hidden opacity-0 px-4 py-3 rounded-lg shadow-md bg-red-600 text-white text-sm font-medium transition-opacity duration-300 ease-in-out">
        <span id="toast-message">Something went wrong</span>
    </div>

</body>

<script>
    let currentReservationData = null;

    document.addEventListener("DOMContentLoaded", function () {
        const dineInContent = document.getElementById("dineInContent");
        const tabLinks = document.querySelectorAll(".tab-link");
        const invoiceModal = document.getElementById("invoice-modal");
        const invoiceDateSpan = document.getElementById("invoice_date");
        const customerNameSpan = document.getElementById("customer_name");
        const invoiceItemsList = document.getElementById("invoiceItemsList");
        const totalPriceInput = document.getElementById("total_price");
        const printInvoiceDateSpan = document.getElementById("print_invoice_date");
        const countdownElements = document.querySelectorAll(".countdown");

        const now = new Date();
        const formattedDate = now.toLocaleDateString('en-CA');
        invoiceDateSpan.textContent = formattedDate;
        printInvoiceDateSpan.textContent = formattedDate;

        // Table click handling
        dineInContent.addEventListener("click", function (event) {
            const table = event.target.closest(".table-link");
            if (!table) return;

            const reservationId = table.getAttribute("data-reservation-id");
            const isOccupied = table.getAttribute("data-occupied") === "1";

            if (isOccupied && reservationId) {
                invoiceItemsList.innerHTML = '';
                totalPriceInput.value = formatCurrency(0);

                table.style.pointerEvents = "none";
                setTimeout(() => { table.style.pointerEvents = "auto"; }, 1000);

                fetch(`/orders/${reservationId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (!data) return;

                        currentReservationData = data;
                        customerNameSpan.textContent = data.customer_name || "N/A";
                        let total = 0;

                        data.orders.forEach(order => {
                            const price = parseFloat(order.price);
                            const qty = parseInt(order.quantity);
                            const subtotal = price * qty;
                            total += subtotal;

                            const orderLine = document.createElement("div");
                            orderLine.classList.add("flex", "justify-between", "items-center", "py-1", "border-b", "border-gray-100");
                            orderLine.innerHTML = `
<span class="flex-1">${order.order_name}</span>
<span class="w-16 text-center">${qty}</span>
<span class="w-20 text-right">${formatCurrency(price)}</span>
<span class="w-24 text-right">${formatCurrency(subtotal)}</span>
`;
                            invoiceItemsList.appendChild(orderLine);
                        });

                        totalPriceInput.value = formatCurrency(total);
                        invoiceModal.classList.remove("hidden");
                        invoiceModal.classList.add("flex");
                    })
                    .catch(() => { showToast("Failed to load invoice data."); });
            } else {
                showToast("Table is not occupied.");
            }
        });

        countdownElements.forEach(el => {
            let seconds = parseInt(el.dataset.seconds);

            function formatTime(s) {
                const h = String(Math.floor(s / 3600)).padStart(2, '0');
                const m = String(Math.floor((s % 3600) / 60)).padStart(2, '0');
                const sec = String(s % 60).padStart(2, '0');
                return `${h}:${m}:${sec} `;
            }

            function update() {
                if (seconds <= 0) { el.textContent = "00:00:00"; return; }
                el.textContent = formatTime(seconds--);
                setTimeout(update, 1000);
            }

            update();
        });
    });

    function formatCurrency(amount) { return `₱${amount.toFixed(2)} `; }

    function showToast(message, type = "error", duration = 3000) {
        const toast = document.getElementById("toast");
        const toastMessage = document.getElementById("toast-message");

        if (!toast || !toastMessage) {
            console.error("Toast element not found in DOM.");
            return;
        }

        toast.className = "fixed top-5 right-5 z-50 px-4 py-3 rounded-lg shadow-md text-white text-sm font-medium transition-opacity duration-300 ease-in-out hidden opacity-0";

        toast.classList.add(
            type === "success" ? "bg-green-600" :
                type === "info" ? "bg-blue-600" : "bg-red-600"
        );

        toastMessage.textContent = message;

        toast.classList.remove("hidden", "opacity-0");
        toast.classList.add("opacity-100");

        setTimeout(() => {
            toast.classList.remove("opacity-100");
            toast.classList.add("opacity-0");
            setTimeout(() => { toast.classList.add("hidden"); }, 300);
        }, duration);
    }


    function openPrintInvoice() {
        document.getElementById("print_invoice_date").textContent = document.getElementById("invoice_date").textContent;
        document.getElementById("print_customer_name").textContent = document.getElementById("customer_name").textContent;
        const invoiceItemsList = document.getElementById("invoiceItemsList").cloneNode(true);
        const printItemsContainer = document.getElementById("print_invoiceItemsList");
        printItemsContainer.innerHTML = "";
        printItemsContainer.appendChild(invoiceItemsList);
        document.getElementById("print_total_price").textContent = document.getElementById("total_price").value;
        const printArea = document.getElementById("print_invoice");
        printArea.classList.remove("hidden");
        printArea.classList.add("flex");
        setTimeout(() => { window.print(); printArea.classList.add("hidden"); printArea.classList.remove("flex"); }, 500);
    }

    function openPaymentModal() {
        if (!currentReservationData) { showToast("No reservation data available"); return; }
        const modal = document.getElementById("payment-modal");
        document.getElementById("payment_customer_name").value = currentReservationData.customer_name || "N/A";
        document.getElementById("payment_pax").value = currentReservationData.pax || 0;
        displayPaymentItems();
        modal.classList.remove("hidden");
        modal.classList.add("flex");
    }

    function closePaymentModal() {
        document.getElementById('payment-modal').classList.add('hidden');
        document.getElementById('payment-modal').classList.remove('flex');
    }

    function displayPaymentItems() {
        if (!currentReservationData) return;

        const paymentItemsList = document.getElementById("paymentItemsList");
        let total = 0;
        paymentItemsList.innerHTML = '';

        currentReservationData.orders.forEach(order => {
            const price = parseFloat(order.price);
            const qty = parseInt(order.quantity);
            const itemTotal = price * qty;
            total += itemTotal;

            const orderLine = document.createElement("div");
            orderLine.classList.add("flex", "justify-between", "items-center", "py-1", "border-b", "border-gray-100");
            orderLine.innerHTML = `
            <span class="flex-1">${order.order_name}</span>
            <span class="w-16 text-center">${qty}</span>
            <span class="w-20 text-right">${formatCurrency(price)}</span>
            <span class="w-24 text-right font-medium">${formatCurrency(itemTotal)}</span>
            <span class="w-24 text-right">
                <input type="number" 
                       class="discount-input border border-gray-300 rounded px-1 py-1 w-16 text-sm" 
                       min="0" 
                       max="${qty}" 
                       value="0" 
                       data-price="${price}" 
                       data-qty="${qty}" 
                       data-item-total="${itemTotal}"
                       data-item-name="${order.order_name}" />
            </span>`;
            paymentItemsList.appendChild(orderLine);
        });

        const discountInputs = document.querySelectorAll('.discount-input');
        discountInputs.forEach(input => {
            input.addEventListener('input', calculateDiscountedTotal);
            input.addEventListener('change', calculateDiscountedTotal);
        });

        document.getElementById("payment_total").textContent = formatCurrency(total);

        const summarySection = document.querySelector('#payment-modal .border.rounded-lg.p-4.bg-gray-50 .space-y-2');
        if (!summarySection) return;

        const existingDiscountSection = document.getElementById('discount_breakdown_section');
        if (existingDiscountSection) {
            existingDiscountSection.remove();
        }

        const discountSection = document.createElement('div');
        discountSection.id = 'discount_breakdown_section';
        discountSection.className = 'border-t pt-3 mt-2';
        discountSection.style.display = 'none';
        discountSection.innerHTML = `
        <h5 class="font-semibold text-gray-700 mb-2">DISCOUNTED PERSON TO PAY:</h5>
        <div id="discount_items_breakdown" class="space-y-1 mb-3"></div>
        <div class="flex justify-between text-lg font-bold text-green-600 border-b pb-2">
            <span>Mga Sabad:</span>
            <span id="discount_person_total" class="font-mono">₱0.00</span>
        </div>
    `;

        const totalRow = summarySection.querySelector('.flex.justify-between.text-lg.font-bold.border-t.pt-2');
        if (totalRow) {
            summarySection.insertBefore(discountSection, totalRow);

            totalRow.innerHTML = `
            <span>Total Amount:</span>
            <span id="payment_total" class="font-mono text-blue-600">${formatCurrency(total)}</span>
        `;
        } else {
            summarySection.appendChild(discountSection);
        }
    }

    function calculateTotalDiscount() {
        const discountInputs = document.querySelectorAll('.discount-input');
        let totalDiscount = 0;

        discountInputs.forEach(input => {
            const price = parseFloat(input.dataset.price);
            const qty = parseInt(input.dataset.qty);
            const itemTotal = parseFloat(input.dataset.itemTotal);
            const eligiblePersons = parseInt(input.value) || 0;

            if (eligiblePersons > 0) {
                const perPersonCost = itemTotal / qty;

                const discountPerPerson = perPersonCost * 0.20;
                const totalDiscountForItem = discountPerPerson * eligiblePersons;
                totalDiscount += totalDiscountForItem;
            }
        });

        return totalDiscount;
    }

    function calculateDiscountedTotal() {
        const discountInputs = document.querySelectorAll('.discount-input');
        const discountBreakdown = document.getElementById('discount_items_breakdown');
        const discountPersonTotal = document.getElementById('discount_person_total');
        const paymentTotal = document.getElementById('payment_total');
        const discountSection = document.getElementById('discount_breakdown_section');

        if (!discountBreakdown || !discountPersonTotal || !paymentTotal) return;

        let totalDiscountedPersonPay = 0;
        let originalTotal = 0;

        discountBreakdown.innerHTML = '';

        discountInputs.forEach(input => {
            const price = parseFloat(input.dataset.price);
            const qty = parseInt(input.dataset.qty);
            const itemTotal = parseFloat(input.dataset.itemTotal);
            const itemName = input.dataset.itemName;
            const eligiblePersons = parseInt(input.value) || 0;

            originalTotal += itemTotal;

            if (eligiblePersons > 0) {
                const perPersonCost = itemTotal / qty;

                const discountedPerPersonCost = perPersonCost * 0.80;

                const discountedPersonsPayForItem = discountedPerPersonCost * eligiblePersons;
                totalDiscountedPersonPay += discountedPersonsPayForItem;

                const breakdownItem = document.createElement('div');
                breakdownItem.className = 'flex justify-between items-center text-sm bg-green-50 px-2 py-1 rounded';
                breakdownItem.innerHTML = `
                <span class="flex-1">${itemName} (${eligiblePersons} person${eligiblePersons > 1 ? 's' : ''})</span>
                <span class="text-gray-600">${formatCurrency(perPersonCost)} → ${formatCurrency(discountedPerPersonCost)} each</span>
                <span class="font-semibold text-green-700 ml-2">${formatCurrency(discountedPersonsPayForItem)}</span>
            `;
                discountBreakdown.appendChild(breakdownItem);
            }
        });

        discountPersonTotal.textContent = formatCurrency(totalDiscountedPersonPay);

        const totalDiscount = calculateTotalDiscount();
        const finalTotal = originalTotal - totalDiscount;
        paymentTotal.textContent = formatCurrency(finalTotal);

        if (discountSection) {
            if (totalDiscountedPersonPay > 0) {
                discountSection.style.display = 'block';
            } else {
                discountSection.style.display = 'none';
            }
        }
    }

    function closeInvoiceModal() {
        const invoiceModal = document.getElementById("invoice-modal");
        invoiceModal.classList.add("hidden");
        invoiceModal.classList.remove("flex");
    }

    function submitPayment() {

        if (!currentReservationData) {
            showToast("No reservation data available", "error");
            return;
        }

        const totalText = document.getElementById("payment_total").textContent;
        const total = parseFloat(totalText.replace('₱', '').replace(',', ''));

        const discountInputs = document.querySelectorAll('.discount-input');
        const discountedPersons = {};

        discountInputs.forEach((input, index) => {
            const orderDetailId = currentReservationData.orders[index].order_detail_id;
            const discountValue = parseInt(input.value) || 0;
            if (discountValue > 0) {
                discountedPersons[orderDetailId] = discountValue;
            }
        });
        const paymentData = {
            reservation_id: currentReservationData.reservation_id,
            customer_name: currentReservationData.customer_name,
            total: total,
            orders: currentReservationData.orders,
            discounted_persons: discountedPersons
        };

        const submitBtn = event.target;
        submitBtn.disabled = true;
        submitBtn.textContent = "Processing...";

        fetch('/process-payment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(paymentData)

        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast("Done!", "success");
                    closePaymentModal();
                    document.getElementById("invoice-modal").classList.add("hidden");
                    document.getElementById("invoice-modal").classList.remove("flex");
                    setTimeout(() => { location.reload(); }, 1500);
                } else {
                    showToast(data.message || "Payment processing failed", "error");
                }
            })
            .catch(error => {
                console.error('Payment error:', error);
                showToast("Payment processing failed. Please try again.", "error");
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = "Process Payment";
            });
    }

    function displayPaymentItems() {
        if (!currentReservationData) return;

        const paymentItemsList = document.getElementById("paymentItemsList");
        let total = 0;
        paymentItemsList.innerHTML = '';

        currentReservationData.orders.forEach((order, index) => {
            const price = parseFloat(order.price);
            const qty = parseInt(order.quantity);
            const itemTotal = price * qty;
            total += itemTotal;

            const orderLine = document.createElement("div");
            orderLine.classList.add("flex", "justify-between", "items-center", "py-1", "border-b", "border-gray-100");
            orderLine.innerHTML = `
            <span class="flex-1">${order.order_name}</span>
            <span class="w-16 text-center">${qty}</span>
            <span class="w-20 text-right">${formatCurrency(price)}</span>
            <span class="w-24 text-right font-medium">${formatCurrency(itemTotal)}</span>
            <span class="w-24 text-right">
                <input type="number" 
                       class="discount-input border border-gray-300 rounded px-1 py-1 w-16 text-sm" 
                       min="0" 
                       max="${qty}" 
                       value="0" 
                       data-price="${price}" 
                       data-qty="${qty}" 
                       data-item-total="${itemTotal}"
                       data-item-name="${order.order_name}"
                       data-order-detail-id="${order.order_detail_id || ''}"
                       data-index="${index}" />
            </span>`;
            paymentItemsList.appendChild(orderLine);
        });

        const discountInputs = document.querySelectorAll('.discount-input');
        discountInputs.forEach(input => {
            input.addEventListener('input', calculateDiscountedTotal);
            input.addEventListener('change', calculateDiscountedTotal);
        });

        document.getElementById("payment_total").textContent = formatCurrency(total);

        const summarySection = document.querySelector('#payment-modal .border.rounded-lg.p-4.bg-gray-50 .space-y-2');
        if (!summarySection) return;

        const existingDiscountSection = document.getElementById('discount_breakdown_section');
        if (existingDiscountSection) {
            existingDiscountSection.remove();
        }
        const discountSection = document.createElement('div');
        discountSection.id = 'discount_breakdown_section';
        discountSection.className = 'border-t pt-3 mt-2';
        discountSection.style.display = 'none';
        discountSection.innerHTML = `
        <h5 class="font-semibold text-gray-700 mb-2">DISCOUNTED PERSON TO PAY:</h5>
        <div id="discount_items_breakdown" class="space-y-1 mb-3"></div>
        <div class="flex justify-between text-lg font-bold text-green-600 border-b pb-2">
            <span>Mga Sabad:</span>
            <span id="discount_person_total" class="font-mono">₱0.00</span>
        </div>
    `;

        const totalRow = summarySection.querySelector('.flex.justify-between.text-lg.font-bold.border-t.pt-2');
        if (totalRow) {
            summarySection.insertBefore(discountSection, totalRow);

            totalRow.innerHTML = `
            <span>Total Amount:</span>
            <span id="payment_total" class="font-mono text-blue-600">${formatCurrency(total)}</span>
        `;
        } else {
            summarySection.appendChild(discountSection);
        }
    }
</script>


</body>

</html>