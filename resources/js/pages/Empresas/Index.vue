<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { useToast } from '@/composables/useToast';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

// Props from Laravel controller
interface Empresa {
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
        complemento?: string;
        bairro: string;
        cidade: string;
        estado: string;
    };
    anexos_count: number;
}

interface Props {
    empresas: Empresa[];
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
];

// Toast notifications
const { success: showSuccessToast, error: showErrorToast } = useToast();

// Filtering functionality
const searchTerm = ref('');
const selectedState = ref('');
const selectedActivity = ref('');
const showingDetails = ref<number | null>(null);

// Pagination functionality
const currentPage = ref(1);
const itemsPerPage = ref(9); // Default items per page
const itemsPerPageOptions = [6, 9, 12, 18, 24, 50];

// Get unique values for filters
const uniqueStates = computed(() => {
    const states = [...new Set(props.empresas.map(empresa => empresa.endereco.estado))];
    return states.sort();
});

const uniqueActivities = computed(() => {
    const activities = [...new Set(props.empresas.map(empresa => empresa.ramo_atividade))];
    return activities.sort();
});

// Filtered empresas
const filteredEmpresas = computed(() => {
    return props.empresas.filter(empresa => {
        const matchesSearch = searchTerm.value === '' ||
            empresa.nome_fantasia?.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
            empresa.razao_social.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
            empresa.cnpj.includes(searchTerm.value) ||
            empresa.email.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
            empresa.endereco.cidade.toLowerCase().includes(searchTerm.value.toLowerCase());

        const matchesState = selectedState.value === '' ||
            empresa.endereco.estado === selectedState.value;

        const matchesActivity = selectedActivity.value === '' ||
            empresa.ramo_atividade === selectedActivity.value;

        return matchesSearch && matchesState && matchesActivity;
    });
});

// Pagination computed properties
const totalItems = computed(() => filteredEmpresas.value.length);
const totalPages = computed(() => Math.ceil(totalItems.value / itemsPerPage.value));

// Paginated empresas
const paginatedEmpresas = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredEmpresas.value.slice(start, end);
});

// Pagination info
const paginationInfo = computed(() => {
    const start = totalItems.value === 0 ? 0 : (currentPage.value - 1) * itemsPerPage.value + 1;
    const end = Math.min(currentPage.value * itemsPerPage.value, totalItems.value);
    return { start, end, total: totalItems.value };
});

// Pagination methods
const goToPage = (page: number) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
    }
};

const previousPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
    }
};

const changeItemsPerPage = (newValue: number) => {
    itemsPerPage.value = newValue;
    // Reset to first page when changing items per page
    currentPage.value = 1;
};

// Get visible page numbers for pagination
const visiblePages = computed(() => {
    const pages: (number | string)[] = [];
    const total = totalPages.value;
    const current = currentPage.value;

    if (total <= 7) {
        // Show all pages if total is 7 or less
        for (let i = 1; i <= total; i++) {
            pages.push(i);
        }
    } else {
        // Always show first page
        pages.push(1);

        if (current > 4) {
            pages.push('...');
        }

        // Show pages around current page
        const start = Math.max(2, current - 1);
        const end = Math.min(total - 1, current + 1);

        for (let i = start; i <= end; i++) {
            if (i !== 1 && i !== total) {
                pages.push(i);
            }
        }

        if (current < total - 3) {
            pages.push('...');
        }

        // Always show last page
        if (total > 1) {
            pages.push(total);
        }
    }

    return pages;
});

// Clear all filters
const clearFilters = () => {
    searchTerm.value = '';
    selectedState.value = '';
    selectedActivity.value = '';
    currentPage.value = 1; // Reset to first page when clearing filters
};

// Reset to first page when filters change
watch([searchTerm, selectedState, selectedActivity], () => {
    currentPage.value = 1;
});

const toggleDetails = (empresaId: number) => {
    showingDetails.value = showingDetails.value === empresaId ? null : empresaId;
};

const deleteEmpresa = (empresa: Empresa) => {
    if (confirm(`Tem certeza que deseja excluir a empresa "${empresa.razao_social}"?`)) {
        router.delete(`/empresas/${empresa.id}`, {
            onSuccess: () => {
                showSuccessToast('Empresa Excluída com sucesso!');
            },
            onError: () => {
                showErrorToast('Erro ao Excluir', 'Ocorreu um erro ao excluir a empresa. Tente novamente.');
            }
        });
    }
};
</script>

