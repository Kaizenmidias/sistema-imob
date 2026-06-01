<template>
  <Layout>
    <!-- Internal Hero -->
    <section class="bg-gradient-to-r from-blue-800 to-blue-900 text-white py-16">
      <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold text-center">Imóveis</h1>
        <div class="flex justify-center mt-4 text-sm">
          <span><a href="/" class="hover:text-blue-200">Início</a></span>
          <span class="mx-2">/</span>
          <span>Imóveis</span>
        </div>
      </div>
    </section>

    <!-- Filters -->
    <section class="py-8 bg-white shadow-sm">
      <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
          <div>
            <label class="block text-gray-700 text-sm font-semibold mb-1">Negócio</label>
            <select class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-700">
              <option>Todos</option>
              <option>Comprar</option>
              <option>Alugar</option>
            </select>
          </div>
          <div>
            <label class="block text-gray-700 text-sm font-semibold mb-1">Tipo</label>
            <select class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-700">
              <option>Todos</option>
              <option>Casa</option>
              <option>Apartamento</option>
            </select>
          </div>
          <div>
            <label class="block text-gray-700 text-sm font-semibold mb-1">Valor mín.</label>
            <input type="text" placeholder="R$ 0,00" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-700" />
          </div>
          <div>
            <label class="block text-gray-700 text-sm font-semibold mb-1">Valor máx.</label>
            <input type="text" placeholder="R$ ilimitado" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-700" />
          </div>
          <div class="flex items-end">
            <button class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg transition flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              Filtrar
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Categories Carousel -->
    <CategoryCarousel />

    <!-- Properties Grid -->
    <section class="py-12 bg-gray-50">
      <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <a v-for="i in 12" :key="i" :href="`/imoveis/imovel-${i}`" class="group">
            <PropertyCard :property="{
              id: i,
              title: 'Imóvel ' + i,
              address: 'Rua Exemplo, ' + i + ' - Bairro, Cidade',
              price: 350000 + (i * 50000),
              bedrooms: Math.floor(Math.random() * 4) + 2,
              bathrooms: Math.floor(Math.random() * 3) + 1,
              area: 100 + (i * 20),
              type: i % 2 === 0 ? 'Venda' : 'Aluguel',
            }" />
          </a>
        </div>
      </div>
    </section>
  </Layout>
</template>

<script setup>
import Layout from '@/Shared/Layout.vue';
import PropertyCard from '@/Shared/PropertyCard.vue';
import CategoryCarousel from '@/Shared/CategoryCarousel.vue';
</script>
