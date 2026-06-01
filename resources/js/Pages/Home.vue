<template>
  <Layout>
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-r from-blue-800 to-blue-900 text-white py-24">
      <div class="absolute inset-0 opacity-20">
        <img src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=1920&h=800&fit=crop" alt="Imóvel" class="w-full h-full object-cover" />
      </div>
      <div class="container mx-auto px-4 relative z-10">
        <div class="text-center mb-12">
          <h1 class="text-4xl md:text-5xl font-bold mb-4">Seja bem vindo! Seu novo lar está aqui.</h1>
        </div>
        
        <!-- Search Form -->
        <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-5xl mx-auto text-gray-800">
          <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
              <label class="block text-gray-700 text-sm font-semibold mb-1">Negócio</label>
              <select class="w-full border border-gray-300 rounded-lg px-4 py-3 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option>Todos</option>
                <option>Comprar</option>
                <option>Alugar</option>
              </select>
            </div>
            <div>
              <label class="block text-gray-700 text-sm font-semibold mb-1">Tipo</label>
              <select class="w-full border border-gray-300 rounded-lg px-4 py-3 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option>Todos</option>
                <option>Casa</option>
                <option>Apartamento</option>
              </select>
            </div>
            <div>
              <label class="block text-gray-700 text-sm font-semibold mb-1">Valor mín.</label>
              <input type="text" placeholder="R$ 0,00" class="w-full border border-gray-300 rounded-lg px-4 py-3 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
            <div>
              <label class="block text-gray-700 text-sm font-semibold mb-1">Valor máx.</label>
              <input type="text" placeholder="R$ ilimitado" class="w-full border border-gray-300 rounded-lg px-4 py-3 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
            <div class="flex items-end">
              <button class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Pesquisar Imóveis
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Categories Carousel -->
    <CategoryCarousel />

    <!-- Seleção Especial -->
    <section class="py-16 bg-white">
      <div class="max-w-[1400px] mx-auto px-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Seleção Especial</h2>
        <PropertyCarousel :properties="selecaoEspecial" />
      </div>
    </section>

    <!-- Mais Procurados -->
    <section class="py-16 bg-gray-50">
      <div class="max-w-[1400px] mx-auto px-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Mais Procurados</h2>
        <PropertyCarousel :properties="maisProcurados" />
      </div>
    </section>

    <!-- Visto Recentemente -->
    <section class="py-16 bg-white">
      <div class="max-w-[1400px] mx-auto px-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Visto Recentemente</h2>
        <PropertyCarousel :properties="vistoRecentemente" />
      </div>
    </section>

    <!-- Instagram Section -->
    <section class="py-16 bg-gray-50">
      <div class="max-w-[1400px] mx-auto px-4">
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-gradient-to-r from-orange-500 via-pink-500 to-purple-500 rounded-full">
              <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
              </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">@conectaimovel</h2>
            <span class="px-4 py-1 bg-orange-100 text-orange-700 rounded-full text-sm font-semibold">Instagram</span>
          </div>
          <button class="px-6 py-2 border border-gray-300 text-gray-700 rounded-full font-semibold hover:bg-gray-100 transition">Seguir</button>
        </div>
        
        <!-- Instagram Carousel -->
        <div class="relative">
          <!-- Fade Overlays -->
          <div class="absolute left-0 top-0 bottom-0 w-20 z-10 pointer-events-none" style="background: linear-gradient(to right, rgba(249, 250, 251, 1), rgba(249, 250, 251, 0));"></div>
          <div class="absolute right-0 top-0 bottom-0 w-20 z-10 pointer-events-none" style="background: linear-gradient(to left, rgba(249, 250, 251, 1), rgba(249, 250, 251, 0));"></div>
          
          <!-- Carousel Track -->
          <div class="overflow-hidden">
            <div
              class="flex gap-4"
              :style="{ transform: `translateX(${instagramPosition}px)` }"
              ref="instagramTrackRef"
            >
              <template v-for="(item, index) in instagramItems" :key="index">
                <a href="#" class="flex-shrink-0 w-64 group">
                  <div class="relative rounded-xl overflow-hidden shadow-md">
                    <img :src="item.image" :alt="item.caption" class="w-full h-80 object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                      <div class="text-white text-center">
                        <div class="flex items-center justify-center gap-4 mb-2">
                          <span class="flex items-center gap-1">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                            {{ item.likes }}
                          </span>
                          <span class="flex items-center gap-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            {{ item.comments }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </a>
              </template>
              <!-- Duplicate items for infinite loop -->
              <template v-for="(item, index) in instagramItems" :key="'dupe-'+index">
                <a href="#" class="flex-shrink-0 w-64 group">
                  <div class="relative rounded-xl overflow-hidden shadow-md">
                    <img :src="item.image" :alt="item.caption" class="w-full h-80 object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                      <div class="text-white text-center">
                        <div class="flex items-center justify-center gap-4 mb-2">
                          <span class="flex items-center gap-1">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                            {{ item.likes }}
                          </span>
                          <span class="flex items-center gap-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            {{ item.comments }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </a>
              </template>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Últimas Notícias -->
    <section class="py-16 bg-white">
      <div class="max-w-[1400px] mx-auto px-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Últimas Notícias</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <a v-for="(item, index) in ultimasNoticias" :key="index" :href="item.link" class="group">
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow">
              <img :src="item.image" :alt="item.title" class="w-full h-56 object-cover">
              <div class="p-6">
                <span class="text-blue-900 text-sm font-semibold uppercase">{{ item.category }}</span>
                <h3 class="text-xl font-bold text-gray-800 mt-2 mb-2 group-hover:text-blue-900 transition-colors">{{ item.title }}</h3>
                <p class="text-gray-600 line-clamp-2 mb-4">{{ item.excerpt }}</p>
                <div class="flex items-center justify-between text-sm text-gray-500">
                  <span>{{ item.date }}</span>
                  <span class="flex items-center gap-1 text-blue-900 font-semibold">
                    Ler mais
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                  </span>
                </div>
              </div>
            </div>
          </a>
        </div>
      </div>
    </section>
  </Layout>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import Layout from '@/Shared/Layout.vue';
