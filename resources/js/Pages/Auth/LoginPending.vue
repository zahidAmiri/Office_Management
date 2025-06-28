<script setup>
import { onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

const checkApproval = async () => {
    const res = await axios.get('/login/check-approval')
    if (res.data.approved) {
        router.visit('/dashboard') // or your landing route
    } else {
        setTimeout(checkApproval, 3000)
    }
}

onMounted(() => {
    checkApproval()
})
</script>

<template>
  <div class="flex flex-col items-center justify-center min-h-screen space-y-4">
    <h1 class="text-2xl font-bold text-gray-800">Waiting for Admin Approval</h1>
    <p class="text-gray-600">Please wait while we notify the admin...</p>
    <div class="w-8 h-8 border-4 border-blue-500 rounded-full animate-spin border-t-transparent"></div>
  </div>
</template>
