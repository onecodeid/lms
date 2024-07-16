<template>
    <v-dialog
        v-model="dialog"
        scrollable
        persistent
        fullscreen
        transition="dialog-transition"
        content-class="dialog-filter"
    >
        <v-card class="pa-1">
            <v-card-title primary-title class="pa-1 blue">
                <v-layout row wrap>
                    <v-flex sm6 class="hidden-xs-only">
                        <v-btn flat color="primary" class="ma-0 btn-icon mr-2" @click="dialog=!dialog" style="float:left">
                            <v-icon class="white--text" medium>arrow_back</v-icon>
                        </v-btn>

                        <h3 class="headline white--text pt-1">DAFTAR ITEM</h3>
                    </v-flex>
                    <v-flex xs12 sm6>
                        <v-text-field
                            solo
                            hide-details
                            placeholder="Pencarian" v-model="query"
                            @change="search"
                        >
                            <template v-slot:append-outer>
                                <v-btn flat color="primary" class="ma-0 btn-icon hidden-md-and-up" @click="search">
                                    <v-icon class="white--text" medium>search</v-icon>
                                </v-btn>
                            </template>

                            <template v-slot:prepend>
                                <v-btn flat color="primary" class="ma-0 btn-icon hidden-sm-and-up mr-2" @click="dialog=!dialog">
                                    <v-icon class="white--text" medium>arrow_back</v-icon>
                                </v-btn>
                            </template>
                        </v-text-field>
                    </v-flex>
                </v-layout>
            </v-card-title>

            <v-card-text class="pa-1">
                <v-list two-line>
                    <template v-for="(item, index) in items">
                        <v-list-tile
                        :key="'i'+item.M_ItemID"
                        avatar
                        @click="add(item)"
                        :class="is_selected(item)"
                        >
                        <v-list-tile-avatar>
                            <img :src="item.img_url">
                        </v-list-tile-avatar>

                        <v-list-tile-content>
                            <v-list-tile-title class="font-weight-medium" v-html="item.M_ItemName"></v-list-tile-title>
                            <div class="v-list__tile__sub-title">
                                <v-layout row wrap class="caption" v-show="item.price_sale==0">
                                    <v-flex xs6>
                                        Harga : Rp {{one_money(item.M_PriceTotal)}}
                                    </v-flex>
                                    <v-flex xs6>
                                        Berat : {{item.M_ItemWeight}} gr
                                    </v-flex>
                                </v-layout>

                                <v-layout row wrap class="caption" v-show="item.price_sale>0">
                                    <v-flex xs8>
                                        Harga : <span class="red--text zalfa-text-over">Rp {{one_money(item.M_PriceTotal)}}</span> <span class="green--text"> Rp {{one_money(item.price_sale)}}</span>
                                    </v-flex>
                                    <v-flex xs4>
                                        Berat : {{item.M_ItemWeight}} gr
                                    </v-flex>
                                </v-layout>
                            </div>
                        </v-list-tile-content>

                        

                        </v-list-tile>

                        <v-divider
                        :key="'id'+item.M_ItemID"
                        :inset="item.inset"
                        ></v-divider>
                    </template>

                    <template v-for="(item, index) in packets">
                        <v-list-tile
                        :key="'p'+item.M_PacketID"
                        avatar
                        @click="add_packet(item)"
                        :class="is_selected(item, true)"
                        >
                        <v-list-tile-avatar>
                            <img :src="item.packet_img">
                        </v-list-tile-avatar>

                        <v-list-tile-content>
                            <v-list-tile-title class="font-weight-medium" v-html="item.M_PacketName"></v-list-tile-title>
                            <div class="v-list__tile__sub-title">
                                <v-layout row wrap class="caption" v-show="item.packet_sale_price==0">
                                    <v-flex xs6>
                                        Harga : Rp {{one_money(item.price_sale)}}
                                    </v-flex>
                                    <v-flex xs6>
                                        Berat : {{item.M_PacketTotalWeight}} gr
                                    </v-flex>
                                </v-layout>

                                <v-layout row wrap class="caption" v-show="item.packet_sale_price>0">
                                    <v-flex xs8>
                                        Harga : <span class="red--text zalfa-text-over">Rp {{one_money(item.price_sale)}}</span> <span class="green--text"> Rp {{one_money(item.packet_sale_price)}}</span>
                                    </v-flex>
                                    <v-flex xs4>
                                        Berat : {{item.M_PacketTotalWeight}} gr
                                    </v-flex>
                                </v-layout>
                            </div>
                        </v-list-tile-content>

                        

                        </v-list-tile>

                        <v-divider
                        :key="'pd'+item.M_PacketID"
                        :inset="item.inset"
                        ></v-divider>
                    </template>
                </v-list>
            </v-card-text>

            <!-- <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="primary" @click="dialog=!dialog">Tutup</v-btn>
            </v-card-actions> -->
        </v-card>

        <v-snackbar
            v-model="snackbar"
            :timeout=3000
            bottom
            vertical
            multi-line
            >
            Item <span class="blue--text ml-2 mr-2"> {{curr_item_name}} </span> berhasil ditambahkan
            <v-btn
                color="pink"
                flat
                @click="snackbar = false"
            >
                Tutup
            </v-btn>
        </v-snackbar>
            
    </v-dialog>
    
</template>

<style>
.dialog-filter .v-card__title, .dialog-filter .v-card__actions {
    flex-grow: 0 !important;
}

