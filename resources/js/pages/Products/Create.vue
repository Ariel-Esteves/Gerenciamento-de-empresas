<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';

interface ProductForm {
    name: string;
    description: string;
    price: number;
    category_id: number | null;
    brand_id: number | null;
    [key: string]: any;
}

// Props from controller (array of { id, name })
const props = defineProps<{
    categories: { id: number; name: string }[];
    brands: { id: number; name: string }[];
}>();

const form = useForm<ProductForm>({
    name: '',
    description: '',
    price: 0,
    category_id: null,
    brand_id: null,
});

const createProduct = (): void => {
    form.post('/products', {
        onSuccess: () => {
            console.log('Product created successfully');
        },
        onError: (errors) => {
            console.error('Error creating product:', errors);
        }
    });
};
</script>


<template>
    <AppLayout>
        <div class="p-6">
            <header>
                <div>
                    <h1>Create Product</h1>
                    <span>Create a new product by filling out the form below.</span>
                </div>
            </header>
            <form @submit.prevent="createProduct">
                <div>
                    <label for="name">Name:</label>
                    <input type="text" id="name" v-model="form.name" required />
                </div>
                <div>
                    <label for="description">Description:</label>
                    <textarea id="description" v-model="form.description" required></textarea>
                </div>
                <div>
                    <label for="price">Price:</label>
                    <input type="number" id="price" v-model="form.price" step="0.01" required />
                </div>
                <div>
                    <label for="category_id">Category:</label>
                    <select id="category_id" v-model="form.category_id">
                        <option :value="null">Select a category</option>
                        <option v-for="cat in props.categories" :key="cat.id" :value="cat.id">
                            {{ cat.name }}
                        </option>
                    </select>
                </div>
                <div>
                    <label for="brand_id">Brand:</label>
                    <select id="brand_id" v-model="form.brand_id">
                        <option :value="null">Select a brand</option>
                        <option v-for="brand in props.brands" :key="brand.id" :value="brand.id">
                            {{ brand.name }}
                        </option>
                    </select>
                </div>
                <button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Creating...' : 'Create' }}
                </button>
            </form>
        </div>
    </AppLayout>
</template>

<style lang="">

</style>
