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
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                height: auto !important;
                background: white !important;
                z-index: 9999 !important;
            }

            #print_invoice .fixed,
            #print_invoice .bg-black,
            #print_invoice .bg-opacity-50 {
                position: static !important;
                background: transparent !important;
            }

            #print_invoice .p-4 {
                padding: 20px !important;
            }

            #print_invoice .flex.justify-between {
                display: grid !important;
                grid-template-columns: 2fr 1fr 1fr 1fr !important;
                gap: 10px !important;
            }

            #print_invoice .flex-1 {
                flex: none !important;
            }

            #print_invoice .w-16,
            #print_invoice .w-20,
            #print_invoice .w-24 {
                width: auto !important;
                text-align: right !important;
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

        .dropdown-menu {
            display: none;
        }

        .dropdown-menu.show {
            display: block;
        }
    </style>
</head>

<body class="relative">
    <div class="relative">
        <div class="mt-2 border-b border-gray-200 flex items-center justify-between px-7">
            <div class="logo flex items-center ml-5">
                <img src="{{ asset('logo/jeongol_logo.jpg') }}" alt="Jeongol Logo" class="h-13 w-20" />
            </div>
            <div class="relative">
                <!-- Profile Button -->
                <button id="userBtn" class="relative flex items-center gap-2 p-4 hover:bg-gray-100 z-50">
                    <div
                        class="w-14 h-14 rounded-full bg-gray-300 flex items-center justify-center font-bold text-black">
                        {{ strtoupper(substr(Auth::user()->firstname, 0, 1)) }}
                    </div>

                    <Payment Details!-- <span id="notifBadgeProfile"
                        class="absolute top-1 right-1 items-center justify-center px-2 py-1 text-xs font-bold text-white bg-red-600 rounded-full hidden"
                        data-count="{{ auth()->user()?->unreadNotifications->count() ?? 0 }}">
                        {{ auth()->user()?->unreadNotifications->count() ?? 0 }}
                        </span>

                        -->
                </button>

                <div id="userMenu"
                    class="hidden absolute top-full right-0 mt-2 w-56 bg-white border rounded-lg shadow-lg z-50">
                    <div class="px-4 py-3 border-b">
                        <p class="text-sm font-medium">{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</p>
                        <p class="text-xs text-gray-500">{{ Auth::user()->role }}</p>
                    </div>
                    <!--
                            <a href="javascript:void(0)" id="notifBtn" class="block px-4 py-2 hover:bg-gray-100 relative">
                                Notifications
                                <span id="notifBadgeLink"
                                    class="absolute top-1 right-1 hidden items-center justify-center px-2 py-1 text-xs font-bold text-white bg-red-600 rounded-full">
                                    {{ auth()->user()?->unreadNotifications->count() ?? 0 }}
                                </span>

                            </a>
        -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">Logout</button>
                    </form>
                </div>
            </div>

            <!-- Notifications Modal
                    <div id="notifModal"
                        class="hidden fixed inset-0 flex items-start justify-end z-50 bg-black bg-opacity-20 p-4 overflow-auto">
                        <div class="w-full max-w-xs sm:w-80 bg-white rounded-lg shadow-lg">
                            <div class="p-5 relative">
                                <h2 class="text-lg font-semibold mb-4">Notifications</h2>
                                <ul id="notifList" class="space-y-2 max-h-96 overflow-y-auto"></ul>
                                <button id="notifClose" class="absolute top-2 right-2">✖</button>
                            </div>
                        </div>
                    </div>
    --
                    <div id="paymentModal"
                        class="hidden fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50 p-4">
                        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md mx-auto relative">
                            <button id="closePaymentBtn"
                                class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 font-bold text-xl">×</button>
                            <h2 class="text-lg font-bold mb-4">Payment Details</h2>
                            <p><strong>Table number</strong> <span id="tableNumber">N/A</span></p>
                            <div>
                                <p><strong>Transaction Receipt</strong></p>
                                <img id="paymentProof" src="" class="mb-2 w-24 h-24 object-contain" style="display:none;">
                            </div>
                            <p><strong>Required Amount:</strong> <span id="requiredAmount">N/A</span></p>
                            <p><strong>Pax:</strong> <span id="paxCount">N/A</span></p>
                            <p><strong>Status:</strong> <span id="paymentStatus">N/A</span></p>
                            <div id="actionButtons" class="mt-4 text-center flex justify-center gap-2">
                                <form id="acceptForm" method="POST" class="inline">@csrf
                                    <button type="submit"
                                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Accept</button>
                                </form>
                                <button id="cancelReservationBtn" class="px-4 py-2 bg-red-500 text-white">Cancel</button>
                            </div>
                        </div>
                    </div>
                    <!-- Receipt Zoom Modal -->
            <div id="receiptModal"
                class="hidden fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50">
                <div class="relative max-w-4xl w-full p-4">
                    <!-- Close button -->
                    <button id="closeReceipt"
                        class="absolute top-2 right-2 text-white hover:text-gray-300 text-3xl font-bold">&times;</button>
                    <!-- Enlarged image -->
                    <img id="receiptImageLarge" src=""
                        class="w-full h-auto max-h-[90vh] object-contain rounded-lg shadow-lg">
                </div>
            </div>

        </div>

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
                        <div class="flex justify-center ">
                            <div
                                class="relative h-40 w-48 bg-white rounded-3xl shadow-md flex items-center justify-center ">
                                <div class="absolute mt-2 -top-1 px-3 bg-gray-200 text-black text-xs rounded-full shadow">
                                    {{ $table->capacity }} Pax
                                </div>

                                <div class="flex flex-col items-center mt-6">
                                    <div
                                        class="w-20 h-20 rounded-full {{ $isOccupied ? 'bg-red-600' : 'bg-green-600' }} text-white flex items-center justify-center shadow">
                                        <span class="text-lg font-semibold">Table-{{ $table->table_number }}</span>
                                    </div>
                                    @if($table->current_reservation_id && $table->remaining_seconds > 0)
                                        <span class="text-red-600 font-medium mt-2 flex items-center space-x-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 6v6l4 2m0-10a9 9 0 1 0 9 9 9 9 0 0 0-9-9z" />
                                            </svg>
                                            <span class="countdown"
                                                data-seconds="{{ $table->remaining_seconds }}">--:--:--</span>
                                        </span>
                                    @else
                                        <span class="text-green-600 font-medium">Available</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
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
                            <h4 class="text-lg font-semibold mb-3">Apply Discounts</h4>
                            <div class="flex justify-between font-semibold border-b pb-2 text-sm">
                                <span class="flex-1">Item</span>
                                <span class="w-20 text-right">Price</span>
                                <span class="w-40 text-center">Discount Type</span>
                                <span class="w-24 text-right">Total</span>
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
                        <button onclick="window.app.printBill()" type="button"
                            class="px-6 py-2.5 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg font-semibold text-sm">
                            Print Bill
                        </button>
                        <button onclick="submitPayment()" type="button"
                            class="px-8 py-2.5 text-white bg-blue-600 hover:bg-blue-700 rounded-lg font-semibold text-sm">
                            Process Payment
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div onclick="printBill()" class="hidden p-4 space-y-4">
            <div>
                <p><strong>Date: </strong><span id="invoice_date"></span></p>
                <p><strong>Customer: </strong><span id="customer_name"></span></p>
            </div>

            <div>
                <div class="flex justify-between font-semibold border-b pb-2">
                    <span class="flex-1">Item</span>
                    <span class="w-16 text-center">Price</span>
                    <span class="w-20 text-right">Quantity</span>
                    <span class="w-24 text-right">Total</span>
                </div>
                <div id="invoiceItemsList" class="space-y-1"></div>
            </div>

            <div class="flex justify-end font-bold text-lg border-t pt-2">
                <span>Total: </span>
                <input type="text" id="total_price" name="total_price"
                    class="bg-gray-200 border border-gray-300 text-sm rounded-lg w-32 p-2.5 ml-4 text-right" readonly />
            </div>
        </div>


        <div id="toast"
            class="fixed top-5 right-5 z-50 hidden opacity-0 px-4 py-3 rounded-lg shadow-md bg-red-600 text-white text-sm font-medium transition-opacity duration-300 ease-in-out">
            <span id="toast-message">Something went wrong</span>
        </div>
    </div>
</body>
<script>
    // Initialize menu data from backend
    window.menuPriceData = @json($menuData);
    window.menuPricesMap = {};
    const CASHIER_NAME = @json(auth()->user()->firstname . ' ' . auth()->user()->lastname);

    // Build menu prices map
    if (window.menuPriceData && Array.isArray(window.menuPriceData) && window.menuPriceData.length > 0) {
        window.menuPriceData.forEach(item => {
            if (item && item.menu_item) {
                window.menuPricesMap[item.menu_item] = {
                    regular: parseFloat(item.regular_price) || 0,
                    student: item.student_price !== null && item.student_price !== undefined ? parseFloat(item.student_price) : null,
                    govt_employee: item.govt_employee_price !== null && item.govt_employee_price !== undefined ? parseFloat(item.govt_employee_price) : null,
                    has_discount: item.has_customer_discount === 1 || item.has_customer_discount === true
                };
            }
        });
    } else {
        window.menuPricesMap = {};
    }

    class CashierApp {
        constructor() {
            this.elements = {};
            this.currentReservationData = null;
            this.currentModalType = null;
            this.currentReservationId = null;
            this.tempCustomerData = {};

            this.init();

        }

        init() {
            const initialize = () => {
                this.initializeElements();
                this.initializeEventListeners();
                this.setupTableClickEvents();
                this.initializeCountdowns();

            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initialize);
            } else {
                setTimeout(initialize, 0);
            }
        }

        initializeElements() {
            this.elements = {
                userBtn: document.getElementById('userBtn'),
                userMenu: document.getElementById('userMenu'),
                notifBtn: document.getElementById('notifBtn'),
                notifModal: document.getElementById('notifModal'),
                notifClose: document.getElementById('notifClose'),
                notifList: document.getElementById('notifList'),
                badgeProfile: document.getElementById('notifBadgeProfile'),
                badgeLink: document.getElementById('notifBadgeLink'),
                notificationPaymentModal: document.getElementById('paymentModal'),
                notificationCloseBtn: document.getElementById('closePaymentBtn'),
                acceptForm: document.getElementById('acceptForm'),
                cancelReservationBtn: document.getElementById('cancelReservationBtn'),
                tablePaymentModal: document.getElementById('payment-modal'),
                submitPaymentBtn: document.getElementById('submitPaymentBtn'),
                printBill: document.getElementById('printBill'),
            };
        }

        initializeEventListeners() {
            this.setupUserMenuEvents();
            this.setupNotificationEvents();
            this.setupModalEvents();
            this.setupPaymentEvents();
            this.initializeImagePopup();
        }

        setupUserMenuEvents() {
            if (this.elements.userBtn && this.elements.userMenu) {
                this.elements.userBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.elements.userMenu.classList.toggle('hidden');
                });

                this.elements.userMenu.addEventListener('click', (e) => e.stopPropagation());
                document.addEventListener('click', () => this.elements.userMenu.classList.add('hidden'));
            }
        }

        setupNotificationEvents() {
            if (this.elements.notifBtn && this.elements.notifModal) {
                this.elements.notifBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.elements.notifModal.classList.remove('hidden');
                });
            }

            if (this.elements.notifClose) {
                this.elements.notifClose.addEventListener('click', () => {
                    this.elements.notifModal.classList.add('hidden');
                });
            }

            if (this.elements.notifModal) {
                this.elements.notifModal.addEventListener('click', () => {
                    this.elements.notifModal.classList.add('hidden');
                });

                const notifContent = this.elements.notifModal.querySelector('div');
                if (notifContent) {
                    notifContent.addEventListener('click', (e) => e.stopPropagation());
                }
            }
        }

        setupModalEvents() {
            if (this.elements.notificationPaymentModal) {
                this.elements.notificationPaymentModal.addEventListener('click', (e) => {
                    if (e.target === e.currentTarget) {
                        this.closeNotificationModal();
                    }
                });
            }

            if (this.elements.tablePaymentModal) {
                this.elements.tablePaymentModal.addEventListener('click', (e) => {
                    if (e.target === e.currentTarget) {
                        this.closeTablePaymentModal();
                    }
                });
            }
        }

        setupPaymentEvents() {
            if (this.elements.notificationCloseBtn) {
                this.elements.notificationCloseBtn.addEventListener('click', () => {
                    this.closeNotificationModal();
                });
            }

            if (this.elements.cancelReservationBtn) {
                this.elements.cancelReservationBtn.addEventListener('click', () => {
                    this.closeNotificationModal();
                });
            }

            document.addEventListener('click', (e) => {
                if (e.target && (e.target.id === 'paymentButton' || e.target.classList.contains('payment-submit-btn'))) {
                    e.preventDefault();
                    this.submitPayment(e);
                }
            });

            const paymentBtn = document.getElementById('paymentButton');
            if (paymentBtn) {
                paymentBtn.addEventListener('click', (event) => {
                    event.preventDefault();
                    this.submitPayment(event);
                });
            }
        }

        setupTableClickEvents() {
            const tableLinks = document.querySelectorAll('.table-link');

            tableLinks.forEach((tableLink) => {
                tableLink.addEventListener('click', (e) => {
                    e.preventDefault();

                    const isOccupied = tableLink.getAttribute('data-occupied') === '1';
                    const tableNumber = tableLink.getAttribute('data-table-number');
                    const reservationId = tableLink.getAttribute('data-reservation-id');

                    if (isOccupied && reservationId) {
                        this.openTablePaymentModal(reservationId, tableNumber);
                    } else {
                        this.showToast('Table ' + tableNumber + ' is not occupied', 'info');
                    }
                });
            });
        }

        initializeCountdowns() {
            const countdownElements = document.querySelectorAll('.countdown');

            countdownElements.forEach(el => {
                let seconds = parseInt(el.dataset.seconds) || 0;

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
                    el.textContent = formatTime(seconds);
                    seconds--;
                    setTimeout(update, 1000);
                }

                update();
            });
        }


        openTablePaymentModal(reservationId, tableNumber) {
            this.currentModalType = 'table';
            this.currentReservationId = reservationId;

            if (this.elements.tablePaymentModal) {
                this.elements.tablePaymentModal.classList.remove('hidden');
                this.fetchReservationData(reservationId, tableNumber);
            } else {
                this.showToast('Payment modal not available', 'error');
            }
        }

        closeTablePaymentModal() {
            if (this.elements.tablePaymentModal) {
                this.elements.tablePaymentModal.classList.add('hidden');
            }
            this.currentModalType = null;
            this.currentReservationId = null;
            this.currentReservationData = null;
            this.tempCustomerData = {};
        }

        fetchPaymentDetails(id) {
            fetch('/receptionist/payments/' + id)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('HTTP error! status: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    this.updatePaymentModal(data);
                    this.toggleActionButtons(data.payment ? data.payment.status : null);
                })
                .catch(error => {
                    this.showToast('Failed to load payment details', 'error');
                });
        }

        fetchReservationData(reservationId, tableNumber) {
            fetch('/orders/' + reservationId)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('HTTP error! status: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && data.reservation_id) {
                        this.populatePaymentModal(data, tableNumber);
                    } else {
                        this.showToast('Reservation not found or no orders available', 'error');
                        this.populateBasicInfo(reservationId);
                    }
                })
                .catch(error => {
                    this.showToast('Failed to load reservation data', 'error');
                    this.populateBasicInfo(reservationId);
                });
        }

        populateBasicInfo(reservationId) {
            const customerNameInput = document.getElementById('payment_customer_name');
            const paxInput = document.getElementById('payment_pax');
            const paymentItemsList = document.getElementById('paymentItemsList');
            const paymentTotal = document.getElementById('payment_total');

            if (customerNameInput) customerNameInput.value = 'Loading...';
            if (paxInput) paxInput.value = 'Loading...';
            if (paymentItemsList) {
                paymentItemsList.innerHTML = '<div class="text-center text-gray-500 py-4">No orders placed yet</div>';
            }
            if (paymentTotal) paymentTotal.textContent = '₱0.00';
        }

        populatePaymentModal(reservationData, tableNumber) {
            const customerNameInput = document.getElementById('payment_customer_name');
            const paxInput = document.getElementById('payment_pax');

            if (customerNameInput) {
                customerNameInput.value = reservationData.customer_name || 'N/A';
            }
            if (paxInput) {
                paxInput.value = reservationData.pax || 'N/A';
            }

            const paymentItemsList = document.getElementById('paymentItemsList');
            const paymentTotal = document.getElementById('payment_total');

            if (reservationData.orders && reservationData.orders.length > 0) {
                this.currentReservationData = reservationData;
                this.processPayment(reservationData);
            } else {
                if (paymentItemsList) {
                    paymentItemsList.innerHTML = '<div class="text-center text-gray-500 py-4">No orders placed yet</div>';
                }
                if (paymentTotal) {
                    paymentTotal.textContent = '₱0.00';
                }
                this.updatePaymentSummaryBreakdown();
            }
        }

        initializeImagePopup() {
            const img = document.getElementById("paymentProof");
            if (!img) return;

            img.addEventListener("click", () => {
                if (!img.src || img.style.display === "none") return;

                // overlay
                const overlay = document.createElement("div");
                overlay.className = "fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50";
                overlay.style.cursor = "zoom-out";

                // preview image (smaller, fixed size)
                const bigImg = document.createElement("img");
                bigImg.src = img.src;
                bigImg.className =
                    "w-[300px] h-[300px] object-contain rounded-lg shadow-lg border-4 border-white";

                overlay.appendChild(bigImg);
                document.body.appendChild(overlay);

                // close on click
                overlay.addEventListener("click", () => overlay.remove());
            });
        }


        updatePaymentModal(data) {
            const paymentProof = document.getElementById('paymentProof');
            this.updateElementText('tableNumber', data.table_id || 'N/A');
            this.updateElementText('requiredAmount', data.advance_payment || 'N/A');
            this.updateElementText('paxCount', data.pax || 'N/A');
            this.updateElementText('paymentStatus', (data.payment && data.payment.status) ? data.payment.status : 'N/A');

            if (paymentProof) {
                if (data.payment && data.payment.proof_path) {
                    paymentProof.src = '/file-serve/' + data.payment.proof_path;
                    paymentProof.style.display = 'block';
                } else {
                    paymentProof.style.display = 'none';
                }
            }
        }

        updateElementText(elementId, text) {
            const element = document.getElementById(elementId);
            if (element) {
                element.textContent = text;
            }
        }

        processPayment(currentReservationData) {
            if (!currentReservationData?.orders) return;

            const paymentItemsList = document.getElementById("paymentItemsList");
            const paymentTotal = document.getElementById("payment_total");

            if (!paymentItemsList) return;

            let subtotal = 0;
            let itemCounter = 0;
            paymentItemsList.innerHTML = '';

            currentReservationData.orders.forEach(order => {
                const itemName = order.order_name;
                const menuItemData = window.menuPricesMap?.[itemName] || {};
                const price = parseFloat(order.regular_price || order.calculated_price || order.unit_price || 0);
                const qty = parseInt(order.quantity) || 1;

                for (let i = 0; i < qty; i++) {
                    subtotal += price;

                    const orderLine = document.createElement("div");
                    const showDiscountSelect = this.isMainCategoryItemWithDiscount(itemName, menuItemData);

                    if (showDiscountSelect) {
                        orderLine.className = "flex justify-between items-center py-2 border-b border-gray-100";
                        orderLine.innerHTML = `
                                <div class="flex-1">
                                    <div class="font-medium">${itemName}</div>
                                </div>
                                <span class="w-20 text-right">₱${price.toFixed(2)}</span>
                                <div class="w-40 text-right">
                                    <select class="discount-type-select border border-gray-300 rounded px-1 py-1 text-sm w-50" 
                                            data-item-price="${price}" 
                                            data-item-name="${itemName}" 
                                            id="discount-select-${itemCounter}">
                                        ${this.getDiscountOptions(menuItemData)}
                                    </select>
                                </div>
                                <span class="w-24 text-right font-medium item-total">₱${price.toFixed(2)}</span>
                            `;
                        paymentItemsList.appendChild(orderLine);
                    } else {
                        orderLine.className = "hidden";
                        orderLine.innerHTML = `<span class="item-total">₱${price.toFixed(2)}</span>`;
                        paymentItemsList.appendChild(orderLine);
                    }

                    itemCounter++;
                }
            });

            document.querySelectorAll('.discount-type-select').forEach(select => {
                select.addEventListener('change', (e) => {
                    this.calculateSingleItemDiscount(e.target);
                    this.updateTotalAfterDiscounts();
                });
            });

            if (paymentTotal) paymentTotal.textContent = '₱' + subtotal.toFixed(2);
            this.setupDiscountBreakdownSection();
        }

        isMainCategoryItemWithDiscount(itemName, menuItemData) {
            const mainItems = ['Unlimited Samgyupsal', 'HotPot', 'Fusion'];
            return mainItems.includes(itemName) && menuItemData.has_discount;
        }

        getDiscountOptions(menuData) {
            let options = '<option value="none">No Discount</option>';
            if (menuData.student > 0) options += '<option value="student">SD</option>';
            if (menuData.govt_employee > 0) options += '<option value="govt_employee">GED</option>';
            if (menuData.has_discount) options += '<option value="pwd_senior">PWD/SR</option>';
            return options;
        }

        calculateSingleItemDiscount(selectElement) {
            const itemPrice = parseFloat(selectElement.dataset.itemPrice);
            const discountType = selectElement.value;
            const itemName = selectElement.dataset.itemName;
            const menuItemData = window.menuPricesMap[itemName] || {};

            let discountedPrice = itemPrice;

            switch (discountType) {
                case 'student':
                    if (menuItemData.student !== null && menuItemData.student !== undefined) {
                        discountedPrice = parseFloat(menuItemData.student);
                    }
                    break;
                case 'govt_employee':
                    if (menuItemData.govt_employee !== null && menuItemData.govt_employee !== undefined) {
                        discountedPrice = parseFloat(menuItemData.govt_employee);
                    }
                    break;
                case 'pwd_senior':
                    discountedPrice = itemPrice * 0.8;
                    break;
                case 'none':
                default:
                    discountedPrice = itemPrice;
            }

            const parentElement = selectElement.closest('.flex');
            if (parentElement) {
                const itemTotalElement = parentElement.querySelector('.item-total');
                if (itemTotalElement) {
                    itemTotalElement.textContent = '₱' + discountedPrice.toFixed(2);
                }
            }
        }

        updateTotalAfterDiscounts() {
            const itemTotals = document.querySelectorAll('.item-total');

            let newTotal = 0;
            itemTotals.forEach(el => {
                newTotal += parseFloat(el.textContent.replace('₱', ''));
            });

            document.getElementById('payment_total').textContent = '₱' + newTotal.toFixed(2);
            this.updatePaymentSummaryBreakdown();
        }

        updatePaymentSummaryBreakdown() {
            const paymentSummaryDiv = document.querySelector('.border.rounded-lg.p-4.bg-gray-50 .space-y-2');

            if (!paymentSummaryDiv) return;

            if (!this.currentReservationData?.orders || this.currentReservationData.orders.length === 0) {
                paymentSummaryDiv.innerHTML = `
                        <div class="flex justify-between text-sm font-semibold border-b pb-2 mb-2">
                            <span class="flex-1">Item</span>
                            <span class="w-24 text-right">Total Price</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t pt-2">
                            <span>Total Amount:</span>
                            <span id="payment_total" class="font-mono text-blue-600">₱0.00</span>
                        </div>
                    `;
                return;
            }

            let totalAmount = 0;
            let breakdownHtml = '';

            const mainMenuItems = ['Unlimited Samgyupsal', 'HotPot', 'Fusion'];
            const itemGroups = {};
            const mainMenuEntries = [];
            let itemCounter = 0;

            this.currentReservationData.orders.forEach((order, orderIndex) => {
                const itemName = order.order_name;
                const price = parseFloat(order.regular_price || order.calculated_price || order.unit_price || 0);
                const qty = parseInt(order.quantity) || 1;

                for (let i = 0; i < qty; i++) {
                    let currentIndex = 0;
                    for (let prevOrderIndex = 0; prevOrderIndex < orderIndex; prevOrderIndex++) {
                        const prevOrder = this.currentReservationData.orders[prevOrderIndex];
                        currentIndex += parseInt(prevOrder.quantity) || 1;
                    }
                    currentIndex += i;

                    const itemTotalElements = document.querySelectorAll('.item-total');
                    const discountSelects = document.querySelectorAll('.discount-type-select');

                    let finalPrice = price;
                    let discountInfo = '';

                    if (currentIndex < itemTotalElements.length) {
                        finalPrice = parseFloat(itemTotalElements[currentIndex].textContent.replace('₱', ''));
                    }

                    let discountType = 'none';
                    if (currentIndex < discountSelects.length) {
                        const select = discountSelects[currentIndex];
                        if (select) {
                            discountType = select.value;
                            if (discountType !== 'none') {
                                const labels = {
                                    student: ' (Student Discount)',
                                    govt_employee: ' (Govt Employee Discount)',
                                    pwd_senior: ' (PWD/Senior Discount)'
                                };
                                discountInfo = labels[discountType] || '';
                            }
                        }
                    }

                    totalAmount += finalPrice;

                    if (mainMenuItems.includes(itemName)) {
                        mainMenuEntries.push({
                            name: itemName + discountInfo,
                            price: finalPrice,
                            hasDiscount: discountInfo !== '',
                            discountType,
                            index: itemCounter
                        });
                    } else {
                        const groupKey = itemName + discountInfo;
                        if (!itemGroups[groupKey]) {
                            itemGroups[groupKey] = {
                                count: 0,
                                totalPrice: 0,
                                unitPrice: finalPrice,
                                hasDiscount: discountInfo !== '',
                                index: itemCounter
                            };
                        }
                        itemGroups[groupKey].count++;
                        itemGroups[groupKey].totalPrice += finalPrice;
                    }

                    itemCounter++;
                }
            });

            let displayItemCounter = 0;

            mainMenuEntries.forEach(entry => {
                const hasDiscount = entry.hasDiscount;
                const baseItemName = entry.name.split(' (')[0];
                const discountType = entry.discountType || 'none';

                breakdownHtml += `<div class="flex justify-between items-center text-sm py-2">
                        <span class="flex-1 pr-2">${entry.name}</span>
                        <span class="w-20 text-right font-mono">₱${entry.price.toFixed(2)}</span>
                        <div class="w-12 flex justify-center ml-2">
                            ${hasDiscount ? `<button onclick="window.app.openCustomerInfoModal('${baseItemName}', ${displayItemCounter}, '${discountType}')" 
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs font-medium">
                                            Info
                                            </button>` : ''}
                        </div>
                    </div>`;
                displayItemCounter++;
            });

            Object.keys(itemGroups).forEach(groupKey => {
                const group = itemGroups[groupKey];
                const displayName = group.count > 1 ? `${groupKey} x${group.count}` : groupKey;
                const hasDiscount = group.hasDiscount;
                const baseItemName = groupKey.split(' (')[0];
                const discountType = group.discountType || 'none';

                breakdownHtml += `<div class="flex justify-between items-center text-sm py-2">
                        <span class="flex-1 pr-2">${displayName}</span>
                        <span class="w-20 text-right font-mono">₱${group.totalPrice.toFixed(2)}</span>
                        <div class="w-12 flex justify-center ml-2">
                            ${hasDiscount ? `<button onclick="window.app.openCustomerInfoModal('${baseItemName}', ${displayItemCounter}, '${discountType}')" 
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs font-medium">
                                            Info
                                            </button>` : ''}
                        </div>
                    </div>`;
                displayItemCounter++;
            });

            paymentSummaryDiv.innerHTML = `
                    <div class="flex justify-between text-sm font-semibold border-b pb-3 mb-4">
                        <span class="flex-1">Item</span>
                        <span class="w-20 text-right">Total Price</span>
                        <span class="w-12 text-center ml-2"></span>
                    </div>
                    ${breakdownHtml}
                    <div class="flex justify-between text-lg font-bold border-t pt-4 mt-4">
                        <span>Total Amount:</span>
                        <span id="payment_total" class="font-mono text-blue-600">₱${totalAmount.toFixed(2)}</span>
                    </div>
                `;
        }

        setupDiscountBreakdownSection() {
            this.updatePaymentSummaryBreakdown();
        }

        openCustomerInfoModal(itemName, itemIndex, discountType = null) {
            let idTypeValue = '';
            switch (discountType) {
                case 'student':
                    idTypeValue = "Student ID";
                    break;
                case 'govt_employee':
                    idTypeValue = "Government ID";
                    break;
                case 'pwd_senior':
                    idTypeValue = "Senior Citizen ID / PWD ID";
                    break;
                default:
                    idTypeValue = "N/A";
            }

            // Check if we already have saved data for this item
            const key = `${itemName}_${itemIndex}`;
            const savedData = this.tempCustomerData[key];
            const savedName = savedData ? savedData.name : '';

            const modalHtml = `
        <div id="customerInfoModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 w-96 max-w-sm mx-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Customer Information</h3>
                    <button id="closeCustomerInfoModal" class="text-gray-400 hover:text-gray-600">X</button>
                </div>
                
                <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <strong>Item:</strong> ${itemName}
                    </p>
                    <p class="text-xs text-blue-600 mt-1">
                        Customer information is required for this discount
                    </p>
                </div>
                
                <form id="customerInfoForm" class="space-y-4">
                    <input type="hidden" name="item_name" value="${itemName}">
                    <input type="hidden" name="item_index" value="${itemIndex}">
                    <input type="hidden" name="customer_type" value="${discountType}">
                    
                    <div>
                        <label for="customerName" class="block text-sm font-medium text-gray-700 mb-1">
                            Customer Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="customerName" name="name" required value="${savedName}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md"
                            placeholder="Enter customer name">
                        <div id="nameError" class="text-red-500 text-xs mt-1 hidden">Customer name is required</div>
                    </div>

                    <div>
                        <label for="customerIdType" class="block text-sm font-medium text-gray-700 mb-1">
                            ID Type <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="customerIdType" name="id_type" value="${savedData ? savedData.id_type || idTypeValue : idTypeValue}" readonly
                            class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-700 cursor-not-allowed">
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" id="cancelCustomerInfo" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md">
                            Close
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
        `;

            const existingModal = document.getElementById('customerInfoModal');
            if (existingModal) {
                existingModal.remove();
            }

            document.body.insertAdjacentHTML('beforeend', modalHtml);
            this.setupCustomerInfoModalEvents();
        }

        setupCustomerInfoModalEvents() {
            const modal = document.getElementById('customerInfoModal');
            const closeBtn = document.getElementById('closeCustomerInfoModal');
            const cancelBtn = document.getElementById('cancelCustomerInfo');
            const form = document.getElementById('customerInfoForm');

            const closeModal = () => {
                if (modal) {
                    modal.remove();
                }
            };

            if (closeBtn) closeBtn.addEventListener('click', closeModal);
            if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

            if (modal) {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        closeModal();
                    }
                });
            }

            if (form) {
                form.addEventListener('submit', (e) => {
                    e.preventDefault();

                    const formData = new FormData(form);
                    const customerName = formData.get('name');
                    const idType = formData.get('id_type');

                    const nameError = document.getElementById('nameError');
                    let hasError = false;


                    if (!customerName ||
                        customerName === '' ||
                        customerName === null ||
                        customerName === undefined ||
                        customerName.trim() === '' ||
                        customerName.trim().length === 0) {

                        nameError.textContent = 'Customer name is required and cannot be empty';
                        nameError.classList.remove('hidden');
                        hasError = true;
                    }
                    else if (customerName.trim().length < 2) {
                        nameError.textContent = 'Customer name must be at least 2 characters';
                        nameError.classList.remove('hidden');
                        hasError = true;
                    }
                    else {
                        nameError.classList.add('hidden');
                    }

                    if (hasError) {
                        this.showToast('Please enter a valid customer name before saving', 'error');
                        return;
                    }

                    const trimmedName = customerName.trim();

                    if (!trimmedName || trimmedName === '' || trimmedName.length === 0) {
                        this.showToast('Customer name cannot be empty', 'error');
                        return;
                    }

                    const customerData = {
                        name: trimmedName,
                        id_type: idType,
                        item_name: formData.get('item_name'),
                        item_index: formData.get('item_index'),
                        customer_type: formData.get('customer_type')
                    };


                    if (this.saveCustomerInfoTemporarily(customerData)) {
                        closeModal();
                    }
                });
            }
        }

        saveCustomerInfoTemporarily(customerData) {
            if (!customerData ||
                !customerData.name ||
                customerData.name === '' ||
                customerData.name === null ||
                customerData.name === undefined ||
                customerData.name.trim() === '' ||
                customerData.name.trim().length === 0) {

                this.showToast('Cannot save empty customer name', 'error');
                return false;
            }

            const key = `${customerData.item_name}_${customerData.item_index}`;
            this.tempCustomerData[key] = {
                name: customerData.name.trim(),
                id_type: customerData.id_type,
                item_name: customerData.item_name,
                item_index: parseInt(customerData.item_index),
                customer_type: customerData.customer_type
            };

            return true;
        }

        submitPayment(event) {
            const currentReservationData = this.currentReservationData;

            if (!currentReservationData) {
                this.showToast("No reservation data available", "error");
                return;
            }

            const totalText = document.getElementById("payment_total").textContent;
            const subtotal = parseFloat(totalText.replace('₱', '').replace(',', ''));

            // Get advance payment from reservation data
            const advancePayment = parseFloat(currentReservationData.advance_payment || 0);

            // Calculate final total after deducting advance payment
            const finalTotal = Math.max(0, subtotal - advancePayment);

            const discountSelects = document.querySelectorAll('.discount-type-select');
            let discountedItems = [];
            let hasActiveDiscounts = false;

            discountSelects.forEach((select, index) => {
                if (select.value !== 'none') {
                    hasActiveDiscounts = true;

                    let itemName = '';
                    itemName = select.dataset.itemName || select.getAttribute('data-item-name') || '';

                    if (!itemName) {
                        const parentDiv = select.closest('.flex');
                        if (parentDiv) {
                            const itemNameDiv = parentDiv.querySelector('.font-medium');
                            if (itemNameDiv) {
                                itemName = itemNameDiv.textContent?.trim() || '';
                            }
                        }
                    }

                    if (!itemName && currentReservationData.orders[index]) {
                        itemName = currentReservationData.orders[index].order_name || '';
                    }

                    if (itemName) {
                        discountedItems.push({
                            index: index,
                            itemName: itemName,
                            discountType: select.value
                        });
                    } else {
                        console.error(`Could not determine item name for select index ${index}`);
                    }
                }
            });

            if (hasActiveDiscounts) {
                let itemsWithoutValidNames = [];

                discountedItems.forEach(item => {
                    const key = `${item.itemName}_${item.index}`;
                    const customerData = this.tempCustomerData[key];

                    let hasValidName = false;

                    if (customerData &&
                        customerData.name &&
                        customerData.name !== '' &&
                        customerData.name !== null &&
                        customerData.name !== undefined &&
                        customerData.name.trim() !== '' &&
                        customerData.name.trim().length > 0) {
                        hasValidName = true;
                    }

                    if (!hasValidName) {
                        itemsWithoutValidNames.push(item.itemName);
                    }
                });

                if (itemsWithoutValidNames.length > 0) {
                    const itemsList = itemsWithoutValidNames.join(', ');
                    this.showToast(`Customer name is required for discounted items.`, "error");
                    return;
                }
            }

            const allCustomerData = [];
            if (hasActiveDiscounts) {
                discountedItems.forEach(item => {
                    const key = `${item.itemName}_${item.index}`;
                    const customerInfo = this.tempCustomerData[key];

                    if (customerInfo &&
                        customerInfo.name &&
                        customerInfo.name.trim() !== '' &&
                        customerInfo.name.trim().length > 0) {
                        allCustomerData.push({
                            name: customerInfo.name.trim(),
                            id_type: customerInfo.id_type,
                            item_name: customerInfo.item_name,
                            item_index: parseInt(customerInfo.item_index),
                            customer_type: customerInfo.customer_type
                        });
                    }
                });

                if (allCustomerData.length !== discountedItems.length) {
                    this.showToast(`PAYMENT BLOCKED: Need customer names for all ${discountedItems.length} discounted items. Only ${allCustomerData.length} valid names provided.`, "error");
                    return;
                }
            }

            // Pass both subtotal and final total to the cash handling modal
            this.showCashHandlingModal(finalTotal, subtotal, advancePayment, currentReservationData, allCustomerData);
        }

        showCashHandlingModal(finalTotal, subtotal, advancePayment, currentReservationData, allCustomerData) {
            const modalHtml = `
                    <div id="cashHandlingModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                        <div class="bg-white rounded-lg p-6 w-80 max-w-sm mx-4 shadow-2xl">
                            <div class="text-center mb-6">
                                <h2 class="text-xl font-bold text-gray-800 mb-4">Cash Payment</h2>
                                
                                <!-- Payment Breakdown -->
                                <div class="bg-blue-50 p-3 rounded-lg mb-4 space-y-2">
                                    <div class="flex justify-between text-sm text-blue-800">
                                        <span>Subtotal:</span>
                                        <span>₱${subtotal.toFixed(2)}</span>
                                    </div>
                                    ${advancePayment > 0 ? `
                                    <div class="flex justify-between text-sm text-green-600 items-center">
                                        <label for="advancePayment" class="mr-2">Advance Payment:</label>
                                        <input 
                                            type="number" 
                                            id="advancePayment" 
                                            name="advancePayment" 
                                            value="${advancePayment.toFixed(2)}"
                                            class="w-24 text-green-600 border border-green-600 rounded px-1 py-0.5 text-right"
                                            step="0.01"
                                            min="0"
                                            oninput="
                                                const newAdvance = parseFloat(this.value)||0;
                                                const newAmountDue = ${subtotal} - newAdvance;
                                                document.getElementById('amountDue').textContent = '₱'+newAmountDue.toFixed(2);
                                                fetch('/cashier/update-advance/${currentReservationData.reservation_id}', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                                                    },
                                                    body: JSON.stringify({ advance_payment: newAdvance })
                                                });
                                            "
                                        >
                                    </div>

                                    <hr class="border-blue-200">
                                    ` : ''}
                                    <div class="flex justify-between">
    <span class="text-sm text-blue-800">Amount Due:</span>
    <span id="amountDue" class="text-2xl font-bold text-blue-600">₱${finalTotal.toFixed(2)}</span>
</div>

                                </div>
                            </div>

                            <form id="cashPaymentForm" class="space-y-4">
                                <div>
                                    <label for="cashReceived" class="block text-sm font-medium text-gray-700 mb-2">
                                        Cash Received
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₱</span>
                                        <input 
                                            type="number" 
                                            id="cashReceived" 
                                            name="cashReceived" 
                                            step="0.01" 
                                            min="${finalTotal}"
                                            class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg text-lg font-mono"
                                            placeholder="0.00"
                                            required
                                            autocomplete="off">
                                    </div>
                                    <div id="cashError" class="text-red-500 text-xs mt-1 hidden">
                                        Insufficient amount
                                    </div>
                                </div>

                                <div class="flex justify-between space-x-3 mt-6">
                                    <button 
                                        type="button" 
                                        id="cancelCashPayment" 
                                        class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                                        Cancel
                                    </button>
                                    <button 
                                        type="submit" 
                                        id="completeCashPayment"
                                        class="flex-1 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 disabled:bg-gray-400"
                                        disabled>
                                        Submit
                                        </button>
                                </div>
                            </form>
                        </div>
                    </div>
                `;

            const existingModal = document.getElementById('cashHandlingModal');
            if (existingModal) {
                existingModal.remove();
            }

            document.body.insertAdjacentHTML('beforeend', modalHtml);
            this.setupCashHandlingEvents(finalTotal, subtotal, advancePayment, currentReservationData, allCustomerData);
        }


        setupCashHandlingEvents(finalTotal, subtotal, advancePayment, currentReservationData, allCustomerData) {
            const modal = document.getElementById('cashHandlingModal');
            const cashInput = document.getElementById('cashReceived');
            const completeButton = document.getElementById('completeCashPayment');
            const cancelButton = document.getElementById('cancelCashPayment');
            const cashError = document.getElementById('cashError');

            setTimeout(() => cashInput.focus(), 100);

            // Validate cash input
            cashInput.addEventListener('input', () => {
                const cashReceived = parseFloat(cashInput.value) || 0;
                cashError.classList.add('hidden');

                if (cashReceived >= finalTotal) {
                    completeButton.disabled = false;
                } else {
                    completeButton.disabled = true;
                    if (cashReceived > 0) {
                        cashError.classList.remove('hidden');
                    }
                }
            });

            // Handle form submission
            const form = document.getElementById('cashPaymentForm');
            form.addEventListener('submit', (e) => {
                e.preventDefault();

                const cashReceived = parseFloat(cashInput.value) || 0;

                if (cashReceived >= finalTotal) {
                    const change = cashReceived - finalTotal;
                    modal.remove();
                    this.processFinalPayment(finalTotal, subtotal, advancePayment, cashReceived, change, currentReservationData, allCustomerData);
                } else {
                    cashError.classList.remove('hidden');
                }
            });

            cancelButton.addEventListener('click', () => {
                modal.remove();
            });

            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                }
            });

            cashInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !completeButton.disabled) {
                    form.dispatchEvent(new Event('submit'));
                }
            });
        }
        showProcessingModal() {
            const modalHtml = `
                <div id="processingModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                    <div class="bg-white rounded-lg p-8 w-64 text-center shadow-2xl">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Processing Payment</h3>
                        <p class="text-sm text-gray-600">Please wait...</p>
                    </div>
                </div>
            `;

            document.body.insertAdjacentHTML('beforeend', modalHtml);
        }

        removeProcessingModal() {
            const modal = document.getElementById('processingModal');
            if (modal) {
                modal.remove();
            }
        }

        processFinalPayment(finalTotal, subtotal, advancePayment, cashReceived, change, currentReservationData, allCustomerData) {
            // Show processing modal
            this.showProcessingModal();

            // Auto-print receipt immediately
            this.printCashReceipt(finalTotal, subtotal, advancePayment, cashReceived, change, currentReservationData);

            // Prepare payment data
            const discountInputs = document.querySelectorAll('.discount-input');
            const discountedPersons = {};

            discountInputs.forEach((input, index) => {
                const orderDetailId = currentReservationData.orders[index]?.order_detail_id;
                const discountValue = parseInt(input.value) || 0;

                if (discountValue > 0 && orderDetailId) {
                    discountedPersons[orderDetailId] = discountValue;
                }
            });

            const ordersData = currentReservationData.orders.map(order => {
                return {
                    order_detail_id: order.order_detail_id,
                    order_name: order.order_name,
                    quantity: parseInt(order.quantity) || 1,
                    price: parseFloat(order.regular_price || order.calculated_price || order.unit_price || 0)
                };
            });

            const paymentData = {
                reservation_id: currentReservationData.reservation_id,
                customer_name: currentReservationData.customer_name,
                subtotal: subtotal,
                advance_payment: advancePayment,
                total: finalTotal, // This is the final amount to pay after advance deduction
                orders: ordersData,
                discounted_persons: discountedPersons,
                customer_data: allCustomerData,
                cash_received: cashReceived,
                change_given: change
            };

            // Process payment
            fetch('/process-payment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(paymentData)
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || `HTTP ${response.status}: Error occurred`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    this.removeProcessingModal();

                    if (data.success) {
                        this.updateTableStatusAfterPayment(currentReservationData.reservation_id);
                        this.showToast("Payment completed successfully!", "success");

                        this.tempCustomerData = {};
                        this.currentReservationData = null;

                        setTimeout(() => { location.reload(); }, 1500);
                    } else {
                        this.showToast(data.message || "Payment processing failed", "error");
                    }
                })
                .catch(error => {
                    this.removeProcessingModal();

                    if (error.message.includes('Session expired') || error.message.includes('expired')) {
                        this.showToast("Your session has expired. Please refresh the page and try again.", "error");
                    } else if (error.message.includes('endpoint not found')) {
                        this.showToast("Payment system is currently unavailable. Please contact support.", "error");
                    } else if (error.message.includes('Server error')) {
                        this.showToast("Server error occurred. Please try again in a few moments.", "error");
                    } else if (error.message.includes('already exists')) {
                        this.showToast("Customer ID number already exists. Please use a different ID number.", "error");
                    } else if (error.message.includes('Database error')) {
                        this.showToast("Database error occurred. Please try again.", "error");
                    } else if (error.message.includes('Unexpected token')) {
                        this.showToast("Server returned invalid response. Please refresh the page and try again.", "error");
                    } else {
                        this.showToast(error.message || "Payment processing failed. Please try again.", "error");
                    }
                });
        }

        printCashReceipt(finalTotal, subtotal, advancePayment, cashReceived, change, currentReservationData) {
            const receiptData = {
                customer_name: currentReservationData.customer_name,
                subtotal: subtotal,
                advance_payment: advancePayment,
                total: finalTotal,
                cash_received: cashReceived,
                change: change,
                orders: currentReservationData.orders.map(order => ({
                    order_name: order.order_name,
                    quantity: parseInt(order.quantity) || 1,
                    price: parseFloat(order.regular_price || order.calculated_price || order.unit_price || 0)
                }))
            };

            fetch('/print-receipt', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(receiptData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Receipt printed to thermal printer');
                    } else {
                        console.error('Thermal printer failed:', data.message);
                        this.printCashReceiptBrowser(finalTotal, subtotal, advancePayment, cashReceived, change, currentReservationData);
                    }
                })
                .catch(error => {
                    console.error('Printer connection error:', error);
                    this.printCashReceiptBrowser(finalTotal, subtotal, advancePayment, cashReceived, change, currentReservationData);
                });
        }
        printCashReceiptBrowser(finalTotal, subtotal, advancePayment, cashReceived, change, currentReservationData) {
            // Create a printable receipt for browser printing
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
        <html>
        <head>
            <title>Receipt</title>
            <style>
                body { font-family: monospace; width: 300px; }
                .center { text-align: center; }
                .line { border-bottom: 1px dashed #000; margin: 5px 0; }
            </style>
        </head>
        <body>
            <div class="center"><strong>RESTAURANT RECEIPT</strong></div>
            <div class="center">${new Date().toLocaleString()}</div>
            <div class="line"></div>
            <div>Customer: ${currentReservationData.customer_name}</div>
            <!-- Add more receipt content here -->
            <div class="line"></div>
            <div>Total: ₱${finalTotal.toFixed(2)}</div>
            <div>Cash: ₱${cashReceived.toFixed(2)}</div>
            <div>Change: ₱${change.toFixed(2)}</div>
        </body>
        </html>
    `);
            printWindow.document.close();
            printWindow.print();
        }

        updateTableStatusAfterPayment(reservationId) {
            const tableElement = document.querySelector(`[data-reservation-id="${reservationId}"]`);

            if (tableElement) {
                const tableCircle = tableElement.querySelector('.w-16.h-16.rounded-full');
                const statusText = tableElement.querySelector('.text-red-600, .text-green-600');
                const countdownElement = tableElement.querySelector('.countdown');

                if (tableCircle) {
                    tableCircle.classList.remove('bg-red-600');
                    tableCircle.classList.add('bg-green-600');
                }

                if (statusText) {
                    statusText.textContent = 'Available';
                    statusText.classList.remove('text-red-600');
                    statusText.classList.add('text-green-600');
                }

                if (countdownElement && countdownElement.parentElement) {
                    countdownElement.parentElement.style.display = 'none';
                }

                tableElement.setAttribute('data-occupied', '0');
                tableElement.setAttribute('data-reservation-id', '');
                tableElement.classList.remove('table-occupied');
                tableElement.classList.add('table-available');
            }
        }

        printBill() {
            const cashierName = CASHIER_NAME;

            const customerName = document.getElementById('payment_customer_name')?.value || 'Walk-in Customer';

            const paymentSummaryDiv = document.querySelector('.border.rounded-lg.p-4.bg-gray-50 .space-y-2');
            const totalElement = document.getElementById('payment_total');

            if (!paymentSummaryDiv || !totalElement) {
                this.showToast('No payment summary available to print', 'error');
                return;
            }

            const summaryItems = paymentSummaryDiv.querySelectorAll('.flex.justify-between.items-center.text-sm.py-2');

            if (summaryItems.length === 0) {
                this.showToast('No items found in payment summary', 'error');
                return;
            }

            const now = new Date();
            const date = now.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });


            let itemsHTML = '';

            summaryItems.forEach(item => {
                const itemNameElement = item.querySelector('.flex-1.pr-2');
                const priceElement = item.querySelector('.w-20.text-right.font-mono');

                if (itemNameElement && priceElement) {
                    const itemName = itemNameElement.textContent.trim();
                    const price = priceElement.textContent.trim();

                    let displayName = itemName;
                    let quantity = '1';

                    const quantityMatch = itemName.match(/(.+)\s+x(\d+)$/);
                    if (quantityMatch) {
                        displayName = quantityMatch[1].trim();
                        quantity = quantityMatch[2];
                    }

                    itemsHTML += `<tr>
                        <td>${displayName}</td>
                        <td style="text-align: center;">${quantity}</td>
                        <td style="text-align: right;">${price}</td>
                    </tr>`;
                }
            });

            const total = totalElement.textContent.trim();

            const printHTML = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Restaurant Bill</title>
                    <style>
                        body { font-family: "Courier New", monospace; margin: 20px; font-size: 12px; line-height: 1.4; }
                        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                        th, td { padding: 8px 4px; border-bottom: 1px dotted #ccc; }
                        th { border-bottom: 2px solid #000; font-weight: bold; }
                        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
                        .footer { margin-top: 30px; text-align: center; font-size: 10px; border-top: 1px dotted #ccc; padding-top: 15px; }
                        @media print { body { margin: 0; } }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>RESTAURANT BILL</h1>
                    </div>
                    <p><strong>Cashier:</strong> ${cashierName}</p>
                    <p><strong>Date:</strong> ${date}</p>
                    <p><strong>Customer:</strong> ${customerName}</p>
                    
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 50%;">Item</th>
                                <th style="width: 20%; text-align: center;">Qty</th>
                                <th style="width: 30%; text-align: right;">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${itemsHTML}
                        </tbody>
                    </table>
                    
                    <div style="border-top: 2px solid #000; padding-top: 10px; margin-top: 20px;">
                        <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 16px;">
                            <span>TOTAL:</span>
                            <span>${total}</span>
                        </div>
                    </div>
                    
        </body>

        </html>
        `;

            const printWindow = window.open('', '_blank', 'width=800,height=600');
            if (printWindow) {
                printWindow.document.write(printHTML);
                printWindow.document.close();
            } else {
                this.showToast('Unable to open print window. Please check popup settings.', 'error');
            }
        }


        showToast(message, type = 'info') {
            if (typeof window.showToast === 'function') {
                window.showToast(message, type);
                return;
            }

            const toastContainer = document.getElementById('toast-container') || this.createToastContainer();

            const toast = document.createElement('div');
            toast.className = `toast toast-${type} fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transform
        translate-x-full transition-transform duration-300`;

            const colors = {
                success: 'bg-green-500 text-white',
                error: 'bg-red-500 text-white',
                warning: 'bg-yellow-500 text-black',
                info: 'bg-blue-500 text-white'
            };

            toast.className += ` ${colors[type] || colors.info}`;
            toast.textContent = message;

            toastContainer.appendChild(toast);

            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);

            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }, 3000);
        }

        createToastContainer() {
            const container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'fixed top-4 right-4 z-50 space-y-2';
            document.body.appendChild(container);
            return container;
        }

        toggleActionButtons(paymentStatus) {
        }
    }

    const app = new CashierApp();
    window.app = app;

    function closePaymentModal() {
        if (window.app) {
            window.app.closeTablePaymentModal();
        }
    }

    function submitPayment(event) {
        if (!event && typeof window.event !== 'undefined') {
            event = window.event;
        }

        if (window.app && window.app.submitPayment) {
            window.app.submitPayment(event);
        }
    }


</script>

</body>

</html>