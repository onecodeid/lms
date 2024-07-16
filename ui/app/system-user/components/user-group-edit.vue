<template>
    <v-dialog
        v-model="dialog"
        max-width="1000px"
        transition="dialog-transition"
        scrollable
    >
        <v-card>
            <v-card-title primary-title class="blue white--text">
                <h3 class="headline">GROUP SETTING</h3>
                <v-spacer></v-spacer>
                <v-icon large class="white--text">group</v-icon>
            </v-card-title>

            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs4>
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-text-field
                                    label="Nama Group"
                                    :value="selected_group?selected_group.group_name:''"
                                    readonly
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12>
                                <v-text-field
                                    label="Kode Group"
                                    :value="selected_group?selected_group.group_code:''"
                                    readonly
                                ></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-flex>
                    <v-flex xs8 pl-2>
                        <v-layout row wrap>
                            <template v-for="(m, i) in menus[0]">
                                <v-flex xs12 v-bind:key="i" class="mb-2 pr-1 pl-1">
                                    <v-card>
                                        <v-card-title primary-title class="primary white--text pt-2 pb-1">
                                            {{ i }}
                                        </v-card-title>
                                        <v-card-text v-if="typeof(m)!='string'" class="pa-2 pt-0">
                                            <v-layout row wrap>
                                                <v-flex xs6 v-for="(mm, ii) in m" v-bind:key="ii">
                                                    <v-checkbox :label="ii" :value="Math.round(mm)" v-model="privileges" hide-details class="mt-2"></v-checkbox>
                                                </v-flex>
                                            </v-layout>
                                        </v-card-text>

                                        <v-card-text v-if="typeof(m)=='string'" class="pa-2 pt-0">
                                            <v-checkbox :label="i" :value="Math.round(m)" hide-details class="mt-2" v-model="privileges"></v-checkbox>
                                        </v-card-text>
                                    </v-card>  
                                </v-flex>
                                  
                            </template>
                        </v-layout>
                        
                        
                    </v-flex>
                    
                </v-layout>
            </v-card-text>

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="primary" @click="dialog=!dialog" flat>Tutup</v-btn>
                
                <v-btn color="primary" @click="save">Simpan</v-btn>
            </v-card-actions>
        </v-card>

    </v-dialog>
</template>

<script>
module.exports = {
    computed : {
        dialog : {
            get () { return this.$store.state.user.dialog_group },
            set (v) { this.$store.commit('user/set_common', ['dialog_group', v]) }
        },

        selected_group () {
            return this.$store.state.user.selected_group
        },

        menus () {
            return this.$store.state.user.menus
        },

        privileges : {
            get () { return this.$store.state.user.privileges },
            set (v) { this.$store.commit('user/set_privileges',v) }
        }
    },

    methods : {
        save () {
            this.$store.dispatch('user/save')
        }
    },

    mounted () {
        this.$store.dispatch('user/search_menus')
    }
}
</script>