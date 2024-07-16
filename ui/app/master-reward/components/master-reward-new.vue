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
                <h3>EXPENSE BARU</h3>
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs12>
                        <v-layout row wrap>

                            <v-flex xs12>
                                <v-text-field
                                    label="Nama Reward"
                                    v-model="reward_name"
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs6 pr-4>
                                <v-layout row wrap>
                                    <v-flex xs12>
                                        <v-text-field
                                            label="Kode Reward"
                                            v-model="reward_code"
                                        ></v-text-field>
                                    </v-flex>

                                    

                                    <v-flex xs12>
                                        <v-text-field
                                            label="Point Reward"
                                            v-model="reward_point"
                                            type="number"
                                        ></v-text-field>
                                    </v-flex>

                                    <v-flex xs12>
                                        <v-text-field
                                            label="Kuota"
                                            v-model="reward_quota"
                                            type="number"
                                        ></v-text-field>
                                    </v-flex>
                                </v-layout>
                            </v-flex>

                            <v-flex xs6>
                                <reward-image></reward-image>
                            </v-flex>
                            

                        </v-layout>
                    </v-flex>
                </v-layout>
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
        'common-datepicker' : httpVueLoader('../../common/components/common-datepicker.vue'),
        'reward-image' : httpVueLoader('./master-reward-image.vue')
    },

    data () {
        return { }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.reward_new.dialog_new },
            set (v) { this.$store.commit('reward_new/set_common', ['dialog_new', v]) }
        },

        reward_name : {
            get () { return this.$store.state.reward_new.reward_name },
            set (v) { this.$store.commit('reward_new/set_common', ['reward_name', v]) }
        },

        reward_code : {
            get () { return this.$store.state.reward_new.reward_code },
            set (v) { this.$store.commit('reward_new/set_common', ['reward_code', v]) }
        },

        reward_point : {
            get () { return this.$store.state.reward_new.reward_point },
            set (v) { this.$store.commit('reward_new/set_common', ['reward_point', v]) }
        },

        reward_quota : {
            get () { return this.$store.state.reward_new.reward_quota },
            set (v) { this.$store.commit('reward_new/set_common', ['reward_quota', v]) }
        }
    },

    methods : {
        save () {
            this.$store.dispatch('reward_new/save')
        }
    },

    mounted () {},

    watch : {}
}
</script>