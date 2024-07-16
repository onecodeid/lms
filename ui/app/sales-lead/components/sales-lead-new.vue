<template>
    <v-dialog v-model="dialog" :height="height" scrollable persistent max-width="100%" transition="dialog-transition"
        content-class="zalfa-dialog-item">
        <v-card>
            <v-card-title primary-title class="primary white--text pt-2 pb-2">
                <v-layout row wrap>
                    <v-flex xs6>
                        <h3 class="headline">LEAD BARU</h3>
                    </v-flex>
                    <v-flex xs6>
                        <!-- <v-text-field
                            solo
                            hide-details
                            placeholder="Pencarian" v-model="query"
                            @change="search"
                        >
                            <template v-slot:append-outer>
                                <v-btn color="primary" class="ma-0 btn-icon" @click="search">
                                    <v-icon>search</v-icon>
                                </v-btn>      
                            </template>
</v-text-field> -->
                    </v-flex>
                </v-layout>
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs8 v-show="false">
                        <v-layout row wrap>
                            <v-flex xs6 v-for="(item, i) in items" :key="i" pl-1 pr-1 mb-1>
                                <v-card>
                                    <v-card-text class="pa-1" v-bind:class="is_selected_class(item)" @click="add(item)">
                                        <v-layout row wrap>
                                            <v-flex xs1>
                                                <v-img
                                                    :src="item.img_url ? item.img_url : '../assets/img/zalfa-item-img.png'"
                                                    height="40"></v-img>
                                            </v-flex>
                                            <v-flex xs11 pl-2>
                                                <div class="caption vertical-center">
                                                    {{ item.M_ItemName }}<br />
                                                    Harga
                                                    <span class="" v-show="item.price_sale == 0">Rp
                                                        {{ one_money(item.M_PriceTotal) }}</span>
                                                    <span class="red--text" v-show="item.price_sale > 0"
                                                        style="text-decoration:line-through">Rp
                                                        {{ one_money(item.M_PriceTotal) }}</span>
                                                    <span class="" v-show="item.price_sale > 0"> Promo Rp
                                                        {{ one_money(item.price_sale) }}</span>
                                                    <span class="red--text ml-2"
                                                        v-show="item.I_StockQty == 0 || item.I_StockQty == null">(Out of
                                                        stock)</span>
                                                </div>
                                            </v-flex>
                                        </v-layout>


                                    </v-card-text>
                                </v-card>
                            </v-flex>

                            <v-flex xs6 v-for="(item, i) in packets" :key="'p-' + i" pl-1 pr-1 mb-1>
                                <v-card>
                                    <v-card-text class="pa-1" v-bind:class="is_selected_class(item, true)"
                                        @click="add(item, true)">
                                        <v-layout row wrap>
                                            <v-flex xs2>
                                                <v-img
                                                    :src="item.packet_img ? item.packet_img : '../assets/img/zalfa-item-img.png'"></v-img>
                                            </v-flex>
                                            <v-flex xs10 pl-2>
                                                <div class="caption vertical-center">
                                                    {{ item.M_PacketName }}<br />
                                                    Harga
                                                    <span v-show="item.packet_sale_price == 0">Rp
                                                        {{ one_money(item.price_sale) }}</span>
                                                    <span v-show="item.packet_sale_price > 0" class="red--text"
                                                        style="text-decoration:line-through">Rp
                                                        {{ one_money(item.price_sale) }}</span>
                                                    <span v-show="item.packet_sale_price > 0">Promo Rp
                                                        {{ one_money(item.packet_sale_price) }}</span>
                                                </div>
                                            </v-flex>
                                        </v-layout>


                                    </v-card-text>
                                </v-card>
                            </v-flex>
                        </v-layout>
                    </v-flex>
                    <v-flex xs6 pl-2>
                        <div style="position:sticky;top:0">
                            <v-layout>
                                <v-flex xs12>
                                    <v-tabs v-model="mainTab" color="cyan" dark slider-color="yellow" class="mb-2">
                                        <v-tab ripple :disabled="!!edit&&!selectedCustomer">EXISTING CUSTOMER</v-tab>
                                        <v-tab ripple :disabled="!!edit&&!!selectedCustomer">LEAD BARU</v-tab>

                                        <v-tab-item>
                                            <sales-lead-new-search></sales-lead-new-search>
                                        </v-tab-item>

                                        <v-tab-item>
                                            <v-card>
                                                <v-card-text class="grey lighten-5">
                                                    <!-- <v-layout row wrap>
                                                        <v-flex xs8>
                                                            <v-select
                                                                :items="leadsources"
                                                                item-value="leadsource_id"
                                                                item-text="leadsource_name"
                                                                return-object
                                                                v-model="selected_leadsource"
                                                                label="Sumber Lead"
                                                            ></v-select>
                                                        </v-flex>

                                                        <v-flex xs4 pl-3>
                                                            <v-select
                                                                :items="leadtypes"
                                                                item-value="leadtype_id"
                                                                item-text="leadtype_name"
                                                                return-object
                                                                v-model="selected_leadtype"
                                                                label="Jenis Lead"
                                                                clearable
                                                            ></v-select>
                                                        </v-flex>
                                                    </v-layout> -->
                                                    <v-text-field label="Nama Prospek"
                                                        placeholder="Tulis nama yang lengkap" v-model="lead_name"
                                                        :clearable="true" :error="lead_name.length < 1"
                                                        :error-messages="lead_name.length < 1 ? ['Wajib diisi'] : ''"
                                                        :error-count="lead_name.length < 1 ? 1 : 0">
                                                        <!-- <template slot="append">
                                                            <v-btn color="btn-icon success" small @click="select_customer"><v-icon>person_pin</v-icon></v-btn>
                                                        </template> -->
                                                    </v-text-field>

                                                    <v-text-field label="Telp / HP Prospek"
                                                        placeholder="Tulis nomor HP tanpa awalan 0 / 62"
                                                        v-model="lead_phone" :clearable="true"
                                                        :error="lead_phone.length < 1"
                                                        :error-messages="lead_phone.length < 1 ? ['Wajib diisi'] : ''"
                                                        :error-count="lead_phone.length < 1 ? 1 : 0" prefix="+62">
                                                    </v-text-field>

                                                    <v-textarea label="Alamat Lengkap"
                                                        placeholder="Kota, Kecamatan, Kelurahan RT RW" rows="2"
                                                        v-model="lead_address" :readonly="false">
                                                    </v-textarea>

                                                    <v-autocomplete v-model="selected_city" :items="cities"
                                                        :search-input.sync="search_city" auto-select-first no-filter
                                                        return-object :clearable="true" item-text="full_city_reverse"
                                                        :loading="loading_city" no-data-text="Pilih Kota / Kecamatan"
                                                        label="Kota / Kecamatan / Kelurahan"
                                                        placeholder="Ketikkan minimal 3 huruf" v-show="true"
                                                        v-if="!selected_city">
                                                        <template slot="item" slot-scope="{ item }">
                                                            <v-list-tile-content>
                                                                <v-list-tile-title
                                                                    v-text="item.full_city_reverse"></v-list-tile-title>
                                                            </v-list-tile-content>
                                                        </template>

                                                    </v-autocomplete>

                                                    <v-text-field label="Kota / Kecamatan / Kelurahan"
                                                        :value="!!selected_city ? selected_city.full_city_reverse : ''"
                                                        readonly v-show="!!selected_city" clearable
                                                        @click:clear="selected_city = null"></v-text-field>
                                                </v-card-text>
                                            </v-card>
                                        </v-tab-item>
                                    </v-tabs>

                                    <v-layout row wrap>
                                        <v-flex xs8>
                                            <v-select :items="leadsources" item-value="leadsource_id"
                                                item-text="leadsource_name" return-object v-model="selected_leadsource"
                                                label="Sumber Lead" outline dense></v-select>
                                        </v-flex>

                                        <v-flex xs4 pl-3>
                                            <v-select :items="leadtypes" item-value="leadtype_id"
                                                item-text="leadtype_name" return-object v-model="selected_leadtype"
                                                label="Jenis Lead" outline dense></v-select>
                                        </v-flex>
                                    </v-layout>

                                    <v-layout row wrap>
                                        <v-flex xs6 pr-3>
                                            <v-select :items="leadGreetings" item-value="attr_id" item-text="attr_name"
                                        label="Tipe greeting" v-model="selectedLeadGreeting"></v-select>
                                        </v-flex>
                                        <v-flex xs6>
                                            <v-text-field label="Kode Iklan" v-model="leadAdsNumber" placeholder="000XXXXX"></v-text-field>
                                        </v-flex>
                                    </v-layout>
                                    

                                    <!-- <v-layout row wrap>
                                        <v-flex xs12 class="font-weight-bold" pa-1>
                                            PRODUK YANG DIMINATI
                                        </v-flex>
                                        <v-flex xs12>
                                            <v-data-table 
                                                :headers="headers"
                                                :items="details"
                                                :loading="false"
                                                hide-actions
                                                class="elevation-1 qo-detail-table">
                                                <template slot="items" slot-scope="props">
                                                    <td class="text-xs-center pa-0" @click="select(props.item)">
                                                        <v-btn color="red" class="btn-icon ma-0" small @click="del(props.index)" dark><v-icon>delete</v-icon></v-btn>
                                                    </td>
                                                    <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.item_name }}</td>
                                                    <td class="text-xs-right pa-2" @click="select(props.item)">{{ one_money(props.item.item_price) }}</td>
                                                    
                                                    
                                                </template>
                                            </v-data-table>
                                        </v-flex>
                                    </v-layout>  -->
                                </v-flex>
                            </v-layout>

                            <v-layout>
                                <v-flex xs12>
                                    <v-autocomplete :items="leadProblems" item-value="attr_id" item-text="attr_name"
                                        multiple chips label="Problem kulit"
                                        v-model="selectedLeadProblem"></v-autocomplete>

                                    <v-autocomplete :items="itemsX" item-value="item_id" item-text="item_name" multiple
                                        chips outline label="Produk yang direkomendasikan" v-model="selectedItems"
                                        return-object></v-autocomplete>

                                    <v-btn-toggle v-model="leadClose" mandatory class="mb-3 pa-1" style="width:100%">
                                        <v-btn flat color="success" value="Y" large>
                                            Closing
                                        </v-btn>
                                        <v-btn flat color="red" value="N" large>
                                            Belum Closing
                                        </v-btn>
                                        <v-select :items="leadPrecloses" item-value="attr_id" item-text="attr_name"
                                            chips solo dense label="Jika belum closing, apa alasannya"
                                            hint="Jika CLOSING, silahkan dikosongi kolom ini" clearable
                                            v-model="selectedLeadPreclose" :disabled="leadClose == 'Y'" prefix="Alasan : "
                                            hide-details class="elevation-0 ml-2"></v-select>
                                    </v-btn-toggle>
                                </v-flex>
                            </v-layout>

                            

                        </div>
                    </v-flex>

                    <v-flex xs6 pl-3>
                        <sales-lead-new-followup></sales-lead-new-followup>
                    </v-flex>
                </v-layout>
            </v-card-text>
            <v-card-actions class="primary lighten-3">
                <v-layout row wrap>
                    <v-flex xs4>

                    </v-flex>
                    <v-flex xs4>

                    </v-flex>
                    <v-flex xs4 class="text-xs-right">
                        <v-spacer></v-spacer>
                        <v-btn color="red" dark @click="dialog = !dialog">Tutup</v-btn>
                        <v-btn color="primary" @click="save" :disabled="!btn_save_enable">Simpan</v-btn>
                    </v-flex>
                </v-layout>
            </v-card-actions>
        </v-card>

    </v-dialog>
