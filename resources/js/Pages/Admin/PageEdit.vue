<template>
  <AdminLayout>
    <template #pageTitle>Editar Página: {{ page?.titulo || 'Página' }}</template>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2 bg-white rounded-xl shadow p-6 border border-gray-200">
        <h3 class="text-xl font-semibold text-gray-800 mb-6">Conteúdo</h3>
        
        <div class="space-y-6">
          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">Título</label>
            <input type="text" v-model="form.titulo" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="Título da página">
          </div>
          
          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">Conteúdo</label>
            <textarea v-model="form.conteudo" rows="15" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="Conteúdo da página..."></textarea>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-xl shadow p-6 border border-gray-200">
        <h3 class="text-xl font-semibold text-gray-800 mb-6">SEO</h3>
        
        <div class="space-y-6">
          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">Meta Title</label>
            <input type="text" v-model="form.meta_title" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="Meta título">
          </div>
          
          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">Meta Description</label>
            <textarea v-model="form.meta_description" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="Meta descrição..."></textarea>
          </div>
        </div>
      </div>
      
      <div class="lg:col-span-3">
        <button class="bg-blue-900 hover:bg-blue-800 text-white px-8 py-3 rounded-lg font-semibold transition">
          Salvar Alterações
        </button>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { reactive } from 'vue';
import AdminLayout from '@/Shared/AdminLayout.vue';

const props = defineProps({
  page: {
    type: Object,
    default: () => ({})
  }
});

const form = reactive({
  titulo: props.page?.titulo || '',
  conteudo: props.page?.conteudo || '',
  meta_title: props.page?.meta_title || '',
  meta_description: props.page?.meta_description || '',
});
</script>
