<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pl-2 pr-2 pb-0">
            <v-layout row wrap>
                <v-flex xs11 sm7 mb-2>
                    <h3 class="display-1 font-weight-light zalfa-text-title">TRANSAKSI BARU</h3>
                    <!-- <test></test> -->
                </v-flex>
                <v-flex xs1 sm5 class="text-xs-right">
                    <v-btn color="success" class="btn-icon ma-0" @click="add" :disabled="!selected_customer">
                        <v-icon>add_shopping_cart</v-icon>
                    </v-btn>

                    <!-- <v-btn color="primary" class="btn-icon ma-0 mr-4" @click="save" :disabled="details.length < 1">
                         &nbsp; KE PEMBAYARAN &nbsp; <v-icon>forward</v-icon>
                    </v-btn> -->
                </v-flex>
            </v-layout>
            
        </v-card-title>
        <v-card-text class="px-2 pt-2">

            <v-layout row wrap>
                <v-flex xs12>
                    <v-select
                        :items="leadsources"
                        v-model="selected_leadsource"
                        label="Sumber Lead / Penjualan"
                        return-object
                        item-value="leadsource_id"
                        item-text="leadsource_name"
                        outline
                        hide-details
                        class="mb-2"
                    ></v-select>

                    <v-layout row wrap>
                        <v-flex xs12 sm5 pr-2>
                            <v-text-field label="Kode Iklan" v-model="sales_ads" placeholder="000XXXXX"></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm7>
                            <v-text-field label="No Pesanan MP" v-model="sales_mp" placeholder="000000000000XXX"></v-text-field>
                        </v-flex>
                    </v-layout>

                    <v-autocomplete
                        label="Pilih Customer"
                        v-model="selected_customer"
                        :items="customers"
                        :search-input.sync="search_customer"
                        auto-select-first
                        no-filter
                        return-object
                        :clearable="true"
                        item-text="M_CustomerName"
                        :loading="false"
                        no-data-text="Pilih Customer"
                        outline
                        :disabled="details.length>0 || !!selected_ds_customer"
                        :hide-details="!!selected_customer"
                        :error="selected_customer==null?true:false"
                        :error-count="selected_customer==null?1:0"
                        :error-messages="['Pilih Customer dulu sebelum pilih item / ekspedisi']"
                        >
                        <template
                            slot="item"
                            slot-scope="{ item }"
                            >
                            <v-list-tile-content>
                                <v-list-tile-title v-text="item.M_CustomerName"></v-list-tile-title>
                            <v-list-tile-sub-title v-text="item.M_CustomerLevelName + ', ' + item.M_CityName"></v-list-tile-sub-title>
                            </v-list-tile-content>
                        </template>

                    </v-autocomplete>
                </v-flex>

                <v-flex xs12>
                    <v-card depressed flat class="zalfa-card-dashed mb-2 mt-2" v-if="selected_customer&&selected_customer.customer_note.length>0">
                        <v-card-text class="pa-1">
                            <div class="caption">Catatan :</div>
                            <div v-for="(note, n) in selected_customer.customer_note" :key="n" class="caption orange--text">- {{note}}</div>
                        </v-card-text>
                    </v-card>
                </v-flex>
            </v-layout>
                <v-list two-line>
                    <v-divider
                        
                        ></v-divider>
                    <template v-for="(item, index) in details">
                        <!-- <v-subheader
                        v-if="item.header"
                        :key="item.header"
                        >
                        {{ item.header }}
                        </v-subheader> -->
                        <v-list-tile
                        :key="index"
                        avatar
                        @click="action(item)"
                        >
                        <v-list-tile-avatar>
                            <img :src="item.img_url">
                        </v-list-tile-avatar>

                        <v-list-tile-content>
                            <v-list-tile-title class="font-weight-medium" v-html="item.item_name"></v-list-tile-title>
                            <!-- <v-list-tile-sub-title v-html="'Harga : Rp '+one_money(item.M_PriceTotal)+' &nbsp;&nbsp;&nbsp; Berat : '+item.M_ItemWeight+'gr'"></v-list-tile-sub-title> -->
                            <div class="v-list__tile__sub-title">
                                <v-layout row wrap class="caption">
                                    <v-flex xs6>
                                        Harga : Rp {{one_money(item.item_price)}}
                                    </v-flex>
                                    <v-flex xs6>
                                        Qty : {{item.item_qty}}
                                    </v-flex>
                                </v-layout>
                            </div>

                            <div class="v-list__tile__sub-title">
                                <v-layout row wrap class="blue--text">
                                    <v-flex xs6>
                                        Sub Total
                                    </v-flex>
                                    <v-flex xs6 class="text-xs-right">
                                        Rp {{one_money(item.item_subtotal)}}
                                    </v-flex>
                                </v-layout>
                            </div>
                        </v-list-tile-content>
                        </v-list-tile>
                        <v-divider
                        :key="index"
                        ></v-divider>
                    </template>
                </v-list>

            <v-layout row wrap class="mb-1 mt-2">
                <!-- <v-flex xs12>
                    <v-divider class="mt-2 mb-2"></v-divider>
                </v-flex> -->
                <v-flex xs6>
                    Sub Total
                </v-flex>
                <v-flex xs6 class="text-xs-right" pr-2>
                    <h3 class="subheading blue--text"><b>Rp {{ one_money(total_price) }}</b></h3>
                </v-flex>
            </v-layout>

            <v-layout row wrap>
                <v-flex xs12>
                    <v-divider class="mt-4 mb-2"></v-divider>
                </v-flex>

                <v-flex xs12>
                    <v-checkbox label="Dropship" hide-details class="mt-0 pt-0" v-model="is_dropship" true-value="Y" false-value="N"></v-checkbox>
                </v-flex>

                <v-flex xs12>
                    <v-autocomplete
                        label="-"
                        v-model="selected_ds_customer"
                        :items="ds_customers"
                        :search-input.sync="search_ds_customer"
                        :disabled="is_dropship == 'N'"
                        auto-select-first
                        no-filter
                        return-object
                        :clearable="true"
                        item-text="M_CustomerName"
                        :loading="false"
                        no-data-text="Pilih Customer"
                        solo
                        hide-details
                        >
                        <template
                            slot="item"
                            slot-scope="{ item }"
                            >
                            <v-list-tile-content>
                                <v-list-tile-title v-text="item.M_CustomerName"></v-list-tile-title>
                            <v-list-tile-sub-title v-text="item.M_CustomerLevelName + ', ' + item.M_CityName"></v-list-tile-sub-title>
                            </v-list-tile-content>
                        </template>

                    </v-autocomplete>
                </v-flex>

                <v-flex xs12>
                    <v-divider class="mt-4 mb-2"></v-divider>
                </v-flex>
                
                <v-flex xs12 sm6 mb-1 :class="{'pr-2':$vuetify.breakpoint.smAndUp}">
                    <v-select
                        :items="expeditions"
                        v-model="selected_expedition"
                        item-text="M_ExpeditionName"
                        item-value="M_ExpeditionID"
                        label="Ekspedisi"
                        placeholder="Pilih salah satu"
                        hide-details
                        return-object
                        outline
                        :disabled="!selected_customer||details.length<1"
                    ></v-select>
                </v-flex>

                <v-flex xs12 sm6>
                    <v-select
                        :items="services"
                        v-model="selected_service"
                        item-text="service"
                        item-value="service"
                        label="Service"
                        placeholder="Pilih salah satu"
                        hide-details
                        return-object
                        :loading="loading_service"
                        outline
                        :disabled="!selected_customer||details.length<1"
                    ></v-select>
                </v-flex>

                <v-flex xs12>
                    <v-divider class="mt-2 mb-2"></v-divider>
                </v-flex>

                <v-flex xs7 mb-2>
                    Estimasi Biaya Kirim ({{total_weight}} gr) 
                    <div class="caption blue--text" v-if="!!origin&&!!destination">
                                       {{origin.city}} <span class="black--text">»</span> {{destination.subdistrict_name}}, {{destination.city}}, {{destination.province}}</div>
                </v-flex>
                <v-flex xs5 class="text-xs-right" pr-2 mb-2>
                    Rp {{ one_money(courier_cost) }}
                </v-flex>

                <v-flex xs3>
                    Grand Total
                </v-flex>
                <v-flex xs9 class="text-xs-right" pr-2>
                    <h3 class="headline">Rp {{ one_money(grand_total_price) }}</h3>
                </v-flex>

                <v-flex xs12>
                    <v-divider class="mt-4 mb-2"></v-divider>
                </v-flex>

                
            </v-layout>

            <!-- <v-data-table 
                :headers="headers"
                :items="details"
                :loading="false"
                hide-actions
                class="elevation-1">
                <template slot="items" slot-scope="props">
                    <td class="text-xs-left pa-2" @click="select(props.item)">
                        <v-btn color="red btn-icon" dark small @click="del(props.index)"><v-icon small>delete</v-icon></v-btn>
                    </td>
                    <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.item_name }}</td>
                    <td class="text-xs-center pa-2" @click="select(props.item)">{{ props.item.item_weight }}</td>
                    <td class="text-xs-left pa-1" @click="select(props.item)">
                        <v-text-field
                            label=""
                            number
                            hide-details
                            :value="props.item.item_qty"
                            solo
                            type="number"
                            reverse
                            @input="change_qty(props.index, $event)"
                            class="zalfa-input-super-dense"
                        ></v-text-field>
                    </td>
                    <td class="text-xs-left pa-2 text-xs-right" @click="select(props.item)">{{ one_money(props.item.item_price) }}</td>
                    <td class="text-xs-left pa-2 text-xs-right" @click="select(props.item)">{{ one_money(props.item.item_discrp) }}</td>
                    <td class="text-xs-left pa-2 text-xs-right" @click="select(props.item)">{{ one_money(props.item.item_subtotal) }}</td>
                </template>
            </v-data-table> -->
            <v-divider></v-divider>
            <!-- <v-pagination
                style="margin-top:10px;margin-bottom:10px"
                v-model="curr_page"
                :length="xtotal_page"
            ></v-pagination> -->

            
        </v-card-text>

        <v-card-actions>
            <v-layout row wrap>
                <v-flex xs8>

                    <v-layout row wrap mt-4>
                        <!-- <v-flex xs4 pl-2>
                            <h3 class="blue--text">Metode Pembayaran</h3>
                        </v-flex>
                        <v-flex xs1>
                            &nbsp;
                        </v-flex> -->
                        <v-flex xs4>
                            
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap mt-1>
                        <!-- <v-flex xs4 pl-2>
                            <v-select
                                :items="payments"
                                v-model="selected_payment"
                                item-text="M_PaymentTypeName"
                                item-value="M_PaymentTypeID"
                                return-object
                                hide-details
                                solo
                                placeholder="Pilih salah satu"
                                color="primary"
                            >
                            
                            </v-select>
                            
                        </v-flex>
                        <v-flex xs1>
                            &nbsp;
                        </v-flex> -->
                        <v-flex xs6>
                            
                        </v-flex>
                    </v-layout>
                </v-flex>
                
            </v-layout>
        </v-card-actions>
        <sales-order-seller-add-newitem></sales-order-seller-add-newitem>
        <common-dialog-confirm
            text="APAKAH DATA YANG ANDA MASUKKAN SUDAH BENAR? Setelah konfirmasi, data akan dikirimkan ke Admin !"
            btn_cancel="Sebentar, Saya mau ubah data"
            btn_confirm="Simpan"
            :data="1"
            @confirm="do_save"
        ></common-dialog-confirm>
        <common-dialog-progress></common-dialog-progress>
        <common-dialog-warning :text="text_warning" v-if="dialog_warning"></common-dialog-warning>
        <sales-order-seller-add-action></sales-order-seller-add-action>
        <sales-order-seller-add-change-qty></sales-order-seller-add-change-qty>
    </v-card>
