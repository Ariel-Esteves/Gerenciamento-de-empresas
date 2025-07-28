import { ref } from 'vue';

export interface Toast {
    id: string;
    type: 'success' | 'error' | 'warning' | 'info';
    title: string;
    message?: string;
    duration?: number;
    persistent?: boolean;
}

const toasts = ref<Toast[]>([]);

export function useToast() {
    const addToast = (toast: Omit<Toast, 'id'>) => {
        const id = Date.now().toString() + Math.random().toString(36).substr(2, 9);
        const newToast: Toast = {
            id,
            duration: 5000, // Default 5 seconds
            ...toast,
        };

        toasts.value.push(newToast);

        // Auto remove after duration (unless persistent)
        if (!newToast.persistent && newToast.duration) {
            setTimeout(() => {
                removeToast(id);
            }, newToast.duration);
        }

        return id;
    };

    const removeToast = (id: string) => {
        const index = toasts.value.findIndex((toast) => toast.id === id);
        if (index > -1) {
            toasts.value.splice(index, 1);
        }
    };

    const clearAll = () => {
        toasts.value = [];
    };

    // Convenience methods
    const success = (title: string, message?: string, options?: Partial<Toast>) => {
        return addToast({ type: 'success', title, message, ...options });
    };

    const error = (title: string, message?: string, options?: Partial<Toast>) => {
        return addToast({ type: 'error', title, message, ...options });
    };

    const warning = (title: string, message?: string, options?: Partial<Toast>) => {
        return addToast({ type: 'warning', title, message, ...options });
    };

    const info = (title: string, message?: string, options?: Partial<Toast>) => {
        return addToast({ type: 'info', title, message, ...options });
    };

    return {
        toasts: toasts.value,
        addToast,
        removeToast,
        clearAll,
        success,
        error,
        warning,
        info,
    };
}
