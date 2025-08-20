<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}" />
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
    </style>
</head>

<body>
    <header class="mt-2">
        <div class="border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-7 relative">
            <div class="flex items-center ml-5">
                <img src="{{ asset('logo/jeongol_logo.jpg') }}" alt="Jeongol Logo" class="h-13 w-20" />
            </div>

            <ul id="menuTabs"
                class="flex flex-wrap -mb-px text-sm font-medium text-center text-black absolute left-1/2 -translate-x-1/2">

                <li class="me-2">
                    <a href="#" data-tab="dinein"
                        class="tab-link inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-300 hover:border-gray-300 group active-tab">
                        <img src="{{ asset('assets/dine-in.png') }}" alt="Dine In" class="w-8 h-8 me-2" />
                        Dine In
                    </a>
                </li>

                <li class="me-2">
                    <a href="#" data-tab="reservations"
                        class="tab-link inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 group">
                        <img src="{{ asset('assets/reservation.png') }}" alt="Reservations" class="w-8 h-8 me-2" />
                        Reservations
                    </a>
                </li>
            </ul>
            <div class="relative z-0" x-data="{ open: false }">
                <button @click="open = !open"
                    class="inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300">
                    <svg class="w-8 h-8 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path
                            d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 13a8.949 8.949 0 0 1-4.951-1.488A3.987 3.987 0 0 1 9 13h2a3.987 3.987 0 0 1 3.951 3.512A8.949 8.949 0 0 1 10 18Z" />
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false"
                    class="absolute top-1/2 right-full -translate-y-1/2 mr-2 w-48 bg-white border rounded shadow-lg z-50">
                    <a href="#notifications" class="block px-4 py-2 hover:bg-gray-100">Notifications</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

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
                        data-modal-toggle="invoice-modal">✕</button>
                </div>

                <div class="p-4 space-y-4">
                    <div>
                        <p><strong>Date: </strong><span id="invoice_date"></span></p>
                        <p><strong>Customer: </strong><span id="customer_name"></span></p>
                    </div>

                    <div>
                        <div class="flex justify-between font-semibold border-b pb-2">
                            <span class="flex-1 ">Item</span>
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

    <!-- Pyment Modal -->
    <div id="payment-modal" tabindex="-1" aria-hidden="true"
        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto h-full bg-black bg-opacity-50 flex justify-center items-center">
        <div class="relative w-full max-w-2xl">
            <div class="relative bg-white text-black rounded-lg shadow-lg h-[80vh] overflow-y-auto flex flex-col">
                <!-- Modal Header -->
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

                <div class="flex-1 px-6 py-5 space-y-4">
                    <div class="flex justify-between font-semibold border-b pb-2">
                        <span>Items</span>
                        <span>Amount</span>
                    </div>

                    <div class="border-t pt-2 space-y-1">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Tax</span>
                            <span></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Discount</span>
                            <span></span>
                        </div>
                    </div>

                    <div class="border-t pt-2 mt-2 text-base font-bold">
                        <div class="flex justify-between">
                            <span>Total</span>
                            <span></span>
                        </div>
                    </div>
                </div>


                <!-- Modal Footer -->
                <div class="flex justify-end gap-4 px-6 py-4 border-t border-gray-200">
                    <button onclick="submitPaymentModal()" type="button"
                        class="w-40 py-2.5 text-white bg-blue-600 hover:bg-blue-700 rounded-lg font-semibold text-sm">
                        Submit
                    </button>

                </div>
            </div>
        </div>
    </div>


    <div id="toast"
        class="fixed top-5 right-5 z-50 hidden opacity-0 px-4 py-3 rounded-lg shadow-md bg-red-600 text-white text-sm font-medium transition-opacity duration-300 ease-in-out">
        <span id="toast-message">Something went wrong</span>
    </div>
    <script>
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

            tabLinks.forEach(tab => {
                tab.addEventListener("click", function (e) {
                    e.preventDefault();
                    const selectedTab = this.getAttribute("data-tab");
                    if (selectedTab === "dinein") {
                        dineInContent.classList.remove("hidden");
                    } else {
                        dineInContent.classList.add("hidden");
                    }
                });
            });

            dineInContent.addEventListener("click", function (event) {
                const table = event.target.closest(".table-link");
                if (!table) return;

                const reservationId = table.getAttribute("data-reservation-id");
                const isOccupied = table.getAttribute("data-occupied") === "1";

                if (isOccupied && reservationId) {
                    invoiceItemsList.innerHTML = '';
                    totalPriceInput.value = formatCurrency(0);

                    table.style.pointerEvents = "none";
                    setTimeout(() => {
                        table.style.pointerEvents = "auto";
                    }, 1000);

                    fetch(`/orders/${reservationId}`)
                        .then(res => res.json())
                        .then(data => {
                            if (!data) return;
                            customerNameSpan.textContent = data.customer_name || "N/A";
                            let total = 0;

                            data.orders.forEach(order => {
                                const price = parseFloat(order.price);
                                const qty = parseInt(order.quantity);
                                const subtotal = price * qty;
                                total += subtotal;

                                const orderLine = document.createElement("div");
                                orderLine.classList.add("flex", "justify-between");
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
                        .catch(() => {
                            showToast("Failed to load invoice data.");
                        });
                } else {
                    showToast("Table is not occupied.");
                }
            });

            document.querySelectorAll("[data-modal-toggle]").forEach(btn => {
                btn.addEventListener("click", () => {
                    invoiceModal.classList.add("hidden");
                    invoiceModal.classList.remove("flex");
                });
            });

            invoiceModal.addEventListener("click", function (e) {
                if (e.target === invoiceModal) {
                    invoiceModal.classList.add("hidden");
                    invoiceModal.classList.remove("flex");
                }
            });

            countdownElements.forEach(el => {
                let seconds = parseInt(el.dataset.seconds);

                function formatTime(s) {
                    const h = String(Math.floor(s / 3600)).padStart(2, '0');
                    const m = String(Math.floor((s % 3600) / 60)).padStart(2, '0');
                    const sec = String(s % 60).padStart(2, '0');
                    return `${h}:${m}:${sec}`;
                }

                function update() {
                    if (seconds <= 0) {
                        el.textContent = "00:00:00";
                        return;
                    }
                    el.textContent = formatTime(seconds--);
                    setTimeout(update, 1000);
                }

                update();
            });
        });

        function formatCurrency(amount) {
            return `₱${amount.toFixed(2)}`;
        }

        function showToast(message, type = "error", duration = 3000) {
            const toast = document.getElementById("toast");
            const toastMessage = document.getElementById("toast-message");

            toast.className = "fixed top-5 right-5 z-50 px-4 py-3 rounded-lg shadow-md text-white text-sm font-medium transition-opacity duration-300 ease-in-out";

            if (type === "success") {
                toast.classList.add("bg-green-600");
            } else if (type === "info") {
                toast.classList.add("bg-blue-600");
            } else {
                toast.classList.add("bg-red-600");
            }

            toastMessage.textContent = message;
            toast.classList.remove("hidden", "opacity-0");
            toast.classList.add("opacity-100");

            setTimeout(() => {
                toast.classList.remove("opacity-100");
                toast.classList.add("opacity-0");
                setTimeout(() => {
                    toast.classList.add("hidden");
                }, 300);
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
            setTimeout(() => {
                window.print();
                printArea.classList.add("hidden");
                printArea.classList.remove("flex");
            }, 500);
        }

        function openPaymentModal() {
            const modal = document.getElementById("payment-modal");
            if (modal) {
                modal.classList.remove("hidden");
                modal.classList.add("flex");
            }
        }
        function closePaymentModal() {
            document.getElementById('payment-modal').classList.add('hidden');
        }
    </script>


</body>

</html>