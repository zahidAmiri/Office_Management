<template>
    <div class="relative overflow-x-auto">
      <!-- Back & Restore Buttons -->
      <Link href="/employees">
        <button class="p-2 px-6 mt-2 text-white bg-green-700 rounded-md hover:bg-green-800 rtl:float-left rtl:mb-2">{{ $t('employee.back') }}</button>
      </Link>
  
      <button
        @click="restoreAll"
        class="float-right p-2 mt-2 font-bold text-white bg-yellow-600 rounded-md hover:bg-green-800"
      >
        {{ $t('employee.restore_all') }}
      </button>
  
      <!-- Trashed Employees Table -->
      <table class="w-full mt-3 text-sm shadow-md">
        <thead class="text-xs text-black uppercase bg-gray-300 border-b border-gray-900">
          <tr>
            <th class="px-6 py-3">#</th>
            <th class="px-6 py-3">{{ $t('employee.name') }}</th>
            <th class="px-6 py-3">{{ $t('employee.department') }}</th>
            <th class="px-6 py-3">{{ $t('employee.contact') }}</th>
            <th class="px-6 py-3">{{ $t('employee.actions') }}</th>
          </tr>
        </thead>
        <tbody v-if="employees.length > 0">
          <tr v-for="(emp, index) in employees" :key="emp.id" class="bg-white border-b">
            <td class="px-6 py-4">{{ index + 1 }}</td>
            <td class="px-6 py-4">{{ emp.name }} {{ emp.last_name }}</td>
            <td class="px-6 py-4">{{ emp.department?.name ?? 'N/A' }}</td>
            <td class="px-6 py-4">{{ emp.contact_number }}</td>
            <td class="px-6 py-4">
              <PrimaryButton @click="restoreOne(emp.id)" class="px-2 mr-1 bg-green-600 hover:bg-green-700 rtl:ml-2">{{ $t('employee.restore_one') }}</PrimaryButton>
              <PrimaryButton @click="deleteOne(emp.id)" class="px-2 bg-red-600 hover:bg-red-700">{{ $t('employee.delete') }}</PrimaryButton>
            </td>
          </tr>
        </tbody>
        <tbody v-else>
          <tr>
            <td colspan="5" class="py-4 text-center text-gray-500">
              No trashed employees found!
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  
  <script setup>
  import { router, Link } from '@inertiajs/vue3'
  import PrimaryButton from "@/Components/PrimaryButton.vue"
  import Layout from "../Layout.vue"
  
  defineOptions({ layout: Layout })
  
  const props = defineProps({
    employees: Array,
  })
  
  const restoreAll = () => {
    router.get('/restore-trashed-employees')
  }
  
  const restoreOne = (id) => {
    router.get(`/restore-one-employee/${id}`)
  }
  
  const deleteOne = (id) => {
    router.delete(`/delete-one-employee/${id}`)
  }
  </script>
  