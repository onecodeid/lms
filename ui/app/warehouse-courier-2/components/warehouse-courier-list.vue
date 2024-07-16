<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs5>
                    <h3>DAFTAR ORDER</h3>
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
                :items="orders"
                :loading="false"
                hide-actions
                class="elevation-1">
                <template slot="items" slot-scope="props">
                    <td class="text-xs-left pa-2" @click="select(props.item)">{{ reverse_date(props.item.W_CourierDate) }}
                        <div>{{ props.item.L_SoNumber }}</div>
                    </td>
                    
                    <td class="text-xs-left pa-2" @click="select(props.item)" v-show="props.item.L_SoIsDS == 'N'"><b>{{ props.item.M_CustomerName }}, {{ props.item.city_name }}</b></td>
                    <td class="text-xs-left pa-2" @click="select(props.item)" v-show="props.item.L_SoIsDS == 'Y'">
                        <b>{{ props.item.M_CustomerName }}</b>
                        <div class="purple--text">— Dropship, {{ props.item.ds_customer_name }}</div></td>
                    <td class="text-xs-center pa-2" @click="select(props.item)">{{ status(props.item) }}</td>
                    
                    <!-- <td class="text-xs-center pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.M_DoctorHP}}</td>
                    <td class="text-xs-left pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.status}}</td> -->
                </template>
            </v-data-table>
            <v-divider></v-divider>
            <v-pagination
                style="margin-top:10px;margin-bottom:10px"
                v-model="curr_page"
                :length="xtotal_page"
            ></v-pagination>
        </v-card-text>
        
        <common-dialog-delete :data="order_id" @confirm_del="confirm_del" v-if="dialog_delete"></common-dialog-delete>
        <common-dialog-print :report_url="report_url" v-if="dialog_report"></common-dialog-print>
        <common-dialog-confirm data="1" text="Proses transaksi tersebut ?" @confirm="do_process"></common-dialog-confirm>
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
        "common-dialog-print" : httpVueLoader("../../common/components/common-dialog-print.vue"),
        "common-dialog-confirm" : httpVueLoader("../../common/components/common-dialog-confirm.vue")
    },

    data () {
        return {
            curr_page: 1,
            xtotal_page: 1,
            headers: [
                {
                    text: "TANGGAL / NOMOR",
                    align: "left",
                    width: "20%",
                    sortable:false,
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "NAMA CUSTOMER",
                    align: "left",
                    sortable: false,
                    width: "60%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "STATUS",
                    align: "center",
                    sortable: false,
                    width: "20%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
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
            get () { return this.$store.state.warehouse.e_date },
            set (v) { this.$store.commit('warehouse/set_edate', v) }
        },

        sdate : {
            get () { return this.$store.state.warehouse.s_date },
            set (v) { this.$store.commit('warehouse/set_sdate', v) }
        },

        query : {
            get () { return this.$store.state.warehouse.query },
            set (v) { this.$store.commit('warehouse/set_common', ['query', v]) }
        },

        dialog_report : {
            get () { return this.$store.state.dialog_print },
            set (v) { this.$store.commit('set_dialog_print', v) }
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

        print_invoice (x) {
            let so = this.$store.state.warehouse.selected_order
            this.report_url = this.$store.state.warehouse.URL+"report/one_wh_001?soid=2"
            this.$store.commit('set_dialog_print', true)
        },

        status (x) {
            if (x.W_CourierSent == 'Y')
                return M_OrderStatusName
            if (x.W_CourierProcessing == 'Y')
                return 'Sedang Diproses'
            return 'Menunggu Diproses'
        },

        process (x) {
            this.select(x)
            this.$store.commit('set_dialog_confirm', true)
        },

        do_process () {
            this.$store.dispatch('warehouse/process')
        }
    },

    mounted () {
        this.$store.dispatch('warehouse/search')
    }
}
</script>