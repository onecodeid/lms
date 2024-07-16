<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs5>
                    <h3 class="display-1 zalfa-text-pink font-weight-light">DAFTAR PURCHASING</h3>
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

                            <v-btn color="success" class="ma-0 ml-1 btn-icon" @click="add_order" title="Order Normal">
                                <v-icon>add</v-icon>
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
            <v-data-table 
                :headers="headers"
                :items="orders"
                :loading="false"
                hide-actions
                class="elevation-1">
                <template slot="items" slot-scope="props">
                    <td class="text-xs-left pa-2" @click="select(props.item)">{{ reverse_date(props.item.P_PurchaseDate) }}</td>
                    <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.P_PurchaseNumber }}</td>
                    <td class="text-xs-left pa-2" @click="select(props.item)">
                        <div><b>{{ props.item.M_VendorName }}</b></div></td>
                    <td class="text-xs-right pa-2" @click="select(props.item)"><b>{{ one_money(props.item.P_PurchaseTotal) }}</b></td>
                    <td class="text-xs-center pa-1" @click="select(props.item)">
                        Approved
                    <td class="text-xs-center pa-1" @click="select(props.item)">
                        <v-btn color="primary" class="ma-0" small @click="edit(props.item)">Detail</v-btn>
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
        
        <common-dialog-delete :data="order_id" @confirm_del="confirm_del" v-if="dialog_delete"></common-dialog-delete>
        <common-dialog-print :report_url="report_url" v-if="dialog_report"></common-dialog-print>


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
        'common-datepicker' : httpVueLoader('../../common/components/common-datepicker.vue'),
        "common-dialog-print" : httpVueLoader("../../common/components/common-dialog-print.vue")
    },

    data () {
        return {
            headers: [
                {
                    text: "TANGGAL",
                    align: "left",
                    width: "8%",
                    sortable:false,
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "NOMOR",
                    align: "left",
                    sortable: false,
                    width: "8%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "NAMA VENDOR",
                    align: "left",
                    sortable: false,
                    width: "51%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "TOTAL",
                    align: "right",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "STATUS",
                    align: "center",
                    sortable: false,
                    width: "13%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "ACTION",
                    align: "center",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ],

            // report_url: ''
        }
    },

    computed : {
        orders () {
            return this.$store.state.purchase.orders
        },

        dialog_delete () {
            return this.$store.state.dialog_delete
        },

        order_id () {
            return this.$store.state.purchase.selected_order.P_PurchaseID
        },

        edate : {
            get () { return this.$store.state.purchase.e_date },
            set (v) { this.$store.commit('purchase/set_edate', v) }
        },

        sdate : {
            get () { return this.$store.state.purchase.s_date },
            set (v) { this.$store.commit('purchase/set_sdate', v) }
        },

        query : {
            get () { return this.$store.state.purchase.query },
            set (v) { this.$store.commit('purchase/set_common', ['query', v]) }
        },

        dialog_report : {
            get () { return this.$store.state.dialog_print },
            set (v) { this.$store.commit('set_dialog_print', v) }
        },

        curr_page : {
            get () { return this.$store.state.purchase.current_page },
            set (v) { this.$store.commit('purchase/set_common', ['current_page', v]) }
        },

        xtotal_page () {
            return this.$store.state.purchase.total_page
        },

        report_url () {
            return this.$store.state.purchase.URL+"report/one_iv_001?soid="+
                this.$store.state.purchase.selected_order.P_PurchaseID
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        },

        reverse_date(x) {
            return x.substr(0,10).split('-').reverse().join('-')
        },

        add () {
            
        },

        edit (x) {
            this.select(x)
            this.$store.commit('purchase_new/set_common', ['edit', true])
            this.$store.commit('purchase_new/set_common', ['dialog_new', true])
            this.$store.dispatch('purchase_new/search_item')
        },

        del (x) {
            this.select(x)
            this.$store.commit('set_dialog_delete', true)
        },

        confirm_del (x) {
            // this.$store.dispatch('order/del', {id:x.data})
        },

        select (x) {
            this.$store.commit('purchase/set_selected_order', x)

            // this.$store.commit('coupon/set_common', ['coupon_code', x.coupon_code])
            // this.$store.commit('coupon/set_common', ['coupon_amount', x.P_PurchaseCouponAmount])
            // this.$store.commit('coupon/set_common', ['coupon_amounts', x.P_PurchaseCouponAmount])
            // this.$store.commit('coupon/set_common', ['coupon_type', x.coupon_type])
            // this.$store.commit('coupon/set_common', ['coupon_id', x.coupon_id])
            // this.$store.commit('coupon/set_common', ['coupon_courier_id', x.coupon_courier_id])
            // this.$store.commit('coupon/set_coupon_item_id', x.coupon_item_id)
            // this.$store.commit('coupon/set_common', ['coupon_set', x.coupon_id == 0 ? false : true])
            // this.$store.commit('coupon/set_common', ['coupon_error', false])
            // this.$store.commit('coupon/set_common', ['coupon_error_msg', ''])
        },

        show (x) {
            this.select(x)
            // this.$store.dispatch('purchase/search_item')
            // this.$store.commit('purchase/set_common', ['dialog_item', true])

            // this.$store.commit('purchase/set_common', ['weight_add', x.P_PurchaseAddWeight])
            // this.$store.commit('purchase/set_common', ['weight_total', x.P_PurchaseTotalWeight])

            // this.$store.commit('purchase/set_common', ['edit', true])
            // for (let exp of this.$store.state.purchase.expeditions)
            //     if (exp.M_ExpeditionID == x.P_PurchaseM_ExpeditionID) {
            //         this.$store.commit('purchase/set_selected_expedition', exp)
            //         this.$store.dispatch('purchase/search_service')
            //     }
            return
        },

        change_edate(x) {
            this.edate = x.new_date
            this.search()
        },

        change_sdate(x) {
            this.sdate = x.new_date
            this.search()
        },

        search () {
            this.$store.dispatch('purchase/search', {})
        },

        print_invoice (x) {
            this.select(x)
            let so = x
            // this.report_url = this.$store.state.purchase.URL+"report/one_iv_001?soid="+so.P_PurchaseID
            this.$store.commit('set_dialog_print', true)
        },

        add_order () {
            this.$store.commit('purchase_new/set_common', ['edit', false])
            this.$store.commit('purchase_new/set_common', ['dialog_new', true])
        },

        change_page(x) {
            this.curr_page = x
            this.$store.dispatch('purchase/search')
        }
    },

    mounted () {
        this.$store.dispatch('purchase/search')
    }
}
</script>