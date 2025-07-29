<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import RamoAtividadeSelector from '@/components/RamoAtividadeSelector.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useToast } from '@/composables/useToast';

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
        title: 'Nova Empresa',
        href: '/empresas/create',
    },
];

// Form data that matches your controller validation
const form = useForm({
    tipo_documento: '',
    cnpj: '',
    razao_social: '',
    nome_fantasia: '',
    email: '',
    telefone: '',
    ramo_atividade: '',
    endereco: {
        cep: '',
        logradouro: '',
        numero: '',
        complemento: '',
        complemento_texto: '',
        bairro: '',
        cidade: '',
        estado: '',
    }
});

const isSubmitting = ref(false);
const { error: showErrorToast, success: showSuccessToast } = useToast();

const submit = () => {
    if (isSubmitting.value) return;

    // Validate required fields
    if (!form.tipo_documento) {
        showErrorToast('Erro de Validação', 'Por favor, selecione o tipo de documento');
        return;
    }

    // Validate razão social
    if (!form.razao_social || form.razao_social.trim().length === 0) {
        showErrorToast('Erro de Validação', 'Por favor, informe a razão social');
        return;
    }

    // Validate document format based on type
    if (form.tipo_documento === 'CNPJ') {
        if (!form.cnpj) {
            showErrorToast('Erro de Validação', 'Por favor, informe o CNPJ');
            return;
        }
        if (!isValidCNPJ(form.cnpj)) {
            showErrorToast('Erro de Validação', 'CNPJ inválido. Verifique o formato e os dígitos verificadores');
            return;
        }
        // Validate razão social for CNPJ
        if (!form.razao_social || form.razao_social.trim().length < 3) {
            showErrorToast('Erro de Validação', 'Para CNPJ, a razão social deve ter pelo menos 3 caracteres');
            return;
        }
    } else if (form.tipo_documento === 'CPF') {
        if (!form.cnpj) {
            showErrorToast('Erro de Validação', 'Por favor, informe o CPF');
            return;
        }
        if (!isValidCPF(form.cnpj)) {
            showErrorToast('Erro de Validação', 'CPF inválido. Verifique o formato e os dígitos verificadores');
            return;
        }
    }

    // Validate email format
    if (!form.email) {
        showErrorToast('Erro de Validação', 'Por favor, informe o email');
        return;
    }
    if (!isValidEmail(form.email)) {
        showErrorToast('Erro de Validação', 'Email inválido. Verifique o formato do email');
        return;
    }

    // Validate phone format
    if (!form.telefone) {
        showErrorToast('Erro de Validação', 'Por favor, informe o telefone');
        return;
    }
    if (!isValidBrazilianPhone(form.telefone)) {
        showErrorToast('Erro de Validação', 'Telefone inválido. Use o formato (11) 99999-9999');
        return;
    }

    // Validate all address fields
    if (!form.endereco.cep) {
        showErrorToast('Erro de Validação', 'Por favor, informe o CEP');
        return;
    }
    if (!isValidCEP(form.endereco.cep)) {
        showErrorToast('Erro de Validação', 'CEP inválido. Use o formato 00000-000');
        return;
    }
    if (!form.endereco.logradouro) {
        showErrorToast('Erro de Validação', 'Por favor, informe o logradouro');
        return;
    }
    if (!form.endereco.numero) {
        showErrorToast('Erro de Validação', 'Por favor, informe o número');
        return;
    }
    if (!form.endereco.bairro) {
        showErrorToast('Erro de Validação', 'Por favor, informe o bairro');
        return;
    }
    if (!form.endereco.cidade) {
        showErrorToast('Erro de Validação', 'Por favor, informe a cidade');
        return;
    }
    if (!form.endereco.estado) {
        showErrorToast('Erro de Validação', 'Por favor, selecione o estado');
        return;
    }

    isSubmitting.value = true;
    form.post('/empresas', {
        onSuccess: () => {
            showSuccessToast('Empresa criada com sucesso!');
            // Redirect handled by controller
        },
        onError: () => {
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

// Format CPF as user types
const formatCPF = (value: string) => {
    const cleaned = value.replace(/\D/g, '');
    const match = cleaned.match(/^(\d{3})(\d{3})(\d{3})(\d{2})$/);
    if (match) {
        return `${match[1]}.${match[2]}.${match[3]}-${match[4]}`;
    }
    return cleaned;
};

// Validate CNPJ
const isValidCNPJ = (cnpj: string): boolean => {
    const cleaned = cnpj.replace(/\D/g, '');

    if (cleaned.length !== 14) return false;
    if (/^(\d)\1+$/.test(cleaned)) return false; // All same digits

    // CNPJ validation algorithm
    let sum = 0;
    let weight = 2;

    // First check digit
    for (let i = 11; i >= 0; i--) {
        sum += parseInt(cleaned.charAt(i)) * weight;
        weight = weight === 9 ? 2 : weight + 1;
    }

    let remainder = sum % 11;
    const digit1 = remainder < 2 ? 0 : 11 - remainder;

    if (parseInt(cleaned.charAt(12)) !== digit1) return false;

    // Second check digit
    sum = 0;
    weight = 2;

    for (let i = 12; i >= 0; i--) {
        sum += parseInt(cleaned.charAt(i)) * weight;
        weight = weight === 9 ? 2 : weight + 1;
    }

    remainder = sum % 11;
    const digit2 = remainder < 2 ? 0 : 11 - remainder;

    return parseInt(cleaned.charAt(13)) === digit2;
};

// Validate CPF
const isValidCPF = (cpf: string): boolean => {
    const cleaned = cpf.replace(/\D/g, '');

    if (cleaned.length !== 11) return false;
    if (/^(\d)\1+$/.test(cleaned)) return false; // All same digits

    // CPF validation algorithm
    let sum = 0;

    // First check digit
    for (let i = 0; i < 9; i++) {
        sum += parseInt(cleaned.charAt(i)) * (10 - i);
    }

    let remainder = sum % 11;
    const digit1 = remainder < 2 ? 0 : 11 - remainder;

    if (parseInt(cleaned.charAt(9)) !== digit1) return false;

    // Second check digit
    sum = 0;
    for (let i = 0; i < 10; i++) {
        sum += parseInt(cleaned.charAt(i)) * (11 - i);
    }

    remainder = sum % 11;
    const digit2 = remainder < 2 ? 0 : 11 - remainder;

    return parseInt(cleaned.charAt(10)) === digit2;
};

// Validate Email
const isValidEmail = (email: string): boolean => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
};

// Validate Brazilian Phone
const isValidBrazilianPhone = (phone: string): boolean => {
    const cleaned = phone.replace(/\D/g, '');
    // Brazilian phone: 10 digits (landline) or 11 digits (mobile with 9)
    // Format: (11) 9999-9999 or (11) 99999-9999
    return cleaned.length === 10 || cleaned.length === 11;
};

// Validate CEP format
const isValidCEP = (cep: string): boolean => {
    const cleaned = cep.replace(/\D/g, '');
    return cleaned.length === 8;
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

    // Handle different phone lengths
    if (cleaned.length <= 10) {
        // Landline: (11) 1234-5678
        const match = cleaned.match(/^(\d{2})(\d{4})(\d{4})$/);
        if (match) {
            return `(${match[1]}) ${match[2]}-${match[3]}`;
        }
    } else {
        // Mobile: (11) 91234-5678
        const match = cleaned.match(/^(\d{2})(\d{5})(\d{4})$/);
        if (match) {
            return `(${match[1]}) ${match[2]}-${match[3]}`;
        }
    }

    // Partial formatting for incomplete numbers
    if (cleaned.length >= 2) {
        if (cleaned.length <= 6) {
            return `(${cleaned.slice(0, 2)}) ${cleaned.slice(2)}`;
        } else if (cleaned.length <= 10) {
            return `(${cleaned.slice(0, 2)}) ${cleaned.slice(2, 6)}-${cleaned.slice(6)}`;
        } else {
            return `(${cleaned.slice(0, 2)}) ${cleaned.slice(2, 7)}-${cleaned.slice(7, 11)}`;
        }
    }

    return cleaned;
};

// Auto-format fields on input
const handleCNPJInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    let formatted = '';

    if (form.tipo_documento === 'CNPJ') {
        formatted = formatCNPJ(target.value);
        // Limit to 18 characters (14 digits + 4 separators)
        if (formatted.replace(/\D/g, '').length > 14) {
            formatted = formatCNPJ(formatted.replace(/\D/g, '').substring(0, 14));
        }
    } else if (form.tipo_documento === 'CPF') {
        formatted = formatCPF(target.value);
        // Limit to 14 characters (11 digits + 3 separators)
        if (formatted.replace(/\D/g, '').length > 11) {
            formatted = formatCPF(formatted.replace(/\D/g, '').substring(0, 11));
        }
    } else {
        // No formatting if no document type selected
        formatted = target.value.replace(/\D/g, '');
    }

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
    // Limit to maximum 15 characters (includes formatting)
    if (formatted.length <= 15) {
        form.telefone = formatted;
    }
};

// Reset form
const resetForm = () => {
    form.reset();
};

// Clear document field when type changes
const handleDocumentTypeChange = () => {
    form.cnpj = '';
};
</script>

<template>

    <Head title="Nova Empresa" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                        Nova Empresa
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Preencha os dados para cadastrar uma nova empresa
                    </p>
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
                            <div>
                                <!-- Tipo de Documento -->
                                <div>
                                    <label for="tipo_documento"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Tipo de Documento *
                                    </label>
                                    <select id="tipo_documento" v-model="form.tipo_documento"
                                        @change="handleDocumentTypeChange"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500': form.errors.tipo_documento }" required>
                                        <option value="">Selecione...</option>
                                        <option value="CNPJ">CNPJ</option>
                                        <option value="CPF">CPF</option>
                                    </select>
                                    <div v-if="form.errors.tipo_documento" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.tipo_documento }}
                                    </div>
                                </div>
                            </div>
                            <!-- CNPJ/CPF -->
                            <div>
                                <label for="cnpj"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ form.tipo_documento === 'CPF' ? 'CPF' : 'CNPJ' }} *
                                </label>
                                <input id="cnpj" type="text" v-model="form.cnpj" @input="handleCNPJInput"
                                    :placeholder="form.tipo_documento === 'CPF' ? '000.000.000-00' : '00.000.000/0000-00'"
                                    :maxlength="form.tipo_documento === 'CPF' ? 14 : 18"
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
                                    :placeholder="form.tipo_documento === 'CNPJ' ? 'Empresa Ltda (mín. 3 caracteres)' : 'Nome da empresa'"
                                    maxlength="255"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    :class="{ 'border-red-500': form.errors.razao_social }" required />
                                <div v-if="form.tipo_documento === 'CNPJ'"
                                    class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Para CNPJ, a razão social deve ter pelo menos 3 caracteres
                                </div>
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
                                <RamoAtividadeSelector v-model="form.ramo_atividade"
                                    placeholder="Selecione o ramo de atividade" :required="true" />
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

                            <!-- Complemento texto -->
                            <div class="md:col-span-3">
                                <label for="complemento_texto"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Complemento texto
                                </label>
                                <input id="complemento_texto" type="text" v-model="form.endereco.complemento_texto"
                                    placeholder="Informações adicionais do endereço (opcional)" maxlength="255"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" />
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-600">
                        <button type="button" @click="resetForm"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            :disabled="isSubmitting">
                            Limpar
                        </button>

                        <div class="flex space-x-3">
                            <router-link to="/empresas"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Cancelar
                            </router-link>

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
                                    Salvando...
                                </span>
                                <span v-else>Salvar Empresa</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
