<script setup lang="ts">
import InputText from 'primevue/inputtext'
import InputMask from 'primevue/inputmask'
import Textarea from 'primevue/textarea'
import Button from 'primevue/button'
import {ref} from 'vue'
import axios, {type AxiosError} from 'axios'
import {useToast} from 'primevue/usetoast'
import {getErrorMessageByCode, ToastHelper} from '@/helpers'

const toastHelper = new ToastHelper(useToast())
const isProcessing = ref(false)
const errors = ref([])
const leadAppealData = ref({
    first_name: '',
    last_name: '',
    phone: '',
    email: '',
    appeal: '',
})

function submit() {
    isProcessing.value = true
    errors.value = []

    axios.post('/api/leads', leadAppealData.value).then((response) => {
        if (response.data.success) {
            toastHelper.success('Ваше обращение отправлено и будет рассмотрено в ближайшее время!')
            leadAppealData.value = {}
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
    <form class="space-y-3">
        <div class="space-y-1">
            <label for="first-name" :class="{ 'p-error': errors['first_name'] }">Имя</label>
            <InputText
                id="first-name"
                v-model="leadAppealData.first_name"
                :class="{ 'p-invalid': errors['first_name'] }"
                aria-describedby="first-name-error"
                autocomplete="given-name"
                class="w-full"
            />
            <small class="p-error" id="first-name-error">{{ errors['first_name']?.[0] || '&nbsp;' }}</small>
        </div>

        <div class="space-y-1">
            <label for="last-name" :class="{ 'p-error': errors['last_name'] }">Фамилия</label>
            <InputText
                id="last-name"
                v-model="leadAppealData.last_name"
                :class="{ 'p-invalid': errors['last_name'] }"
                aria-describedby="last-name-error"
                autocomplete="family-name"
                class="w-full"
            />
            <small class="p-error" id="last-name-error">{{ errors['last_name']?.[0] || '&nbsp;' }}</small>
        </div>

        <div class="space-y-1">
            <label for="phone" :class="{ 'p-error': errors['phone'] }">Телефон</label>
            <InputMask
                id="phone"
                v-model="leadAppealData.phone"
                mask="+7 (999) 999-99-99"
                aria-describedby="phone-error"
                :class="{ 'p-invalid': errors['phone'] }"
                class="w-full"
            />
            <small class="p-error" id="phone-error">{{ errors['phone']?.[0] || '&nbsp;' }}</small>
        </div>

        <div class="space-y-1">
            <label for="email" :class="{ 'p-error': errors['email'] }">E-mail</label>
            <InputText
                id="email"
                v-model="leadAppealData.email"
                :class="{ 'p-invalid': errors['email'] }"
                aria-describedby="email-error"
                autocomplete="email"
                class="w-full"
            />
            <small class="p-error" id="email-error">{{ errors['email']?.[0] || '&nbsp;' }}</small>
        </div>

        <div class="space-y-1">
            <label for="appeal" :class="{ 'p-error': errors['appeal'] }">Текст обращения</label>
            <Textarea
                id="appeal"
                v-model="leadAppealData.appeal"
                rows="3"
                :class="{ 'p-invalid': errors['appeal'] }"
                aria-describedby="appeal-error"
                class="w-full"
            />
            <small class="p-error" id="appeal-error">{{ errors['appeal']?.[0] || '&nbsp;' }}</small>
        </div>

        <div class="mt-2 space-y-2">
            <Button type="submit" label="Отправить" :loading="isProcessing" @click.prevent="submit" class="w-full"/>
        </div>
    </form>
</template>

<style scoped>

</style>
