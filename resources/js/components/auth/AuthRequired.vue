<script setup lang="ts">
import {useGlobalModalStore} from '@/stores/globalModal'
import Message from 'primevue/message'

useGlobalModalStore().isAuth = true
</script>

<template>
    <div class="flex justify-center">
        <Message :closable="false" severity="info">
            Для просмотра страницы необходимо войти в аккаунт.
        </Message>
    </div>
</template>

<style scoped>

</style>
