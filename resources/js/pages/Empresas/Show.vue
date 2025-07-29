<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { useToast } from '@/composables/useToast';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

// Props from Laravel controller
interface Endereco {
    id: number;
    cep: string;
    logradouro: string;
    numero: string;
    complemento?: string;
    bairro: string;
    cidade: string;
    estado: string;
}

interface Anexo {
    id: number;
    nome_arquivo: string;
    caminho_arquivo: string;
    tipo_mime: string;
    tamanho: number;
    tipo_anexo: 'contrato' | 'documento' | 'imagem' | 'outro';
    created_at: string;
}

interface Empresa {
    id: number;
    cnpj: string;
    razao_social: string;
    nome_fantasia: string;
    email: string;
    telefone: string;
    ramo_atividade: string;
    endereco: Endereco;
    anexos: Anexo[];
    created_at: string;
    updated_at: string;
}

interface Props {
    empresa: Empresa;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Hierarquia',
        href: '/hierarquia',
    },
    {
        title: 'Empresas',
        href: '/empresas',
    },
    {
        title: props.empresa.nome_fantasia || props.empresa.razao_social,
        href: `/empresas/${props.empresa.id}`,
    },
];

const showDeleteDialog = ref(false);
const isDeleting = ref(false);
const { success: showSuccessToast, error: showErrorToast } = useToast();

// Format file size
const formatFileSize = (bytes: number): string => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

// Get file icon based on type
const getFileIcon = (tipo: string, mimeType: string): string => {
    if (tipo === 'imagem' || mimeType.startsWith('image/')) return '🖼️';
    if (tipo === 'contrato') return '📄';
    if (mimeType === 'application/pdf') return '📕';
    if (mimeType.includes('word')) return '📝';
    if (mimeType.includes('excel') || mimeType.includes('spreadsheet')) return '📊';
    return '📎';
};

// Format date
const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleString('pt-BR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// Delete empresa
const confirmDelete = () => {
    showDeleteDialog.value = true;
};

const deleteEmpresa = () => {
    if (isDeleting.value) return;

    isDeleting.value = true;
    router.delete(`/empresas/${props.empresa.id}`, {
        onSuccess: () => {
            showSuccessToast('Empresa excluída com sucesso!');
            // Redirect will be handled by controller
        },
        onError: () => {
            showErrorToast('Erro ao Excluir', 'Ocorreu um erro ao excluir a empresa. Tente novamente.');
            isDeleting.value = false;
            showDeleteDialog.value = false;
        },
        onFinish: () => {
            isDeleting.value = false;
        }
    });
};

const cancelDelete = () => {
    showDeleteDialog.value = false;
};

// Download file
const downloadFile = (anexo: Anexo) => {
    window.location.href = `/anexos/${anexo.id}/download`;
};
</script>

<template>

    <Head :title="empresa.nome_fantasia || empresa.razao_social" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ empresa.nome_fantasia || empresa.razao_social }}
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ empresa.razao_social }}
                    </p>
                </div>

                <div class="flex space-x-3">
                    <Link :href="`/empresas/${empresa.id}/edit`"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Editar
                    </Link>

                    <button @click="confirmDelete"
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Excluir
                    </button>
                </div>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Dados da Empresa -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Informações Gerais -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Informações Gerais
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                                    CNPJ
                                </label>
                                <p class="text-gray-900 dark:text-gray-100 font-mono">
                                    {{ empresa.cnpj }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                                    Ramo de Atividade
                                </label>
                                <p class="text-gray-900 dark:text-gray-100">
                                    {{ empresa.ramo_atividade }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                                    Email
                                </label>
                                <p class="text-gray-900 dark:text-gray-100">
                                    <a :href="`mailto:${empresa.email}`"
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        {{ empresa.email }}
                                    </a>
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                                    Telefone
                                </label>
                                <p class="text-gray-900 dark:text-gray-100">
                                    <a :href="`tel:${empresa.telefone}`"
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        {{ empresa.telefone }}
                                    </a>
                                </p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">
                                    Cadastrado em
                                </label>
                                <p class="text-gray-900 dark:text-gray-100">
                                    {{ formatDate(empresa.created_at) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Endereço -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Endereço
                        </h2>

                        <div class="space-y-4">
                            <div class="flex items-start space-x-2">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <div>
                                    <p class="text-gray-900 dark:text-gray-100">
                                        {{ empresa.endereco.logradouro }}, {{ empresa.endereco.numero }}
                                        <span v-if="empresa.endereco.complemento"> - {{ empresa.endereco.complemento
                                        }}</span>
                                    </p>
                                    <p class="text-gray-600 dark:text-gray-400">
                                        {{ empresa.endereco.bairro }}, {{ empresa.endereco.cidade }} - {{
                                            empresa.endereco.estado }}
                                    </p>
                                    <p class="text-gray-600 dark:text-gray-400">
                                        CEP: {{ empresa.endereco.cep }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Stats -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Resumo
                        </h3>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Total de Anexos</span>
                                <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ empresa.anexos.length }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Última atualização</span>
                                <span class="text-sm text-gray-900 dark:text-gray-100">
                                    {{ formatDate(empresa.updated_at) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Ações Rápidas
                        </h3>

                        <div class="space-y-3">
                            <Link href="/anexos/create" :data="{ empresa_id: empresa.id }"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Adicionar Anexo
                            </Link>

                            <Link :href="`/empresas/${empresa.id}/edit`"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Editar Empresa
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Anexos Section -->
            <div v-if="empresa.anexos.length > 0"
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Anexos ({{ empresa.anexos.length }})
                    </h2>

                    <Link href="/anexos/create" :data="{ empresa_id: empresa.id }"
                        class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Adicionar
                    </Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="anexo in empresa.anexos" :key="anexo.id"
                        class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-start space-x-3">
                            <span class="text-2xl">{{ getFileIcon(anexo.tipo_anexo, anexo.tipo_mime) }}</span>

                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                    {{ anexo.nome_arquivo }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 capitalize">
                                    {{ anexo.tipo_anexo }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ formatFileSize(anexo.tamanho) }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ formatDate(anexo.created_at) }}
                                </p>

                                <div class="flex space-x-2 mt-2">
                                    <button @click="downloadFile(anexo)"
                                        class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        Download
                                    </button>

                                    <Link :href="`/anexos/${anexo.id}`"
                                        class="text-xs text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300">
                                    Ver detalhes
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State for Anexos -->
            <div v-else
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
                <div class="text-gray-400 text-6xl mb-4">📎</div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Nenhum anexo</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Esta empresa ainda não possui anexos cadastrados.
                </p>
                <Link href="/anexos/create" :data="{ empresa_id: empresa.id }"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Adicionar Primeiro Anexo
                </Link>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteDialog" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 transition-opacity" @click="cancelDelete">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <!-- Modal content -->
                <div
                    class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>

                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                                Confirmar Exclusão
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Tem certeza que deseja excluir a empresa
                                    <strong>{{ empresa.nome_fantasia || empresa.razao_social }}</strong>?
                                    Esta ação não pode ser desfeita e todos os anexos serão removidos também.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button @click="deleteEmpresa" :disabled="isDeleting"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            <span v-if="isDeleting">Excluindo...</span>
                            <span v-else>Excluir</span>
                        </button>

                        <button @click="cancelDelete" :disabled="isDeleting"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
