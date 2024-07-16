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

                    <v-flex xs12>
                        <v-divider></v-divider>
                    </v-flex>
                </v-layout>
                
            </v-card-title>

            <v-card-text class="pt-1">
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
                            {{ props.item.M_ItemCode }}</td>
                        <td class="text-xs-left pa-2" @click="select(props.item)" v-bind:class="is_selected(props.item)">
                            {{ props.item.M_ItemName }}</td>
                        <td class="text-xs-left pa-2 text-xs-center" v-bind:class="is_selected(props.item)" @click="select(props.item)">
                            <v-btn color="success" small class="ma-0" @click="add(props.item)" v-show="is_selected(props.item) == ''" :disabled="is_selected(props.item) != ''">ADD</v-btn>
                            <v-btn color="red" dark small class="ma-0" @click="del(props.item)" v-show="is_selected(props.item) != ''" :disabled="is_selected(props.item) == ''">REMOVE</v-btn>
                        </td>
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
                    text: "KODE BARANG",
                    align: "left",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "NAMA BARANG",
                    align: "center",
                    sortable: false,
                    width: "70%",
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
            let items = this.$store.state.purchase_new.items
            let status = true

            for (let i in items)
                if (items[i].item_id == x.M_ItemID) status = false

            if (status) {
                    items.push({detail_id:0, item_id:x.M_ItemID, item_name:x.M_ItemName, item_qty:1, item_price:0, item_total:0})
            //     // this.dialog = false
                this.$store.commit('purchase_new/set_items', items)
                this.curr_item_name = x.M_ItemName
                this.snackbar = true
            }
        },

        del (x) {
            let items = this.$store.state.purchase_new.items
            let idx = -1

            for (let i in items)
                if (items[i].item_id == x.M_ItemID) idx = i

            if (idx > -1) {
                items.splice(idx, 1)
                this.$store.commit('purchase_new/set_items', items)
            }
        },

        is_selected (item) {
            let itm = this.$store.state.purchase_new.items
            for (let i of itm) {
                if (item.M_ItemID == i.item_id)
                    return 'green lighten-4'
            }
            return ''
        },

        search () {
            this.$store.dispatch('newitem/search_item', {})
        }
    },

    mounted () {
        this.$store.dispatch('newitem/search_item', {})
    }
}
</script>