<template>
    <div class="p-6 space-y-6">
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold">Backups</h2>
        
        <button
          @click="runBackup"
          :disabled="loading"
          class="px-4 py-2 text-white bg-indigo-600 rounded hover:bg-indigo-700 disabled:opacity-50"
        >
        <i class="pr-2 rtl:pl-2 fa-solid fa-trash-arrow-up"></i>
          {{ loading ? 'Backing up…' : 'Take Backup' }}
        </button>
      </div>

      <p v-if="flash" class="text-green-600">{{ flash }}</p>
      <p v-if="error" class="text-red-600">{{ error }}</p>

      <ul v-if="backups.length" class="divide-y divide-gray-200">
        <li v-for="b in backups" :key="b.name" class="flex items-center justify-between py-2">
          <div>
            <p class="font-mono">{{ b.name }}</p>
            <small class="text-gray-500">
              {{ prettyDate(b.last_modified) }} – {{ prettySize(b.size) }}
            </small>
          </div>

          <a
            :href="b.download_url"
            class="text-blue-600 hover:underline"
            >Download</a
          >
        </li>
      </ul>

      <p v-else>No backups found.</p>
    </div>
  </template>

  <script setup>
  import { ref, onMounted } from 'vue'

  import axios from 'axios'
  import Layout from '../Layout.vue'

  defineOptions({
    layout: Layout
  })
  const backups = ref([])
  const loading = ref(false)
  const flash   = ref('')
  const error   = ref('')

  const prettyDate = ts => new Date(ts * 1000).toLocaleString()
  const prettySize = bytes => {
    const u = ['B','KB','MB','GB']; let i=0
    while (bytes >= 1024 && ++i) bytes /= 1024
    return bytes.toFixed(1)+' '+u[i]
  }

  const fetchBackups = async () => {
    try { backups.value = (await axios.get('/backup/files')).data }
    catch { error.value = 'Could not load backups.' }
  }

  const runBackup = async () => {
    loading.value = true; flash.value=''; error.value=''
    try {
      const { data } = await axios.post('/backup/run')
      flash.value = data.message
      await fetchBackups()
    } catch { error.value = 'Backup failed.' }
    finally { loading.value = false }
  }

  onMounted(fetchBackups)
  </script>
