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
                <h3>AKUN BANK BARU</h3>
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs12>
                        <v-layout row wrap>

                            <v-flex xs12>
                                <v-autocomplete
                                    :items="banks"
                                    item-text="M_BankName"
                                    item-value="M_BankID"
                                    return-object
                                    v-model="selected_bank"
                                    label="Bank Pengirim"
                                    placeholder="-- Pilih --"
                                ></v-autocomplete>
                            </v-flex>
                            <v-flex xs12>
                                <v-text-field
                                    label="Nomor Rekening"
                                    v-model="bankaccount_number"
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12>
                                <v-text-field
                                    label="Nama Rekening"
                                    v-model="bankaccount_name"
                                ></v-text-field>
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
        dialog : {
            get () { return this.$store.state.bankaccount_new.dialog_new },
            set (v) { this.$store.commit('bankaccount_new/set_common', ['dialog_new', v]) }
        },

        bankaccount_name : {
            get () { return this.$store.state.bankaccount_new.bankaccount_name },
            set (v) { this.$store.commit('bankaccount_new/set_common', ['bankaccount_name', v]) }
        },

        bankaccount_number : {
            get () { return this.$store.state.bankaccount_new.bankaccount_number },
            set (v) { this.$store.commit('bankaccount_new/set_common', ['bankaccount_number', v]) }
        },

        banks () {
            return this.$store.state.bankaccount_new.banks
        },

        selected_bank : {
            get () { return this.$store.state.bankaccount_new.selected_bank },
            set (v) { this.$store.commit('bankaccount_new/set_selected_bank', v) }
        }
    },

    methods : {
        save () {
            this.$store.dispatch('bankaccount_new/save')
        }
    },

    mounted () {},

    watch : {}
}
</script>