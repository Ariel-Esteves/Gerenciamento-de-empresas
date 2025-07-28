<script setup lang="ts">
import { ref, computed, watch } from 'vue';

interface SubRamo {
    id: string;
    nome: string;
}

interface RamoAtividade {
    id: string;
    nome: string;
    subramos: SubRamo[];
}

// Props
const props = defineProps<{
    modelValue?: string;
    placeholder?: string;
    required?: boolean;
    disabled?: boolean;
}>();

// Emits
const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

// Data mockada com ramos de atividade em dois níveis
const ramosAtividade: RamoAtividade[] = [
    {
        id: 'tecnologia',
        nome: 'Tecnologia da Informação',
        subramos: [
            { id: 'desenvolvimento-software', nome: 'Desenvolvimento de Software' },
            { id: 'consultoria-ti', nome: 'Consultoria em TI' },
            { id: 'suporte-tecnico', nome: 'Suporte Técnico' },
            { id: 'seguranca-digital', nome: 'Segurança Digital' },
            { id: 'cloud-computing', nome: 'Cloud Computing' },
        ]
    },
    {
        id: 'construcao',
        nome: 'Construção Civil',
        subramos: [
            { id: 'construcao-residencial', nome: 'Construção Residencial' },
            { id: 'construcao-comercial', nome: 'Construção Comercial' },
            { id: 'reforma-ampliacao', nome: 'Reforma e Ampliação' },
            { id: 'engenharia-estrutural', nome: 'Engenharia Estrutural' },
            { id: 'arquitetura-design', nome: 'Arquitetura e Design' },
        ]
    },
    {
        id: 'alimenticio',
        nome: 'Alimentício',
        subramos: [
            { id: 'restaurante', nome: 'Restaurante' },
            { id: 'lanchonete', nome: 'Lanchonete' },
            { id: 'padaria-confeitaria', nome: 'Padaria e Confeitaria' },
            { id: 'catering-eventos', nome: 'Catering para Eventos' },
            { id: 'delivery-food', nome: 'Delivery de Comida' },
        ]
    },
    {
        id: 'comercio',
        nome: 'Comércio Varejista',
        subramos: [
            { id: 'vestuario-moda', nome: 'Vestuário e Moda' },
            { id: 'eletronicos', nome: 'Eletrônicos' },
            { id: 'casa-decoracao', nome: 'Casa e Decoração' },
            { id: 'esportes-lazer', nome: 'Esportes e Lazer' },
            { id: 'livraria-papelaria', nome: 'Livraria e Papelaria' },
        ]
    },
    {
        id: 'saude',
        nome: 'Saúde e Bem-estar',
        subramos: [
            { id: 'clinica-medica', nome: 'Clínica Médica' },
            { id: 'odontologia', nome: 'Odontologia' },
            { id: 'fisioterapia', nome: 'Fisioterapia' },
            { id: 'psicologia', nome: 'Psicologia' },
            { id: 'farmacia', nome: 'Farmácia' },
        ]
    },
    {
        id: 'educacao',
        nome: 'Educação',
        subramos: [
            { id: 'escola-infantil', nome: 'Escola Infantil' },
            { id: 'ensino-fundamental', nome: 'Ensino Fundamental' },
            { id: 'ensino-medio', nome: 'Ensino Médio' },
            { id: 'cursos-tecnicos', nome: 'Cursos Técnicos' },
            { id: 'reforco-escolar', nome: 'Reforço Escolar' },
        ]
    },
    {
        id: 'servicos',
        nome: 'Prestação de Serviços',
        subramos: [
            { id: 'limpeza-conservacao', nome: 'Limpeza e Conservação' },
            { id: 'seguranca-privada', nome: 'Segurança Privada' },
            { id: 'transporte-logistica', nome: 'Transporte e Logística' },
            { id: 'manutencao-predial', nome: 'Manutenção Predial' },
            { id: 'jardinagem-paisagismo', nome: 'Jardinagem e Paisagismo' },
        ]
    },
    {
        id: 'industria',
        nome: 'Indústria',
        subramos: [
            { id: 'metalurgia', nome: 'Metalurgia' },
            { id: 'textil', nome: 'Têxtil' },
            { id: 'quimica-farmaceutica', nome: 'Química e Farmacêutica' },
            { id: 'alimenticia-bebidas', nome: 'Alimentícia e Bebidas' },
            { id: 'moveis-madeira', nome: 'Móveis e Madeira' },
        ]
    }
];

