<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs5>
                    <h3 class="display-1 font-weight-light zalfa-text-title">Laporan Perolehan Komisi Admin</h3>
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
                        v-if="dtx"
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
                        v-if="dtx"
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

                            <!-- <v-btn color="success" class="ma-0 ml-2 btn-icon" @click="add">
                                <v-icon>add</v-icon>
                            </v-btn>   -->
                        </template>
                    </v-text-field>
                </v-flex>
            </v-layout>
        </v-card-title>
        <v-card-text class="pt-2">
            <v-layout row wrap>
                <v-flex xs3 pr-2>
                    <stat-fee-per-admin-user-list></stat-fee-per-admin-user-list>
                </v-flex>
                <v-flex xs9>
                    <v-data-table 
                        :headers="headers"
                        :items="fees"
                        :loading="false"
                        hide-actions
                        class="elevation-1">
                        <template slot="items" slot-scope="props">
                            
                            <td class="text-xs-left pa-2" @click="select(props.item)">
                                {{ props.item.F_FeeL_SoDate.substr(0,10).split('-').reverse().join('-') }}<br>
                                <span class="blue--text">{{ props.item.L_SoNumber }}</span></td>
                            <!-- <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.S_UserProfileFullName }}</td> -->
                            <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.M_CustomerName }}, {{props.item.city_name}}</td>
                            <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.M_ItemName?props.item.M_ItemName:props.item.M_PacketName }}</td>
                            <td class="text-xs-center pa-2" @click="select(props.item)">{{ props.item.F_FeeQty }}</td>
                            <td class="text-xs-right pa-2" @click="select(props.item)">{{ one_money(props.item.F_FeeTotal) }}</td>                    
                            
                            <!-- <td class="text-xs-center pa-0" @click="select(props.item)">
                                <v-btn color="primary" class="btn-icon ma-0" small @click="edit(props.item)"><v-icon>create</v-icon></v-btn>
                                <v-btn color="red" dark class="btn-icon ma-0" small @click="del(props.item)"><v-icon>delete</v-icon></v-btn>
                            </td> -->
                        </template>
                    </v-data-table>

                    <v-pagination
                        style="margin-top:10px;margin-bottom:10px"
                        v-model="curr_page"
                        :length="xtotal_page"
                        @input="change_page"
                    ></v-pagination>
                </v-flex>
            </v-layout>
            
            <v-divider></v-divider>
            
        </v-card-text>
        
        <common-dialog-delete :data="fee_id" @confirm_del="confirm_del" v-if="dialog_delete"></common-dialog-delete>
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
        'common-datepicker' : httpVueLoader('../../common/components/common-datepicker.vue'),
        "common-dialog-delete" : httpVueLoader("../../common/components/common-dialog-delete.vue"),
        "stat-fee-per-admin-user-list" : httpVueLoader("./stat-fee-per-admin-user-list.vue")
    },

    data () {
        return {
            headers: [
                {
                    text: "TANGGAL",
                    align: "left",
                    sortable: false,
                    width: "7%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                // {
                //     text: "ADMIN",
                //     align: "left",
                //     sortable: false,
                //     width: "15%",
                //     class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                // },
                {
                    text: "CUSTOMER",
                    align: "left",
                    sortable: false,
                    width: "25%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "NAMA ITEM",
                    align: "left",
                    sortable: false,
                    width: "41%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "QTY",
                    align: "center",
                    sortable: false,
                    width: "7%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "FEE TOTAL",
                    align: "right",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ]
        }
    },

    computed : {
        fees () {
            return this.$store.state.fee.fees
        },

        dialog_delete () {
            return this.$store.state.dialog_delete
        },

        fee_id () {
            return this.$store.state.fee.selected_fee.M_ExpenseID
        },

        query : {
            get () { return this.$store.state.fee.search },
            set (v) { this.$store.commit('fee/update_search', v) }
        },

        curr_page : {
            get () { return this.$store.state.fee.current_page },
            set (v) { this.$store.commit('fee/update_current_page', v) }
        },

        xtotal_page () {
            return this.$store.state.fee.total_fee_page
        },

        edate : {
            get () { return this.$store.state.fee.edate },
            set (v) { this.$store.commit('fee/set_edate', v) }
        },

        sdate : {
            get () { return this.$store.state.fee.sdate },
            set (v) { this.$store.commit('fee/set_sdate', v) }
        },

        dtx () {
            return this.$store.state.fee.dtx
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        },

        add () {
            this.$store.commit('fee_new/set_common', ['edit', false])
            this.$store.commit('fee_new/set_common', ['fee_name', ''])
            this.$store.commit('fee_new/set_common', ['fee_code', ''])
            this.$store.commit('fee_new/set_common', ['dialog_new', true])
        },

        edit (x) {
            this.select(x)
            let sc = x
            this.$store.commit('fee_new/set_common', ['edit', true])
            this.$store.commit('fee_new/set_common', ['fee_name', sc.M_ExpenseName])
            this.$store.commit('fee_new/set_common', ['fee_code', sc.M_ExpenseCode])

            this.$store.commit('fee_new/set_common', ['dialog_new', true])
        },

        del (x) {
            this.select(x)
            this.$store.commit('set_dialog_delete', true)
        },

        confirm_del (x) {
            this.$store.dispatch('fee/del', {id:x.data})
        },

        select (x) {
            this.$store.commit('fee/set_selected_fee', x)
        },

        search () {
            return this.$store.dispatch('fee/search_user', {})
            // return this.$store.dispatch('fee/search', {})
        },

        change_page(x) {
            this.curr_page = x
            this.$store.dispatch('fee/search', {})
        },

        change_edate(x) {
            this.edate = x.new_date
            this.search()
        },

        change_sdate(x) {
            this.sdate = x.new_date
            this.search()
        }
    },

    mounted () {
        let d = new Date().toISOString().substr(0, 10).split('-')
        d.pop()
        d.push('01')
        this.$store.commit('fee/set_sdate', d.join('-'))
        this.$store.commit('fee/set_dtx', true)
        // this.$store.dispatch('fee/search_user', {})
    }
}
</script>