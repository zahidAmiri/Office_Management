<template>
  <div class="p-6">
    <h1 class="mb-4 text-2xl font-bold dark:text-white">{{ $t('supply.add') }}</h1>

    <button
     v-if="userRole === 'manager' || userRole === 'office_admin'"
      @click="openModal()"
      class="px-4 py-2 mb-6 text-white bg-green-600 rounded hover:bg-green-700"
    >
      + {{ $t('supply.add') }}
    </button>

    <a
  v-if="userRole === 'manager' || userRole === 'office_admin'"
  :href="`/supplies/print-all?lang=${locale}`"
  target="_blank"
  class="px-4 py-2 mb-4 ml-3 text-white bg-yellow-600 rounded hover:bg-yellow-700 rtl:mr-2"
>
  🖨️ {{ $t('supply.print_all') }}
</a>





    <!-- Supply Table -->
    <table class="w-full text-sm text-center shadow dark:text-white">
      <thead class="text-xs uppercase bg-gray-400 dark:bg-gray-700">
        <tr>
          <th class="px-3 py-2">#</th>
          <th class="px-3 py-2">{{ $t('supply.product') }}</th>
          <th class="px-3 py-2">{{ $t('supply.quantity') }}</th>
          <th class="px-3 py-2">{{ $t('supply.unit') }}</th>
          <th class="px-3 py-2">{{ $t('supply.type') }}</th>
          <th class="px-3 py-2">{{ $t('supply.supplier') }}</th>
          <th class="px-3 py-2">{{ $t('supply.recipient') }}</th>
          <th class="px-3 py-2">{{ $t('supply.date') }}</th>
          <th class="px-3 py-2">{{ $t('supply.description') }}</th>
          <th class="px-3 py-2"  v-if="userRole === 'manager' || userRole === 'office_admin'">{{ $t('supply.actions') }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(supply, index) in supplies" :key="supply.id" class="border-b dark:even:bg-gray-500 dark:odd:bg-gray-600">
          <td class="px-3 py-2">{{ index + 1 }}</td>
          <td class="px-3 py-2">{{ supply.product_name }}</td>
          <td class="px-3 py-2">{{ supply.quantity }}</td>
          <td class="px-3 py-2">{{ supply.unit }}</td>
          <td class="px-3 py-2">{{ supply.product_type }}</td>
          <td class="px-3 py-2">{{ supply.supplier?.name || 'N/A' }}</td>
          <td class="px-3 py-2">{{ supply.recipient?.name || 'N/A' }}</td>
          <td class="px-3 py-2">{{ formatShortDate(supply.date) }}</td>
          <td class="px-3 py-2">{{ supply.description }}</td>
          <td class="flex px-3 py-2"  v-if="userRole === 'manager' || userRole === 'office_admin'">
            <button @click="openModal(supply)" class="p-2 px-3 mx-2 text-white bg-indigo-500 rounded-md hover:bg-indigo-700">📝 {{ $t('supply.edit') }}</button>
            <button @click="deleteSupply(supply.id)" class="p-2 px-3 mx-2 text-white bg-red-500 rounded-md hover:bg-red-700">🗑️{{ $t('supply.delete') }}</button>
                <a
      :href="`/supplies/${supply.id}/print`"
      target="_blank"
      class="p-2 px-3 mx-2 text-white bg-yellow-600 rounded-md hover:bg-yellow-700"
    >
    🖨️ {{ $t('supply.print_one') }}
    </a>

          </td>

        </tr>
      </tbody>
    </table>

    <!-- Modal Popup for Add/Edit -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    >
      <div class="w-full max-w-lg p-6 bg-white rounded shadow-lg">
        <h2 class="mb-4 text-xl font-bold">{{ editingSupply ? $t('supply.edit') : $t('supply.add') }}</h2>

        <form @submit.prevent="submit">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label>{{ $t('supply.product') }}</label>
              <input
                v-model="form.product_name"
                :placeholder="$t('supply.product')"
                type="text"
                class="w-full p-2 border rounded"
                required
              />
              <p v-if="form.errors.product_name" class="text-sm text-red-500">{{ form.errors.product_name }}</p>
            </div>

            <div>
              <label>{{ $t('supply.quantity') }}</label>
              <input
                v-model="form.quantity"
                :placeholder="$t('supply.quantity')"
                type="number"
                min="1"
                class="w-full p-2 border rounded"
                required
              />
              <p v-if="form.errors.quantity" class="text-sm text-red-500">{{ form.errors.quantity }}</p>
            </div>

            <div>
              <label>{{ $t('supply.unit') }}</label>
              <input
                v-model="form.unit"
                type="text"
                class="w-full p-2 border rounded"
                :placeholder="$t('supply.unit')+' (pcs, kg, m)'"
                required
              />
              <p v-if="form.errors.unit" class="text-sm text-red-500">{{ form.errors.unit }}</p>
            </div>

            <div>
              <label>{{ $t('supply.type') }}</label>
              <input v-model="form.product_type" type="text" class="w-full p-2 border rounded"
              :placeholder="$t('supply.type') + ' (Dell, V5)'"
              />
              <p v-if="form.errors.product_type" class="text-sm text-red-500">{{ form.errors.product_type }}</p>
            </div>

            <div>
              <label>{{ $t('supply.supplier') }}</label>
              <select v-model="form.supplier_id" class="w-full p-2 text-center border rounded" required>
                <option disabled value="">-- Select Supplier --</option>
                <option v-for="emp in employees" :key="emp.id" :value="emp.id">{{ emp.name }} {{ emp.last_name }}</option>
              </select>
              <p v-if="form.errors.supplier_id" class="text-sm text-red-500">{{ form.errors.supplier_id }}</p>
            </div>

            <div>
              <label>{{ $t('supply.recipient') }}</label>
              <select v-model="form.recipient_id" class="w-full p-2 text-center border rounded" required>
                <option disabled value="">-- Select Recipient --</option>
                <option v-for="emp in employees" :key="emp.id" :value="emp.id">{{ emp.name  }} {{ emp.last_name }}</option>
              </select>
              <p v-if="form.errors.recipient_id" class="text-sm text-red-500">{{ form.errors.recipient_id }}</p>
            </div>

            <div>
              <label>{{ $t('supply.date') }}</label>
              <input v-model="form.date" type="date" class="w-full p-2 border rounded" required />
              <p v-if="form.errors.date" class="text-sm text-red-500">{{ form.errors.date }}</p>
            </div>

            <div class="col-span-2">
              <label>{{ $t('supply.description') }}</label>
              <textarea v-model="form.description" class="w-full p-2 border rounded"></textarea>
              <p v-if="form.errors.description" class="text-sm text-red-500">{{ form.errors.description }}</p>
            </div>
          </div>

          <div class="flex justify-end gap-2 mt-4">
            <button type="button" @click="closeModal" class="px-4 py-2 border rounded rtl:bg-gray-300">{{ $t('leave_allocation.cancel') }}</button>
            <button
              type="submit"
              class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700"
              :disabled="form.processing"
            >
              {{ editingSupply ? $t('supply.edit') : $t('supply.add_button') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { Inertia } from '@inertiajs/inertia'
import { defineProps } from 'vue'
import Layout from "../Layout.vue";

import { useI18n } from 'vue-i18n'
const { locale } = useI18n()

defineOptions({ layout: Layout })

const page = usePage()
const userRole = page.props.auth.user?.role;


const props = defineProps({
  supplies: Array,
  employees: Array,
})

const showModal = ref(false)
const editingSupply = ref(null) // null means adding new

const form = useForm({
  product_name: '',
  quantity: '',
  unit: '',
  product_type: '',
  supplier_id: '',
  recipient_id: '',
  date: '',
  description: '',
})

// Open modal for add or edit (if supply passed)
function openModal(supply = null) {
  editingSupply.value = supply
  if (supply) {
    // Fill form with supply data for editing
    form.reset()
    form.product_name = supply.product_name
    form.quantity = supply.quantity
    form.unit = supply.unit
    form.product_type = supply.product_type
    form.supplier_id = supply.supplier_id
    form.recipient_id = supply.recipient_id
    form.date = supply.date
    form.description = supply.description
  } else {
    form.reset()
  }
  showModal.value = true
}

// Close modal
function closeModal() {
  showModal.value = false
  editingSupply.value = null
  form.reset()
}

// Submit add or edit
function submit() {
  if (editingSupply.value) {
    // Update existing supply via PUT
    form.put(`/supplies/${editingSupply.value.id}`, {
      onSuccess: () => {
        closeModal()
      },
    })
  } else {
    // Create new supply via POST
    form.post('/supplies', {
      onSuccess: () => {
        closeModal()
      },
    })
  }
}

// Delete supply
function deleteSupply(id) {
  if (!confirm('Are you sure you want to delete this supply?')) return
  Inertia.delete(`/supplies/${id}`)
}

function formatShortDate(dateStr) {
  const date = new Date(dateStr)
  return date.toLocaleDateString(undefined, { month: 'short', day: 'numeric' })
}

</script>