// State
const selectedRamo = ref<string>('');
const selectedSubRamo = ref<string>('');
const showDropdown = ref(false);

// Computed
const selectedRamoData = computed(() => {
    return ramosAtividade.find(ramo => ramo.id === selectedRamo.value);
});

const selectedSubRamoData = computed(() => {
    if (!selectedRamoData.value) return null;
    return selectedRamoData.value.subramos.find(subramo => subramo.id === selectedSubRamo.value);
});

const displayValue = computed(() => {
    if (selectedRamoData.value && selectedSubRamoData.value) {
        return `${selectedRamoData.value.nome} > ${selectedSubRamoData.value.nome}`;
    }
    return '';
});

// Methods
const selectRamo = (ramoId: string) => {
    selectedRamo.value = ramoId;
    selectedSubRamo.value = '';
    // Não fecha o dropdown ainda, permite selecionar o subramo
};

const selectSubRamo = (subRamoId: string) => {
    selectedSubRamo.value = subRamoId;

    // Emite o valor completo
    if (selectedRamoData.value && selectedSubRamoData.value) {
        const fullValue = `${selectedRamoData.value.nome} > ${selectedSubRamoData.value.nome}`;
        emit('update:modelValue', fullValue);
    }

    showDropdown.value = false;
};

const clearSelection = () => {
    selectedRamo.value = '';
    selectedSubRamo.value = '';
    emit('update:modelValue', '');
};

const toggleDropdown = () => {
    if (!props.disabled) {
        showDropdown.value = !showDropdown.value;
    }
};

// Watch for external value changes
watch(() => props.modelValue, (newValue) => {
    if (!newValue) {
        selectedRamo.value = '';
        selectedSubRamo.value = '';
        return;
    }

    // Try to parse the value to find matching ramo and subramo
    for (const ramo of ramosAtividade) {
        for (const subramo of ramo.subramos) {
            const fullValue = `${ramo.nome} > ${subramo.nome}`;
            if (fullValue === newValue) {
                selectedRamo.value = ramo.id;
                selectedSubRamo.value = subramo.id;
                return;
            }
        }
    }
}, { immediate: true });

// Close dropdown when clicking outside
const closeDropdown = () => {
    showDropdown.value = false;
};
</script>

<template>
    <div class="relative" @click.stop>
        <!-- Input Field -->
        <div class="relative">
            <input type="text" :value="displayValue" :placeholder="placeholder || 'Selecione o ramo de atividade'"
                :required="required" :disabled="disabled" readonly @click="toggleDropdown"
                class="w-full px-3 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white cursor-pointer"
                :class="{
                    'bg-gray-50 dark:bg-gray-600': disabled,
                    'hover:bg-gray-50 dark:hover:bg-gray-600': !disabled
                }" />

            <!-- Clear button -->
            <button v-if="displayValue && !disabled" type="button" @click.stop="clearSelection"
                class="absolute right-8 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>

            <!-- Dropdown arrow -->
            <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                    :class="{ 'rotate-180': showDropdown }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </div>

        <!-- Dropdown -->
        <div v-if="showDropdown"
            class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-96 overflow-y-auto">
            <div class="py-1">
                <!-- Ramos principais -->
                <div v-for="ramo in ramosAtividade" :key="ramo.id"
                    class="border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                    <!-- Cabeçalho do ramo -->
                    <div @click="selectRamo(ramo.id)"
                        class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer flex items-center justify-between"
                        :class="{
                            'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300': selectedRamo === ramo.id
                        }">
                        <span>{{ ramo.nome }}</span>
                        <svg class="w-4 h-4 transition-transform duration-200"
                            :class="{ 'rotate-90': selectedRamo === ramo.id }" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </div>

                    <!-- Subramos -->
                    <div v-if="selectedRamo === ramo.id" class="bg-white dark:bg-gray-800">
                        <div v-for="subramo in ramo.subramos" :key="subramo.id" @click="selectSubRamo(subramo.id)"
                            class="px-8 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer border-l-2 border-transparent hover:border-blue-500"
                            :class="{
                                'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-blue-500': selectedSubRamo === subramo.id
                            }">
                            {{ subramo.nome }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Backdrop para fechar dropdown -->
        <div v-if="showDropdown" class="fixed inset-0 z-40" @click="closeDropdown"></div>
    </div>
</template>
