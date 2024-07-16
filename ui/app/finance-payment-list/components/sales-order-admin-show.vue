<template>
<v-dialog
    v-model="dialog"
    scrollable
    persistent 
    max-width="1000px"
    transition="dialog-transition"
>
    <v-card>
        <v-card-title primary-title class="pb-0 pt-3">
            <v-layout row wrap>
                <v-flex xs6>
                    <h3 class="headline mb-0">Detail Order</h3>        
                </v-flex>
                <v-flex xs6 class="text-xs-right">
                    <h3>{{ selected_order.M_CustomerName }}</h3>
                    <p class="mb-0">{{ selected_order.M_CustomerAddress }}</p>
                    <p class="mb-0">{{ selected_order.city_name }}</p>
                </v-flex>
            </v-layout>
        </v-card-title>
        <v-card-text>
            <!-- <v-layout row wrap>
                <v-flex xs6>
                    <v-select
                        :items="expeditions"
                        v-model="selected_expedition"
                        label="Ekspedisi"
                        return-object
                        item-value="M_ExpeditionID"
                        item-text="M_ExpeditionName"
                        hide-details
                    ></v-select>
                </v-flex>
            </v-layout> -->
            <v-data-table 
                :headers="headers"
                :items="items"
                :loading="false"
                hide-actions
                class="elevation-1">
                <template slot="items" slot-scope="props">
                    <td class="text-xs-left pa-2" @click="select(props.item)">
                        <v-checkbox label="" 
                            :value="props.item.L_SoDetailID"
                            v-model="selected_items"
                            hide-details
                            :disabled="props.item.L_SoDetailApproved != 'N'"></v-checkbox>
                    </td>
                    <td class="text-xs-left pa-2" @click="select(props.item)" v-bind:class="is_selected(props.item) ? '':'zalfa-line-trough'">
                        {{ props.item.M_ItemName }}</td>
                    <td class="text-xs-left pa-2 text-xs-right" @click="select(props.item)">{{ one_money(props.item.L_SoDetailQty) }}</td>
                    <td class="text-xs-left pa-2 text-xs-right" @click="select(props.item)">
                        <v-text-field
                            hide-details
                            type="number"
                            solo
                            :value="props.item.L_SoDetailApprovedQty"
                            @input="change_appr_qty(props.index, $event)"
                            reverse
                            :disabled="!is_selected(props.item) || props.item.L_SoDetailApproved != 'N'"
                            class="zalfa-input-super-dense"
                        ></v-text-field>
                    </td>
                    <td class="text-xs-left pa-2 text-xs-right" @click="select(props.item)">{{ one_money(props.item.I_StockQty) }}</td>
                    <td class="text-xs-left pa-2 text-xs-right" @click="select(props.item)">{{ one_money(props.item.L_SoDetailPrice) }}</td>
                    <td class="text-xs-left pa-2 text-xs-right" @click="select(props.item)">{{ one_money(props.item.L_SoDetailDiscTotal) }}</td>
                    <td class="text-xs-left pa-2 text-xs-right" @click="select(props.item)">{{ one_money(props.item.L_SoDetailSubTotal) }}</td>
                    
                    <!-- <td class="text-xs-left pa-0" @click="select(props.item)">
                        <v-btn color="primary" class="btn-icon ma-0" small @click="edit(props.item)"><v-icon>create</v-icon></v-btn>
                        <v-btn color="red" dark class="btn-icon ma-0" small @click="del(props.item)"><v-icon>delete</v-icon></v-btn>
                    </td> -->
                    <!-- <td class="text-xs-center pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.M_DoctorHP}}</td>
                    <td class="text-xs-left pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.status}}</td> -->
                </template>
            </v-data-table>

            <v-layout row wrap>
                <v-flex xs3 mt-3>
                    <h4>Total berat :</h4>
                    <h3>{{ weight_subtotal }} KG</h3>
                    <v-layout row wrap>
                        <v-flex xs5>
                            <v-text-field
                                label="Berat Tambahan"
                                hide-details
                                suffix="KG"
                                placeholder="0"
                                v-model="weight_add"
                            ></v-text-field>
                        </v-flex>
                        <v-flex xs1>
                            &nbsp;
                        </v-flex>
                        <v-flex xs5>
                            <v-text-field
                                label="Berat Total"
                                hide-details
                                suffix="KG"
                                readonly
                                :value="weight_total"
                            ></v-text-field>
                        </v-flex>
                    </v-layout>
                    
                </v-flex>

                <v-flex xs9>
                    <v-layout row wrap mt-3>
                        <v-flex xs9 class="text-xs-right">
                            <h4>Subtotal</h4>
                        </v-flex>
                        <v-flex xs3 class="text-xs-right pr-2">
                            <h4>{{ one_money(total) }}</h4>
                        </v-flex>
                    </v-layout>
                    <v-layout row wrap mt-2>
                        <v-flex xs2 class="text-xs-right pr-4 pt-2">
                            Ekspedisi
                        </v-flex>
                        <v-flex xs4 class="text-xs-right pr-2">
                            <v-select
                                :items="expeditions"
                                v-model="selected_expedition"
                                label=""
                                return-object
                                item-value="M_ExpeditionID"
                                item-text="M_ExpeditionName"
                                hide-details
                                solo
                                dense
                                height="36"
                                class="zalfa-input-super-dense"
                            ></v-select>
                        </v-flex>
                        <v-flex xs3>
                            <v-select
                                :items="services"
                                v-model="selected_service"
                                label="Service"
                                return-object
                                item-value="service"
                                item-text="service"
                                hide-details
                                solo
                                dense
                                height="36"
                                class="zalfa-input-super-dense"
                            ></v-select>
                        </v-flex>
                        
                        <v-flex xs3 class="text-xs-right pr-2 pl-5">
                            <v-text-field
                                solo
                                hide-details
                                class="zalfa-input-super-dense"
                                reverse
                                v-model="courier_cost"
                                ></v-text-field>
                        </v-flex>
                    </v-layout>

                    <v-layout row wrap mt-3>
                        <v-flex xs9 class="text-xs-right">
                            <h3>Total</h3>
                        </v-flex>
                        <v-flex xs3 class="text-xs-right pr-2">
                            <h3>{{ one_money(total + courier_cost) }}</h3>
                        </v-flex>
                    </v-layout>
                </v-flex>
            </v-layout>
            

        </v-card-text>
        <v-card-actions>
            <v-btn flat color="primary" @click="dialog=!dialog">Tutup</v-btn>
            <v-spacer></v-spacer>
            <v-btn color="red" @click="dialog=!dialog" 
                :disabled="selected_items.length > 0" dark>Tolak Semua</v-btn>
            <v-btn color="primary" @click="approve" 
                :disabled="selected_items.length < 1 || selected_order.L_SoApproved != 'Y'">Setujui</v-btn>
        </v-card-actions>
    </v-card>
