<script setup>
import ActionSection from '@/Components/ActionSection.vue'
import Pagination from '@/Components/Pagination.vue'
import SortLink from '@/Components/SortLink.vue'

const props = defineProps({
    keywords: Object,
    sortKey: String,
    sortDirection: String,
})

const nextSortDir = (target) => {
    if (target == props.sortKey) {
        return props.sortDirection == 'asc' ? 'desc' : 'asc'
    } else {
        return 'asc'
    }
}

const keywordSortDir = nextSortDir('keyword')
const updatedAtSortDir = nextSortDir('updated_at')
</script>

<template>
    <div v-if="keywords.data.length > 0">
        <div>
            <ActionSection>
                <template #title> Keywords </template>

                <template #description> Select a keyword to view search reports. </template>

                <!-- Keywords List -->
                <template #content>
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <div class="break-all font-bold">
                                <SortLink
                                    sort="keyword"
                                    :dir="keywordSortDir"
                                    :active-key="sortKey"
                                >
                                    Keyword
                                </SortLink>
                            </div>

                            <div class="flex items-center ml-2">
                                <div class="text-sm font-bold">
                                    <SortLink
                                        sort="updated_at"
                                        :dir="updatedAtSortDir"
                                        :active-key="sortKey"
                                    >
                                        Last Updated
                                    </SortLink>
                                </div>
                            </div>
                        </div>
                        <div
                            v-for="keyword in keywords.data"
                            :key="keyword.id"
                            class="flex items-center justify-between"
                        >
                            <div class="break-all">
                                {{ keyword.keyword }}
                            </div>

                            <div class="flex items-center ml-2">
                                <div v-if="keyword.updated_at_ago" class="text-sm text-gray-400">
                                    Last Updated {{ keyword.updated_at_ago }}
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </ActionSection>
        </div>
        <div class="mt-1 sm:mt-4">
            <Pagination :links="keywords.links" />
        </div>
    </div>
</template>
