<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-bold dark:text-white">{{ $t('distribution.title') }}</h1>
      <button 
      v-if="userRole === 'manager' || userRole === 'office_admin'"
       @click="openModal()" class="px-4 py-2 text-white bg-blue-600 rounded">+ {{ $t('distribution.add') }}</button>
    </div>
    <!-- Table -->
    <table class="w-full text-sm text-center border shadow table-auto dark:text-white">
      <thead class="bg-gray-400 dark:bg-gray-700">
        <tr>
          <th class="p-2">#</th>
          <th class="p-2">{{ $t('distribution.product') }}</th>
          <th class="p-2">{{ $t('distribution.quantity') }}</th>
          <th class="p-2">{{ $t('distribution.unit') }}</th>
          <th class="p-2">{{ $t('distribution.remaining') }}</th>
          <th class="p-2">{{ $t('distribution.distributer') }}</th>
          <th class="p-2">{{ $t('distribution.recipient') }}</th>
          <th class="p-2">{{ $t('distribution.date') }}</th>
          <th class="p-2">{{ $t('distribution.description') }}</th>
          <th class="p-2" v-if="userRole === 'manager' || userRole === 'office_admin'">{{ $t('distribution.actions') }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in distributions" :key="item.id" class="border-t dark:even:bg-gray-500 dark:bg-gray-600">
          <td class="p-2">{{ index + 1 }}</td>
          <td class="p-2">{{ item.product_name }}</td>
          <td class="p-2">{{ item.quantity }}</td>
          <td class="p-2">{{ item.unit }}</td>
          <td class="p-2">
            {{
              remainingQuantity(item.product_name, item.unit)
            }}
          </td>
          <td class="p-2">{{ item.distributor?.name }}</td>
          <td class="p-2">{{ item.department?.name }}</td>
          <td class="p-2">{{ item.date }}</td>
          <td class="p-2">{{ item.description }}</td>
          <td class="flex p-2 space-x-2" v-if="userRole === 'manager' || userRole === 'office_admin'">
            <button @click="openModal(item)" class="p-2 px-3 mx-2 text-white bg-indigo-500 rounded-md hover:bg-indigo-700">{{ $t('distribution.edit') }}</button>
            <button @click="destroy(item.id)" class="p-2 px-3 mx-2 text-white bg-red-500 rounded-md hover:bg-red-700">{{ $t('distribution.delete') }}</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Modal -->
    <div v-if="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="relative w-full max-w-2xl p-6 bg-white rounded shadow">
        <button @click="closeModal" class="absolute text-lg top-2 right-2">×</button>
        <h2 class="mb-4 text-xl font-bold">{{ form.id ? $t('distribution.form_title_edit') : $t('distribution.form_title_add') }}</h2>

        <form @submit.prevent="submit">
          <div class="grid grid-cols-2 gap-4">
           
            <div>
  <label>{{ $t('distribution.product') }}</label>
  <select v-model="form.product_name" class="w-full p-2 text-center border rounded">
    <option disabled value="">{{ $t('distribution.select') }}</option>
    <option
      v-for="item in storeItems"
      :key="item.product_name + item.unit"
      :value="item.product_name"
    >
      {{ item.product_name }} ({{ item.quantity }} {{ item.unit }})
    </option>
  </select>
  <p class="text-sm text-red-500">{{ form.errors.product_name }}</p>
</div>

            <div>
              <label>{{ $t('distribution.unit') }}</label>
              <select v-model="form.unit" class="w-full p-2 text-center border rounded">
                <option disabled value="">{{ $t('distribution.select') }}</option>
                <option
                  v-for="item in storeItems.filter(i => i.product_name === form.product_name)"
                  :key="item.unit"
                  :value="item.unit"
                >
                  {{ item.unit }}
                </option>
              </select>
              <p class="text-sm text-red-500">{{ form.errors.unit }}</p>
            </div>

            <div>
              <label>{{ $t('distribution.quantity') }}</label>
              <input type="number" v-model="form.quantity" class="w-full p-2 text-center border rounded" />
              <p class="text-sm text-red-500">{{ form.errors.quantity }}</p>
            </div>

            <div>
              <label>{{ $t('distribution.distributer') }}</label>
              <select v-model="form.distributed_by" class="w-full p-2 text-center border rounded">
                <option disabled value="">{{ $t('distribution.select') }}</option>
                <option v-for="emp in employees" :key="emp.id" :value="emp.id">{{ emp.name }} {{ emp.last_name }}</option>
              </select>
              <p class="text-sm text-red-500">{{ form.errors.distributed_by }}</p>
            </div>

            <div>
              <label>{{ $t('distribution.recipient') }}</label>
              <select v-model="form.received_by" class="w-full p-2 text-center border rounded">
                <option disabled value="">{{ $t('distribution.select') }}</option>
                <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
              </select>
              <p class="text-sm text-red-500">{{ form.errors.received_by }}</p>
            </div>

            <div>
              <label>{{ $t('distribution.date') }}</label>
              <input type="date" v-model="form.date" class="w-full p-2 border rounded" />
              <p class="text-sm text-red-500">{{ form.errors.date }}</p>
            </div>

            <div class="col-span-2">
              <label>{{ $t('distribution.description') }}</label>
              <textarea v-model="form.description" class="w-full p-2 border rounded"></textarea>
              <p class="text-sm text-red-500">{{ form.errors.description }}</p>
            </div>
          </div>

          <button type="submit" class="px-4 py-2 mt-4 text-white bg-green-600 rounded hover:bg-green-700">
            {{ form.id ? $t('distribution.update') : $t('distribution.add') }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, router, usePage } from '@inertiajs/vue3'
import Layout from '../Layout.vue'

defineOptions({ layout: Layout })
const page = usePage()
const userRole = page.props.auth.user?.role
const props = defineProps({
  distributions: Array,
  employees: Array,
  departments: Array,
  storeItems: Array,
})

const modalOpen = ref(false)

const form = useForm({
  id: null,
  product_name: '',
  quantity: '',
  unit: '',
  distributed_by: '',
  received_by: '',
  date: '',
  description: '',
})

const openModal = (item = null) => {
  modalOpen.value = true
  if (item) {
    form.id = item.id
    form.product_name = item.product_name
    form.quantity = item.quantity
    form.unit = item.unit
    form.distributed_by = item.distributed_by
    form.received_by = item.received_by
    form.date = item.date
    form.description = item.description
  } else {
    form.reset()
    form.id = null
  }
}

const closeModal = () => {
  modalOpen.value = false
  form.clearErrors()
}

const submit = () => {
  if (form.id) {
    form.put(`/distributions/${form.id}`, {
      onSuccess: closeModal,
    })
  } else {
    form.post('/distributions', {
      onSuccess: closeModal,
    })
  }
}

const destroy = (id) => {
  if (confirm('Are you sure you want to delete this distribution?')) {
    router.delete(`/distributions/${id}`)
  }
}

// Returns the remaining quantity of a product + unit from store
const remainingQuantity = (productName, unit) => {
  const item = props.storeItems.find(i => i.product_name === productName && i.unit === unit)
  return item ? item.quantity : 0
}
</script>
