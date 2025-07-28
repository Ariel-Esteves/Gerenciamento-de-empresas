<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

// Props from Laravel controller
interface Props {
    empresas: {
        id: number;
        cnpj: string;
        razao_social: string;
        nome_fantasia: string;
        email: string;
        telefone: string;
        ramo_atividade: string;
        endereco: {
            id: number;
            cep: string;
            logradouro: string;
            numero: string;
            bairro: string;
            cidade: string;
            estado: string;
        };
        anexos_count: number;
    }[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Empresas',
        href: '/empresas',
    },
];
</script>

<template>

    <Head title="Empresas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Empresas Cadastradas</h1>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Nova Empresa
                </button>
            </div>

            <!-- Empresas Grid -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <div v-for="empresa in empresas" :key="empresa.id"
                    class="p-6 border border-sidebar-border/70 rounded-xl hover:shadow-lg transition-shadow">
                    <!-- Company Header -->
                    <div class="mb-4">
                        <h3 class="font-semibold text-lg">{{ empresa.nome_fantasia || empresa.razao_social }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ empresa.razao_social }}</p>
                        <p class="text-xs text-gray-500">CNPJ: {{ empresa.cnpj }}</p>
                    </div>

                    <!-- Company Details -->
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-4 text-gray-400">📧</span>
                            <span>{{ empresa.email }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-4 text-gray-400">📞</span>
                            <span>{{ empresa.telefone }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-4 text-gray-400">🏢</span>
                            <span>{{ empresa.ramo_atividade }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-4 text-gray-400">📍</span>
                            <span>{{ empresa.endereco.cidade }}, {{ empresa.endereco.estado }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-4 text-gray-400">📎</span>
                            <span>{{ empresa.anexos_count }} anexos</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-4 flex gap-2">
                        <button class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded">
                            Ver Detalhes
                        </button>
                        <button class="px-3 py-1 text-sm bg-blue-100 text-blue-700 hover:bg-blue-200 rounded">
                            Editar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="empresas.length === 0" class="text-center py-12">
                <div class="text-gray-400 text-6xl mb-4">🏢</div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Nenhuma empresa cadastrada</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">Comece adicionando sua primeira empresa</p>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Cadastrar Primeira Empresa
                </button>
            </div>
        </div>
    </AppLayout>
</template>
