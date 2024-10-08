<script setup lang="ts">
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import {ref} from 'vue'
import axios, {type AxiosError} from 'axios'
import {useToast} from 'primevue/usetoast'
import {useRouter} from 'vue-router'
import {getErrorMessageByCode, ToastHelper} from '@/helpers'

defineEmits(['switch-to-login'])

const router = useRouter()
const toastHelper = new ToastHelper(useToast())
const isProcessing = ref(false)
const errors = ref([])
const registerData = ref({
    username: '',
    email: '',
    password: '',
    password_confirmation: ''
})

function submitRegister() {
    isProcessing.value = true
    errors.value = []

    axios.post('/api/auth/register', registerData.value).then((response) => {
        if (response.data.success) {
            router.go(0)
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
            <label for="username" :class="{ 'p-error': errors['username'] }">Имя пользователя</label>
            <InputText
                id="username"
                v-model="registerData.username"
                class="w-full"
                :class="{ 'p-invalid': errors['username'] }"
                aria-describedby="username-error"
                autocomplete="username"
            />
            <small class="p-error" id="username-error">{{ errors['username']?.[0] || '&nbsp;' }}</small>
        </div>

        <div class="space-y-1">
            <label for="email" :class="{ 'p-error': errors['email'] }">E-mail</label>
            <InputText
                id="email"
                v-model="registerData.email"
                class="w-full"
                :class="{ 'p-invalid': errors['email'] }"
                aria-describedby="email-error"
                autocomplete="email"
            />
            <small class="p-error" id="email-error">{{ errors['email']?.[0] || '&nbsp;' }}</small>
        </div>

        <div class="space-y-1">
            <label for="password" :class="{ 'p-error': errors['password'] }">Пароль</label>
            <InputText
                id="password"
                v-model="registerData.password"
                type="password"
                class="w-full"
                :class="{ 'p-invalid': errors['password'] }"
                aria-describedby="password-error"
                autocomplete="new-password"
            />
            <small class="p-error" id="password-error">{{ errors['password']?.[0] || '&nbsp;' }}</small>
        </div>

        <div class="space-y-1">
            <label for="password-confirmation" :class="{ 'p-error': errors['password_confirmation'] }">
                Пароль ещё раз
            </label>
            <InputText
                id="password-confirmation"
                v-model="registerData.password_confirmation"
                type="password"
                class="w-full"
                :class="{ 'p-invalid': errors['password_confirmation'] }"
                aria-describedby="password-confirmation-error"
                autocomplete="new-password"
            />
            <small class="p-error" id="password-confirmation-error">
                {{ errors['password_confirmation']?.[0] || '&nbsp;' }}
            </small>
        </div>

        <div class="pt-4 space-y-2">
            <Button
                type="submit"
                label="Зарегистрироваться"
                class="w-full"
                :loading="isProcessing"
                @click.prevent="submitRegister"
            />
            <Button label="Уже есть учетная запись?" class="w-full p-button-link" @click="$emit('switch-to-login')"/>
        </div>
    </form>
</template>

<style scoped>

</style>
