<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DestinationForm from '@/Components/DestinationForm.vue'

defineProps({
    categories: {
        type: Array,
        required: true,
    },
})
</script>

<template>
    <AdminLayout>
        <DestinationForm :categories="categories" />
    </AdminLayout>
</template>
