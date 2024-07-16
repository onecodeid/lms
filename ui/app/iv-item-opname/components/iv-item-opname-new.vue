<template>
    <v-dialog
        v-model="dialog"
        scrollable
        :overlay="false"
        max-width="800px"
        transition="dialog-transition"
    >
        <v-card>
            <v-card-title primary-title class="py-3 info white--text">
                <v-layout row wrap>
                    <v-flex xs6>
                        <h3 v-show="!edit">PENYESUAIAN STOK [ BARU ]</h3>
                        <h3 v-show="edit">PENYESUAIAN STOK NO {{ opname_number }}</h3>
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
                                    v-model="opname_number"
                                    readonly
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12 mt-3>
                                <v-text-field
                                    label="Tanggal"
                                    hide-details
                                    v-model="opname_date"
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
                        ></v-select>
                    </v-flex>

                    <v-flex xs9 pl-2>
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-textarea
                                    label="Catatan"
                                    v-model="opname_note"
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
                                <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.M_ItemCode }}</td>
                                <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.M_ItemName }}</td>
                                <td class="text-xs-right pa-2" @click="select(props.item)">{{ props.item.stock_qty }}</td>
                                <td class="text-xs-left pa-2" @click="select(props.item)" v-if="!edit">
                                    <v-text-field
                                        solo
                                        hide-details
                                        :value="props.item.curr_qty"
                                        type="number"
                                        reverse
                                        @input="change_qty(props.index, $event)"
                                        class="zalfa-input-super-dense"
                                    ></v-text-field>
                                </td>
                                <td class="text-xs-right pa-2" @click="select(props.item)" v-if="edit">{{ props.item.curr_qty }}</td>
                                <td class="text-xs-right pa-2" @click="select(props.item)">{{ parseFloat(props.item.curr_qty) >= parseFloat(props.item.stock_qty) ? '+ ' : '- ' }} {{ Math.abs(props.item.curr_qty - props.item.stock_qty) }}</td>
                                <td class="text-xs-left pa-1" @click="select(props.item)">
                                    <v-text-field
                                        solo
                                        hide-details
                                        :value="props.item.adjust_note"
                                        @input="change_note(props.index, $event)"
                                        class="zalfa-input-super-dense"
                                        v-show="!edit"
                                    ></v-text-field>
                                    <span v-show="edit">{{props.item.adjust_note}}</span>
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
                <v-btn color="orange" dark @click="print_me" v-show="edit" :disabled="!btn_save_enable">Cetak</v-btn>
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
                    align: "left",
                    sortable: false,
                    width: "8%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "NAMA ITEM",
                    align: "left",
                    sortable: false,
                    width: "30%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "QTY SISTEM",
                    align: "right",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "QTY GUDANG",
                    align: "right",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "ADJUSTMENT",
                    align: "right",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "CATATAN",
                    align: "center",
                    sortable: false,
                    width: "27%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ]
        }
    },

    computed : {
        ...Vuex.mapState({
            warehouses: s => s.opname_new.warehouses 
        }),

        selected_warehouse : {
            get () { return this.$store.state.opname_new.selected_warehouse },
            set (v) { this.$store.commit('opname_new/set_object', ['selected_warehouse', v]) }
        },

        dialog : {
            get () { return this.$store.state.opname_new.dialog_new },
            set (v) { this.$store.commit('opname_new/set_common', ['dialog_new', v]) }
        },

        opname_number : {
            get () { return this.$store.state.opname_new.opname_number },
            set (v) { this.$store.commit('opname_new/set_common', ['opname_number', v]) }
        },

        opname_date : {
            get () { return this.$store.state.opname_new.opname_date },
            set (v) { this.$store.commit('opname_new/set_common', ['opname_date', v]) }
        },

        opname_note : {
            get () { return this.$store.state.opname_new.opname_note },
            set (v) { this.$store.commit('opname_new/set_common', ['opname_note', v]) }
        },

        items () { 
            return this.$store.state.opname_new.items
        },

        edit () {
            return this.$store.state.opname_new.edit
        },

        btn_save_enable () {
            if (this.opname_note != '' && this.items.length > 0)
                return true

            return false
        }
    },

    methods : {
        select (x) {
            this.$store.commit('opname_new/set_selected_item', x)
        },

        save () {
            this.$store.dispatch('opname_new/save')
        },

        add_item () {
            this.$store.dispatch('opname_new/search_item', {})
            this.$store.commit('opname_new/set_common', ['dialog_item', true])
        },

        change_qty(i, v) {
            let x = this.items
            x[i][`curr_qty`] = v
            // x[i][`item_af_qty`] = parseFloat(x[i][`item_bf_qty`]) + parseFloat(v)

            this.$store.commit('opname_new/set_items', x)
        },

        change_note(i, v) {
            let x = this.items
            x[i][`adjust_note`] = v
            // x[i][`item_af_qty`] = parseFloat(x[i][`item_bf_qty`]) + parseFloat(v)

            this.$store.commit('opname_new/set_items', x)
        },

        del (idx) {
            this.items.splice(idx, 1)
        },

        print_me () {
            this.$store.commit('set_dialog_print', true)
        }
    },

    watch : {
        dialog (v, o) {
            if (v && !o && !this.edit) {
                this.$store.dispatch('opname_new/search_item')
            } else if (v && !o && !!this.edit) {
                this.$store.dispatch('opname_new/search_detail')
            }
        }
    },

    mounted () {
        // if (this.edit) {
        //     this.$store.dispatch('opname_new/search_item')
        // }
    }
}
</script>