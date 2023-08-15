<script setup>
import ActionSection from '@/Components/ActionSection.vue'

defineProps({
    queue_size: Number,
    queue_time: String,
})
</script>

<template>
    <ActionSection>
        <template #title> Status </template>

        <template #description> New reports are added to a queue for processing. </template>

        <!-- Keywords List -->
        <template #content>
            <div>
                Queue Size: {{ queue_size }}
                <span v-if="queue_size > 0">(Estimated Time To Process: {{ queue_time }})</span>
            </div>
        </template>
    </ActionSection>
</template>