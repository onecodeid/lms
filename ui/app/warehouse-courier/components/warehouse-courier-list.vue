<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs2>
                    <h3 class="display-1 font-weight-light zalfa-text-title">PENGIRIMAN</h3>
                </v-flex>
                <v-flex xs2>
                    <common-datepicker
                        label="Tanggal"
                        :date="sdate"
                        data="0"
                        @change="change_sdate"
                        classs="mt-0 ml-5"
                        hints="Tanggal Awal"
                        :details="true"
                    ></common-datepicker>
                </v-flex>

                <v-flex xs2>
                    <common-datepicker
                        label="Tanggal"
                        :date="edate"
                        data="0"
                        @change="change_edate"
                        classs="mt-0 ml-1 mr-5"
                        hints="Tanggal Akhir"
                        :details="true"
                    ></common-datepicker>
                </v-flex>

                <v-flex xs2>
                    <v-select
                        :items="expeditions"
                        v-model="selected_expedition"
                        return-object
                        item-text="M_ExpeditionName"
                        item-value="M_ExpeditionID"
                        solo
                        hide-details
                        placeholder="Semua Ekspedisi"
                        clearable
                    ></v-select>    
                </v-flex>

                <v-flex xs2 pl-2>
                    <v-select
                        :items="sent_statuses"
                        v-model="selected_sent_status"
                        return-object
                        item-text="text"
                        item-value="value"
                        solo
                        hide-details
                        placeholder="Semua Status"
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

                            <v-menu
                            bottom left
                            transition="scale-transition"
                            >
                                <template v-slot:activator="{ on }">
                                    <v-btn
                                    color="orange"
                                    dark
                                    v-on="on"
                                    class="btn-icon ma-0 ml-1"
                                    >
                                    <v-icon>print</v-icon>
                                    </v-btn>
                                </template>

                                <v-list>
                                    <v-list-tile @click="print_me('logbook')">
                                        <v-list-tile-title><v-icon small>print</v-icon> <span class="body-1">Cetak Logbook Ekspedisi</span></v-list-tile-title>
                                    </v-list-tile>
                                </v-list>
                            </v-menu>
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
                    <td class="text-xs-left pa-2" @click="select(props.item)">{{ reverse_date(props.item.W_CourierDate) }}</td>
                    <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.L_SoNumber }}</td>
                    
                    <td class="text-xs-left pa-2" @click="select(props.item)" v-show="props.item.L_SoIsDS == 'N'">
                        <b>{{ props.item.M_CustomerName }}</b> — <span class="grey--text">{{ props.item.level_name }}, {{ props.item.customer_full_address.city_name }}</span></td>
                    <td class="text-xs-left pa-2" @click="select(props.item)" v-show="props.item.L_SoIsDS == 'Y'">
                        <b>{{ props.item.M_CustomerName }}</b> — <span class="grey--text">{{ props.item.level_name }}, {{ props.item.customer_full_address.city_name }}</span>
                        <div class="purple--text">— Dropship, {{ props.item.ds_customer_name }}</div></td>
                    
                    <td class="text-xs-center pa-2" @click="select(props.item)">
                        <span v-for="(i, n) in props.item.items" v-bind:key="n" v-bind:class="n%2 == 0 ? 'cyan--text' : ''">{{n==0?'':','}} {{ i }}</span>
                        
                        </td>
                    <td class="text-xs-center pa-2" @click="select(props.item)">{{ props.item.L_SoTotalWeight / 1000 }} Kg</td>
                    <td class="text-xs-center pa-2 blue--text" @click="select(props.item)">
                        <v-spacer></v-spacer><v-img :src="'../'+props.item.M_ExpeditionLogo" height="24" width="48" position="center center" class="img-zalfa-exp-logo-small"></v-img>
                        <div v-show="props.item.ex_code!='EX.OTHER'">{{ props.item.M_ExpeditionName }}, {{ props.item.L_SoExpService }}</div>
                        <div v-show="props.item.ex_code=='EX.OTHER'">{{ props.item.M_ExpeditionName }}, {{ props.item.ex_other }}</div>
                        </td>
                    
                    <td class="text-xs-center pa-2" @click="select(props.item)">
                        <div v-for="(i, n) in status(props.item)" v-bind:key="n">
                            {{ i }}
                            </div>
                        <div v-show="props.item.M_OrderStatusCode == 'WH.Sent'"><a @click="waybill(props.item)">{{ props.item.W_CourierReceiptNo }}</a></div>
                    </td>

                    <td class="text-xs-center pa-1" @click="select(props.item)">
                        <v-btn block color="blue" class="btn-icon ma-0" small @click="process(props.item)" dark v-show="props.item.W_CourierProcessing == 'N'">
                            <v-icon class="mr-2">cached</v-icon> PROSES</v-btn>

                        <v-btn block color="orange" class="btn-icon ma-0 mb-1" small @click="print_sj(props.item)" dark v-show="props.item.W_CourierProcessing == 'Y' && props.item.W_CourierSent == 'N'">
                            <v-icon class="mr-2">print</v-icon> S. JALAN</v-btn>

                        <v-btn block color="purple" class="btn-icon ma-0" small @click="send(props.item)" dark v-show="props.item.W_CourierProcessing == 'Y' && props.item.W_CourierSent == 'N'">
                            <v-icon class="mr-2">local_shipping</v-icon> K I R I M</v-btn>

                        <v-btn block color="purple" class="btn-icon ma-0" small @click="receipt(props.item)" dark v-show="props.item.W_CourierReceiptNo == '' && props.item.W_CourierSent == 'Y'">
                            <v-icon class="mr-2">local_shipping</v-icon> INPUT RESI</v-btn>

                        <v-btn block color="purple" class="btn-icon ma-0" small @click="receipt_edit(props.item)" dark v-show="props.item.W_CourierReceiptNo != '' && props.item.W_CourierSent == 'Y'">
                            <v-icon class="mr-2">local_shipping</v-icon> UBAH RESI</v-btn>
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
        <common-dialog-confirm data="1" text="Proses transaksi tersebut ?" @confirm="do_process"></common-dialog-confirm>
        <common-dialog-manifest v-if="dialog_manifest" :manifest="manifests"></common-dialog-manifest>
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

