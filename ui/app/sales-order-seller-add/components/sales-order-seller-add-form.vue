<template>
    <v-card>
        <v-card-title primary-title class="pb-0 pt-2">
            <v-layout row wrap>
                <v-flex xs7>
                    <h3 class="display-1 font-weight-light zalfa-text-title">
                        TRANSAKSI BARU » <span class="grey--text font-weight-thin headline">METODE PEMBAYARAN</span></h3>
                </v-flex>
                <v-flex xs5 class="text-xs-right">
                    <v-btn color="success" class="btn-icon ma-0" @click="add" :disabled="selected_customer==null">
                        <v-icon>shopping_cart</v-icon> &nbsp; TAMBAH BARANG
                    </v-btn>

                    <v-btn color="primary" class="btn-icon ma-0 mr-4" @click="save" :disabled="details.length < 1 || !expedition_set">
                         &nbsp; KE PEMBAYARAN &nbsp; <v-icon>forward</v-icon>
                    </v-btn>
                </v-flex>
            </v-layout>
            
        </v-card-title>
        <v-card-text class="pb-0 pt-2">
            <v-layout row wrap>
                <v-flex xs3 pr-2>
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

                    <v-layout>
                        <v-flex xs12 lg5 mr-2>
                            <v-text-field label="Kode Iklan" v-model="sales_ads" placeholder="000XXXXX"></v-text-field>
                        </v-flex>
                        <v-flex xs12 lg7>
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
                        solo
                        :disabled="details.length>0 || !!selected_ds_customer"
                        :error="selected_customer==null?true:false"
                        :error-count="selected_customer==null?1:0"
                        :error-messages="['Pilih Customer dulu sebelum pilih item / ekspedisi']"
                        :hide-details="selected_customer&&selected_customer.customer_note.length>0"
                        v-show="!selected_customer"
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

                    <v-text-field
                        label="Customer"
                        v-if="!!selected_customer"
                        :clearable="lead_id==0"
                        :value="selected_customer.M_CustomerName"
                        readonly
                        solo
                        @click:clear="selected_customer=null"
                    ></v-text-field>

                    <v-layout row wrap>
                        <v-flex xs12>
                            <v-card depressed flat class="zalfa-card-dashed mb-2 mt-2" v-if="selected_customer&&selected_customer.customer_note.length>0">
                                <v-card-text class="pa-1">
                                    <div class="caption">Catatan :</div>
                                    <div v-for="(note, n) in selected_customer.customer_note" :key="n" class="caption orange--text">- {{note}}</div>
                                </v-card-text>
                            </v-card>
                        </v-flex>
                    </v-layout>

                    <v-layout row wrap>
                        <v-flex xs12>
                            <v-checkbox label="Dropship" hide-details class="mt-0 pt-0" v-model="is_dropship" true-value="Y" false-value="N"></v-checkbox>
                        </v-flex>
                        <v-flex xs12>
                            <v-autocomplete
                                label="Tujuan Dropship"
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
                    </v-layout>

                    <v-layout row wrap>
                        <v-flex xs6>
                            <v-text-field
                                label="Total Berat"
                                
                                suffix="gram"
                                v-model="total_weight"
                                readonly
                                color="red"
                            ></v-text-field>
                        </v-flex>

                        <v-flex xs6>
                            
                        </v-flex>
                    </v-layout>

                    <v-select
                        :items="expeditions"
                        v-model="selected_expedition"
                        item-text="M_ExpeditionName"
                        item-value="M_ExpeditionID"
                        label="Ekspedisi"
                        placeholder="Pilih salah satu"
                        :disabled="selected_customer==null"
                        return-object
                    ></v-select>
                    
                    <v-select
                        :items="services"
                        v-model="selected_service"
                        item-text="service"
                        item-value="service"
                        label="Service"
                        placeholder="Pilih salah satu"
                        :disabled="selected_expedition==null"
                        return-object
                        :loading="loading_service"
                        v-show="!ex_cargo_show && !ex_free_show"
                    ></v-select>

                    <v-text-field
                        label="Nama Ekspedisi"
                        v-model="ex_other"
                        v-show="ex_cargo_show"
                    ></v-text-field>

                    <v-text-field
                        label="Catatan"
                        v-model="ex_note"
                        v-show="ex_cargo_show || ex_free_show"
                    ></v-text-field>

                    <v-text-field
                        label="Perkiraan Ongkos Kirim"
                        v-model="courier_cost"
                        prefix="Rp"
                        v-show="ex_cargo_show"
                    ></v-text-field>

                </v-flex>
                <v-flex xs9>
                    <v-data-table 
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
                            <!-- <td class="text-xs-left pa-0" @click="select(props.item)">
                                <v-btn color="primary" class="btn-icon ma-0" small @click="edit(props.item)"><v-icon>create</v-icon></v-btn>
                                <v-btn color="red" dark class="btn-icon ma-0" small @click="del(props.item)"><v-icon>delete</v-icon></v-btn>
                            </td> -->
                            <!-- <td class="text-xs-center pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.M_DoctorHP}}</td>
                            <td class="text-xs-left pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.status}}</td> -->
                        </template>
                    </v-data-table>

                    <v-divider class="mt-4 mb-4"></v-divider>

                    <v-layout row wrap>
                        <v-flex xs12>
                            <v-layout row wrap>
                                <v-flex xs10 class="text-xs-right pr-3">
                                    <h3>Total</h3>
                                </v-flex>
                                <v-flex xs2 class="text-xs-right pr-3">
                                    <h3>{{ one_money(total_price) }}</h3>
                                </v-flex>
                            </v-layout>

                            <v-layout row wrap class="mt-1">
                                <v-flex xs10 class="text-xs-right pr-3">
                                    Estimasi Biaya Pengiriman
                                    <div class="caption blue--text" v-if="!!origin&&!!destination">
                                       {{origin.city}} <span class="black--text">»</span> {{destination.subdistrict_name}}, {{destination.city}}, {{destination.province}}</div>
                                </v-flex>
                                <v-flex xs2 class="text-xs-right pr-3">
                                    {{ one_money(courier_cost) }}
                                </v-flex>
                            </v-layout>
                            
                            <v-layout row wrap class="mt-1">
                                <v-flex xs10 class="text-xs-right pr-3">
                                    <h3>Grand Total</h3>
                                </v-flex>
                                <v-flex xs2 class="text-xs-right pr-3">
                                    <h3>{{ one_money(grand_total_price) }}</h3>
                                </v-flex>
                            </v-layout>
                        </v-flex>
                    </v-layout>
                </v-flex>
            </v-layout>
            
            
            <!-- <v-pagination
                style="margin-top:10px;margin-bottom:10px"
                v-model="curr_page"
                :length="xtotal_page"
            ></v-pagination> -->

            
        </v-card-text>

        <v-card-actions>
            <v-layout row wrap>
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
        "common-dialog-progress" : httpVueLoader("../../common/components/common-dialog-progress.vue")
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
                if (['EX.FREE','EX.OTHER'].indexOf(v.M_ExpeditionCode) < 0)
                    this.$store.dispatch('salesorder/search_service', {})
                if (['EX.FREE'].indexOf(v.M_ExpeditionCode) > -1) { 
                    this.courier_cost = 0
                    this.ex_other = ''
                    this.ex_note = ''
                }

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

        ex_other : {
            get () { return this.$store.state.salesorder.ex_other },
            set (v) { this.$store.commit('salesorder/set_common', ['ex_other', v]) }
        },

        ex_note : {
            get () { return this.$store.state.salesorder.ex_note },
            set (v) { this.$store.commit('salesorder/set_common', ['ex_note', v]) }
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

        customers () {
            return this.$store.state.salesorder.customers
        },

        selected_customer : {
            get () { return this.$store.state.salesorder.selected_customer },
            set (v) { this.$store.commit('salesorder/set_selected_customer', v) }
        },

        search_ds_customer : {
            get () { return this.$store.state.salesorder.search_ds_customer },
            set (v) { this.$store.commit('salesorder/set_common', ['search_ds_customer', v]) }
        },

        search_customer : {
            get () { return this.$store.state.salesorder.search_customer },
            set (v) { this.$store.commit('salesorder/set_common', ['search_customer', v]) }
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

        ex_cargo_show () {
            if (!this.selected_expedition) return false
            if (this.selected_expedition.M_ExpeditionCode != 'EX.OTHER') return false
            return true
        },

        ex_free_show () {
            if (!this.selected_expedition) return false
            if (this.selected_expedition.M_ExpeditionCode != 'EX.FREE') return false
            return true
        },

        expedition_set () {
            let exp = this.selected_expedition
            if (!exp) return false
            if (exp.M_ExpeditionCode != 'EX.FREE' && exp.M_ExpeditionCode != 'EX.OTHER')
                if (!this.selected_service) return false
            return true
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
            return
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
            this.$store.dispatch('salesorder/search_payment', {}).then(d => {
                this.$store.commit('salesorder/set_common', ['selected_tab', 2])
            })
            // this.$store.commit('set_dialog_confirm', true)
        },

        do_save () {
            this.$store.dispatch('salesorder/save')
        },

        thr_search: _.debounce( function () {
            this.$store.dispatch("salesorder/search_ds_customer")
        }, 700),

        thr_search_2: _.debounce( function () {
            this.$store.dispatch("salesorder/search_customer")
        }, 700),

        del (idx) {
            let itm = this.$store.state.salesorder.items
            itm.splice(idx, 1)
            this.$store.commit('salesorder/update_items', itm)
            this.$store.commit('salesorder/update_total_item', itm.length)
        }
    },

    mounted () {
        this.$store.dispatch('salesorder/search_expedition', {})
        this.$store.dispatch('salesorder/search_payment', {})
        this.$store.dispatch('salesorder/search_account', {})
        this.$store.dispatch('salesorder/search_leadsource')

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
            this.thr_search_2()
        }
    }
}
</script>