import PropertyCard from '@/Shared/PropertyCard.vue';
import CategoryCarousel from '@/Shared/CategoryCarousel.vue';

// Component for Property Carousel
const PropertyCarousel = {
  props: ['properties'],
  setup(props) {
    const carouselRef = ref(null);
    const trackRef = ref(null);
    const position = ref(0);
    const isDragging = ref(false);
    const startX = ref(0);
    const startPosition = ref(0);
    let maxScroll = 0;

    const startDrag = (e) => {
      isDragging.value = true;
      startX.value = e.type.includes('mouse') ? e.pageX : e.touches[0].clientX;
      startPosition.value = position.value;
      if (trackRef.value) {
        trackRef.value.style.transition = 'none';
      }
    };

    const onDrag = (e) => {
      if (!isDragging.value) return;
      const currentX = e.type.includes('mouse') ? e.pageX : e.touches[0].clientX;
      const diff = currentX - startX.value;
      position.value = Math.min(0, Math.max(-maxScroll, startPosition.value + diff));
    };

    const stopDrag = () => {
      isDragging.value = false;
      if (trackRef.value) {
        trackRef.value.style.transition = 'transform 0.3s ease-out';
      }
    };

    onMounted(() => {
      if (carouselRef.value && trackRef.value) {
        maxScroll = trackRef.value.scrollWidth - carouselRef.value.clientWidth;
      }
      window.addEventListener('resize', () => {
        if (carouselRef.value && trackRef.value) {
          maxScroll = trackRef.value.scrollWidth - carouselRef.value.clientWidth;
        }
      });
    });

    onUnmounted(() => {
      window.removeEventListener('resize', () => {});
    });

    return { carouselRef, trackRef, position, startDrag, onDrag, stopDrag };
  },
  template: `
    <div class="relative">
      <div
        ref="carouselRef"
        class="overflow-x-hidden cursor-grab active:cursor-grabbing"
        @mousedown="startDrag"
        @mousemove="onDrag"
        @mouseup="stopDrag"
        @mouseleave="stopDrag"
        @touchstart="startDrag"
        @touchmove="onDrag"
        @touchend="stopDrag"
      >
        <div
          class="flex gap-4 transition-transform duration-300 ease-out"
          :style="{ transform: 'translateX(' + position + 'px)' }"
          ref="trackRef"
        >
          <a v-for="(property, index) in properties" :key="index" :href="'/imoveis/imovel-' + (index + 1)" class="group flex-shrink-0 w-80">
            <PropertyCard :property="property" />
          </a>
        </div>
      </div>
    </div>
  `
};

// Data
const selecaoEspecial = ref([
  { id: 1, title: 'Imóvel 1', address: 'Rua Exemplo, 123 - Bairro, Cidade', price: 450000, bedrooms: 3, bathrooms: 2, area: 130, type: 'Aluguel' },
  { id: 2, title: 'Imóvel 2', address: 'Rua Exemplo, 123 - Bairro, Cidade', price: 550000, bedrooms: 4, bathrooms: 3, area: 160, type: 'Venda' },
  { id: 3, title: 'Imóvel 3', address: 'Rua Exemplo, 123 - Bairro, Cidade', price: 650000, bedrooms: 5, bathrooms: 4, area: 190, type: 'Aluguel' },
  { id: 4, title: 'Imóvel 4', address: 'Rua Exemplo, 123 - Bairro, Cidade', price: 750000, bedrooms: 6, bathrooms: 5, area: 220, type: 'Venda' },
  { id: 5, title: 'Imóvel 5', address: 'Rua Exemplo, 123 - Bairro, Cidade', price: 850000, bedrooms: 7, bathrooms: 6, area: 250, type: 'Aluguel' },
  { id: 6, title: 'Imóvel 6', address: 'Rua Exemplo, 123 - Bairro, Cidade', price: 950000, bedrooms: 8, bathrooms: 7, area: 280, type: 'Venda' },
]);

