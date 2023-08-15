<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import ReportsList from '@/Pages/Keyword/Partials/ReportsList.vue'

defineProps({
    keyword: String,
    reports: Array,
})
</script>

<template>
    <AppLayout title="Keywords">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Keyword: {{ keyword }}
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <ReportsList :reports="reports" />
            </div>
        </div>
    </AppLayout>
</template>
