<template>
    <div class="p-6 space-y-4">
      <h2 class="text-2xl font-bold">Distribute Product</h2>
  
      <form @submit.prevent="submit">
        <select v-model="form.product_name" class="w-full p-2 border rounded">
          <option disabled value="">-- Select Product --</option>
          <option v-for="item in storeItems" :key="item.id" :value="item.product_name">
            {{ item.product_name }} ({{ item.quantity }} {{ item.unit }})
          </option>
        </select>
  
        <input type="number" v-model.number="form.quantity" placeholder="Quantity" class="w-full p-2 mt-2 border rounded" />
  
        <select v-model="form.unit" class="w-full p-2 mt-2 border rounded">
          <option v-for="unit in units" :key="unit" :value="unit">{{ unit }}</option>
        </select>
  
        <select v-model="form.distributed_by" class="w-full p-2 mt-2 border rounded">
          <option v-for="emp in employees" :key="emp.id" :value="emp.id">{{ emp.name }}</option>
        </select>
  
        <select v-model="form.received_by" class="w-full p-2 mt-2 border rounded">
          <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
        </select>
  
        <input type="date" v-model="form.date" class="w-full p-2 mt-2 border rounded" />
  
        <textarea v-model="form.description" placeholder="Description" class="w-full p-2 mt-2 border rounded"></textarea>
  
        <button class="px-4 py-2 mt-4 text-white bg-blue-600 rounded hover:bg-blue-700">Distribute</button>
      </form>
    </div>
  </template>
  
  <script setup>
  import { useForm } from '@inertiajs/vue3'
  import Layout from '../Layout.vue'

  defineOptions({
    layout: Layout
  })
  const props = defineProps({
    employees: Array,
    departments: Array,
    storeItems: Array
  })
  
  const units = ['kg', 'litre', 'pcs', 'm']
  
  const form = useForm({
    product_name: '',
    quantity: '',
    unit: '',
    distributed_by: '',
    received_by: '',
    date: '',
    description: ''
  })
  
  const submit = () => {
    form.post('/distributions')
  }
  </script>
  