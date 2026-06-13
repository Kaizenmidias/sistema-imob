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
        <p class="text-xs text-gray-500 mt-2">Upload assíncrono com Uppy. Formatos: JPG, PNG, WEBP, HEIC. Maximo {{ maxSizeLabel }} por arquivo.</p>
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
            <button v-if="featuredItem.status === 'uploading'" type="button" class="text-xs font-semibold text-amber-700 hover:text-amber-900" @click.stop="cancelItem(featuredItem)">
              Cancelar
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
          Sem limite de quantidade
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
        <p class="text-xs text-gray-500 mt-2">Uploads paralelos com Uppy. Cada arquivo sobe separadamente, vai para area temporaria segura e depois segue para a fila de processamento.</p>
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
            <button v-if="item.status === 'uploading'" type="button" class="bg-white/95 hover:bg-white text-amber-700 px-2 py-1 rounded text-[11px] font-semibold" @click.stop="cancelItem(item)">
              Cancelar
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
import Uppy from '@uppy/core';
import XHRUpload from '@uppy/xhr-upload';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';

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
    type: [Number, null],
    default: null,
  },
  maxFileSizeBytes: {
    type: Number,
    default: 10 * 1024 * 1024,
  },
  parallelUploads: {
    type: Number,
    default: 6,
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
const featuredItem = ref(null);
const galleryItems = ref([]);
let featuredUppy = null;
let galleryUppy = null;

const maxSizeLabel = computed(() => `${Math.round(props.maxFileSizeBytes / 1024 / 1024)}MB`);
const uploadSummary = computed(() => `${currentImageCount()} imagens na fila`);

function normalizeExistingItem(photo) {
  const processing = photo?.processing_status && !['completed', 'uploaded'].includes(photo.processing_status);

  return {
    id: `existing-${photo.id}`,
    uppyFileId: null,
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
    id: file.id,
    uppyFileId: file.id,
    existingPhotoId: null,
    token: null,
    file: file.data || null,
    previewUrl: file.preview || (file.data instanceof File ? URL.createObjectURL(file.data) : placeholderImage),
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
  const file = event.target.files?.[0];
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
  addFilesToUppy(featuredUppy, [file]);
}

async function appendGallery(files) {
  uploadError.value = '';
  const validFiles = files.filter(Boolean);
  if (!validateBeforeAdd(validFiles)) return;

  addFilesToUppy(galleryUppy, validFiles);
}

function validateBeforeAdd(files) {
  for (const file of files) {
    if (file.size > props.maxFileSizeBytes) {
      uploadError.value = `A imagem ${file.name} excede o limite de ${maxSizeLabel.value}.`;
      return false;
    }
  }

  return true;
}

async function removeFeatured() {
  if (!featuredItem.value) return;
  if (featuredItem.value.uppyFileId && featuredUppy?.getFile(featuredItem.value.uppyFileId)) {
    featuredUppy.removeFile(featuredItem.value.uppyFileId);
  }
  await removeItemToken(featuredItem.value);

  if (featuredItem.value.existingPhotoId) {
    removedPhotoIds.value = Array.from(new Set([...removedPhotoIds.value, featuredItem.value.existingPhotoId]));
  }

  featuredItem.value = null;
}

async function removeGallery(id) {
  const item = galleryItems.value.find((entry) => entry.id === id);
  if (!item) return;

  if (item.uppyFileId && galleryUppy?.getFile(item.uppyFileId)) {
    galleryUppy.removeFile(item.uppyFileId);
  }
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
  if (item?.uppyFileId && galleryUppy?.getFile(item.uppyFileId)) {
    try {
      await galleryUppy.retryUpload(item.uppyFileId);
      return;
    } catch {
    }
  }

  if (item?.uppyFileId && featuredUppy?.getFile(item.uppyFileId)) {
    try {
      await featuredUppy.retryUpload(item.uppyFileId);
      return;
    } catch {
    }
  }

  if (!(item?.file instanceof File)) return;
  if (item === featuredItem.value) {
    addFilesToUppy(featuredUppy, [item.file]);
    return;
  }

  addFilesToUppy(galleryUppy, [item.file]);
}

function cancelItem(item) {
  if (!item?.uppyFileId) return;

  if (featuredUppy?.getFile(item.uppyFileId)) {
    featuredUppy.removeFile(item.uppyFileId);
    featuredItem.value = null;
    return;
  }

  if (galleryUppy?.getFile(item.uppyFileId)) {
    galleryUppy.removeFile(item.uppyFileId);
    galleryItems.value = galleryItems.value.filter((entry) => entry.id !== item.id);
  }
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
    completed: 'Processamento concluido',
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

function createUppy(kind) {
  const uppy = new Uppy({
    autoProceed: true,
    allowMultipleUploadBatches: true,
    restrictions: {
      allowedFileTypes: ['.jpg', '.jpeg', '.png', '.webp', '.heic', '.heif'],
      maxFileSize: props.maxFileSizeBytes,
      maxNumberOfFiles: kind === 'featured' ? 1 : null,
    },
  });

  uppy.use(XHRUpload, {
    endpoint: props.uploadUrl,
    method: 'post',
    fieldName: 'file',
    formData: true,
    limit: props.parallelUploads,
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
    },
  });

  uppy.on('file-added', (file) => {
    const item = createUploadItem(file);
    if (kind === 'featured') {
      featuredItem.value = item;
      return;
    }

    galleryItems.value.push(item);
  });

  uppy.on('upload-progress', (file, progress) => {
    const item = findItemByUppyId(file.id);
    if (!item) return;
    item.status = 'uploading';
    item.progress = Math.round(((progress.bytesUploaded || 0) / Math.max(progress.bytesTotal || 1, 1)) * 100);
  });

  uppy.on('upload-success', (file, response) => {
    const item = findItemByUppyId(file.id);
    if (!item) return;
    item.token = response?.body?.token || null;
    item.status = 'uploaded';
    item.progress = 100;
    item.error = '';
  });

  uppy.on('upload-error', (file, error, response) => {
    const item = findItemByUppyId(file.id);
    if (!item) return;
    item.status = 'error';
    item.error = response?.body?.message || error?.message || 'Falha ao enviar a imagem.';
  });

  uppy.on('restriction-failed', (file, error) => {
    uploadError.value = error?.message || `Falha ao validar ${file?.name || 'a imagem'}.`;
  });

  return uppy;
}

function addFilesToUppy(uppy, files) {
  if (!uppy) return;

  files.forEach((file) => {
    try {
      uppy.addFile({
        name: file.name,
        type: file.type,
        data: file,
      });
    } catch (error) {
      uploadError.value = error?.message || 'Nao foi possivel adicionar a imagem para upload.';
    }
  });
}

function findItemByUppyId(fileId) {
  if (featuredItem.value?.uppyFileId === fileId) {
    return featuredItem.value;
  }

  return galleryItems.value.find((entry) => entry.uppyFileId === fileId) || null;
}

function syncExistingPhotos(photos) {
  const existingFeatured = photos.find((photo) => photo?.principal);
  const existingGallery = photos
    .filter((photo) => !photo?.principal)
    .sort((a, b) => Number(a?.ordem ?? 0) - Number(b?.ordem ?? 0));

  if (!featuredItem.value || featuredItem.value.isExisting) {
    featuredItem.value = existingFeatured ? normalizeExistingItem(existingFeatured) : featuredItem.value?.isExisting ? null : featuredItem.value;
  }

  const stagedGallery = galleryItems.value.filter((entry) => !entry.isExisting);
  galleryItems.value = [...existingGallery.map(normalizeExistingItem), ...stagedGallery];
}

onMounted(() => {
  featuredUppy = createUppy('featured');
  galleryUppy = createUppy('gallery');
  syncExistingPhotos(props.existingPhotos || []);
});

onBeforeUnmount(() => {
  featuredUppy?.destroy();
  galleryUppy?.destroy();
});

watch(
  () => props.existingPhotos,
  (photos) => {
    syncExistingPhotos(Array.isArray(photos) ? photos : []);
  },
  { deep: true }
);
</script>
