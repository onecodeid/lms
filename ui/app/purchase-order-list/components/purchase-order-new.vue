<template>
    <v-dialog
        v-model="dialog"
        scrollable
        persistent
        max-width="1000px"
        transition="dialog-transition"
    >
        <v-card>
            <v-card-title primary-title class="primary white--text py-2">
                <h3>BARU</h3>
            </v-card-title>

            <v-card-text class="py-2">
                <v-layout row wrap>
                    <v-flex xs3>
                        <v-layout row wrap>
                            <v-flex xs6>
                                <common-datepicker
                                    label="Tanggal Order"
                                    :date="order_date"
                                    data="0"
                                    @change="change_date"
                                    classs=""
                                    hints=" "
                                    :details="true"
                                    :solo="false"
                                ></common-datepicker>        
                            </v-flex>
                            <v-flex xs6 class="text-xs-right">
                                <v-btn color="success" class="btn-icon" @click="add"><v-icon>add</v-icon> BARANG</v-btn>
                            </v-flex>
                        </v-layout>
                        

                        <v-select
                            :items="vendors"
                            v-model="selected_vendor"
                            return-object
                            item-value="M_VendorID"
                            item-text="M_VendorName"
                            label="Vendor / Supplier"
                            placeholder="Pilih Vendor"
                        ></v-select>

                        <v-textarea
                            v-model="order_note"
                            label="Catatan"
                            placeholder=" "
                        ></v-textarea>

                        <v-text-field
                            label="Total"
                            reverse
                            readonly
                            :value="total"
                            :mask="memask(total)"
                        ></v-text-field>
                        
                    </v-flex>
                    <v-flex xs9 pl-3>
                        <v-data-table 
                            :headers="headers"
                            :items="items"
                            :loading="false"
                            hide-actions
                            class="elevation-1">
                            <template slot="items" slot-scope="props">
                                <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.item_code }}</td>
                                <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.item_name }}</td>
                                
                                <td class="text-xs-right pa-2" @click="select(props.item)" v-show="edit">
                                    {{ one_money(props.item.item_qty) }}</td>
                                <td class="text-xs-right pa-1" @click="select(props.item)" v-show="!edit">
                                    <v-text-field
                                        solo
                                        hide-details
                                        :value="props.item.item_qty"
                                        reverse
                                        
                                        @input="change_qty(props.index, $event)"
                                        class="zalfa-input-super-dense"
                                        :mask="memask(props.item.item_qty)"
                                    ></v-text-field></td>

                                <td class="text-xs-right pa-2" @click="select(props.item)" v-show="edit">{{ one_money(props.item.item_price) }}</td>
                                <td class="text-xs-right pa-1" @click="select(props.item)" v-show="!edit">
                                    <v-text-field
                                        solo
                                        hide-details
                                        :value="props.item.item_price"
                                        reverse
                                        
                                        @input="change_price(props.index, $event)"
                                        class="zalfa-input-super-dense"
                                        :mask="memask(props.item.item_price)"
                                    ></v-text-field>
                                </td>

                                <td class="text-xs-right pa-1" @click="select(props.item)">
                                    <b>{{ one_money(props.item.item_total) }}</b>
                                <td class="text-xs-center pa-1" @click="select(props.item)">
                                    <v-btn color="red" dark class="btn-icon" title="Hapus item" @click="del(props.index)"><v-icon>delete</v-icon></v-btn>
                                </td>
                                <!-- <td class="text-xs-center pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.M_DoctorHP}}</td>
                                <td class="text-xs-left pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.status}}</td> -->
                            </template>
                        </v-data-table>
                        <v-divider></v-divider>
                        <!-- <v-pagination
                            style="margin-top:10px;margin-bottom:10px"
                            v-model="curr_page"
                            :length="xtotal_page"
                            @input="change_page"
                        ></v-pagination> -->
                    </v-flex>
                </v-layout>
            </v-card-text>
            <v-card-actions>
                <v-btn color="primary" flat @click="dialog=!dialog">Tutup</v-btn>
                <v-spacer></v-spacer>
                <v-btn color="primary" @click="save">Simpan</v-btn>
            </v-card-actions>
        </v-card>    
    </v-dialog>
    
</template>

<style>
.zalfa-input-super-dense .v-input__control {
    min-height: 36px !important;
}
</style>

<script>
module.exports = {
    components : {
        'common-datepicker' : httpVueLoader('../../common/components/common-datepicker.vue')
    },

    data () {
        return {
            headers: [
                {
                    text: "KODE ITEM",
                    align: "left",
                    width: "10%",
                    sortable:false,
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "NAMA ITEM",
                    align: "left",
                    sortable: false,
                    width: "35%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "QTY",
                    align: "left",
                    sortable: false,
                    width: "15%",
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
                    text: "SUB TOTAL",
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
            ]
        }
    },

    computed : {
        edit () {
            return this.$store.state.purchase_new.edit
        },

        dialog : {
            get () { return this.$store.state.purchase_new.dialog_new },
            set (v) { this.$store.commit('purchase_new/set_common', ['dialog_new', v]) }
        },

        vendors () {
            return this.$store.state.purchase_new.vendors
        },

        selected_vendor : {
            get () { return this.$store.state.purchase_new.selected_vendor },
            set (v) { this.$store.commit('purchase_new/set_selected_vendor', v) }
        },

        order_note : {
            get () { return this.$store.state.purchase_new.order_note },
            set (v) { this.$store.commit('purchase_new/set_common', ['order_note', v]) }
        },

        order_date : {
            get () { return this.$store.state.purchase_new.order_date },
            set (v) { this.$store.commit('purchase_new/set_order_date', v) }
        },

        items () {
            return this.$store.state.purchase_new.items
        },

        selected_item : {
            get () { return this.$store.state.purchase_new.selected_item },
            set (v) { this.$store.commit('purchase_new/set_selected_item', v) }
        },

        total () {
            let total = 0
            for (let i of this.items)
                total += parseFloat(i.item_total)
            return total
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        },

        select (x) {
            this.selected_item = x
        },

        change_date(x) {
            this.order_date = x.new_date
            // this.search()
        },

        change_qty (idx, v) {
            let items = this.items
            items[idx].item_qty = v
            items[idx].item_total = v * items[idx].item_price
            this.$store.commit('purchase_new/set_items', items)
        },

        change_price (idx, v) {
            let items = this.items
            items[idx].item_price = v
            items[idx].item_total = v * items[idx].item_qty
            this.$store.commit('purchase_new/set_items', items)
        },

        del (idx) {
            let items = this.items
            items.splice(idx, 1)
            this.$store.commit('purchase_new/set_items', items)
        },

        add () {
            this.$store.commit('newitem/set_common', ['dialog_items', true] )
        },

        memask (x) {
            return window.one_mask_money(x)
        },

        save () {
            this.$store.dispatch('purchase_new/save')
        }
    },

    mounted () {
        this.$store.dispatch('purchase_new/search_vendor')
        
    },

    watch : {
        dialog (v, o) {
            if (v && !o && !this.edit) {
                this.$store.commit('newitem/set_common', ['dialog_items', true] )
            }
        }
    }
}
</script>