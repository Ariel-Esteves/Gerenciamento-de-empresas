<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

// Props from Laravel controller
interface Empresa {
    id: number;
    cnpj: string;
    razao_social: string;
    nome_fantasia?: string;
    email: string;
    telefone: string;
    ramo_atividade: string;
}

interface Anexo {
    id: number;
    tipo: string;
    descricao?: string;
    nome_arquivo: string;
    caminho_arquivo: string;
    tamanho_arquivo: number;
    created_at: string;
    updated_at: string;
    empresa: Empresa;
}

interface Props {
    anexo: Anexo;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Anexos',
        href: '/anexos',
    },
    {
        title: props.anexo.nome_arquivo,
        href: `/anexos/${props.anexo.id}`,
    },
];

// Loading states
const isDeleting = ref(false);
const showDeleteConfirm = ref(false);
const isDownloading = ref(false);

// File type options for display
const tipoOptions = [
    { value: 'contrato', label: 'Contrato' },
    { value: 'documento', label: 'Documento' },
    { value: 'certidao', label: 'Certidão' },
    { value: 'licenca', label: 'Licença' },
    { value: 'outro', label: 'Outro' },
];

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
        case 'certidao':
            return 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-300';
        case 'licenca':
            return 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-300';
        case 'outro':
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-300';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-300';
    }
};

const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleString('pt-BR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getFileExtension = (fileName: string): string => {
    return fileName.split('.').pop()?.toUpperCase() || 'ARQUIVO';
};

const isImageFile = (fileName: string): boolean => {
    const extension = fileName.split('.').pop()?.toLowerCase();
    return ['jpg', 'jpeg', 'png', 'gif', 'svg'].includes(extension || '');
};

const isPdfFile = (fileName: string): boolean => {
    const extension = fileName.split('.').pop()?.toLowerCase();
    return extension === 'pdf';
};

// Actions
const downloadFile = () => {
    if (isDownloading.value) return;

    isDownloading.value = true;

    // Create a temporary link to trigger download
    const link = document.createElement('a');
    link.href = `/anexos/${props.anexo.id}/download`;
    link.target = '_blank';
    link.click();

    // Reset loading state after a short delay
    setTimeout(() => {
        isDownloading.value = false;
    }, 1000);
};

const openFile = () => {
    window.open(`/anexos/${props.anexo.id}/view`, '_blank');
};

const confirmDelete = () => {
    showDeleteConfirm.value = true;
};

const cancelDelete = () => {
    showDeleteConfirm.value = false;
};

const deleteAnexo = () => {
    if (isDeleting.value) return;

    isDeleting.value = true;

    router.delete(`/anexos/${props.anexo.id}`, {
        onSuccess: () => {
            // Redirect to anexos index after successful deletion
            router.visit('/anexos');
        },
        onError: () => {
            alert('Erro ao excluir anexo. Tente novamente.');
            showDeleteConfirm.value = false;
            isDeleting.value = false;
        }
    });
};
</script>

<template>

    <Head :title="`Anexo: ${anexo.nome_arquivo}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="text-4xl">
                        {{ getFileIcon(anexo.nome_arquivo) }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ anexo.nome_arquivo }}
                        </h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ getTipoLabel(anexo.tipo) }} • {{ formatFileSize(anexo.tamanho_arquivo) }}
                        </p>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="flex space-x-3">
                    <button @click="downloadFile" :disabled="isDownloading"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md hover:bg-blue-100 dark:hover:bg-blue-900/40 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                        <svg v-if="!isDownloading" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <svg v-else class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        {{ isDownloading ? 'Baixando...' : 'Download' }}
                    </button>

                    <button v-if="isImageFile(anexo.nome_arquivo) || isPdfFile(anexo.nome_arquivo)" @click="openFile"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Visualizar
                    </button>

                    <Link :href="`/anexos/${anexo.id}/edit`"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Editar
                    </Link>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- File Information -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="p-6">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                Informações do Arquivo
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- File Details -->
                                <div class="space-y-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nome do arquivo
                                        </dt>
                                        <dd
                                            class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-mono bg-gray-50 dark:bg-gray-700 px-3 py-2 rounded">
                                            {{ anexo.nome_arquivo }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tamanho</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ formatFileSize(anexo.tamanho_arquivo) }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Formato</dt>
                                        <dd class="mt-1">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                {{ getFileExtension(anexo.nome_arquivo) }}
                                            </span>
                                        </dd>
                                    </div>
                                </div>

                                <!-- Document Details -->
                                <div class="space-y-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tipo de
                                            documento</dt>
                                        <dd class="mt-1">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                :class="getTipoBadgeColor(anexo.tipo)">
                                                {{ getTipoLabel(anexo.tipo) }}
                                            </span>
                                        </dd>
                                    </div>

                                    <div v-if="anexo.descricao">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Descrição</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ anexo.descricao }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Data de upload
                                        </dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ formatDate(anexo.created_at) }}
                                        </dd>
                                    </div>

                                    <div v-if="anexo.updated_at !== anexo.created_at">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Última
                                            atualização</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ formatDate(anexo.updated_at) }}
                                        </dd>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- File Preview (if image or PDF) -->
                    <div v-if="isImageFile(anexo.nome_arquivo)"
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="p-6">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                Pré-visualização
                            </h2>
                            <div class="flex justify-center">
                                <img :src="`/anexos/${anexo.id}/view`" :alt="anexo.nome_arquivo"
                                    class="max-w-full h-auto rounded-lg shadow-sm border border-gray-200 dark:border-gray-600"
                                    style="max-height: 500px" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Company Information -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="p-6">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                Empresa Vinculada
                            </h2>

                            <div class="space-y-3">
                                <div>
                                    <Link :href="`/empresas/${anexo.empresa.id}`"
                                        class="text-lg font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">
                                    {{ anexo.empresa.nome_fantasia || anexo.empresa.razao_social }}
                                    </Link>
                                    <p v-if="anexo.empresa.nome_fantasia"
                                        class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ anexo.empresa.razao_social }}
                                    </p>
                                </div>

                                <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                    <p><strong>CNPJ:</strong> {{ anexo.empresa.cnpj }}</p>
                                    <p><strong>Email:</strong> {{ anexo.empresa.email }}</p>
                                    <p><strong>Telefone:</strong> {{ anexo.empresa.telefone }}</p>
                                    <p><strong>Ramo:</strong> {{ anexo.empresa.ramo_atividade }}</p>
                                </div>

                                <div class="pt-3 border-t border-gray-200 dark:border-gray-600">
                                    <Link :href="`/empresas/${anexo.empresa.id}`"
                                        class="inline-flex items-center text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">
                                    Ver detalhes da empresa
                                    <svg class="ml-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="p-6">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                Ações Rápidas
                            </h2>

                            <div class="space-y-3">
                                <Link href="/anexos"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Ver todos os anexos
                                </Link>

                                <Link href="/anexos/create"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Novo anexo
                                </Link>

                                <button @click="confirmDelete"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md hover:bg-red-100 dark:hover:bg-red-900/40 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Excluir anexo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
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
                                    Tem certeza de que deseja excluir "<strong>{{ anexo.nome_arquivo }}</strong>"? Esta
                                    ação não pode ser desfeita e o arquivo será permanentemente removido do sistema.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button @click="deleteAnexo" :disabled="isDeleting"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            <span v-if="isDeleting" class="flex items-center">
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
