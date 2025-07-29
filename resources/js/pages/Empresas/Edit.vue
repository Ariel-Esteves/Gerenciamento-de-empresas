<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { useToast } from '@/composables/useToast';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
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

interface Empresa {
    id: number;
    cnpj: string;
    razao_social: string;
    nome_fantasia: string;
    email: string;
    telefone: string;
    ramo_atividade: string;
    endereco: Endereco;
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
    {
        title: 'Editar',
        href: `/empresas/${props.empresa.id}/edit`,
    },
];

// Form data pre-populated with existing data
const form = useForm({
    cnpj: props.empresa.cnpj,
    razao_social: props.empresa.razao_social,
    nome_fantasia: props.empresa.nome_fantasia || '',
    email: props.empresa.email,
    telefone: props.empresa.telefone,
    ramo_atividade: props.empresa.ramo_atividade,
    endereco: {
        cep: props.empresa.endereco.cep,
        logradouro: props.empresa.endereco.logradouro,
        numero: props.empresa.endereco.numero,
        complemento: props.empresa.endereco.complemento || '',
        bairro: props.empresa.endereco.bairro,
        cidade: props.empresa.endereco.cidade,
        estado: props.empresa.endereco.estado,
    }
});

const isSubmitting = ref(false);
const { success: showSuccessToast, error: showErrorToast } = useToast();

const submit = () => {
    if (isSubmitting.value) return;

    isSubmitting.value = true;
    form.put(`/empresas/${props.empresa.id}`, {
        onSuccess: () => {
            showSuccessToast('Empresa Atualizada');
            // Redirect handled by controller
        },
        onError: () => {
            showErrorToast('Erro ao Atualizar', 'Ocorreu um erro ao atualizar a empresa. Verifique os dados e tente novamente.');
            isSubmitting.value = false;
        },
        onFinish: () => {
            isSubmitting.value = false;
        }
    });
};

// Format CNPJ as user types
const formatCNPJ = (value: string) => {
    const cleaned = value.replace(/\D/g, '');
    const match = cleaned.match(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/);
    if (match) {
        return `${match[1]}.${match[2]}.${match[3]}/${match[4]}-${match[5]}`;
    }
    return cleaned;
};

// Format CEP as user types
const formatCEP = (value: string) => {
    const cleaned = value.replace(/\D/g, '');
    const match = cleaned.match(/^(\d{5})(\d{3})$/);
    if (match) {
        return `${match[1]}-${match[2]}`;
    }
    return cleaned;
};

// Format phone as user types
const formatPhone = (value: string) => {
    const cleaned = value.replace(/\D/g, '');
    const match = cleaned.match(/^(\d{2})(\d{4,5})(\d{4})$/);
    if (match) {
        return `(${match[1]}) ${match[2]}-${match[3]}`;
    }
    return cleaned;
};

// Auto-format fields on input
const handleCNPJInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const formatted = formatCNPJ(target.value);
    form.cnpj = formatted;
};

const handleCEPInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const formatted = formatCEP(target.value);
    form.endereco.cep = formatted;
};

const handlePhoneInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const formatted = formatPhone(target.value);
    form.telefone = formatted;
};

// Reset form to original values
const resetForm = () => {
    form.reset();
};

