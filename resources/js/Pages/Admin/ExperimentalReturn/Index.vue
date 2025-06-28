

<template>
  <div class="p-2">
    <h1 class="mb-4 text-2xl font-bold dark:text-white">{{ $t('ex_return.title') }}</h1>

    <table class="min-w-full text-center border border-gray-300 rounded-lg shadow-sm">
      <thead class="text-sm text-gray-700 uppercase bg-gray-100 dark:text-white">
        <tr>
          <th class="p-2 bg-gray-300 border dark:bg-gray-700">{{ $t('ex_return.product') }}</th>
          <th class="p-2 bg-gray-300 border dark:bg-gray-700">{{ $t('ex_return.quantity') }}</th>
          <th class="p-2 bg-gray-300 border dark:bg-gray-700">{{ $t('ex_return.department') }}</th>
          <th class="p-2 bg-gray-300 border dark:bg-gray-700">{{ $t('ex_return.recipient') }}</th>
          <th class="p-2 bg-gray-300 border dark:bg-gray-700">{{ $t('ex_return.transfer_date') }}</th>
          <th class="p-2 bg-gray-300 border dark:bg-gray-700">{{ $t('ex_return.transfered_condition') }}</th>
          <th class="p-2 bg-gray-200 border dark:bg-gray-400">{{ $t('ex_return.return_condition') }}</th>
          <th class="p-2 bg-gray-200 border dark:bg-gray-400">{{ $t('ex_return.return_condition') }}</th>
          <th class="p-2 bg-gray-200 border dark:bg-gray-400">{{ $t('ex_return.return_description') }}</th>
          <th class="p-2 bg-gray-300 border dark:bg-gray-700">{{ $t('ex_return.status') }}</th>
          <th class="p-2 bg-gray-300 border dark:bg-gray-700">{{ $t('ex_return.actions') }}</th>
        </tr>
      </thead>
      <tbody>
        <template v-for="transfer in transfers" :key="transfer.id">
          <tr class="border-t dark:text-white">
            <td class="p-2 bg-gray-300 border dark:bg-gray-700">{{ transfer.product_name }}</td>
            <td class="p-2 bg-gray-300 border dark:bg-gray-700">{{ transfer.quantity }}</td>
            <td class="p-2 bg-gray-300 border dark:bg-gray-700">{{ transfer.department?.name }}</td>
            <td class="p-2 bg-gray-300 border dark:bg-gray-700">{{ transfer.recipient?.name }}</td>
            <td class="p-2 bg-gray-300 border dark:bg-gray-700">{{ formatDate(transfer.date) }}</td>
            <td class="p-2 bg-gray-300 border dark:bg-gray-700">{{ transfer.product_condition }}</td>

            <td class="p-2 bg-gray-200 border dark:bg-gray-400">
              {{ transfer.status === 'Returned' ? transfer.equipment_return?.returned_condition ?? '-' : '-' }}
            </td>
            <td class="p-2 bg-gray-200 border dark:bg-gray-400">
              {{ transfer.status === 'Returned' ? formatDate(transfer.equipment_return?.return_date) : '-' }}
            </td>
            <td class="p-2 bg-gray-200 border dark:bg-gray-400">
              {{ transfer.status === 'Returned' ? transfer.equipment_return?.description ?? '-' : '-' }}
            </td>
            <td class="p-2 bg-gray-300 border dark:bg-gray-700">
              <span :class="{
                'text-green-600': transfer.status === 'Returned',
                'text-yellow-600': transfer.status === 'In Working'
              }">
              {{ $t(`ex_return.${transfer.status}`) }}
              </span>
            </td>
            <td class="p-2 bg-gray-300 border dark:bg-gray-700">
              <button
                v-if="transfer.status === 'In Working'"
                @click="openReturnForm(transfer)"
                class="px-3 py-1 text-white bg-blue-500 rounded hover:bg-blue-600"
              >
              {{ $t('ex_return.return') }}
              </button>
              <span v-else class="text-sm text-gray-800">✅</span>
            </td>
          </tr>

          <!-- Return form row -->
          <tr v-if="showReturnForm === transfer.id">
            <td :colspan="11" class="p-4 bg-gray-100 border">
              <div class="flex flex-col items-start gap-4 md:flex-row ">
                <input v-model="form.return_date" type="date" class="p-2 border rounded" />
                <select v-model="form.returned_condition" class="p-2 border rounded w-[110px] text-center">
                  <option v-for="i in 5" :key="i" :value="i">{{ $t('ex_return.condition') }}  {{ i }}</option>
                </select>
                <input
                  v-model="form.description"
                  type="text"
                  :placeholder="$t('ex_return.description')"
                  class="flex-1 p-2 border rounded"
                />
                <button @click="submit" class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-600">
                 {{ $t('ex_return.return') }}
                </button>
                <button @click="showReturnForm = null" class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-700">
                  {{ $t('ex_return.cancel') }}
                </button>
              </div>
              <p class="mt-2 text-sm text-red-500" v-if="form.errors.transfer_id">{{ form.errors.transfer_id }}</p>
            </td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import Layout from '../Layout.vue'

defineOptions({ layout: Layout })

const props = defineProps({
  transfers: Array
})

const showReturnForm = ref(null)

const formatDate = (dateStr) => {
  const date = new Date(dateStr)
  const options = { month: 'long', day: 'numeric' }
  const formatted = date.toLocaleDateString('en-US', options)
  return formatted.replace(' ', ', ')
}

const form = useForm({
  transfer_id: null,
  return_date: new Date().toISOString().split('T')[0],
  returned_condition: 1,
  description: ''
})

const openReturnForm = (transfer) => {
  showReturnForm.value = transfer.id
  form.transfer_id = transfer.id
  form.return_date = transfer.equipment_return?.return_date ?? new Date().toISOString().split('T')[0]
  form.returned_condition = transfer.equipment_return?.returned_condition ?? 1
  form.description = transfer.equipment_return?.description ?? ''
}

const submit = () => {
  form.post(route('experimental-returns.store'), {
    preserveScroll: true,
    onSuccess: () => {
      showReturnForm.value = null
      form.reset()
    }
  })
}
</script>