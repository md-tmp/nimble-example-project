<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import KeywordsList from '@/Pages/Keyword/Partials/KeywordsList.vue'

defineProps({
    keywords: Object,
    sort_key: String,
    sort_direction: String,
    search: String,
})
</script>

<template>
    <AppLayout title="Keywords">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Keywords</h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <KeywordsList
                    :keywords="keywords"
                    :sort-key="sort_key"
                    :sort-direction="sort_direction"
                    :search="search"
                />
            </div>
        </div>
    </AppLayout>
</template>
