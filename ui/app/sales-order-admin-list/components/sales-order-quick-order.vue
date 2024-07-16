<template>
    <v-dialog
        v-model="dialog"
        :height="height"
        scrollable
        persistent
        max-width="100%"
        transition="dialog-transition"
        content-class="zalfa-dialog-item"
    >
        <v-card>
            <v-card-title primary-title class="primary white--text pt-2 pb-2">
                <v-layout row wrap>
                    <v-flex xs6>
                        <h3 class="headline">QUICK ORDER</h3>        
                    </v-flex>
                    <v-flex xs6>
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
                            </template>
                        </v-text-field>
                    </v-flex>
                </v-layout>
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs8>
                        <v-layout row wrap>
                            <v-flex xs6 v-for="(item,i) in items" :key="i" pl-1 pr-1 mb-1>
                                <v-card>
                                    <v-card-text class="pa-1" v-bind:class="is_selected_class(item)" @click="add(item)">
                                        <v-layout row wrap>
                                            <v-flex xs2>
                                                <v-img :src="item.img_url?item.img_url:'../assets/img/zalfa-item-img.png'"></v-img>
                                            </v-flex>
                                            <v-flex xs10 pl-2>
                                                <div class="caption vertical-center">
                                                    {{item.M_ItemName}}<br />
                                                    Harga 
                                                        <span class="" v-show="item.price_sale==0">Rp {{one_money(item.M_PriceTotal)}}</span>
                                                        <span class="red--text" v-show="item.price_sale>0" style="text-decoration:line-through">Rp {{one_money(item.M_PriceTotal)}}</span>
                                                        <span class="" v-show="item.price_sale>0"> Promo Rp {{one_money(item.price_sale)}}</span>
                                                    <span class="red--text ml-2" v-show="item.I_StockQty == 0 || item.I_StockQty == null">(Out of stock)</span>
                                                </div>
                                            </v-flex>
                                        </v-layout>
                                        
                                        
                                    </v-card-text>
                                </v-card>
                            </v-flex>

                            <v-flex xs6 v-for="(item,i) in packets" :key="'p-'+i" pl-1 pr-1 mb-1>
                                <v-card>
                                    <v-card-text class="pa-1" v-bind:class="is_selected_class(item, true)" @click="add(item, true)">
                                        <v-layout row wrap>
                                            <v-flex xs2>
                                                <v-img :src="item.packet_img?item.packet_img:'../assets/img/zalfa-item-img.png'"></v-img>
                                            </v-flex>
                                            <v-flex xs10 pl-2>
                                                <div class="caption vertical-center">
                                                    {{item.M_PacketName}}<br />
                                                    Harga 
                                                        <span v-show="item.packet_sale_price==0">Rp {{one_money(item.price_sale)}}</span>
                                                        <span v-show="item.packet_sale_price>0" class="red--text" style="text-decoration:line-through">Rp {{one_money(item.price_sale)}}</span>
                                                        <span v-show="item.packet_sale_price>0">Promo Rp {{one_money(item.packet_sale_price)}}</span>
                                                </div>
                                            </v-flex>
                                        </v-layout>
                                        
                                        
                                    </v-card-text>
                                </v-card>
                            </v-flex>
                        </v-layout>
                    </v-flex>
                    <v-flex xs4 pl-2>
                        <div style="position:sticky;top:0">
                            <v-layout row wrap>
                                <!-- <v-flex xs6>
                                    <v-btn :color="!!selected_expedition?'green':'orange'" @click="edit_expedition" class="white--text">
                                        <v-icon class="mr-2" small>shopping_cart</v-icon>
                                        <span v-show="!selected_expedition"><b>Pilih Ekspedisi</b></span>
                                        <span v-if="!!selected_expedition">
                                            <span v-if="selected_expedition" v-show="selected_expedition.M_ExpeditionCode!='EX.OTHER'">{{selected_expedition?selected_expedition.M_ExpeditionName:'-'}}, {{selected_service?selected_service.service:'-'}}</span>
                                            <span v-if="selected_expedition" v-show="selected_expedition.M_ExpeditionCode=='EX.OTHER'">{{selected_expedition?selected_expedition.M_ExpeditionName:'-'}}, {{ex_other}}</span>
                                        </span>
                                    </v-btn>
                                </v-flex> -->
                                <v-flex xs6>
                                    <v-select
                                        :items="leadsources"
                                        v-model="selected_leadsource"
                                        label="Sumber Lead / Penjualan"
                                        return-object
                                        item-value="leadsource_id"
                                        item-text="leadsource_name"
                                    ></v-select>
                                </v-flex>
                            </v-layout>

                            <v-layout row wrap>
                                <v-flex xs12 sm5 pr-2>
                                    <v-text-field label="Kode Iklan" v-model="sales_ads" placeholder="000XXXXX"></v-text-field>
                                </v-flex>
                                <v-flex xs12 sm7>
                                    <v-text-field label="No Pesanan MP" v-model="sales_mp" placeholder="000000000000XXX"></v-text-field>
                                </v-flex>
                            </v-layout>
                            
                            <v-text-field
                                label="Nama Customer"
                                placeholder="Tulis nama yang lengkap"
                                v-model="cust_name"
                                :readonly="cust_id != 0"
                                :clearable="cust_id != 0"
                                @click:clear="clear_me"
                                :hide-details="cust_id!=0&&selected_customer&&selected_customer.customer_note.length>0"
                                :error="cust_name.length<1"
                                :error-messages="cust_name.length<1?['Wajib diisi']:''"
                                :error-count="cust_name.length<1?1:0"
                            >
                                <template slot="append">
                                    <v-btn color="btn-icon success" small @click="select_customer"><v-icon>person_pin</v-icon></v-btn>
                                </template>
                            </v-text-field>

                            <v-layout row wrap>
                                <v-flex xs12>
                                    <v-card depressed flat class="zalfa-card-dashed mb-2 mt-2" v-if="cust_id!=0&&selected_customer&&selected_customer.customer_note.length>0">
                                        <v-card-text class="pa-1">
                                            <div class="caption">Catatan :</div>
                                            <div class="caption orange--text" v-for="(note, n) in selected_customer.customer_note" :key="n">- {{note}}</div>
                                        </v-card-text>
                                    </v-card>
                                </v-flex>
                            </v-layout>

                            <v-autocomplete
                                v-model="selected_city"
                                :items="cities"
                                :search-input.sync="search_city"
                                auto-select-first
                                no-filter
                                return-object
                                :clearable="true"
                                item-text="full_address_reverse"
                                :loading="loading_city"
                                no-data-text="Pilih Kota / Kecamatan"
                                label="Kota / Kecamatan / Kelurahan"
                                placeholder="Ketikkan minimal 3 huruf"
                                v-show="cust_id==0"
                                >
                                <template
                                    slot="item"
                                    slot-scope="{ item }"
                                    >
                                    <v-list-tile-content>
                                        <v-list-tile-title v-text="item.full_address_reverse"></v-list-tile-title>
                                    </v-list-tile-content>
                                </template>

                            </v-autocomplete>

                            <v-text-field
                                label="Kota / Kecamatan / Kelurahan"
                                v-model="cust_city"
                                readonly
                                v-show="cust_id != 0"
                            ></v-text-field>

                            <v-textarea
                                label="Alamat Lengkap"
                                placeholder="Kota, Kecamatan, Kelurahan RT RW"
                                rows="2"
                                v-model="cust_address"
                                :readonly="cust_id != 0"
                                :error="cust_address.length<1"
                                :error-messages="cust_address.length<1?['Wajib diisi']:''"
                                :error-count="cust_address.length<1?1:0">
                                
                            </v-textarea>

                            <v-layout row wrap>
                                <v-flex xs4>
                                    <v-text-field
                                        label="Kode Pos"
                                        placeholder=" "
                                        v-model="cust_postcode"
                                        :error="cust_postcode.length<5"
                                        :error-messages="cust_postcode.length<5?['Wajib diisi, 5 karakter']:''"
                                        :error-count="cust_postcode.length<5?1:0"
                                    ></v-text-field>
                                </v-flex>
                                <v-flex xs8 pl-4>
                                    <v-text-field
                                        label="No Whatsapp"
                                        placeholder="Contoh : 0898XXX"
                                        v-model="cust_phone"
                                        :error="cust_phone.length<1"
                                        :error-messages="cust_phone.length<1?['Wajib diisi']:''"
                                        :error-count="cust_phone.length<1?1:0"
                                    ></v-text-field>
                                </v-flex>
                            </v-layout>

                            <v-layout row wrap>
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
                                            <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.item_qty }}</td>
                                            <td class="text-xs-center pa-2" @click="select(props.item)">{{ one_money(props.item.item_subtotal) }}</td>
                                            
                                            
                                        </template>
                                    </v-data-table>
                                </v-flex>
                                <v-flex xs8 class="text-xs-right" mt-2>
                                    Total
                                </v-flex>
                                <v-flex xs4 class="text-xs-right" pr-3 mt-2>
                                    {{one_money(total)}}
                                </v-flex>
                                
                                <v-flex xs8 class="text-xs-right" mt-1>
                                    Ongkir
                                    <div class="caption blue--text" v-if="!!origin&&!!destination">
                                       {{origin.city}} <span class="black--text">»</span> {{destination.subdistrict_name}}, {{destination.city}}, {{destination.province}}</div>
                                </v-flex>
                                <v-flex xs4 class="text-xs-right" pr-3 mt-1>
                                    {{one_money(exp_cost)}}
                                </v-flex>

                                <v-flex xs8 class="text-xs-right" mt-1>
                                    COD
                                </v-flex>
                                <v-flex xs4 class="text-xs-right" pr-3 mt-1>
                                    {{one_money(cod_cost)}}
                                </v-flex>

                                <v-flex xs8 class="text-xs-right subheading" mt-1>
                                    <b>Grand Total</b>
                                </v-flex>
                                <v-flex xs4 class="text-xs-right subheading" pr-3 mt-1>
                                    <b>{{one_money(grand_total)}}</b>
                                </v-flex>
                            </v-layout>
                        </div>
                    </v-flex>
                </v-layout>
            </v-card-text>
            <v-card-actions class="primary lighten-3">
                <v-layout row wrap>
                    <v-flex xs4>
                        <v-btn :color="!!selected_expedition?'green':'orange'" @click="edit_expedition" class="white--text">
                            <v-icon class="mr-2" small>shopping_cart</v-icon>
                            <span v-show="!selected_expedition"><b>Pilih Ekspedisi</b></span>
                            <span v-if="!!selected_expedition">
                                <span v-if="selected_expedition" v-show="selected_expedition.M_ExpeditionCode!='EX.OTHER'">{{selected_expedition?selected_expedition.M_ExpeditionName:'-'}}, {{selected_service?selected_service.service:'-'}}</span>
                                <span v-if="selected_expedition" v-show="selected_expedition.M_ExpeditionCode=='EX.OTHER'">{{selected_expedition?selected_expedition.M_ExpeditionName:'-'}}, {{ex_other}}</span>
                            </span>
                        </v-btn>
                        <!-- <div class="white--text font-weight-bold body-2">
                            <v-icon class="mr-1 red--text">shopping_cart</v-icon>
                            Ekspedisi : 
                                <span v-if="selected_expedition" v-show="selected_expedition.M_ExpeditionCode!='EX.OTHER'">{{selected_expedition?selected_expedition.M_ExpeditionName:'-'}}, {{selected_service?selected_service.service:'-'}}</span>
                                <span v-if="selected_expedition" v-show="selected_expedition.M_ExpeditionCode=='EX.OTHER'">{{selected_expedition?selected_expedition.M_ExpeditionName:'-'}}, {{ex_other}}</span>
                            <a href="#" class="ml-2" @click="edit_expedition" 
                                v-show="expedition_set">[ Ubah ]</a>
                            <a href="#" class="ml-2 red--text" @click="edit_expedition" 
                                v-show="!expedition_set">[ Ekspedisi belum dipilih ! ]</a>
                        </div> -->
                    </v-flex>
                    <v-flex xs4>
                        <!-- <v-select
                            :items="payments"
                            v-model="selected_payment"
                            return-object
                            solo
                            hide-details
                            placeholder="Pilih metode pembayaran"
                        >
                            <template slot="item" slot-scope="data">
                                {{data.item.M_PaymentTypeName}} {{data.item.channel_id==null?'':' - '+data.item.channel_name}}
                            </template>
                            <template slot="selection" slot-scope="data">
                                {{data.item.M_PaymentTypeName}} {{data.item.channel_id==null?'':' - '+data.item.channel_name}}
                            </template>
                            <template slot="append-outer">
                                <v-btn :title="order_note" :color="order_note==''?'red':'success'" dark class="ma-0 ml-4 btn-icon" @click="note">
                                    <v-icon>description</v-icon>
                                </v-btn>
                            </template>
                        </v-select> -->
                    </v-flex>
                    <v-flex xs4 class="text-xs-right">
                        <v-spacer></v-spacer>
                        <v-btn color="red" dark @click="dialog=!dialog">Tutup</v-btn>
                        <v-btn color="primary" @click="note" :disabled="!btn_save_enable">Ke Pembayaran</v-btn>
                    </v-flex>
                </v-layout>
                
                
            </v-card-actions>
        </v-card>
        <sales-order-expedition></sales-order-expedition>
        <sales-order-payment-note></sales-order-payment-note>
        <sales-order-quick-order-remove-coupon></sales-order-quick-order-remove-coupon>
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
    components : {
        "sales-order-expedition":httpVueLoader('./sales-order-expedition.vue?1234'),
        "sales-order-payment-note":httpVueLoader('./sales-order-payment-note.vue?1234'),
        "sales-order-quick-order-remove-coupon":httpVueLoader('./sales-order-quick-order-remove-coupon.vue')
    },

    data () {
        return {
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
                    width: "55%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "QTY",
                    align: "center",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "SUBTOTAL",
                    align: "right",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ]
        }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.quickorder.dialog_quick },
            set (v) { this.$store.commit('quickorder/set_common', ['dialog_quick', v]) }
        },

        items () {
            return this.$store.state.quickorder.items
        },

        packets () {
            return this.$store.state.quickorder.packets
        },

        height () {
            return window.innerHeight * 0.95
        },

        details () {
            return this.$store.state.quickorder.details
        },

        cities () {
            return this.$store.state.quickorder.cities
        },

        selected_city : {
            get () { return this.$store.state.quickorder.selected_city },
            set (v) { this.$store.commit('quickorder/set_selected_city', v) }
        },

        search_city : {
            get () { return this.$store.state.quickorder.search_city },
            set (v) { this.$store.commit('quickorder/set_common', ['search_city', v]) }
        },

        selected_expedition () {
            return this.$store.state.quickorder.selected_expedition
        },

        selected_service () {
            return this.$store.state.quickorder.selected_service
        },

        total () {
            return this.$store.state.quickorder.total
        },

        exp_cost () {
            return this.$store.state.quickorder.courier_cost
        },

        cod_cost () {
            return 0
        },

        grand_total () {
            return parseFloat(this.total) + parseFloat(this.exp_cost) + parseFloat(this.cod_cost)
        },

        cust_name : {
            get () { return this.$store.state.quickorder.cust_name },
            set (v) { this.$store.commit('quickorder/set_common', ['cust_name', v]) }
        },

        cust_address : {
            get () { return this.$store.state.quickorder.cust_address },
            set (v) { this.$store.commit('quickorder/set_common', ['cust_address', v]) }
        },

        cust_postcode : {
            get () { return this.$store.state.quickorder.cust_postcode },
            set (v) { this.$store.commit('quickorder/set_common', ['cust_postcode', v]) }
        },

        cust_phone : {
            get () { return this.$store.state.quickorder.cust_phone },
            set (v) { this.$store.commit('quickorder/set_common', ['cust_phone', v]) }
        },

        cust_id () {
            return this.$store.state.quickorder.cust_id
        },

        cust_city () {
            return this.$store.state.quickorder.cust_city
        },

        loading_city () {
            return this.$store.state.quickorder.loading_city
        },

        btn_save_enable () {
            if (this.cust_name == '' || this.cust_address == '' || this.cust_phone == '')
                return false
            if (this.cust_postcode.length < 5)
                return false
            if (!this.expedition_set)
                return false
            if (this.details.length < 1)
                return false
            // if (!this.selected_payment)
            //     return false
            return true
        },

        weight_total () {
            return this.$store.state.quickorder.weight_total
        },

        payments () {
            return this.$store.state.quickorder.payments
        },

        selected_payment : {
            get () { return this.$store.state.quickorder.selected_payment },
            set (v) { this.$store.commit('quickorder/set_selected_payment', v) }
        },

        expedition_set () {
            let exp = this.selected_expedition
            if (!exp) return false
            if (exp.M_ExpeditionCode != 'EX.FREE' && exp.M_ExpeditionCode != 'EX.OTHER')
                if (!this.selected_service) return false
            return true
        },

        ex_other () {
            return this.$store.state.quickorder.ex_other
        },

        query : {
            get () { return this.$store.state.quickorder.query },
            set (v) { this.$store.commit('quickorder/set_common', ['query', v]) }
        },

        order_note () {
            return this.$store.state.quickorder.order_note
        },

        selected_customer () {
            return this.$store.state.customer.selected_customer
        },

        origin () {
            return this.$store.state.quickorder.origin
        },

        destination () {
            return this.$store.state.quickorder.destination
        },

        lead_id () {
            return this.$store.state.quickorder.lead_id
        },

        leadsources () {
            return this.$store.state.quickorder.leadsources
        },

        selected_leadsource : {
            get () { return this.$store.state.quickorder.selected_leadsource },
            set (v) { this.$store.commit('quickorder/set_selected_leadsource', v) }
        },

        sales_ads : {
            get () { return this.$store.state.quickorder.sales_ads },
            set (v) { this.$store.commit('quickorder/set_object', ['sales_ads', v]) }
        },

        sales_mp : {
            get () { return this.$store.state.quickorder.sales_mp },
            set (v) { this.$store.commit('quickorder/set_object', ['sales_mp', v]) }
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        },

        add (x, packet) {
            let items = this.$store.state.quickorder.details
            let status = true

            if (!packet) {
                
                for (let i in items)
                    if (items[i].item_id == x.M_ItemID && items[i].is_packet == 'N') status = false

                if (status) {
                    if (x.price_sale == 0) 
                        items.push({item_id:x.M_ItemID, item_name:x.M_ItemName, item_qty:1, item_price:x.M_PriceAmount, item_disc:0, item_discrp:x.M_PriceDiscTotal, item_subtotal:x.M_PriceTotal, item_weight:x.M_ItemWeight, is_packet:'N'})
                    else
                        items.push({item_id:x.M_ItemID, item_name:x.M_ItemName, item_qty:1, item_price:x.price_sale, item_disc:0, item_discrp:0, item_subtotal:x.price_sale, item_weight:x.M_ItemWeight, is_packet:'N'})
                    this.$store.commit('quickorder/set_details', items)
                }
            } else {
                for (let i in items)
                    if (items[i].item_id == x.M_PacketID && items[i].is_packet == 'Y') status = false

                if (status) {
                    if (x.packet_sale_price == 0)
                        items.push({item_id:x.M_PacketID, item_name:x.M_PacketName, item_qty:1, item_price:x.price_sale, item_disc:0, item_discrp:0, item_subtotal:x.price_sale, item_weight:x.M_PacketTotalWeight, is_packet:'Y'})
                    else
                        items.push({item_id:x.M_PacketID, item_name:x.M_PacketName, item_qty:1, item_price:x.price_sale, item_disc:0, item_discrp:0, item_subtotal:x.packet_sale_price, item_weight:x.M_PacketTotalWeight, is_packet:'Y'})
                    this.$store.commit('quickorder/set_details', items)
                }
            }
            
        },

        del (i) {
            let items = this.$store.state.quickorder.details
            items.splice(i,1)
            this.$store.commit('quickorder/set_details', items)
        },

        is_selected (i, packet) {
            let items = this.$store.state.quickorder.details
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

        is_selected_class (i, packet) {
            if (packet) {
                if (this.is_selected(i, packet)) return 'cyan lighten-4'
            } else {
                if (this.is_selected(i)) return 'cyan lighten-4'
            }
                
            return ''
        },

        thr_search: _.debounce( function () {
            this.$store.dispatch("quickorder/search_city")
        }, 700),

        edit_expedition () {
            this.$store.commit('quickorder/set_common', ['dialog_expedition', true])
        },

        save () {
            this.$store.dispatch('quickorder/save')
        },

        select (x) {
            return
        },

        select_customer () {
            this.$store.commit('quickorder/set_common', ['dialog_customer', true])
        },

        clear_me () {
            this.$store.commit('quickorder/set_common', ['cust_id', 0])
        },

        search () {
            this.$store.dispatch('quickorder/search_item')
            this.$store.dispatch('quickorder/search_packet')
        },

        note () {
            this.$store.commit('quickorder/set_common', ['dialog_note', true])
        }
    },

    mounted () {
        this.$store.dispatch('quickorder/search_item')
        this.$store.dispatch('quickorder/search_packet')
        this.$store.dispatch('quickorder/search_leadsource')
        // this.$store.dispatch('quickorder/search_payment_expanded')
    },

    watch : {
        search_city(val, old) {
            if (val == null || typeof val == 'undefined') val = ""
            if (val == old ) return
            if (this.$store.state.quickorder.search_status == 1 ) return  
            this.$store.commit("quickorder/set_common", ['search_city', val])
            this.thr_search()
        },

        weight_total (v, o) {
            this.$store.dispatch('quickorder/search_service', {})
            let x = this.$store.state.quickorder.services
            let y = this.$store.state.quickorder.selected_service

            for (let i of x) {
                if (this.selected_service.service == i.service) {
                    this.$store.commit('quickorder/set_selected_service', i)
                    this.$store.commit('quickorder/set_common', ['courier_cost', this.selected_service.cost[0].value])
                }
            }
                
        },

        cust_id (v, o) {
            if (o != 0 && v == 0) {
                this.$store.commit('quickorder/set_common', ['cust_name', ''])
                this.$store.commit('quickorder/set_common', ['cust_address', ''])
                this.$store.commit('quickorder/set_common', ['cust_city', ''])
                this.$store.commit('quickorder/set_common', ['cust_postcode', ''])
                this.$store.commit('quickorder/set_common', ['cust_phone', ''])
            }
        }
    }
}
</script>