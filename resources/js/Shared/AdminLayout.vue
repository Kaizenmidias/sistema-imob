<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-blue-900 text-white flex flex-col">
      <div class="p-6">
        <div class="flex items-center gap-3">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
          </svg>
          <span class="text-xl font-bold">Painel Admin</span>
        </div>
      </div>
      
      <nav class="mt-6 flex-1">
        <Link href="/admin" class="flex items-center gap-3 px-6 py-3 hover:bg-blue-800 transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
          </svg>
          <span>Dashboard</span>
        </Link>
        
        <Link href="/admin/properties" class="flex items-center gap-3 px-6 py-3 hover:bg-blue-800 transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
          </svg>
          <span>Imóveis</span>
        </Link>

        <Link href="/admin/business-types" class="flex items-center gap-3 px-6 py-3 hover:bg-blue-800 transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h6m6-10h2a2 2 0 012 2v4a2 2 0 01-2 2h-2m-6 0h6"></path>
          </svg>
          <span>Tipos de Negócio</span>
        </Link>

        <details class="group">
          <summary class="flex items-center gap-3 px-6 py-3 hover:bg-blue-800 transition cursor-pointer select-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6m16 0H4"></path>
            </svg>
            <span>Categorias</span>
          </summary>
          <div class="pl-14">
            <Link href="/admin/categories/property-types" class="block px-6 py-2 text-blue-100 hover:text-white hover:bg-blue-800 transition rounded-r">
              Tipos de Imóvel
            </Link>
            <Link href="/admin/categories/special" class="block px-6 py-2 text-blue-100 hover:text-white hover:bg-blue-800 transition rounded-r">
              Categorias Especiais
            </Link>
          </div>
        </details>
        
        <Link href="/admin/pages" class="flex items-center gap-3 px-6 py-3 hover:bg-blue-800 transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
          </svg>
          <span>Páginas</span>
        </Link>

        <details class="group">
          <summary class="flex items-center gap-3 px-6 py-3 hover:bg-blue-800 transition cursor-pointer select-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2"></path>
            </svg>
            <span>Blog</span>
          </summary>
          <div class="pl-14">
            <Link href="/admin/blog/posts" class="block px-6 py-2 text-blue-100 hover:text-white hover:bg-blue-800 transition rounded-r">
              Postagens
            </Link>
            <Link href="/admin/blog/categories" class="block px-6 py-2 text-blue-100 hover:text-white hover:bg-blue-800 transition rounded-r">
              Categorias
            </Link>
          </div>
        </details>
        
        <Link href="/admin/appearance" class="flex items-center gap-3 px-6 py-3 hover:bg-blue-800 transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
          </svg>
          <span>Aparência</span>
        </Link>
        
        <Link href="/admin/leads" class="flex items-center gap-3 px-6 py-3 hover:bg-blue-800 transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
          </svg>
          <span>Leads</span>
        </Link>
        
        <Link href="/admin/settings" class="flex items-center gap-3 px-6 py-3 hover:bg-blue-800 transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
          <span>Configurações</span>
        </Link>

        <Link href="/admin/instagram" class="flex items-center gap-3 px-6 py-3 hover:bg-blue-800 transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7c2.761 0 5 2.239 5 5s-2.239 5-5 5-5-2.239-5-5 2.239-5 5-5z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 3h-9A4.5 4.5 0 003 7.5v9A4.5 4.5 0 007.5 21h9a4.5 4.5 0 004.5-4.5v-9A4.5 4.5 0 0016.5 3z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.5 6.5h.01"></path>
          </svg>
          <span>Instagram</span>
        </Link>
      </nav>
      
      <div class="p-6 border-t border-blue-800">
        <a href="/" class="flex items-center gap-3 px-2 py-2 text-blue-200 hover:text-white transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          <span>Voltar ao Site</span>
        </a>
      </div>
    </aside>

    <!-- Content -->
    <main class="flex-1 overflow-auto">
      <header class="bg-white shadow px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">
          <slot name="pageTitle">Dashboard</slot>
        </h1>
      </header>
      <div class="p-6">
        <slot></slot>
      </div>
    </main>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>
