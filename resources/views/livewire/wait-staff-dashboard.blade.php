<style>
    @keyframes heartbeat {

        0%,
        100% {
            transform: scale(1);
        }

        25% {
            transform: scale(1.1);
        }

        50% {
            transform: scale(1);
        }

        75% {
            transform: scale(1.05);
        }
    }

    .animate-heartbeat {
        animation: heartbeat 1.5s ease-in-out infinite;
    }
</style>
<div wire:poll.10s="loadTables">

    <div class="max-w-7xl mx-auto px-4 py-6" x-data="waitStaffData()">

        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Select Table</h2>
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4">
                <template x-for="table in tables" :key="table.id">
                    <button @click="selectTable(table.id)" :class="{
                                        'bg-orange-600 text-white shadow-xl scale-105': activeTable === table.id,
                                        'bg-orange-100 text-orange-800': table.hasOrders && table.hasActiveSession,
                                        'bg-gray-100 text-gray-600 cursor-not-allowed': !table.hasActiveSession
                                    }" :disabled="!table.hasActiveSession"
                        class="relative p-6 rounded-xl font-bold text-lg transition-all">
                        <span x-text="'Table ' + table.number"></span>

                        <template x-if="table.hasReadyItems">
                            <span
                                class="absolute -top-1 -right-1 text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-md border border-green-200 animate-heartbeat whitespace-nowrap">
                                Ready to Serve!
                            </span>
                        </template>
                    </button>
                </template>
            </div>
        </div>

        <div x-show="activeTable" x-transition class="grid grid-cols-2 gap-4 mb-6">
            <button @click="showMenu = true"
                class="bg-green-600 text-white p-6 rounded-xl font-bold text-lg flex items-center justify-center gap-3">
                Add Order
            </button>
            <button @click="showRefillModal = true" :disabled="!hasUnlimitedPackage"
                :class="hasUnlimitedPackage ? 'bg-blue-600 text-white hover:bg-blue-700 active:bg-blue-800' : 'bg-gray-300 text-gray-500 cursor-not-allowed'"
                class="p-6 rounded-xl font-bold text-lg flex items-center justify-center gap-3 transition-colors shadow-lg touch-manipulation">
                Refill
            </button>
        </div>

        <div x-show="activeTable" x-transition class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800">
                    Table <span x-text="activeTable ? getTableNumber(activeTable) : ''"></span> Orders
                </h2>
                <!-- Serve All Ready Button -->
                <template x-if="hasReadyItemsInCurrentTable()">
                    <button @click="serveAllReady()"
                        class="px-6 py-3 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white rounded-lg font-semibold transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Serve All Ready
                    </button>
                </template>
            </div>

            <template x-if="currentOrders.length === 0 && currentRefills.length === 0">
                <div class="text-center py-16">
                    <svg class="w-20 h-20 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                    <h3 class="text-xl font-bold text-gray-600 mb-2">No Orders Yet</h3>
                </div>
            </template>

            <!-- Orders & Refills List -->
            <template x-if="currentOrders.length > 0 || currentRefills.length > 0">
                <div class="border-2 rounded-xl overflow-hidden">
                    <!-- Regular Orders -->
                    <template x-for="(order, index) in currentOrders" :key="'order-' + order.id">
                        <div class="flex items-center justify-between p-5 border-l-4" :class="{
                                'border-green-500 bg-green-50': order.status === 'Ready',
                                'border-blue-500 bg-blue-50': order.status === 'Served',
                                'border-orange-500 bg-orange-50': order.status === 'Pending',
                                'border-red-500 bg-red-50': order.status === 'Cancelled',
                                'border-b-2': index < currentOrders.length - 1 || currentRefills.length > 0
                            }">
                            <div class="flex-1">
                                <div class="flex items-center gap-4 mb-2">
                                    <span class="font-bold text-lg text-gray-800"
                                        :class="{'line-through text-gray-400': order.status === 'Cancelled'}"
                                        x-text="order.item"></span>

                                    <template x-if="!order.isUnlimited">
                                        <span
                                            class="text-base font-semibold text-gray-600 bg-white px-3 py-1 rounded-lg">
                                            x<span x-text="order.quantity"></span>
                                        </span>
                                    </template>

                                    <!-- Status Badge -->
                                    <span class="text-xs font-semibold px-3 py-1 rounded-full" :class="{
                                        'bg-green-100 text-green-700': order.status === 'Ready',
                                        'bg-blue-100 text-blue-700': order.status === 'Served',
                                        'bg-orange-100 text-orange-700': order.status === 'Pending',
                                        'bg-red-100 text-red-700': order.status === 'Cancelled'
                                    }" x-text="order.status"></span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 ml-4">
                                <!-- Remove Button for Pending Orders -->
                                <template x-if="order.status === 'Pending'">
                                    <button @click="removeItem(order.id, 'order')"
                                        class="p-3 text-red-600 bg-red-100 hover:bg-red-200 active:bg-red-300 rounded-xl transition-colors touch-manipulation"
                                        title="Remove order">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Refills -->
                    <template x-for="(refill, index) in currentRefills" :key="'refill-' + refill.id">
                        <div class="flex items-center justify-between p-5 border-l-4 active:bg-gray-100 transition-colors touch-manipulation"
                            :class="{
                                'border-green-500 bg-green-50': refill.status === 'Ready',
                                'border-blue-500 bg-blue-50': refill.status === 'Served',
                                'border-purple-500 bg-purple-50': refill.status === 'Pending',
                                'border-red-500 bg-red-50': refill.status === 'Cancelled',
                                'border-b-2': index < currentRefills.length - 1
                            }">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <!-- Refill Badge -->
                                    <span class="text-xs font-bold px-2 py-1 rounded-full bg-purple-600 text-white">
                                        REFILL
                                    </span>

                                    <!-- Ingredient Name -->
                                    <span class="font-bold text-lg text-gray-800"
                                        :class="{'line-through text-gray-400': refill.status === 'Cancelled'}"
                                        x-text="refill.ingredient_name"></span>

                                    <!-- Quantity -->
                                    <span class="text-base font-semibold text-gray-600 bg-white px-3 py-1 rounded-lg">
                                        x<span x-text="refill.quantity"></span> plate(s)
                                    </span>
                                </div>

                                <!-- Status Badge -->
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="text-xs font-semibold px-3 py-1 rounded-full" :class="{
                                        'bg-green-100 text-green-700': refill.status === 'Ready',
                                        'bg-blue-100 text-blue-700': refill.status === 'Served',
                                        'bg-orange-100 text-orange-700': refill.status === 'Pending',
                                        'bg-red-100 text-red-700': refill.status === 'Cancelled'
                                    }" x-text="refill.status"></span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 ml-4">
                                <template x-if="refill.status === 'Pending'">
                                    <button @click="removeItem(refill.id, 'refill')"
                                        class="p-3 text-red-600 bg-red-100 hover:bg-red-200 active:bg-red-300 rounded-xl transition-colors touch-manipulation"
                                        title="Remove refill">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <!-- Menu Modal -->
        <div x-show="showMenu" x-cloak @click.self="showMenu = false"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-2 sm:p-4 z-50"
            style="display: none;">
            <div @click.away="showMenu = false"
                class="bg-white rounded-xl w-full h-full sm:h-auto sm:max-w-6xl sm:max-h-[90vh] overflow-hidden shadow-2xl flex flex-col">

                <!-- Header -->
                <div class="bg-white border-b p-4 sm:p-5 flex items-center justify-between shrink-0">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900">Add Order</h2>
                    <button @click="showMenu = false"
                        class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center touch-manipulation rounded-lg hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Content Grid -->
                <div class="flex-1 overflow-hidden grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x">

                    <!-- Left Side: Menu Items -->
                    <div class="overflow-y-auto p-4 sm:p-6">
                        <h3
                            class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4 sticky top-0 bg-white pb-2 z-10">
                            Menu Items</h3>
                        @forelse($menuItems as $category => $items)
                            <div class="mb-6 last:mb-0">
                                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">
                                    {{ $category }}
                                </h4>
                                <div class="space-y-2">
                                    @foreach($items as $item)
                                        <button
                                            @click="addToCart({{ $item['id'] }}, @js($item['menu_item'])
                                                                                                                                                , {{ $item['regular_price'] }})"
                                            class="w-full p-3 sm:p-4 text-left bg-white hover:bg-gray-50 active:bg-gray-100 border border-gray-200 hover:border-gray-300 rounded-lg transition-all touch-manipulation group">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1 min-w-0">
                                                    <div class="font-medium text-sm text-gray-900 group-hover:text-gray-900">
                                                        {{ $item['menu_item'] }}
                                                    </div>
                                                </div>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-400 py-12">
                                <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p class="text-sm">No menu items available</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Right Side: Order Summary -->
                    <div class="bg-gray-50 p-4 sm:p-6 flex flex-col min-h-0">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4 shrink-0">Order
                            Summary</h3>

                        <!-- Order Items List - Scrollable -->
                        <div class="flex-1 overflow-y-auto mb-4 space-y-2 min-h-0">
                            <template x-if="orderCart.length === 0">
                                <div class="flex flex-col items-center justify-center text-gray-300 py-12">
                                    <svg class="w-16 h-16 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <p class="text-sm font-medium">Your cart is empty</p>
                                    <p class="text-xs mt-1">Add items from the menu</p>
                                </div>
                            </template>

                            <template x-for="(item, index) in orderCart" :key="index">
                                <div class="bg-white rounded-lg p-3 border border-gray-200">
                                    <div class="flex items-center justify-between gap-3">
                                        <h4 class="flex-1 font-medium text-sm text-gray-900" x-text="item.name"></h4>

                                        <div class="flex items-center gap-2">
                                            <button @click="decreaseQuantity(index)"
                                                class="w-7 h-7 flex items-center justify-center hover:bg-gray-100 rounded text-gray-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M20 12H4" />
                                                </svg>
                                            </button>
                                            <span class="w-8 text-center font-semibold text-sm"
                                                x-text="item.quantity"></span>
                                            <button @click="increaseQuantity(index)"
                                                class="w-7 h-7 flex items-center justify-center hover:bg-gray-100 rounded text-gray-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </button>
                                        </div>

                                        <button @click="removeFromCart(index)"
                                            class="text-gray-400 hover:text-red-500 p-1">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Notes -->
                        <div class="mb-4 shrink-0">
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Notes (Optional)</label>
                            <textarea x-model="orderNotes" placeholder="Add special instructions..."
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none text-sm text-gray-900 placeholder-gray-400"
                                rows="2"></textarea>
                        </div>

                        <!-- Submit Button -->
                        <button @click="submitOrder()" :disabled="orderCart.length === 0"
                            :class="orderCart.length === 0 ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700 text-white shadow-sm hover:shadow'"
                            class="w-full py-3.5 font-semibold text-base rounded-lg transition-all touch-manipulation shrink-0">
                            Place Order
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Refill Modal -->
        <div x-show="showRefillModal" x-cloak @click.self="closeRefillModal()"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
            style="display: none;">

            <div @click.away="closeRefillModal()"
                class="bg-white rounded-2xl max-w-3xl w-full max-h-[85vh] overflow-y-auto shadow-2xl">

                <!-- Header -->
                <div class="sticky top-0 bg-white border-b-2 p-6 flex items-center justify-between z-10 rounded-t-2xl">
                    <h2 class="text-2xl font-bold text-gray-800">Select Refill Items</h2>
                    <button @click="closeRefillModal()"
                        class="text-gray-500 hover:text-gray-700 text-4xl w-12 h-12 flex items-center justify-center rounded-full hover:bg-gray-100">
                        ×
                    </button>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <!-- No Unlimited Package -->
                    <template x-if="!hasUnlimitedPackage">
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-gray-500 text-lg">This table doesn't have an unlimited package.</p>
                        </div>
                    </template>

                    <!-- With Unlimited Package -->
                    <template x-if="hasUnlimitedPackage">
                        <div>
                            <template x-for="category in getRefillCategories()" :key="category">
                                <div class="mb-6">
                                    <h3 class="text-lg font-bold text-gray-700 mb-3 pb-2 border-b" x-text="category">
                                    </h3>

                                    <div class="space-y-3">
                                        <template x-for="item in getRefillsByCategory(category)" :key="item.id">
                                            <div
                                                class="flex items-center justify-between border rounded-xl p-3 bg-white hover:bg-gray-50 transition">

                                                <!-- Left side: Checkbox + name -->
                                                <div class="flex items-center gap-3 flex-1">
                                                    <input type="checkbox"
                                                        @change="toggleRefillItem(item.id, $event.target.checked)"
                                                        class="w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500" />
                                                    <span class="font-semibold text-gray-800" x-text="item.name"></span>
                                                </div>

                                                <!-- Right side: Quantity controls -->
                                                <template x-if="selectedRefills[item.id]">
                                                    <div class="flex items-center gap-2">
                                                        <button type="button"
                                                            @click="updateRefillPlates(item.id, Math.max(selectedRefills[item.id].plates - 1, 1))"
                                                            class="w-8 h-8 flex items-center justify-center bg-gray-200 rounded hover:bg-gray-300 font-bold">−</button>

                                                        <input type="text"
                                                            x-model.number="selectedRefills[item.id].plates"
                                                            @input="if (!/^\d*$/.test($event.target.value)) $event.target.value = selectedRefills[item.id].plates"
                                                            @keypress="if (!/[0-9]/.test($event.key)) $event.preventDefault()"
                                                            @paste.prevent
                                                            class="w-12 text-center border rounded-lg px-2 py-1 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                                            style="appearance: textfield; -moz-appearance: textfield; -webkit-appearance: none;" />

                                                        <button type="button"
                                                            @click="updateRefillPlates(item.id, selectedRefills[item.id].plates + 1)"
                                                            class="w-8 h-8 flex items-center justify-center bg-gray-200 rounded hover:bg-gray-300 font-bold">+</button>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <!-- Submit -->
                            <div class="sticky bottom-0 bg-white pt-4 border-t-2 mt-6">
                                <button @click="submitRefills()" :disabled="Object.keys(selectedRefills).length === 0"
                                    :class="Object.keys(selectedRefills).length > 0 ? 
                                'bg-blue-600 hover:bg-blue-700 text-white' : 
                                'bg-gray-300 text-gray-500 cursor-not-allowed'"
                                    class="w-full p-4 rounded-xl font-bold text-lg transition">
                                    Add Refill(s)
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>


        <!-- Serve Confirmation Modal -->
        <div x-show="showServeModal" x-cloak @click.self="showServeModal = false"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
            style="display: none;">
            <div @click.away="showServeModal = false" class="bg-white rounded-2xl max-w-md w-full shadow-2xl">
                <div class="p-6">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>

                    <h3 class="text-xl font-bold text-gray-900 text-center mb-2">
                        Serve Items
                    </h3>

                    <p class="text-gray-600 text-center mb-6" x-text="serveModalMessage"></p>

                    <!-- Items List -->
                    <div class="max-h-60 overflow-y-auto mb-6 border rounded-lg">
                        <template x-for="item in itemsToServe" :key="item.id">
                            <div class="flex items-center justify-between p-3 border-b last:border-b-0 bg-gray-50">
                                <div class="flex-1">
                                    <span class="font-medium text-sm text-gray-900" x-text="item.name"></span>
                                </div>
                                <span class="text-sm text-gray-600" x-text="item.quantity"></span>
                            </div>
                        </template>
                    </div>

                    <div class="flex gap-3">
                        <button @click="showServeModal = false"
                            class="flex-1 px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold transition-colors">
                            Cancel
                        </button>
                        <button @click="confirmServe()"
                            class="flex-1 px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition-colors">
                            Confirm Serve
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function waitStaffData() {
        return {
            activeTable: @json($activeTable),
            showMenu: false,
            showRefillModal: false,
            showServeModal: false,
            serveModalMessage: '',
            itemsToServe: [],
            currentOrders: @json($orders),
            currentRefills: @json($refills),
            tableNote: @json($tableNote),
            tables: @json($tables),
            availableRefills: @json($availableRefills),
            hasUnlimitedPackage: @json($hasUnlimitedPackage),
            selectedRefills: {},
            orderCart: [],
            orderNotes: '',

            init() {
                setInterval(async () => {
                    if (this.activeTable) {
                        await @this.call('loadOrders', this.activeTable);
                    }
                    await @this.call('loadTables');
                    this.tables = @this.get('tables');
                }, 3000);

                Livewire.on('ordersUpdated', (data) => {
                    this.currentOrders = data.orders || @this.get('orders');
                    this.currentRefills = data.refills || @this.get('refills');
                    this.tableNote = data.tableNote || @this.get('tableNote');
                    this.availableRefills = data.availableRefills || @this.get('availableRefills');
                    this.hasUnlimitedPackage = data.hasUnlimitedPackage || @this.get('hasUnlimitedPackage');
                });

                Livewire.on('tablesUpdated', (data) => {
                    this.tables = data.tables || data;
                });
            },

            getTableNumber(tableId) {
                const table = this.tables.find(t => t.id === tableId);
                return table ? table.number : tableId;
            },

            hasReadyItemsInCurrentTable() {
                const hasReadyOrders = this.currentOrders.some(order => order.status === 'Ready');
                const hasReadyRefills = this.currentRefills.some(refill => refill.status === 'Ready');
                return hasReadyOrders || hasReadyRefills;
            },

            async selectTable(tableId) {
                this.activeTable = tableId;
                this.selectedRefills = {};
                await @this.call('loadOrders', tableId);
                this.currentOrders = @this.get('orders');
                this.currentRefills = @this.get('refills');
                this.tableNote = @this.get('tableNote');
                this.availableRefills = @this.get('availableRefills');
                this.hasUnlimitedPackage = @this.get('hasUnlimitedPackage');
            },

            async removeItem(id, type) {
                const confirmMsg = type === 'order' ? 'Remove this item from the order?' : 'Remove this refill?';
                if (confirm(confirmMsg)) {
                    if (type === 'order') {
                        await @this.call('removeOrder', id);
                    } else {
                        await @this.call('removeRefill', id);
                    }
                    this.currentOrders = @this.get('orders');
                    this.currentRefills = @this.get('refills');
                    this.tables = @this.get('tables');
                }
            },

            serveAllReady() {
                if (!this.activeTable) {
                    alert('Please select a table first');
                    return;
                }

                this.itemsToServe = [];

                const readyOrders = this.currentOrders.filter(o => o.status === 'Ready');
                readyOrders.forEach(order => {
                    this.itemsToServe.push({
                        id: order.id,
                        name: order.item,
                        quantity: order.isUnlimited ? 'Unlimited' : `x${order.quantity}`,
                        type: 'order'
                    });
                });

                const readyRefills = this.currentRefills.filter(r => r.status === 'Ready');
                readyRefills.forEach(refill => {
                    this.itemsToServe.push({
                        id: refill.id,
                        name: refill.ingredient_name,
                        quantity: `x${refill.quantity} plate(s)`,
                        type: 'refill'
                    });
                });

                if (this.itemsToServe.length === 0) {
                    alert('No ready items to serve');
                    return;
                }

                this.showServeModal = true;
            },

            async confirmServe() {
                await @this.call('serveAllReady', this.activeTable);
                this.currentOrders = @this.get('orders');
                this.currentRefills = @this.get('refills');
                this.tables = @this.get('tables');
                this.showServeModal = false;
                this.itemsToServe = [];
            },

            async submitRefills() {
                if (!this.activeTable) {
                    alert('Please select a table first');
                    return;
                }

                if (Object.keys(this.selectedRefills).length === 0) {
                    alert('Please select at least one refill');
                    return;
                }

                await @this.call('addRefill', this.activeTable, Object.values(this.selectedRefills));
                this.currentRefills = @this.get('refills');
                this.tables = @this.get('tables');
                this.selectedRefills = {};
                this.showRefillModal = false;
            },

            getRefillCategories() {
                const categories = [...new Set(this.availableRefills.map(item => item.category))];
                return categories.sort();
            },

            getRefillsByCategory(category) {
                return this.availableRefills.filter(item => item.category === category);
            },

            toggleRefillItem(ingredientId, selected) {
                if (selected) {
                    this.selectedRefills[ingredientId] = {
                        ingredient_id: ingredientId,
                        plates: 1,
                        selected: true
                    };
                } else {
                    delete this.selectedRefills[ingredientId];
                }
            },

            updateRefillPlates(ingredientId, plates) {
                if (this.selectedRefills[ingredientId]) {
                    this.selectedRefills[ingredientId].plates = parseInt(plates) || 1;
                }
            },

            closeRefillModal() {
                this.selectedRefills = {};
                this.showRefillModal = false;
            },

            addToCart(menuId, name, price) {
                const existingIndex = this.orderCart.findIndex(item => item.menuId === menuId);
                if (existingIndex !== -1) {
                    this.orderCart[existingIndex].quantity++;
                } else {
                    this.orderCart.push({ menuId, name, price: parseFloat(price), quantity: 1 });
                }
            },

            removeFromCart(index) {
                this.orderCart.splice(index, 1);
            },

            increaseQuantity(index) {
                this.orderCart[index].quantity++;
            },

            decreaseQuantity(index) {
                if (this.orderCart[index].quantity > 1) {
                    this.orderCart[index].quantity--;
                }
            },

            async submitOrder() {
                if (this.orderCart.length === 0 || !this.activeTable) {
                    return;
                }

                for (const item of this.orderCart) {
                    await @this.call('addOrder', this.activeTable, item.menuId, item.quantity, this.orderNotes);
                }

                this.currentOrders = @this.get('orders');
                this.currentRefills = @this.get('refills');
                this.tables = @this.get('tables');
                this.orderCart = [];
                this.orderNotes = '';
                this.showMenu = false;
            }
        }
    }
</script>
</div>