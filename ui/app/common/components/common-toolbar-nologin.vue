<template>
  <v-toolbar dark class="zalfa-bg-pink-01">

    <v-toolbar-title class="white--text text-center">Zalfa Miracle</v-toolbar-title>

    <v-spacer></v-spacer>

    
    <!-- <h3 class="mr-2">{{ user.user_name }}</h3> -->
    

    

  </v-toolbar>
</template>

<style>
    .profile-menu .v-list__tile {
        font-size: 13px;
        height: 40px;
        padding-left: 10px;
    }

    .profile-menu .v-list__tile__action {
        min-width: 25px;
    }

    .profile-menu .v-list__tile__action .v-icon {
        font-size: 18px;
    }

    #oneApp > .v-menu__content {
        min-width: 220px !important;
    }
</style>
<style scoped>
    .v-badge__badge {
        font-size: 12px;
        top: -3px;
        right: -3px;
        height: 18px;
        width: 18px;
    }

    #btn_play {
        width: 0px;
        height: 0px;
    }

    .zalfa-btn-notification {
        background: rgba(200,200,200,0.2) !important;
    }
</style>
<script>
module.exports = {
    data () {
        return {
            drawer_notification: false,
            user: this.$store.state.system.user
        }
    },

    computed : {
        drawer : {
            get () { return this.$store.state.system.drawer },
            set (v) { this.$store.commit('system/set_drawer', v) }
        },

        notif_unread () {
            return this.$store.state.system.notif_unread
        },

        notif_total () {
            return this.$store.state.system.notif_total
        },

        notif_messages () {
            return this.$store.state.system.notif_messages
        },

        notif_md5 () {
            return this.$store.state.system.notif_md5
        },

        sound () {
            return this.$store.state.system.sound
        }
    },

    methods : {
        change_password () {
            window.location.replace('../system-profile/?tab=2')
        },

        profile () {
            window.location.replace('../system-profile/')
        },

        logout () {
            this.$store.dispatch('system/do_logout')
        },

        notif () {
            this.$store.commit('system/set_drawer_notif', true)
        },

        playme () {
            // this.$refs.alarm.play()
        }
    },

    mounted () {
        this.$store.dispatch('system/get_notif_unread')
    },

    watch : {
        notif_md5 (v, o) {
            console.log(`md5: ${v}`)
            if (v!=o && v!="")
                this.$refs.btn_play.$el.click()
        }
    }
}
</script>