.img-zalfa-exp-logo-small {
    margin: auto
}
</style>

<script>
module.exports = {
    components : {
        "common-dialog-delete" : httpVueLoader("../../common/components/common-dialog-delete.vue"),
        'common-datepicker' : httpVueLoader('../../common/components/common-datepicker.vue'),
        "common-dialog-print" : httpVueLoader("../../common/components/common-dialog-print.vue"),
        "common-dialog-confirm" : httpVueLoader("../../common/components/common-dialog-confirm.vue"),
        "common-dialog-manifest" : httpVueLoader("../../common/components/common-dialog-manifest.vue")
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
                    text: "NO ORDER",
                    align: "left",
                    sortable: false,
                    width: "8%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "NAMA CUSTOMER",
                    align: "left",
                    sortable: false,
                    width: "22.5%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "ITEMS",
                    align: "center",
                    sortable: false,
                    width: "17.5%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "TOTAL BERAT",
                    align: "center",
                    sortable: false,
                    width: "7.5%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "EKSPEDISI",
                    align: "center",
                    sortable: false,
                    width: "7.5%",
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
                    width: "7%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ],

            report_url: ''
        }
    },

    computed : {
        orders () {
            return this.$store.state.warehouse.orders
        },

        dialog_delete () {
            return this.$store.state.dialog_delete
        },

        order_id () {
            return this.$store.state.warehouse.selected_order.L_SalesOrderID
        },

        edate : {
            get () { return this.$store.state.warehouse.edate },
            set (v) { this.$store.commit('warehouse/set_edate', v) }
        },

        sdate : {
            get () { return this.$store.state.warehouse.sdate },
            set (v) { this.$store.commit('warehouse/set_sdate', v) }
        },

        query : {
            get () { return this.$store.state.warehouse.query },
            set (v) { this.$store.commit('warehouse/set_common', ['query', v]) }
        },

        dialog_report : {
            get () { return this.$store.state.dialog_print },
            set (v) { this.$store.commit('set_dialog_print', v) }
        },

        expeditions () {
            return this.$store.state.warehouse.expeditions
        },

        selected_expedition : {
            get () {
                return this.$store.state.warehouse.selected_expedition
            },
            set (v) {
                this.$store.commit('warehouse/set_selected_expedition', v)
                this.search()
            }
        },

        sent_statuses () {
            return this.$store.state.warehouse.sent_statuses
        },

        selected_sent_status : {
            get () {
                return this.$store.state.warehouse.selected_sent_status
            },
            set (v) {
                this.$store.commit('warehouse/set_selected_sent_status', v)
                this.search()
            }
        },

        dialog_manifest : {
            get () { return this.$store.state.dialog_manifest },
            set (v) { this.$store.commit('set_dialog_manifest', v) }
        },

        manifests () {
            return this.$store.state.warehouse.manifests
        },

        curr_page () {
            return this.$store.state.warehouse.current_page
        },

        xtotal_page () {
            return this.$store.state.warehouse.total_page
        },
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
            this.$store.commit('warehouse/set_selected_order', x)
        },

        show (x) {
            this.select(x)
            this.$store.dispatch('warehouse/search_item')
            this.$store.commit('warehouse/set_common', ['dialog_item', true])

            this.$store.commit('warehouse/set_common', ['weight_add', x.L_SoAddWeight])
            this.$store.commit('warehouse/set_common', ['weight_total', x.L_SoTotalWeight])
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
            this.$store.dispatch('warehouse/search', {})
        },

        print_sj (x) {
            this.select(x)
            let so = x
            this.report_url = this.$store.state.warehouse.URL+"report/one_wh_001_a5?soid="+so.L_SoID
            this.$store.commit('set_dialog_print', true)
        },

        send (x) {
            this.select(x)
            this.$store.commit('warehouse/set_common', ['dialog_item', true])
        },

        receipt (x) {
            this.select(x)
            this.$store.commit('warehouse/set_common', ['courier_note', ''])
            this.$store.commit('warehouse/set_common', ['receipt_no', ''])
            this.$store.commit('warehouse/set_common', ['dialog_receipt', true])
        },

        receipt_edit (x) {
            this.select(x)
            this.$store.commit('warehouse/set_common', ['courier_note', x.W_CourierNote])
            this.$store.commit('warehouse/set_common', ['receipt_no', x.W_CourierReceiptNo])
            this.$store.commit('warehouse/set_common', ['dialog_receipt', true])
        },

        status (x) {
            if (x.W_CourierSent == 'Y')
                return x.M_OrderStatusName.split(';')
            if (x.W_CourierProcessing == 'Y')
                return ['Sedang Diproses']
            return ['Menunggu Diproses']
        },

        process (x) {
            this.select(x)
            this.$store.commit('set_dialog_confirm', true)
        },

        do_process () {
            this.$store.dispatch('warehouse/process')
        },

        waybill (x) {
            this.select(x)
            this.$store.commit('warehouse/set_common', ['receipt_no', x.W_CourierReceiptNo])
            this.$store.commit('warehouse/set_common', ['courier_note', x.W_CourierNote])
            this.$store.dispatch('warehouse/waybill')
        },

        print_me (x) {
            let URL = this.$store.state.warehouse.URL
            if (x == 'logbook') {
                let params = ['sdate='+this.sdate, 'edate='+this.edate, 'exp_id='+((this.selected_expedition)?this.selected_expedition.M_ExpeditionID:0)]
                this.report_url = URL+'report/one_wh_002?'+params.join('&')
                
            }
            
            this.dialog_report = true
            return
        },

        change_page (x) {
            this.$store.commit('warehouse/set_common', ['current_page', x])
            this.$store.dispatch('warehouse/search', {})
        }
    },

    mounted () {
        this.$store.dispatch('warehouse/search')
    }
}
</script>