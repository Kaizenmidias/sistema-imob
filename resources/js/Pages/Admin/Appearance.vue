<template>
  <AdminLayout>
    <template #pageTitle>Aparência</template>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <div class="bg-white rounded-xl shadow p-6 border border-gray-200">
        <h3 class="text-xl font-semibold text-gray-800 mb-6">Cores do Site</h3>
        <div class="grid grid-cols-2 gap-6">
          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">Cor Principal</label>
            <div class="flex gap-3 items-center">
              <input type="color" v-model="settings.primary_color || '#1e3a8a'" class="w-12 h-10 border-2 border-gray-300 rounded cursor-pointer">
              <span class="text-gray-700 font-mono">{{ settings.primary_color || '#1e3a8a' }}</span>
            </div>
          </div>
          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">Cor Secundária</label>
            <div class="flex gap-3 items-center">
              <input type="color" v-model="settings.secondary_color || '#f97316'" class="w-12 h-10 border-2 border-gray-300 rounded cursor-pointer">
              <span class="text-gray-700 font-mono">{{ settings.secondary_color || '#f97316' }}</span>
            </div>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-xl shadow p-6 border border-gray-200">
        <h3 class="text-xl font-semibold text-gray-800 mb-6">Arquivos</h3>
        <div class="space-y-6">
          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">Logotipo</label>
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-blue-400 transition cursor-pointer">
              <svg class="w-10 h-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              <p class="text-gray-600">Clique para enviar logotipo</p>
            </div>
          </div>
          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">Favicon</label>
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-blue-400 transition cursor-pointer">
              <svg class="w-10 h-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              <p class="text-gray-600">Clique para enviar favicon</p>
            </div>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-xl shadow p-6 border border-gray-200 lg:col-span-2">
        <h3 class="text-xl font-semibold text-gray-800 mb-6">Banner Principal</h3>
        <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-blue-400 transition cursor-pointer">
          <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
          </svg>
          <p class="text-gray-600 text-lg">Clique para enviar o banner da primeira seção</p>
        </div>
      </div>
      
      <div class="lg:col-span-2">
        <button class="bg-blue-900 hover:bg-blue-800 text-white px-8 py-3 rounded-lg font-semibold transition">
          Salvar Alterações
        </button>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Shared/AdminLayout.vue';

const props = defineProps({
  settings: {
    type: Object,
    default: () => ({}),
  },
});
</script>
