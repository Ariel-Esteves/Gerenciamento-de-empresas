<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { useToast } from '@/composables/useToast';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

// Props from Laravel controller
interface Empresa {
    id: number;
    cnpj: string;
    razao_social: string;
    nome_fantasia?: string;
}

interface Anexo {
    id: number;
    tipo_anexo: string;
    descricao?: string;
    nome_arquivo: string;
    caminho_arquivo: string;
    tamanho_arquivo: number;
    created_at: string;
    updated_at: string;
    empresa: Empresa;
}

interface Props {
    anexos: Anexo[];
    filters?: {
        search?: string;
        tipo?: string;
        empresa_id?: number;
    };
    empresas?: Empresa[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Hierarquia',
        href: '/hierarquia',
    },
    {
        title: 'Anexos',
        href: '/anexos',
    },
];

// Reactive filters
const search = ref(props.filters?.search || '');
const selectedTipo = ref(props.filters?.tipo || '');
const selectedEmpresa = ref(props.filters?.empresa_id || '');

// File type options
const tipoOptions = [
    { value: '', label: 'Todos os tipos' },
    { value: 'contrato', label: 'Contrato' },
    { value: 'documento', label: 'Documento' },
    { value: 'imagem', label: 'Imagem' },
    { value: 'outro', label: 'Outro' },
];

// Loading states
const isDeleting = ref<number | null>(null);
const showDeleteConfirm = ref<number | null>(null);
const { success: showSuccessToast, error: showErrorToast } = useToast();

// Computed properties
const hasFilters = computed(() => {
    return search.value || selectedTipo.value || selectedEmpresa.value;
});

const filteredAnexosCount = computed(() => props.anexos.length);

