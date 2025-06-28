<template>
 <aside
  :dir="locale === 'en' ? 'ltr' : 'rtl'"
  class="w-64 h-full overflow-y-auto text-white bg-gray-700 shadow bg-gradient-to-b dark:bg-gray-900 dark:text-white"
>
    <Link href="/dashboard">
    <div class="p-4 text-2xl font-bold tracking-wide border-b border-gray-700">
      <img src="logo.png" alt="Logo" class="object-contain w-auto h-16">
    </div>
    </Link>

    <hr>
    <nav class="mt-4 space-y-2 ">


      <Link
        href="/dashboard"
        :class="[
    'flex items-center px-4 py-2 transition rounded-md hover:bg-gray-700',
    $page.url.startsWith('/dashboard') ? 'bg-gray-500 font-semibold' : ''
  ]"
      >
        <i class="mr-3 bi bi-house-door-fill rtl:pl-2"></i> {{ $t("sidebar.dashboard") }}
      </Link>


      <Link
        href="/departments"
        :class="[
        'flex items-center px-4 py-2 transition rounded-md hover:bg-gray-700',
        $page.url.startsWith('/departments') ? 'bg-gray-500 font-semibold' : ''
        ]"
      >
        <i class="mr-3 bi bi-diagram-3-fill rtl:pl-2"></i> {{ $t("sidebar.departments") }}
      </Link>

      <Link
        :href="route('employees.index')"
        :class="[
        'flex items-center px-4 py-2 transition rounded-md hover:bg-gray-700',
        $page.url.startsWith('/employees') ? 'bg-gray-500 font-semibold' : ''
        ]"
      >
        <i class="mr-3 bi bi-people-fill rtl:pl-2"></i> {{ $t("sidebar.employees") }}
      </Link>

      <Link
        :href="route('leave.index')"
        :class="[
        'flex items-center px-4 py-2 transition rounded-md hover:bg-gray-700',
        $page.url.startsWith('/leave') ? 'bg-gray-500 font-semibold' : ''
        ]"
      >
        <i class="mr-3 bi bi-clipboard-check rtl:pl-2"></i> {{ $t("sidebar.leave_allocation") }}
      </Link>

      <Link
        :href="route('addleave.index')"
        :class="[
        'flex items-center px-4 py-2 transition rounded-md hover:bg-gray-700',
        $page.url.startsWith('/addleave') ? 'bg-gray-500 font-semibold' : ''
        ]"
      >
        <i class="mr-3 bi bi-calendar-check rtl:pl-2"></i> {{ $t("sidebar.leave") }}
      </Link>

      <Link
        :href="route('supplies.index')"
        :class="[
        'flex items-center px-4 py-2 transition rounded-md hover:bg-gray-700',
        $page.url.startsWith('/supplies') ? 'bg-gray-500 font-semibold' : ''
        ]"
      >
        <i class="mr-3 bi bi-people-fill rtl:pl-2"></i> {{ $t("sidebar.supply") }}
      </Link>

      <Link
        :href="route('store.index')"
        :class="[
        'flex items-center px-4 py-2 transition rounded-md hover:bg-gray-700',
        $page.url.startsWith('/store') ? 'bg-gray-500 font-semibold' : ''
        ]"
      >
        <i class="mr-3 bi bi-shop rtl:pl-2"></i> {{ $t("sidebar.store") }}
      </Link>

<!-- Sidebar.vue (snippet) -->
<Link
  :href="route('used-store.index')"
  :class="[
    'flex items-center px-4 py-2 transition rounded-md hover:bg-gray-700',
    $page.url.startsWith('/used-store') ? 'bg-gray-500 font-semibold' : ''
  ]">
  <i class="mr-3 bi bi-shop rtl:pl-2"></i>
  {{ $t('sidebar.used_store', 'Used Store') }}
</Link>



      <Link
        :href="route('distributions.index')"
        :class="[
        'flex items-center px-4 py-2 transition rounded-md hover:bg-gray-700',
        $page.url.startsWith('/distributions') ? 'bg-gray-500 font-semibold' : ''
        ]"
      >
        <i class="mr-3 bi bi-truck rtl:pl-2"></i> {{ $t("sidebar.distribution") }}
      </Link>

      <Link
        :href="route('experimental-transfers.index')"
        :class="[
        'flex items-center px-4 py-2 transition rounded-md hover:bg-gray-700',
        $page.url.startsWith('/experimental-transfers') ? 'bg-gray-500 font-semibold' : ''
        ]"
      >
        <i class="mr-3 bi bi-gear-wide-connected rtl:pl-2"></i> {{ $t("sidebar.transfer") }}
      </Link>

      <Link
        :href="route('experimental-return.index')"
        :class="[
        'flex items-center px-4 py-2 transition rounded-md hover:bg-gray-700',
        $page.url.startsWith('/experimental-return') ? 'bg-gray-500 font-semibold' : ''
        ]"
      >
        <i class="mr-3 bi bi-arrow-counterclockwise rtl:pl-2"></i> {{ $t("sidebar.return") }}
      </Link>

      <Link
        v-if="userRole === 'manager'"
        :href="route('register')"
        :class="[
        'flex items-center px-4 py-2 transition rounded-md hover:bg-gray-700',
        $page.url.startsWith('/register') ? 'bg-gray-500 font-semibold' : ''
        ]"
      >
        <i class="mr-3 bi bi-person-plus-fill rtl:pl-2"></i> {{ $t("sidebar.register") }}
      </Link>

      <Link
        href="/backup"
        class="flex items-center px-4 py-2 transition rounded-md hover:bg-gray-700"
      >
        <i class="mr-3 rtl:pl-2 fa-solid fa-trash-arrow-up"></i> {{ $t("sidebar.setting") }}
      </Link>
    </nav>
  </aside>
</template>

<script setup>
import { Link, usePage } from "@inertiajs/vue3";
const page = usePage();
const userRole = page.props.auth.user?.role;

import { useI18n } from "vue-i18n";
const { locale } = useI18n();
</script>
