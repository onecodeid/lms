<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs5>
                    <h3 class="display-1 font-weight-light zalfa-text-title">PENERIMAAN <span class="orange--text">TRANSAKSI</span> COD</h3>
                </v-flex>
                <v-flex xs3>
                    <v-layout row wrap>
                        <v-flex xs6>
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
                        <v-flex xs6>
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
                    </v-layout>
                    
                </v-flex>

                <v-flex xs2>
                    <v-select
                        :items="expeditions"
                        item-text="M_ExpeditionName"
                        item-value="M_ExpeditionID"
                        return-object
                        v-model="selected_expedition"
                        solo
                        placeholder="Pilih ekspedisi"
                        clearable
                        hide-details
                    >
                        <template slot="item" slot-scope="data">
                            <v-img :src="URL+'../ui/app/'+data.item.M_ExpeditionLogo" max-width="48" height="24" contain class="mr-2"></v-img>
                            {{data.item.M_ExpeditionName}}
                        </template>
                        <template slot="selection" slot-scope="data">
                            <div class="black--text">{{data.item.M_ExpeditionName}}</div><v-spacer></v-spacer>
                            <v-img :src="URL+'../ui/app/'+data.item.M_ExpeditionLogo" max-width="48" height="24" contain class="mr-2"></v-img>
                        </template>
                    </v-select>
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
                    <td class="text-xs-left pa-2" @click="select(props.item)">{{ reverse_date(props.item.F_CodDate) }}</td>
                    <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.L_SoNumber }}</td>
                    <td class="text-xs-left pa-2" @click="select(props.item)">
                        <v-layout row wrap>
                            <v-img :src="URL+'../ui/app/'+props.item.M_ExpeditionLogo" max-width="48" height="24" contain></v-img>
                            <div class="ml-2">{{props.item.M_ExpeditionName}}</div><v-spacer></v-spacer>
                        </v-layout>
                    </td>
                    <td class="text-xs-center pa-2" @click="select(props.item)">{{ props.item.F_CodOrderQty }}</td>
                    <td class="text-xs-right pa-2" @click="select(props.item)">{{ one_money(props.item.F_CodOrderTotalActual) }}</td>
                    <td class="text-xs-center py-0 pl-0 pr-1" @click="select(props.item)">
                        <div class="blue--text text-xs-center" v-show="props.item.F_CodType=='A'">PENERIMAAN<br>TRANSAKSI SUKSES</div>
                        <div class="red--text text-xs-center" v-show="props.item.F_CodType=='R'">TRANSAKSI<br>GAGAL</div>
                    </td>
                    <td class="py-1 px-2">
                        <v-btn color="primary" small class="ma-0" block @click="edit(props.item)">Detail</v-btn>
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

.img-zalfa-exp-logo-small {
    margin: auto
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
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "EKSPEDISI",
                    align: "left",
                    sortable: false,
                    width: "36.5%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "JUMLAH TRANSAKSI",
                    align: "center",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "TOTAL NOMINAL",
                    align: "right",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "STATUS",
                    align: "center",
                    sortable: false,
                    width: "12.5%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "ACTION",
                    align: "center",
                    sortable: false,
                    width: "8%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ],

            report_url: ''
        }
    },

    computed : {
        orders () {
            return this.$store.state.cod.orders
        },

        dialog_delete () {
            return this.$store.state.dialog_delete
        },

        order_id () {
            return this.$store.state.cod.selected_order.L_SalesOrderID
        },

        edate : {
            get () { return this.$store.state.cod.e_date },
            set (v) { this.$store.commit('cod/set_edate', v) }
        },

        sdate : {
            get () { return this.$store.state.cod.s_date },
            set (v) { this.$store.commit('cod/set_sdate', v) }
        },

        query : {
            get () { return this.$store.state.cod.query },
            set (v) { this.$store.commit('cod/set_common', ['query', v]) }
        },

        dialog_report : {
            get () { return this.$store.state.dialog_print },
            set (v) { this.$store.commit('set_dialog_print', v) }
        },

        statuses () {
            return this.$store.state.cod.statuses
        },

        selected_status : {
            get () { return this.$store.state.cod.selected_status },
            set (v) { 
                this.$store.commit('cod/set_selected_status', v) 
                this.search()
            }
        },

        curr_page () {
            return this.$store.state.cod.current_page
        },

        xtotal_page () {
            return this.$store.state.cod.total_page
        },

        URL () {
            return this.$store.state.cod.URL
        },

        expeditions () {
            return this.$store.state.cod.expeditions
        },

        selected_expedition : {
            get () { return this.$store.state.cod.selected_expedition },
            set (v) { 
                this.$store.commit('cod/set_selected_expedition', v) 
                this.$store.dispatch('cod/search')
            }
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
            this.$store.commit('cod/set_common', ['edit', false])
            this.$store.commit('cod/set_common', ['dialog_new', true])

            this.$store.commit('cod_new/set_common', ['date', new Date().toISOString().substr(0, 10)])
            this.$store.commit('cod_new/set_common', ['total_actual', 0])
            this.$store.commit('cod_new/set_selected_expedition', null)
            this.$store.commit('cod_new/set_selected_type', this.$store.state.cod_new.types[0])

            this.$store.commit('cod_new/set_items', [])
            this.$store.commit('cod_new/set_common', ['total_item', 0])
        },

        edit (x) {
            this.select(x)
            this.$store.commit('cod/set_common', ['edit', true])
            this.$store.commit('cod/set_common', ['dialog_new', true])

            this.$store.commit('cod_new/set_common', ['date', x.F_CodDate])
            this.$store.commit('cod_new/set_common', ['total_actual', x.F_CodOrderTotalActual])

            // -
            for (let e of this.$store.state.cod_new.expeditions)
                if (e.M_ExpeditionID == x.F_CodM_ExpeditionID)
                    this.$store.commit('cod_new/set_selected_expedition', e)

            // -
            for (let e of this.$store.state.cod_new.types)
                if (e.value == x.F_CodType)
                    this.$store.commit('cod_new/set_selected_type', e)

            this.$store.dispatch('cod/search_item')
        },

        select (x) {
            this.$store.commit('cod/set_selected_order', x)
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
            this.$store.dispatch('cod/search', {})
        },

        print_invoice (x) {
            // this.select(x)
            // let so = x
            // this.report_url = this.$store.state.cod.URL+"report/one_iv_001?soid="+so.L_SoID
            // this.$store.commit('set_dialog_print', true)
        },

        change_page (x) {
            this.$store.commit('cod/set_common', ['current_page', x])
            this.search()
        }
    },

    mounted () {
        this.$store.dispatch('cod/search')
        this.$store.dispatch('cod/search_expedition')
    }
}
</script>