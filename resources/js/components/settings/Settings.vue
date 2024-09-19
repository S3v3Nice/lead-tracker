<script setup lang="ts">
import type {MenuItem} from 'primevue/menuitem'
import {ref, watch} from 'vue'
import TabMenu from 'primevue/tabmenu'
import {useRoute} from 'vue-router'
import SecuritySettings from '@/components/settings/SecuritySettings.vue'

const route = useRoute()
const tabs = ref<MenuItem[]>([
    {label: 'Профиль', route: 'settings.profile', icon: 'fa-regular fa-user'},
    {label: 'Безопасность', route: 'settings.security', icon: 'fa-solid fa-shield-halved', isWarningBadge: true},
])
const activeTabIndex = ref(getActualActiveTabIndex())

watch(route, () => {
    activeTabIndex.value = getActualActiveTabIndex()
})

function getActualActiveTabIndex() {
    return tabs.value.findIndex(tab => tab.route === route.name)
}
</script>

<template>
    <div class="surface-overlay rounded-xl border">
        <p class="text-3xl font-semibold p-4">Настройки</p>
    </div>
    <div class="p-4 surface-overlay rounded-xl border mt-3">
        <div class="settings-section-content">
            <SecuritySettings/>
        </div>
    </div>
</template>

<style scoped>
.settings-section-content {
    max-width: 42rem;
}
</style>