<template>

    <Head title="Empresas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Flash Messages -->
            <!-- Success message will be shown via Inertia's default flash handling -->
            <!-- Error message will be shown via Inertia's default flash handling -->

            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                        Empresas Cadastradas
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400">
                        Mostrando {{ paginationInfo.start }}-{{ paginationInfo.end }} de {{ paginationInfo.total }}
                        empresa{{ paginationInfo.total !== 1 ? 's' : '' }}
                        {{ filteredEmpresas.length === empresas.length ? '' : `(filtrado${paginationInfo.total !== 1 ?
                            's' : ''} de ${empresas.length})` }}
                    </p>
                </div>

                <Link :href="route('empresas.create')"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Nova Empresa
                </Link>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Buscar
                        </label>
                        <div class="relative">
                            <input id="search" v-model="searchTerm" type="text"
                                placeholder="Nome, CNPJ, email ou cidade..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400" />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- State Filter -->
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Estado
                        </label>
                        <select id="state" v-model="selectedState"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            <option value="">Todos os estados</option>
                            <option v-for="state in uniqueStates" :key="state" :value="state">
                                {{ state }}
                            </option>
                        </select>
                    </div>

                    <!-- Activity Filter -->
                    <div>
                        <label for="activity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Ramo de Atividade
                        </label>
                        <select id="activity" v-model="selectedActivity"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            <option value="">Todas as atividades</option>
                            <option v-for="activity in uniqueActivities" :key="activity" :value="activity">
                                {{ activity }}
                            </option>
                        </select>
                    </div>

                    <!-- Items per page -->
                    <div>
                        <label for="itemsPerPage"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Itens por página
                        </label>
                        <select id="itemsPerPage" :value="itemsPerPage"
                            @change="changeItemsPerPage(Number(($event.target as HTMLSelectElement).value))"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            <option v-for="option in itemsPerPageOptions" :key="option" :value="option">
                                {{ option }}
                            </option>
                        </select>
                    </div>

                    <!-- Clear Filters -->
                    <div class="flex items-end">
                        <button @click="clearFilters"
                            class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-500 transition-colors"
                            :disabled="!searchTerm && !selectedState && !selectedActivity">
                            Limpar Filtros
                        </button>
                    </div>
                </div>

                <!-- Active Filters Display -->
                <div v-if="searchTerm || selectedState || selectedActivity" class="mt-4 flex flex-wrap gap-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Filtros ativos:</span>

                    <span v-if="searchTerm"
                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        Busca: "{{ searchTerm }}"
                        <button @click="searchTerm = ''"
                            class="ml-1 text-blue-600 hover:text-blue-800 dark:text-blue-400">
                            ✕
                        </button>
                    </span>

                    <span v-if="selectedState"
                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        Estado: {{ selectedState }}
                        <button @click="selectedState = ''"
                            class="ml-1 text-green-600 hover:text-green-800 dark:text-green-400">
                            ✕
                        </button>
                    </span>

                    <span v-if="selectedActivity"
                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                        Atividade: {{ selectedActivity }}
                        <button @click="selectedActivity = ''"
                            class="ml-1 text-purple-600 hover:text-purple-800 dark:text-purple-400">
                            ✕
                        </button>
                    </span>
                </div>
            </div>

            <!-- Empresas Grid -->
            <div v-if="paginatedEmpresas.length > 0" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <div v-for="empresa in paginatedEmpresas" :key="empresa.id"
                    class="p-6 border border-sidebar-border/70 dark:border-sidebar-border rounded-xl hover:shadow-lg transition-all duration-200 bg-white dark:bg-gray-800">
                    <!-- Company Header -->
                    <div class="mb-4">
                        <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100">
                            {{ empresa.nome_fantasia || empresa.razao_social }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400" v-if="empresa.nome_fantasia">
                            {{ empresa.razao_social }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 font-mono">
                            CNPJ: {{ empresa.cnpj }}
                        </p>
                    </div>

                    <!-- Company Details -->
                    <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                        <div class="flex items-center gap-2">
                            <span class="text-gray-400">📧</span>
                            <span class="truncate">{{ empresa.email }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-gray-400">📞</span>
                            <span>{{ empresa.telefone }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-gray-400">🏢</span>
                            <span>{{ empresa.ramo_atividade }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-gray-400">📍</span>
                            <span>{{ empresa.endereco.cidade }}, {{ empresa.endereco.estado }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-gray-400">📎</span>
                            <span>{{ empresa.anexos_count }} anexo{{ empresa.anexos_count !== 1 ? 's' : '' }}</span>
                        </div>
                    </div>

                    <!-- Expandable Details -->
                    <div v-if="showingDetails === empresa.id"
                        class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <h4 class="font-medium mb-2">Endereço Completo:</h4>
                            <p>
                                {{ empresa.endereco.logradouro }}, {{ empresa.endereco.numero }}
                                <span v-if="empresa.endereco.complemento">, {{ empresa.endereco.complemento }}</span>
                            </p>
                            <p>{{ empresa.endereco.bairro }}</p>
                            <p>{{ empresa.endereco.cidade }} - {{ empresa.endereco.estado }}</p>
                            <p>CEP: {{ empresa.endereco.cep }}</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-4 flex flex-wrap gap-2">
                        <button @click="toggleDetails(empresa.id)"
                            class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors">
                            {{ showingDetails === empresa.id ? 'Menos' : 'Mais' }} Detalhes
                        </button>

                        <Link :href="route('empresas.show', empresa.id)"
                            class="px-3 py-1 text-sm bg-blue-100 text-blue-700 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800 rounded transition-colors">
                        Visualizar
                        </Link>

                        <Link :href="route('empresas.edit', empresa.id)"
                            class="px-3 py-1 text-sm bg-yellow-100 text-yellow-700 hover:bg-yellow-200 dark:bg-yellow-900 dark:text-yellow-300 dark:hover:bg-yellow-800 rounded transition-colors">
                        Editar
                        </Link>

                        <button @click="deleteEmpresa(empresa)"
                            class="px-3 py-1 text-sm bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900 dark:text-red-300 dark:hover:bg-red-800 rounded transition-colors">
                            Excluir
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
                <!-- No empresas at all -->
                <div v-if="empresas.length === 0">
                    <div class="text-gray-400 dark:text-gray-600 text-6xl mb-4">🏢</div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                        Nenhuma empresa cadastrada
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        Comece adicionando sua primeira empresa ao sistema
                    </p>
                    <Link :href="route('empresas.create')"
                        class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Cadastrar Primeira Empresa
                    </Link>
                </div>

                <!-- No filtered results -->
                <div v-else>
                    <div class="text-gray-400 dark:text-gray-600 text-6xl mb-4">🔍</div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                        Nenhuma empresa encontrada
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        Tente ajustar os filtros ou realize uma nova busca
                    </p>
                    <button @click="clearFilters"
                        class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Limpar Filtros
                    </button>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="totalPages > 1"
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <!-- Pagination Info -->
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Mostrando {{ paginationInfo.start }} a {{ paginationInfo.end }} de {{ paginationInfo.total }}
                        resultados
                    </div>

                    <!-- Pagination Controls -->
                    <div class="flex items-center space-x-2">
                        <!-- Previous Button -->
                        <button @click="previousPage" :disabled="currentPage === 1"
                            class="relative inline-flex items-center px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            <span class="ml-1">Anterior</span>
                        </button>

                        <!-- Page Numbers -->
                        <div class="flex items-center space-x-1">
                            <button v-for="page in visiblePages" :key="page"
                                @click="typeof page === 'number' ? goToPage(page) : null"
                                :disabled="typeof page !== 'number'" :class="[
                                    'relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors',
                                    page === currentPage
                                        ? 'bg-blue-600 text-white border-blue-600'
                                        : typeof page === 'number'
                                            ? 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600'
                                            : 'bg-white dark:bg-gray-700 text-gray-400 dark:text-gray-500 border-gray-300 dark:border-gray-600 cursor-default',
                                    'border'
                                ]">
                                {{ page }}
                            </button>
                        </div>

                        <!-- Next Button -->
                        <button @click="nextPage" :disabled="currentPage === totalPages"
                            class="relative inline-flex items-center px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                            <span class="mr-1">Próxima</span>
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div v-if="filteredEmpresas.length > 0" class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div
                    class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-sidebar-border/70 dark:border-sidebar-border">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                        {{ filteredEmpresas.length }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        {{ filteredEmpresas.length === empresas.length ? 'Total de Empresas' : 'Empresas Filtradas' }}
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-sidebar-border/70 dark:border-sidebar-border">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                        {{filteredEmpresas.reduce((sum, e) => sum + e.anexos_count, 0)}}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Total de Anexos
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-sidebar-border/70 dark:border-sidebar-border">
                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                        {{new Set(filteredEmpresas.map(e => e.endereco.estado)).size}}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Estados
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-sidebar-border/70 dark:border-sidebar-border">
                    <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                        {{new Set(filteredEmpresas.map(e => e.ramo_atividade)).size}}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Ramos de Atividade
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>
