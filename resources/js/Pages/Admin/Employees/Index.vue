<template>
    <div class="relative overflow-x-auto">
        <!-- Header Buttons (Only for manager) -->

<!-- Header Buttons (Only for manager) -->
<div class="flex items-center justify-between mt-4 mb-2" v-if="userRole === 'manager'">
    <!-- Left side: Add + Print buttons -->
    <div class="flex items-center space-x-2">
        <button
            @click="openAddModal"
            class="px-4 py-2 font-bold text-white bg-green-700 rounded hover:bg-green-800"
        >
            {{ $t('employee.add') }}
        </button>

        <a
            :href="`employee/print_all?lang=${locale}`"
            target="_blank"
            class="p-2 px-3 text-white bg-yellow-600 rounded-md hover:bg-yellow-700 rtl:mr-5"
        >
            🖨️ {{ $t('employee.print') }}
        </a>
    </div>

    <!-- Right side: Trash button -->
    <button
        @click="showTrashData"
        class="p-2 px-6 font-bold text-white bg-yellow-600 rounded-md hover:bg-yellow-700 hover:shadow-lg"
    >
        {{ $t('employee.trash') }}
    </button>
</div>

        <!-- Search -->
        <TextInput
            type="text"
            class="block w-full p-2 mt-2 mb-2 border rounded-md border-emerald-500 dark:bg-gray-700"
            :placeholder="$t('employee.search')"
            v-model="search"
        />

        <!-- Employee Table -->
        <table class="w-full text-sm text-center border shadow-md dark:text-white">
            <thead class="text-xs text-black uppercase bg-gray-400 border-b dark:bg-gray-700 dark:text-white">
                <tr>
                    <th class="px-3 py-2">#</th>
                    <th class="px-3 py-2">{{ $t('employee.name') }}</th>
                    <th class="px-3 py-2">{{ $t('employee.father_name') }}</th>
                    <th class="px-3 py-2">{{ $t('employee.ranking') }}</th>
                    <th class="px-3 py-2">{{ $t('employee.contact') }}</th>
                    <th class="px-3 py-2">{{ $t('employee.department') }}</th>
                    <th class="px-3 py-2">{{ $t('employee.image') }}</th>
                    <th class="px-3 py-2" v-if="userRole === 'manager'">{{ $t('employee.actions') }}</th>
                </tr>
            </thead>
            <tbody v-if="employees.length > 0">
                <tr
                    v-for="(emp, index) in employees"
                    :key="emp.id"
                    class="border-b border-black even:bg-gray-300 bg-gradient-to-r odd:bg-gray-400 "
                >
                    <td class="px-3 py-2">{{ index + 1 }}</td>
                    <td class="px-3 py-2"> {{ emp.name }} {{ emp.last_name }}</td>
                    <td class="px-3 py-2">{{ emp.father_name }}</td>
                    <td class="px-3 py-2">{{ emp.ranking }}</td>
                    <td class="px-3 py-2">{{ emp.contact_number }}</td>
                    <td class="px-3 py-2">{{ emp.department?.name || "N/A" }}</td>
                    <td class="px-3 py-2">
                        <img
                            v-if="emp.image"
                            :src="`/storage/${emp.image}`"
                            class="content-center object-cover w-16 h-16"
                        />
                        <img
                            v-else
                            src="/storage/employees/egojhuYHfVMjIm7EDVbmFOb8RYi8SgcGQJv3n9O5.png"
                            class="object-cover w-16 h-16 rounded-full"
                        />
                    </td>
                    <td class="px-3 py-2 space-x-1" v-if="userRole === 'manager'">
                        <button
                            class="p-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 rtl:ml-2"
                            @click="editEmployee(emp)"
                        >
                        {{ $t('employee.edit') }}
                        </button>
                        <button
                            class="p-2 text-white bg-red-500 rounded-md hover:bg-red-600"
                            @click="deleteEmployee(emp.id)"
                        >
                        {{ $t('employee.delete') }}
                        </button>
                    </td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr>
                    <td colspan="8" class="py-4 text-center text-gray-500">
                        {{ $t('employee.not_found') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Modal -->
        <Modal :show="showModal" @close="cancel" maxWidth="lg">
            <div class="p-6 bg-white rounded-md shadow-lg">
                <h2 class="mb-4 text-xl font-semibold text-center">
                    {{ isEditing ? $t('employee.edit_title') : $t('employee.add') }}
                </h2>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <input v-model="form.name" type="text" :placeholder="$t('employee.name')" class="w-full p-2 border rounded" />
                    <InputError v-if="form.errors.name" class="mt-2" :message="form.errors.name" />

                    <input v-model="form.last_name" type="text" :placeholder="$t('employee.last_name')" class="w-full p-2 border rounded" />
                    <InputError v-if="form.errors.last_name" class="mt-2" :message="form.errors.last_name" />

                    <input v-model="form.father_name" type="text" :placeholder="$t('employee.father_name')" class="w-full p-2 border rounded" />
                    <InputError v-if="form.errors.father_name" class="mt-2" :message="form.errors.father_name" />

                    <input v-model="form.ranking" type="text" :placeholder="$t('employee.ranking')" class="w-full p-2 border rounded" />
                    <InputError v-if="form.errors.ranking" class="mt-2" :message="form.errors.ranking" />

                    <input v-model="form.contact_number" type="text" :placeholder="$t('employee.contact')" class="w-full p-2 border rounded" />
                    <InputError v-if="form.errors.contact_number" class="mt-2" :message="form.errors.contact_number" />

                    <input v-model="form.id_number" type="text" :placeholder="$t('employee.id_number')" class="w-full p-2 border rounded" />
                    <InputError v-if="form.errors.id_number" class="mt-2" :message="form.errors.id_number" />

                    <input v-model="form.card_number" type="text" :placeholder="$t('employee.card_number')" class="w-full p-2 border rounded" />
                    <InputError v-if="form.errors.card_number" class="mt-2" :message="form.errors.card_number" />

                    <select v-model="form.department_id" class="w-full p-2 text-center border rounded">
                        <option value="">{{ $t('employee.select_department') }}</option>
                        <option v-for="dep in departments" :key="dep.id" :value="dep.id">{{ dep.name }}</option>
                    </select>

                    <input type="file" @change="handleFileUpload" class="w-full p-2 bg-white border rounded" />

                    <textarea v-model="form.description" :placeholder="$t('employee.description')" class="w-full p-2 border rounded"></textarea>

                    <div class="flex justify-end space-x-2">
                        <button type="button" @click="cancel" class="px-4 py-2 text-black bg-gray-400 rounded rtl:ml-2">{{ $t('employee.cancel') }}</button>
                        <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">
                            {{ isEditing ? $t('employee.edit') : $t('employee.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";
import { useForm, router, usePage } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import Layout from "../Layout.vue";

import { useI18n } from 'vue-i18n';
const { locale } = useI18n();

defineOptions({ layout: Layout });

const props = defineProps({
    employees: Array,
    departments: Array,
});

const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

const form = useForm({
    name: "",
    last_name: "",
    father_name: "",
    ranking: "",
    contact_number: "",
    id_number: "",
    card_number: "",
    image: null,
    description: "",
    department_id: "",
});

const handleFileUpload = (event) => {
    form.image = event.target.files[0];
};

const submitForm = () => {
    const url = isEditing.value ? `/employees/${editingId.value}` : "/employees";

    form.transform((data) => ({
        ...data,
        _method: isEditing.value ? 'PUT' : 'POST',
    }));

    const options = {
        forceFormData: true,
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            showModal.value = false;
            form.reset();
            isEditing.value = false;
        },
    };

    form.post(url, options);
};

const cancel = () => {
    showModal.value = false;
    isEditing.value = false;
    form.reset();
    form.errors = "";
};

const openAddModal = () => {
    isEditing.value = false;
    form.reset();
    showModal.value = true;
};

const editEmployee = (emp) => {
    isEditing.value = true;
    editingId.value = emp.id;

    form.name = emp.name;
    form.last_name = emp.last_name;
    form.father_name = emp.father_name;
    form.ranking = emp.ranking;
    form.contact_number = emp.contact_number;
    form.id_number = emp.id_number;
    form.card_number = emp.card_number;
    form.department_id = emp.department_id;
    form.description = emp.description;
    form.image = null;
    showModal.value = true;
};

const deleteEmployee = (id) => {
    if (confirm("Are you sure you want to delete this employee?")) {
        router.delete(`/employees/${id}`);
    }
};

const showTrashData = () => {
    router.get('/trashedemployees');
};

const page = usePage();
const userRole = page.props.auth.user?.role;

const search = ref(page.props.search || "");
watch(search, (value) => {
    router.get('/employees', { search: value }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
});
</script>
