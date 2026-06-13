<template>
  <div class="space-y-6">
    <div>
      <div class="flex items-center justify-between gap-3">
        <label class="block text-gray-700 text-sm font-medium">Imagem de Destaque</label>
        <div class="text-xs text-gray-500">
          {{ uploadSummary }}
        </div>
      </div>
      <input ref="featuredInputRef" type="file" :accept="acceptAttr" class="hidden" @change="onFeaturedSelected">
      <div
        class="mt-2 border-2 border-dashed rounded-xl p-6 text-center transition cursor-pointer"
        :class="featuredDropActive ? 'border-blue-500 bg-blue-50' : 'border-gray-300 hover:border-blue-400'"
        @click="openFeaturedPicker"
        @dragenter.prevent="featuredDropActive = true"
        @dragover.prevent="featuredDropActive = true"
        @dragleave.prevent="featuredDropActive = false"
        @drop.prevent="onFeaturedDrop"
      >
        <svg class="w-10 h-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        <p class="text-gray-700 font-medium">Arraste ou clique para enviar a imagem de destaque</p>
        <p class="text-xs text-gray-500 mt-2">Formatos: JPG, PNG, WEBP, HEIC. Maximo {{ maxSizeLabel }} por arquivo.</p>
      </div>

      <div v-if="featuredItem" class="mt-4 border border-gray-200 rounded-xl overflow-hidden bg-white">
        <img :src="featuredItem.previewUrl" alt="Imagem de destaque" class="w-full h-52 object-cover">
        <div class="p-4 flex items-start justify-between gap-4">
          <div class="min-w-0">
            <div class="font-medium text-gray-900 truncate">{{ featuredItem.name }}</div>
            <div class="text-xs text-gray-500 mt-1">{{ statusLabel(featuredItem) }}</div>
            <div v-if="featuredItem.error" class="text-xs text-red-600 mt-1">{{ featuredItem.error }}</div>
            <div v-if="featuredItem.progress > 0 && featuredItem.progress < 100" class="mt-3 w-full max-w-xs bg-gray-100 rounded-full h-2">
              <div class="bg-blue-600 h-2 rounded-full transition-all" :style="{ width: `${featuredItem.progress}%` }"></div>
            </div>
          </div>
          <div class="flex gap-2 shrink-0">
            <button v-if="featuredItem.status === 'error'" type="button" class="text-xs font-semibold text-blue-700 hover:text-blue-900" @click.stop="retryItem(featuredItem)">
              Reenviar
            </button>
            <button type="button" class="text-xs font-semibold text-red-600 hover:text-red-800" @click.stop="removeFeatured">
              Remover
            </button>
          </div>
        </div>
      </div>
    </div>

    <div>
      <div class="flex items-center justify-between gap-3">
        <label class="block text-gray-700 text-sm font-medium">Galeria</label>
        <div class="text-xs text-gray-500">
          Ate {{ maxFiles }} imagens por imovel
        </div>
      </div>
      <input ref="galleryInputRef" type="file" :accept="acceptAttr" multiple class="hidden" @change="onGallerySelected">
      <div
        class="mt-2 border-2 border-dashed rounded-xl p-6 text-center transition cursor-pointer"
        :class="galleryDropActive ? 'border-blue-500 bg-blue-50' : 'border-gray-300 hover:border-blue-400'"
        @click="openGalleryPicker"
        @dragenter.prevent="galleryDropActive = true"
        @dragover.prevent="galleryDropActive = true"
        @dragleave.prevent="galleryDropActive = false"
        @drop.prevent="onGalleryDrop"
      >
        <svg class="w-10 h-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        <p class="text-gray-700 font-medium">Arraste varias imagens ou clique para enviar</p>
        <p class="text-xs text-gray-500 mt-2">As imagens sobem primeiro para uma area temporaria segura e serao processadas em background.</p>
      </div>

      <div v-if="uploadError" class="text-sm text-red-600 mt-2">{{ uploadError }}</div>

      <div v-if="galleryItems.length > 0" class="mt-4 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
        <div
          v-for="item in galleryItems"
          :key="item.id"
          class="relative border border-gray-200 rounded-xl overflow-hidden bg-white shadow-sm"
          draggable="true"
          @dragstart="onDragStart(item.id)"
          @dragover.prevent
          @drop="onDrop(item.id)"
        >
          <img :src="item.previewUrl" class="w-full h-28 object-cover">
          <div class="p-3">
            <div class="text-xs font-medium text-gray-900 truncate">{{ item.name }}</div>
            <div class="text-[11px] text-gray-500 mt-1">{{ statusLabel(item) }}</div>
            <div v-if="item.progress > 0 && item.progress < 100" class="mt-2 bg-gray-100 rounded-full h-1.5">
              <div class="bg-blue-600 h-1.5 rounded-full transition-all" :style="{ width: `${item.progress}%` }"></div>
            </div>
            <div v-if="item.error" class="mt-1 text-[11px] text-red-600 line-clamp-2">{{ item.error }}</div>
          </div>
          <div class="absolute top-2 right-2 flex gap-1">
            <button v-if="item.status === 'error'" type="button" class="bg-white/95 hover:bg-white text-blue-700 px-2 py-1 rounded text-[11px] font-semibold" @click.stop="retryItem(item)">
              Retry
            </button>
            <button type="button" class="bg-white/95 hover:bg-white text-red-600 px-2 py-1 rounded text-[11px] font-semibold" @click.stop="removeGallery(item.id)">
              Remover
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios';
import { computed, ref } from 'vue';

