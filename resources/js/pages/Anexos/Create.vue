<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

// Props from Laravel controller
interface Empresa {
    id: number;
    cnpj: string;
    razao_social: string;
    nome_fantasia?: string;
}

interface Props {
    empresas: Empresa[];
    empresa_id?: number; // If coming from a specific empresa
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
        title: 'Novo Anexo',
        href: '/anexos/create',
    },
];

// Form data
const form = useForm({
    empresa_id: props.empresa_id || '',
    tipo_anexo: '',
    descricao: '',
    arquivo: null as File | null,
});

const isSubmitting = ref(false);
const fileInput = ref<HTMLInputElement>();
const dragOver = ref(false);
const selectedFile = ref<File | null>(null);

// File type options
const tipoOptions = [
    { value: 'contrato', label: 'Contrato' },
    { value: 'documento', label: 'Documento' },
    { value: 'certidao', label: 'Certidão' },
    { value: 'licenca', label: 'Licença' },
    { value: 'outro', label: 'Outro' },
];

// Computed properties
const selectedEmpresa = computed(() => {
    if (!form.empresa_id) return null;
    return props.empresas.find(e => e.id === parseInt(form.empresa_id.toString()));
});

const filePreview = computed(() => {
    if (!selectedFile.value) return null;

    const file = selectedFile.value;
    return {
        name: file.name,
        size: formatFileSize(file.size),
        type: file.type,
        isImage: file.type.startsWith('image/'),
        isPdf: file.type === 'application/pdf',
    };
});

// File handling functions
const handleFileSelect = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        setSelectedFile(file);
    }
};

const setSelectedFile = (file: File) => {
    selectedFile.value = file;
    form.arquivo = file;

    // Auto-suggest description based on filename
    if (!form.descricao) {
        const nameWithoutExtension = file.name.replace(/\.[^/.]+$/, "");
        form.descricao = nameWithoutExtension.replace(/[_-]/g, ' ');
    }

    // Debug logging
    console.log('File selected:', {
        name: file.name,
        size: file.size,
        type: file.type
    });
};

