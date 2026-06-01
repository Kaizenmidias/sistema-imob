<template>
  <Layout>
    <!-- Hero -->
    <section class="relative bg-gradient-to-r from-orange-600 to-orange-700 text-white py-24">
      <div class="absolute inset-0 opacity-20">
        <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=1920&h=800&fit=crop" alt="" class="w-full h-full object-cover" />
      </div>
      <div class="container mx-auto px-4 relative z-10 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Venda Seu Imóvel</h1>
        <p class="text-xl max-w-2xl mx-auto">Deixe-nos ajudar você a vender seu imóvel rapidamente e pelo melhor preço</p>
      </div>
    </section>

    <!-- Form -->
    <section class="py-16 bg-gray-50">
      <div class="container mx-auto px-4">
        <div class="bg-white rounded-xl shadow-lg p-8 max-w-3xl mx-auto">
          <h2 class="text-2xl font-bold text-gray-800 mb-8 text-center">Cadastre Seu Imóvel</h2>
          
          <form @submit.prevent="submitForm" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-gray-700 font-medium mb-2">Nome Completo</label>
                <input type="text" v-model="form.name" placeholder="Seu nome" class="w-full border border-gray-300 rounded-lg px-4 py-3" required />
              </div>
              <div>
                <label class="block text-gray-700 font-medium mb-2">Telefone</label>
                <input type="tel" v-model="form.phone" placeholder="(11) 99999-9999" class="w-full border border-gray-300 rounded-lg px-4 py-3" required />
              </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-gray-700 font-medium mb-2">E-mail</label>
                <input type="email" v-model="form.email" placeholder="seu@email.com" class="w-full border border-gray-300 rounded-lg px-4 py-3" required />
              </div>
              <div>
                <label class="block text-gray-700 font-medium mb-2">Tipo do Imóvel</label>
                <select v-model="form.propertyType" class="w-full border border-gray-300 rounded-lg px-4 py-3" required>
                  <option value="">Selecione</option>
                  <option value="casa">Casa</option>
                  <option value="apartamento">Apartamento</option>
                </select>
              </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div>
                <label class="block text-gray-700 font-medium mb-2">Quartos</label>
                <input type="number" v-model="form.bedrooms" placeholder="3" class="w-full border border-gray-300 rounded-lg px-4 py-3" />
              </div>
              <div>
                <label class="block text-gray-700 font-medium mb-2">Banheiros</label>
                <input type="number" v-model="form.bathrooms" placeholder="2" class="w-full border border-gray-300 rounded-lg px-4 py-3" />
              </div>
              <div>
                <label class="block text-gray-700 font-medium mb-2">Área (m²)</label>
                <input type="number" v-model="form.area" placeholder="150" class="w-full border border-gray-300 rounded-lg px-4 py-3" />
              </div>
            </div>
            <div>
              <label class="block text-gray-700 font-medium mb-2">Mensagem</label>
              <textarea v-model="form.message" rows="4" placeholder="Descreva seu imóvel..." class="w-full border border-gray-300 rounded-lg px-4 py-3"></textarea>
            </div>
            <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-6 rounded-lg transition">
              Enviar
            </button>
          </form>
        </div>
      </div>
    </section>
  </Layout>
</template>

<script setup>
import { ref } from 'vue';
import Layout from '@/Shared/Layout.vue';

const form = ref({
  name: '',
  phone: '',
  email: '',
  propertyType: '',
  bedrooms: '',
  bathrooms: '',
  area: '',
  message: '',
});

function submitForm() {
  alert('Dados enviados! Entraremos em contato em breve.');
  form.value = {
    name: '',
    phone: '',
    email: '',
    propertyType: '',
    bedrooms: '',
    bathrooms: '',
    area: '',
    message: '',
  };
}
</script>