.dialog-filter .v-card__text {
    flex-grow: 1 !important;
}

.dialog-filter .v-text-field.v-text-field--solo .v-input__control, .dialog-filter .btn-icon {
    min-height: 40px;
}
.dialog-filter .v-text-field.v-text-field--solo .v-input__append-outer, .dialog-filter .v-text-field.v-text-field--solo .v-input__prepend-outer {
    margin-top: 0px;
    margin-left: 0px;
}
.dialog-filter .v-input__prepend-outer {
    margin-right: 0px;
}
</style>

<script>
module.exports = {
    data () {
        return {
            headers: [
                {
                    text: "#",
                    align: "left",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "NAMA BARANG",
                    align: "center",
                    sortable: false,
                    width: "35%",
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
                    text: "HARGA TOTAL",
                    align: "right",
                    sortable: false,
                    width: "15%",
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
            headers_packet: [
                {
                    text: "#",
                    align: "left",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "NAMA PAKET",
                    align: "center",
                    sortable: false,
                    width: "35%",
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
                    text: "ACTION",
                    align: "center",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ],

            selected_tab: "ITEM",
            curr_item_name: ''
        }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.newitem.dialog_items },
            set (v) { this.$store.commit('newitem/set_common', ['dialog_items', v]) }
        },

        items () {
            return this.$store.state.newitem.items
        },

        packets () {
            return this.$store.state.newitem.packets
        },

        snackbar : {
            get () { return this.$store.state.newitem.snackbar },
            set (v) { this.$store.commit('newitem/set_common', ['snackbar', v]) }
        },

        query: {
            get () { return this.$store.state.newitem.search },
            set (v) { this.$store.commit('newitem/set_common', ['search', v]) }
        }
    },

    methods : {
        select () {
            return
        },

        one_money (x) {
            return window.one_money(x)
        },

        add (x) {
            let items = this.$store.state.salesorder.items
            let status = true

            for (let i in items)
                if (items[i].item_id == x.M_ItemID && items[i].is_packet == 'N') status = false

            if (status) {
                // items.push({item_id:x.M_ItemID, item_name:x.M_ItemName, item_qty:1, item_price:x.M_PriceAmount, item_disc:0, item_discrp:x.M_PriceDiscTotal, item_subtotal:x.M_PriceTotal, item_weight:x.M_ItemWeight, is_packet:'N', img_url:x.img_url})
                if (x.price_sale==0)
                    items.push({item_id:x.M_ItemID, item_name:x.M_ItemName, item_qty:1, item_price:x.M_PriceAmount, item_disc:0, item_discrp:x.M_PriceDiscTotal, item_subtotal:x.M_PriceTotal, item_weight:x.M_ItemWeight, coupon_id:0, coupon_amount:0, is_packet:'N', img_url:x.img_url})
                else
                    items.push({item_id:x.M_ItemID, item_name:x.M_ItemName, item_qty:1, item_price:x.price_sale, item_disc:0, item_discrp:0, item_subtotal:x.price_sale, item_weight:x.M_ItemWeight, coupon_id:0, coupon_amount:0, is_packet:'N', img_url:x.img_url})
                // this.dialog = false
                this.$store.commit('salesorder/update_items', items)
                this.curr_item_name = x.M_ItemName
                this.snackbar = true
            }
        },

        add_packet (x) {
            let items = this.$store.state.salesorder.items
            let status = true

            for (let i in items)
                if (items[i].item_id == x.M_PacketID && items[i].is_packet == 'Y') status = false

            if (status) {
                // items.push({item_id:x.M_PacketID, item_name:x.M_PacketName, item_qty:1, item_price:x.price_sale, item_disc:0, item_discrp:0, item_subtotal:x.price_sale, item_weight:x.M_PacketTotalWeight, is_packet:'Y'})
                if (x.packet_sale_price==0)
                    items.push({item_id:x.M_PacketID, item_name:x.M_PacketName, item_qty:1, item_price:x.price_sale, item_disc:0, item_discrp:0, item_subtotal:x.price_sale, item_weight:x.M_PacketTotalWeight, coupon_id:0, coupon_amount:0, is_packet:'Y', img_url:x.packet_img})
                else
                    items.push({item_id:x.M_PacketID, item_name:x.M_PacketName, item_qty:1, item_price:x.packet_sale_price, item_disc:0, item_discrp:0, item_subtotal:x.packet_sale_price, item_weight:x.M_PacketTotalWeight, coupon_id:0, coupon_amount:0, is_packet:'Y', img_url:x.packet_img})
                // this.dialog = false
                this.$store.commit('salesorder/update_items', items)
                this.curr_item_name = x.M_PacketName
                this.snackbar = true
            }
        },

        is_selected (item, packet) {
            let itm = this.$store.state.salesorder.items
            for (let i of itm) {
                if (item.M_ItemID == i.item_id && i.is_packet == 'N' && !packet)
                    return 'green lighten-4'
                if (item.M_PacketID == i.item_id && i.is_packet == 'Y' && packet)
                    return 'green lighten-4'
            }
            
            return ''
        },

        search () {
            this.$store.dispatch('newitem/search_item', {})
            this.$store.dispatch('newitem/search_packet', {})
        }
    },

    mounted () {
        this.$store.dispatch('newitem/search_item', {})
        this.$store.dispatch('newitem/search_packet', {})
    }
}
</script>