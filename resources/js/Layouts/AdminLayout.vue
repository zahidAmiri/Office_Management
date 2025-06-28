<template>
  <div class="flex h-screen bg-gray-100">
    <Sidebar />

    <div class="flex flex-col flex-1 overflow-hidden">
      <Navbar />

      <main class="p-6 overflow-y-auto">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import Sidebar from '@/Components/Sidebar.vue'
import Navbar from '@/Components/Navbar.vue'
</script>
