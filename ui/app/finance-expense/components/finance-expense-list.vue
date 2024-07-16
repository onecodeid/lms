<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs5>
                    <h3 class="display-1 font-weight-light zalfa-text-title">Daftar Expense</h3>
                </v-flex>

                <v-flex xs2>
                    <common-datepicker
                        label="Dari Tanggal"
                        :date="sdate"
                        data="0"
                        @change="change_sdate"
                        classs="mt-0 ml-5"
                        hints=" "
                        :details="false"
                    ></common-datepicker>
                </v-flex>

                <v-flex xs2>
                    <common-datepicker
                        label="Sampai Tanggal"
                        :date="edate"
                        data="0"
                        @change="change_edate"
                        classs="mt-0 ml-1 mr-5"
                        hints=""
                        :details="false"
                    ></common-datepicker>
                </v-flex>

                <v-flex xs3 class="text-xs-right">
                    <!-- <v-btn color="success" class="ma-0 btn-icon" @click="add">
                        <v-icon>add</v-icon>
                    </v-btn> -->

                    <v-text-field
                        solo
                        hide-details
                        placeholder="Pencarian" v-model="query"
                        @change="search"
                    >
                        <template v-slot:append-outer>
                            <v-btn color="primary" class="ma-0 btn-icon" @click="search">
                                <v-icon>search</v-icon>
                            </v-btn>      

                            <v-btn color="success" class="ma-0 ml-2 btn-icon" @click="add">
                                <v-icon>add</v-icon>
                            </v-btn>  
                        </template>
                    </v-text-field>
                </v-flex>
            </v-layout>
        </v-card-title>
        <v-card-text class="pt-2">
            <v-data-table 
                :headers="headers"
                :items="expenses"
                :loading="false"
                hide-actions
                class="elevation-1">
                <template slot="items" slot-scope="props">
                    
                    <td class="text-xs-center pa-2" @click="select(props.item)">{{ props.item.expense_date }}</td>
                    <td class="text-xs-center pa-2" @click="select(props.item)">{{ props.item.expense_code }}</td>
                    <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.expense_name }}</td>
                    <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.expense_note }}</td>
                    <td class="text-xs-right pa-2" @click="select(props.item)"><span style="float:left">Rp</span>{{ one_money(props.item.expense_amount) }}</td>                    
                    
                    <td class="text-xs-center pa-0" @click="select(props.item)">
                        <v-btn color="primary" class="btn-icon ma-0" small @click="edit(props.item)"><v-icon>create</v-icon></v-btn>
                        <v-btn color="red" dark class="btn-icon ma-0" small @click="del(props.item)"><v-icon>delete</v-icon></v-btn>
                    </td>
                    <!-- <td class="text-xs-center pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.M_DoctorHP}}</td>
                    <td class="text-xs-left pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.status}}</td> -->
                </template>
            </v-data-table>
            <v-divider></v-divider>
            <v-pagination
                style="margin-top:10px;margin-bottom:10px"
                v-model="curr_page"
                :length="xtotal_page"
                @input="change_page"
            ></v-pagination>
        </v-card-text>
        
        <common-dialog-delete :data="expense_id" @confirm_del="confirm_del" v-if="dialog_delete"></common-dialog-delete>
    </v-card>
</template>

<style scoped>
.v-text-field.v-text-field--solo .v-input__control {
    min-height: 36px;
}
.v-text-field.v-text-field--solo .v-input__append-outer {
    margin-top: 0px;
    margin-left: 0px;
}
</style>

<script>
module.exports = {
    components : {
        "common-dialog-delete" : httpVueLoader("../../common/components/common-dialog-delete.vue"),
        'common-datepicker' : httpVueLoader('../../common/components/common-datepicker.vue')
    },

    data () {
        return {
            headers: [
                {
                    text: "TANGGAL",
                    align: "center",
                    sortable: false,
                    width: "7%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "KODE",
                    align: "center",
                    sortable: false,
                    width: "7%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "NAMA",
                    align: "left",
                    sortable: false,
                    width: "26%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "CATATAN",
                    align: "left",
                    sortable: false,
                    width: "40%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "J U M L A H",
                    align: "right",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "ACTION",
                    align: "center",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ]
        }
    },

    computed : {
        expenses () {
            return this.$store.state.expense.expenses
        },

        dialog_delete () {
            return this.$store.state.dialog_delete
        },

        expense_id () {
            return this.$store.state.expense.selected_expense.expense_id
        },

        query : {
            get () { return this.$store.state.expense.search },
            set (v) { this.$store.commit('expense/set_common', ['search', v]) }
        },

        curr_page : {
            get () { return this.$store.state.expense.current_page },
            set (v) { this.$store.commit('expense/set_common', ['current_page', v]) }
        },

        xtotal_page () {
            return this.$store.state.expense.total_expense_page
        },

        edate : {
            get () { return this.$store.state.expense.e_date },
            set (v) { this.$store.commit('expense/set_common', ['edate', v]) }
        },

        sdate : {
            get () { return this.$store.state.expense.s_date },
            set (v) { this.$store.commit('expense/set_common', ['sdate', v]) }
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        },

        add () {
            // this.$store.dispatch('expense_new/search_level_price', {})
            this.$store.commit('expense_new/set_common', ['edit', false])
            this.$store.commit('expense_new/set_common', ['expense_amount', 0])
            this.$store.commit('expense_new/set_common', ['expense_date', new Date().toISOString().substr(0, 10)])
            this.$store.commit('expense_new/set_common', ['expense_note', ''])
            this.$store.commit('expense_new/set_common', ['dialog_new', true])
            this.$store.commit('expense_new/set_selected_expense', null)
        },

        edit (x) {
            this.select(x)
            let sc = x
            this.$store.commit('expense_new/set_common', ['edit', true])
            this.$store.commit('expense_new/set_common', ['expense_amount', sc.expense_amount])
            this.$store.commit('expense_new/set_common', ['expense_date', sc.expense_date])
            this.$store.commit('expense_new/set_common', ['expense_note', sc.expense_note])

            // EXPENSE ID
            let e = this.$store.state.expense_new.expenses
            this.$store.commit('expense_new/set_selected_expense', null)
            for (let x of e)
                if (x.M_ExpenseID == sc.m_expense_id)
                    this.$store.commit('expense_new/set_selected_expense', x)

            this.$store.commit('expense_new/set_common', ['dialog_new', true])
        },

        del (x) {
            this.select(x)
            this.$store.commit('set_dialog_delete', true)
        },

        confirm_del (x) {
            this.$store.dispatch('expense/del', {expense_id:x.data})
        },

        select (x) {
            this.$store.commit('expense/set_selected_expense', x)
        },

        search () {
            return this.$store.dispatch('expense/search', {})
        },

        change_page(x) {
            this.curr_page = x
            this.$store.dispatch('expense/search', {})
        },

        change_edate(x) {
            this.edate = x.new_date
            this.search()
        },

        change_sdate(x) {
            this.sdate = x.new_date
            this.search()
        }
    }
}
</script>