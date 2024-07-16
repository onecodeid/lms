<template>
    <v-dialog
        v-model="dialog"
        scrollable
        persistent
        max-width="1000px"
        transition="dialog-transition"
        top
    >
        <v-card>
            <v-card-title primary-title class="pb-0">
                <v-layout row wrap>
                    <v-flex xs6>
                        <h3>TAMBAH BARANG</h3>
                    </v-flex>
                    <v-flex xs3 pr-1>
                        <v-btn color="primary" :outline="selected_tab=='PACKET'" block @click="selected_tab='ITEM'" class="mb-0 mt-0">SINGLE ITEM</v-btn>
                    </v-flex>

                    <v-flex xs3>
                        <v-btn color="primary" :outline="selected_tab=='ITEM'" block @click="selected_tab='PACKET'" class="mb-0 mt-0">PAKET</v-btn>
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
                            <img :src="props.item.img_url" width="100%" />
                        </td>
                        <td class="text-xs-left pa-2" @click="select(props.item)">
                            <b>{{ props.item.M_PacketName }}</b><br />{{ props.item.item_names }}</td>
                        <td class="text-xs-left pa-2 text-xs-right" @click="select(props.item)">{{ one_money(props.item.price_sale) }}</td>
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
                        <td class="text-xs-left pa-2 text-xs-right" v-bind:class="is_selected(props.item)" @click="select(props.item)">{{ one_money(props.item.M_PriceAmount) }}</td>
                        <td class="text-xs-left pa-2 text-xs-right" v-bind:class="is_selected(props.item)" @click="select(props.item)">{{ one_money(props.item.M_PriceDiscTotal) }}</td>
                        <td class="text-xs-left pa-2 text-xs-right" v-bind:class="is_selected(props.item)" @click="select(props.item)">{{ one_money(props.item.M_PriceTotal) }}</td>
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
        }
    },

    methods : {
        select (x) {
            this.$store.commit('newitem/set_selected_item', x)
        },

        one_money (x) {
            return window.one_money(x)
        },

        add (x) {
            this.select(x)
            let items = this.$store.state.campaigndetail.items
            let status = true

            for (let i in items)
                if (items[i].packet_id == x.M_ItemID && items[i].is_packet == 'N') status = false
            if (status) {
                this.$store.dispatch('campaigndetail/add', {is_packet:'N'})
            }
        },

        add_packet (x) {
            this.$store.commit('newitem/set_selected_packet', x)
            let items = this.$store.state.campaigndetail.items
            let status = true

            for (let i in items)
                if (items[i].packet_id == x.M_PacketID && items[i].is_packet == 'Y') status = false

            if (status) {
                this.$store.dispatch('campaigndetail/add', {is_packet:'Y'})
            }
        },

        is_selected (item) {
            // let itm = this.$store.state.salesorder.items
            // for (let i of itm) {
                
            //     console.log(i)
            //     if (item.M_ItemID == i.item_id && i.is_packet == 'N')
            //         return 'green lighten-4'
            // }
            return ''
        }
    },

    mounted () {
        this.$store.dispatch('newitem/search_item', [])
        this.$store.dispatch('newitem/search_packet', [])
    }
}
</script>