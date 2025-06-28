<template>
  <div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold dark:text-white">{{ $t('leave_allocation.title') }}</h1>

      <a
      v-if="userRole === 'manager' || userRole === 'office_admin'"
            :href="`leave_allocation/print_all?lang=${locale}`"
            target="_blank"
            class="p-2 px-3 text-white bg-yellow-600 rounded-md hover:bg-yellow-700"
        >
            🖨️ {{ $t('leave_allocation.print') }}
        </a>

      <button
        v-if="userRole === 'manager' || userRole === 'office_admin'"
        @click="showForm = true"
        class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700"
      >
      ✚ {{ $t('leave_allocation.add') }}
      </button>


    </div>

    <!-- Modal Form -->
    <div
      v-if="showForm"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    >
      <div class="w-full max-w-md p-6 space-y-4 bg-white rounded-lg shadow-lg">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-semibold">{{ $t('leave_allocation.add') }}</h2>
          <button
            @click="closeModal"
            class="text-xl font-bold text-gray-600 hover:text-gray-900"
          >
            &times;
          </button>
        </div>

        <!-- Employee Select -->
        <div>
          <select v-model="form.employee_id" class="w-full p-2 text-center border rounded">
            <option disabled value="">-- {{ $t('leave_allocation.select_employee') }} --</option>
            <option v-for="emp in employees" :key="emp.id" :value="emp.id">
              {{ emp.name }} {{ emp.last_name }}
            </option>
          </select>
          <p v-if="form.errors.employee_id" class="mt-1 text-sm text-red-600">
            {{ form.errors.employee_id }}
          </p>
        </div>

        <!-- Leave Type Select -->
        <div>
          <select v-model="form.leave_type_id" class="w-full p-2 text-center border rounded">
            <option disabled value="">-- {{ $t('leave_allocation.select_leave_type') }} --</option>
            <option v-for="type in leaveTypes" :key="type.id" :value="type.id">
              {{ type.name }}
            </option>
          </select>
          <p v-if="form.errors.leave_type_id" class="mt-1 text-sm text-red-600">
            {{ form.errors.leave_type_id }}
          </p>
        </div>

        <!-- Total Days -->
        <div>
          <input
            type="number"
            v-model="form.total_days"
            :placeholder="$t('leave_allocation.total_days')"
            class="w-full p-2 text-center border rounded"
          />
          <p v-if="form.errors.total_days" class="mt-1 text-sm text-red-600">
            {{ form.errors.total_days }}
          </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-2">
          <button
            @click="closeModal"
            class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 rtl:ml-2"
          >
          {{ $t('leave_allocation.cancel') }}
          </button>
          <button
            @click="submitAllocation"
            :disabled="form.processing"
            class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700"
          >
          {{ $t('leave_allocation.save') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Allocation Table -->
    <table class="w-full text-sm text-center border shadow-md dark:text-white">
      <thead class="text-xs text-black uppercase bg-gray-500 border-b dark:text-white dark:bg-gray-800">
        <tr>
          <th class="px-3 py-2">#</th>
          <th class="px-3 py-2">{{ $t('leave_allocation.employee') }}</th>
          <th class="px-3 py-2">{{ $t('leave_allocation.leave_type') }}</th>
          <th class="px-3 py-2">{{ $t('leave_allocation.total_days') }}</th>
          <th class="px-3 py-2">{{ $t('leave_allocation.used_days') }}</th>
          <th class="px-3 py-2">{{ $t('leave_allocation.remaining_days') }}</th>
        </tr>
      </thead>
      <tbody v-if="employees.length > 0">
        <template v-for="(employee, index) in employees" :key="employee.id">
          <tr
            v-for="allocation in employee.leave_allocations"
            :key="allocation.id"
            class="border-b bg-gradient-to-r even:bg-gray-200 odd:bg-gray-300 dark:even:bg-gray-500 dark:odd:bg-gray-700"
          >
            <td class="px-3 py-2">{{ index + 1 }}</td>
            <td class="px-3 py-2">{{ employee.name }}</td>
            <td class="px-3 py-2">{{ allocation.leave_type?.name || 'N/A' }}</td>
            <td class="px-3 py-2">{{ allocation.total_days }}</td>
            <td class="px-3 py-2">{{ allocation.used_days || 0 }}</td>
            <td class="px-3 py-2">
              {{ allocation.total_days - (allocation.used_days || 0) }}
            </td>
          </tr>
        </template>
      </tbody>
      <tbody v-else>
        <tr>
          <td colspan="6" class="py-4 text-center text-gray-500">
            {{ $t('leave_allocation.not_found') }}
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Filter UI -->
    <div class="grid grid-cols-1 gap-4 mt-8 md:grid-cols-3 dark:text-white">

      <div>
        <label for="employee" class="block mb-1 font-medium ">{{ $t('leave_allocation.select_employee') }}:</label>
        <select v-model="selectedEmployee" class="w-full p-2 text-center border rounded dark:bg-gray-700">
          <option disabled value="">-- {{ $t('leave_allocation.select_employee') }} --</option>
          <option v-for="emp in employees" :key="emp.id" :value="emp.id">{{ emp.name }}</option>
        </select>
      </div>

      <div>
        <label for="leaveType" class="block mb-1 font-medium ">{{ $t('leave_allocation.select_employee') }}:</label>
        <select v-model="selectedLeaveType" class="w-full p-2 text-center border rounded dark:bg-gray-700">
          <option disabled value="">-- {{ $t('leave_allocation.select_leave_type') }} --</option>
          <option v-for="type in leaveTypes" :key="type.id" :value="type.id">{{ type.name }}</option>
        </select>
      </div>

      <div v-if="selectedEmployee && selectedLeaveType">
        <label class="block mb-1 font-medium">{{ $t('leave_allocation.remaining_days') }}:</label>
        <p class="px-2 py-1 font-semibold rounded">
          {{ getRemainingLeavesForEmployee(selectedEmployee, selectedLeaveType) }} {{ $t('leave_allocation.days') }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import Layout from '../Layout.vue';

import { useI18n } from 'vue-i18n';
const { locale } = useI18n();

defineOptions({ layout: Layout });

const page = usePage()
const userRole = page.props.auth.user?.role;

const props = defineProps({
  employees: Array,
  leaveTypes: Array,
});

const showForm = ref(false);
const selectedEmployee = ref('');
const selectedLeaveType = ref('');

// Inertia form setup
const form = useForm({
  employee_id: '',
  leave_type_id: '',
  total_days: '',
});

const closeModal = () => {
  showForm.value = false;
  form.reset();
  form.clearErrors();
};

const submitAllocation = () => {
  form.post('/leave', {
    onSuccess: () => {
      closeModal();
    },
  });
};

const getRemainingLeavesForEmployee = (employeeId, leaveTypeId) => {
  const employee = props.employees.find(e => e.id === employeeId);
  if (!employee || !employee.leave_allocations) return 0;

  const allocation = employee.leave_allocations.find(
    a => a.leave_type_id === leaveTypeId
  );

  if (!allocation) return 0;

  const used = allocation.used_days || 0;
  return (allocation.total_days || 0) - used;
};
</script>
