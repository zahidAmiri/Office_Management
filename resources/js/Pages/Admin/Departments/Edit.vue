<template>
    <div class="">
        <Modal :show="showModal" @close="showModal = false" maxWidth="lg">
            <div class="p-6">
                <h2 class="mb-4 text-xl font-bold text-center text-black">
                    Edit Department
                </h2>
                <form @submit.prevent="submitForm">
                    <!-- Name -->
                    <input
                        v-model="form.name"
                        placeholder="Name"
                        class="w-full p-2 mb-2 bg-gray-300 border rounded"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />

                    <!-- Code -->
                    <input
                        v-model="form.code"
                        placeholder="Code"
                        class="w-full p-2 mb-2 bg-gray-300 border rounded"
                    />
                    <InputError class="mt-2" :message="form.errors.code" />

                    <!-- Department Head -->
                    <input
                        v-model="form.department_head"
                        placeholder="Department Head"
                        class="w-full p-2 mb-2 bg-gray-300 border rounded"
                    />
                    <InputError class="mt-2" :message="form.errors.department_head" />

                    <!-- Description -->
                    <input
                        v-model="form.description"
                        placeholder="Description"
                        class="w-full p-2 mb-2 bg-gray-300 border rounded"
                    />
                    <InputError class="mt-2" :message="form.errors.description" />

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-2">
                        <button
                            type="button"
                            @click="CancleToggle"
                            class="px-4 py-2 bg-gray-300 rounded"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700"
                        >
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import Layout from '../Layout.vue'
import { defineProps, ref } from 'vue'
import InputError from '@/Components/InputError.vue'

defineOptions({
    layout: Layout
})

const props = defineProps({
    department: {
        type: Object,
        required: true
    }
})

// Initialize the form with department data from props
const form = useForm({
    name: props.department.name || '', // Set initial value from props
    code: props.department.code || '', // Set initial value from props
    department_head: props.department.department_head || '', // Set initial value from props
    description: props.department.description || '' // Set initial value from props
})

const showModal = ref(true)

const CancleToggle = () => {
    showModal.value = false
    history.back()
}

const submitForm = () => {
    form.put(`/editdepartment/${props.department.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false
        }
    })
}
</script>