const maisProcurados = ref([
  { id: 1, title: 'Imóvel A', address: 'Rua A, 123 - Bairro, Cidade', price: 380000, bedrooms: 2, bathrooms: 1, area: 90, type: 'Venda' },
  { id: 2, title: 'Imóvel B', address: 'Rua B, 123 - Bairro, Cidade', price: 420000, bedrooms: 3, bathrooms: 2, area: 110, type: 'Aluguel' },
  { id: 3, title: 'Imóvel C', address: 'Rua C, 123 - Bairro, Cidade', price: 510000, bedrooms: 4, bathrooms: 2, area: 140, type: 'Venda' },
  { id: 4, title: 'Imóvel D', address: 'Rua D, 123 - Bairro, Cidade', price: 630000, bedrooms: 5, bathrooms: 3, area: 170, type: 'Aluguel' },
  { id: 5, title: 'Imóvel E', address: 'Rua E, 123 - Bairro, Cidade', price: 770000, bedrooms: 6, bathrooms: 4, area: 210, type: 'Venda' },
]);

const vistoRecentemente = ref([
  { id: 1, title: 'Imóvel X', address: 'Rua X, 123 - Bairro, Cidade', price: 350000, bedrooms: 2, bathrooms: 1, area: 80, type: 'Venda' },
  { id: 2, title: 'Imóvel Y', address: 'Rua Y, 123 - Bairro, Cidade', price: 480000, bedrooms: 3, bathrooms: 2, area: 120, type: 'Aluguel' },
  { id: 3, title: 'Imóvel Z', address: 'Rua Z, 123 - Bairro, Cidade', price: 590000, bedrooms: 4, bathrooms: 3, area: 150, type: 'Venda' },
  { id: 4, title: 'Imóvel W', address: 'Rua W, 123 - Bairro, Cidade', price: 710000, bedrooms: 5, bathrooms: 4, area: 190, type: 'Aluguel' },
]);

const instagramItems = ref([
  { id: 1, image: 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=600&h=600&fit=crop', caption: 'Imóvel incrível', likes: '123', comments: '24' },
  { id: 2, image: 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=600&h=600&fit=crop', caption: 'Casa nova', likes: '89', comments: '15' },
  { id: 3, image: 'https://images.unsplash.com/photo-1600585154526-990dced4db0d?w=600&h=600&fit=crop', caption: 'Decor perfeito', likes: '234', comments: '41' },
  { id: 4, image: 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=600&h=600&fit=crop', caption: 'Vista linda', likes: '156', comments: '29' },
  { id: 5, image: 'https://images.unsplash.com/photo-1570129477492-45c003edd2be?w=600&h=600&fit=crop', caption: 'Lar dos sonhos', likes: '312', comments: '57' },
  { id: 6, image: 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&h=600&fit=crop', caption: 'Quarto novo', likes: '198', comments: '33' },
  { id: 7, image: 'https://images.unsplash.com/photo-1613490493576-7f17f7737b38?w=600&h=600&fit=crop', caption: 'Decoração', likes: '267', comments: '48' },
  { id: 8, image: 'https://images.unsplash.com/photo-1582560501396-11951a3e86c?w=600&h=600&fit=crop', caption: 'Sala linda', likes: '145', comments: '22' },
]);

const ultimasNoticias = ref([
  { id: 1, title: 'Dicas para comprar seu primeiro imóvel', excerpt: 'Confira as melhores dicas para quem está comprando seu primeiro imóvel e quer evitar erros.', category: 'Dicas', date: '01 de Junho, 2026', link: '#', image: 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=800&h=500&fit=crop' },
  { id: 2, title: 'Mercado imobiliário em alta no Brasil', excerpt: 'Veja as tendências do mercado imobiliário para o segundo semestre de 2026.', category: 'Mercado', date: '30 de Maio, 2026', link: '#', image: 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800&h=500&fit=crop' },
  { id: 3, title: 'Como decorar sua casa sem gastar muito', excerpt: 'Ideias criativas para decorar sua casa com estilo e sem gastar muito dinheiro.', category: 'Decoração', date: '28 de Maio, 2026', link: '#', image: 'https://images.unsplash.com/photo-1600585154526-990dced4db0d?w=800&h=500&fit=crop' },
]);

// Instagram Auto Scroll
const instagramPosition = ref(0);
const instagramTrackRef = ref(null);
let instagramInterval = null;

onMounted(() => {
  const itemWidth = 272; // 256 + 16 gap
  instagramInterval = setInterval(() => {
    instagramPosition.value -= 1;
    if (Math.abs(instagramPosition.value) >= itemWidth * instagramItems.value.length) {
      instagramPosition.value = 0;
    }
  }, 30);
});

onUnmounted(() => {
  if (instagramInterval) clearInterval(instagramInterval);
});
</script>
