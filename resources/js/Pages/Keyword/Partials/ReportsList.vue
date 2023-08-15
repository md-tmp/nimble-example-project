<script setup>
import ActionSection from '@/Components/ActionSection.vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    reports: Array,
})
</script>

<template>
    <div>
        <ActionSection>
            <template #title> Reports </template>

            <template #description> Google Result Reports </template>

            <!-- Reports List -->
            <template #content>
                <div class="space-y-6">
                    <table class="table-fixed border-separate mx-auto my-auto w-full text-left">
                        <thead>
                            <tr>
                                <th>Report Date</th>
                                <th>Total Links</th>
                                <th>Total Ads</th>
                                <th>Total Results</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="report in reports" :key="report.id">
                                <td>{{ report.created_at_ago }}</td>
                                <td>{{ report.total_links }}</td>
                                <td>{{ report.total_ads }}</td>
                                <td>{{ report.total_results.toLocaleString() }}</td>
                                <td class="text-right">
                                    <Link
                                        :href="route('results.cache', report.id)"
                                        class="underline text-sm text-gray-600 hover:text-gray-900"
                                    >
                                        View Cache
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </template>
        </ActionSection>
    </div>
</template>
