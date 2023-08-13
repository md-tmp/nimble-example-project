<script setup>
import ActionSection from '@/Components/ActionSection.vue'
import Pagination from '@/Components/Pagination.vue'
import SortLink from '@/Components/SortLink.vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    keywords: Object,
    sortKey: String,
    sortDirection: String,
    search: String,
})

const form = useForm({
    search: props.search,
    sort: props.sortKey,
    dir: props.sortDirection,
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
    <div>
        <div>
            <ActionSection>
                <template #title> Keywords </template>

                <template #description> Select a keyword to view search reports. </template>

                <!-- Keywords List -->
                <template #content>
                    <div class="space-y-6">
                        <div class="mb-2">
                            <form @submit.prevent="form.get(route('keywords.index'))">
                                <label
                                    for="search"
                                    class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white"
                                    >Search</label
                                >
                                <div class="relative">
                                    <div
                                        class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                    >
                                        <svg
                                            class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                            aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 20 20"
                                        >
                                            <path
                                                stroke="currentColor"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"
                                            />
                                        </svg>
                                    </div>
                                    <input
                                        id="search"
                                        v-model="form.search"
                                        type="text"
                                        class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-gray-500 focus:border-gray-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                        placeholder="Search..."
                                    />
                                    <button
                                        type="submit"
                                        class="text-white absolute right-2.5 bottom-2.5 bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                                    >
                                        Search
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="break-all font-bold">
                                <SortLink
                                    sort="keyword"
                                    :dir="keywordSortDir"
                                    :active-key="sortKey"
                                    :search="search"
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
                                        :search="search"
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
