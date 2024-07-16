<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs9>
                    <h3 class="display-1 font-weight-light zalfa-text-title">Masterdata Bank Account</h3>
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
                :items="bankaccounts"
                :loading="false"
                hide-actions
                class="elevation-1">
                <template slot="items" slot-scope="props">
                    <td class="text-xs-left pa-2" @click="select(props.item)">
                        <v-img :src="URL+'../ui/app/'+props.item.bank_logo" height="24" width="48" style="float:left;margin-right:10px"></v-img>
                        {{ props.item.bank_name }}</td>
                    <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.account_number }}</td>
                    <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.account_name_only }}</td>                    
                    
                    <td class="text-xs-center pa-0" @click="select(props.item)">
                        <v-btn color="primary" class="btn-icon ma-0" small @click="edit(props.item)"><v-icon>create</v-icon></v-btn>
                        <v-btn color="red" dark class="btn-icon ma-0" small @click="del(props.item)"><v-icon>delete</v-icon></v-btn>
                    </td>
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
        
        <common-dialog-delete :data="account_id" @confirm_del="confirm_del" v-if="dialog_delete"></common-dialog-delete>
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
        "common-dialog-delete" : httpVueLoader("../../common/components/common-dialog-delete.vue")
    },

    data () {
        return {
            headers: [
                {
                    text: "NAMA BANK",
                    align: "left",
                    sortable: false,
                    width: "20%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "NOMOR REKENING",
                    align: "left",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "NAMA",
                    align: "left",
                    sortable: false,
                    width: "55%",
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
        bankaccounts () {
            return this.$store.state.bankaccount.bankaccounts
        },

        dialog_delete () {
            return this.$store.state.dialog_delete
        },

        bankaccount_id () {
            return this.$store.state.bankaccount.selected_bankaccount.M_BankAccountID
        },

        query : {
            get () { return this.$store.state.bankaccount.search },
            set (v) { this.$store.commit('bankaccount/update_search', v) }
        },

        curr_page : {
            get () { return this.$store.state.bankaccount.current_page },
            set (v) { this.$store.commit('bankaccount/update_current_page', v) }
        },

        xtotal_page () {
            return this.$store.state.bankaccount.total_bankaccount_page
        },

        URL () {
            return this.$store.state.bankaccount.URL
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        },

        add () {
            this.$store.commit('bankaccount_new/set_common', ['edit', false])
            this.$store.commit('bankaccount_new/set_common', ['bankaccount_name', ''])
            this.$store.commit('bankaccount_new/set_common', ['bankaccount_number', ''])
            this.$store.commit('bankaccount_new/set_selected_bank', null)
            this.$store.commit('bankaccount_new/set_common', ['dialog_new', true])
        },

        edit (x) {
            this.select(x)
            let sc = x
            this.$store.commit('bankaccount_new/set_common', ['edit', true])
            this.$store.commit('bankaccount_new/set_common', ['bankaccount_name', sc.account_name_only])
            this.$store.commit('bankaccount_new/set_common', ['bankaccount_number', sc.account_number])

            let y = this.$store.state.bankaccount_new.banks
            for (let i in y)
                if (y[i].M_BankID == sc.bank_id)
                    this.$store.commit('bankaccount_new/set_selected_bank', y[i])

            this.$store.commit('bankaccount_new/set_common', ['dialog_new', true])
        },

        del (x) {
            this.select(x)
            this.$store.commit('set_dialog_delete', true)
        },

        confirm_del (x) {
            this.$store.dispatch('bankaccount/del', {id:x.data})
        },

        select (x) {
            this.$store.commit('bankaccount/set_selected_bankaccount', x)
        },

        search () {
            return this.$store.dispatch('bankaccount/search', {})
        },

        change_page(x) {
            this.curr_page = x
            this.$store.dispatch('bankaccount/search', {})
        }
    }
}
</script>