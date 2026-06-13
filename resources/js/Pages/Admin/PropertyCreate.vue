<template>
  <AdminLayout>
    <template #pageTitle>{{ isEdit ? 'Editar Imóvel' : 'Novo Imóvel' }}</template>
    
    <div v-if="showProcessingBanner" class="mb-6 rounded-xl border border-blue-200 bg-blue-50 px-4 py-4 text-blue-950">
      <div class="flex items-center justify-between gap-4">
        <div>
          <div class="font-semibold">Processando imagens em background</div>
          <div class="text-sm text-blue-900/80 mt-1">
            {{ processingSummaryText }}
          </div>
        </div>
        <div class="text-sm font-semibold whitespace-nowrap">
          {{ processingCounts.completed }}/{{ processingCounts.total }} concluídas
        </div>
      </div>
      <div class="mt-3 h-2 rounded-full bg-blue-100 overflow-hidden">
        <div class="h-2 rounded-full bg-blue-700 transition-all duration-300" :style="{ width: `${processingProgress}%` }"></div>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6 border border-gray-200">
      <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="space-y-6">
          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">Título</label>
            <input v-model="form.titulo" type="text" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="Título do imóvel">
            <div v-if="form.errors.titulo" class="text-sm text-red-600 mt-1">{{ form.errors.titulo }}</div>
          </div>
          
          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">Código de Referência</label>
            <input v-model="form.codigo_referencia" :disabled="!isEdit" type="text" class="w-full border border-gray-300 rounded-lg px-4 py-3 disabled:bg-gray-100 disabled:text-gray-500" placeholder="Gerado automaticamente">
            <div v-if="form.errors.codigo_referencia" class="text-sm text-red-600 mt-1">{{ form.errors.codigo_referencia }}</div>
          </div>
          
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-gray-700 mb-2 text-sm font-medium">Categoria</label>
              <select v-model="form.tipo_propriedade_id" class="w-full border border-gray-300 rounded-lg px-4 py-3">
                <option v-for="type in propertyTypes" :key="type.id" :value="type.id">
                  {{ type.nome_subtipo ? `${type.nome_tipo} / ${type.nome_subtipo}` : type.nome_tipo }}
                </option>
              </select>
              <div v-if="form.errors.tipo_propriedade_id" class="text-sm text-red-600 mt-1">{{ form.errors.tipo_propriedade_id }}</div>
            </div>
            <div>
              <label class="block text-gray-700 mb-2 text-sm font-medium">Negócio</label>
              <select v-model="form.business_type_id" class="w-full border border-gray-300 rounded-lg px-4 py-3">
                <option v-for="bt in businessTypes" :key="bt.id" :value="bt.id">{{ bt.name }}</option>
              </select>
              <div v-if="form.errors.business_type_id" class="text-sm text-red-600 mt-1">{{ form.errors.business_type_id }}</div>
            </div>
          </div>
          
          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">Preço</label>
            <input v-model="form.valor" type="text" inputmode="numeric" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="R$ 0.000,00" @input="onPriceInput">
            <div v-if="form.errors.valor" class="text-sm text-red-600 mt-1">{{ form.errors.valor }}</div>
          </div>
          
          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">Endereço</label>
            <input v-model="form.endereco" type="text" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="Rua, Número">
            <div v-if="form.errors.endereco" class="text-sm text-red-600 mt-1">{{ form.errors.endereco }}</div>
          </div>

          <div class="grid grid-cols-3 gap-4">
            <div>
              <label class="block text-gray-700 mb-2 text-sm font-medium">Bairro</label>
              <input v-model="form.bairro" type="text" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="Bairro">
              <div v-if="form.errors.bairro" class="text-sm text-red-600 mt-1">{{ form.errors.bairro }}</div>
            </div>
            <div>
              <label class="block text-gray-700 mb-2 text-sm font-medium">Cidade</label>
              <input v-model="form.cidade" type="text" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="Cidade">
              <div v-if="form.errors.cidade" class="text-sm text-red-600 mt-1">{{ form.errors.cidade }}</div>
            </div>
            <div>
              <label class="block text-gray-700 mb-2 text-sm font-medium">UF</label>
              <input v-model="form.estado" type="text" maxlength="2" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="SP">
              <div v-if="form.errors.estado" class="text-sm text-red-600 mt-1">{{ form.errors.estado }}</div>
            </div>
          </div>
          
          <div class="grid grid-cols-3 gap-4">
            <div>
              <label class="block text-gray-700 mb-2 text-sm font-medium">Quartos</label>
              <input v-model.number="form.quartos" type="number" min="0" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="0">
              <div v-if="form.errors.quartos" class="text-sm text-red-600 mt-1">{{ form.errors.quartos }}</div>
            </div>
            <div>
              <label class="block text-gray-700 mb-2 text-sm font-medium">Banheiros</label>
              <input v-model.number="form.banheiros" type="number" min="0" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="0">
              <div v-if="form.errors.banheiros" class="text-sm text-red-600 mt-1">{{ form.errors.banheiros }}</div>
            </div>
            <div>
              <label class="block text-gray-700 mb-2 text-sm font-medium">Garagens</label>
              <input v-model.number="form.garagens" type="number" min="0" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="0">
              <div v-if="form.errors.garagens" class="text-sm text-red-600 mt-1">{{ form.errors.garagens }}</div>
            </div>
          </div>
        </div>
        
        <div class="space-y-6">
          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">Área (m²)</label>
            <input type="number" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="0">
          </div>

          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">Flags</label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <label class="flex items-center gap-2 text-sm text-gray-700">
                <input v-model="form.is_exclusive" type="checkbox" class="rounded border-gray-300">
                Exclusivo
              </label>
              <label class="flex items-center gap-2 text-sm text-gray-700">
                <input v-model="form.is_off_market" type="checkbox" class="rounded border-gray-300">
                Off Market
              </label>
            </div>
            <div v-if="form.errors.is_exclusive" class="text-sm text-red-600 mt-1">{{ form.errors.is_exclusive }}</div>
            <div v-if="form.errors.is_off_market" class="text-sm text-red-600 mt-1">{{ form.errors.is_off_market }}</div>
          </div>

          <div v-if="specialCategories.length > 0">
            <label class="block text-gray-700 mb-2 text-sm font-medium">Categorias Especiais</label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <label v-for="sc in specialCategories" :key="sc.id" class="flex items-center gap-2 text-sm text-gray-700">
                <input v-model="form.special_category_ids" type="checkbox" :value="sc.id" class="rounded border-gray-300">
                {{ sc.name }}
              </label>
            </div>
            <div v-if="form.errors.special_category_ids" class="text-sm text-red-600 mt-1">{{ form.errors.special_category_ids }}</div>
          </div>

          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">Exibir na Home</label>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
              <label class="flex items-center gap-2 text-sm text-gray-700">
                <input v-model="form.show_in_home_selecao_especial" type="checkbox" class="rounded border-gray-300">
                Seleção especial
              </label>
              <label class="flex items-center gap-2 text-sm text-gray-700">
                <input v-model="form.show_in_home_mais_procurados" type="checkbox" class="rounded border-gray-300">
                Mais procurados
              </label>
              <label class="flex items-center gap-2 text-sm text-gray-700">
                <input v-model="form.show_in_home_visto_recentemente" type="checkbox" class="rounded border-gray-300">
                Visto recentemente
              </label>
            </div>
            <div v-if="form.errors.show_in_home_selecao_especial" class="text-sm text-red-600 mt-1">{{ form.errors.show_in_home_selecao_especial }}</div>
            <div v-if="form.errors.show_in_home_mais_procurados" class="text-sm text-red-600 mt-1">{{ form.errors.show_in_home_mais_procurados }}</div>
            <div v-if="form.errors.show_in_home_visto_recentemente" class="text-sm text-red-600 mt-1">{{ form.errors.show_in_home_visto_recentemente }}</div>
          </div>

          <PropertyImageUploader
            ref="imageUploaderRef"
            :existing-photos="propertyPhotos"
            :upload-url="`${adminBase}/properties/uploads`"
            :delete-upload-base-url="`${adminBase}/properties/uploads`"
            :max-files="imageUploadConfig?.maxFiles ?? null"
            :max-file-size-bytes="imageUploadConfig?.maxFileSizeBytes || (10 * 1024 * 1024)"
            :parallel-uploads="imageUploadConfig?.parallelUploads || 6"
          />
          <div v-if="form.errors.featured_upload_token" class="text-sm text-red-600 -mt-4">{{ form.errors.featured_upload_token }}</div>
          <div v-if="form.errors.gallery_upload_tokens" class="text-sm text-red-600 -mt-4">{{ form.errors.gallery_upload_tokens }}</div>
          <div v-if="uploadFormError" class="text-sm text-red-600 -mt-4">{{ uploadFormError }}</div>
          
          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">Descrição</label>
            <textarea v-model="form.descricao" rows="8" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="Descrição do imóvel..."></textarea>
            <div v-if="form.errors.descricao" class="text-sm text-red-600 mt-1">{{ form.errors.descricao }}</div>
          </div>

          <div>
            <label class="block text-gray-700 mb-2 text-sm font-medium">SEO</label>
            <div class="space-y-4">
              <div>
                <div class="text-sm text-gray-700 font-medium mb-2">Meta Title</div>
                <input v-model="form.meta_title" type="text" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="Título SEO (opcional)">
                <div v-if="form.errors.meta_title" class="text-sm text-red-600 mt-1">{{ form.errors.meta_title }}</div>
              </div>
              <div>
                <div class="text-sm text-gray-700 font-medium mb-2">Meta Description</div>
                <textarea v-model="form.meta_description" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="Descrição SEO (opcional)"></textarea>
                <div v-if="form.errors.meta_description" class="text-sm text-red-600 mt-1">{{ form.errors.meta_description }}</div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="lg:col-span-2">
          <button type="submit" :disabled="form.processing" class="bg-blue-900 hover:bg-blue-800 disabled:opacity-60 text-white px-8 py-3 rounded-lg font-semibold transition">
            Salvar Imóvel
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup>
import axios from 'axios';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Shared/AdminLayout.vue';
import PropertyImageUploader from '@/Shared/PropertyImageUploader.vue';

