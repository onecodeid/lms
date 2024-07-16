<template>
    <v-dialog
        v-model="dialog"
        scrollable
        persistent
        max-width="1000px"
        transition="dialog-transition"
        top
        content-class="zalfa-dialog-item"
    >
        <v-card>
            <v-card-title primary-title class="pb-0 pt-2">
                <v-layout row wrap>
                    <v-flex xs5>
                        <h3 class="headline">TAMBAH BARANG</h3>
                    </v-flex>
                    <v-flex xs3>
                        <v-text-field
                            solo
                            hide-details
                            placeholder="Pencarian"
                            @change="search"
                            v-model="query"
                        >
                            <template v-slot:append-outer>
                                <v-btn color="primary" class="ma-0 btn-icon" @click="search">
                                    <v-icon>search</v-icon>
                                </v-btn>      
                            </template>
                        </v-text-field>
                    </v-flex>
                    <v-flex xs2 pl-1>
                        <v-btn color="primary" :outline="selected_tab=='PACKET'" block @click="selected_tab='ITEM'" class="mb-0 mt-0">ITEM</v-btn>
                    </v-flex>

                    <v-flex xs2 pl-1>
                        <v-btn color="primary" :outline="selected_tab=='ITEM'" block @click="selected_tab='PACKET'" class="mb-0 mt-0">PAKET</v-btn>
                    </v-flex>

                    <v-flex xs12>
                        <v-divider></v-divider>
                    </v-flex>
                </v-layout>
                
            </v-card-title>

            <v-card-text class="pt-1" v-show="selected_tab=='PACKET'">
                <v-layout row wrap>
                    
                </v-layout>
                <v-data-table 
                    :headers="headers_packet"
                    :items="packets"
                    :loading="false"
                    hide-actions
                    class="elevation-1">
                    <template slot="items" slot-scope="props">
                        <td class="text-xs-left pa-2" @click="select(props.item)">
                            <img :src="props.item.packet_img?props.item.packet_img:'../assets/img/zalfa-item-img.png'" width="50%" />
                        </td>
                        <td class="text-xs-left pa-2" @click="select(props.item)">
                            <b>{{ props.item.M_PacketName }}</b><br />{{ props.item.item_names }}</td>

                        <td class="text-xs-left pa-2 text-xs-right" @click="select(props.item)" v-show="props.item.packet_sale_price==0">Rp {{ one_money(props.item.price_sale) }}</td>
                        <td class="text-xs-left pa-2 text-xs-right" @click="select(props.item)" v-show="props.item.packet_sale_price>0">
                            <div class="red--text" style="text-decoration:line-through">Rp {{ one_money(props.item.price_sale) }}</div>
                            <div class="green--text">Promo Rp {{ one_money(props.item.packet_sale_price) }}</div></td>

                        <!-- <td class="text-xs-left pa-2 text-xs-right" @click="select(props.item)">{{ one_money(props.item.stock_qty) }}</td> -->
                        <td class="text-xs-left pa-2 text-xs-center" @click="select(props.item)">
                            <v-btn color="success" small class="ma-0" @click="add_packet(props.item)">ADD</v-btn>
                        </td>
                        <!-- <td class="text-xs-left pa-0" @click="select(props.item)">
                            <v-btn color="primary" class="btn-icon ma-0" small @click="edit(props.item)"><v-icon>create</v-icon></v-btn>
                            <v-btn color="red" dark class="btn-icon ma-0" small @click="del(props.item)"><v-icon>delete</v-icon></v-btn>
                        </td> -->
                        <!-- <td class="text-xs-center pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.M_DoctorHP}}</td>
                        <td class="text-xs-left pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.status}}</td> -->
                    </template>
                </v-data-table>
            </v-card-text>

            <v-card-text class="pt-1" v-show="selected_tab=='ITEM'">
                <v-layout row wrap>
                    
                </v-layout>
                <v-data-table 
                    :headers="headers"
                    :items="items"
                    :loading="false"
                    hide-actions
                    class="elevation-1">
                    <template slot="items" slot-scope="props">
                        <td class="text-xs-left pa-1" @click="select(props.item)" v-bind:class="is_selected(props.item)">
                            <img :src="props.item.img_url" width="50%" />
                        </td>
                        <td class="text-xs-left pa-2" @click="select(props.item)" v-bind:class="is_selected(props.item)">
                            {{ props.item.M_ItemName }}</td>
                        
                            <td class="text-xs-left pa-2 text-xs-right" v-bind:class="is_selected(props.item)" @click="select(props.item)" v-show="props.item.price_sale==0">Rp {{ one_money(props.item.M_PriceAmount) }}</td>
                            <td class="text-xs-left pa-2 text-xs-right" v-bind:class="is_selected(props.item)" @click="select(props.item)" v-show="props.item.price_sale==0">Rp {{ one_money(props.item.M_PriceDiscTotal) }}</td>
                            <td class="text-xs-left pa-2 text-xs-right" v-bind:class="is_selected(props.item)" @click="select(props.item)" v-show="props.item.price_sale==0">Rp {{ one_money(props.item.M_PriceTotal) }}</td>

                            <td class="text-xs-left pa-2 text-xs-right" v-bind:class="is_selected(props.item)" @click="select(props.item)" v-show="props.item.price_sale>0"><div class="red--text zalfa-text-over">Rp {{ one_money(props.item.M_PriceAmount) }}</div><div class="green--text">Promo Rp {{ one_money(props.item.price_sale) }}</div></td>
                            <td class="text-xs-left pa-2 text-xs-right" v-bind:class="is_selected(props.item)" @click="select(props.item)" v-show="props.item.price_sale>0"><div class="red--text zalfa-text-over">Rp {{ one_money(props.item.M_PriceDiscTotal) }}</div><div class="green--text">0</div></td>
                            <td class="text-xs-left pa-2 text-xs-right" v-bind:class="is_selected(props.item)" @click="select(props.item)" v-show="props.item.price_sale>0"><div class="red--text zalfa-text-over">Rp {{ one_money(props.item.M_PriceTotal) }}</div><div class="green--text">Rp {{ one_money(props.item.price_sale) }}</div></td>

                        <td class="text-xs-left pa-2 text-xs-center" v-bind:class="is_selected(props.item)" @click="select(props.item)">
                            <v-btn color="success" small class="ma-0" @click="add(props.item)" :disabled="is_selected(props.item) != ''">ADD</v-btn>
                        </td>
                        <!-- <td class="text-xs-left pa-0" @click="select(props.item)">
                            <v-btn color="primary" class="btn-icon ma-0" small @click="edit(props.item)"><v-icon>create</v-icon></v-btn>
                            <v-btn color="red" dark class="btn-icon ma-0" small @click="del(props.item)"><v-icon>delete</v-icon></v-btn>
                        </td> -->
                        <!-- <td class="text-xs-center pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.M_DoctorHP}}</td>
                        <td class="text-xs-left pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.status}}</td> -->
                    </template>
                </v-data-table>
            </v-card-text>

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="primary" @click="dialog=!dialog">Tutup</v-btn>
            </v-card-actions>
        </v-card>

        <v-snackbar
            v-model="snackbar"
            :timeout=3000
            top
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
.zalfa-dialog-item .v-text-field.v-text-field--solo .v-input__append-outer {
    margin-top: 0px;
    margin-left: 0px;
}

