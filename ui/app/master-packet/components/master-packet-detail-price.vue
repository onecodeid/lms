<template>
    <v-dialog
        v-model="dialog"
        scrollable
        :overlay="false"
        max-width="1000px"
        transition="dialog-transition"
    >
        <v-card>
            <v-card-title primary-title>
                <h3>UBAH HARGA {{ packet_name }}</h3>
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs6 pr-5>
                        <v-layout row wrap>
                            <v-flex xs6 mt-3>
                                <v-text-field
                                    label="Kode Barang (SKU)"
                                    hide-details
                                    v-model="item_code"
                                    disabled
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12 :class="`mt-`+v_space">
                                <v-text-field
                                    label="Nama Barang"
                                    hide-details
                                    v-model="item_name"
                                    disabled
                                ></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-flex>

                    <v-flex xs6>
                        <v-layout row wrap>
                            <v-data-table 
                                :headers="headers"
                                :items="levels"
                                :loading="false"
                                hide-actions
                                class="elevation-1">
                                <template slot="items" slot-scope="props">
                                    <td class="text-xs-left pa-2">{{ props.item.M_CustomerLevelName }}</td>
                                    <td class="text-xs-left pa-2">
                                        <v-text-field
                                            solo
                                            :value="props.item.M_PacketPriceNormal"
                                            hide-details
                                            reverse
                                            class="input-dense"
                                            disabled
                                        ></v-text-field>
                                    </td>
                                    <td class="text-xs-left pa-2">
                                        <v-text-field
                                            solo
                                            :value="props.item.M_PacketPriceSale"
                                            hide-details
                                            reverse
                                            class="input-dense"
                                            @input="change_price('Sale', props.index, $event)"
                                        ></v-text-field>
                                    </td>
                                    <td class="text-xs-left pa-2">
                                        <v-text-field
                                            solo
                                            :value="props.item.M_PacketPriceNormal - props.item.M_PacketPriceSale"
                                            hide-details
                                            reverse
                                            class="input-dense"
                                            disabled
                                        ></v-text-field>
                                    </td>
                                    
                                </template>
                            </v-data-table>
                            <v-divider></v-divider>
                        </v-layout>
                    </v-flex>
                </v-layout>
            </v-card-text>

            <v-card-actions>
                <v-btn color="primary" flat @click="dialog=!dialog">Batal</v-btn>
                <v-spacer></v-spacer>
                <v-btn color="primary" @click="save">Simpan</v-btn>                
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<style>
.input-dense .v-input__control {
    min-height: 36px !important;
}
</style>
<script>
module.exports = {
    data () {
        return {
            headers: [
                {
                    text: "LEVEL",
                    align: "left",
                    sortable: false,
                    width: "25%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "HARGA NORMAL",
                    align: "right",
                    sortable: false,
                    width: "25%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "HARGA PAKET",
                    align: "right",
                    sortable: false,
                    width: "25%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "SELISIH",
                    align: "right",
                    sortable: false,
                    width: "25%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ]
        }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.packetdetail.dialog_price },
            set (v) { this.$store.commit('packetdetail/set_common', ['dialog_price', v]) }
        },

        item_name () {
            return this.$store.state.packetdetail.selected_item.M_ItemName
        },

        item_code () {
            return this.$store.state.packetdetail.selected_item.M_ItemCode
        },

        packet_name () {
            if (this.$store.state.packet.selected_packet)
                return this.$store.state.packet.selected_packet.M_PacketName
            return '-'
        },

        v_space () {
            return 3
        },

        levels () {
            return this.$store.state.packetdetail.prices            
        }
    },

    methods : {
        save () {
            this.$store.dispatch('item_new/save')
        },

        change_price(type, i, v) {
            let x = this.levels
            x[i][`M_PacketPrice${type}`] = v

            this.$store.commit('packetdetail/update_prices', x)
        }
    },

    mounted () {
        // this.$store.dispatch('item_new/search_unit', [])
        // this.$store.dispatch('item_new/search_level_price', [])
    },

    watch : {
        
    }
}
</script>