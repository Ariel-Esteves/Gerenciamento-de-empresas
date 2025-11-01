<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="m-auto h-full p-6 gap-6 flex flex-col">
            <header class="flex justify-between">
                <div class="text-left text-2xl font-semibold text-gray-900 dark:text-gray-100">
                    <h1>Product List</h1>
                    <span class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Find the products and their details
                        below.
                    </span>
                </div>
                <ButtonPrimary class="mt-4" to="products/create" label="New Product" />
            </header>

            <div
                class="flex p-8 rounded-lg bg-gray-800 flex-wrap mx-auto gap-6 w-fit justify-items-center shadow-sm border border-gray-700">

                <Link :href="'products/' + product.id" class="flex-1 m-1 w-50 bg-gray-700 rounded-md min-w-32"
                    v-for="product in products" :key="product.id">

                <div>
                    <img alt="Product Image" :src="product.images[product.images.length - 1]?.image"
                        class="m-auto w-40 h-40 object-cover " />
                </div>
                <div class="p-5 ">
                    <div class="text-center">
                        <h3>
                            {{ product.name }}
                        </h3>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                        <p>
                            {{ product.description }}
                        </p>
                    </div>
                    <div class="text-right bottom-0">
                        <button>
                            $ {{ product.price }}
                        </button>
                    </div>
                </div>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import ButtonPrimary from '@/components/ButtonPrimary.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Hierarquia de Empresas',
        href: '/hierarquia',
    },
];
interface Product {
    id: number;
    name: string;
    description: string;
    price: number;
    images: { id: number; image: string }[];
}

interface Props {
    products: Product[];
}


const props = defineProps<Props>();
</script>
