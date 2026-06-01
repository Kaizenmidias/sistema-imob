<template>
  <AdminLayout>
    <template #pageTitle>Leads</template>

    <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Nome</th>
            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">E-mail</th>
            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Telefone</th>
            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Origem</th>
            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Data</th>
            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="lead in leads" :key="lead.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 font-semibold text-gray-800">{{ lead.name }}</td>
            <td class="px-6 py-4 text-gray-600">{{ lead.email }}</td>
            <td class="px-6 py-4 text-gray-600">{{ lead.phone }}</td>
            <td class="px-6 py-4 text-gray-600">{{ lead.source || 'Site' }}</td>
            <td class="px-6 py-4 text-gray-600">{{ new Date(lead.created_at).toLocaleDateString('pt-BR') }}</td>
            <td class="px-6 py-4">
              <span :class="lead.status === 'Novo' ? 'bg-blue-100 text-blue-700' : lead.status === 'Contatado' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700'" class="px-3 py-1 rounded-full text-xs font-semibold">
                {{ lead.status || 'Novo' }}
              </span>
            </td>
          </tr>
          <tr v-if="leads.length === 0">
            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
              Nenhum lead ainda
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Shared/AdminLayout.vue';

const props = defineProps({
  leads: {
    type: Array,
    default: () => [],
  },
});
</script>
