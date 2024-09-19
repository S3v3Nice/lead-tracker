import {reactive} from 'vue'

class ThemeManager {
    private static readonly themeLinkElementId = 'theme-link'
    private static readonly themeKey = 'theme'
    private static readonly lightTheme = 'light'
    private static readonly darkTheme = 'dark'

    private theme = ThemeManager.lightTheme

    public load() {
        const theme = localStorage.getItem(ThemeManager.themeKey)

        if (theme != null && theme !== this.theme) {
            this.changeTheme(theme)
        }
    }

    public toggleTheme() {
        const newTheme = this.theme === ThemeManager.lightTheme ? ThemeManager.darkTheme : ThemeManager.lightTheme

        this.changeTheme(newTheme)
        localStorage.setItem(ThemeManager.themeKey, newTheme)
    }

    public isLight() {
        return this.theme === ThemeManager.lightTheme
    }

    private changeTheme(newTheme: string) {

        if (this.theme !== newTheme) {
            const linkElement = document.getElementById(ThemeManager.themeLinkElementId)

            if (linkElement === null) {
                return
            }

            const newThemeUrl = linkElement.getAttribute('href')!.replace(this.theme, newTheme)

            const cloneLinkElement = document.createElement('link')
            cloneLinkElement.setAttribute('id', ThemeManager.themeLinkElementId + '-clone')
            cloneLinkElement.setAttribute('rel', 'stylesheet')
            cloneLinkElement.setAttribute('href', newThemeUrl)
            cloneLinkElement.addEventListener('load', () => {
                linkElement?.remove()
                cloneLinkElement.setAttribute('id', ThemeManager.themeLinkElementId);
                this.theme = newTheme
            })

            linkElement.parentNode?.insertBefore(cloneLinkElement, linkElement.nextSibling)
        }
    }
}

const themeManager = reactive(new ThemeManager())

export default function useThemeManager() {
    return themeManager
}