const removeFile = () => {
    selectedFile.value = null;
    form.arquivo = null;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

// Drag and drop handlers
const handleDragOver = (event: DragEvent) => {
    event.preventDefault();
    dragOver.value = true;
};

const handleDragLeave = (event: DragEvent) => {
    event.preventDefault();
    dragOver.value = false;
};

const handleDrop = (event: DragEvent) => {
    event.preventDefault();
    dragOver.value = false;

    const files = event.dataTransfer?.files;
    if (files && files[0]) {
        setSelectedFile(files[0]);
    }
};

// Utility functions
const formatFileSize = (bytes: number): string => {
    if (bytes === 0) return '0 Bytes';

    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const getFileIcon = (type: string): string => {
    if (type.startsWith('image/')) return '🖼️';
    if (type === 'application/pdf') return '📄';
    if (type.includes('word')) return '📝';
    if (type.includes('excel') || type.includes('spreadsheet')) return '📊';
    if (type.includes('zip') || type.includes('rar')) return '📦';
    return '📎';
};

// Form submission
const submit = () => {
    if (isSubmitting.value) return;

    if (!selectedFile.value) {
        alert('Por favor, selecione um arquivo');
        return;
    }

    if (!form.empresa_id) {
        alert('Por favor, selecione uma empresa');
        return;
    }

    if (!form.tipo_anexo) {
        alert('Por favor, selecione o tipo de documento');
        return;
    }

    isSubmitting.value = true;

    // Debug logging
    console.log('Form data being submitted:', {
        empresa_id: form.empresa_id,
        tipo_anexo: form.tipo_anexo,
        descricao: form.descricao,
        arquivo: selectedFile.value?.name,
        arquivo_size: selectedFile.value?.size
    });

    form.post('/anexos', {
        forceFormData: true, // Ensure multipart/form-data for file upload
        onSuccess: (response) => {
            console.log('Upload successful:', response);
            alert('Anexo salvo com sucesso!');
            // Redirect handled by controller
        },
        onError: (errors) => {
            console.error('Upload failed:', errors);
            console.error('Form errors:', form.errors);

            // Show specific error messages
            if (form.errors.arquivo) {
                alert('Erro no arquivo: ' + form.errors.arquivo);
            } else if (form.errors.empresa_id) {
                alert('Erro na empresa: ' + form.errors.empresa_id);
            } else if (form.errors.tipo_anexo) {
                alert('Erro no tipo: ' + form.errors.tipo_anexo);
            } else {
                alert('Erro ao salvar anexo. Verifique os dados e tente novamente.');
            }

            isSubmitting.value = false;
        },
        onFinish: () => {
            isSubmitting.value = false;
        }
    });
};

// Reset form
const resetForm = () => {
    form.reset();
    removeFile();
};
</script>

<template>

    <Head title="Novo Anexo" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                        Novo Anexo
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Faça upload de um documento para associar a uma empresa
                    </p>
                </div>

                <!-- Quick Actions -->
                <div class="flex space-x-3">
                    <Link href="/anexos"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Ver Todos os Anexos
                    </Link>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <form @submit.prevent="submit" class="p-6 space-y-8">

                    <!-- Empresa Selection -->
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Empresa
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <label for="empresa_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Selecionar Empresa *
                                </label>
                                <select id="empresa_id" v-model="form.empresa_id"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    :class="{ 'border-red-500': form.errors.empresa_id }" required>
                                    <option value="">Selecione uma empresa...</option>
                                    <option v-for="empresa in empresas" :key="empresa.id" :value="empresa.id">
                                        {{ empresa.nome_fantasia || empresa.razao_social }} ({{ empresa.cnpj }})
                                    </option>
                                </select>
                                <div v-if="form.errors.empresa_id" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.empresa_id }}
                                </div>
                            </div>

                            <!-- Selected Empresa Info -->
                            <div v-if="selectedEmpresa"
                                class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-4 0L9 21m-4 0h4m0 0v-4a2 2 0 011-1h2a2 2 0 011 1v4" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-blue-900 dark:text-blue-100">
                                            {{ selectedEmpresa.nome_fantasia || selectedEmpresa.razao_social }}
                                        </p>
                                        <p class="text-xs text-blue-700 dark:text-blue-300">
                                            CNPJ: {{ selectedEmpresa.cnpj }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- File Upload Section -->
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Arquivo
                        </h2>

                        <!-- File Drop Zone -->
                        <div class="relative border-2 border-dashed rounded-lg p-8 text-center transition-colors"
                            :class="{
                                'border-blue-400 bg-blue-50 dark:bg-blue-900/20': dragOver,
                                'border-gray-300 dark:border-gray-600': !dragOver && !selectedFile,
                                'border-green-400 bg-green-50 dark:bg-green-900/20': selectedFile
                            }" @dragover="handleDragOver" @dragleave="handleDragLeave" @drop="handleDrop"
                            @click="fileInput?.click()">
                            <input ref="fileInput" type="file" class="hidden" @change="handleFileSelect"
                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.zip,.rar" />

                            <div v-if="!selectedFile">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" stroke="currentColor" fill="none"
                                    viewBox="0 0 48 48">
                                    <path
                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="text-gray-600 dark:text-gray-400">
                                    <p class="text-lg font-medium mb-2">
                                        {{ dragOver ? 'Solte o arquivo aqui' :
                                            'Clique para selecionar ou arraste um arquivo' }}
                                    </p>
                                    <p class="text-sm">
                                        PDF, DOC, DOCX, JPG, PNG, GIF, ZIP, RAR (máx. 10MB)
                                    </p>
                                </div>
                            </div>

                            <!-- File Preview -->
                            <div v-else class="flex items-center justify-center space-x-4">
                                <div class="text-4xl">
                                    {{ getFileIcon(filePreview!.type) }}
                                </div>
                                <div class="text-left">
                                    <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        {{ filePreview!.name }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ filePreview!.size }}
                                    </p>
                                </div>
                                <button type="button" @click.stop="removeFile"
                                    class="p-2 text-red-600 hover:text-red-800 hover:bg-red-100 dark:hover:bg-red-900/20 rounded-full">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div v-if="form.errors.arquivo" class="mt-2 text-sm text-red-600">
                            {{ form.errors.arquivo }}
                        </div>
                    </div>

                    <!-- File Details -->
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Detalhes do Anexo
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Tipo -->
                            <div>
                                <label for="tipo_anexo"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Tipo de Documento *
                                </label>
                                <select id="tipo_anexo" v-model="form.tipo_anexo"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    :class="{ 'border-red-500': form.errors.tipo_anexo }" required>
                                    <option value="">Selecione o tipo...</option>
                                    <option v-for="option in tipoOptions" :key="option.value" :value="option.value">
                                        {{ option.label }}
                                    </option>
                                </select>
                                <div v-if="form.errors.tipo_anexo" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.tipo_anexo }}
                                </div>
                            </div>

                            <!-- Descrição -->
                            <div>
                                <label for="descricao"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Descrição
                                </label>
                                <input id="descricao" type="text" v-model="form.descricao"
                                    placeholder="Descrição do documento (opcional)" maxlength="255"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    :class="{ 'border-red-500': form.errors.descricao }" />
                                <div v-if="form.errors.descricao" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.descricao }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Instructions -->
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">
                            📋 Instruções para Upload
                        </h3>
                        <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                            <li>• Formatos aceitos: PDF, DOC, DOCX, JPG, PNG, GIF, ZIP, RAR</li>
                            <li>• Tamanho máximo: 10MB por arquivo</li>
                            <li>• Certifique-se de que o arquivo não está corrompido</li>
                            <li>• Use nomes descritivos para facilitar a organização</li>
                        </ul>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-600">
                        <button type="button" @click="resetForm"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            :disabled="isSubmitting">
                            Limpar Formulário
                        </button>

                        <div class="flex space-x-3">
                            <Link href="/anexos"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancelar
                            </Link>

                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="isSubmitting || !selectedFile">
                                <span v-if="isSubmitting" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Fazendo Upload...
                                </span>
                                <span v-else>Salvar Anexo</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
