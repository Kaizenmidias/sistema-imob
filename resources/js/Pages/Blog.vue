<template>
  <Layout>
    <section class="bg-gradient-to-r from-emerald-700 to-emerald-800 text-white py-16">
      <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold">Blog</h1>
        <p class="text-xl mt-4">Dicas e notícias do mercado imobiliário</p>
      </div>
    </section>
    <section class="py-12 bg-gray-50">
      <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div v-for="i in 6" :key="i" class="bg-white rounded-xl shadow overflow-hidden">
            <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=400&h=250&fit=crop" alt="" class="w-full h-48 object-cover" />
            <div class="p-6">
              <span class="text-blue-600 text-sm font-semibold uppercase">Categoria</span>
              <h3 class="text-xl font-bold text-gray-800 mt-2">Título do Post {{ i }}</h3>
              <p class="text-gray-600 mt-2 line-clamp-3">Um breve resumo do conteúdo do post para dar uma ideia do que se trata o artigo...</p>
              <button class="mt-4 text-blue-600 font-semibold hover:text-blue-700">Leia mais →</button>
            </div>
          </div>
        </div>
      </div>
    </section>
  </Layout>
</template>
<script setup>
import Layout from '@/Shared/Layout.vue';
</script>
