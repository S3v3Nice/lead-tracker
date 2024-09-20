<script setup lang="ts">
import {useAuthStore} from '@/stores/auth'
import LeadAppealForm from '@/components/lead/LeadAppealForm.vue'

const authStore = useAuthStore()
</script>

<template>
    <div v-if="!authStore.isAuthenticated" class="surface-overlay p-4 mx-auto rounded-lg border w-full sm:max-w-[600px]">
        <p class="text-2xl font-semibold mb-4 text-center">Расскажите о себе</p>
        <LeadAppealForm/>
    </div>
</template>

<style scoped>

</style>
