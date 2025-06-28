<template>
    <div class="relative overflow-x-auto"
    :dir="locale === 'en'? 'ltr' : 'rtl'"
    >
        <!-- Add Department Button -->
        <button
            v-if="userRole === 'manager'"
            @click="showModal = true"
            class="p-2 mt-2 font-bold text-white bg-green-700 rounded-md hover:bg-green-800"
        >
        {{ $t('department.add') }}
        </button>

         <!-- print Department Button -->

         <a
         v-if="userRole === 'manager' || userRole === 'office_admin'"
      :href="`department/print_all?lang=${locale}`"
      target="_blank"
      class="p-1 px-3 mx-2 text-white bg-yellow-600 rounded-md hover:bg-yellow-700"
    >
    🖨️ {{ $t('department.print') }}
    </a>

        <button
            v-if="userRole === 'manager'"
            @click="showTrashData"
            class="float-right p-2 px-6 mt-2 font-bold text-white bg-yellow-600 rounded-md hover:bg-yellow-700 hover:shadow-lg rtl:float-left"
        >
        {{ $t('department.trash') }}
        </button>

        <TextInput
            v-model="search"
            :placeholder="$t('department.search')"
            class="block w-full p-2 py-3 mt-2 mb-4 border border-gray-300 rounded dark:bg-gray-800"
        />

        <!-- Department Table -->
        <table

            class="w-full mt-3 text-sm text-center shadow-md rtl:text-center dark:text-black"
        >
            <thead
                class="text-xs text-black uppercase bg-gray-400 border-b border-gray-900 dark:bg-gray-500 dark:text-black"
            >
                <tr>
                    <th class="px-6 py-3">#</th>
                    <th class="px-6 py-3">{{ $t('department.name') }}</th>
                    <th class="px-6 py-3">{{ $t('department.code') }}</th>
                    <th class="px-6 py-3">{{ $t('department.department_head') }}</th>
                    <th class="px-6 py-3">{{ $t('department.description') }}</th>
                    <th class="px-6 py-3" v-if="userRole === 'manager'">
                        {{ $t('department.actions') }}
                    </th>
                </tr>
            </thead>
            <tbody v-if="departments.length > 0">
                <tr
                    v-for="(department, index) in departments"
                    :key="department.id"
                    class="text-black border-b border-gray-200 dark:border-gray-700 even:bg-gray-200 odd:bg-gray-300 dark:even:bg-gray-500 dark:odd:bg-gray-400"
                >
                    <td class="px-6 py-4">{{ index + 1 }}</td>
                    <td class="px-6 py-4">{{ department.name }}</td>
                    <td class="px-6 py-4">{{ department.code }}</td>
                    <td class="px-6 py-4">{{ department.department_head }}</td>
                    <td class="px-6 py-4">{{ department.description }}</td>
                    <td class="px-6 py-4" v-if="userRole === 'manager'">
                        <PrimaryButton
                            @click="DeleteDepartment(department.id)"
                            class="px-6 bg-red-600 hover:bg-red-700 active:bg-red-800 rtl:ml-1"
                        >
                        {{ $t('department.delete') }}
                        </PrimaryButton>
                        <PrimaryButton
                            @click="EditDepartment(department.id)"
                            class="ml-1 bg-gradient-to-r from-blue-500 via-blue-550 to-blue-600"
                        >
                        {{ $t('department.edit') }}
                        </PrimaryButton>
                    </td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr>
                    <td colspan="5" class="py-4 text-center text-gray-500">
                        {{ $t('department.not_found') }}
                    </td>
                </tr>
            </tbody>
        </table>
        <!-- Modal code -->
        <Modal :show="showModal" @close="showModal = false" maxWidth="lg">
            <div class="p-6">
                <h2 class="mb-4 text-xl font-bold text-center text-black">
                    {{ $t('department.add') }}
                </h2>
                <form @submit.prevent="submitForm">
                    <TextInput
                        v-model="form.name"
                        :placeholder="$t('department.name')"
                        class="w-full p-2 mb-2 bg-gray-300 border rounded"
                    />

                    <InputError class="mt-2" :message="form.errors.name" />

                    <TextInput
                        v-model="form.code"
                        :placeholder="$t('department.code')"
                        class="w-full p-2 mb-2 bg-gray-300 border rounded"
                    />
                    <InputError class="mt-2" :message="form.errors.code" />

                    <TextInput
                        v-model="form.department_head"
                        :placeholder="$t('department.department_head')"
                        class="w-full p-2 mb-2 bg-gray-300 border rounded"
                    />
                    <InputError
                        class="mt-2"
                        :message="form.errors.department_head"
                    />

                    <TextInput
                        v-model="form.description"
                        :placeholder="$t('department.description')"
                        class="w-full p-2 mb-2 bg-gray-300 border rounded"
                    ></TextInput>
                    <InputError
                        class="mt-2"
                        :message="form.errors.description"
                    />

                    <div class="flex justify-end space-x-2">
                        <button
                            type="button"
                            @click="CancleToggle"
                            class="px-4 py-2 bg-gray-300 rounded rtl:ml-2"
                        >
                        {{ $t('department.cancel') }}
                        </button>

                        <button
                            type="submit"
                            class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700"
                        >
                        {{ $t('department.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";
import Layout from "../Layout.vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Modal from "@/Components/Modal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";

import { useI18n } from 'vue-i18n';
const { locale } = useI18n();

const page = usePage();
const userRole = page.props.auth.user?.role;

defineOptions({ layout: Layout });

const props = defineProps({
    departments: Array,
    department: Object,
    toggle: Boolean,
});

const showModal = ref(false);

const CancleToggle = () => {
    showModal.value = false;
    form.reset();
    form.errors = "";
};
const form = useForm({
    name: "",
    code: "",
    department_head: "",
    description: "",
});

const submitForm = () => {
    form.post("/departments", {
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false;
            form.reset();
        },
    });
};

const DeleteDepartment = (id) => {
    router.delete(`/departments/${id}`, {
        onSuccess: () => {
            console.log("route successfully");
        },
    });
};

const showTrashData = () => {
    router.get("/trashdepartments");
};

const search = ref(page.props.search || "");

watch(search, (value) => {
    router.get(
        "/departments",
        { search: value },
        {
            preserveScroll: true,
            preserveState: true,
            replace: true, // avoids adding browser history entries on each keystroke
        }
    );
});

const EditDepartment = (id) => {
    router.get(`/editdepartment/${id}`, {
        onSuccess: () => {
            console.log("route successfull");
        },
    });
};
</script>