const page = usePage();
const adminBase = computed(() => page.props?.paths?.admin || '/admin');

const props = defineProps({
  propertyTypes: {
    type: Array,
    default: () => [],
  },
  businessTypes: {
    type: Array,
    default: () => [],
  },
  specialCategories: {
    type: Array,
    default: () => [],
  },
  property: {
    type: Object,
    default: null,
  },
  selectedSpecialCategoryIds: {
    type: Array,
    default: () => [],
  },
  generatedReferenceCode: {
    type: String,
    default: '',
  },
  imageUploadConfig: {
    type: Object,
    default: () => ({ maxFiles: null, maxFileSizeBytes: 10 * 1024 * 1024, parallelUploads: 6, pollIntervalMs: 4000 }),
  },
});

const isEdit = computed(() => !!props.property?.id);

const defaultPropertyTypeId = computed(() => props.propertyTypes[0]?.id ?? null);
const defaultBusinessTypeId = computed(() => props.businessTypes[0]?.id ?? null);

const formatCurrencyNumberBRL = (value) => {
  const number = Number(value || 0);
  return number.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
};

const form = useForm({
  titulo: props.property?.titulo || '',
  codigo_referencia: props.property?.codigo_referencia || (isEdit.value ? '' : props.generatedReferenceCode),
  meta_title: props.property?.meta_title || '',
  meta_description: props.property?.meta_description || '',
  descricao: props.property?.descricao || '',
  tipo_propriedade_id: props.property?.tipo_propriedade_id ?? defaultPropertyTypeId.value,
  business_type_id: props.property?.business_type_id ?? defaultBusinessTypeId.value,
  valor: isEdit.value ? formatCurrencyNumberBRL(props.property?.valor) : 'R$ 0,00',
  endereco: props.property?.endereco || '',
  bairro: props.property?.bairro || '',
  cidade: props.property?.cidade || '',
  estado: props.property?.estado || 'SP',
  quartos: props.property?.quartos ?? null,
  banheiros: props.property?.banheiros ?? null,
  garagens: props.property?.garagens ?? null,
  is_exclusive: !!props.property?.is_exclusive,
  is_off_market: !!props.property?.is_off_market,
  show_in_home_selecao_especial: !!props.property?.show_in_home_selecao_especial,
  show_in_home_mais_procurados: !!props.property?.show_in_home_mais_procurados,
  show_in_home_visto_recentemente: !!props.property?.show_in_home_visto_recentemente,
  featured_upload_token: null,
  gallery_upload_tokens: [],
  remove_photo_ids: [],
  photo_order_ids: [],
  special_category_ids: props.selectedSpecialCategoryIds || [],
});

