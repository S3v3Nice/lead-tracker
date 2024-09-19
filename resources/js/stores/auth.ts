import axios from 'axios'
import {defineStore} from 'pinia'
import {type User} from '@/types'

interface AuthUser {
    isAuthenticated: boolean
    isFetched: boolean
    user: User | null
}

export const useAuthStore = defineStore('auth', {
    state: (): AuthUser => ({
        isAuthenticated: false,
        isFetched: false,
        user: null,
    }),
    getters: {
        id: (state) => state.user?.id,
        username: (state) => state.user?.username,
        email: (state) => state.user?.email,
        emailVerifiedAt: (state) => state.user?.email_verified_at,
        createdAt: (state) => state.user?.created_at,
        updatedAt: (state) => state.user?.updated_at,
        hasVerifiedEmail: (state) => state.user?.email_verified_at !== null,
    },
    actions: {
        async fetchUser() {
            await axios.get('/api/auth/user').then(({data}) => {
                if (Object.keys(data).length === 0) {
                    this.reset()
                    return
                }

                this.isAuthenticated = true
                this.isFetched = true
                this.user = data
            }).catch(() => {
                this.reset()
            })
        },

        reset() {
            this.isAuthenticated = false
            this.user = null
        }
    }
})
