<template>
    <v-dialog
        v-model="dialog"
        scrollable
        :overlay="false"
        max-width="800px"
        transition="dialog-transition"
    >
        <v-card>
            <v-card-title primary-title>
                <v-layout row wrap>
                    <v-flex xs6>
                        <h3 v-show="!edit">PENYESUAIAN STOK [ BARU ]</h3>
                        <h3 v-show="edit">PENYESUAIAN STOK NO {{ adjustment_number }}</h3>
                    </v-flex>
                    <v-flex xs6 class="text-xs-right">
                        <v-btn color="success" small @click="add_item" v-show="!edit" :disabled="!selected_warehouse">
                            <v-icon class="mr-2 zalfa-font-12">add</v-icon> Tambah Item</v-btn>
                    </v-flex>
                </v-layout>
                
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <!-- <v-flex xs6 pr-4>
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-text-field
                                    label="Nomor"
                                    hide-details
                                    v-model="adjustment_number"
                                    readonly
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12 mt-3>
                                <v-text-field
                                    label="Tanggal"
                                    hide-details
                                    v-model="adjustment_date"
                                ></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-flex> -->
                    <v-flex xs3>
                        <v-select
                            :items="warehouses"
                            v-model="selected_warehouse"
                            label="Gudang"
                            return-object
                            item-text="warehouse_name"
                            item-value="warehouse_id"
                            :disabled="items.length>0"
                        ></v-select>
                    </v-flex>

                    <v-flex xs9 pl-2>
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-textarea
                                    label="Catatan"
                                    v-model="adjustment_note"
                                    rows="1"
                                    :disabled="edit"
                                    placeholder="Tuliskan catatan anda disini">
                                </v-textarea>
                            </v-flex>
                        </v-layout>
                    </v-flex>
                </v-layout>

                <v-layout row wrap>
                    <v-flex xs12>
                        
                        <v-data-table 
                            :headers="headers"
                            :items="items"
                            :loading="false"
                            hide-actions
                            class="elevation-1">
                            <template slot="items" slot-scope="props">
                                <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.item_code }}</td>
                                <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.item_name }}, {{ props.item.address_kelurahan }}</td>
                                <td class="text-xs-right pa-2" @click="select(props.item)">{{ props.item.item_bf_qty }}</td>
                                <td class="text-xs-left pa-2" @click="select(props.item)" v-if="!edit">
                                    <v-text-field
                                        solo
                                        hide-details
                                        :value="props.item.item_qty"
                                        type="number"
                                        reverse
                                        @input="change_qty(props.index, $event)"
                                        class="zalfa-input-super-dense"
                                    ></v-text-field>
                                </td>
                                <td class="text-xs-right pa-2" @click="select(props.item)" v-if="edit">{{ props.item.item_qty }}</td>
                                <td class="text-xs-right pa-2" @click="select(props.item)">{{ props.item.item_af_qty }}</td>
                                <td class="text-xs-left pa-0" @click="select(props.item)">
                                    <v-btn color="red" dark class="btn-icon ma-0" small @click="del(props.index)" :disabled="edit"><v-icon>delete</v-icon></v-btn>
                                </td>
                                <!-- <td class="text-xs-center pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.M_DoctorHP}}</td>
                                <td class="text-xs-left pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.status}}</td> -->
                            </template>
                        </v-data-table>
                        <v-divider></v-divider>
                        

                    </v-flex>
                </v-layout>
            </v-card-text>

            <v-card-actions>
                <v-btn color="primary" flat @click="dialog=!dialog">Tutup</v-btn>
                <v-spacer></v-spacer>
                <v-btn color="primary" @click="save" v-show="!edit" :disabled="!btn_save_enable">Simpan</v-btn>                
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<style>
.zalfa-input-super-dense .v-input__control {
    min-height: 36px !important;
}

.zalfa-font-12 {
    font-size: 1.2em;
}
</style>

<script>
module.exports = {
    data () {
        return {
            headers: [
                {
                    text: "KODE",
                    align: "center",
                    sortable: false,
                    width: "20%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "NAMA ITEM",
                    align: "center",
                    sortable: false,
                    width: "40%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "QTY SBLM",
                    align: "left",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "QTY ADJUSTMENT",
                    align: "left",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "QTY SSDH",
                    align: "left",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "ACTION",
                    align: "center",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ]
        }
    },

    computed : {
        ...Vuex.mapState({
            warehouses: s => s.adjustment_new.warehouses 
        }),

        selected_warehouse : {
            get () { return this.$store.state.adjustment_new.selected_warehouse },
            set (v) { this.$store.commit('adjustment_new/set_object', ['selected_warehouse', v]) }
        },

        dialog : {
            get () { return this.$store.state.adjustment_new.dialog_new },
            set (v) { this.$store.commit('adjustment_new/set_common', ['dialog_new', v]) }
        },

        adjustment_number : {
            get () { return this.$store.state.adjustment_new.adjustment_number },
            set (v) { this.$store.commit('adjustment_new/set_common', ['adjustment_number', v]) }
        },

        adjustment_date : {
            get () { return this.$store.state.adjustment_new.adjustment_date },
            set (v) { this.$store.commit('adjustment_new/set_common', ['adjustment_date', v]) }
        },

        adjustment_note : {
            get () { return this.$store.state.adjustment_new.adjustment_note },
            set (v) { this.$store.commit('adjustment_new/set_common', ['adjustment_note', v]) }
        },

        items () { 
            return this.$store.state.adjustment_new.items
        },

        edit () {
            return this.$store.state.adjustment_new.edit
        },

        btn_save_enable () {
            if (this.adjustment_note != '' && this.items.length > 0)
                return true

            return false
        }
    },

    methods : {
        select (x) {
            this.$store.commit('adjustment_new/set_selected_item', x)
        },

        save () {
            this.$store.dispatch('adjustment_new/save')
        },

        add_item () {
            this.$store.dispatch('adjustment_new/search_item', {})
            this.$store.commit('adjustment_new/set_common', ['dialog_item', true])
        },

        change_qty(i, v) {
            let x = this.items
            x[i][`item_qty`] = v
            x[i][`item_af_qty`] = parseFloat(x[i][`item_bf_qty`]) + parseFloat(v)

            this.$store.commit('adjustment_new/set_items', x)
        },

        del (idx) {
            this.items.splice(idx, 1)
        }
    },

    watch : {
    }
}
</script>