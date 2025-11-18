<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Cashier</title>
    @livewireStyles
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
                <button id="userBtn" class="relative flex items-center gap-2 p-4 hover:bg-gray-100 z-50">
                    <div
                        class="w-14 h-14 rounded-full bg-gray-300 flex items-center justify-center font-bold text-black">
                        {{ strtoupper(substr(Auth::user()->firstname, 0, 1)) }}
                    </div>
                </button>
                <div id="userMenu"
                    class="hidden absolute top-full right-0 mt-2 w-56 bg-white border rounded-lg shadow-lg z-50">
                    <div class="px-4 py-3 border-b">
                        <p class="text-sm font-medium">{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</p>
                        <p class="text-xs text-gray-500">{{ Auth::user()->role }}</p>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">Logout</button>
                    </form>
                </div>
            </div>
            <div id="receiptModal"
                class="hidden fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50">
                <div class="relative max-w-4xl w-full p-4">
                    <button id="closeReceipt"
                        class="absolute top-2 right-2 text-white hover:text-gray-300 text-3xl font-bold">&times;</button>
                    <img id="receiptImageLarge" src=""
                        class="w-full h-auto max-h-[90vh] object-contain rounded-lg shadow-lg">
                </div>
            </div>
        </div>
        <livewire:cashier-table-layout />
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
                            <div class="space-y-2" id="paymentSummaryContent">
                            </div>
                            <div class="mt-6 pt-4 border-t border-gray-300">
                                <h5 class="text-md font-semibold mb-3">Cash Payment</h5>
                                <div class="bg-blue-50 p-3 rounded-lg mb-4 space-y-2">
                                    <div class="flex justify-between text-sm text-blue-800">
                                        <span>Subtotal:</span>
                                        <span id="summary_subtotal">₱0.00</span>
                                    </div>
                                    <div class="flex justify-between text-sm text-green-600" id="advance_payment_row"
                                        style="display: none;">
                                        <span>Advance Payment:</span>
                                        <span id="summary_advance">₱0.00</span>
                                    </div>
                                    <div class="flex justify-between font-bold text-blue-600 text-lg border-t pt-2">
                                        <span>Amount Due:</span>
                                        <span id="summary_amount_due">₱0.00</span>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="cashReceived" class="block text-sm font-medium text-gray-700 mb-2">
                                        Cash Received <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span
                                            class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₱</span>
                                        <input type="number" id="cashReceived" step="0.01" min="0"
                                            class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg text-lg font-mono"
                                            placeholder="0.00" autocomplete="off">
                                    </div>
                                    <div id="cashError" class="text-red-500 text-xs mt-1 hidden">
                                        Insufficient amount
                                    </div>
                                    <div class="text-sm text-gray-600 mt-2">
                                        Change: <span id="change_amount" class="font-bold text-green-600">₱0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-4 px-6 py-4 border-t border-gray-200">
                        <button onclick="window.app.showPrintModal()" type="button"
                            class="px-6 py-2.5 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg font-semibold text-sm">
                            Print Bill
                        </button>
                        <button onclick="window.app.showProcessPaymentModal(event)" type="button" id="processPaymentBtn"
                            class="px-8 py-2.5 text-white bg-blue-600 hover:bg-blue-700 rounded-lg font-semibold text-sm disabled:bg-gray-400"
                            disabled>
                            Process Payment
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div id="printBillModal"
            class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Print Bill</h3>
                </div>
                <div class="px-6 py-4">
                    <p class="text-gray-700 mb-4">
                        Print bill for <strong id="printModalCustomerName">Walk-in Customer</strong>?
                    </p>
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 max-h-64 overflow-y-auto">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Order Summary</h4>
                        <div id="printModalOrderItems" class="space-y-2">
                        </div>
                        <div class="border-t border-gray-300 mt-3 pt-3">
                            <div class="flex justify-between items-center font-semibold text-gray-900">
                                <span>Total:</span>
                                <span id="printModalTotal" class="text-lg">₱0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                    <button onclick="window.app.closePrintModal()" type="button"
                        class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium text-sm">
                        Cancel
                    </button>
                    <button onclick="window.app.confirmPrintBill()" type="button"
                        class="px-4 py-2 text-white bg-blue-600 hover:bg-blue-700 rounded-lg font-medium text-sm">
                        Print
                    </button>
                </div>
            </div>
        </div>
        <div id="processPaymentModal"
            class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Confirm Payment</h3>
                </div>
                <div class="px-6 py-4 max-h-96 overflow-y-auto">
                    <div class="mb-4">
                        <p class="text-sm text-gray-600">Customer</p>
                        <p class="font-semibold text-gray-900" id="confirmModalCustomerName">Walk-in Customer</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 mb-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Order Summary</h4>
                        <div id="confirmModalOrderItems" class="space-y-2 mb-3">
                        </div>
                        <div class="border-t border-gray-300 pt-3 space-y-1 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal:</span>
                                <span id="confirmModalSubtotal" class="font-mono">₱0.00</span>
                            </div>
                            <div id="confirmModalAdvancePayment" class="flex justify-between hidden">
                                <span class="text-gray-600">Advance Payment:</span>
                                <span id="confirmModalAdvanceAmount" class="font-mono">₱0.00</span>
                            </div>
                            <div id="confirmModalDiscount" class="flex justify-between hidden">
                                <span class="text-gray-600">Discount:</span>
                                <span id="confirmModalDiscountAmount" class="font-mono text-red-600">-₱0.00</span>
                            </div>
                            <div
                                class="flex justify-between items-center font-semibold text-base pt-2 border-t border-gray-400">
                                <span>Total Amount Due:</span>
                                <span id="confirmModalTotal" class="text-lg">₱0.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4 bg-blue-50">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Payment Details</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Cash Received:</span>
                                <span id="confirmModalCashReceived" class="font-mono font-semibold">₱0.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Change:</span>
                                <span id="confirmModalChange"
                                    class="font-mono font-semibold text-green-600">₱0.00</span>
                            </div>
                        </div>
                    </div>

                    <div id="confirmModalDiscountedCustomers" class="mt-4 hidden">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Discounted Customers</h4>
                        <div id="confirmModalCustomersList" class="space-y-2 text-xs">
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                    <button onclick="window.app.closeProcessPaymentModal()" type="button"
                        class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium text-sm">
                        Cancel
                    </button>
                    <button onclick="window.app.confirmProcessPayment()" type="button"
                        class="px-6 py-2 text-white bg-blue-600 hover:bg-blue-700 rounded-lg font-medium text-sm">
                        Confirm & Print Receipt
                    </button>
                </div>
            </div>
        </div>
        <div id="toast"
            class="fixed top-5 right-5 z-50 hidden opacity-0 px-4 py-3 rounded-lg shadow-md bg-red-600 text-white text-sm font-medium transition-opacity duration-300 ease-in-out">
            <span id="toast-message">Something went wrong</span>
        </div>
    </div>
    @livewireScripts
</body>
<script>
    window.menuPriceData = @json($menuData);
    window.menuPricesMap = {};
    const CASHIER_NAME = @json(auth()->user()->firstname . ' ' . auth()->user()->lastname);

    if (Array.isArray(window.menuPriceData)) {
        window.menuPriceData.forEach(item => {
            if (item && item.menu_item) {
                window.menuPricesMap[item.menu_item] = {
                    regular: parseFloat(item.regular_price) || 0,
                    student_percent: item.student_percent ? parseFloat(item.student_percent) : null,
                    govt_percent: item.govt_percent ? parseFloat(item.govt_percent) : null,
                    senior_percent: item.senior_percent ? parseFloat(item.senior_percent) : null,
                    pwd_percent: item.pwd_percent ? parseFloat(item.pwd_percent) : null,
                    has_discount: item.has_discount === 1 || item.has_discount === true
                };
            }
        });
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
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => {
                    this.initializeElements();
                    this.initializeEventListeners();
                    this.setupTableClickEvents();
                });
            } else {
                this.initializeElements();
                this.initializeEventListeners();
                this.setupTableClickEvents();
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
        openTablePaymentModal(reservationId, tableNumber) {
            this.currentModalType = 'table';
            this.currentReservationId = reservationId;

            if (this.elements.tablePaymentModal) {
                this.elements.tablePaymentModal.classList.remove('hidden');
                this.fetchOrderData(reservationId, tableNumber);
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

        fetchOrderData(reservationId, tableNumber) {
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
                        this.showToast('No order/orders found.', 'error');
                        this.populateBasicInfo(reservationId);
                    }
                })
                .catch(error => {
                    this.showToast('Failed to load data', 'error');
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
            if (reservationData.orders && reservationData.orders.length > 0) {
                this.currentReservationData = reservationData;
                this.processPayment(reservationData);
            } else {
                if (paymentItemsList) {
                    paymentItemsList.innerHTML = '<div class="text-center text-gray-500 py-4">No orders placed yet</div>';
                }
                this.updatePaymentSummaryBreakdown();
            }
            this.setupCashReceivedInput();
        }

        initializeImagePopup() {
            const img = document.getElementById("paymentProof");
            if (!img) return;

            img.addEventListener("click", () => {
                if (!img.src || img.style.display === "none") return;

                const overlay = document.createElement("div");
                overlay.className = "fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50";
                overlay.style.cursor = "zoom-out";

                const bigImg = document.createElement("img");
                bigImg.src = img.src;
                bigImg.className =
                    "w-[300px] h-[300px] object-contain rounded-lg shadow-lg border-4 border-white";

                overlay.appendChild(bigImg);
                document.body.appendChild(overlay);

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
                const qty = parseInt(order.quantity) || 1;
                const price = parseFloat(order.regular_price || 0);
                for (let i = 0; i < qty; i++) {
                    subtotal += price;
                    const orderLine = document.createElement("div");
                    const isMainMenuItem = this.isMainCategoryItemWithDiscount(itemName, menuItemData);
                    if (isMainMenuItem) {
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
                                data-item-index="${itemCounter}" 
                                id="discount-select-${itemCounter}">
                            ${this.getDiscountOptions(menuItemData)}
                        </select>
                    </div>
                    <span class="w-24 text-right font-medium item-total" data-item-index="${itemCounter}">₱${price.toFixed(2)}</span>
                `;
                        paymentItemsList.appendChild(orderLine);
                    } else {
                        orderLine.className = "hidden";
                        orderLine.innerHTML = `<span class="item-total" data-item-index="${itemCounter}">₱${price.toFixed(2)}</span>`;
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

            const mainMenuItems = ['Samgyupsal', 'HotPot', 'Fusion'];
            const itemGroups = {};
            const mainMenuEntries = [];
            let itemCounter = 0;

            const discountSelectMap = {};
            document.querySelectorAll('.discount-type-select').forEach(select => {
                const itemIndex = parseInt(select.getAttribute('data-item-index'));
                if (!isNaN(itemIndex)) {
                    discountSelectMap[itemIndex] = select;
                }
            });

            const itemTotalMap = {};
            document.querySelectorAll('.item-total').forEach(total => {
                const itemIndex = parseInt(total.getAttribute('data-item-index'));
                if (!isNaN(itemIndex)) {
                    itemTotalMap[itemIndex] = parseFloat(total.textContent.replace('₱', ''));
                }
            });

            this.currentReservationData.orders.forEach((order, orderIndex) => {
                const itemName = order.order_name;
                const price = parseFloat(order.regular_price || 0);
                const qty = parseInt(order.quantity) || 1;

                for (let i = 0; i < qty; i++) {
                    let finalPrice = itemTotalMap[itemCounter] || price;

                    let discountInfo = '';
                    let discountType = 'none';

                    const select = discountSelectMap[itemCounter];
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
                                discountType: discountType,
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
                ${hasDiscount ? `<button onclick="window.app.openCustomerInfoModal('${baseItemName}', ${entry.index}, '${discountType}')" 
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
                ${hasDiscount ? `<button onclick="window.app.openCustomerInfoModal('${baseItemName}', ${group.index}, '${discountType}')" 
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

        isMainCategoryItemWithDiscount(itemName, menuItemData) {
            return menuItemData && menuItemData.has_discount === true;
        }

        getDiscountOptions(menuData) {
            let options = '<option value="none">No Discount</option>';

            if (menuData.student_percent > 0)
                options += '<option value="student">SD</option>';

            if (menuData.govt_percent > 0)
                options += '<option value="govt_employee">GED</option>';

            if (menuData.senior_percent > 0 || menuData.pwd_percent > 0)
                options += '<option value="pwd_senior">PWD/SC</option>';

            return options;
        }
        calculateSingleItemDiscount(selectElement) {
            const itemPrice = parseFloat(selectElement.dataset.itemPrice);
            const discountType = selectElement.value;
            const itemName = selectElement.dataset.itemName;
            const menuItemData = window.menuPricesMap[itemName] || {};

            let discountPercent = 0;

            switch (discountType) {
                case 'student':
                    discountPercent = menuItemData.student_percent || 0;
                    break;
                case 'govt_employee':
                    discountPercent = menuItemData.govt_percent || 0;
                    break;
                case 'pwd_senior':
                    discountPercent = menuItemData.pwd_percent || menuItemData.senior_percent;
                    break;
                default:
                    discountPercent = 0;
            }
            let discountedPrice = itemPrice * (1 - (discountPercent / 100));
            const decimalPart = discountedPrice - Math.floor(discountedPrice);
            if (decimalPart >= 0.5) {
                discountedPrice = Math.ceil(discountedPrice);
            } else {
                discountedPrice = Math.floor(discountedPrice);
            }
            const discountAmount = itemPrice - discountedPrice;
            selectElement.dataset.discountAmount = discountAmount.toFixed(2);

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
            const itemGroups = {};
            const mainMenuEntries = [];
            let itemCounter = 0;
            const discountSelectMap = {};
            document.querySelectorAll('.discount-type-select').forEach(select => {
                const itemIndex = parseInt(select.getAttribute('data-item-index'));
                if (!isNaN(itemIndex)) {
                    discountSelectMap[itemIndex] = select;
                }
            });

            const itemTotalMap = {};
            document.querySelectorAll('.item-total').forEach(total => {
                const itemIndex = parseInt(total.getAttribute('data-item-index'));
                if (!isNaN(itemIndex)) {
                    itemTotalMap[itemIndex] = parseFloat(total.textContent.replace('₱', ''));
                }
            });
            this.currentReservationData.orders.forEach((order, orderIndex) => {
                const itemName = order.order_name;
                const price = parseFloat(order.regular_price || 0);
                const qty = parseInt(order.quantity) || 1;

                const menuItemData = window.menuPricesMap?.[itemName] || {};
                const isDiscountableItem = menuItemData.has_discount === true;

                for (let i = 0; i < qty; i++) {
                    let finalPrice = itemTotalMap[itemCounter] || price;
                    let discountInfo = '';
                    let discountType = 'none';

                    const select = discountSelectMap[itemCounter];
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

                    totalAmount += finalPrice;

                    if (isDiscountableItem) {
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
                                discountType: discountType,
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

                breakdownHtml += `
        <div class="flex justify-between items-center text-sm py-2">
            <span class="flex-1 pr-2">${entry.name}</span>
            <span class="w-20 text-right font-mono">₱${entry.price.toFixed(2)}</span>
            <div class="w-12 flex justify-center ml-2">
                ${hasDiscount ? `<button onclick="window.app.openCustomerInfoModal('${baseItemName}', ${entry.index}, '${discountType}')" 
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

                breakdownHtml += `
        <div class="flex justify-between items-center text-sm py-2">
            <span class="flex-1 pr-2">${displayName}</span>
            <span class="w-20 text-right font-mono">₱${group.totalPrice.toFixed(2)}</span>
            <div class="w-12 flex justify-center ml-2">
                ${hasDiscount ? `<button onclick="window.app.openCustomerInfoModal('${baseItemName}', ${group.index}, '${discountType}')" 
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

            const subtotalEl = document.getElementById('summary_subtotal');
            const advanceEl = document.getElementById('summary_advance');
            const amountDueEl = document.getElementById('summary_amount_due');
            const advanceRow = document.getElementById('advance_payment_row');

            if (this.currentReservationData) {
                const subtotal = parseFloat(document.getElementById('payment_total').textContent.replace('₱', '').replace(',', ''));
                const advancePayment = this.currentReservationData.order_type === 'walkin' ? 0 : parseFloat(this.currentReservationData.advance_payment || 0);
                const amountDue = Math.max(0, subtotal - advancePayment);

                if (subtotalEl) subtotalEl.textContent = '₱' + subtotal.toFixed(2);
                if (advanceEl) advanceEl.textContent = '₱' + advancePayment.toFixed(2);
                if (amountDueEl) amountDueEl.textContent = '₱' + amountDue.toFixed(2);

                if (advanceRow) {
                    advanceRow.style.display = advancePayment > 0 ? 'flex' : 'none';
                }

                const cashInput = document.getElementById('cashReceived');
                if (cashInput) {
                    cashInput.setAttribute('min', amountDue.toFixed(2));
                }
            }
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

            const key = `${itemName}_${itemIndex}`;
            const savedData = this.tempCustomerData[key];
            const savedIdNumber = savedData ? savedData.id_number : '';

            const modalHtml = `
    <div id="customerInfoModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-96 max-w-sm mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Customer Information</h3>
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
                    <label for="customerIdNumber" class="block text-sm font-medium text-gray-700 mb-1">
                        ID Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="customerIdNumber" name="id_number" required maxlength="12" value="${savedIdNumber}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md"
                        placeholder="Enter ID number"
                        onkeypress="return /[0-9]/i.test(event.key)">
                    <div id="idNumberError" class="text-red-500 text-xs mt-1 hidden">ID number is required</div>
                </div>

                <div>
                    <label for="customerName" class="block text-sm font-medium text-gray-700 mb-1">
                        Customer Name <span class="text-gray-400">(Optional)</span>
                    </label>
                    <input type="text" id="customerName" name="name" value="${savedData ? savedData.name || '' : ''}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md"
                        placeholder="Enter customer name (optional)"
                        oninput="this.value = this.value.replace(/[0-9]/g, '')">
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
            const form = document.getElementById('customerInfoForm');
            const cancelBtn = document.getElementById('cancelCustomerInfo');

            if (cancelBtn) {
                cancelBtn.addEventListener('click', () => {
                    if (modal) modal.remove();
                });
            }

            const checkCustomer = async (idNumber, idType, customerType) => {
                const idNumberError = document.getElementById('idNumberError');
                const submitBtn = form?.querySelector('button[type="submit"]');

                try {
                    const response = await fetch('/cashier/check-customer', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            id_number: idNumber,
                            id_type: idType,
                            discount_type: customerType
                        })
                    });

                    const data = await response.json();

                    if (!idNumberError || !submitBtn) {
                        console.error('Required elements not found');
                        return false;
                    }

                    const currentDiscountType = form.querySelector('input[name="customer_type"]')?.value;
                    const currentItemIndex = form.querySelector('input[name="item_index"]')?.value;
                    const currentItemName = form.querySelector('input[name="item_name"]')?.value;

                    if (!currentDiscountType || !currentItemIndex || !currentItemName) {
                        console.error('Missing form data');
                        return false;
                    }

                    const usedForSameDiscount = Object.entries(this.tempCustomerData).some(([key, data]) => {
                        const [itemName, index] = key.split('_');
                        return data.id_number === idNumber &&
                            data.customer_type === currentDiscountType &&
                            (itemName !== currentItemName || index !== currentItemIndex);
                    });

                    if (usedForSameDiscount) {
                        idNumberError.textContent = 'This ID is already used for this discount type in current transaction';
                        idNumberError.classList.remove('hidden');
                        submitBtn.disabled = true;
                        return false;
                    }

                    if (!data.can_use_discount) {
                        idNumberError.textContent = data.message || 'This ID has already been used today';
                        idNumberError.classList.remove('hidden');
                        submitBtn.disabled = true;
                        return false;
                    }

                    if (data.exists && data.customer_name) {
                        const customerNameInput = document.getElementById('customerName');
                        if (customerNameInput) {
                            customerNameInput.value = data.customer_name;
                        }
                    }

                    idNumberError.classList.add('hidden');
                    submitBtn.disabled = false;
                    return true;

                } catch (error) {
                    console.error('Error checking customer:', error);
                    if (idNumberError && submitBtn) {
                        idNumberError.textContent = 'Error validating ID number';
                        idNumberError.classList.remove('hidden');
                        submitBtn.disabled = true;
                    }
                    return false;
                }
            };

            if (form) {
                const idNumberInput = document.getElementById('customerIdNumber');
                if (idNumberInput) {
                    let timeout = null;
                    idNumberInput.addEventListener('input', (e) => {
                        clearTimeout(timeout);
                        const value = e.target.value;
                        const idType = document.getElementById('customerIdType')?.value;
                        const customerType = form.querySelector('input[name="customer_type"]')?.value;

                        if (!idType || !customerType) {
                            console.error('Missing form values');
                            return;
                        }

                        if (value.length < 12) {
                            const idNumberError = document.getElementById('idNumberError');
                            if (idNumberError) {
                                idNumberError.textContent = 'ID number must be 12 characters';
                                idNumberError.classList.remove('hidden');
                            }
                            const submitBtn = form.querySelector('button[type="submit"]');
                            if (submitBtn) {
                                submitBtn.disabled = true;
                            }
                            return;
                        }

                        timeout = setTimeout(() => {
                            checkCustomer(value, idType, customerType);
                        }, 500);
                    });
                }

                form.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const idNumber = document.getElementById('customerIdNumber')?.value;
                    const idType = document.getElementById('customerIdType')?.value;
                    const customerType = form.querySelector('input[name="customer_type"]')?.value;

                    if (!idNumber || !idType || !customerType) {
                        console.error('Missing form data on submit');
                        return;
                    }

                    if (idNumber.length < 12) {
                        return;
                    }

                    const isValid = await checkCustomer(idNumber, idType, customerType);
                    if (!isValid) {
                        return;
                    }

                    const customerData = {
                        id_number: idNumber,
                        name: document.getElementById('customerName')?.value || '',
                        id_type: idType,
                        item_name: form.querySelector('input[name="item_name"]')?.value,
                        item_index: form.querySelector('input[name="item_index"]')?.value,
                        customer_type: customerType
                    };

                    if (this.saveCustomerInfoTemporarily(customerData)) {
                        if (modal) modal.remove();
                    }
                });
            }
        }
        saveCustomerInfoTemporarily(customerData) {
            if (!customerData ||
                !customerData.id_number ||
                customerData.id_number === '' ||
                customerData.id_number === null ||
                customerData.id_number === undefined ||
                customerData.id_number.trim() === '' ||
                customerData.id_number.trim().length === 0) {

                this.showToast('Cannot save empty ID number', 'error');
                return false;
            }
            const key = `${customerData.item_name}_${customerData.item_index}`;
            this.tempCustomerData[key] = {
                id_number: customerData.id_number.trim(),
                name: customerData.name ? customerData.name.trim() : '',
                id_type: customerData.id_type,
                item_name: customerData.item_name,
                item_index: parseInt(customerData.item_index),
                customer_type: customerData.customer_type
            };

            return true;
        }

        submitPayment(event) {
            this.showProcessPaymentModal(event);
        }

        setupCashReceivedInput() {
            const cashInput = document.getElementById('cashReceived');
            const changeDisplay = document.getElementById('change_amount');
            const cashError = document.getElementById('cashError');
            const processBtn = document.getElementById('processPaymentBtn');

            if (!cashInput) return;

            cashInput.addEventListener('input', () => {
                const cashReceived = parseFloat(cashInput.value) || 0;
                const amountDue = parseFloat(document.getElementById('summary_amount_due').textContent.replace('₱', '').replace(',', '')) || 0;

                if (cashReceived >= amountDue && amountDue > 0) {
                    const change = cashReceived - amountDue;
                    changeDisplay.textContent = '₱' + change.toFixed(2);
                    cashError.classList.add('hidden');
                    processBtn.disabled = false;
                } else {
                    changeDisplay.textContent = '₱0.00';
                    processBtn.disabled = true;
                    if (cashReceived > 0 && cashReceived < amountDue) {
                        cashError.classList.remove('hidden');
                    } else {
                        cashError.classList.add('hidden');
                    }
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
            this.showProcessingModal();

            const actualAdvancePayment = currentReservationData.order_type === 'walkin' ? 0 : advancePayment;
            const actualFinalTotal = currentReservationData.order_type === 'walkin' ? subtotal : finalTotal;


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
                    order_id: order.order_id,
                    order_name: order.order_name,
                    quantity: parseInt(order.quantity) || 1,
                    price: parseFloat(order.order_price || order.unit_price || order.regular_price || 0)
                };
            });

            const paymentData = {
                reservation_id: currentReservationData.reservation_id,
                order_type: currentReservationData.order_type,
                subtotal: subtotal,
                advance_payment: actualAdvancePayment,
                total: actualFinalTotal,
                orders: ordersData,
                discounted_persons: discountedPersons,
                customer_data: allCustomerData,
                cash_received: cashReceived,
                change_given: change
            };

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
                        this.printFinalReceipt(
                            actualFinalTotal,
                            subtotal,
                            actualAdvancePayment,
                            cashReceived,
                            change,
                            currentReservationData,
                            data.transaction_no
                        );

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
                    this.showToast(error.message || "Payment processing failed. Please try again.", "error");
                });
        }

        printFinalReceipt(finalTotal, subtotal, advancePayment, cashReceived, change, currentReservationData, transactionNo) {
            const today = new Date();
            const dateStr = today.toLocaleDateString('en-PH', { year: 'numeric', month: '2-digit', day: '2-digit' });
            const timeStr = today.toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit', hour12: true });

            const actualAdvancePayment = currentReservationData.order_type === 'walkin' ? 0 : advancePayment;

            const vatableSales = (subtotal / 1.12).toFixed(2);
            const vat = (subtotal - vatableSales).toFixed(2);

            let discountAmount = 0;
            document.querySelectorAll('.discount-type-select').forEach(select => {
                const discountValue = parseFloat(select.dataset.discountAmount || 0);
                discountAmount += discountValue;
            });
            discountAmount = parseFloat(discountAmount.toFixed(2));

            let orderItemsHTML = '';
            const itemGroups = {};
            let itemIndex = 0;

            currentReservationData.orders.forEach(order => {
                const itemName = order.order_name;
                const qty = parseInt(order.quantity) || 1;
                const regularPrice = parseFloat(order.regular_price || 0);

                for (let i = 0; i < qty; i++) {
                    const itemTotalElements = document.querySelectorAll('.item-total');
                    let finalPrice = regularPrice;

                    if (itemIndex < itemTotalElements.length) {
                        finalPrice = parseFloat(itemTotalElements[itemIndex].textContent.replace('₱', ''));
                    }

                    const groupKey = `${itemName}_${finalPrice.toFixed(2)}`;
                    if (!itemGroups[groupKey]) {
                        itemGroups[groupKey] = {
                            itemName: itemName,
                            quantity: 0,
                            unitPrice: finalPrice,
                            totalAmount: 0
                        };
                    }
                    itemGroups[groupKey].quantity += 1;
                    itemGroups[groupKey].totalAmount += finalPrice;
                    itemIndex++;
                }
            });

            Object.keys(itemGroups).forEach(groupKey => {
                const item = itemGroups[groupKey];
                const itemDescription = item.quantity > 1 ? `${item.itemName} x${item.quantity}` : item.itemName;
                orderItemsHTML += `
        <tr>
            <td class="item-desc">${itemDescription}</td>
            <td class="item-price">${item.unitPrice.toFixed(2)}</td>
            <td class="item-amount">${item.totalAmount.toFixed(2)}</td>
        </tr>`;
            });

            const printHTML = `
    <!DOCTYPE html>
    <html>
    <head>
        <title>Receipt</title>
        <style>
            @media print { 
                @page { 
                    size: 48mm 210mm;
                    margin: 0; 
                } 
                body { 
                    margin: 0; 
                    padding: 2mm; 
                }
            }
            body { 
                font-family: Arial, Helvetica, sans-serif; 
                font-size: 7pt;
                width: 48mm;
                margin: 0 auto; 
                padding: 3mm 1mm;
                line-height: 1.3;
            }
            .center { text-align: center; }
            .bold { font-weight: bold; }
            .separator { 
                border-top: 1px solid #000; 
                margin: 2mm 0; 
            }
            .header {
                font-size: 8pt;
                font-weight: bold;
                margin-bottom: 1mm;
            }
            .subheader {
                font-size: 7pt;
                margin-bottom: 1mm;
            }
            .title {
                font-size: 8pt;
                font-weight: bold;
                margin: 2mm 0;
            }
            .info-row {
                display: flex;
                justify-content: space-between;
                font-size: 7pt;
                margin: 1mm 0;
            }
            table { 
                width: 100%; 
                border-collapse: collapse;
                margin: 2mm 0;
            }
            th {
                font-size: 7pt;
                font-weight: bold;
                padding: 1mm 0;
                border-top: 1px solid #000;
                border-bottom: 1px solid #000;
                text-align: left;
            }
            td { 
                padding: 1mm 0;
                font-size: 6pt;
                vertical-align: top;
            }
            .item-desc {
                width: 50%;
                text-align: left;
            }
            .item-price {
                width: 25%;
                text-align: right;
            }
            .item-amount {
                width: 25%;
                text-align: right;
            }
            .summary {
                font-size: 7pt;
                margin-top: 2mm;
            }
            .summary-row {
                display: flex;
                justify-content: space-between;
                margin: 1mm 0;
            }
            .total-row {
                font-weight: bold;
                font-size: 7pt;
                border-top: 1px solid #000;
                padding-top: 2mm;
                margin-top: 2mm;
            }
            .footer {
                font-size: 6pt;
                margin-top: 3mm;
            }
        </style>
    </head>
    <body>
        <div class="center header">Jeongol Izakaya</div>
        <div class="center header">Hotpot & Grill</div>
        <div class="center subheader">Koronadal City,</div>
        <div class="center subheader">South Cotabato, Philippines</div>
        <div class="center subheader">VAT Reg. TIN</div>
        <div class="center subheader">295-774-127-00003</div>
        <div class="center title">SALES INVOICE</div>
        
        <div class="separator"></div>
        
        <div class="info-row">
            <span>Date:</span>
            <span>${dateStr}</span>
        </div>
        <div class="info-row">
            <span>Time:</span>
            <span>${timeStr}</span>
        </div>
        <div class="info-row">
            <span>Transaction No:</span>
            <span class="bold">${transactionNo || 'N/A'}</span>
        </div>
        <div class="info-row">
            <span>Cashier:</span>
            <span>${CASHIER_NAME}</span>
        </div>
        
        <div class="separator"></div>
        
        <table>
            <thead>
                <tr>
                    <th class="item-desc">ITEM</th>
                    <th class="item-price">PRICE</th>
                    <th class="item-amount">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                ${orderItemsHTML}
            </tbody>
        </table>
        
        <div class="separator"></div>
        
        <div class="summary">
            <div class="summary-row">
                <span>VATable Sales:</span>
                <span>${vatableSales}</span>
            </div>
            <div class="summary-row">
                <span>VAT (12%):</span>
                <span>${vat}</span>
            </div>
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>${subtotal.toFixed(2)}</span>
            </div>
            ${actualAdvancePayment > 0 ? `
            <div class="summary-row">
                <span>Advance Payment:</span>
                <span>${actualAdvancePayment.toFixed(2)}</span>
            </div>
            ` : ''}
            ${discountAmount > 0 ? `
            <div class="summary-row">
                <span>Discount:</span>
                <span>${discountAmount.toFixed(2)}</span>
            </div>
            ` : ''}
            <div class="summary-row total-row">
                <span>TOTAL:</span>
                <span>${finalTotal.toFixed(2)}</span>
            </div>
        </div>
        
        <div class="separator"></div>
        
        <div class="summary">
            <div class="summary-row">
                <span>Cash:</span>
                <span>${cashReceived.toFixed(2)}</span>
            </div>
            <div class="summary-row">
                <span>Change:</span>
                <span>${change.toFixed(2)}</span>
            </div>
        </div>
        
        <div class="separator"></div>
        
        <div class="center footer">Thank you for dining with us!</div>
        <div class="center footer">Please come again!</div>
    </body>
    </html>
    `;

            const printWindow = window.open('', '_blank', 'width=400,height=600');
            if (!printWindow) {
                this.showToast('Unable to open print window. Please check popup settings.', 'error');
                return;
            }

            printWindow.document.write(printHTML);
            printWindow.document.close();

            printWindow.onload = function () {
                setTimeout(() => {
                    printWindow.print();
                    setTimeout(() => printWindow.close(), 100);
                }, 500);
            };
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
                <td style="padding: 3px 2px; font-size: 10px; text-align: left; width: 55%;">${displayName}</td>
                <td style="padding: 3px 2px; font-size: 10px; text-align: center; width: 15%;">${quantity}</td>
                <td style="padding: 3px 2px; font-size: 10px; text-align: right; width: 30%;">${price}</td>
            </tr>`;
                }
            });

            const total = totalElement.textContent.trim();
            const printHTML = `<!DOCTYPE html>
    <html>
    <head>
        <title>Bill</title>
        <style>
            @media print { 
                @page { size: 48mm 210mm; margin: 0; } 
                body { margin: 0; padding: 2mm; } 
            }
            body { 
                font-family: Arial, Helvetica, sans-serif; 
                font-size: 7pt;
                width: 48mm;
                margin: 0 auto; 
                padding: 3mm 1mm;
                line-height: 1.3;
            }
            .center { text-align: center; }
            .bold { font-weight: bold; }
            .separator { 
                border-top: 1px solid #000; 
                margin: 2mm 0; 
            }
            .header {
                font-size: 8pt;
                font-weight: bold;
                margin-bottom: 1mm;
            }
            .address {
                font-size: 7pt;
                margin-bottom: 1mm;
            }
            .title {
                font-size: 8pt;
                font-weight: bold;
                margin: 2mm 0;
            }
            .info {
                font-size: 7pt;
                margin-bottom: 1mm;
            }
            table { 
                width: 100%; 
                border-collapse: collapse;
                margin: 2mm 0;
            }
            th {
                font-size: 7pt;
                font-weight: bold;
                padding: 1mm 0;
                border-bottom: 1px solid #000;
                text-align: left;
            }
            td { 
                padding: 1mm 0;
                font-size: 6pt;
            }
            .total-row {
                font-size: 7pt;
                font-weight: bold;
                padding-top: 2mm;
            }
            .footer {
                font-size: 6pt;
                margin-top: 3mm;
            }
        </style>
    </head>
    <body>
        <div class="center header">Jeongol Izakaya</div>
        <div class="center address">Koronadal City, South Cotabato.</div>
        <div class="center title">BILL</div>
        <div class="separator"></div>
        <div class="info">Customer: ${customerName}</div>
        <div class="separator"></div>
        <table>
            <thead>
                <tr>
                    <th style="width: 55%;">ITEM</th>
                    <th style="width: 15%; text-align: center;">QTY</th>
                    <th style="width: 30%; text-align: right;">AMOUNT</th>
                </tr>
            </thead>
            <tbody>${itemsHTML}</tbody>
        </table>
        <div class="separator"></div>
        <div class="center total-row">TOTAL: ${total}</div>
    </body>
    </html>`;

            const printWindow = window.open('', '_blank', 'width=400,height=600');
            if (!printWindow) {
                this.showToast('Unable to open print window. Please check popup settings.', 'error');
                return;
            }

            printWindow.document.write(printHTML);
            printWindow.document.close();

            printWindow.onload = function () {
                setTimeout(() => {
                    printWindow.print();
                    setTimeout(() => printWindow.close(), 100);
                }, 500);
            };
        }

        showPrintModal() {
            const customerName = document.getElementById('payment_customer_name')?.value || 'Walk-in Customer';
            const paymentSummaryDiv = document.querySelector('.border.rounded-lg.p-4.bg-gray-50 .space-y-2');
            const totalElement = document.getElementById('payment_total');

            if (!paymentSummaryDiv || !totalElement) {
                this.showToast('No payment summary available', 'error');
                return;
            }

            const summaryItems = paymentSummaryDiv.querySelectorAll('.flex.justify-between.items-center.text-sm.py-2');
            if (summaryItems.length === 0) {
                this.showToast('No items found in payment summary', 'error');
                return;
            }

            document.getElementById('printModalCustomerName').textContent = customerName;

            const orderItemsContainer = document.getElementById('printModalOrderItems');
            orderItemsContainer.innerHTML = '';

            summaryItems.forEach(item => {
                const itemNameElement = item.querySelector('.flex-1.pr-2');
                const priceElement = item.querySelector('.w-20.text-right.font-mono');

                if (itemNameElement && priceElement) {
                    const itemName = itemNameElement.textContent.trim();
                    const price = priceElement.textContent.trim();

                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'flex justify-between items-center text-sm py-1';
                    itemDiv.innerHTML = `
                <span class="text-gray-700">${itemName}</span>
                <span class="font-mono text-gray-900">${price}</span>
            `;
                    orderItemsContainer.appendChild(itemDiv);
                }
            });

            document.getElementById('printModalTotal').textContent = totalElement.textContent.trim();

            document.getElementById('printBillModal').classList.remove('hidden');
        }

        closePrintModal() {
            document.getElementById('printBillModal').classList.add('hidden');
        }

        confirmPrintBill() {
            this.closePrintModal();
            this.printBill();
        }

        showProcessPaymentModal(event) {
            const currentReservationData = this.currentReservationData;

            if (!currentReservationData) {
                this.showToast("No reservation data available", "error");
                return;
            }

            const totalText = document.getElementById("payment_total").textContent;
            const subtotal = parseFloat(totalText.replace('₱', '').replace(',', ''));
            const advancePayment = currentReservationData.order_type === 'walkin' ? 0 : parseFloat(currentReservationData.advance_payment || 0);
            const finalTotal = Math.max(0, subtotal - advancePayment);

            const cashReceived = parseFloat(document.getElementById('cashReceived').value) || 0;

            if (cashReceived < finalTotal) {
                this.showToast("Insufficient cash amount", "error");
                return;
            }

            const discountSelects = document.querySelectorAll('.discount-type-select');
            let discountedItems = [];
            let hasActiveDiscounts = false;

            discountSelects.forEach((select, index) => {
                if (select.value !== 'none') {
                    hasActiveDiscounts = true;
                    let itemName = select.dataset.itemName || '';
                    if (itemName) {
                        discountedItems.push({
                            index: index,
                            itemName: itemName,
                            discountType: select.value
                        });
                    }
                }
            });

            if (hasActiveDiscounts) {
                let itemsWithoutValidInfo = [];

                discountedItems.forEach(item => {
                    const key = `${item.itemName}_${item.index}`;
                    const customerData = this.tempCustomerData[key];

                    if (!customerData || !customerData.id_number || customerData.id_number.trim() === '') {
                        itemsWithoutValidInfo.push(item.itemName);
                    }
                });

                if (itemsWithoutValidInfo.length > 0) {
                    this.showToast(`ID number is required for discounted items.`, "error");
                    return;
                }
            }

            this.pendingPaymentData = {
                finalTotal,
                subtotal,
                advancePayment,
                cashReceived,
                change: cashReceived - finalTotal,
                currentReservationData,
                hasActiveDiscounts,
                discountedItems
            };

            this.populateProcessPaymentModal();
        }
        populateProcessPaymentModal() {
            const data = this.pendingPaymentData;
            const customerName = document.getElementById('payment_customer_name')?.value || 'Walk-in Customer';
            document.getElementById('confirmModalCustomerName').textContent = customerName;
            const orderItemsContainer = document.getElementById('confirmModalOrderItems');
            orderItemsContainer.innerHTML = '';
            const paymentSummaryDiv = document.querySelector('.border.rounded-lg.p-4.bg-gray-50 .space-y-2');
            const summaryItems = paymentSummaryDiv.querySelectorAll('.flex.justify-between.items-center.text-sm.py-2');

            summaryItems.forEach(item => {
                const itemNameElement = item.querySelector('.flex-1.pr-2');
                const priceElement = item.querySelector('.w-20.text-right.font-mono');

                if (itemNameElement && priceElement) {
                    const itemName = itemNameElement.textContent.trim();
                    const price = priceElement.textContent.trim();

                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'flex justify-between items-center text-sm py-1';
                    itemDiv.innerHTML = `
                <span class="text-gray-700">${itemName}</span>
                <span class="font-mono text-gray-900">${price}</span>
            `;
                    orderItemsContainer.appendChild(itemDiv);
                }
            });
            document.getElementById('confirmModalSubtotal').textContent = `₱${data.subtotal.toFixed(2)}`;
            if (data.advancePayment > 0) {
                document.getElementById('confirmModalAdvancePayment').classList.remove('hidden');
                document.getElementById('confirmModalAdvanceAmount').textContent = `₱${data.advancePayment.toFixed(2)}`;
            } else {
                document.getElementById('confirmModalAdvancePayment').classList.add('hidden');
            }
            let discountAmount = 0;
            document.querySelectorAll('.discount-type-select').forEach(select => {
                const discountValue = parseFloat(select.dataset.discountAmount || 0);
                discountAmount += discountValue;
            });

            if (discountAmount > 0) {
                document.getElementById('confirmModalDiscount').classList.remove('hidden');
                document.getElementById('confirmModalDiscountAmount').textContent = `-₱${discountAmount.toFixed(2)}`;
            } else {
                document.getElementById('confirmModalDiscount').classList.add('hidden');
            }

            document.getElementById('confirmModalTotal').textContent = `₱${data.finalTotal.toFixed(2)}`;
            document.getElementById('confirmModalCashReceived').textContent = `₱${data.cashReceived.toFixed(2)}`;
            document.getElementById('confirmModalChange').textContent = `₱${data.change.toFixed(2)}`;
            if (data.hasActiveDiscounts && data.discountedItems.length > 0) {
                const customersListContainer = document.getElementById('confirmModalCustomersList');
                customersListContainer.innerHTML = '';

                data.discountedItems.forEach(item => {
                    const key = `${item.itemName}_${item.index}`;
                    const customerInfo = this.tempCustomerData[key];

                    if (customerInfo && customerInfo.id_number) {
                        const customerDiv = document.createElement('div');
                        customerDiv.className = 'p-2 bg-gray-100 rounded border border-gray-300';
                        customerDiv.innerHTML = `
                    <div class="font-semibold">${customerInfo.name || 'N/A'}</div>
                    <div class="text-gray-600">ID: ${customerInfo.id_number}</div>
                    <div class="text-gray-600">Type: ${item.discountType.toUpperCase()}</div>
                    <div class="text-gray-600">Item: ${item.itemName}</div>
                `;
                        customersListContainer.appendChild(customerDiv);
                    }
                });

                document.getElementById('confirmModalDiscountedCustomers').classList.remove('hidden');
            } else {
                document.getElementById('confirmModalDiscountedCustomers').classList.add('hidden');
            }
            document.getElementById('processPaymentModal').classList.remove('hidden');
        }

        closeProcessPaymentModal() {
            document.getElementById('processPaymentModal').classList.add('hidden');
            this.pendingPaymentData = null;
        }

        confirmProcessPayment() {
            if (!this.pendingPaymentData) {
                this.showToast("Payment data not available", "error");
                return;
            }

            const data = this.pendingPaymentData;
            const allCustomerData = [];
            if (data.hasActiveDiscounts) {
                data.discountedItems.forEach(item => {
                    const key = `${item.itemName}_${item.index}`;
                    const customerInfo = this.tempCustomerData[key];

                    if (customerInfo && customerInfo.id_number && customerInfo.id_number.trim() !== '') {
                        allCustomerData.push({
                            id_number: customerInfo.id_number.trim(),
                            name: customerInfo.name ? customerInfo.name.trim() : '',
                            id_type: customerInfo.id_type,
                            item_name: customerInfo.item_name,
                            item_index: parseInt(customerInfo.item_index),
                            customer_type: customerInfo.customer_type
                        });
                    }
                });
            }
            this.closeProcessPaymentModal();
            this.processFinalPayment(
                data.finalTotal,
                data.subtotal,
                data.advancePayment,
                data.cashReceived,
                data.change,
                data.currentReservationData,
                allCustomerData
            );
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

</html>