// Utility functions
const formatFileSize = (bytes: number): string => {
    if (bytes === 0) return '0 Bytes';

    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const getFileIcon = (fileName: string): string => {
    const extension = fileName.split('.').pop()?.toLowerCase();

    switch (extension) {
        case 'pdf':
            return '📄';
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
        case 'svg':
            return '🖼️';
        case 'doc':
        case 'docx':
            return '📝';
        case 'xls':
        case 'xlsx':
            return '📊';
        case 'zip':
        case 'rar':
        case '7z':
            return '📦';
        default:
            return '📎';
    }
};

const getTipoLabel = (tipo: string): string => {
    const option = tipoOptions.find(t => t.value === tipo);
    return option ? option.label : tipo;
};

const getTipoBadgeColor = (tipo: string): string => {
    switch (tipo) {
        case 'contrato':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300';
        case 'documento':
            return 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300';
        case 'imagem':
            return 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-300';
        case 'outro':
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-300';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-300';
    }
};

const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleString('pt-BR', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// Actions
const applyFilters = () => {
    const params: any = {};

    if (search.value) params.search = search.value;
    if (selectedTipo.value) params.tipo = selectedTipo.value;
    if (selectedEmpresa.value) params.empresa_id = selectedEmpresa.value;

    router.get('/anexos', params, {
        preserveState: true,
        replace: true
    });
};

const clearFilters = () => {
    search.value = '';
    selectedTipo.value = '';
    selectedEmpresa.value = '';

    router.get('/anexos', {}, {
        preserveState: true,
        replace: true
    });
};

const downloadFile = (anexo: Anexo) => {
    window.open(`/anexos/${anexo.id}/download`, '_blank');
};

const confirmDelete = (id: number) => {
    showDeleteConfirm.value = id;
};

const cancelDelete = () => {
    showDeleteConfirm.value = null;
};

const deleteAnexo = (id: number) => {
    if (isDeleting.value) return;

    isDeleting.value = id;

    router.delete(`/anexos/${id}`, {
        onSuccess: () => {
            showDeleteConfirm.value = null;
            showSuccessToast('Anexo Excluído', 'O anexo foi excluído com sucesso!');
        },
        onError: () => {
            showErrorToast('Erro ao Excluir', 'Erro ao excluir anexo. Tente novamente.');
        },
        onFinish: () => {
            isDeleting.value = null;
        }
    });
};
</script>

<template>

    <Head title="Anexos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                        Anexos
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Gerencie todos os documentos e arquivos das empresas
                    </p>
                </div>

                <Link href="/anexos/create"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Novo Anexo
                </Link>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Buscar
                        </label>
                        <input id="search" type="text" v-model="search" placeholder="Nome do arquivo, descrição..."
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            @keyup.enter="applyFilters" />
                    </div>

                    <!-- Tipo -->
                    <div>
                        <label for="tipo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tipo
                        </label>
                        <select id="tipo" v-model="selectedTipo"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option v-for="option in tipoOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                    </div>

                    <!-- Empresa -->
                    <div>
                        <label for="empresa" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Empresa
                        </label>
                        <select id="empresa" v-model="selectedEmpresa"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Todas as empresas</option>
                            <option v-for="empresa in (empresas || [])" :key="empresa.id" :value="empresa.id">
                                {{ empresa.nome_fantasia || empresa.razao_social }}
                            </option>
                        </select>
                    </div>

                    <!-- Filter Actions -->
                    <div class="flex items-end space-x-2">
                        <button @click="applyFilters"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Filtrar
                        </button>
                        <button v-if="hasFilters" @click="clearFilters"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Limpar
                        </button>
                    </div>
                </div>

                <!-- Results Summary -->
                <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                    <span>{{ filteredAnexosCount }} {{ filteredAnexosCount === 1 ? 'anexo encontrado'
                        : 'anexos encontrados' }}</span>
                    <span v-if="hasFilters" class="text-blue-600 dark:text-blue-400">
                        Filtros aplicados
                    </span>
                </div>
            </div>

            <!-- Anexos Grid -->
            <div v-if="anexos.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="anexo in anexos" :key="anexo.id"
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                    <!-- File Header -->
                    <div class="p-4 border-b border-gray-200 dark:border-gray-600">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="text-2xl">
                                    {{ getFileIcon(anexo.nome_arquivo) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                        {{ anexo.nome_arquivo }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ formatFileSize(anexo.tamanho_arquivo) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Type Badge -->
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                :class="getTipoBadgeColor(anexo.tipo_anexo)">
                                {{ getTipoLabel(anexo.tipo_anexo) }}
                            </span>
                        </div>
                    </div>

                    <!-- File Content -->
                    <div class="p-4">
                        <!-- Description -->
                        <div v-if="anexo.descricao" class="mb-3">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ anexo.descricao }}
                            </p>
                        </div>

                        <!-- Company Info -->
                        <div class="mb-3">
                            <Link :href="`/empresas/${anexo.empresa.id}`"
                                class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">
                            {{ anexo.empresa.nome_fantasia || anexo.empresa.razao_social }}
                            </Link>
                        </div>

                        <!-- Date -->
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                            Enviado em {{ formatDate(anexo.created_at) }}
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between">
                            <div class="flex space-x-2">
                                <button @click="downloadFile(anexo)"
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/20 rounded-md hover:bg-blue-100 dark:hover:bg-blue-900/40">
                                    <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Download
                                </button>

                                <Link :href="`/anexos/${anexo.id}`"
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 rounded-md hover:bg-gray-100 dark:hover:bg-gray-600">
                                <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Ver
                                </Link>
                            </div>

                            <button @click="confirmDelete(anexo.id)"
                                class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 dark:hover:bg-red-900/20 rounded">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
                <div class="text-6xl mb-4">📁</div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                    Nenhum anexo encontrado
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    {{ hasFilters ? 'Tente ajustar os filtros de busca' : 'Comece adicionando o primeiro anexo' }}
                </p>
                <Link v-if="!hasFilters" href="/anexos/create"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Novo Anexo
                </Link>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteConfirm" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/20 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                Excluir Anexo
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Tem certeza de que deseja excluir este anexo? Esta ação não pode ser desfeita e o
                                    arquivo será permanentemente removido.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button @click="deleteAnexo(showDeleteConfirm)" :disabled="isDeleting === showDeleteConfirm"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            <span v-if="isDeleting === showDeleteConfirm" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Excluindo...
                            </span>
                            <span v-else>Excluir</span>
                        </button>
                        <button @click="cancelDelete"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
