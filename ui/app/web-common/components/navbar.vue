<template>
    <div>
        <v-navigation-drawer v-model="sidebar" app>
            <v-list>
                <v-list-tile v-for="item in menuItems" :key="item.title" :to="item.path">
                    <v-list-tile-action>
                        <v-icon>{{ item.icon }}</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-content>{{ item.title }}</v-list-tile-content>
                </v-list-tile>
            </v-list>
        </v-navigation-drawer>

        <v-toolbar app>
            <span class="hidden-sm-and-up">
                <v-toolbar-side-icon @click="sidebar = !sidebar">
                </v-toolbar-side-icon>
            </span>
            <v-toolbar-title>
                <router-link to="/" tag="span" style="cursor: pointer">
                    {{ appTitle }}
                </router-link>
            </v-toolbar-title>
            <v-spacer></v-spacer>
            <v-toolbar-items class="hidden-xs-only">
                <v-btn flat v-for="item in menuItems" :key="item.title"  @click="goTo(item.path, item.blank??null)">
                    <v-icon left dark>{{ item.icon }}</v-icon>
                    {{ item.title }}
                </v-btn>
            </v-toolbar-items>
        </v-toolbar>
    </div>
</template>

<style scoped>
header.v-toolbar {
    position: fixed;
    z-index: 2;
    width: 100%;
}
</style>

<script>
module.exports = {
    data () {
        return {
            appTitle: 'LPK Global',
            sidebar: false,
            menuItems: [
                { title: 'Home', path: '/web-home', icon: 'home' },
                { title: 'Pendaftaran', path: '/web-register', icon: 'face' },
                { title: 'Konfirmasi', path: '/web-payment-confirmation', icon: 'money' },
                { title: 'Log In', path: '/system-login', icon: 'lock_open', blank: true }
            ]
        }
    },

    methods : {
        goTo(x, y) {
            if (!y)
                location.href = "../"+x
            else
                window.open("../"+x, '_blank')
        }
    }
}
</script>