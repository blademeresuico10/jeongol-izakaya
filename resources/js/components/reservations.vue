<template>
  <div>
    <div class="time-display">{{ manilaTime }}</div>

    <div class="table-layout">
      <div
        v-for="table in tables"
        :key="table.id"
        class="table-link"
        @click="toggleInlineOptions(table.id)"
      >
        <div class="table available">
          <div class="table-number">Table {{ table.table_number }}</div>
          <div
            class="inline-options"
            :style="{ display: inlineOptionsId === table.id ? 'flex' : 'none' }"
          >
            <button class="inline-btn" @click.stop="makeOrder(table)">Place Order</button>
            <button class="inline-btn" @click.stop="makeReservation(table)">Make Reservation</button>
          </div>
        </div>
      </div>
    </div>

    <div class="bottom-buttons">
      <a class="view-button" href="/view_reservations">View Reservation</a>
      <a class="view-button" href="/kitchen">View Kitchen</a>
      <a class="view-button" href="#">Modify Orders</a>
    </div>

    <!-- Modal -->
    <div class="modal" v-if="showModal" @click.self="closeModal">
      <div class="modal-content">
        <span class="close-modal" @click="closeModal">&times;</span>
        <h2>Customer Info and Menu</h2>

        <div class="modal-section">
          <label><strong>Customer</strong></label>
          <input v-model="form.customerName" type="text" placeholder="Customer's name" required>
        </div>

        <div class="modal-section modal-flex">
          <div class="modal-column">
            <label><strong>Number of Pax</strong></label>
            <input v-model.number="form.pax" type="number" min="1" required>
          </div>
          <div class="modal-column">
            <label><strong>Reserved Now</strong></label>
            <input type="date" v-model="form.date" :disabled="disableDateTime">
            <input type="time" v-model="form.time" :disabled="disableDateTime"
                   @input="updateMenuPrices">
            <p><strong>Reservation Time Frame:</strong>
              <span style="font-size:0.9rem">{{ timeFrameDisplay }}</span>
            </p>
          </div>
        </div>

        <div class="modal-section">
          <label><strong>Advance Payment</strong></label>
          <input v-model="form.advancePayment" type="text" placeholder="Enter Amount">
        </div>

        <hr>

        <div class="modal-section modal-flex">
          <div class="modal-column">
            <p><strong>Place Order</strong></p>
            <div v-for="item in uniqueMenuItems" :key="item">
              <label>
                <input type="checkbox" :value="item" v-model="form.selectedItems">
                {{ item }}
              </label>
            </div>
            <br>
            <div><strong>Total: ₱{{ total.toFixed(2) }}</strong></div>
          </div>
          <div class="modal-column">
            <p><strong>Order Quantity</strong></p>
            <div class="order-input">
              <div v-for="item in form.selectedItems" :key="item">
                <label>{{ item }}
                  <input type="number" v-model.number="form.quantities[item]" min="1" style="width:50px">
                </label>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-section">
          <textarea v-model="form.notes" placeholder="Add notes" rows="2"></textarea>
        </div>

        <div class="modal-actions">
          <button class="pay-btn" :disabled="submitting" @click="submitToCashier">
            {{ submitting ? 'Submitting...' : 'Submit to cashier' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import axios from 'axios'
import { format } from 'date-fns'

const props = defineProps({
  tables: Array,
  menuItems: Object
})

const manilaTime = ref('')
const inlineOptionsId = ref(null)
const showModal = ref(false)
const selectedTable = ref(null)
const isPlacingOrder = ref(false)
const disableDateTime = ref(false)
const submitting = ref(false)

const form = ref({
  customerName: '',
  pax: 1,
  date: '',
  time: '',
  advancePayment: '',
  selectedItems: [],
  quantities: {},
  notes: ''
})

const fullMenuPrices = props.menuItems
const menuPrices = ref({})
const uniqueMenuItems = Object.keys(fullMenuPrices)

function toggleInlineOptions(id) {
  inlineOptionsId.value = (inlineOptionsId.value === id) ? null : id
}

function makeOrder(table) {
  selectedTable.value = table
  isPlacingOrder.value = true
  disableDateTime.value = true
  openModal()
  setNowDateTime()
}

function makeReservation(table) {
  selectedTable.value = table
  isPlacingOrder.value = false
  disableDateTime.value = false
  openModal()
  setNowDateTime()
}

function openModal() {
  form.value = {
    customerName: '',
    pax: 1,
    date: '',
    time: '',
    advancePayment: '',
    selectedItems: [],
    quantities: {},
    notes: ''
  }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
}

function setNowDateTime() {
  const now = new Date()
  form.value.date = format(now, 'yyyy-MM-dd')
  form.value.time = format(now, 'HH:mm')
  updateMenuPrices()
}

function updateMenuPrices() {
  const [h, m] = form.value.time.split(':').map(Number)
  const minutes = h * 60 + m
  const isLunch = minutes < 960
  menuPrices.value = {}
  for (const item in fullMenuPrices) {
    const prices = fullMenuPrices[item]
    menuPrices.value[item] = isLunch ? (prices.lunch ?? prices.dinner) : (prices.dinner ?? prices.lunch)
  }
}

watch(() => form.value.selectedItems, () => {
  for (const item of form.value.selectedItems) {
    if (!form.value.quantities[item]) {
      form.value.quantities[item] = 1
    }
  }
})

const total = computed(() => {
  return form.value.selectedItems.reduce((sum, item) => {
    const qty = form.value.quantities[item] || 0
    return sum + (qty * parseFloat(menuPrices.value[item] || 0))
  }, 0)
})

const timeFrameDisplay = computed(() => {
  if (!form.value.date || !form.value.time) return ''
  const [h, m] = form.value.time.split(':').map(Number)
  const start = new Date(form.value.date)
  start.setHours(h, m)
  const end = new Date(start)
  end.setHours(end.getHours() + 2)
  return `${start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })} - ${end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`
})

async function submitToCashier() {
  const [h, m] = form.value.time.split(':').map(Number)
  const totalMinutes = h * 60 + m
  if (totalMinutes < 690 || totalMinutes > (isPlacingOrder.value ? 1200 : 1080)) {
    alert(`Invalid time. Please select between 11:30 AM and ${isPlacingOrder.value ? '8:00 PM' : '6:00 PM'}.`)
    return
  }

  submitting.value = true
  try {
    const payload = {
      customer_name: form.value.customerName,
      pax: form.value.pax,
      reserved_date: form.value.date,
      arrival_time: form.value.time,
      table_id: selectedTable.value.id,
      advance_payment: form.value.advancePayment,
      orders: form.value.selectedItems.map(item => ({
        item,
        qty: form.value.quantities[item],
        notes: form.value.notes
      }))
    }
    const res = await axios.post('/receptionist/storeReservation', payload)
    if (res.data.success) {
      alert('Reservation submitted!')
      closeModal()
      location.reload()
    } else {
      alert(res.data.message || 'Failed to save reservation.')
    }
  } catch {
    alert('An error occurred.')
  }
  submitting.value = false
}

onMounted(() => {
  setInterval(() => {
    manilaTime.value = new Date().toLocaleString('en-PH', {
      timeZone: 'Asia/Manila', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true
    })
  }, 1000)
})
</script>

<style scoped>
/* Paste your same CSS here or use global from your Blade @include */
</style>