</v-dialog>
    
</template>

<style>
.zalfa-line-trough {
    text-decoration: line-through;
}

.zalfa-input-super-dense .v-input__control {
    min-height: 36px !important;
}
</style>

<script>
module.exports = {
    data () {
        return {
            w : [],
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
                    text: "QTY",
                    align: "right",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "APPR QTY",
                    align: "right",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "STOK",
                    align: "right",
                    sortable: false,
                    width: "10%",
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
                }
            ]
        }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.salesorder.dialog_item },
            set (v) { this.$store.commit('salesorder/set_common', ['dialog_item', v]) }
        },

        items () {
            return this.$store.state.salesorder.items
        },

        selected_order () {
            return this.$store.state.salesorder.selected_order
        },

        selected_items : {
            get () { return this.$store.state.salesorder.selected_items },
            set (v) { this.$store.commit('salesorder/set_selected_items', v) }
        },

        selected_order () {
            return this.$store.state.salesorder.selected_order
        },

        expeditions () {
            return this.$store.state.salesorder.expeditions
        },

        selected_expedition : {
            get () { return this.$store.state.salesorder.selected_expedition },
            set (v) { 
                this.$store.commit('salesorder/set_selected_expedition', v) 
                this.$store.dispatch('salesorder/search_service', {})
            }
        },

        services () {
            return this.$store.state.salesorder.services
        },

        selected_service : {
            get () { return this.$store.state.salesorder.selected_service },
            set (v) { 
                this.$store.commit('salesorder/set_selected_service', v) 
                this.courier_cost = v.cost[0].value
            }
        },

        courier_cost : {
            get () { return this.$store.state.salesorder.courier_cost },
            set (v) { this.$store.commit('salesorder/set_common', ['courier_cost', v]) }
        },

        total () {
            let items = this.items
            let total = 0
            for (let item of items)
                total += Math.round(item.L_SoDetailSubTotal)

            return total
        },

        weight_subtotal () {
            return (this.$store.state.salesorder.selected_order.L_SoSubTotalWeight / 1000)
        },

        weight_add : {
            get () { return this.$store.state.salesorder.weight_add / 1000 },
            set (v) { 
                this.$store.commit('salesorder/set_common', ['weight_add', v*1000]) 

                let x = (parseFloat(this.weight_subtotal) + parseFloat(v)) * 1000 
                this.$store.commit('salesorder/set_common', ['weight_total', x]) 
            }
        },

        weight_total () {
            return parseFloat(this.$store.state.salesorder.weight_total/1000).toFixed(2)
        }
    },

    methods : {
        one_money (x) {
            return one_money(x)
        },

        select (x) {
            this.$store.commit('salesorder/set_selected_item', x)
        },

        is_selected (x) {
            let y = x.L_SoDetailID
            if (this.selected_items.indexOf(y) > -1)
                return true

            return false
        },

        change_appr_qty (i, e) {
            let items = this.items
            items[i].L_SoDetailApprovedQty = e
            this.$store.commit('salesorder/update_items', items)
        },

        approve () {
            this.$store.dispatch('salesorder/approve')
        }
    },

    mounted () {
        this.$store.dispatch('salesorder/search_expedition')
    },

    watch : {
        dialog (n, o) {
        }
    }
}
</script>