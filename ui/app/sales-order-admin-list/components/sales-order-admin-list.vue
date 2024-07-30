<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs5>
                    <h3 class="display-1 zalfa-text-pink font-weight-light">DAFTAR PENERIMAAN KAS</h3>
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

                            <v-btn color="purple" dark class="ma-0 ml-1 btn-icon" @click="quick_order" title="Quick Order">
                                <v-icon>flash_on</v-icon>
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
                    <td class="text-xs-left pa-2" @click="select(props.item)">{{ reverse_date(props.item.L_SoDate) }}</td>
                    <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.L_SoNumber }}
                        <div :class="props.item.leadsource_color+'--text'">— {{props.item.leadsource_name}}</div></td>
                    <td class="text-xs-left pa-2" @click="select(props.item)">
                        <div><b>{{ props.item.M_CustomerName }}</b> — <span class="grey--text">{{ props.item.level_name }}, {{ props.item.city_name }}</span></div>
                        <div><v-icon small>smartphone</v-icon> {{props.item.M_CustomerPhone}}</div>
                        <div class="purple--text" v-if="props.item.L_SoIsDS == 'Y'">— Dropship, {{ props.item.ds_customer_name }} - {{ props.item.ds_city_name}}</div></td>
                    <!-- <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.referrer_name }}</td> -->
                    <!-- <td class="text-xs-center pa-2" @click="select(props.item)">{{ props.item.L_SoTotalUniqueQty }}</td> -->
                    <!-- <td class="text-xs-right pa-2" @click="select(props.item)">{{ one_money(props.item.L_SoTotalWeight) }} gr</td> -->
                    <td class="text-xs-right pa-2" @click="select(props.item)"><b>{{ one_money(props.item.L_SoTotal) }}</b></td>
                    <td class="text-xs-center pa-1" @click="select(props.item)">
                        <!-- <v-btn :color="status_color(props.item.M_OrderStatusCode)" outline small ma-0 block>{{ props.item.M_OrderStatusName }}</v-btn></td> -->
                        <div v-for="(i, n) in status(props.item)" v-bind:key="n" v-bind:class="status_color(props.item.M_OrderStatusCode)+'--text'">
                            {{ i }}
                            </div></td>
                    <td class="text-xs-center pa-1" @click="select(props.item)">
                        <!-- <v-btn color="orange" class="btn-icon ma-0 mb-1" small @click="print_invoice(props.item)" v-if="btn_inv_show(props.item.M_OrderStatusCode)" dark block>
                            <v-icon class="mr-2">print</v-icon> Inv</v-btn> -->
                        <v-btn color="deep-orange" class="btn-icon ma-0 mb-1" small @click="payment_confirmation(props.item)" v-if="props.item.M_OrderStatusCode=='SO.Confirmed'" dark block title="Konfirmasi Pembayaran">
                            <v-icon class="mr-2">payment</v-icon> Konf. Bayar</v-btn>
                        <v-btn color="primary" class="btn-icon ma-0" small @click="show(props.item)" block v-if="props.item.M_OrderStatusCode!='SO.Confirmed'">Detail</v-btn>

                        <v-layout row wrap v-if="props.item.M_OrderStatusCode=='SO.Confirmed'">
                            <v-flex xs6 pr-1><v-btn color="primary" class="btn-icon ma-0" small @click="show(props.item)" block>Detail</v-btn></v-flex>
                            <v-flex xs6><v-btn color="primary" class="btn-icon ma-0" small @click="send_email(props.item)" block> <v-icon>email</v-icon> Email</v-btn></v-flex>
                        </v-layout>
                        
                        <!-- <v-btn color="red" dark class="btn-icon ma-0" small @click="del(props.item)"><v-icon>delete</v-icon></v-btn> -->
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
                    text: "NAMA CUSTOMER",
                    align: "left",
                    sortable: false,
                    width: "49%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                // {
                //     text: "REFERRER",
                //     align: "left",
                //     sortable: false,
                //     width: "7.5%",
                //     class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                // },
                // {
                //     text: "U-QTY",
                //     align: "center",
                //     sortable: false,
                //     width: "7.5%",
                //     class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                // },
                // {
                //     text: "BERAT",
                //     align: "right",
                //     sortable: false,
                //     width: "7.5%",
                //     class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                // },
                {
                    text: "TOTAL BELANJA",
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
            return this.$store.state.salesorder.orders
        },

        dialog_delete () {
            return this.$store.state.dialog_delete
        },

        order_id () {
            return this.$store.state.salesorder.selected_order.L_SalesOrderID
        },

        edate : {
            get () { return this.$store.state.salesorder.edate },
            set (v) { this.$store.commit('salesorder/set_edate', v) }
        },

        sdate : {
            get () { return this.$store.state.salesorder.sdate },
            set (v) { this.$store.commit('salesorder/set_sdate', v) }
        },

        query : {
            get () { return this.$store.state.salesorder.query },
            set (v) { this.$store.commit('salesorder/set_common', ['query', v]) }
        },

        dialog_report : {
            get () { return this.$store.state.dialog_print },
            set (v) { this.$store.commit('set_dialog_print', v) }
        },

        curr_page : {
            get () { return this.$store.state.salesorder.current_page },
            set (v) { this.$store.commit('salesorder/set_common', ['current_page', v]) }
        },

        xtotal_page () {
            return this.$store.state.salesorder.total_page
        },

        report_url () {
            return this.$store.state.salesorder.URL+"report/one_iv_001?soid="+
                this.$store.state.salesorder.selected_order.L_SoID
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
        },

        del (x) {
            this.select(x)
            this.$store.commit('set_dialog_delete', true)
        },

        confirm_del (x) {
            // this.$store.dispatch('order/del', {id:x.data})
        },

        select (x) {
            this.$store.commit('salesorder/set_selected_order', x)

            this.$store.commit('coupon/set_common', ['coupon_code', x.coupon_code])
            this.$store.commit('coupon/set_common', ['coupon_amount', x.L_SoCouponAmount])
            this.$store.commit('coupon/set_common', ['coupon_amounts', x.L_SoCouponAmount])
            this.$store.commit('coupon/set_common', ['coupon_type', x.coupon_type])
            this.$store.commit('coupon/set_common', ['coupon_id', x.coupon_id])
            this.$store.commit('coupon/set_common', ['coupon_courier_id', x.coupon_courier_id])
            this.$store.commit('coupon/set_coupon_item_id', x.coupon_item_id)
            this.$store.commit('coupon/set_common', ['coupon_set', x.coupon_id == 0 ? false : true])
            this.$store.commit('coupon/set_common', ['coupon_error', false])
            this.$store.commit('coupon/set_common', ['coupon_error_msg', ''])
        },

        show (x) {
            this.select(x)
            this.$store.dispatch('salesorder/search_item')
            this.$store.commit('salesorder/set_common', ['dialog_item', true])

            this.$store.commit('salesorder/set_common', ['weight_add', x.L_SoAddWeight])
            this.$store.commit('salesorder/set_common', ['weight_total', x.L_SoTotalWeight])

            this.$store.commit('salesorder/set_common', ['edit', true])
            for (let exp of this.$store.state.salesorder.expeditions)
                if (exp.M_ExpeditionID == x.L_SoM_ExpeditionID) {
                    this.$store.commit('salesorder/set_selected_expedition', exp)
                    this.$store.dispatch('salesorder/search_service')
                }
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
            this.$store.dispatch('salesorder/search', {})
        },

        print_invoice (x) {
            this.select(x)
            let so = x
            // this.report_url = this.$store.state.salesorder.URL+"report/one_iv_001?soid="+so.L_SoID
            this.$store.commit('set_dialog_print', true)
        },

        payment_confirmation (x) {
            this.select(x)
            this.$store.commit('payment/set_common', ['transfer_amount', x.L_SoTotal])
            this.$store.commit('salesorder/set_common', ['dialog_payment', true])
        },

        btn_inv_show (x) {
            if (['SO.Confirmed'].indexOf(x) > -1)
                return true
            return false
        },

        status (x) {
            // if (x.W_CourierSent == 'Y')
            return x.M_OrderStatusName.split(';')
            // if (x.W_CourierProcessing == 'Y')
            //     return ['Sedang Diproses']
            // return ['Menunggu Diproses']
        },

        status_color (x) {
            if (['SO.NEW', 'SO.Approved'].indexOf(x) > -1)
                return 'success'
            if (x == 'SO.Confirmed') return 'orange'
            if (x == 'IV.Paid') return 'darken-4 deep-orange'
            if (x == 'IV.Confirmed') return 'cyan'
        },

        quick_order () {
            this.$store.commit('quickorder/set_common', ['dialog_quick', true])
            this.$store.commit('quickorder/set_details', [])
            this.$store.commit('quickorder/set_common', ['cust_id', 0])
            this.$store.commit('quickorder/set_common', ['cust_name', ''])
            this.$store.commit('quickorder/set_common', ['cust_address', ''])
            this.$store.commit('quickorder/set_common', ['cust_postcode', ''])
            this.$store.commit('quickorder/set_common', ['cust_phone', ''])
            this.$store.commit('quickorder/set_selected_city', null)
            this.$store.commit('quickorder/set_selected_expedition', null)
            this.$store.commit('quickorder/set_selected_service', null)
            this.$store.dispatch('quickorder/search_item')
        },

        add_order () {
            window.location.href = "../sales-order-seller-add"
        },

        change_page(x) {
            this.curr_page = x
            this.$store.dispatch('salesorder/search')
        },

        send_email(x) {
            this.select(x)
            this.$store.dispatch('salesorder/send_email')
        }
    },

    mounted () {
        this.$store.dispatch('salesorder/search')
    }
}
</script>