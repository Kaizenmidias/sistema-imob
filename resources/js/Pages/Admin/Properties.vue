<template>
  <AdminLayout>
    <template #pageTitle>Imóveis</template>
    
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-xl font-semibold text-gray-800">Lista de Imóveis</h3>
      <button @click="goToCreate" class="bg-blue-900 hover:bg-blue-800 text-white px-6 py-2 rounded-lg font-semibold transition">
        Novo Imóvel
      </button>
    </div>

    <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Imóvel</th>
            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Tipo</th>
            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Negócio</th>
            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Preço</th>
            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="property in properties" :key="property.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0 a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                  </svg>
                </div>
                <div>
                  <div class="font-semibold text-gray-800">{{ property.titulo }}</div>
                  <div class="text-sm text-gray-500">{{ property.endereco }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-gray-600">{{ property.propertyType?.nome_tipo || '-' }}</td>
            <td class="px-6 py-4">
              <span :class="property.operacao === 'Venda' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700'" class="px-3 py-1 rounded-full text-xs font-semibold">
                {{ property.operacao }}
              </span>
            </td>
            <td class="px-6 py-4 font-semibold text-gray-800">R$ {{ formatPrice(property.valor) }}</td>
            <td class="px-6 py-4">
              <span :class="property.ativo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="px-3 py-1 rounded-full text-xs font-semibold">
                {{ property.ativo ? 'Ativo' : 'Inativo' }}
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <button class="text-blue-600 hover:text-blue-800 font-medium">Editar</button>
                <button class="text-red-600 hover:text-red-800 font-medium">Excluir</button>
              </div>
            </td>
          </tr>
          <tr v-if="properties.length === 0">
            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
              Nenhum imóvel cadastrado ainda
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AdminLayout>
</template>

<script setup>
import { useRouter } from 'vue-router';
import AdminLayout from '@/Shared/AdminLayout.vue';

const props = defineProps({
  properties: {
    type: Array,
    default: () => [],
  },
});

const router = useRouter();

const goToCreate = () => {
  router.push('/admin/properties/create');
};

const formatPrice = (value) => {
  return Number(value || 0).toLocaleString('pt-BR');
};
</script>
