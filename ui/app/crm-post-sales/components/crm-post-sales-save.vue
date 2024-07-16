<template>
    <v-dialog
        v-model="dialog"
        scrollable
        :overlay="false"
        max-width="500px"
        transition="dialog-transition"
    >
        <v-card>
            
            <v-card-title primary-title class="cyan white--text pt-3">
                <h3 v-show="save_step==0">SIMPAN PROFIL</h3>
                <h3 v-show="save_step==1">SIMPAN PROFIL {{ save_to == 1 ? selected_profile.profile_name : "BARU" }}</h3>
            </v-card-title>

            <v-card-text v-show="save_step==0">
                <v-btn color="primary" class="py-2" block @click="save_step=1;save_to=1" v-if="!!selected_profile"><span style="white-space: normal;">SIMPAN PROFIL : {{ selected_profile.profile_name }}</span></v-btn>
                <v-btn color="success" block @click="save_step=1;save_to=0">SIMPAN SEBAGAI PROFIL BARU</v-btn>
            </v-card-text>

            <v-card-text>
                <v-card v-if="!!selected_profile" class="mb-1" v-show="save_step==1&&save_to==1" flat>
                    <v-card-text class="pa-0">
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-layout row wrap>
                                    <v-flex xs12>
                                        <v-text-field
                                            label="Profil Terpilih"
                                            :value="selected_profile.profile_name"
                                            readonly
                                        ></v-text-field>
                                    </v-flex>
                                </v-layout>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                </v-card>

                <v-card v-show="save_step==1&&save_to==0" flat>
                    <v-card-text class="pa-0">
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-layout row wrap>
                                    <v-flex xs12>
                                        <v-text-field
                                            label="Nama Profil"
                                            v-model="profile_name"
                                        ></v-text-field>
                                    </v-flex>

                                    <v-flex xs12>
                                        <v-textarea
                                            label="Data JSON"
                                            v-model="profile_json"
                                            row="3"
                                            readonly
                                            disabled
                                        ></v-textarea>
                                    </v-flex>

                                </v-layout>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                </v-card>
                
            </v-card-text>

            <v-card-actions>
                <v-btn color="primary" flat @click="dialog=!dialog">Batal</v-btn>
                <v-spacer></v-spacer>
                <v-btn color="primary" @click="save">Simpan</v-btn>                
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<style>
.input-dense .v-input__control {
    min-height: 36px !important;
}
</style>
<script>
module.exports = {
    components : {
        'common-datepicker' : httpVueLoader('../../common/components/common-datepicker.vue')
    },

    data () {
        return { 
            tab_titles: [
                'Filter', 'Profil'
            ]
        }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.post_sales.dialog_new },
            set (v) { this.$store.commit('post_sales/set_common', ['dialog_new', v]) }
        },

        profile_name : {
            get () { return this.$store.state.post_sales.profile_name },
            set (v) { this.$store.commit('post_sales/set_common', ['profile_name', v]) }
        },

        profile_json : {
            get () { return this.$store.state.post_sales.profile_json },
            set (v) { this.$store.commit('post_sales/set_common', ['profile_json', v]) }
        },

        selected_profile () { return this.$store.state.post_sales.selected_profile },
        
        save_step : {
            get () { return this.$store.state.post_sales.save_step },
            set (v) { this.$store.commit('post_sales/set_common', ['save_step', v]) }
        },

        save_to : { 
            get () { return this.$store.state.post_sales.save_to },
            set (v) {
                this.$store.commit('post_sales/set_common', ['save_to', v]) 
            }
         }
    },

    methods : {
        save () {
            this.$store.dispatch('post_sales/save')
        }
    },

    mounted () {},

    watch : {}
}
</script>