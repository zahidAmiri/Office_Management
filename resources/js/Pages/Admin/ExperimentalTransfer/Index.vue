<template>
    <div class="relative p-6 overflow-x-auto">
      <!-- HEADER -->
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold dark:text-white">{{ $t('ex_distribution.title') }}</h1>
        <button
          v-if="isAdmin"
          @click="openModal"
          class="px-4 py-2 font-semibold text-white bg-green-600 rounded shadow hover:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:bg-green-700 dark:hover:bg-green-600 dark:active:bg-green-800 dark:focus:ring-green-400"
        >
          + {{ $t('ex_distribution.distribute') }}
        </button>
      </div>

      <!-- TABLE -->
      <table class="w-full overflow-hidden text-sm text-center border border-gray-300 rounded-lg shadow-sm dark:text-white dark:border-gray-600">
        <thead class="text-xs font-semibold tracking-wider text-gray-700 uppercase bg-gray-100 dark:text-gray-200 dark:bg-gray-700">
          <tr>
            <th class="py-2">#</th>
            <th class="py-2">{{ $t('ex_distribution.product') }}</th>
            <th class="py-2">{{ $t('ex_distribution.recipient') }}</th>
            <th class="py-2">{{ $t('ex_distribution.distributed_by') }}</th>
            <th class="py-2">{{ $t('ex_distribution.quantity') }}</th>
            <th class="py-2">{{ $t('ex_distribution.condition') }}</th>
            <th class="py-2">{{ $t('ex_distribution.date') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(t,i) in transfers"
            :key="t.id"
            class="transition-colors border-b border-gray-200 even:bg-gray-50 hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-700 dark:odd:bg-gray-400 dark:even:bg-gray-500"
          >
            <td class="py-2">{{ i + 1 }}</td>
            <td class="py-2">{{ t.product_name }}</td>
            <td class="py-2">{{ t.recipient?.name || 'N/A' }}</td>
            <td class="py-2">{{ t.distributor?.name || 'N/A' }}</td>
            <td class="py-2">{{ t.quantity }}</td>
            <td class="py-2">{{ t.product_condition }}</td>
            <td class="py-2">{{ t.date }}</td>
          </tr>
          <tr v-if="!transfers.length">
            <td colspan="7" class="py-6 text-center text-gray-500 dark:text-gray-400">No transfers found</td>
          </tr>
        </tbody>
      </table>

      <!-- MODAL -->
      <Modal :show="showModal" @close="cancel" maxWidth="lg">
        <div class="p-6 bg-white shadow-xl dark:bg-gray-800 rounded-xl ring-1 ring-gray-900/10 dark:ring-gray-700/50 backdrop-blur-sm">
          <h2 class="mb-4 text-xl font-semibold text-center dark:text-white">{{ $t('ex_distribution.title') }}</h2>

          <form @submit.prevent="submitForm" class="space-y-4">
            <!-- SOURCE -->
            <select v-model="form.source" class="w-full p-2 bg-white border border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
              <option value="store">{{ $t('ex_distribution.from_new') }}</option>
              <option value="used">{{ $t('ex_distribution.from_used') }}</option>
            </select>

            <!-- DEPARTMENT -->
            <select v-model="form.department_id" class="w-full p-2 text-center bg-white border border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
              <option disabled value="">{{ $t('ex_distribution.select_department') }}</option>
              <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
            </select>
            <InputError :message="form.errors.department_id" />

            <!-- PRODUCT -->
            <select v-model="form.product_name" class="w-full p-2 text-center bg-white border border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
              <option disabled value="">{{ $t('ex_distribution.select_product') }}</option>
              <option v-for="item in productOptions" :key="item.id" :value="item.product_name">
                {{ item.product_name }} ({{ item.quantity }} {{ item.unit }}<template v-if="form.source==='used'"> | lvl {{ item.condition }}</template>)
              </option>
            </select>
            <InputError :message="form.errors.product_name" />

            <!-- UNIT -->
            <select v-model="form.unit" class="w-full p-2 text-center bg-white border border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
              <option disabled value="">{{ $t('ex_distribution.select_unit') }}</option>
              <option v-for="item in productOptions.filter(i => i.product_name === form.product_name)" :key="item.id + '-u'" :value="item.unit">{{ item.unit }}</option>
            </select>
            <InputError :message="form.errors.unit" />

            <!-- QUANTITY -->
            <input v-model.number="form.quantity" type="number" min="1" class="w-full p-2 bg-white border border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400" :placeholder="$t('ex_distribution.quantity')" />
            <InputError :message="form.errors.quantity" />

            <!-- CONDITION -->
            <template v-if="form.source==='store'">
              <input v-model.number="form.product_condition" type="number" min="1" max="5" class="w-full p-2 bg-white border border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400" :placeholder="$t('ex_distribution.condition')" />
            </template>
            <template v-else>
              <select v-model="form.condition" class="w-full p-2 text-center bg-white border border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                <option v-for="lvl in availableLevels" :key="'lvl' + lvl" :value="lvl">{{ $t('ex_distribution.used_level') }} {{ lvl }}</option>
              </select>
              <InputError v-if="!availableLevels.length" message="No reusable levels" />
            </template>

            <!-- TYPE -->
            <input v-model="form.product_type" class="w-full p-2 bg-white border border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400" :placeholder="$t('ex_distribution.type')" />

            <!-- RECIPIENT -->
            <select v-model="form.recipient_id" class="w-full p-2 text-center bg-white border border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
              <option disabled value="">{{ $t('ex_distribution.select_recipient') }}</option>
              <option v-for="e in employees" :key="e.id" :value="e.id">{{ e.name }}</option>
            </select>
            <InputError :message="form.errors.recipient_id" />

            <!-- DISTRIBUTOR -->
            <select v-model="form.distributed_by" class="w-full p-2 text-center bg-white border border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
              <option disabled value="">{{ $t('ex_distribution.select_distributer') }}</option>
              <option v-for="e in employees" :key="e.id" :value="e.id">{{ e.name }}</option>
            </select>
            <InputError :message="form.errors.distributed_by" />

            <!-- DATE -->
            <input v-model="form.date" type="date" class="w-full p-2 bg-white border border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400" />

            <!-- DESC -->
            <textarea v-model="form.description" class="w-full p-2 bg-white border border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400" :placeholder="$t('ex_distribution.description')" />

            <!-- ACTIONS -->
            <div class="flex justify-end gap-2">
              <button type="button" @click="cancel" class="px-4 py-2 font-semibold text-gray-700 bg-gray-200 rounded hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 dark:bg-gray-600 dark:text-gray-100 dark:hover:bg-gray-500 dark:active:bg-gray-600 dark:focus:ring-gray-500">
                {{ $t('ex_distribution.cancel') }}
              </button>
              <button type="submit" class="px-4 py-2 font-semibold text-white bg-blue-600 rounded hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-blue-400 disabled:opacity-50 disabled:cursor-not-allowed" :disabled="form.processing">
                {{ $t('ex_distribution.add') }}
              </button>
            </div>
          </form>
        </div>
      </Modal>
    </div>
  </template>

  <script setup>
  import { ref, computed, watch } from 'vue'
  import { useForm, usePage } from '@inertiajs/vue3'
  import Modal from '@/Components/Modal.vue'
  import InputError from '@/Components/InputError.vue'
  import Layout from '../Layout.vue'
  defineOptions({ layout: Layout })

  const page = usePage()
  const isAdmin = ['manager', 'office_admin'].includes(page.props.auth.user?.role)

  const props = defineProps({
    transfers: Array,
    departments: Array,
    employees: Array,
    storeItems: Array,
    usedItems: Array,
  })

  const showModal = ref(false)

  const form = useForm({
    source: 'store',
    department_id: '',
    product_name: '',
    product_type: '',
    unit: '',
    quantity: '',
    recipient_id: '',
    distributed_by: '',
    date: '',
    product_condition: 1,
    condition: 2,
    description: '',
  })

  const productOptions = computed(() =>
    form.source === 'store'
      ? props.storeItems
      : props.usedItems.filter(i => i.condition > 1 && i.condition < 5)
  )

  const availableLevels = computed(() => {
    if (form.source !== 'used') return []
    return [...new Set(
      props.usedItems
        .filter(
          i =>
            i.product_name === form.product_name &&
            i.unit === form.unit &&
            i.condition > 1 &&
            i.condition < 5
        )
        .map(i => i.condition)
    )]
  })

  watch([() => form.source, () => form.product_name], () => {
    form.unit = ''
    if (form.source === 'used') {
      form.condition = availableLevels.value[0] ?? null
    }
  })

  const openModal = () => {
    form.reset()
    form.source = 'store'
    showModal.value = true
  }

  const cancel = () => {
    form.reset()
    form.clearErrors()
    showModal.value = false
  }

  const submitForm = () => {
    form.post('/experimental-transfers', {
      preserveScroll: true,
      onSuccess: cancel,
    })
  }
  </script>
