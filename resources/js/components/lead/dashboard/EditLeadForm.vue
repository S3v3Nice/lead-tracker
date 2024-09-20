<script setup lang="ts">
import InputText from 'primevue/inputtext'
import InputMask from 'primevue/inputmask'
import Textarea from 'primevue/textarea'
import Button from 'primevue/button'
import {ref, toRef} from 'vue'
import axios, {type AxiosError} from 'axios'
import {useToast} from 'primevue/usetoast'
import {getErrorMessageByCode, ToastHelper} from '@/helpers'
import type {Lead} from '@/types'

const props = defineProps<{
    lead: Lead
}>()
const emit = defineEmits(['cancel', 'processed'])

const toastHelper = new ToastHelper(useToast())
const lead = toRef(props.lead)
const isProcessing = ref(false)
const errors = ref([])

function submit() {
    isProcessing.value = true
    errors.value = []

    axios.patch(`/api/leads/${lead.value.id}`, lead.value).then((response) => {
        if (response.data.success) {
            toastHelper.success('Информация о лиде изменена!')
            emit('processed')
        } else {
            if (response.data.errors) {
                errors.value = response.data.errors
            }
            if (response.data.message) {
                toastHelper.error(response.data.message)
            }
        }
    }).catch((error: AxiosError) => {
        toastHelper.error(getErrorMessageByCode(error.response!.status))
    }).finally(() => {
        isProcessing.value = false
    })
}
</script>

<template>
    <form class="space-y-3" @submit.prevent="submit">
        <div class="space-y-1">
            <label for="first-name" :class="{ 'p-error': errors['first_name'] }">Имя</label>
            <InputText
                id="first-name"
                v-model="lead.first_name"
                aria-describedby="first-name-error"
                autocomplete="given-name"
                :class="{ 'p-invalid': errors['first_name'] }"
                class="w-full"
            />
            <small class="p-error" id="first-name-error">{{ errors['first_name']?.[0] || '&nbsp;' }}</small>
        </div>

        <div class="space-y-1">
            <label for="last-name" :class="{ 'p-error': errors['last_name'] }">Фамилия</label>
            <InputText
                id="last-name"
                v-model="lead.last_name"
                aria-describedby="last-name-error"
                autocomplete="family-name"
                :class="{ 'p-invalid': errors['last_name'] }"
                class="w-full"
            />
            <small class="p-error" id="last-name-error">{{ errors['last_name']?.[0] || '&nbsp;' }}</small>
        </div>

        <div class="space-y-1">
            <label for="phone" :class="{ 'p-error': errors['phone'] }">Телефон</label>
            <InputMask
                id="phone"
                v-model="lead.phone"
                mask="+7 (999) 999-99-99"
                aria-describedby="phone-error"
                autocomplete="off"
                :class="{ 'p-invalid': errors['phone'] }"
                class="w-full"
            />
            <small class="p-error" id="phone-error">{{ errors['phone']?.[0] || '&nbsp;' }}</small>
        </div>

        <div class="space-y-1">
            <label for="email" :class="{ 'p-error': errors['email'] }">E-mail</label>
            <InputText
                id="email"
                v-model="lead.email"
                aria-describedby="email-error"
                autocomplete="email"
                :class="{ 'p-invalid': errors['email'] }"
                class="w-full"
            />
            <small class="p-error" id="email-error">{{ errors['email']?.[0] || '&nbsp;' }}</small>
        </div>

        <div class="space-y-1">
            <label for="appeal" :class="{ 'p-error': errors['appeal'] }">Текст обращения</label>
            <Textarea
                id="appeal"
                v-model="lead.appeal"
                rows="3"
                :class="{ 'p-invalid': errors['appeal'] }"
                aria-describedby="appeal-error"
                class="w-full"
            />
            <small class="p-error" id="appeal-error">{{ errors['appeal']?.[0] || '&nbsp;' }}</small>
        </div>

        <div class="flex justify-end gap-2">
            <Button label="Отмена" severity="secondary" @click="$emit('cancel')"></Button>
            <Button label="Сохранить" :loading="isProcessing" type="submit"></Button>
        </div>
    </form>
</template>

<style scoped>

</style>
