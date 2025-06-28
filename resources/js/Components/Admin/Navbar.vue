<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";

import { switchTheme } from '../../Theme.js'

// Import raw JSON language files for correct language names
import enMessages from "../../lang/en.json";
import paMessages from "../../lang/pa.json";

const page = usePage();
const userRole = page.props.auth.user?.role;

const userDropdownOpen = ref(false);
const toggleUserDropdown = () => {
    userDropdownOpen.value = !userDropdownOpen.value;
};
const closeUserDropdown = () => {
    userDropdownOpen.value = false;
};

const langDropdownOpen = ref(false);
const toggleLangDropdown = () => {
    langDropdownOpen.value = !langDropdownOpen.value;
};
const closeLangDropdown = () => {
    langDropdownOpen.value = false;
};

const { locale, t } = useI18n();

// Use raw JSON strings for language names (not translated by current locale)
const languages = {
    en: enMessages.language.english, // "English"
    pa: paMessages.language.pashto, // "پښتو"
};

const currentLangLabel = computed(
    () => languages[locale.value] || locale.value
);

const changeLanguage = (lang) => {
    locale.value = lang;
    localStorage.setItem("locale", lang);
    closeLangDropdown();
};

const logout = () => {
    router.post("/logout");
};

// -- Click outside directive logic --
function useClickOutside(elRef, callback) {
    const handler = (event) => {
        if (!elRef.value) return;
        if (elRef.value.contains(event.target)) return;
        callback(event);
    };

    onMounted(() => {
        document.addEventListener("click", handler);
    });
    onBeforeUnmount(() => {
        document.removeEventListener("click", handler);
    });
    return handler;
}

import { ref as vueRef } from "vue";
const langDropdownRef = vueRef(null);
const userDropdownRef = vueRef(null);

useClickOutside(langDropdownRef, closeLangDropdown);
useClickOutside(userDropdownRef, closeUserDropdown);
</script>

<template>
    <header
    :dir="locale === 'en' ? 'ltr' : 'rtl'"
  :class="[
    'fixed top-0 z-30 flex items-center justify-between h-16 px-6 shadow border-l-2',
    'bg-gray-600 dark:bg-gray-900',
    'text-white dark:text-white',
    locale === 'en' ? 'left-64 right-0' : 'right-64 left-0',
  ]"
    >
        <div class="font-serif text-xl font-semibold"> {{ $t('navbar.title') }}</div>

        <div class="flex items-center space-x-4">
            <!-- Notification Bell -->
            <button class="relative rtl:ml-2">
                <i class="text-xl bi bi-bell"></i>
                <span
                    class="absolute top-0 right-0 inline-block w-2 h-2 bg-red-600 rounded-full"
                ></span>
            </button>

            <!-- Language Dropdown -->
            <div ref="langDropdownRef" class="relative">
                <button
                    @click="toggleLangDropdown"
                    class="flex items-center justify-center px-3 py-1 text-gray-700 bg-gray-300 rounded-md hover:bg-gray-400 focus:outline-none"
                    :title="t('language.label')"
                >
                    {{ currentLangLabel }}
                    <svg
                        class="w-4 h-4 ml-1"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 9l-7 7-7-7"
                        ></path>
                    </svg>
                </button>

                <div
                    v-if="langDropdownOpen"
                    class="absolute right-0 z-50 w-32 mt-2 origin-top-right bg-gray-300 rounded-md shadow-lg ring-1 ring-black ring-opacity-5"
                >
                    <ul>
                        <li
                            v-for="(label, code) in languages"
                            :key="code"
                            @click="changeLanguage(code)"
                            class="px-4 py-2 text-gray-700 cursor-pointer hover:bg-gray-200"
                        >
                            {{ label }}
                        </li>
                    </ul>
                </div>
            </div>


            <div>
                <button
                    @click="switchTheme"
                    class="grid w-6 h-6 rounded-full hover:bg-slate-700 place-items-center hover:outline outline-1 outline-white"
                >
                    <i class="fa-solid fa-circle-half-stroke"></i>
                </button>
            </div>
            
            <!-- User Dropdown -->
            <div ref="userDropdownRef" class="relative">
                <button
                    @click="toggleUserDropdown"
                    class="flex items-center justify-center w-8 h-8 text-white bg-gray-300 rounded-full focus:outline-none"
                >
                    <span class="font-bold text-gray-700">U</span>
                </button>

                <div
                    v-if="userDropdownOpen"
                    class="absolute right-0 z-50 w-40 mt-2 origin-top-right bg-gray-300 rounded-md shadow-lg rtl:mr-[-50px] ring-1 ring-black ring-opacity-5 rtl:w-[120px]"
                >
                    <div class="py-2">
                        <a
                            v-if="userRole === 'manager'"
                            href="/profile"
                            class="block px-4 py-2 text-gray-700 text-md hover:bg-gray-300"
                        >
                        {{ $t('navbar.profile') }}
                        </a>
                        <a
                            href="/settings"
                            class="block px-4 py-2 text-gray-700 text-md hover:bg-gray-300"
                        >
                        {{ $t('navbar.setting') }}
                        </a>
                        <form @submit.prevent="logout">
                            <button
                                type="submit"
                                class="block w-full px-4 py-2 text-sm text-left text-red-600 hover:bg-gray-100 rtl:text-right"
                            >
                            {{ $t('navbar.logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>
