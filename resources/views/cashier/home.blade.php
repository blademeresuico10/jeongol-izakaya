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

                            <!-- Cash Payment Section (Moved here) -->
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
                        <button onclick="window.app.printBill()" type="button"
                            class="px-6 py-2.5 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg font-semibold text-sm">
                            Print Bill
                        </button>
                        <button onclick="window.app.submitPayment(event)" type="button" id="processPaymentBtn"
                            class="px-8 py-2.5 text-white bg-blue-600 hover:bg-blue-700 rounded-lg font-semibold text-sm disabled:bg-gray-400"
                            disabled>
                            Process Payment
                        </button>
                    </div>
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

            // Setup cash input
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
            let itemCounter = 0; // ✅ This is defined
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

            // Create a map of discount selects by their item index
            const discountSelectMap = {};
            document.querySelectorAll('.discount-type-select').forEach(select => {
                const itemIndex = parseInt(select.getAttribute('data-item-index'));
                if (!isNaN(itemIndex)) {
                    discountSelectMap[itemIndex] = select;
                }
            });

            // Create a map of item totals by their item index
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
                    // Get the final price from the item total map
                    let finalPrice = itemTotalMap[itemCounter] || price;

                    // Get discount info if available
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
            let itemCounter = 0; // ✅ MAKE SURE THIS IS HERE

            // Rest of the function...
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
                    let finalPrice = itemTotalMap[itemCounter] || price; // ✅ USE itemCounter
                    let discountInfo = '';
                    let discountType = 'none';

                    const select = discountSelectMap[itemCounter]; // ✅ USE itemCounter
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
                            index: itemCounter // ✅ USE itemCounter
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
                                index: itemCounter // ✅ USE itemCounter
                            };
                        }
                        itemGroups[groupKey].count++;
                        itemGroups[groupKey].totalPrice += finalPrice;
                    }

                    itemCounter++; // ✅ INCREMENT itemCounter
                }
            });

            // Build breakdown display
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

            // --- CASH PAYMENT SECTION ---
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

                // Update min value for cash input
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
                        placeholder="Enter customer name"
                        oninput="this.value = this.value.replace(/[0-9]/g, '')">
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
            const advancePayment = currentReservationData.order_type === 'walkin' ? 0 : parseFloat(currentReservationData.advance_payment || 0);
            const finalTotal = Math.max(0, subtotal - advancePayment);

            const cashReceived = parseFloat(document.getElementById('cashReceived').value) || 0;

            if (cashReceived < finalTotal) {
                this.showToast("Insufficient cash amount", "error");
                return;
            }

            // Validate discounts
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
                let itemsWithoutValidNames = [];

                discountedItems.forEach(item => {
                    const key = `${item.itemName}_${item.index}`;
                    const customerData = this.tempCustomerData[key];

                    if (!customerData || !customerData.name || customerData.name.trim() === '') {
                        itemsWithoutValidNames.push(item.itemName);
                    }
                });

                if (itemsWithoutValidNames.length > 0) {
                    this.showToast(`Customer name is required for discounted items.`, "error");
                    return;
                }
            }

            // Collect customer data
            const allCustomerData = [];
            if (hasActiveDiscounts) {
                discountedItems.forEach(item => {
                    const key = `${item.itemName}_${item.index}`;
                    const customerInfo = this.tempCustomerData[key];

                    if (customerInfo && customerInfo.name && customerInfo.name.trim() !== '') {
                        allCustomerData.push({
                            name: customerInfo.name.trim(),
                            id_type: customerInfo.id_type,
                            item_name: customerInfo.item_name,
                            item_index: parseInt(customerInfo.item_index),
                            customer_type: customerInfo.customer_type
                        });
                    }
                });
            }

            const change = cashReceived - finalTotal;

            this.processFinalPayment(finalTotal, subtotal, advancePayment, cashReceived, change, currentReservationData, allCustomerData);
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
            this.showToast(error.message || "Payment processing failed. Please try again.", "error");
        });
}

        printFinalReceipt(finalTotal, subtotal, advancePayment, cashReceived, change, currentReservationData, transactionNo ) {
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
    </tr>
    `;
            });

            const printHTML = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Jeongol Receipt</title>
            <style>
                @media print { 
                    @page { 
                        size: 80mm auto;
                        margin: 0; 
                    } 
                    body { 
                        margin: 0; 
                        padding: 0; 
                        -webkit-print-color-adjust: exact;
                        print-color-adjust: exact;
                    }
                }
                * {
                    box-sizing: border-box;
                    -webkit-font-smoothing: none;
                    -moz-osx-font-smoothing: grayscale;
                }
                body { 
                    font-family: 'Courier New', 'Consolas', monospace;
                    font-size: 12px;
                    width: 300px;
                    max-width: 300px;
                    margin: 0;
                    padding: 10px;
                    line-height: 1.4;
                    color: #000;
                    background: #fff;
                }
                .header {
                    text-align: center;
                    border-bottom: 1px dashed #000;
                    padding-bottom: 6px;
                    margin-bottom: 6px;
                    word-wrap: break-word;
                }
                .header h3 {
                    margin: 4px 0;
                    font-size: 14px;
                    font-weight: bold;
                    letter-spacing: 0;
                }
                .header p {
                    margin: 2px 0;
                    font-size: 11px;
                    letter-spacing: 0;
                }
                .info-section {
                    margin: 8px 0;
                    font-size: 11px;
                }
                .info-row {
                    display: flex;
                    justify-content: space-between;
                    margin: 2px 0;
                    gap: 4px;
                }
                .info-row span:first-child {
                    flex-shrink: 0;
                }
                .info-row span:last-child {
                    text-align: right;
                    word-break: break-word;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 8px 0;
                    font-size: 11px;
                }
                th {
                    border-top: 1px solid #000;
                    border-bottom: 1px solid #000;
                    padding: 4px 2px;
                    text-align: left;
                    font-weight: bold;
                    font-size: 11px;
                }
                td {
                    padding: 3px 2px;
                    vertical-align: top;
                    word-wrap: break-word;
                    border-bottom: 1px solid #ddd;
                }
                .item-desc {
                    width: 40%;
                    min-width: 80px;
                }
                .item-qty {
                    width: 15%;
                    text-align: center;
                    min-width: 25px;
                }
                .item-price {
                    width: 22%;
                    text-align: right;
                    min-width: 40px;
                }
                .item-amount {
                    width: 23%;
                    text-align: right;
                    min-width: 45px;
                }
                .summary {
                    margin-top: 6px;
                    border-top: 1px dashed #000;
                    padding-top: 6px;
                }
                .summary-row {
                    display: flex;
                    justify-content: space-between;
                    margin: 3px 0;
                    font-size: 11px;
                    gap: 4px;
                }
                .summary-row span:first-child {
                    flex: 1;
                }
                .summary-row span:last-child {
                    text-align: right;
                    white-space: nowrap;
                }
                .total-row {
                    font-weight: bold;
                    font-size: 12px;
                    border-top: 1px solid #000;
                    padding-top: 4px;
                    margin-top: 4px;
                }
                .footer {
                    text-align: center;
                    margin-top: 10px;
                    border-top: 1px dashed #000;
                    padding-top: 8px;
                    font-size: 11px;
                }
                .footer p {
                    margin: 3px 0;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h3>Jeongol Izakaya Hotpot & Grill</h3>
                <p>Koronadal City, South Cotabato, Philippines</p>
                <p>VAT Reg. TIN 295-774-127-00003</p>
                <p style="margin-top: 6px; font-weight: bold;">Receipt</p>
            </div>
            <div class="info-row">
                <span>Transaction No:</span>
                <span style="font-weight: bold;">${transactionNo || 'N/A'}</span>
            </div>

            <table>
                <thead>
                    <tr>
                        <th class="item-desc">Item Description</th>
                        <th class="item-price">Price</th>
                        <th class="item-amount">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    ${orderItemsHTML}
                </tbody>
            </table>

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
                    <span>Total Sales (VAT Inclusive):</span>
                    <span>${subtotal.toFixed(2)}</span>
                </div>
                ${actualAdvancePayment > 0 ? `
                <div class="summary-row">
                    <span>Advance Payment:</span>
                    <span>${actualAdvancePayment.toFixed(2)}</span>
                </div>
                ` : ''}
                <div class="summary-row">
                    <span>Discount:</span>
                    <span>${discountAmount.toFixed(2)}</span>
                </div>
                <div class="summary-row total-row">
                    <span>TOTAL AMOUNT DUE:</span>
                    <span>${finalTotal.toFixed(2)}</span>
                </div>
                <div class="summary-row" style="margin-top: 6px;">
                    <span>Cash Received:</span>
                    <span>${cashReceived.toFixed(2)}</span>
                </div>
                <div class="summary-row">
                    <span>Change:</span>
                    <span>${change.toFixed(2)}</span>
                </div>
            </div>

            <div class="info-section">
                <div class="info-row">
                    <span>Date:</span>
                    <span>${dateStr} ${timeStr}</span>
                </div>
                <div class="info-row">
                    <span>Cashier:</span>
                    <span>${CASHIER_NAME}</span>
                </div>
            </div>

            <div class="footer">
                <p>Thank you for dining with us!</p>
                <p>Please come again</p>
            </div>
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

            // Build items as table rows
            let itemsHTML = '';
            summaryItems.forEach(item => {
                const itemNameElement = item.querySelector('.flex-1.pr-2');
                const priceElement = item.querySelector('.w-20.text-right.font-mono');
                if (itemNameElement && priceElement) {
                    const itemName = itemNameElement.textContent.trim();
                    const price = priceElement.textContent.trim();

                    let displayName = itemName;
                    let quantity = '1';
                    const quantityMatch = itemName.match(/(.+)\s+x(\d+)$/); // ✅ FIXED
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
                        @page { size: 80mm auto; margin: 0; } 
                        body { margin: 0; padding: 2mm; } 
                    }
                    body { 
                        font-family: "Courier New", monospace; 
                        font-size: 12px; 
                        width: 280px; 
                        margin: 0 auto; 
                        padding: 8px; 
                    }
                    .center { text-align: center; }
                    .dotted-line { 
                        border-top: 1px dotted #000; 
                        margin: 6px 0; 
                    }
                    table { 
                        width: 100%; 
                        border-collapse: collapse; 
                    }
                    th, td { 
                        padding: 3px 2px; 
                        font-size: 10px; 
                    }
                    th {
                        border-bottom: 1px dotted #000;
                        font-weight: bold;
                    }
                </style>
            </head>
            <body>
            
                <div class="center" style="font-size: 20px;"><strong>Bill</strong></div>
                <div class="dotted-line"></div>
                <div style="font-size: 13px;">Customer: ${customerName}</div>
                @foreach ($reservations as $reservation)
                    <div>Table: {{ $reservation->table?->table_number ?? 'N/A' }}</div>
                @endforeach

                <div class="dotted-line"></div>
                <table>
                    <thead>
                        <tr>
                            <th style="text-align: left; width: 55%;">Item</th>
                            <th style="text-align: center; width: 15%;">Qty</th>
                            <th style="text-align: right; width: 30%;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>${itemsHTML}</tbody>
                </table>
                <div class="dotted-line"></div>
                <div class="center"><strong>TOTAL: ${total}</strong></div>
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

<!-- -->
<!-- --><!-- -->
<!-- -->

</html>