const props = defineProps({
  existingPhotos: {
    type: Array,
    default: () => [],
  },
  uploadUrl: {
    type: String,
    required: true,
  },
  deleteUploadBaseUrl: {
    type: String,
    required: true,
  },
  maxFiles: {
    type: Number,
    default: 50,
  },
  maxFileSizeBytes: {
    type: Number,
    default: 20 * 1024 * 1024,
  },
});

const acceptAttr = '.jpg,.jpeg,.png,.webp,.heic,.heif';
const placeholderImage = `data:image/svg+xml,${encodeURIComponent(
  `<svg xmlns="http://www.w3.org/2000/svg" width="800" height="600" viewBox="0 0 800 600">
    <rect width="800" height="600" fill="#0f172a"/>
    <rect x="80" y="110" width="640" height="380" rx="24" fill="rgba(255,255,255,0.08)"/>
    <text x="400" y="322" text-anchor="middle" font-family="Arial, sans-serif" font-size="28" fill="rgba(255,255,255,0.75)">Processando imagem</text>
  </svg>`
)}`;

const featuredInputRef = ref(null);
const galleryInputRef = ref(null);
const featuredDropActive = ref(false);
const galleryDropActive = ref(false);
const dragSourceId = ref(null);
const uploadError = ref('');
const removedPhotoIds = ref([]);

const existingFeatured = props.existingPhotos.find((photo) => photo?.principal);
const existingGallery = props.existingPhotos
  .filter((photo) => !photo?.principal)
  .sort((a, b) => Number(a?.ordem ?? 0) - Number(b?.ordem ?? 0));

const featuredItem = ref(existingFeatured ? normalizeExistingItem(existingFeatured) : null);
const galleryItems = ref(existingGallery.map(normalizeExistingItem));

const maxSizeLabel = computed(() => `${Math.round(props.maxFileSizeBytes / 1024 / 1024)}MB`);
const uploadSummary = computed(() => `${currentImageCount()} / ${props.maxFiles} imagens`);

function normalizeExistingItem(photo) {
  const processing = photo?.processing_status && photo.processing_status !== 'processed';

  return {
    id: `existing-${photo.id}`,
    existingPhotoId: photo.id,
    token: null,
    file: null,
    previewUrl: photo.thumb_small_url || photo.url || placeholderImage,
    name: photo.principal ? 'Imagem de destaque' : `Imagem ${photo.id}`,
    status: processing ? 'processing' : 'uploaded',
    progress: processing ? 100 : 0,
    error: photo.processing_error || '',
    isExisting: true,
  };
}

function createUploadItem(file) {
  return {
    id: crypto.randomUUID(),
    existingPhotoId: null,
    token: null,
    file,
    previewUrl: URL.createObjectURL(file),
    name: file.name,
    status: 'queued',
    progress: 0,
    error: '',
    isExisting: false,
  };
}

function openFeaturedPicker() {
  featuredInputRef.value?.click();
}

function openGalleryPicker() {
  galleryInputRef.value?.click();
}

async function onFeaturedSelected(event) {
  const file = event.target.files?.[0] || null;
  if (!file) return;
  await replaceFeatured(file);
  if (featuredInputRef.value) featuredInputRef.value.value = '';
}

async function onGallerySelected(event) {
  const files = Array.from(event.target.files || []);
  await appendGallery(files);
  if (galleryInputRef.value) galleryInputRef.value.value = '';
}

async function onFeaturedDrop(event) {
  featuredDropActive.value = false;
  const file = Array.from(event.dataTransfer?.files || [])[0];
  if (!file) return;
  await replaceFeatured(file);
}

async function onGalleryDrop(event) {
  galleryDropActive.value = false;
  const files = Array.from(event.dataTransfer?.files || []);
  await appendGallery(files);
}

