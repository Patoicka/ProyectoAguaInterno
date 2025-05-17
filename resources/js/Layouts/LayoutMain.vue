<script setup>

import SectionMain from "@/Components/SectionMain.vue";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";

const props = defineProps({
    fullWidth: {
        type: Boolean,
        default: false,
    },
});

</script>

<template>
    <LayoutAuthenticated>
        <SectionMain :fullWidth="fullWidth">
            <slot />
        </SectionMain>
    </LayoutAuthenticated>
</template>
