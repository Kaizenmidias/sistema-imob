<template>
  <AdminLayout>
    <template #pageTitle>Páginas</template>
    
    <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Página</th>
            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Slug</th>
            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="page in pageList" :key="page.slug" class="hover:bg-gray-50">
            <td class="px-6 py-4 font-semibold text-gray-800">{{ page.titulo }}</td>
            <td class="px-6 py-4 text-gray-600">{{ page.slug }}</td>
            <td class="px-6 py-4">
              <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Ativo</span>
            </td>
            <td class="px-6 py-4">
              <button @click="editPage(page.slug)" class="text-blue-600 hover:text-blue-800 font-medium">Editar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import AdminLayout from '@/Shared/AdminLayout.vue';

const router = useRouter();

const pageList = ref([
  { titulo: 'Página Inicial', slug: 'home' },
  { titulo: 'Imóveis', slug: 'imoveis' },
  { titulo: 'Quem Somos', slug: 'quem-somos' },
  { titulo: 'Off Market', slug: 'off-market' },
  { titulo: 'Gestão Exclusiva', slug: 'gestao-exclusiva' },
  { titulo: 'Calculadora', slug: 'calculadora' },
  { titulo: 'Avalie seu Imóvel', slug: 'avalie-seu-imovel' },
  { titulo: 'Corretor Parceiro', slug: 'corretor-parceiro' },
  { titulo: 'Blog', slug: 'blog' },
  { titulo: 'Contato', slug: 'contato' },
]);

const editPage = (slug) => {
  router.push('/admin/pages/' + slug);
};
</script>
