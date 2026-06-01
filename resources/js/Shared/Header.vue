<template>
  <header class="bg-blue-900 text-white sticky top-0 z-40 shadow-md">
    <div class="container mx-auto px-4">
      <div class="flex items-center justify-between h-20">
        <!-- Logo -->
        <div class="flex items-center">
          <div class="text-2xl font-bold flex items-center gap-2">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span>Imobiliária</span>
          </div>
        </div>

        <!-- Desktop Nav -->
        <nav class="hidden md:flex items-center space-x-8">
          <a href="/" class="hover:text-blue-200 transition font-medium">Início</a>
          <a href="/imoveis" class="hover:text-blue-200 transition font-medium">Imóveis</a>
          <a href="/venda-seu-imovel" class="hover:text-blue-200 transition font-medium">Venda seu Imóvel</a>
        </nav>

        <!-- Right Side -->
        <div class="flex items-center space-x-4">
          <!-- Search -->
          <button class="p-2 hover:bg-blue-800 rounded-full transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </button>

          <!-- Favorites -->
          <button class="p-2 hover:bg-blue-800 rounded-full transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
          </button>

          <!-- Hamburger Menu -->
          <button @click="openMenu" class="p-2 hover:bg-blue-800 rounded-full transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Drawer Menu -->
    <transition name="fade">
      <DrawerMenu v-if="isMenuOpen" :is-open="isMenuOpen" :menu-items="menuItems" @close="closeMenu" />
    </transition>
  </header>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import DrawerMenu from './DrawerMenu.vue';

const isMenuOpen = ref(false);
const menuItems = ref([
  { id: 1, label: 'Quem Somos', icon: 'users', url: '/quem-somos' },
  { id: 2, label: 'Off Market', icon: 'tag', url: '/off-market' },
  { id: 3, label: 'Gestão Exclusiva', icon: 'key', url: '/gestao-exclusiva' },
  { id: 4, label: 'Calculadora', icon: 'calculator', url: '/calculadora' },
  { id: 5, label: 'Avalie seu Imóvel', icon: 'home', url: '/avalie-seu-imovel' },
  { id: 6, label: 'Corretor Parceiro', icon: 'user-tie', url: '/corretor-parceiro' },
  { id: 7, label: 'Blog', icon: 'newspaper', url: '/blog' },
  { id: 8, label: 'Contatos', icon: 'phone', url: '/contato' },
]);

function openMenu() {
  isMenuOpen.value = true;
}

function closeMenu() {
  isMenuOpen.value = false;
}
</script>
