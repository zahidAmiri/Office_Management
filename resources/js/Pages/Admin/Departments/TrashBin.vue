<template>
    <div class="relative overflow-x-auto"
    >
        <!-- Add Department Button -->
       

        <Link href="departments"><button class="p-2 px-6 mt-2 text-white bg-green-700 rounded-md hover:bg-green-800 rtl:float-left rtl:mb-3">{{ $t('department.back') }}</button></Link>
       
        <button
            @click="RestoreAll"
            class="float-right p-2 mt-2 font-bold text-white bg-yellow-600 rounded-md hover:bg-green-800"
        >
        {{ $t('department.restore_all') }}
        </button>
    
        <!-- Department Table -->
        <table
            class="w-full mt-3 text-sm text-left rtl:text-center dark:text-black"
        >
            <thead
                class="text-xs text-black uppercase border-b border-gray-900 dark:bg-gray-300 dark:text-black"
            >
                <tr>
                    <th class="px-6 py-3">#</th>
                    <th class="px-6 py-3">{{ $t('department.name') }}</th>
                    <th class="px-6 py-3">{{ $t('department.code') }}</th>
                    <th class="px-6 py-3">{{ $t('department.department_head') }}</th>
                    <th class="px-6 py-3">{{ $t('department.description') }}</th>
                    <th class="px-6 py-3">{{ $t('department.actions') }}</th>
                </tr>
            </thead>
            <tbody v-if="departments.length > 0">
                <tr
                    v-for="(department, index) in departments"
                    :key="department.id"
                    class="text-black bg-white border-b border-gray-200 dark:bg-gray-300 dark:border-gray-700"
                >
                    <td class="px-6 py-4">{{ index + 1 }}</td>
                    <td class="px-6 py-4">{{ department.name }}</td>
                    <td class="px-6 py-4">{{ department.code }}</td>
                    <td class="px-6 py-4">{{ department.department_head }}</td>
                    <td class="px-6 py-4">{{ department.description }}</td>
                    <td class="px-6 py-4">
                        <PrimaryButton @click="RestoreOneDepartment(department.id)" class="px-2 mr-1 bg-green-600 hover:bg-green-700 active:bg-green-900">{{ $t('department.restore_one') }}</PrimaryButton>
                        <PrimaryButton @click="DeleteOneDepartment(department.id)" class="px-2 bg-red-600 hover:bg-red-700 active:bg-red-900">{{ $t('department.delete') }}</PrimaryButton>
                    </td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr>
                    <td colspan="5" class="py-4 text-center text-gray-500">
                        {{ $t('department.not_trash_found') }}
                    </td>
                </tr>
            </tbody>
        </table>

    </div>
</template>

<script setup>
import {Link} from "@inertiajs/vue3";
import { ref } from "vue";
import Layout from "../Layout.vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Modal from "@/Components/Modal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";

defineOptions({ layout: Layout });

const props = defineProps({
    departments: Array,
});


const form = useForm({
    name: "",
    code: "",
    department_head: "",
    description: "",
});


const RestoreAll = () => {
    router.get('RestoreAllTrashed', {
        onSuccess: () => {
            console.log('route successfully')
        }
    })
}

const RestoreOneDepartment = (id) => {
    router.get(`/RestoreOneDepartment/${id}`, {
        onSuccess: () => {
            console.log('route success')
        }
    })
}



const DeleteOneDepartment = (id) => {
    router.delete(`/DeleteOneDepartment/${id}`, {
        onSuccess: () => {
            console.log('route success')
        }
    })
}
</script>
