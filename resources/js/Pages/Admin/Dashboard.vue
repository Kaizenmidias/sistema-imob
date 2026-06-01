<template>
  <AdminLayout>
    <template #pageTitle>Dashboard</template>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="bg-white rounded-xl shadow p-6 border border-gray-200">
        <div class="text-4xl font-bold text-blue-900">{{ propertiesCount }}</div>
        <div class="text-gray-600">Imóveis</div>
      </div>
      <div class="bg-white rounded-xl shadow p-6 border border-gray-200">
        <div class="text-4xl font-bold text-green-600">{{ leadsCount }}</div>
        <div class="text-gray-600">Leads</div>
      </div>
      <div class="bg-white rounded-xl shadow p-6 border border-gray-200">
        <div class="text-4xl font-bold text-orange-500">0</div>
        <div class="text-gray-600">Visitas Hoje</div>
      </div>
      <div class="bg-white rounded-xl shadow p-6 border border-gray-200">
        <div class="text-4xl font-bold text-purple-600">0</div>
        <div class="text-gray-600">Novos Leads Hoje</div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Shared/AdminLayout.vue';

const props = defineProps({
  propertiesCount: { type: Number, default: 0 },
  leadsCount: { type: Number, default: 0 },
});
</script>
