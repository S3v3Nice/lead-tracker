<script setup lang="ts">
import Toast from 'primevue/toast'
import GlobalModals from '@/components/GlobalModals.vue'
import Header from '@/components/Header.vue'
</script>

<template>
    <Toast/>
    <GlobalModals/>
    <div class="surface-ground">
        <Header/>
        <div class='page-container mb-4'>
            <RouterView></RouterView>
        </div>
    </div>
</template>

<style scoped>
.page-container {
    /* Margin to prevent the fixed header from overlapping the beginning of the page content. */
    margin-top: var(--header-with-margin-height);
}
</style>
