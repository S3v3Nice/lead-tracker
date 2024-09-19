import {defineStore} from 'pinia'

export const useGlobalModalStore = defineStore('global-modal', {
    state: () => ({
        isAuth: false,
    })
})
