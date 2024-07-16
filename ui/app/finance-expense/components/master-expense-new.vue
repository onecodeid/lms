<template>
    <v-dialog
        v-model="dialog"
        scrollable
        :overlay="false"
        max-width="300px"
        transition="dialog-transition"
    >
        <v-card>
            <v-card-title primary-title class="purple white--text pt-3">
                <h3>EXPENSE BARU</h3>
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs12>
                        <v-layout row wrap>

                            <v-flex xs12>
                                <v-text-field
                                    label="Kode Expense"
                                    v-model="expense_code"
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12>
                                <v-text-field
                                    label="Nama Expense"
                                    v-model="expense_name"
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
            get () { return this.$store.state.m_expense_new.dialog_new },
            set (v) { this.$store.commit('m_expense_new/set_common', ['dialog_new', v]) }
        },

        expense_name : {
            get () { return this.$store.state.m_expense_new.expense_name },
            set (v) { this.$store.commit('m_expense_new/set_common', ['expense_name', v]) }
        },

        expense_code : {
            get () { return this.$store.state.m_expense_new.expense_code },
            set (v) { this.$store.commit('m_expense_new/set_common', ['expense_code', v]) }
        }
    },

    methods : {
        save () {
            var x = this.$store
            this.$store.dispatch('expense_new/save_m_expense').then((r) => {
                console.log(r)
                x.dispatch('expense_new/search_expense', {})
            })
        }
    },

    mounted () {},

    watch : {}
}
</script>