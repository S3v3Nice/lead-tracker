<script setup lang="ts">
import {useAuthStore} from '@/stores/auth'
import LeadAppealForm from '@/components/lead/LeadAppealForm.vue'
import LeadDashboard from '@/components/lead/dashboard/LeadDashboard.vue'

const authStore = useAuthStore()
</script>

<template>
    <LeadDashboard v-if="authStore.isAuthenticated"/>
    <div v-else class="surface-overlay p-4 mx-auto rounded-xl border w-full sm:max-w-[600px]">
        <p class="text-2xl font-semibold mb-4 text-center">Расскажите о себе</p>
        <LeadAppealForm/>
    </div>
</template>

<style scoped>

</style>
