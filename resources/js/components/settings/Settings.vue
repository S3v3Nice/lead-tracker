<script setup lang="ts">
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import {computed, ref} from 'vue'
import Divider from 'primevue/divider'
import InputGroup from 'primevue/inputgroup'
import Message from 'primevue/message'
import {useAuthStore} from '@/stores/auth'
import axios, {type AxiosError} from 'axios'
import {useToast} from 'primevue/usetoast'
import {getErrorMessageByCode, ToastHelper} from '@/helpers'

const toastHelper = new ToastHelper(useToast())
const authStore = useAuthStore()

const usernameData = ref({
    username: authStore.username,
})
const usernameErrors = ref([])
const isChangingUsername = ref(false)
const isProcessingChangeUsername = ref(false)

const emailData = ref({
    email: authStore.email,
    email_confirmation: authStore.email,
})
const emailErrors = ref([])
const isEmailHidden = ref(true)
const isChangingEmail = ref(false)
const isProcessingChangeEmail = ref(false)
const isProcessingSendEmailVerificationLink = ref(false)

const passwordData = ref({
    password: '',
    password_confirmation: '',
})
const passwordErrors = ref([])
const isChangingPassword = ref(false)
const isProcessingChangePassword = ref(false)

const hiddenEmail = computed(() => {
    const email = authStore.email!
    const atIndex = email.indexOf('@')
    const usernamePart = email.substring(0, Math.min(2, atIndex))
    const domainPart = email.substring(atIndex)

    return usernamePart + '***' + domainPart
})

function toggleChangeUsername() {
    isChangingUsername.value = !isChangingUsername.value
    if (isChangingUsername.value) {
        usernameData.value.username = authStore.username
        usernameErrors.value = []
    }
}

function submitChangeUsername() {
    isProcessingChangeUsername.value = true
    usernameErrors.value = []

    axios.put('/api/settings/security/username', usernameData.value).then((response) => {
        if (response.data.success) {
            toggleChangeUsername()
            toastHelper.success('Имя пользователя изменено.')
            authStore.fetchUser()
        } else {
            if (response.data.errors) {
                usernameErrors.value = response.data.errors
            }
            if (response.data.message) {
                toastHelper.error(response.data.message)
            }
        }
    }).catch((error: AxiosError) => {
        toastHelper.error(getErrorMessageByCode(error.response!.status))
    }).finally(() => {
        isProcessingChangeUsername.value = false
    })
}

function toggleHideEmail() {
    isEmailHidden.value = !isEmailHidden.value
}

function toggleChangeEmail() {
    isChangingEmail.value = !isChangingEmail.value
    if (isChangingEmail.value) {
        emailData.value.email = authStore.email
        emailData.value.email_confirmation = authStore.email
        emailErrors.value = []
    }
}

function submitChangeEmail() {
    isProcessingChangeEmail.value = true
    emailErrors.value = []

    axios.put('/api/settings/security/email', emailData.value).then((response) => {
        if (response.data.success) {
            toggleChangeEmail()
            toastHelper.success('E-mail изменён. Подтвердите его, перейдя по ссылке из письма.')
            authStore.fetchUser()
        } else {
            if (response.data.errors) {
                emailErrors.value = response.data.errors
            }
            if (response.data.message) {
                toastHelper.error(response.data.message)
            }
        }
    }).catch((error: AxiosError) => {
        toastHelper.error(getErrorMessageByCode(error.response!.status))
    }).finally(() => {
        isProcessingChangeEmail.value = false
    })
}

function sendEmailVerificationLink() {
    isProcessingSendEmailVerificationLink.value = true

    axios.post('/api/settings/security/email-verification').then((response) => {
        if (response.data.success) {
            toastHelper.success('Ссылка для подтверждения E-mail отправлена.')
            authStore.fetchUser()
        } else {
            if (response.data.message) {
                toastHelper.error(response.data.message)
            }
        }
    }).catch((error: AxiosError) => {
        toastHelper.error(getErrorMessageByCode(error.response!.status))
    }).finally(() => {
        isProcessingSendEmailVerificationLink.value = false
    })
}

