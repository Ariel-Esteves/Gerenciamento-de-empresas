<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';

export interface ToastType {
    id: string;
    type: 'success' | 'error' | 'warning' | 'info';
    title: string;
    message?: string;
    duration?: number;
}

interface Props {
    toast: ToastType;
    onRemove: (id: string) => void;
}

const props = defineProps<Props>();

const isVisible = ref(false);
const timeoutId = ref<NodeJS.Timeout>();

const getIconAndColors = (type: ToastType['type']) => {
    switch (type) {
        case 'success':
            return {
                icon: '✅',
                bgColor: 'bg-green-50 dark:bg-green-900/20',
                borderColor: 'border-green-200 dark:border-green-800',
                textColor: 'text-green-800 dark:text-green-200',
                titleColor: 'text-green-900 dark:text-green-100'
            };
        case 'error':
            return {
                icon: '❌',
                bgColor: 'bg-red-50 dark:bg-red-900/20',
                borderColor: 'border-red-200 dark:border-red-800',
                textColor: 'text-red-800 dark:text-red-200',
                titleColor: 'text-red-900 dark:text-red-100'
            };
        case 'warning':
            return {
                icon: '⚠️',
                bgColor: 'bg-yellow-50 dark:bg-yellow-900/20',
                borderColor: 'border-yellow-200 dark:border-yellow-800',
                textColor: 'text-yellow-800 dark:text-yellow-200',
                titleColor: 'text-yellow-900 dark:text-yellow-100'
            };
        case 'info':
            return {
                icon: 'ℹ️',
                bgColor: 'bg-blue-50 dark:bg-blue-900/20',
                borderColor: 'border-blue-200 dark:border-blue-800',
                textColor: 'text-blue-800 dark:text-blue-200',
                titleColor: 'text-blue-900 dark:text-blue-100'
            };
        default:
            return {
                icon: 'ℹ️',
                bgColor: 'bg-gray-50 dark:bg-gray-900/20',
                borderColor: 'border-gray-200 dark:border-gray-800',
                textColor: 'text-gray-800 dark:text-gray-200',
                titleColor: 'text-gray-900 dark:text-gray-100'
            };
    }
};

const closeToast = () => {
    isVisible.value = false;
    setTimeout(() => {
        props.onRemove(props.toast.id);
    }, 300); // Wait for animation to complete
};

onMounted(() => {
    // Show toast with animation
    setTimeout(() => {
        isVisible.value = true;
    }, 50);

    // Auto-remove after duration
    const duration = props.toast.duration || 5000;
    timeoutId.value = setTimeout(() => {
        closeToast();
    }, duration);
});

onUnmounted(() => {
    if (timeoutId.value) {
        clearTimeout(timeoutId.value);
    }
});

const colors = getIconAndColors(props.toast.type);
</script>

<template>
    <div :class="[
        'transform transition-all duration-300 ease-in-out',
        isVisible ? 'translate-x-0 opacity-100' : 'translate-x-full opacity-0'
    ]">
        <div :class="[
            'max-w-sm w-full shadow-lg rounded-lg pointer-events-auto border',
            colors.bgColor,
            colors.borderColor
        ]">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0 text-xl mr-3">
                        {{ colors.icon }}
                    </div>
                    <div class="w-0 flex-1">
                        <p :class="['text-sm font-medium', colors.titleColor]">
                            {{ toast.title }}
                        </p>
                        <p v-if="toast.message" :class="['mt-1 text-sm', colors.textColor]">
                            {{ toast.message }}
                        </p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="closeToast" :class="[
                            'inline-flex rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 p-1',
                            colors.textColor,
                            'hover:opacity-75'
                        ]">
                            <span class="sr-only">Fechar</span>
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
