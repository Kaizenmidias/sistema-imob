<template>
  <AdminLayout>
    <template #pageTitle>Novo Imóvel</template>
    
    <div class="bg-white rounded-xl shadow p-6 border border-gray-200">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="space-y-6">
          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">Título</label>
            <input type="text" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="Título do imóvel">
          </div>
          
          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">Código de Referência</label>
            <input type="text" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="Código">
          </div>
          
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-gray-700 mb-2 text-sm font-medium">Tipo</label>
              <select class="w-full border border-gray-300 rounded-lg px-4 py-3">
                <option>Casa</option>
                <option>Apartamento</option>
              </select>
            </div>
            <div>
              <label class="block text-gray-700 mb-2 text-sm font-medium">Negócio</label>
              <select class="w-full border border-gray-300 rounded-lg px-4 py-3">
                <option>Venda</option>
                <option>Aluguel</option>
              </select>
            </div>
          </div>
          
          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">Preço</label>
            <input type="text" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="R$ 0,00">
          </div>
          
          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">Endereço</label>
            <input type="text" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="Rua, Número">
          </div>
          
          <div class="grid grid-cols-3 gap-4">
            <div>
              <label class="block text-gray-700 mb-2 text-sm font-medium">Quartos</label>
              <input type="number" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="0">
            </div>
            <div>
              <label class="block text-gray-700 mb-2 text-sm font-medium">Banheiros</label>
              <input type="number" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="0">
            </div>
            <div>
              <label class="block text-gray-700 mb-2 text-sm font-medium">Garagens</label>
              <input type="number" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="0">
            </div>
          </div>
        </div>
        
        <div class="space-y-6">
          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">Área (m²)</label>
            <input type="number" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="0">
          </div>
          
          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">Fotos</label>
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-blue-400 transition cursor-pointer">
              <svg class="w-10 h-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              <p class="text-gray-600">Clique para enviar fotos</p>
            </div>
          </div>
          
          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">Descrição</label>
            <textarea rows="8" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="Descrição do imóvel..."></textarea>
          </div>
        </div>
        
        <div class="lg:col-span-2">
          <button class="bg-blue-900 hover:bg-blue-800 text-white px-8 py-3 rounded-lg font-semibold transition">
            Salvar Imóvel
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Shared/AdminLayout.vue';
</script>
