<template>
    <div>
        <v-navigation-drawer
            v-model="drawer"
            fixed app
            temporary
            :value="false"
            dark
            style='background-image: linear-gradient(rgba(196, 33, 131, 0.8), rgba(196, 33, 131, 0.8)); color:white;'   
        >
            <v-list>
                <!-- LEVEL 1 -->
                <template v-for="(i, n) in menus">
                    <v-list-tile v-if="typeof(i) == 'string'" v-bind:key="n" @click="goto(i)">
                        <v-list-tile-action>
                            <v-icon>{{ icon_me(n) }}</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-title>{{ n }}</v-list-tile-title>
                        <v-list-tile-action>
                            <v-spacer></v-spacer>
                            <v-btn
                            icon
                            @click.stop="mini = !mini"
                            >
                            <!-- <v-icon>chevron_left</v-icon> -->
                            </v-btn>
                        </v-list-tile-action>
                    </v-list-tile>

                    <v-list-group
                        no-action
                        :prepend-icon="icon_me(n)"
                        :value="c"
                        v-bind:key="n"
                        v-if="typeof(i) != 'string'"
                        class="white--text"
                    >
                        <template v-slot:activator>
                            <v-list-tile>
                                <v-list-tile-title class="white--text">{{ n }}</v-list-tile-title>
                            </v-list-tile>
                        </template>

                        <template v-for="(ii, nn) in i">
                            <v-list-tile v-bind:key="nn" @click="goto(ii)"
                            >
                                <v-list-tile-title v-text="nn" class="white--text"></v-list-tile-title>
                                    <!-- <v-list-tile-action>
                                    <v-icon v-text="crud[1]"></v-icon>
                                </v-list-tile-action> -->
                            </v-list-tile>

                            <!-- <v-list-tile-title v-text="nn" v-if="typeof(ii) == 'string'" v-bind:key="nn">
                                
                                <v-list-tile-title>{{ nn }}</v-list-tile-title>
                                
                            </v-list-tile-title> -->
                        </template>
                    </v-list-group>
                </template>
                <!--/ LEVEL 1 -->            

            </v-list>
        </v-navigation-drawer>

        <common-notification></common-notification>
    <div>
</template>

<script>
module.exports = {
    components : {
        "common-notification" : httpVueLoader("./common-notification.vue")
    },

    data () {
        return {
            items: [
            { title: 'Home', icon: 'dashboard' },
            { title: 'About', icon: 'question_answer' }
            ],
            mini: true,
            right: null,

            admins: [
                ['Management', 'people_outline'],
                ['Settings', 'settings']
            ],
            cruds: [
                ['Create', 'add'],
                ['Read', 'insert_drive_file'],
                ['Update', 'update'],
                ['Delete', 'delete']
            ],
            collapse: true
        }
    },

    computed : {
        c () {
            console.log(`c : ${!this.mini}`)
            return false
            return !this.mini
        },

        menus () {
            return this.$store.state.system.menus
        },

        drawer : {
            get () { return this.$store.state.system.drawer },
            set (v) { this.$store.commit('system/set_drawer', v) }
        }
    },

    methods : {
        icon_me (x) {
            let icons = this.$store.state.system.icons
            if (icons[x])
                return icons[x]
            return ''
        },

        goto (x) {
            // alert(this.mini)
            // if (this.mini)
            //     this.mini = false
            // else
            let URL = this.$store.state.system.URL
                window.location = URL + '../ui/app/' + x
        }
    },

    watch : {
        mini (v, o) {
            console.log(v)
            this.collapse = true
            console.log(this.collapse)
        }
    }
}
</script>