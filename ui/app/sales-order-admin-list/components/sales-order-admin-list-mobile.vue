<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0 px-2">
            <v-layout row wrap>
                <v-flex xs12 sm6 mb-2>
                    <h3 class="display-1 zalfa-text-pink font-weight-light">DAFTAR TRANSAKSI</h3>
                </v-flex>
                
                <v-flex xs12 sm6 class="text-xs-right">
                    <v-text-field
                        solo
                        hide-details
                        placeholder="Pencarian" v-model="query"
                        @change="search"
                    >
                        <template v-slot:append-outer>
                            <v-btn color="primary" class="ma-0 btn-icon hidden-xs-only" @click="search">
                                <v-icon>search</v-icon>
                            </v-btn>

                            <v-btn color="purple" class="ma-0 ml-1 btn-icon hidden-xs-only" @click="filter" dark>
                                <v-icon>filter_list</v-icon>
                            </v-btn>

                            <v-btn color="purple" dark class="ma-0 ml-1 btn-icon hidden-xs-only" @click="quick_order" title="Quick Order">
                                <v-icon>flash_on</v-icon>
                            </v-btn>

                            <v-btn color="success" class="ma-0 ml-1 btn-icon hidden-xs-only" @click="add_order" title="Order Normal">
                                <v-icon>add</v-icon>
                            </v-btn>
                        </template>

                        <template v-slot:prepend>
                            <v-btn color="success" class="ma-0 mr-1 btn-icon hidden-sm-and-up" @click="add_order" title="Order Normal">
                                <v-icon>add</v-icon>
                            </v-btn>

                            <v-btn color="purple" dark class="ma-0 mr-1 btn-icon hidden-sm-and-up" @click="quick_order" title="Quick Order">
                                <v-icon>flash_on</v-icon>
                            </v-btn>

                            <v-btn color="purple" class="ma-0 mr-1 btn-icon hidden-sm-and-up" @click="filter" dark>
                                <v-icon>filter_list</v-icon>
                            </v-btn>

                            <v-btn color="primary" class="ma-0 btn-icon hidden-sm-and-up" @click="search">
                                <v-icon>search</v-icon>
                            </v-btn> 
                        </template>
                    </v-text-field>
                    
                </v-flex>
            </v-layout>
        </v-card-title>
        <v-card-text class="pt-2 px-2">

            <v-card v-for="(item, n) in orders" v-bind:key="n" class="mb-2" @click="show(item)">
                <v-card-title primary-title class="pa-2 grey lighten-2">
                    <v-layout row wrap>
                        <v-flex xs8>
                            {{ reverse_date(item.L_SoDate) }}<br>
                            <b>{{ item.M_CustomerName }}</b>, {{ item.city_name }}</v-flex>
                        <v-flex xs4 class="text-xs-right">
                            <h3 class="blue--text">{{ item.L_SoNumber }}</h3>
                            <div class="caption"><i>{{item.referrer_name?'— '+item.referrer_name:''}}</i></div>
                            </v-flex>
                    </v-layout>                        
                </v-card-title>
                <v-card-text class="pa-2">
                    <v-layout row wrap>
                        <v-flex xs5>
                            <div class="caption">Total belanja :</div>
                            <v-layout row wrap>
                                <v-flex xs4 class="orange--text">Rp</v-flex>
                                <v-flex xs8 class="text-xs-right title orange--text">{{one_money(item.L_SoTotal)}}</v-flex>
                            </v-layout>
                        </v-flex>
                        <v-flex xs6 offset-xs1 class="text-xs-right">
                            <div class="caption">Status :</div>
                            <div v-for="(i, n) in status_render(item.M_OrderStatusName)" v-bind:key="n" :class="status_color(item.M_OrderStatusCode)+'--text'">
                            {{ i }}
                            </div>
                            
                        </v-flex>
                    </v-layout>
                </v-card-text>
            </v-card>
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
.v-text-field.v-text-field--solo .v-input__append-outer, .v-text-field.v-text-field--solo .v-input__prepend-outer {
    margin-top: 0px;
    margin-left: 0px;
}
.v-input__prepend-outer {
    margin-right: 0px;
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
                    width: "26.5%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "REFERRER",
                    align: "left",
                    sortable: false,
                    width: "7.5%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "U-QTY",
                    align: "center",
                    sortable: false,
                    width: "7.5%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "BERAT",
                    align: "right",
                    sortable: false,
                    width: "7.5%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
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
            get () { return this.$store.state.salesorder.e_date },
            set (v) { this.$store.commit('salesorder/set_edate', v) }
        },

        sdate : {
            get () { return this.$store.state.salesorder.s_date },
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
        },

        filter () {
            this.$store.commit('salesorder/set_common', ['dialog_filter', true])
        },

        status_render (status) {
            return status.split(';')
        }
    },

    mounted () {
        this.$store.dispatch('salesorder/search')
    }
}
</script>