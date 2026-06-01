<template>
  <Layout>
    <!-- Hero -->
    <section class="bg-gradient-to-r from-purple-800 to-purple-900 text-white py-16">
      <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold">Off Market</h1>
        <p class="text-xl mt-4">Imóveis exclusivos que não estão disponíveis publicamente</p>
      </div>
    </section>
    
    <!-- Content -->
    <section class="py-16 bg-gray-50">
      <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <div>
            <h2 class="text-3xl font-bold text-gray-800 mb-6">Imóveis Exclusivos</h2>
            <p class="text-gray-600 mb-4">
              Nossos imóveis Off Market são propriedades que não são anunciadas publicamente, garantindo privacidade tanto para compradores quanto para vendedores.
            </p>
            <p class="text-gray-600 mb-4">
              Este tipo de negócio é ideal para quem busca discreção e exclusividade, com acesso a oportunidades únicas no mercado.
            </p>
          </div>
          <div>
            <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=600&h=400&fit=crop" alt="" class="rounded-xl shadow-lg w-full" />
          </div>
        </div>
      </div>
    </section>
  </Layout>
</template>
<script setup>
import Layout from '@/Shared/Layout.vue';
</script>
