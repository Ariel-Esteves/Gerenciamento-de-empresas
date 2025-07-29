<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

// Props from Laravel controller
interface Company {
    id: number;
    razao_social: string;
    nome_fantasia: string;
    cnpj: string;
    email: string;
    telefone: string;
    cidade: string;
    estado: string;
}

interface Subcategory {
    name: string;
    companies: Company[];
    totalCompanies: number;
}

interface Category {
    name: string;
    subcategories: { [key: string]: Subcategory };
    totalCompanies: number;
}

interface Statistics {
    totalStates: number;
    topStates: { [key: string]: number };
    totalCategories: number;
    topCategories: { [key: string]: number };
    totalAnexos: number;
}

interface Props {
    hierarchy: { [key: string]: Category };
    totalEmpresas: number;
    statistics: Statistics;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Hierarquia de Empresas',
        href: '/hierarquia',
    },
];

// State for controlling expanded categories and subcategories
const expandedCategories = ref<Set<string>>(new Set());
const expandedSubcategories = ref<Set<string>>(new Set());
const selectedCompany = ref<Company | null>(null);
const searchTerm = ref('');

// Computed properties
const filteredHierarchy = computed(() => {
    if (!searchTerm.value) return props.hierarchy;

    const filtered: { [key: string]: Category } = {};
    const search = searchTerm.value.toLowerCase();

    for (const [categoryName, category] of Object.entries(props.hierarchy)) {
        const filteredSubcategories: { [key: string]: Subcategory } = {};
        let hasMatches = false;

        for (const [subName, subcategory] of Object.entries(category.subcategories)) {
            const filteredCompanies = subcategory.companies.filter(company =>
                company.razao_social.toLowerCase().includes(search) ||
                company.nome_fantasia?.toLowerCase().includes(search) ||
                company.cnpj.includes(search) ||
                company.email.toLowerCase().includes(search) ||
                company.cidade.toLowerCase().includes(search) ||
                company.estado.toLowerCase().includes(search)
            );

            if (filteredCompanies.length > 0 ||
                categoryName.toLowerCase().includes(search) ||
                subName.toLowerCase().includes(search)) {
                filteredSubcategories[subName] = {
                    ...subcategory,
                    companies: filteredCompanies,
                    totalCompanies: filteredCompanies.length
                };
                hasMatches = true;
            }
        }

        if (hasMatches) {
            filtered[categoryName] = {
                ...category,
                subcategories: filteredSubcategories,
                totalCompanies: Object.values(filteredSubcategories)
                    .reduce((sum, sub) => sum + sub.totalCompanies, 0)
            };
        }
    }

    return filtered;
});

// Methods
const toggleCategory = (categoryName: string) => {
    if (expandedCategories.value.has(categoryName)) {
        expandedCategories.value.delete(categoryName);
    } else {
        expandedCategories.value.add(categoryName);
    }
};

const toggleSubcategory = (categoryName: string, subcategoryName: string) => {
    const key = `${categoryName}:${subcategoryName}`;
    if (expandedSubcategories.value.has(key)) {
        expandedSubcategories.value.delete(key);
    } else {
        expandedSubcategories.value.add(key);
    }
};

const expandAll = () => {
    expandedCategories.value = new Set(Object.keys(props.hierarchy));
    expandedSubcategories.value = new Set();
    Object.entries(props.hierarchy).forEach(([categoryName, category]) => {
        Object.keys(category.subcategories).forEach(subName => {
            expandedSubcategories.value.add(`${categoryName}:${subName}`);
        });
    });
};

const collapseAll = () => {
    expandedCategories.value.clear();
    expandedSubcategories.value.clear();
};

const showCompanyDetails = (company: Company) => {
    selectedCompany.value = company;
};

const closeCompanyDetails = () => {
    selectedCompany.value = null;
};

const getCompanyIcon = (categoryName: string) => {
    const icons: { [key: string]: string } = {
        'Tecnologia da Informação': '💻',
        'Construção Civil': '🏗️',
        'Alimentício': '🍽️',
        'Comércio Varejista': '🛍️',
        'Saúde e Bem-estar': '🏥',
        'Educação': '📚',
        'Prestação de Serviços': '🔧',
        'Indústria': '🏭',
        'Agronegócio': '🌾',
        'Transporte': '🚛',
        'Financeiro': '💰',
        'Imobiliário': '🏠'
    };
    return icons[categoryName] || '🏢';
};

const getCategoryColor = (index: number) => {
    const colors = [
        'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
        'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200'
    ];
    return colors[index % colors.length];
};
</script>