const imageUploaderRef = ref(null);
const uploadFormError = ref('');
const propertyPhotos = ref(Array.isArray(props.property?.photos) ? props.property.photos : []);
const processingCounts = ref({
  total: propertyPhotos.value.length,
  pending: propertyPhotos.value.filter((photo) => photo?.processing_status === 'pending').length,
  processing: propertyPhotos.value.filter((photo) => photo?.processing_status === 'processing').length,
  completed: propertyPhotos.value.filter((photo) => photo?.processing_status === 'completed').length,
  failed: propertyPhotos.value.filter((photo) => photo?.processing_status === 'failed').length,
});
let processingTimer = null;

const formatCurrencyBRL = (value) => {
  const digits = String(value ?? '').replace(/\D/g, '');
  const number = Number(digits) / 100;
  return number.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
};

const showProcessingBanner = computed(() => isEdit.value && processingCounts.value.total > 0 && (processingCounts.value.pending > 0 || processingCounts.value.processing > 0 || processingCounts.value.failed > 0));
const processingProgress = computed(() => {
  const total = Number(processingCounts.value.total || 0);
  if (!total) return 0;
  return Math.max(0, Math.min(100, Math.round((Number(processingCounts.value.completed || 0) / total) * 100)));
});
const processingSummaryText = computed(() => {
  const { pending, processing, failed } = processingCounts.value;
  if (failed > 0) {
    return `${processingCounts.value.completed} concluídas, ${failed} com falha e ${pending + processing} ainda em processamento.`;
  }

  return `${pending} pendentes e ${processing} em processamento na fila.`;
});

