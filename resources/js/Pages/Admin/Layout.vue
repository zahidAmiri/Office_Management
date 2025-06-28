<template>
  <div :dir="locale === 'en' ? 'ltr' : 'rtl'" class="flex h-screen overflow-hidden font-myfont dark:bg-gray-800">
    <!-- Sidebar -->
    <Sidebar class="flex-shrink-0 w-64 h-full overflow-y-auto text-white bg-gray-800" />

    <!-- Main content area -->
    <div class="flex flex-col flex-1 overflow-hidden">
      <Navbar />
      <main class="flex-1 p-6 pt-20 overflow-y-auto">
        <slot />
      </main>
    </div>
  </div>
</template>


<script setup>
import Sidebar from '@/Components/Admin/Sidebar.vue';
import Navbar from '@/Components/Admin/Navbar.vue';

import { useI18n } from "vue-i18n";
const { locale } = useI18n();

</script>
