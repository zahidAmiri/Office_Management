<template>
    <div class="min-h-screen p-6 bg-white dark:bg-gray-900">
      <h1 class="mb-6 text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">
        {{ $t('used_store.title', 'Used Store') }}
      </h1>

      <div class="overflow-x-auto border border-gray-200 rounded-lg shadow-lg dark:border-gray-700">
        <table class="min-w-full text-sm text-left bg-white dark:bg-gray-800">
          <thead class="text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-300">
            <tr>
              <th class="px-6 py-3 font-semibold">#</th>
              <th class="px-6 py-3 font-semibold">{{ $t('used_store.product', 'Product') }}</th>
              <th class="px-6 py-3 font-semibold">{{ $t('used_store.unit', 'Unit') }}</th>
              <th class="px-6 py-3 font-semibold">{{ $t('used_store.quantity', 'Quantity') }}</th>
              <th class="px-6 py-3 font-semibold">{{ $t('used_store.condition', 'Condition') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(item, idx) in usedItems"
              :key="item.id"
              :class="[
                'border-t dark:border-gray-700',
                item.condition === 5
                  ? 'bg-red-100 text-red-900 dark:bg-red-500 dark:text-white font-semibold'
                  : 'hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 transition-colors duration-150'
              ]"
            >
              <td class="px-6 py-4">{{ idx + 1 }}</td>
              <td class="px-6 py-4">{{ item.product_name }}</td>
              <td class="px-6 py-4">{{ item.unit }}</td>
              <td class="px-6 py-4">{{ item.quantity }}</td>
              <td class="px-6 py-4">Lvl&nbsp;{{ item.condition }}</td>
            </tr>

            <tr v-if="usedItems.length === 0">
              <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                {{ $t('used_store.no_stock', 'No used stock yet') }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </template>
  <script setup>
    import Layout from '../Layout.vue';
    defineOptions({
        layout: Layout
    })
  defineProps({ usedItems: Array })
  </script>
