<script setup lang="ts">
import {useAuthStore} from '@/stores/auth'
import {computed, nextTick, ref} from 'vue'
import axios from 'axios'
import useThemeManager from '@/theme-manager'
import Button from 'primevue/button'
import UserAvatar from '@/components/user/UserAvatar.vue'
import Menu from 'primevue/menu'
import InputSwitch from 'primevue/inputswitch'
import {useRouter} from 'vue-router'
import type {MenuItem} from 'primevue/menuitem'
import {useGlobalModalStore} from '@/stores/globalModal'

const router = useRouter()
const authStore = useAuthStore()
const globalModalStore = useGlobalModalStore()
const themeManager = useThemeManager()

const userMenu = ref<Menu>()
const userMenuItems = computed<MenuItem[]>(() => [
    {
        separator: true,
        visible: authStore.isAuthenticated,
    },
    {
        label: 'Настройки',
        icon: 'fa-solid fa-gear',
        visible: authStore.isAuthenticated,
        route: 'settings',
    },
    {
        separator: true,
        visible: authStore.isAuthenticated,
    },
    {
        label: 'Тёмная тема',
        icon: 'fa-regular fa-moon',
        switchValue: !themeManager.isLight(),
        command: () => themeManager.toggleTheme(),
    },
    {
        separator: true,
    },
    {
        label: 'Войти',
        icon: 'fa-solid fa-arrow-right-to-bracket',
        visible: !authStore.isAuthenticated,
        command: () => globalModalStore.isAuth = true,
    },
    {
        label: 'Выйти',
        icon: 'fa-solid fa-arrow-right-from-bracket',
        visible: authStore.isAuthenticated,
        command: logout,
    },
])

function toggleUserMenu(event: Event) {
    userMenu.value!.toggle(event)
}

function invokeUserMenuCommand(item: MenuItem) {
    if (item.command) {
        item.command(null!)
    }

    const shouldHideMenu = item.switchValue === undefined

    if (shouldHideMenu) {
        userMenu.value!.hide()
    }
}

function logout() {
    axios.post('/api/auth/logout').then(() => {
        router.go(0)
    })
}
</script>

<template>
    <div class="fixed w-full top-0 left-0 z-[1000] flex flex-col">
        <div class="h-[var(--header-height)] surface-overlay p-2 lg:px-0 border-b flex items-center">
            <div class="page-container flex gap-2 h-full items-center">
                <RouterLink :to="{name: 'home'}" class="h-[70%] sm:h-[90%]">
                    <img v-if="themeManager.isLight()" src="/images/logo.svg" alt="Logo" class="h-full">
                    <img v-else src="/images/logo-dark.svg" alt="Logo" class="h-full">
                </RouterLink>

                <div class="flex ml-auto gap-4 items-center">
                    <Button
                        v-if="!authStore.isAuthenticated"
                        icon="fa-regular fa-user"
                        @click="toggleUserMenu"
                        aria-haspopup="true"
                        aria-controls="user-menu"
                        aria-label="Меню пользователя"
                        class="mr-2 md:mr-0"
                    />
                    <Button
                        v-else
                        unstyled
                        @click="toggleUserMenu"
                        aria-haspopup="true"
                        aria-controls="user-menu"
                        aria-label="Меню пользователя"
                        class="mr-2 md:mr-0"
                    >
                        <UserAvatar :user="authStore.user"/>
                    </Button>
                </div>
            </div>

            <Menu
                ref="userMenu"
                id="user-menu"
                :model="userMenuItems"
                :popup="true"
                @focus="() => nextTick(() => (userMenu!['focusedOptionIndex'] = -1))"
            >
                <template v-if="authStore.isAuthenticated" #start>
                    <div class="flex p-2 gap-2 items-center">
                        <UserAvatar :user="authStore.user" size="large"/>
                        <p>{{ authStore.username }}</p>
                    </div>
                </template>
                <template #item="{ item, props }">
                    <Component
                        :is="item.route ? 'RouterLink' : 'a'"
                        :to="{ name: item.route }"
                        class="flex space-x-5 justify-between"
                        v-bind="props.action"
                        @click.stop="invokeUserMenuCommand(item)"
                    >
                        <div>
                            <span class="w-[20px]" :class="item.icon"/>
                            <span class="ml-2">{{ item.label }}</span>
                        </div>
                        <InputSwitch v-if="item.switchValue != undefined" :model-value="item.switchValue"></InputSwitch>
                    </Component>
                </template>
            </Menu>
        </div>
    </div>
</template>

<style scoped>

</style>
