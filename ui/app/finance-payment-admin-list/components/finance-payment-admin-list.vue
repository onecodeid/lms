<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs4>
                    <h3 class="display-1 font-weight-thin"><span class="orange--text">DAFTAR</span> PIUTANG</h3>
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

                <v-flex xs2>
                    <v-select
                        :items="statuses"
                        v-model="selected_status"
                        return-object
                        item-text="text"
                        item-value="value"
                        solo
                        hide-details
                        placeholder="Semua Pembayaran"
                        clearable
                    ></v-select>
                </v-flex>

                <v-flex xs2 class="text-xs-right" pl-2>
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
            <v-data-table 
                :headers="headers"
                :items="orders"
                :loading="false"
                hide-actions
                class="elevation-1">
                <template slot="items" slot-scope="props">
                    <td class="text-xs-left pa-2" @click="select(props.item)">{{ reverse_date(props.item.L_SoDate) }}</td>
                    <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.L_SoNumber }}</td>
                    <td class="text-xs-left pa-2" @click="select(props.item)">
                        <div><b>{{ props.item.M_CustomerName }}</b> — <span class="grey--text">{{props.item.level_name}}, {{props.item.city_name}}</span></div>
                        <div class="purple--text" v-if="props.item.L_SoIsDS == 'Y'">— Dropship, {{ props.item.ds_customer_name }} - {{ props.item.ds_city_name}}</div></td>
                    <td class="text-xs-center pa-2" @click="select(props.item)">{{ props.item.L_SoTotalQty }}</td>
                    
                    <td class="text-xs-right pa-2" @click="select(props.item)">{{ one_money(props.item.L_SoTotal) }}</td>
                    <td class="text-xs-right pa-2" @click="select(props.item)">{{ one_money(props.item.F_PaymentAmount) }}</td>
                    <td class="text-xs-center pa-1" v-bind:class="status_color(props.item.M_OrderStatusCode)" @click="select(props.item)">
                        <div>{{ props.item.M_OrderStatusName }}</div></td>
                    <td class="text-xs-center pa-0" @click="select(props.item)">
                        <v-btn color="orange" class="btn-icon ma-0 xs6" small @click="print_invoice(props.item)" v-if="btn_inv_show(props.item.M_OrderStatusCode)" dark v-show="props.item.M_OrderStatusCode == 'SO.Confirmed'">
                            <v-icon class="mr-2">print</v-icon> Inv</v-btn>
                        <v-btn color="primary" class="btn-icon ma-0" small @click="show(props.item)" v-show="props.item.M_OrderStatusCode == 'SO.Confirmed'">Lihat</v-btn>
                        <v-btn color="deep-orange darken-4" class="btn-icon ma-0" dark small @click="show(props.item)" v-show="props.item.M_OrderStatusCode == 'IV.Paid'">Verifikasi</v-btn>

                        <v-btn color="primary" class="btn-icon ma-0" small @click="show(props.item)" v-show="['IV.Confirmed','WH.Processing','WH.Courier','WH.Sent'].indexOf(props.item.M_OrderStatusCode) > -1">Lihat</v-btn>
                        
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
                    width: "27.5%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "QTY ITEM",
                    align: "center",
                    sortable: false,
                    width: "7.5%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "TOTAL TAGIHAN",
                    align: "right",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "JUMLAH DIBAYAR",
                    align: "right",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "STATUS",
                    align: "center",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "ACTION",
                    align: "center",
                    sortable: false,
                    width: "17%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ],

            report_url: ''
        }
    },

    computed : {
        orders () {
            return this.$store.state.payment.orders
        },

        dialog_delete () {
            return this.$store.state.dialog_delete
        },

        order_id () {
            return this.$store.state.payment.selected_order.L_SalesOrderID
        },

        edate : {
            get () { return this.$store.state.payment.e_date },
            set (v) { this.$store.commit('payment/set_edate', v) }
        },

        sdate : {
            get () { return this.$store.state.payment.s_date },
            set (v) { this.$store.commit('payment/set_sdate', v) }
        },

        query : {
            get () { return this.$store.state.payment.query },
            set (v) { this.$store.commit('payment/set_common', ['query', v]) }
        },

        dialog_report : {
            get () { return this.$store.state.dialog_print },
            set (v) { this.$store.commit('set_dialog_print', v) }
        },

        statuses () {
            return this.$store.state.payment.statuses
        },

        selected_status : {
            get () { return this.$store.state.payment.selected_status },
            set (v) { 
                this.$store.commit('payment/set_selected_status', v) 
                this.search()
            }
        },

        curr_page () {
            return this.$store.state.payment.current_page
        },

        xtotal_page () {
            return this.$store.state.payment.total_page
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
            this.$store.commit('payment/set_selected_order', x)
        },

        show (x) {
            this.select(x)
            // this.$store.dispatch('payment/search_item')
            this.$store.commit('payment/set_common', ['dialog_item', true])

            this.$store.commit('payment/set_common', ['weight_add', x.L_SoAddWeight])
            this.$store.commit('payment/set_common', ['weight_total', x.L_SoTotalWeight])

            this.$store.commit('payment/set_common', ['edit', true])
            for (let exp of this.$store.state.payment.expeditions)
                if (exp.M_ExpeditionID == x.L_SoM_ExpeditionID) {
                    this.$store.commit('payment/set_selected_expedition', exp)
                    this.$store.dispatch('payment/search_service')
                }

            this.$store.commit('payment/set_common', ['transfer_amount', x.L_SoTotal])
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
            this.$store.dispatch('payment/search', {})
        },

        print_invoice (x) {
            this.select(x)
            let so = x
            this.report_url = this.$store.state.payment.URL+"report/one_iv_001?soid="+so.L_SoID
            this.$store.commit('set_dialog_print', true)
        },

        btn_inv_show (x) {
            if (['SO.Confirmed', 'IV.Confirmed', 'IV.Paid'].indexOf(x) > -1)
                return true
            return false
        },

        status_color (x) {
            if (['SO.NEW', 'SO.Approved'].indexOf(x) > -1)
                return 'success--text'
            if (x == 'SO.Confirmed') return 'orange--text'
            if (x == 'IV.Paid') return 'deep-orange--text darken-4'
            if (x == 'IV.Confirmed') return 'cyan--text'
        },

        change_page (x) {
            this.$store.commit('payment/set_common', ['current_page', x])
            this.search()
        }
    },

    mounted () {
        this.$store.dispatch('payment/search')
    }
}
</script>