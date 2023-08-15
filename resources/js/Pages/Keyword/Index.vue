<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import KeywordsList from '@/Pages/Keyword/Partials/KeywordsList.vue'
import QueueStatus from '@/Pages/Keyword/Partials/QueueStatus.vue'
import KeywordImportForm from '@/Pages/Keyword/Partials/KeywordImportForm.vue'

defineProps({
    queue_size: String,
    queue_time: String,
    keywords: Object,
    sort_key: String,
    sort_direction: String,
    search: String,
    errors: Object,
})
</script>

<template>
    <AppLayout title="Keywords">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Keywords</h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-5 sm:px-6 lg:px-8">
                <KeywordImportForm :errors="errors" />
            </div>
            <div class="max-w-7xl mx-auto py-5 sm:px-6 lg:px-8">
                <KeywordsList
                    :keywords="keywords"
                    :sort-key="sort_key"
                    :sort-direction="sort_direction"
                    :search="search"
                />
            </div>
            <div class="max-w-7xl mx-auto py-5 sm:px-6 lg:px-8">
                <QueueStatus :queue_size="queue_size" :queue_time="queue_time" />
            </div>
        </div>
    </AppLayout>
</template>
