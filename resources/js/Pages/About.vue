<template>
  <Layout>
    <!-- Internal Hero -->
    <section class="bg-gradient-to-r from-blue-800 to-blue-900 text-white py-16">
      <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold text-center">Quem Somos</h1>
        <div class="flex justify-center mt-4 text-sm">
          <span><a href="/" class="hover:text-blue-200">Início</a></span>
          <span class="mx-2">/</span>
          <span>Quem Somos</span>
        </div>
      </div>
    </section>

    <!-- Content -->
    <section class="py-16 bg-white">
      <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
          <div>
            <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=600&h=400&fit=crop" alt="Equipe" class="rounded-xl shadow-lg w-full" />
          </div>
          <div>
            <h2 class="text-3xl font-bold text-gray-800 mb-6">Nossa História</h2>
            <p class="text-gray-600 mb-4 leading-relaxed">
              Somos uma imobiliária com anos de experiência no mercado, dedicada a encontrar o lar perfeito para cada cliente. 
              Nossa equipe é composta por profissionais apaixonados por imóveis e comprometidos com a excelência no atendimento.
            </p>
            <p class="text-gray-600 mb-4 leading-relaxed">
              Valorizamos a transparência, a confiança e a satisfação do cliente, buscando sempre as melhores soluções para cada situação.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Mission, Vision, Values -->
    <section class="py-16 bg-gray-50">
      <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="bg-white p-8 rounded-xl shadow-md text-center">
            <div class="text-blue-600 text-5xl mb-4">🎯</div>
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Missão</h3>
            <p class="text-gray-600">
              Proporcionar a melhor experiência no mercado imobiliário, conectando pessoas aos seus sonhos.
            </p>
          </div>
          <div class="bg-white p-8 rounded-xl shadow-md text-center">
            <div class="text-blue-600 text-5xl mb-4">🚀</div>
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Visão</h3>
            <p class="text-gray-600">
              Ser a imobiliária de referência no mercado, reconhecida pela qualidade e confiança.
            </p>
          </div>
          <div class="bg-white p-8 rounded-xl shadow-md text-center">
            <div class="text-blue-600 text-5xl mb-4">💎</div>
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Valores</h3>
            <p class="text-gray-600">
              Transparência, ética, respeito e excelência são os pilares da nossa empresa.
            </p>
          </div>
        </div>
      </div>
    </section>
  </Layout>
</template>

<script setup>
import Layout from '@/Shared/Layout.vue';
</script>
