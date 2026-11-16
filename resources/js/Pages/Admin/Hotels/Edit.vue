<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import HotelForm from '@/Components/HotelForm.vue'

defineProps({
    hotel: {
        type: Object,
        required: true,
    },
})
</script>

<template>
    <AdminLayout>
        <HotelForm :hotel="hotel" />
    </AdminLayout>
</template>