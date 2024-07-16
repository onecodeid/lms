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
                            <v-flex xs12 v-show="!add_mode">
                                <v-select
                                    :items="expenses"
                                    v-model="selected_expense"
                                    return-object
                                    item-text="M_ExpenseName"
                                    item-value="M_ExpenseID"
                                    label="Jenis Ekspense"
                                    placeholder="Silahkan pilih expense"
                                >
                                    <template slot="item" slot-scope="data">
                                        <span class="blue--text mr-2">{{data.item.M_ExpenseCode}}</span> {{data.item.M_ExpenseName}}
                                    </template>

                                    <template slot="selection" slot-scope="data">
                                        <span class="blue--text mr-2">{{data.item.M_ExpenseCode}}</span> {{data.item.M_ExpenseName}}
                                    </template>

                                    <template slot="append-outer">
                                        <v-btn small color="success" class="ma-0 ml-2 btn-icon" @click="add_expense">
                                            <v-icon>add</v-icon>
                                        </v-btn>
                                    </template>
                                </v-select>
                            </v-flex>

                            <v-flex xs3 pr-2 v-show="add_mode">
                                <v-text-field
                                    label="Kode Expense"
                                    v-model="expense_code"
                                    placeholder="E.XXX"
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs9 v-show="add_mode">
                                <v-text-field
                                    label="Nama Expense"
                                    v-model="expense_name"
                                    placeholder="Misal : Tagihan Listrik, Peralatan Kantor"
                                >
                                    <template slot="append-outer">
                                        <v-btn small color="purple" dark class="ma-0 ml-2 btn-icon" @click="add_expense">
                                            <v-icon>swap_horiz</v-icon>
                                        </v-btn>
                                    </template>
                                </v-text-field>
                            </v-flex>

                            <v-flex xs6 pr-3>
                                <common-datepicker
                                    label="Tanggal Expense"
                                    :date="expense_date"
                                    data="0"
                                    @change="change_expense_date"
                                    classs=""
                                    hints=" "
                                    :details="true"
                                    :solo="false"
                                ></common-datepicker>
                            </v-flex>

                            <v-flex xs6>
                                <v-text-field
                                    label="Jumlah"
                                    reverse
                                    suffix="Rp"
                                    v-model="expense_amount"

                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12>
                                <v-textarea
                                    label="Catatan"
                                    v-model="expense_note">

                                </v-textarea>
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
        return { 
            add_mode: false
        }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.expense_new.dialog_new },
            set (v) { this.$store.commit('expense_new/set_common', ['dialog_new', v]) }
        },

        item_name : {
            get () { return this.$store.state.expense_new.item_name },
            set (v) { this.$store.commit('expense_new/set_common', ['item_name', v]) }
        },

        expenses () {
            return this.$store.state.expense_new.expenses
        },

        selected_expense : {
            get () { return this.$store.state.expense_new.selected_expense },
            set (v) { this.$store.commit('expense_new/set_selected_expense', v) }
        },

        expense_date : {
            get () { return this.$store.state.expense_new.expense_date },
            set (v) { this.$store.commit('expense_new/set_common', ['expense_date', v]) }
        },

        expense_amount : {
            get () { return this.$store.state.expense_new.expense_amount },
            set (v) { this.$store.commit('expense_new/set_common', ['expense_amount', v]) }
        },

        expense_note : {
            get () { return this.$store.state.expense_new.expense_note },
            set (v) { this.$store.commit('expense_new/set_common', ['expense_note', v]) }
        },

        expense_name : {
            get () { return this.$store.state.expense_new.expense_name },
            set (v) { this.$store.commit('expense_new/set_common', ['expense_name', v]) }
        },

        expense_code : {
            get () { return this.$store.state.expense_new.expense_code },
            set (v) { this.$store.commit('expense_new/set_common', ['expense_code', v]) }
        }
    },

    methods : {
        save () {
            this.$store.dispatch('expense_new/save', {add_mode:this.add_mode})
        },

        change_expense_date(x) {
            this.expense_date = x.new_date
        },

        add_expense() {
            this.add_mode = !this.add_mode
        }
    },

    mounted () {
        this.$store.dispatch('expense_new/search_expense', {})
    },

    watch : {
        dialog (v, o) {
            if (v && !o) {
                this.add_mode = false
                this.expense_name = ''
                this.expense_code = ''
            }
        }
    }
}
</script>