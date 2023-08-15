<script setup>
import ActionSection from '@/Components/ActionSection.vue'
import { useForm } from '@inertiajs/vue3'

defineProps({
    errors: Object,
})

const form = useForm({
    import_file: null,
})

function submit() {
    form.post(route('keywords.store'), {
        preserveState: false,
    })
}
</script>

<template>
    <ActionSection>
        <template #title> Import Keywords CSV </template>

        <template #description>
            Reports are run for all uploaded keywords. You can re-upload an existing keyword to run
            a new report for it.
        </template>

        <!-- Keywords List -->
        <template #content>
            <form @submit.prevent="submit">
                <div v-if="$page.props.jetstream.flash.upload_success" class="font-bold mb-4">
                    {{ $page.props.jetstream.flash.upload_success }}
                </div>
                <div>
                    <label for="csv-file" class="mr-1 font-bold"> Import CSV: </label>
                    <input
                        id="csv-file"
                        type="file"
                        @input="form.import_file = $event.target.files[0]"
                    />
                </div>
                <div class="red-600 font-bold mt-1" v-if="errors.import_file">
                    {{ errors.import_file }}
                </div>
                <div class="text-sm mt-1">
                    CSV Rules: Keyword column heading required, minimum 1 keyword, maximum 100
                    keywords.
                </div>
                <div class="mt-1">
                    <a
                        href="https://raw.githubusercontent.com/md-tmp/nimble-example-project/main/example.csv"
                        target="_BLANK"
                        class="underline text-sm text-gray-600 hover:text-gray-900"
                    >
                        Example CSV File
                    </a>
                </div>
                <div>
                    <button
                        class="text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-2 mt-2 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                        type="submit"
                    >
                        Import
                    </button>
                </div>
            </form>
            <div>
                <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                    {{ form.progress.percentage }}%
                </progress>
            </div>
        </template>
    </ActionSection>
</template>