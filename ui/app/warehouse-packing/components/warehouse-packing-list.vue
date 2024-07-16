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

                                    <v-list-tile @click="print_me('recap_logbook')">
                                        <v-list-tile-title><v-icon small>print</v-icon> <span class="body-1">Cetak Rekap Logbook</span></v-list-tile-title>
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
                    
                    <td class="text-xs-left pa-2" @click="select(props.item)" v-show="props.item.L_SoIsDS == 'N'"><b>{{ props.item.M_CustomerName }}, {{ props.item.city_name }}</b></td>
                    <td class="text-xs-left pa-2" @click="select(props.item)" v-show="props.item.L_SoIsDS == 'Y'">
                        <b>{{ props.item.M_CustomerName }}</b>
                        <div class="purple--text">— Dropship, {{ props.item.ds_customer_name }}</div></td>
                    
                    <td class="text-xs-center pa-2" @click="select(props.item)">
                        <span v-for="(i, n) in props.item.items" v-bind:key="n" v-bind:class="n%2 == 0 ? 'cyan--text' : ''">{{n==0?'':','}} {{ i }}</span>
                        
                        </td>
                    <td class="text-xs-center pa-2" @click="select(props.item)">{{ props.item.L_SoTotalWeight / 1000 }} Kg</td>
                    <td class="text-xs-center pa-2 blue--text" @click="select(props.item)">
                        <v-spacer></v-spacer><v-img :src="'../'+props.item.M_ExpeditionLogo" height="24" width="48" position="center center" class="img-zalfa-exp-logo-small"></v-img>
                        <div>{{ props.item.M_ExpeditionName }}, {{ props.item.L_SoExpService }}</div>
                        </td>
                    
                    <td class="text-xs-center pa-2" @click="select(props.item)">
                        <div class="orange--text">{{ props.item.W_CourierProcessing == "Y" ? props.item.W_CourierProcessingDate.substr(0,16) : '-' }}</div>
                        <div class="purple--text">{{ props.item.W_CourierSent == "Y" ? props.item.W_CourierSentDate.substr(0,16) : '-' }}</div>
                    </td>

                    <td class="text-xs-center pa-1" @click="select(props.item)">
                        <v-btn block color="purple" class="btn-icon ma-0" small @click="packing(props.item)" dark v-show="props.item.S_UserID == null">
                            <v-icon class="mr-2">label</v-icon> SET</v-btn>
                        <div v-show="props.item.S_UserID != null">{{props.item.S_UserUsername}}</div>
                        <a v-show="props.item.S_UserID != null" href="javascript:;" @click="edit_packing(props.item)">[ ubah ]</a>
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
            ></v-pagination>
        </v-card-text>
        
        <common-dialog-delete :data="order_id" @confirm_del="confirm_del" v-if="dialog_delete"></common-dialog-delete>
        <common-dialog-print :report_url="report_url" v-if="dialog_report"></common-dialog-print>
        <!-- <common-dialog-confirm data="1" text="Proses transaksi tersebut ?" @confirm="do_process"></common-dialog-confirm> -->
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
        // "common-dialog-confirm" : httpVueLoader("../../common/components/common-dialog-confirm.vue"),
        "common-dialog-manifest" : httpVueLoader("../../common/components/common-dialog-manifest.vue")
    },

    data () {
        return {
            curr_page: 1,
            xtotal_page: 1,
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
                    text: "PROSES / KIRIM",
                    align: "center",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "PACKING",
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
            return this.$store.state.packing.orders
        },

        dialog_delete () {
            return this.$store.state.dialog_delete
        },

        order_id () {
            return this.$store.state.packing.selected_order.L_SalesOrderID
        },

        edate : {
            get () { return this.$store.state.packing.edate },
            set (v) { this.$store.commit('packing/set_edate', v) }
        },

        sdate : {
            get () { return this.$store.state.packing.sdate },
            set (v) { this.$store.commit('packing/set_sdate', v) }
        },

        query : {
            get () { return this.$store.state.packing.query },
            set (v) { this.$store.commit('packing/set_common', ['query', v]) }
        },

        dialog_report : {
            get () { return this.$store.state.dialog_print },
            set (v) { this.$store.commit('set_dialog_print', v) }
        },

        expeditions () {
            return this.$store.state.packing.expeditions
        },

        selected_expedition : {
            get () {
                return this.$store.state.packing.selected_expedition
            },
            set (v) {
                this.$store.commit('packing/set_selected_expedition', v)
                this.search()
            }
        },

        sent_statuses () {
            return this.$store.state.packing.sent_statuses
        },

        selected_sent_status : {
            get () {
                return this.$store.state.packing.selected_sent_status
            },
            set (v) {
                this.$store.commit('packing/set_selected_sent_status', v)
                this.search()
            }
        },

        dialog_manifest : {
            get () { return this.$store.state.dialog_manifest },
            set (v) { this.$store.commit('set_dialog_manifest', v) }
        },

        manifests () {
            return this.$store.state.packing.manifests
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        },

        reverse_date(x) {
            return x.substr(0,10).split('-').reverse().join('-')
        },

        add () {},

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
            this.$store.commit('packing/set_selected_order', x)
        },

        show (x) {
            this.select(x)
            this.$store.dispatch('packing/search_item')
            this.$store.commit('packing/set_common', ['dialog_item', true])

            this.$store.commit('packing/set_common', ['weight_add', x.L_SoAddWeight])
            this.$store.commit('packing/set_common', ['weight_total', x.L_SoTotalWeight])
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
            this.$store.dispatch('packing/search', {})
        },

        print_sj (x) {
            this.select(x)
            let so = x
            this.report_url = this.$store.state.packing.URL+"report/one_wh_001?soid="+so.L_SoID
            this.$store.commit('set_dialog_print', true)
        },

        packing (x) {
            this.select(x)
            this.$store.commit('packing/set_common', ['edit', false])
            this.$store.commit('packing/set_selected_user', null)
            this.$store.commit('packing/set_common', ['dialog_packing', true])
        },

        edit_packing (x) {
            this.select(x)
            this.$store.commit('packing/set_common', ['edit', true])

            let y = this.$store.state.packing.users
            for (let i in y)
                if (y[i].user_id == x.S_UserID)
                    this.$store.commit('packing/set_selected_user', y[i])

            this.$store.commit('packing/set_common', ['dialog_packing', true])
        },

        // status (x) {
        //     if (x.W_CourierSent == 'Y')
        //         return x.M_OrderStatusName.split(';')
        //     if (x.W_CourierProcessing == 'Y')
        //         return ['Sedang Diproses']
        //     return ['Menunggu Diproses']
        // },

        print_me (x) {
            let URL = this.$store.state.packing.URL
            if (x == 'logbook') {
                let params = ['sdate='+this.sdate, 'edate='+this.edate, 'exp_id='+((this.selected_expedition)?this.selected_expedition.M_ExpeditionID:0)]
                this.report_url = URL+'report/one_wh_002?'+params.join('&')   
            } else if (x == 'recap_logbook') {
                let params = ['sdate='+this.sdate, 'edate='+this.edate, 'exp_id='+((this.selected_expedition)?this.selected_expedition.M_ExpeditionID:0)]
                this.report_url = URL+'report/one_wh_003?'+params.join('&')   
            }
            
            this.dialog_report = true
            return
        }
    },

    mounted () {
        this.$store.dispatch('packing/search')
        this.$store.dispatch('packing/search_expedition')
    }
}
</script>