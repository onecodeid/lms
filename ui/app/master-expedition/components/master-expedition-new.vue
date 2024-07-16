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
                <h3 v-show="!edit">INPUT EKSPEDISI BARU</h3>
                <h3 v-show="edit">UBAH DATA EKSPEDISI</h3>
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs12>
                        <v-layout row wrap>

                            <v-flex xs12>
                                <v-text-field
                                    label="Kode Ekspedisi"
                                    v-model="expedition_code"
                                    disabled
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12>
                                <v-text-field
                                    label="Nama Ekspedisi"
                                    v-model="expedition_name"
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs6>
                                <v-checkbox label="Layanan COD ?" v-model="expedition_cod" true-value="Y" false-value="N"></v-checkbox>
                            </v-flex>

                            <v-flex xs6>
                                <v-text-field
                                    label="COD Rate"
                                    suffix="%"
                                    v-model="expedition_cod_rate"
                                    :disabled="expedition_cod!='Y'"
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12>
                                <v-select
                                    :items="cod_rate_types"
                                    v-model="selected_cod_rate_type"
                                    item-value="id"
                                    item-text="text"
                                    return-object
                                    label="Perhitungan COD Rate"
                                    :disabled="expedition_cod!='Y'"
                                ></v-select>
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
        'common-datepicker' : httpVueLoader('../../common/components/common-datepicker.vue')
    },

    data () {
        return { }
    },

    computed : {
        edit () {
            return this.$store.state.expedition_new.edit
        },

        dialog : {
            get () { return this.$store.state.expedition_new.dialog_new },
            set (v) { this.$store.commit('expedition_new/set_common', ['dialog_new', v]) }
        },

        expedition_name : {
            get () { return this.$store.state.expedition_new.expedition_name },
            set (v) { this.$store.commit('expedition_new/set_common', ['expedition_name', v]) }
        },

        expedition_code : {
            get () { return this.$store.state.expedition_new.expedition_code },
            set (v) { this.$store.commit('expedition_new/set_common', ['expedition_code', v]) }
        },

        expedition_cod : {
            get () { return this.$store.state.expedition_new.expedition_cod },
            set (v) { this.$store.commit('expedition_new/set_common', ['expedition_cod', v]) }
        },

        expedition_cod_rate : {
            get () { return this.$store.state.expedition_new.expedition_cod_rate },
            set (v) { this.$store.commit('expedition_new/set_common', ['expedition_cod_rate', v]) }
        },

        cod_rate_types () {
            return this.$store.state.expedition_new.cod_rate_types
        },

        selected_cod_rate_type : {
            get () { return this.$store.state.expedition_new.selected_cod_rate_type },
            set (v) { this.$store.commit('expedition_new/set_selected_cod_rate_type', v) }
        }
    },

    methods : {
        save () {
            this.$store.dispatch('expedition_new/save')
        }
    },

    mounted () {},

    watch : {}
}
</script>