async function replaceFeatured(file) {
  uploadError.value = '';
  if (!validateBeforeAdd([file])) return;

  await removeFeatured();
  const item = createUploadItem(file);
  featuredItem.value = item;
  await uploadItem(item);
}

async function appendGallery(files) {
  uploadError.value = '';
  const validFiles = files.filter(Boolean);
  if (!validateBeforeAdd(validFiles)) return;

  for (const file of validFiles) {
    const item = createUploadItem(file);
    galleryItems.value.push(item);
    await uploadItem(item);
  }
}

function validateBeforeAdd(files) {
  if (currentImageCount() + files.length > props.maxFiles) {
    uploadError.value = `O limite maximo por imovel e de ${props.maxFiles} imagens.`;
    return false;
  }

  for (const file of files) {
    if (file.size > props.maxFileSizeBytes) {
      uploadError.value = `A imagem ${file.name} excede o limite de ${maxSizeLabel.value}.`;
      return false;
    }
  }

  return true;
}

async function uploadItem(item) {
  if (!(item.file instanceof File)) return;

  item.status = 'uploading';
  item.progress = 0;
  item.error = '';

  const formData = new FormData();
  formData.append('file', item.file);

  try {
    const response = await axios.post(props.uploadUrl, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
      onUploadProgress: (event) => {
        if (!event.total) return;
        item.progress = Math.round((event.loaded / event.total) * 100);
      },
    });

    item.token = response.data?.token || null;
    item.status = 'uploaded';
    item.progress = 100;
  } catch (error) {
    item.status = 'error';
    item.error = error?.response?.data?.message || 'Falha ao enviar a imagem.';
  }
}

async function removeFeatured() {
  if (!featuredItem.value) return;
  await removeItemToken(featuredItem.value);

  if (featuredItem.value.existingPhotoId) {
    removedPhotoIds.value = Array.from(new Set([...removedPhotoIds.value, featuredItem.value.existingPhotoId]));
  }

  featuredItem.value = null;
}

async function removeGallery(id) {
  const item = galleryItems.value.find((entry) => entry.id === id);
  if (!item) return;

  await removeItemToken(item);

  if (item.existingPhotoId) {
    removedPhotoIds.value = Array.from(new Set([...removedPhotoIds.value, item.existingPhotoId]));
  }

  galleryItems.value = galleryItems.value.filter((entry) => entry.id !== id);
}

async function removeItemToken(item) {
  if (!item?.token) return;

  try {
    await axios.delete(`${props.deleteUploadBaseUrl}/${item.token}`);
  } catch {
    // Best effort cleanup. The temp upload also expires server-side.
  }
}

async function retryItem(item) {
  if (!(item?.file instanceof File)) return;
  await uploadItem(item);
}

function onDragStart(id) {
  dragSourceId.value = id;
}

function onDrop(targetId) {
  const sourceId = dragSourceId.value;
  dragSourceId.value = null;
  if (!sourceId || sourceId === targetId) return;

  const list = [...galleryItems.value];
  const sourceIndex = list.findIndex((entry) => entry.id === sourceId);
  const targetIndex = list.findIndex((entry) => entry.id === targetId);
  if (sourceIndex === -1 || targetIndex === -1) return;

  const [moved] = list.splice(sourceIndex, 1);
  list.splice(targetIndex, 0, moved);
  galleryItems.value = list;
}

function statusLabel(item) {
  return {
    queued: 'Na fila para envio',
    uploading: `Enviando ${item.progress}%`,
    uploaded: 'Upload temporario concluido',
    processing: 'Processando em background',
    error: 'Falha no envio',
  }[item.status] || 'Pendente';
}

function currentImageCount() {
  return (featuredItem.value ? 1 : 0) + galleryItems.value.length;
}

function getSubmissionPayload() {
  const uploading = [featuredItem.value, ...galleryItems.value].filter(Boolean).some((item) => item.status === 'uploading');
  const hasErrors = [featuredItem.value, ...galleryItems.value].filter(Boolean).some((item) => item.status === 'error');

  return {
    featured_upload_token: featuredItem.value?.token || null,
    gallery_upload_tokens: galleryItems.value
      .filter((item) => !item.existingPhotoId && item.token)
      .map((item) => item.token),
    remove_photo_ids: removedPhotoIds.value,
    photo_order_ids: galleryItems.value
      .filter((item) => item.existingPhotoId && !removedPhotoIds.value.includes(item.existingPhotoId))
      .map((item) => item.existingPhotoId),
    hasPendingUploads: uploading,
    hasUploadErrors: hasErrors,
  };
}

defineExpose({
  getSubmissionPayload,
});
</script>
