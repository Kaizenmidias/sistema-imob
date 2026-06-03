<template>
  <AdminLayout>
    <template #pageTitle>Editar usuário</template>

    <div class="bg-white rounded-xl shadow border border-gray-200 p-6 max-w-2xl">
      <div class="flex items-center gap-4 mb-6">
        <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-100 border border-gray-200 flex items-center justify-center">
          <img v-if="user?.profile_photo_url" :src="user.profile_photo_url" alt="" class="w-full h-full object-cover" />
          <span v-else class="text-sm font-semibold text-gray-600">{{ initials }}</span>
        </div>
        <div class="min-w-0">
          <div class="text-sm text-gray-500">Usuário</div>
          <div class="font-semibold text-gray-900 truncate">{{ user?.name }}</div>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
          <label class="block text-gray-700 mb-2 text-sm font-medium">Nome</label>
          <input v-model="form.name" type="text" class="w-full border border-gray-300 rounded-lg px-4 py-3" />
          <div v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</div>
        </div>

        <div class="md:col-span-2">
          <label class="block text-gray-700 mb-2 text-sm font-medium">E-mail</label>
          <input v-model="form.email" type="email" class="w-full border border-gray-300 rounded-lg px-4 py-3" />
          <div v-if="form.errors.email" class="text-sm text-red-600 mt-1">{{ form.errors.email }}</div>
        </div>

        <div>
          <label class="block text-gray-700 mb-2 text-sm font-medium">Perfil</label>
          <select v-model="form.role" class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white">
            <option value="user">Usuário</option>
            <option value="admin">Administrador</option>
          </select>
          <div v-if="form.errors.role" class="text-sm text-red-600 mt-1">{{ form.errors.role }}</div>
        </div>

        <div>
          <label class="block text-gray-700 mb-2 text-sm font-medium">Acesso ao painel</label>
          <select v-model="form.admin_enabled" class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white">
            <option :value="true">Ativo</option>
            <option :value="false">Bloqueado</option>
          </select>
          <div v-if="form.errors.admin_enabled" class="text-sm text-red-600 mt-1">{{ form.errors.admin_enabled }}</div>
        </div>

        <div class="md:col-span-2">
          <label class="block text-gray-700 mb-2 text-sm font-medium">Nova senha (opcional)</label>
          <input v-model="form.password" type="password" class="w-full border border-gray-300 rounded-lg px-4 py-3" />
          <div v-if="form.errors.password" class="text-sm text-red-600 mt-1">{{ form.errors.password }}</div>
        </div>

        <div class="md:col-span-2">
          <label class="block text-gray-700 mb-2 text-sm font-medium">Confirmar nova senha</label>
          <input v-model="form.password_confirmation" type="password" class="w-full border border-gray-300 rounded-lg px-4 py-3" />
        </div>
      </div>

      <div class="flex items-center gap-3 mt-6">
        <button
          type="button"
          class="bg-blue-900 hover:bg-blue-800 disabled:opacity-60 text-white px-6 py-2 rounded-lg font-semibold transition"
          :disabled="form.processing"
          @click="save"
        >
          Salvar
        </button>
        <Link :href="`${adminBase}/users`" class="text-gray-700 hover:text-gray-900 font-semibold">Voltar</Link>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Shared/AdminLayout.vue';

const page = usePage();
const adminBase = computed(() => page.props?.paths?.admin || '/admin');

const props = defineProps({
  user: { type: Object, default: null },
});

const user = computed(() => props.user || null);

const initials = computed(() => {
  const name = String(user.value?.name || '').trim();
  if (!name) return 'U';
  const parts = name.split(/\s+/).filter(Boolean).slice(0, 2);
  return parts.map((p) => p[0]?.toUpperCase()).join('');
});

const form = useForm({
  name: user.value?.name || '',
  email: user.value?.email || '',
  role: user.value?.role || 'user',
  admin_enabled: !!user.value?.admin_enabled,
  password: '',
  password_confirmation: '',
});

const save = () => {
  form.post(`${adminBase.value}/users/${user.value.id}`, { method: 'put', preserveScroll: true });
};
</script>

