<template>
    <div class="p-6">
      <h1 class="mb-4 text-2xl font-bold dark:text-white">{{ $t('store.title') }}</h1>
  
      <table class="w-full overflow-hidden text-sm text-center text-gray-700 border border-gray-300 rounded-md shadow-md dark:text-white dark:border-gray-600">
  <thead class="text-xs text-gray-600 uppercase bg-gray-100 dark:bg-gray-500 dark:text-white">
    <tr>
      <th class="px-6 py-3">#</th>
      <th class="px-6 py-3">{{ $t('store.product_name') }}</th>
      <th class="px-6 py-3">{{ $t('store.quantity') }}</th>
      <th class="px-6 py-3">{{ $t('store.unit') }}</th>
      <th class="px-6 py-3">{{ $t('store.description') }}</th>
    </tr>
  </thead>
  <tbody>
    <tr
      v-for="(item, index) in stores"
      :key="item.id"
      class="bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-600 even:bg-gray-50 even:dark:bg-gray-700"
    >
      <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
        {{ index + 1 }}
      </td>
      <td class="px-6 py-4">{{ item.product_name }}</td>
      <td class="px-6 py-4">{{ item.quantity }}</td>
      <td class="px-6 py-4">{{ item.unit }}</td>
      <td class="px-6 py-4">{{ item.description }}</td>
    </tr>
  </tbody>
</table>

    </div>
  </template>
  
  <script setup>
  import Layout from "../Layout.vue";
  defineOptions({ layout: Layout });

  import { defineProps } from 'vue';
  
  defineProps({
    stores: Array
  });
  </script>
  