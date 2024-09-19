import type {RouteRecordRaw} from 'vue-router'
import type {Component} from 'vue'

import Home from '@/components/Home.vue'
import PasswordReset from '@/components/auth/PasswordReset.vue'
import Settings from '@/components/settings/Settings.vue'
import NotFound from '@/components/NotFound.vue'
import EmailVerification from '@/components/auth/EmailVerification.vue'

declare module 'vue-router' {
    interface RouteMeta {
        title?: string
        requiresAuth?: boolean
        requiresModerator?: boolean
        requiresAdmin?: boolean
        defaultComponent?: Component
    }
}

const routes: RouteRecordRaw[] = [
    {
        path: '/',
        name: 'home',
        component: Home,
        meta: {
            title: 'Добро пожаловать'
        }
    },
    {
        path: '/email/verify/:id/:email/:hash',
        name: 'verify-email',
        props: ({params}) => ({email: params.email}),
        component: EmailVerification,
        meta: {
            title: 'Подтверждение E-mail',
        },
    },
    {
        path: '/password/reset',
        name: 'password-reset',
        component: PasswordReset,
        meta: {
            title: 'Сброс пароля',
        },
    },
    {
        path: '/settings',
        name: 'settings',
        component: Settings,
        meta: {
            title: 'Настройки',
            requiresAuth: true,
        },
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        component: NotFound,
        meta: {
            title: 'Не найдено'
        }
    }
]

export default routes