</template>

<style>
.v-text-field.v-text-field--solo .v-input__control {
    min-height: 36px;
}

.vertical-center {
    margin: 0;
    position: absolute;
    top: 50%;
    -ms-transform: translateY(-50%);
    transform: translateY(-50%);
}

.zalfa-dialog-item {
    margin: 12px !important;
    max-height: 95% !important;
}

.zalfa-dialog-item .v-text-field.v-text-field--solo .v-input__append-outer {
    margin-top: 0px;
    margin-left: 0px;
}

.qo-detail-table table thead tr {
    height: auto
}

.zalfa-card-dashed {
    border: dashed 1px red !important
}
</style>
<script>
module.exports = {
    components: {
        // "sales-order-expedition":httpVueLoader('./sales-order-expedition.vue?1234'),
        "sales-lead-new-followup":httpVueLoader('./sales-lead-new-followup.vue?1234'),
        "sales-lead-new-search": httpVueLoader('./sales-lead-new-search.vue')
    },

    data() {
        return {
            toggle_one: "Y",
            active: null,
            text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
            headers: [
                {
                    text: "#",
                    align: "center",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "NAMA ITEM",
                    align: "left",
                    sortable: false,
                    width: "75%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                // {
                //     text: "QTY",
                //     align: "center",
                //     sortable: false,
                //     width: "15%",
                //     class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                // },
                {
                    text: "HARGA",
                    align: "right",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ]
        }
    },

    computed: {

        ...Vuex.mapState('saleslead_new',
            ['customers', 'items', 'packets', 'details', 'cities', 'leadsources',
                'leadtypes', 'total',
                'leadProblems', 'leadGreetings', 'leadPrecloses',
                'followUps', 'edit']),

        __s() { return this.$store.state.saleslead_new },

        dialog: {
            get() { return this.$store.state.saleslead_new.dialog_quick },
            set(v) { this.$store.commit('saleslead_new/set_common', ['dialog_quick', v]) }
        },

        itemsX() {
            let items = []
            for (let x of this.items)
                items.push({ item_id: x.M_ItemID, item_name: x.M_ItemName, item_qty: 1, item_price: (x.price_sale == 0 ? x.M_PriceAmount : x.price_sale), item_disc: 0, item_discrp: (x.price_sale == 0 ? x.M_PriceDiscTotal : 0), item_subtotal: (x.price_sale == 0 ? x.M_PriceTotal : x.price_sale), item_weight: x.M_ItemWeight, is_packet: 'N' })
            for (let x of this.packets)
                items.push({ item_id: x.M_PacketID, item_name: x.M_PacketName, item_qty: 1, item_price: (x.packet_sale_price > 0 ? x.packet_sale_price : x.price_sale), item_disc: 0, item_discrp: 0, item_subtotal: (x.packet_sale_price > 0 ? x.packet_sale_price : x.price_sale), item_weight: x.M_PacketTotalWeight, is_packet: 'Y' })

            return items
        },

        height() {
            return window.innerHeight * 0.95
        },

        selected_city: {
            get() { return this.$store.state.saleslead_new.selected_city },
            set(v) { this.$store.commit('saleslead_new/set_selected_city', v) }
        },

        search_city: {
            get() { return this.$store.state.saleslead_new.search_city },
            set(v) { this.$store.commit('saleslead_new/set_common', ['search_city', v]) }
        },

        selected_leadsource: {
            get() { return this.$store.state.saleslead_new.selected_leadsource },
            set(v) { this.$store.commit('saleslead_new/set_selected_leadsource', v) }
        },

        selected_leadtype: {
            get() { return this.$store.state.saleslead_new.selected_leadtype },
            set(v) { this.$store.commit('saleslead_new/set_selected_leadtype', v) }
        },

        exp_cost() {
            return this.$store.state.saleslead_new.courier_cost
        },

        cod_cost() {
            return 0
        },

        grand_total() {
            return parseFloat(this.total) + parseFloat(this.exp_cost) + parseFloat(this.cod_cost)
        },

        lead_name: {
            get() { return this.$store.state.saleslead_new.lead_name },
            set(v) { this.$store.commit('saleslead_new/set_common', ['lead_name', v]) }
        },

        lead_address: {
            get() { return this.$store.state.saleslead_new.lead_address },
            set(v) { this.$store.commit('saleslead_new/set_common', ['lead_address', v]) }
        },

        cust_postcode: {
            get() { return this.$store.state.saleslead_new.cust_postcode },
            set(v) { this.$store.commit('saleslead_new/set_common', ['cust_postcode', v]) }
        },

        lead_phone: {
            get() { return this.$store.state.saleslead_new.lead_phone },
            set(v) { this.$store.commit('saleslead_new/set_common', ['lead_phone', v]) }
        },

        cust_id() {
            return this.$store.state.saleslead_new.cust_id
        },

        cust_city() {
            return this.$store.state.saleslead_new.cust_city
        },

        loading_city() {
            return this.$store.state.saleslead_new.loading_city
        },

        btn_save_enable() {
            // if (this.lead_name == '' || this.lead_address == '' || this.lead_phone == '')
            //     return false
            // if (this.cust_postcode.length < 5)
            // return false
            // if (!this.expedition_set)
            // return false
            // if (this.details.length < 1)
            // return false
            // if (!this.selected_payment)
            //     return false
            return true
        },

        weight_total() {
            return this.$store.state.saleslead_new.weight_total
        },

        payments() {
            return this.$store.state.saleslead_new.payments
        },

        selected_payment: {
            get() { return this.$store.state.saleslead_new.selected_payment },
            set(v) { this.$store.commit('saleslead_new/set_selected_payment', v) }
        },

        expedition_set() {
            let exp = this.selected_expedition
            if (!exp) return false
            if (exp.M_ExpeditionCode != 'EX.FREE' && exp.M_ExpeditionCode != 'EX.OTHER')
                if (!this.selected_service) return false
            return true
        },

        ex_other() {
            return this.$store.state.saleslead_new.ex_other
        },

        query: {
            get() { return this.$store.state.saleslead_new.query },
            set(v) { this.$store.commit('saleslead_new/set_common', ['query', v]) }
        },

        order_note() {
            return this.$store.state.saleslead_new.order_note
        },

        selected_customer() {
            return this.$store.state.customer.selected_customer
        },

        origin() {
            return this.$store.state.saleslead_new.origin
        },

        destination() {
            return this.$store.state.saleslead_new.destination
        },

        searchCustomer: {
            get() { return this.$store.state.saleslead_new.searchCustomer },
            set(v) { this.$store.commit('saleslead_new/set_object', ['searchCustomer', v]) }
        },

        selectedCustomer: {
            get() { return this.$store.state.saleslead_new.selectedCustomer },
            set(v) {
                this.$store.commit('saleslead_new/set_object', ['selectedCustomer', v])
            }
        },

        selectedLeadProblem: {
            get() { return this.__s.selectedLeadProblem },
            set(v) { this.__c('selectedLeadProblem', v) }
        },

        selectedLeadGreeting: {
            get() { return this.__s.selectedLeadGreeting },
            set(v) { this.__c('selectedLeadGreeting', v) }
        },

        selectedLeadPreclose: {
            get() { return this.__s.selectedLeadPreclose },
            set(v) { this.__c('selectedLeadPreclose', v) }
        },

        selectedItems: {
            get() { return this.__s.selected_items },
            set(v) { this.__c('selected_items', v) }
        },

        leadClose: {
            get() { return this.__s.leadClose },
            set(v) {
                if (v == 'Y') this.selectedLeadPreclose = null
                this.__c('leadClose', v)
            }
        },

        mainTab: {
            get() { return this.__s.mainTab },
            set(v) { this.__c('mainTab', v) }
        },

        leadAdsNumber: {
            get() { return this.__s.leadAdsNumber },
            set(v) { this.__c('leadAdsNumber', v) }
        }
    },

    methods: {
        __c(a, b) { this.$store.commit('saleslead_new/set_object', [a, b]) },
        __d(a, b) { return this.$store.dispatch("saleslead_new/" + a, b) },

        one_money(x) {
            return window.one_money(x)
        },

        add(x, packet) {

            // If exist, delete
            if (this.is_selected(x, packet)) {
                let i = -1
                for (let n in this.details) {
                    let d = this.details[n]
                    console.log(d)
                    console.log(x)
                    if (!!packet) {
                        if (d.item_id == x.M_PacketID && d.is_packet == "Y") i = n
                    }
                    else {
                        if (d.item_id == x.M_ItemID && d.is_packet == "N") i = n
                    }
                }

                if (i > -1) {
                    this.del(i)
                    return
                }
            }

            let items = this.$store.state.saleslead_new.details
            let status = true

            if (!packet) {
                for (let i in items)
                    if (items[i].item_id == x.M_ItemID && items[i].is_packet == 'N') status = false

                if (status) {
                    if (x.price_sale == 0)
                        items.push({ item_id: x.M_ItemID, item_name: x.M_ItemName, item_qty: 1, item_price: x.M_PriceAmount, item_disc: 0, item_discrp: x.M_PriceDiscTotal, item_subtotal: x.M_PriceTotal, item_weight: x.M_ItemWeight, is_packet: 'N' })
                    else
                        items.push({ item_id: x.M_ItemID, item_name: x.M_ItemName, item_qty: 1, item_price: x.price_sale, item_disc: 0, item_discrp: 0, item_subtotal: x.price_sale, item_weight: x.M_ItemWeight, is_packet: 'N' })
                    this.$store.commit('saleslead_new/set_details', items)
                }
            } else {
                for (let i in items)
                    if (items[i].item_id == x.M_PacketID && items[i].is_packet == 'Y') status = false

                if (status) {
                    if (x.packet_sale_price == 0)
                        items.push({ item_id: x.M_PacketID, item_name: x.M_PacketName, item_qty: 1, item_price: x.price_sale, item_disc: 0, item_discrp: 0, item_subtotal: x.price_sale, item_weight: x.M_PacketTotalWeight, is_packet: 'Y' })
                    else
                        items.push({ item_id: x.M_PacketID, item_name: x.M_PacketName, item_qty: 1, item_price: x.price_sale, item_disc: 0, item_discrp: 0, item_subtotal: x.packet_sale_price, item_weight: x.M_PacketTotalWeight, is_packet: 'Y' })
                    this.$store.commit('saleslead_new/set_details', items)
                }
            }

        },

        del(i) {
            let items = this.$store.state.saleslead_new.details
            items.splice(i, 1)
            this.$store.commit('saleslead_new/set_details', items)
        },

        is_selected(i, packet) {
            let items = this.$store.state.saleslead_new.details
            for (let item of items)
                if (packet) {
                    if (item.item_id == i.M_PacketID && item.is_packet == 'Y')
                        return true
                } else {
                    if (item.item_id == i.M_ItemID && item.is_packet == 'N')
                        return true
                }

            return false
        },

        is_selected_class(i, packet) {
            if (packet) {
                if (this.is_selected(i, packet)) return 'cyan lighten-4'
            } else {
                if (this.is_selected(i)) return 'cyan lighten-4'
            }

            return ''
        },

        thr_search: _.debounce(function () {
            this.$store.dispatch("saleslead_new/search_city")
        }, 700),

        edit_expedition() {
            this.$store.commit('saleslead_new/set_common', ['dialog_expedition', true])
        },

        save() {
            this.$store.dispatch('saleslead_new/save')
        },

        select(x) {
            return
        },

        select_customer() {
            this.$store.commit('saleslead_new/set_common', ['dialog_customer', true])
        },

        clear_me() {
            this.$store.commit('saleslead_new/set_common', ['cust_id', 0])
        },

        search() {
            this.$store.dispatch('saleslead_new/search_item')
            this.$store.dispatch('saleslead_new/search_packet')
        },

        note() {
            this.$store.commit('saleslead_new/set_common', ['dialog_note', true])
        },

        thr_search_2: _.debounce(function () {
            this.$store.dispatch("saleslead_new/searchCustomer")
        }, 700),

        fuAdd () {
            let x = structuredClone(this.followUps)
            x.push({fu_id:0})
            this.__c("followUps", x)
        }
    },

    mounted() {
        this.$store.dispatch('saleslead_new/search_item')
        this.$store.dispatch('saleslead_new/search_leadsource')
        this.$store.dispatch('saleslead_new/search_leadtype')

        this.__d('searchLeadAttr', 'LEAD.PROBLEM').then(d => {
            this.__d('searchLeadAttr', 'LEAD.GREETING').then(e => {
                this.__d('searchLeadAttr', 'LEAD.PRECLOSE').then(f => {

                })
            })
        })
        // this.$store.dispatch('saleslead_new/search_payment_expanded')
    },

    watch: {
        search_city(val, old) {
            if (val == null || typeof val == 'undefined') val = ""
            if (val == old) return
            if (this.$store.state.saleslead_new.search_status == 1) return
            this.$store.commit("saleslead_new/set_common", ['search_city', val])
            this.thr_search()
        },

        weight_total(v, o) {
            this.$store.dispatch('saleslead_new/search_service', {})
            let x = this.$store.state.saleslead_new.services
            let y = this.$store.state.saleslead_new.selected_service

            for (let i of x) {
                if (this.selected_service.service == i.service) {
                    this.$store.commit('saleslead_new/set_selected_service', i)
                    this.$store.commit('saleslead_new/set_common', ['courier_cost', this.selected_service.cost[0].value])
                }
            }

        },

        cust_id(v, o) {
            if (o != 0 && v == 0) {
                this.$store.commit('saleslead_new/set_common', ['lead_name', ''])
                this.$store.commit('saleslead_new/set_common', ['lead_address', ''])
                this.$store.commit('saleslead_new/set_common', ['cust_city', ''])
                this.$store.commit('saleslead_new/set_common', ['cust_postcode', ''])
                this.$store.commit('saleslead_new/set_common', ['lead_phone', ''])
            }
        },

        searchCustomer(val, old) {
            if (val == null || typeof val == 'undefined') val = ""
            if (val == old) return
            if (this.$store.state.saleslead_new.search_status == 1) return
            this.$store.commit("saleslead_new/set_object", ['searchCustomer', val])
            this.thr_search_2()
        }
    }
}
</script>