</template>

<style scoped>
.zalfa-input-super-dense .v-input__control {
    min-height: 36px !important;
}

.zalfa-card-dashed {
    border: dashed 1px red
}
</style>

<script>
module.exports = {
    components : {
        "sales-order-seller-add-newitem" : httpVueLoader("./sales-order-seller-add-newitem.vue"),
        "common-dialog-confirm" : httpVueLoader("../../common/components/common-dialog-confirm.vue"),
        "common-dialog-progress" : httpVueLoader("../../common/components/common-dialog-progress.vue"),
        "common-dialog-warning" : httpVueLoader("../../common/components/common-dialog-warning.vue"),
        "sales-order-seller-add-action" : httpVueLoader("./sales-order-seller-add-action.vue"),
        "sales-order-seller-add-change-qty" : httpVueLoader("./sales-order-seller-add-change-qty.vue"),
        "test" : httpVueLoader("./test.vue")
    },

    data () {
        return {
            headers: [
                {
                    text: "#",
                    align: "left",
                    sortable: false,
                    width: "5%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "NAMA BARANG",
                    align: "center",
                    sortable: false,
                    width: "33%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "BERAT (gram)",
                    align: "center",
                    sortable: false,
                    width: "7%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "QTY",
                    align: "center",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "HARGA",
                    align: "right",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "DISKON",
                    align: "right",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "SUB TOTAL",
                    align: "right",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ]
        }
    },

    computed : {
        details () {
            return this.$store.state.salesorder.items
        },

        total_price () {
            let details = this.details
            let ttl = 0
            for (let i in details)
                ttl += Math.round(details[i].item_subtotal)
            this.$store.commit('salesorder/set_common', ['total_price', ttl])

            return ttl
        },

        grand_total_price () {
            return parseFloat(this.total_price) + parseFloat(this.courier_cost)
        },

        expeditions () {
            return this.$store.state.salesorder.expeditions
        },

        selected_expedition : {
            get () { return this.$store.state.salesorder.selected_expedition },
            set (v) { 
                this.$store.commit('salesorder/set_selected_expedition', v)
                this.$store.dispatch('salesorder/search_service', {})

                // reset payment
                this.$store.commit('salesorder/set_common', ['selected_payment_id', 0])
                this.$store.dispatch('salesorder/search_payment', {})
            }
        },

        services () {
            return this.$store.state.salesorder.services
        },

        selected_service : {
            get () { return this.$store.state.salesorder.selected_service },
            set (v) { 
                this.$store.commit('salesorder/set_selected_service', v) 
                this.courier_cost = v.cost[0].value
            }
        },

        payments () {
            return this.$store.state.salesorder.payments
        },

        selected_payment : {
            get () { return this.$store.state.salesorder.selected_payment },
            set (v) { this.$store.commit('salesorder/set_selected_payment', v) }
        },

        total_weight () {
            return this.$store.state.salesorder.total_weight
        },

        courier_cost : {
            get () { return this.$store.state.salesorder.courier_cost },
            set (v) { this.$store.commit('salesorder/set_common', ['courier_cost', v]) }
        },

        ds_customers () {
            return this.$store.state.salesorder.ds_customers
        },

        selected_ds_customer : {
            get () { return this.$store.state.salesorder.selected_ds_customer },
            set (v) {
                if (typeof v == 'undefined') {
                    this.$store.commit('salesorder/set_common', ['is_dropship', 'N'])
                }
                this.$store.commit('salesorder/set_selected_ds_customer', v)
                this.$store.dispatch('salesorder/search_service', {})
            }
        },

        search_ds_customer : {
            get () { return this.$store.state.salesorder.search_ds_customer },
            set (v) { 
                this.$store.commit('salesorder/set_common', ['search_ds_customer', v])
            }
        },

        is_dropship : {
            get () { return this.$store.state.salesorder.is_dropship },
            set (v) { this.$store.commit('salesorder/set_common', ['is_dropship', v]) }
        },

        accounts () {
            return this.$store.state.salesorder.accounts
        },

        loading_service : {
            get () { return this.$store.state.salesorder.loading_service },
            set (v) { this.$store.commit('salesorder/set_common', ['loading_service', v]) }
        },

        dialog_warning : {
            get () { return this.$store.state.dialog_warning },
            set (v) { this.$store.commit('set_dialog_warning', v) }
        },

        text_warning () {
            return this.$store.state.text_warning
        },

        ex_cargo_show () {
            // if (!this.selected_expedition)
            //     return false
            // if (this.selected_expedition.M_ExpeditionCode != 'EX.OTHER')
            //     return false
            return true
        },

        customers () {
            return this.$store.state.salesorder.customers
        },

        selected_customer : {
            get () { return this.$store.state.salesorder.selected_customer },
            set (v) { this.$store.commit('salesorder/set_selected_customer', v) }
        },

        search_customer : {
            get () { return this.$store.state.salesorder.search_customer },
            set (v) { this.$store.commit('salesorder/set_common', ['search_customer', v]) }
        },

        origin () {
            return this.$store.state.salesorder.origin
        },

        destination () {
            return this.$store.state.salesorder.destination
        },

        lead_id () {
            return this.$store.state.salesorder.lead_id
        },

        leadsources () {
            return this.$store.state.salesorder.leadsources
        },

        selected_leadsource : {
            get () { return this.$store.state.salesorder.selected_leadsource },
            set (v) { this.$store.commit('salesorder/set_selected_leadsource', v) }
        },

        sales_ads : {
            get () { return this.$store.state.salesorder.sales_ads },
            set (v) { this.$store.commit('salesorder/set_object', ['sales_ads', v]) }
        },

        sales_mp : {
            get () { return this.$store.state.salesorder.sales_mp },
            set (v) { this.$store.commit('salesorder/set_object', ['sales_mp', v]) }
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        },

        select (x) {
            this.$store.commit('salesorder/set_selected_item', x)
        },

        add () {
            this.$store.dispatch('newitem/search_item', {})
            this.$store.dispatch('newitem/search_packet', {})
            this.$store.commit('newitem/set_common', ['dialog_items', true] )
        },

        change_qty (i, j) {
            let items = this.$store.state.salesorder.items
            items[i].item_qty = j
            items[i].item_subtotal = (items[i].item_price - items[i].item_discrp) * items[i].item_qty

            this.$store.commit('salesorder/update_items', items)
        },

        save () {
            this.$store.commit('salesorder/set_common', ['selected_tab', 2])
            // this.$store.commit('set_dialog_confirm', true)
        },

        do_save () {
            this.$store.dispatch('salesorder/save')
        },

        thr_search: _.debounce( function () {
            this.$store.dispatch("salesorder/search_ds_customer")
        }, 700),

        thr_search_c: _.debounce( function () {
            this.$store.dispatch("salesorder/search_customer")
        }, 700),

        del (idx) {
            let itm = this.$store.state.salesorder.items
            itm.splice(idx, 1)
            this.$store.commit('salesorder/update_items', itm)
            this.$store.commit('salesorder/update_total_item', itm.length)
        },

        action (x) {
            this.select(x)
            this.$store.commit('salesorder/set_common', ['dialog_action', true])
        }
    },

    mounted () {
        this.$store.dispatch('salesorder/search_expedition', {})
        this.$store.dispatch('salesorder/search_payment', {})
        this.$store.dispatch('salesorder/search_account', {})

        // this.add()
    },

    watch : {
        total_weight (v, o) {
            this.$store.dispatch('salesorder/search_service', {})
            let x = this.$store.state.salesorder.services
            let y = this.$store.state.salesorder.selected_service

            for (let i of x) {
                if (this.selected_service.service == i.service)
                    this.selected_service = i
            }
                
        },

        search_ds_customer(val, old) {
            if (val == null || typeof val == 'undefined') val = ""
            if (val == old ) return
            if (this.$store.state.salesorder.search_status == 1 ) return  
            this.$store.commit("salesorder/set_common", ['search_ds_customer', val])
            this.$store.commit("salesorder/set_ds_customers", [])
            this.thr_search()
        },

        search_customer(val, old) {
            if (val == null || typeof val == 'undefined') val = ""
            if (val == old ) return
            if (this.$store.state.salesorder.search_status == 1 ) return  
            this.$store.commit("salesorder/set_common", ['search_customer', val])
            this.thr_search_c()
        }
    }
}
</script>