// Format date for display
const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleString('pt-BR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>

    <Head :title="`Editar ${empresa.nome_fantasia || empresa.razao_social}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                        Editar Empresa
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Atualize os dados da empresa {{ empresa.nome_fantasia || empresa.razao_social }}
                    </p>
                </div>

                <!-- Quick Info -->
                <div class="text-right text-sm text-gray-500 dark:text-gray-400">
                    <p>Cadastrado em: {{ formatDate(empresa.created_at) }}</p>
                    <p>Última atualização: {{ formatDate(empresa.updated_at) }}</p>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <form @submit.prevent="submit" class="p-6 space-y-8">

                    <!-- Dados da Empresa -->
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Dados da Empresa
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- CNPJ -->
                            <div>
                                <label for="cnpj"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    CNPJ *
                                </label>
                                <input id="cnpj" type="text" v-model="form.cnpj" @input="handleCNPJInput"
                                    placeholder="00.000.000/0000-00" maxlength="18"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    :class="{ 'border-red-500': form.errors.cnpj }" required />
                                <div v-if="form.errors.cnpj" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.cnpj }}
                                </div>
                            </div>

                            <!-- Razão Social -->
                            <div>
                                <label for="razao_social"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Razão Social *
                                </label>
                                <input id="razao_social" type="text" v-model="form.razao_social"
                                    placeholder="Empresa Ltda" maxlength="255"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    :class="{ 'border-red-500': form.errors.razao_social }" required />
                                <div v-if="form.errors.razao_social" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.razao_social }}
                                </div>
                            </div>

                            <!-- Nome Fantasia -->
                            <div>
                                <label for="nome_fantasia"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Nome Fantasia
                                </label>
                                <input id="nome_fantasia" type="text" v-model="form.nome_fantasia"
                                    placeholder="Nome comercial da empresa" maxlength="255"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    :class="{ 'border-red-500': form.errors.nome_fantasia }" />
                                <div v-if="form.errors.nome_fantasia" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.nome_fantasia }}
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Email *
                                </label>
                                <input id="email" type="email" v-model="form.email" placeholder="contato@empresa.com.br"
                                    maxlength="150"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    :class="{ 'border-red-500': form.errors.email }" required />
                                <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.email }}
                                </div>
                            </div>

                            <!-- Telefone -->
                            <div>
                                <label for="telefone"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Telefone *
                                </label>
                                <input id="telefone" type="text" v-model="form.telefone" @input="handlePhoneInput"
                                    placeholder="(11) 99999-9999" maxlength="15"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    :class="{ 'border-red-500': form.errors.telefone }" required />
                                <div v-if="form.errors.telefone" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.telefone }}
                                </div>
                            </div>

                            <!-- Ramo de Atividade -->
                            <div>
                                <label for="ramo_atividade"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Ramo de Atividade *
                                </label>
                                <input id="ramo_atividade" type="text" v-model="form.ramo_atividade"
                                    placeholder="Ex: Tecnologia, Construção, Alimentício" maxlength="100"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    :class="{ 'border-red-500': form.errors.ramo_atividade }" required />
                                <div v-if="form.errors.ramo_atividade" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.ramo_atividade }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Endereço -->
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Endereço
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- CEP -->
                            <div>
                                <label for="cep"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    CEP *
                                </label>
                                <input id="cep" type="text" v-model="form.endereco.cep" @input="handleCEPInput"
                                    placeholder="00000-000" maxlength="9"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    :class="{ 'border-red-500': form.errors['endereco.cep'] }" required />
                                <div v-if="form.errors['endereco.cep']" class="mt-1 text-sm text-red-600">
                                    {{ form.errors['endereco.cep'] }}
                                </div>
                            </div>

                            <!-- Estado -->
                            <div>
                                <label for="estado"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Estado *
                                </label>
                                <select id="estado" v-model="form.endereco.estado"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    :class="{ 'border-red-500': form.errors['endereco.estado'] }" required>
                                    <option value="">Selecione...</option>
                                    <option value="AC">AC</option>
                                    <option value="AL">AL</option>
                                    <option value="AP">AP</option>
                                    <option value="AM">AM</option>
                                    <option value="BA">BA</option>
                                    <option value="CE">CE</option>
                                    <option value="DF">DF</option>
                                    <option value="ES">ES</option>
                                    <option value="GO">GO</option>
                                    <option value="MA">MA</option>
                                    <option value="MT">MT</option>
                                    <option value="MS">MS</option>
                                    <option value="MG">MG</option>
                                    <option value="PA">PA</option>
                                    <option value="PB">PB</option>
                                    <option value="PR">PR</option>
                                    <option value="PE">PE</option>
                                    <option value="PI">PI</option>
                                    <option value="RJ">RJ</option>
                                    <option value="RN">RN</option>
                                    <option value="RS">RS</option>
                                    <option value="RO">RO</option>
                                    <option value="RR">RR</option>
                                    <option value="SC">SC</option>
                                    <option value="SP">SP</option>
                                    <option value="SE">SE</option>
                                    <option value="TO">TO</option>
                                </select>
                                <div v-if="form.errors['endereco.estado']" class="mt-1 text-sm text-red-600">
                                    {{ form.errors['endereco.estado'] }}
                                </div>
                            </div>

                            <!-- Cidade -->
                            <div>
                                <label for="cidade"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Cidade *
                                </label>
                                <input id="cidade" type="text" v-model="form.endereco.cidade"
                                    placeholder="Nome da cidade" maxlength="100"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    :class="{ 'border-red-500': form.errors['endereco.cidade'] }" required />
                                <div v-if="form.errors['endereco.cidade']" class="mt-1 text-sm text-red-600">
                                    {{ form.errors['endereco.cidade'] }}
                                </div>
                            </div>

                            <!-- Logradouro -->
                            <div class="md:col-span-2">
                                <label for="logradouro"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Logradouro *
                                </label>
                                <input id="logradouro" type="text" v-model="form.endereco.logradouro"
                                    placeholder="Rua, Avenida, etc." maxlength="255"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    :class="{ 'border-red-500': form.errors['endereco.logradouro'] }" required />
                                <div v-if="form.errors['endereco.logradouro']" class="mt-1 text-sm text-red-600">
                                    {{ form.errors['endereco.logradouro'] }}
                                </div>
                            </div>

                            <!-- Número -->
                            <div>
                                <label for="numero"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Número *
                                </label>
                                <input id="numero" type="text" v-model="form.endereco.numero" placeholder="123"
                                    maxlength="10"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    :class="{ 'border-red-500': form.errors['endereco.numero'] }" required />
                                <div v-if="form.errors['endereco.numero']" class="mt-1 text-sm text-red-600">
                                    {{ form.errors['endereco.numero'] }}
                                </div>
                            </div>

                            <!-- Bairro -->
                            <div class="md:col-span-2">
                                <label for="bairro"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Bairro *
                                </label>
                                <input id="bairro" type="text" v-model="form.endereco.bairro"
                                    placeholder="Nome do bairro" maxlength="100"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    :class="{ 'border-red-500': form.errors['endereco.bairro'] }" required />
                                <div v-if="form.errors['endereco.bairro']" class="mt-1 text-sm text-red-600">
                                    {{ form.errors['endereco.bairro'] }}
                                </div>
                            </div>

                            <!-- Complemento -->
                            <div class="md:col-span-3">
                                <label for="complemento"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Complemento
                                </label>
                                <input id="complemento" type="text" v-model="form.endereco.complemento"
                                    placeholder="Apartamento, Sala, etc. (opcional)" maxlength="100"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" />
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-600">
                        <button type="button" @click="resetForm"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            :disabled="isSubmitting">
                            Restaurar
                        </button>

                        <div class="flex space-x-3">
                            <Link :href="`/empresas/${empresa.id}`"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancelar
                            </Link>

                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="isSubmitting">
                                <span v-if="isSubmitting" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Salvando alterações...
                                </span>
                                <span v-else>Salvar Alterações</span>
                            </button>
                        </div>
                    </div>

                    <!-- Changes Indicator -->
                    <div v-if="form.isDirty"
                        class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md">
                        <div class="flex">
                            <svg class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                    Você tem alterações não salvas. Lembre-se de salvar antes de sair desta página.
                                </p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