const onPriceInput = () => {
  form.valor = formatCurrencyBRL(form.valor);
};

async function refreshProcessingStatus() {
  if (!isEdit.value || !props.property?.id) return;

  try {
    const response = await axios.get(`${adminBase.value}/properties/${props.property.id}/image-processing-status`);
    const counts = response.data?.counts || {};
    processingCounts.value = {
      total: Number(counts.total || 0),
      pending: Number(counts.pending || 0),
      processing: Number(counts.processing || 0),
      completed: Number(counts.completed || 0),
      failed: Number(counts.failed || 0),
    };

    if (Array.isArray(response.data?.photos)) {
      propertyPhotos.value = response.data.photos;
    }

    if ((processingCounts.value.pending + processingCounts.value.processing) === 0 && processingTimer) {
      clearInterval(processingTimer);
      processingTimer = null;
    }
  } catch {
  }
}

const submit = () => {
  uploadFormError.value = '';

  const payload = imageUploaderRef.value?.getSubmissionPayload?.();
  if (!payload) {
    uploadFormError.value = 'Nao foi possivel preparar o envio das imagens.';
    return;
  }

  if (payload.hasPendingUploads) {
    uploadFormError.value = 'Aguarde o termino dos uploads antes de salvar o imovel.';
    return;
  }

  if (payload.hasUploadErrors) {
    uploadFormError.value = 'Existem imagens com falha de envio. Reenvie ou remova antes de salvar.';
    return;
  }

  form.featured_upload_token = payload.featured_upload_token;
  form.gallery_upload_tokens = payload.gallery_upload_tokens;
  form.remove_photo_ids = payload.remove_photo_ids;
  form.photo_order_ids = payload.photo_order_ids;

  if (isEdit.value) {
    form.transform((data) => ({ ...data, _method: 'put' })).post(`${adminBase.value}/properties/${props.property.id}`, { forceFormData: true });
    return;
  }

  form.post(`${adminBase.value}/properties`, { forceFormData: true });
};

onMounted(() => {
  if (!isEdit.value || !props.property?.id) return;

  if ((processingCounts.value.pending + processingCounts.value.processing + processingCounts.value.failed) > 0) {
    refreshProcessingStatus();
  }

  processingTimer = setInterval(refreshProcessingStatus, props.imageUploadConfig?.pollIntervalMs || 4000);
});

onBeforeUnmount(() => {
  if (processingTimer) {
    clearInterval(processingTimer);
    processingTimer = null;
  }
});
</script>