.zalfa-dialog-item .v-text-field.v-text-field--solo .v-input__control {
    min-height: 36px;
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

        query : {
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
                if (x.price_sale==0)
                    items.push({item_id:x.M_ItemID, item_name:x.M_ItemName, item_qty:1, item_price:x.M_PriceAmount, item_disc:0, item_discrp:x.M_PriceDiscTotal, item_subtotal:x.M_PriceTotal, item_weight:x.M_ItemWeight, coupon_id:0, coupon_amount:0, is_packet:'N'})
                else
                    items.push({item_id:x.M_ItemID, item_name:x.M_ItemName, item_qty:1, item_price:x.price_sale, item_disc:0, item_discrp:0, item_subtotal:x.price_sale, item_weight:x.M_ItemWeight, coupon_id:0, coupon_amount:0, is_packet:'N'})
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
                if (x.packet_sale_price==0)
                    items.push({item_id:x.M_PacketID, item_name:x.M_PacketName, item_qty:1, item_price:x.price_sale, item_disc:0, item_discrp:0, item_subtotal:x.price_sale, item_weight:x.M_PacketTotalWeight, coupon_id:0, coupon_amount:0, is_packet:'Y'})
                else
                    items.push({item_id:x.M_PacketID, item_name:x.M_PacketName, item_qty:1, item_price:x.packet_sale_price, item_disc:0, item_discrp:0, item_subtotal:x.packet_sale_price, item_weight:x.M_PacketTotalWeight, coupon_id:0, coupon_amount:0, is_packet:'Y'})
                // this.dialog = false
                this.$store.commit('salesorder/update_items', items)
                this.curr_item_name = x.M_PacketName
                this.snackbar = true
            }
        },

        is_selected (item) {
            let itm = this.$store.state.salesorder.items
            for (let i of itm) {
                if (item.M_ItemID == i.item_id && i.is_packet == 'N')
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
        // this.$store.dispatch('newitem/search_item', {})
        // this.$store.dispatch('newitem/search_packet', {})
    }
}
</script>