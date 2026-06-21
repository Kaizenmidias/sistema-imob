<template>
  <Layout>
    <div class="container mx-auto px-4 py-4">
      <a href="/imoveis" class="text-blue-800 hover:text-blue-600 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Voltar
      </a>
    </div>

    <div class="container mx-auto px-4 mb-8">
      <PropertyGallery :images="photos" :initial-index="0" />
    </div>

    <div class="container mx-auto px-4 pb-12">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
          <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div>
              <div class="flex flex-wrap items-center gap-3 mb-2">
                <span v-if="property.code" class="bg-blue-900 text-white px-4 py-1 rounded-full text-sm font-semibold">{{ property.code }}</span>
                <span v-if="property.isExclusive" class="bg-orange-500 text-white px-4 py-1 rounded-full text-sm font-semibold">EXCLUSIVO</span>
                <span class="bg-gray-200 text-gray-700 px-4 py-1 rounded-full text-sm font-semibold">{{ photos.length }} Fotos</span>
              </div>
              <h1 class="text-2xl font-bold text-gray-800 mb-1">{{ property.title }}</h1>
              <p class="text-gray-600">{{ property.address }}</p>
              <p v-if="property.condominiumName" class="text-sm text-gray-500 mt-1">Condomínio: {{ property.condominiumName }}</p>
            </div>
            <div class="flex items-center gap-3">
              <button class="p-2 border border-gray-300 rounded-full hover:bg-gray-50">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
              </button>
              <button class="p-2 border border-gray-300 rounded-full hover:bg-gray-50 flex items-center gap-2 px-4">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                </svg>
                Compartilhar
              </button>
            </div>
          </div>

          <div v-if="highlightItems.length" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 mb-8">
            <div v-for="item in highlightItems" :key="item.label" class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
              <div class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">{{ item.label }}</div>
              <div class="mt-2 text-lg font-bold text-gray-900">{{ item.value }}</div>
            </div>
          </div>

          <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">DESCRIÇÃO DO IMÓVEL</h2>
            <div class="text-gray-700 leading-relaxed space-y-4" v-html="property.description"></div>
          </div>

          <div v-if="showDetailsSection" class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-xl font-bold text-gray-800">CARACTERÍSTICAS DO IMÓVEL</h2>

            <div v-if="commercialFlags.length" class="mt-6">
              <div class="text-sm font-semibold uppercase tracking-[0.18em] text-gray-500 mb-3">Comercial</div>
              <div class="flex flex-wrap gap-3">
                <span v-for="item in commercialFlags" :key="item" class="inline-flex items-center rounded-full bg-green-50 px-4 py-2 text-sm font-semibold text-green-700">
                  {{ item }}
                </span>
              </div>
            </div>

            <div v-if="areaItems.length" class="mt-6">
              <div class="text-sm font-semibold uppercase tracking-[0.18em] text-gray-500 mb-3">Áreas</div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div v-for="item in areaItems" :key="item.label" class="rounded-2xl bg-gray-50 px-4 py-4">
                  <div class="text-sm text-gray-500">{{ item.label }}</div>
                  <div class="mt-1 text-base font-semibold text-gray-900">{{ item.value }}</div>
                </div>
              </div>
            </div>

            <div v-if="financialItems.length" class="mt-6">
              <div class="text-sm font-semibold uppercase tracking-[0.18em] text-gray-500 mb-3">Financeiro</div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div v-for="item in financialItems" :key="item.label" class="rounded-2xl bg-gray-50 px-4 py-4">
                  <div class="text-sm text-gray-500">{{ item.label }}</div>
                  <div class="mt-1 text-base font-semibold text-gray-900">{{ item.value }}</div>
                </div>
              </div>
            </div>

            <div v-if="extraItems.length" class="mt-6">
              <div class="text-sm font-semibold uppercase tracking-[0.18em] text-gray-500 mb-3">Extras</div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div v-for="item in extraItems" :key="item.label" class="rounded-2xl bg-gray-50 px-4 py-4">
                  <div class="text-sm text-gray-500">{{ item.label }}</div>
                  <div class="mt-1 text-base font-semibold text-gray-900">{{ item.value }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="lg:col-span-1">
          <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-28 border border-gray-200">
            <div class="flex flex-wrap items-center gap-3 mb-4">
              <span
                v-for="label in businessBadges"
                :key="label"
                :class="label === 'ALUGUEL' ? 'bg-orange-500' : (label === 'VENDA' ? 'bg-blue-900' : 'bg-gray-700')"
                class="text-white px-4 py-1 rounded-full text-sm font-bold"
              >
                {{ label }}
              </span>
              <span class="bg-gray-100 text-gray-700 px-4 py-1 rounded-full text-sm font-semibold">{{ property.propertyType }}</span>
            </div>

            <div class="space-y-3 mb-6">
              <div v-for="row in priceRows" :key="row.key" class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-4">
                <div :class="row.key === 'rent' ? 'text-orange-700' : 'text-blue-900'" class="text-sm font-semibold uppercase tracking-[0.18em]">
                  {{ row.label }}
                </div>
                <div :class="row.key === 'rent' ? 'text-orange-700' : 'text-blue-900'" class="mt-2 text-4xl font-bold">
                  {{ formatCurrencyBRL(row.value) }}<span v-if="row.suffix" class="text-2xl">{{ row.suffix }}</span>
                </div>
              </div>
              <div v-if="priceRows.length === 0" class="rounded-2xl border border-dashed border-gray-200 px-4 py-4 text-gray-400">
                Consulte valores
              </div>
            </div>

            <div v-if="financialItems.length" class="space-y-3 mb-8">
              <div v-for="item in financialItems" :key="item.label" class="flex items-center justify-between gap-3">
                <span class="text-gray-500">{{ item.label }}</span>
                <span class="text-gray-800 font-medium">{{ item.value }}</span>
              </div>
            </div>

            <div class="border-t border-gray-100 pt-6 mb-6">
              <form @submit.prevent="submitContact" class="space-y-4">
                <div>
                  <input
                    v-model="contactForm.nome"
                    type="text"
                    placeholder="Seu nome *"
                    class="w-full px-4 py-3 bg-gray-100 rounded-full border-0 focus:ring-2 focus:ring-blue-500"
                    required
                  />
                </div>
                <div class="flex items-center gap-3">
                  <div class="px-4 py-3 bg-gray-100 rounded-full text-gray-600">BR</div>
                  <input
                    v-model="contactForm.telefone"
                    type="tel"
                    placeholder="(00) 00000-0000"
                    class="flex-1 px-4 py-3 bg-gray-100 rounded-full border-0 focus:ring-2 focus:ring-blue-500"
                    required
                  />
                </div>
                <div>
                  <input
                    v-model="contactForm.email"
                    type="email"
                    placeholder="Seu e-mail"
                    class="w-full px-4 py-3 bg-gray-100 rounded-full border-0 focus:ring-2 focus:ring-blue-500"
                  />
                </div>
                <div>
                  <textarea
                    v-model="contactForm.mensagem"
                    placeholder="Olá, estou interessado nesse imóvel que encontrei no site."
                    rows="4"
                    class="w-full px-4 py-3 bg-gray-100 rounded-3xl border-0 focus:ring-2 focus:ring-blue-500 resize-none"
                  ></textarea>
                </div>
                <button
                  type="submit"
                  class="w-full bg-blue-900 hover:bg-blue-800 text-white font-bold py-4 px-6 rounded-full transition disabled:opacity-60 disabled:cursor-not-allowed"
                  :disabled="contactForm.processing"
                >
                  Enviar mensagem
                </button>
              </form>
            </div>

            <div class="bg-gradient-to-br from-orange-100 to-orange-50 rounded-2xl p-6 border border-orange-200">
              <div class="flex items-start gap-4">
                <div class="p-3 bg-orange-700/20 rounded-full">
                  <svg class="w-7 h-7 text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-bold text-gray-800">Agende sua visita</h3>
                  <p class="text-gray-600 text-sm">Conheça o imóvel pessoalmente</p>
                </div>
              </div>
              <button class="w-full mt-4 bg-orange-500 hover:bg-orange-600 text-white font-bold py-4 px-6 rounded-full transition flex items-center justify-center gap-2">
                <span>Agendar visita</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import PropertyGallery from '@/Components/PropertyGallery.vue';
import Layout from '@/Shared/Layout.vue';

const props = defineProps({
  property: {
    type: Object,
    required: true,
  },
});

const placeholderImage = `data:image/svg+xml,${encodeURIComponent(
  `<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="800" viewBox="0 0 1200 800">
    <defs>
      <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
        <stop offset="0" stop-color="#0f172a"/>
        <stop offset="1" stop-color="#1e3a8a"/>
      </linearGradient>
    </defs>
    <rect width="1200" height="800" fill="url(#g)"/>
    <rect x="130" y="140" width="940" height="520" rx="28" fill="rgba(255,255,255,0.10)"/>
    <path d="M320 590l260-230 120 115 160-150 320 330H320z" fill="rgba(255,255,255,0.26)"/>
    <circle cx="420" cy="340" r="58" fill="rgba(255,255,255,0.22)"/>
    <text x="600" y="740" text-anchor="middle" font-family="Arial, sans-serif" font-size="28" fill="rgba(255,255,255,0.72)">Imagem indisponível</text>
  </svg>`
)}`;

const photos = computed(() => {
  const list = props.property?.photos ?? [];
  if (Array.isArray(list) && list.length > 0) return list;
  return [{ full: placeholderImage, medium: placeholderImage, thumb: placeholderImage }];
});

const businessBadges = computed(() => {
  const labels = Array.isArray(props.property?.businessLabels) ? props.property.businessLabels : [];

  if (labels.length > 0) {
    return labels.map((label) => {
      if (label === 'Comprar') return 'VENDA';
      if (label === 'Alugar' || label === 'Aluguel') return 'ALUGUEL';
      return String(label || '').toUpperCase();
    });
  }

  if (props.property?.type) {
    return [String(props.property.type).toUpperCase()];
  }

  return [];
});

const priceRows = computed(() => {
  const rows = Array.isArray(props.property?.prices) ? props.property.prices.filter((row) => Number(row?.value || 0) > 0) : [];

  if (rows.length > 0) {
    return rows;
  }

  if (Number(props.property?.price || 0) > 0) {
    return [{
      key: 'default',
      label: String(props.property?.type || 'Valor'),
      value: props.property.price,
      suffix: String(props.property?.type || '').toLowerCase().includes('alugu') ? '/mês' : '',
    }];
  }

  return [];
});

const highlightItems = computed(() => {
  const items = [
    countItem('Quartos', props.property?.bedrooms, 'Quartos'),
    countItem('Suítes', props.property?.suites, 'Suítes'),
    countItem('Banheiros', props.property?.bathrooms, 'Banheiros'),
    countItem('Lavabos', props.property?.lavabos, 'Lavabos'),
    countItem('Vagas', props.property?.garages, 'Vagas'),
    floorItem(props.property?.floor),
  ];

  return items.filter(Boolean);
});

const areaItems = computed(() => {
  const items = [
    measureItem('Área Total', props.property?.totalArea),
    measureItem('Área Construída', props.property?.builtArea),
  ];

  return items.filter(Boolean);
});

const financialItems = computed(() => {
  const items = [
    currencyItem('Condomínio', props.property?.condominiumFee),
    currencyItem('IPTU', props.property?.iptuFee),
  ];

  return items.filter(Boolean);
});

const commercialFlags = computed(() => {
  const items = [];

  if (props.property?.acceptsExchange) items.push('Aceita Permuta');
  if (props.property?.acceptsFinancing) items.push('Aceita Financiamento');
  if (props.property?.furnished) items.push('Mobiliado');

  return items;
});

const extraItems = computed(() => {
  const items = [
    textItem('Posição Solar', props.property?.solarPosition),
    numericItem('Ano de Construção', props.property?.constructionYear),
  ];

  return items.filter(Boolean);
});

const showDetailsSection = computed(() =>
  commercialFlags.value.length > 0 ||
  areaItems.value.length > 0 ||
  financialItems.value.length > 0 ||
  extraItems.value.length > 0
);

const contactForm = useForm({
  property_id: props.property?.id ?? null,
  nome: '',
  telefone: '',
  email: '',
  mensagem: 'Olá, estou interessado nesse imóvel que encontrei no site.',
  origem: 'Site - Interesse no Imóvel',
});

function countItem(label, value, suffix) {
  const number = Number(value || 0);
  if (number <= 0) return null;
  return { label, value: `${number} ${suffix}` };
}

function floorItem(value) {
  const number = Number(value || 0);
  if (number <= 0) return null;
  return { label: 'Andar', value: `${number}º Andar` };
}

function measureItem(label, value) {
  const number = Number(value || 0);
  if (number <= 0) return null;
  return { label, value: `${formatMeasure(number)} m²` };
}

function currencyItem(label, value) {
  const number = Number(value || 0);
  if (number <= 0) return null;
  return { label, value: formatCurrencyBRL(number) };
}

function textItem(label, value) {
  const text = String(value || '').trim();
  if (!text) return null;
  return { label, value: text };
}

function numericItem(label, value) {
  const number = Number(value || 0);
  if (number <= 0) return null;
  return { label, value: String(number) };
}

function formatMeasure(value) {
  return Number(value || 0).toLocaleString('pt-BR', {
    minimumFractionDigits: Number(value || 0) % 1 === 0 ? 0 : 2,
    maximumFractionDigits: 2,
  });
}

function formatCurrencyBRL(value) {
  return Number(value || 0).toLocaleString('pt-BR', {
    style: 'currency',
    currency: 'BRL',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
}

function submitContact() {
  contactForm.post('/contato/send', {
    preserveScroll: true,
    onSuccess: () => {
      alert('Mensagem enviada com sucesso! Entraremos em contato em breve.');
      contactForm.reset('nome', 'telefone', 'email');
      contactForm.mensagem = 'Olá, estou interessado nesse imóvel que encontrei no site.';
      contactForm.clearErrors();
    },
  });
}
</script>
