<template>
  <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow">
    <div class="relative">
      <img :src="property.photo || 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=400&h=300&fit=crop'" :alt="property.title" class="w-full h-64 object-cover" />
      <div class="absolute top-4 left-4">
        <span class="bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full uppercase">
          {{ property.type || 'Venda' }}
        </span>
      </div>
      <div class="absolute top-4 right-4">
        <button class="p-2 bg-white/80 rounded-full hover:bg-white transition">
          <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
          </svg>
        </button>
      </div>
    </div>
    <div class="p-6">
      <h3 class="font-bold text-lg mb-2">{{ property.title || 'Imóvel em Destaque' }}</h3>
      <p class="text-gray-600 text-sm mb-4">{{ property.address || 'Rua Exemplo, 123 - Bairro, Cidade' }}</p>
      <div class="flex items-center space-x-4 text-sm text-gray-600 mb-4">
        <span class="flex items-center">
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
          </svg>
          {{ property.bedrooms || 3 }} quartos
        </span>
        <span class="flex items-center">
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          {{ property.bathrooms || 2 }} banheiros
        </span>
        <span class="flex items-center">
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"></path>
          </svg>
          {{ property.area || 150 }} m²
        </span>
      </div>
      <p class="text-2xl font-bold text-blue-900">R$ {{ formatPrice(property.price) }}</p>
    </div>
  </div>
</template>

<script setup>
defineProps({
  property: {
    type: Object,
    required: true,
  },
});

const formatPrice = (price) => {
  if (!price) return '0,00';
  return price.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
};
</script>
