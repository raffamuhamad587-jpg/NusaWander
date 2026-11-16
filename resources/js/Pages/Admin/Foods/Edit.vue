<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import FoodForm from '@/Components/FoodForm.vue'

defineProps({
    food: {
        type: Object,
        required: true,
    },
})
</script>

<template>
    <AdminLayout>
        <FoodForm :food="food" />
    </AdminLayout>
</template>