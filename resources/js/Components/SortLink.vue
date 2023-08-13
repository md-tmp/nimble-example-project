<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    sort: String,
    dir: String,
    activeKey: String,
    search: String
})
</script>

<template>
    <Link :data="{ sort, dir, search }">
        <slot />
        <span v-if="activeKey == sort" v-html="dir == 'asc' ? '&#9650;' : '&#9660;'" />
    </Link>
</template>