<template>

    <Head title="Hierarquia de Empresas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                        🏢 Hierarquia de Empresas
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">
                        Visualização hierárquica por ramo de atividade - {{ totalEmpresas }} empresas cadastradas
                    </p>
                </div>

                <!-- Quick Actions -->
                <div class="flex space-x-3">
                    <button @click="collapseAll"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        Recolher Tudo
                    </button>
                    <button @click="expandAll"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 transition-colors">
                        Expandir Tudo
                    </button>
                </div>
            </div>

            <!-- Statistics Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div
                    class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="flex items-center">
                        <div class="text-3xl text-blue-600 dark:text-blue-400 mr-4">🏢</div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ totalEmpresas }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total de Empresas</div>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="flex items-center">
                        <div class="text-3xl text-green-600 dark:text-green-400 mr-4">📊</div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{
                                statistics.totalCategories }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Ramos de Atividade</div>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="flex items-center">
                        <div class="text-3xl text-purple-600 dark:text-purple-400 mr-4">📍</div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ statistics.totalStates
                            }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Estados</div>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="flex items-center">
                        <div class="text-3xl text-orange-600 dark:text-orange-400 mr-4">📎</div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ statistics.totalAnexos
                            }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total de Anexos</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                <div class="relative">
                    <input v-model="searchTerm" type="text" placeholder="Buscar por empresa, CNPJ, categoria, cidade..."
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Hierarchical Tree View -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="p-6">
                    <div v-if="Object.keys(filteredHierarchy).length === 0" class="text-center py-12">
                        <div class="text-gray-400 dark:text-gray-600 text-6xl mb-4">🔍</div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                            Nenhum resultado encontrado
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Tente ajustar os termos de busca
                        </p>
                    </div>

                    <div v-else class="space-y-4">
                        <!-- Categories -->
                        <div v-for="(category, categoryName, categoryIndex) in filteredHierarchy"
                            :key="String(categoryName)"
                            class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">

                            <!-- Category Header -->
                            <div @click="toggleCategory(String(categoryName))" :class="getCategoryColor(categoryIndex)"
                                class="p-4 cursor-pointer hover:opacity-80 transition-opacity">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <span class="text-2xl">{{ getCompanyIcon(String(categoryName)) }}</span>
                                        <div>
                                            <h3 class="text-lg font-semibold">{{ categoryName }}</h3>
                                            <p class="text-sm opacity-80">{{ category.totalCompanies }} empresa{{
                                                category.totalCompanies !== 1 ? 's' : '' }}</p>
                                        </div>
                                    </div>
                                    <svg :class="{ 'rotate-90': expandedCategories.has(String(categoryName)) }"
                                        class="w-5 h-5 transition-transform duration-200" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Subcategories -->
                            <div v-if="expandedCategories.has(String(categoryName))"
                                class="border-t border-gray-200 dark:border-gray-700">
                                <div v-for="(subcategory, subName) in category.subcategories" :key="String(subName)"
                                    class="border-b border-gray-100 dark:border-gray-800 last:border-b-0">

                                    <!-- Subcategory Header -->
                                    <div @click="toggleSubcategory(String(categoryName), String(subName))"
                                        class="p-4 bg-gray-50 dark:bg-gray-700 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <span class="text-gray-400">└</span>
                                                <div>
                                                    <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ subName
                                                    }}</h4>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{
                                                        subcategory.totalCompanies }} empresa{{
                                                            subcategory.totalCompanies !== 1 ? 's' : '' }}</p>
                                                </div>
                                            </div>
                                            <svg :class="{ 'rotate-90': expandedSubcategories.has(`${String(categoryName)}:${String(subName)}`) }"
                                                class="w-4 h-4 transition-transform duration-200 text-gray-600 dark:text-gray-400"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Companies List -->
                                    <div v-if="expandedSubcategories.has(`${String(categoryName)}:${String(subName)}`)"
                                        class="p-4 bg-white dark:bg-gray-800">
                                        <div class="grid gap-3">
                                            <div v-for="company in subcategory.companies" :key="company.id"
                                                @click="showCompanyDetails(company)"
                                                class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex-1 min-w-0">
                                                        <h5
                                                            class="font-medium text-gray-900 dark:text-gray-100 truncate">
                                                            {{ company.nome_fantasia || company.razao_social }}
                                                        </h5>
                                                        <p v-if="company.nome_fantasia"
                                                            class="text-sm text-gray-600 dark:text-gray-400 truncate">
                                                            {{ company.razao_social }}
                                                        </p>
                                                        <div
                                                            class="flex items-center space-x-4 mt-2 text-sm text-gray-500 dark:text-gray-400">
                                                            <span>📋 {{ company.cnpj }}</span>
                                                            <span>📍 {{ company.cidade }}, {{ company.estado }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4 flex-shrink-0">
                                                        <svg class="w-5 h-5 text-gray-400" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Company Details Modal -->
        <div v-if="selectedCompany" @click="closeCompanyDetails"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div @click.stop class="bg-white dark:bg-gray-800 rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                            Detalhes da Empresa
                        </h3>
                        <button @click="closeCompanyDetails"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 text-lg">
                                {{ selectedCompany.nome_fantasia || selectedCompany.razao_social }}
                            </h4>
                            <p v-if="selectedCompany.nome_fantasia" class="text-gray-600 dark:text-gray-400">
                                {{ selectedCompany.razao_social }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">CNPJ:</span>
                                <span class="ml-2 font-mono">{{ selectedCompany.cnpj }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">Email:</span>
                                <span class="ml-2">{{ selectedCompany.email }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">Telefone:</span>
                                <span class="ml-2">{{ selectedCompany.telefone }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">Localização:</span>
                                <span class="ml-2">{{ selectedCompany.cidade }}, {{ selectedCompany.estado }}</span>
                            </div>
                        </div>

                        <div class="flex space-x-3 pt-4">
                            <a :href="`/empresas/${selectedCompany.id}`"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                Ver Detalhes Completos
                            </a>
                            <a :href="`/empresas/${selectedCompany.id}/edit`"
                                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm">
                                Editar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.rotate-90 {
    transform: rotate(90deg);
}
</style>
