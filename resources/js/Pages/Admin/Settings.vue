<template>
  <AdminLayout>
    <template #pageTitle>Configurações</template>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-white rounded-xl shadow border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informações do Site</h3>
        <div class="space-y-4">
          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">Nome do Site</label>
            <input type="text" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="Imobiliária" />
          </div>
          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">Telefone</label>
            <input type="text" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="(00) 00000-0000" />
          </div>
          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">E-mail</label>
            <input type="email" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="contato@site.com" />
          </div>
          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">WhatsApp</label>
            <input type="text" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="(00) 00000-0000" />
          </div>
          <div class="pt-4">
            <button class="bg-blue-900 hover:bg-blue-800 text-white px-6 py-2 rounded-lg font-semibold transition">Salvar Alterações</button>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Redes Sociais</h3>
        <div class="space-y-4">
          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">Instagram</label>
            <input type="url" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="https://instagram.com/usuario" />
          </div>
          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">Facebook</label>
            <input type="url" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="https://facebook.com/usuario" />
          </div>
          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">LinkedIn</label>
            <input type="url" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="https://linkedin.com/in/usuario" />
          </div>
          <div class="pt-4">
            <button class="bg-blue-900 hover:bg-blue-800 text-white px-6 py-2 rounded-lg font-semibold transition">Salvar Alterações</button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Shared/AdminLayout.vue';

const props = defineProps({
  settings: {
    type: Array,
    default: () => [],
  },
});
</script>
