<template>
  <div class="relative p-4 overflow-x-auto">
    <!-- Add New Leave Request Button -->
    <div class="flex items-center justify-between mb-4">
      <button
      v-if="userRole === 'manager' || userRole === 'office_admin'"
       @click="openAddModal" class="px-4 py-2 font-bold text-white bg-green-700 rounded hover:bg-green-800">
       {{ $t('leave.add') }}
      </button>

      <a
      v-if="userRole === 'manager' || userRole === 'office_admin'"
            :href="`add_leave/print_all?lang=${locale}`"
            target="_blank"
            class="p-2 px-3 text-white bg-yellow-600 rounded-md hover:bg-yellow-700"
        >
            🖨️ {{ $t('leave.print') }}
        </a>

    </div>

    <!-- Leave Requests Table -->
    <table class="w-full text-sm text-center border shadow-md dark:text-white">
      <thead class="text-xs text-black uppercase bg-gray-400 border-b dark:bg-gray-700 dark:text-white">
        <tr>
          <th class="px-3 py-2">#</th>
          <th class="px-3 py-2">{{ $t('leave.employee') }}</th>
          <th class="px-3 py-2">{{ $t('leave.leave_type') }}</th>
          <th class="px-3 py-2">{{ $t('leave.total_days') }}</th>
          <th class="px-3 py-2">{{ $t('leave.date') }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(leave, index) in leaveRequests" :key="leave.id"
        class=" odd:bg-gray-200 even:bg-gray-300 dark:even:bg-gray-500 dark:odd:bg-gray-600">
          <td class="px-3 py-2">{{ index + 1 }}</td>
          <td class="px-3 py-2">{{ leave.employee?.name || 'N/A' }}</td>
          <td class="px-3 py-2">{{ leave.leave_type?.name || 'N/A' }}</td>
          <td class="px-3 py-2">{{ leave.days || 0 }}</td>
          <td class="px-3 py-2">{{ currentDate }}</td>
        </tr>
      </tbody>
    </table>

    <!-- Error Message for Insufficient Leave Days -->
    <div v-if="errorMessage" class="mt-4 text-red-500">{{ errorMessage }}</div>

    <!-- Custom Modal -->
    <transition name="fade">
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="relative w-full max-w-2xl p-6 bg-white rounded-lg shadow-xl">
          <button @click="cancel" class="absolute text-xl text-gray-500 top-2 right-2 hover:text-gray-700">✕</button>
          <h2 class="mb-4 text-lg font-semibold">{{ $t('leave.add') }}</h2>
          <form @submit.prevent="submitLeaveRequest" class="space-y-4">
            <div>
              <label class="block text-sm font-medium">{{ $t('leave.employee') }}</label>
              <select v-model="newLeave.employee_id" class="w-full p-2 text-center border rounded" required>
                <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                  {{ employee.name }} {{ employee.last_name }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium">{{ $t('leave.leave_type') }}</label>
              <select v-model="newLeave.leave_type_id" class="w-full p-2 text-center border rounded" required>
                <option v-for="type in leaveTypes" :key="type.id" :value="type.id">
                  {{ type.name }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium">{{ $t('leave.days') }}</label>
              <input v-model="newLeave.days" type="number" class="w-full p-2 text-center border rounded" required />
            </div>
            <div>
              <label class="block text-sm font-medium">{{ $t('leave.start_date') }}</label>
              <input v-model="newLeave.start_date" type="date" class="w-full p-2 text-center border rounded" required />
            </div>
            <div>
              <label class="block text-sm font-medium">{{ $t('leave.end_date') }}</label>
              <input v-model="newLeave.end_date" type="date" class="w-full p-2 text-center border rounded" required />
            </div>
            <div class="flex justify-end space-x-2">
              <button type="button" @click="cancel" class="px-4 py-2 text-black bg-gray-400 rounded rtl:ml-2">{{ $t('leave.cancel') }}</button>
              <button type="submit" class="px-4 py-2 text-white bg-green-700 rounded hover:bg-green-800">{{ $t('leave.save') }}</button>
            </div>
          </form>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, watchEffect } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import Layout from "../Layout.vue";

import { useI18n } from 'vue-i18n';
const { locale } = useI18n();

const currentDate = new Date().toLocaleDateString('en-GB'); // format: DD/MM/YYYY or adjust as needed


const page = usePage()
const userRole = page.props.auth.user?.role;

defineOptions({
  layout: Layout,
});

const showModal = ref(false);
const leaveRequests = ref([]);
const employees = ref([]);
const leaveTypes = ref([]);
const newLeave = ref({
  employee_id: "",
  leave_type_id: "",
  days: "",
  start_date: "",
  end_date: "",
});
const errorMessage = ref("");

const { props } = usePage();
leaveRequests.value = props.leaveRequests || [];
employees.value = props.employees || [];
leaveTypes.value = props.leaveTypes || [];

const openAddModal = () => {
  showModal.value = true;
};

const cancel = () => {
  showModal.value = false;
  errorMessage.value = "";
};

const submitLeaveRequest = () => {
  router.post("/addleave", newLeave.value, {
    onSuccess: () => {
      cancel();
    },
    onError: (errors) => {
      if (errors.error) {
        errorMessage.value = errors.error;
      }
    },
  });
};

watchEffect(() => {
  leaveRequests.value = props.leaveRequests || [];
  employees.value = props.employees || [];
  leaveTypes.value = props.leaveTypes || [];
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
