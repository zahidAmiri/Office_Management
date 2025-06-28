<template>
    <div class="min-h-screen p-8 space-y-10 text-center"
    :dir="locale === 'en'? 'ltr' : 'rtl'"
    >
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">{{ $t('dashboard.title') }}</h1>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        
          <Link :href="route('employees.index')">
  <div class="p-6 transition duration-200 bg-blue-200 rounded-lg shadow hover:shadow-lg hover:bg-blue-300">
    <h3 class="text-lg font-bold text-blue-800">{{ $t('dashboard.employees') }}</h3>
    <p class="mt-2 text-2xl font-semibold text-blue-900">
      {{ stats.employees_count }}
    </p>
  </div>
</Link>

          <Link :href="route('departments.index')">
            <div class="p-6 bg-green-200 rounded-lg shadow hover:bg-green-300">
                <h3 class="text-lg font-bold text-green-800">{{ $t('dashboard.departments') }}</h3>
                <p class="mt-2 text-2xl font-semibold text-green-900">
                    {{ stats.departments_count }}
                </p>
            </div>

          </Link>
          
          
          <Link :href="route('leave.index')">
            <div class="p-6 bg-yellow-100 rounded-lg shadow hover:bg-yellow-200">
                <h3 class="text-lg font-bold text-yellow-800">
                    {{ $t('dashboard.leaves') }}
                </h3>
                <p class="mt-2 text-2xl font-semibold text-yellow-900">
                    {{ stats.pending_leaves }}
                </p>
            </div>
          </Link>
          
          
          <Link :href="route('supplies.index')">
            <div class="p-6 bg-red-200 rounded-lg shadow hover:bg-red-300">
                <h3 class="text-lg font-bold text-red-800">{{ $t('dashboard.supplies') }}</h3>
                <p class="mt-2 text-2xl font-semibold text-red-900">
                    {{ stats.supplies_count }}
                </p>
            </div>
          </Link>
          

          <Link :href="route('store.index')">
            <div class="p-6 bg-purple-200 rounded-lg shadow hover:bg-purple-300">
                <h3 class="text-lg font-bold text-purple-800">{{ $t('dashboard.store_items') }}</h3>
                <p class="mt-2 text-2xl font-semibold text-purple-900">
                    {{ stats.store_items }}
                </p>
            </div>
          </Link>
          
          
          <Link :href="route('distributions.index')">
            <div class="p-6 bg-indigo-200 rounded-lg shadow hover:bg-indigo-300">
                <h3 class="text-lg font-bold text-indigo-800">{{ $t('dashboard.distributions') }}</h3>
                <p class="mt-2 text-2xl font-semibold text-indigo-900">
                    {{ stats.distributions }}
                </p>
            </div>
          </Link>
          
          
          <Link :href="route('experimental-transfers.index')">
            <div class="p-6 bg-teal-100 rounded-lg shadow hover:bg-teal-200">
                <h3 class="text-lg font-bold text-teal-800">{{ $t('dashboard.transfer') }}</h3>
                <p class="mt-2 text-2xl font-semibold text-teal-900">
                    {{ stats.transfers }}
                </p>
            </div>
          </Link>
          

          <Link :href="route('experimental-return.index')">
            <div class="p-6 bg-orange-200 rounded-lg shadow hover:bg-orange-300">
                <h3 class="text-lg font-bold text-orange-800">{{ $t('dashboard.return') }}</h3>
                <p class="mt-2 text-2xl font-semibold text-orange-900">
                    {{ stats.returns }}
                </p>
            </div>
          </Link>
          

            <div
                v-if="stats.users !== null"
                class="p-6 bg-gray-300 rounded-lg shadow sm:col-span-2 lg:col-span-1"
            >
                <h3 class="text-lg font-bold text-gray-800">{{ $t('dashboard.users') }}</h3>
                <p class="mt-2 text-2xl font-semibold text-gray-900">
                    {{ stats.users }}
                </p>
            </div>
        </div>

        <section>
  <h2 class="pb-2 mb-6 text-2xl font-semibold text-gray-800 border-b border-gray-300">
    {{ $t('dashboard.recent_employees') }}
  </h2>
  <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-gray-500 text-md dark:text-gray-400">
      <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-500 dark:text-gray-300">
        <tr>
          <th scope="col" class="px-6 py-3">
            {{ $t('dashboard.name') }}
          </th>
          <th scope="col" class="px-6 py-3">
            {{ $t('dashboard.date') }}
          </th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="emp in recent_employees"
          :key="emp.id"
          class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
        >
          <th
            scope="row"
            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"
          >
            {{ emp.name }}
          </th>
          <td class="px-6 py-4">
            {{ new Date(emp.created_at).toLocaleDateString() }}
          </td>
        </tr>
        <tr v-if="recent_employees.length === 0">
          <td colspan="2" class="px-6 py-4 italic text-center text-gray-500 dark:text-gray-400">
            No recent employees found.
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</section>

    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import Layout from "./Layout.vue";

import { useI18n } from "vue-i18n";
const { locale } = useI18n();


defineOptions({
    layout: Layout,
});
const props = defineProps({
    stats: Object,
    recent_employees: {
        type: Array,
        default: () => [],
    },
});
</script>