function toggleChangePassword() {
    isChangingPassword.value = !isChangingPassword.value
    if (!isChangingPassword.value) {
        passwordData.value.password = ''
        passwordData.value.password_confirmation = ''
        passwordErrors.value = []
    }
}

function submitChangePassword() {
    isProcessingChangePassword.value = true
    passwordErrors.value = []

    axios.put('/api/settings/security/password', passwordData.value).then((response) => {
        if (response.data.success) {
            toggleChangePassword()
            toastHelper.success('Пароль изменён.')
        } else {
            if (response.data.errors) {
                passwordErrors.value = response.data.errors
            }
            if (response.data.message) {
                toastHelper.error(response.data.message)
            }
        }
    }).catch((error: AxiosError) => {
        toastHelper.error(getErrorMessageByCode(error.response!.status))
    }).finally(() => {
        isProcessingChangePassword.value = false
    })
}
</script>

<template>
    <div class="surface-overlay rounded-xl border">
        <p class="text-3xl font-semibold p-4">Настройки</p>
    </div>
    <div class="p-4 surface-overlay rounded-xl border mt-3">
        <div class="max-w-[42rem]">
            <div v-if="!isChangingUsername" class="space-y-1">
                <label for="username">Имя пользователя</label>
                <InputGroup>
                    <InputText id="username" disabled :value="authStore.username" autocomplete="off"/>
                    <Button
                        title="Изменить"
                        icon="fa-solid fa-pen"
                        outlined
                        @click="toggleChangeUsername"
                    />
                </InputGroup>
            </div>
            <form v-else class="space-y-2">
                <div class="space-y-1">
                    <label for="username" :class="{ 'p-error': usernameErrors['username'] }">Новое имя пользователя</label>
                    <InputText
                        id="username"
                        v-model="usernameData.username"
                        class="w-full"
                        :class="{ 'p-invalid': usernameErrors['username'] }"
                        aria-describedby="username-error"
                        autocomplete="username"
                    />
                    <small class="p-error" id="username-error">{{ usernameErrors['username']?.[0] || '&nbsp;' }}</small>
                </div>

                <div class="pt-2 flex gap-2 xs:flex-row flex-col">
                    <Button
                        type="submit"
                        label="Сохранить"
                        icon="fa-solid fa-check"
                        :disabled="usernameData.username === authStore.username || !usernameData.username"
                        :loading="isProcessingChangeUsername"
                        @click.prevent="submitChangeUsername"
                    />
                    <Button
                        label="Отмена"
                        icon="fa-solid fa-xmark"
                        severity="secondary"
                        :disabled="isProcessingChangeUsername"
                        @click.prevent="toggleChangeUsername"
                    />
                </div>
            </form>

            <Divider/>

            <div v-if="!isChangingEmail" class="space-y-1">
                <label for="current_email">E-mail</label>
                <InputGroup>
                    <InputText id="current_email" disabled :value="isEmailHidden ? hiddenEmail : authStore.email"
                               autocomplete="off"/>
                    <Button
                        :title="isEmailHidden ? 'Показать' : 'Скрыть'"
                        :icon="isEmailHidden ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash'"
                        severity="secondary"
                        outlined
                        @click="toggleHideEmail"
                    />
                    <Button
                        title="Изменить"
                        icon="fa-solid fa-pen"
                        outlined
                        @click="toggleChangeEmail"
                    />
                </InputGroup>
            </div>
            <form v-else class="space-y-2">
                <div class="space-y-1">
                    <label for="email" :class="{ 'p-error': emailErrors['email'] }">Новый E-mail</label>
                    <InputText
                        id="email"
                        v-model="emailData.email"
                        class="w-full"
                        :class="{ 'p-invalid': emailErrors['email'] }"
                        aria-describedby="email-error"
                        autocomplete="email"
                    />
                    <small class="p-error" id="email-error">{{ emailErrors['email']?.[0] || '&nbsp;' }}</small>
                </div>

                <div class="space-y-1">
                    <label for="email_confirmation" :class="{ 'p-error': emailErrors['email_confirmation'] }">
                        Новый E-mail ещё раз
                    </label>
                    <InputText
                        id="email_confirmation"
                        v-model="emailData.email_confirmation"
                        class="w-full"
                        :class="{ 'p-invalid': emailErrors['email_confirmation'] }"
                        aria-describedby="email-error"
                        autocomplete="email"
                    />
                    <small class="p-error" id="email-error">
                        {{ emailErrors['email_confirmation']?.[0] || '&nbsp;' }}
                    </small>
                </div>

                <div class="pt-2 flex gap-2 xs:flex-row flex-col">
                    <Button
                        type="submit"
                        label="Сохранить"
                        icon="fa-solid fa-check"
                        :disabled="emailData.email === authStore.email || !emailData.email || !emailData.email_confirmation"
                        :loading="isProcessingChangeEmail"
                        @click.prevent="submitChangeEmail"
                    />
                    <Button
                        label="Отмена"
                        icon="fa-solid fa-xmark"
                        severity="secondary"
                        :disabled="isProcessingChangeEmail"
                        @click.prevent="toggleChangeEmail"
                    />
                </div>
            </form>

            <Message v-if="!authStore.hasVerifiedEmail" severity="warn" :closable="false">
                <p>Подтвердите E-mail адрес, перейдя по ссылке из письма.</p>
                <Button
                    outlined
                    severity="warning"
                    class="mt-2"
                    :loading="isProcessingSendEmailVerificationLink"
                    @click="sendEmailVerificationLink"
                >
                    Отправить ссылку повторно
                </Button>
            </Message>

            <Divider/>

            <div v-if="!isChangingPassword" class="space-y-1">
                <p>Пароль</p>
                <Button
                    type="submit"
                    label="Изменить пароль"
                    outlined
                    @click.prevent="toggleChangePassword"
                    class="xs:w-auto w-full"
                />
            </div>
            <form v-else class="space-y-2">
                <InputText name="username" hidden :value="authStore.username" autocomplete="username"/>

                <div class="space-y-1">
                    <label for="password" :class="{ 'p-error': passwordErrors['password'] }">Новый пароль</label>
                    <InputText
                        id="password"
                        v-model="passwordData.password"
                        class="w-full"
                        type="password"
                        :class="{ 'p-invalid': passwordErrors['password'] }"
                        aria-describedby="password-error"
                        autocomplete="new-password"
                    />
                    <small class="p-error" id="password-error">{{ passwordErrors['password']?.[0] || '&nbsp;' }}</small>
                </div>

                <div class="space-y-1">
                    <label for="password_confirmation" :class="{ 'p-error': passwordErrors['password_confirmation'] }">
                        Новый пароль ещё раз
                    </label>
                    <InputText
                        id="password_confirmation"
                        v-model="passwordData.password_confirmation"
                        class="w-full"
                        type="password"
                        :class="{ 'p-invalid': passwordErrors['password_confirmation'] }"
                        aria-describedby="password-error"
                        autocomplete="new-password"
                    />
                    <small class="p-error" id="password-error">
                        {{ passwordErrors['password_confirmation']?.[0] || '&nbsp;' }}
                    </small>
                </div>

                <div class="pt-2 flex gap-2 xs:flex-row flex-col">
                    <Button
                        type="submit"
                        label="Сохранить"
                        icon="fa-solid fa-check"
                        :disabled="!passwordData.password || !passwordData.password_confirmation"
                        :loading="isProcessingChangePassword"
                        @click.prevent="submitChangePassword"
                    />
                    <Button
                        label="Отмена"
                        icon="fa-solid fa-xmark"
                        severity="secondary"
                        :disabled="isProcessingChangePassword"
                        @click.prevent="toggleChangePassword"
                    />
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
</style>
