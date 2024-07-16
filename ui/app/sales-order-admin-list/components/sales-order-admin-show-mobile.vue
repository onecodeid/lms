<template>
<v-dialog
    v-model="dialog"
    scrollable
    persistent 
    max-width="1000px"
    transition="dialog-transition"
    fullscreen
>
    <v-card>
        <v-card-title primary-title class="pa-2 primary white--text">
            <v-layout row wrap>
                <v-flex xs12 sm4>
                    <v-btn flat color="primary" class="ma-0 btn-icon mr-2 hidden-md-and-up" @click="dialog=!dialog" style="float:left">
                        <v-icon class="white--text" medium>arrow_back</v-icon>
                    </v-btn>
                    <h3 class="headline mb-0 pr-5 text-xs-center">Detail Order</h3>        
                </v-flex>                
            </v-layout>
        </v-card-title>
        <v-card-text class="px-2">
            <v-layout row wrap>
                <v-flex xs12 sm6 mb-2>
                    <h3>{{ selected_order.M_CustomerName }}</h3>
                    <p class="mb-0">{{ selected_order.M_CustomerAddress }}</p>
                    <p class="mb-0">{{ selected_order.city_name }}</p>
                </v-flex>
                <v-flex xs12 sm6 offset-xs0 offset-sm0 mb-2 class="text-xs-left text-sm-right pl-1" 
                    v-show="selected_order.L_SoIsDS=='Y'"
                    :style="{'border-left: 7px solid purple':$vuetify.breakpoint.xsOnly}">
                    <h3 class="purple--text"><i>{{ selected_order.ds_customer_name }}</i></h3>
                    <p class="mb-0 purple--text"><i>{{ selected_order.ds_customer_address }}</i></p>
                    <p class="mb-0 purple--text"><i>{{ selected_order.ds_city_name }}</i></p>
                </v-flex>
            </v-layout>

            <v-layout row wrap>
                <v-flex xs12>
                    
                    <v-card v-for="(item, n) in items" v-bind:key="n" class="mb-2">
                    <v-card-text class="pa-2">
                        <v-layout row wrap>
                            <v-flex xs3 sm1>
                                <v-img :src="item.img_url?item.img_url:'../assets/img/zalfa-item-img.png'"></v-img>        
                            </v-flex>
                            <v-flex xs9 sm11 pl-2>
                                <v-layout row wrap>
                                    <v-flex xs12 sm8>
                                        <h3 class="subheading">{{ item.M_ItemName ? item.M_ItemName : item.M_PacketName }}</h3>        
                                    </v-flex>
                                    <v-flex xs4 class="text-xs-right blue--text hidden-xs-only">
                                        <b>Rp {{ one_money(item.L_SoDetailSubTotal) }}</b>
                                    </v-flex>
                                    <v-flex xs8 sm6>
                                        Harga : Rp {{ one_money(item.L_SoDetailPrice) }}
                                    </v-flex>
                                    <v-flex xs4 sm6>
                                        Qty : {{ one_money(item.L_SoDetailQty) }} / <span class="green--text">{{item.L_SoDetailApprovedQt}}</span>
                                    </v-flex>
                                    <v-flex xs6 class="hidden-sm-and-up" pt-3>
                                        Sub Total
                                    </v-flex>
                                    <v-flex xs6 class="text-xs-right blue--text hidden-sm-and-up" pt-3>
                                        <b>Rp {{ one_money(item.L_SoDetailSubTotal) }}</b>
                                    </v-flex>
                                </v-layout>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                </v-card>
                </v-flex>
            </v-layout>
                

                <v-layout row wrap>
                    <v-flex xs12>
                        <v-divider class="mt-2 mb-2"></v-divider>
                    </v-flex>
                    <v-flex xs4>
                        <div>Berat Item :</div>
                        <div>{{ weight_subtotal }} KG</div>
                    </v-flex>
                    <v-flex xs4 pr-2>
                        <v-text-field
                                label="Berat Packing"
                                hide-details
                                suffix="KG"
                                placeholder="0"
                                v-model="weight_add"
                                :readonly="['SO.NEW','SO.Approved'].indexOf(selected_order.M_OrderStatusCode) < 0"
                            ></v-text-field>
                    </v-flex>
                    <v-flex xs4 pl-2>
                        <v-text-field
                                label="Berat Total"
                                hide-details
                                suffix="KG"
                                readonly
                                :value="weight_total"
                            ></v-text-field>
                    </v-flex> 
                </v-layout>

                <v-layout row wrap class="mb-1">
                    <v-flex xs12>
                        <v-divider class="mt-2 mb-2"></v-divider>
                    </v-flex>
                    <v-flex xs6>
                        Sub Total
                    </v-flex>
                    <v-flex xs6 class="text-xs-right" pr-2>
                        <h3 class="subheading blue--text"><b>Rp {{ one_money(total) }}</b></h3>
                    </v-flex>
                </v-layout>

                <v-layout row wrap class="mb-1">
                    <v-flex xs6 sm9>
                        Biaya Kirim
                        <span class="hidden-xs-only">: {{ selected_expedition.M_ExpeditionName }}, {{ selected_order.L_SoExpService }}</span>
                    </v-flex>
                    <v-flex xs6 sm3 class="text-xs-right" pr-2>
                        Rp {{ one_money(courier_cost) }}
                    </v-flex>
                    <v-flex xs12 class="caption hidden-sm-and-up">
                        {{ selected_expedition.M_ExpeditionName }}, {{ selected_order.L_SoExpService }}
                    </v-flex>

                    <v-flex xs12 sm6 :class="{'pr-2':$vuetify.breakpoint.smAndUp}" mb-2>
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
                            v-show="['SO.NEW','SO.Approved'].indexOf(selected_order.M_OrderStatusCode) > -1"
                        ></v-select>
                    </v-flex>
                    <v-flex xs12 sm6 mb-2>
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
                            v-show="['SO.NEW','SO.Approved'].indexOf(selected_order.M_OrderStatusCode) > -1"
                        ></v-select>
                    </v-flex>
                </v-layout>

                <v-layout row wrap>
                    <v-flex xs12>
                        <v-divider class="mt-2 mb-2"></v-divider>
                    </v-flex>
                    <v-flex xs3 pt-3>
                        <h3 class="subheading">Total</h3>
                    </v-flex>
                    <v-flex xs9 class="text-xs-right" pr-2>
                        <h3 class="display-1">Rp {{ one_money(grand_total) }}</h3>
                    </v-flex>
                </v-layout>
            

        </v-card-text>
        <v-card-actions class="pa-0">
            <v-layout row>
                <v-flex xs12 v-show="selected_order.M_OrderStatusCode=='SO.NEW'">
                    <v-btn color="red" @click="dialog=!dialog" 
                    :disabled="true" :dark="selected_items.length == 0" block large
                    >Tolak Semua</v-btn>
                </v-flex>
                <v-flex xs12 v-show="['SO.NEW','SO.Approved'].indexOf(selected_order.M_OrderStatusCode) > -1">
                    <v-btn color="primary" @click="approve"
                    :disabled="selected_items.length < 1 || selected_order.L_SoApproved != 'N'"
                    large block>Setujui</v-btn>
                </v-flex>
                <v-flex xs12 v-show="['SO.Confirmed'].indexOf(selected_order.M_OrderStatusCode) > -1">
                    <v-btn color="red" @click="cancel_order"
                    dark large block putline><v-icon small class="mr-1">delete</v-icon> Batal Order</v-btn>
                </v-flex>
                <v-flex xs12 v-show="['SO.Confirmed'].indexOf(selected_order.M_OrderStatusCode) > -1">
                    <v-btn color="orange" @click="payment_confirmation"
                    dark large block><v-icon small class="mr-1">payment</v-icon> Konf Bayar</v-btn>
                </v-flex>
            </v-layout>            
        </v-card-actions>

        <common-dialog-confirm v-if="dialog_confirm" @confirm="do_cancel_item"></common-dialog-confirm>
        <!-- <common-dialog-confirm v-if="dialog_confirm_2" @confirm="do_cancel_order"></common-dialog-confirm> -->
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
    components : {
        "common-dialog-confirm": httpVueLoader("../../common/components/common-dialog-confirm.vue")
    },

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
        },

        grand_total () {
            return parseFloat(this.total) + parseFloat(this.courier_cost)
        },

        dialog_confirm : {
            get () { return this.$store.state.dialog_confirm },
            set (v) { this.$store.commit('set_dialog_confirm', v) }
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
            let z = this.selected_items.indexOf(y)
            if (z > -1)
                return true

            let items = this.items
            for (let i in items)
                if (items[i].L_SoDetailID == y)
                    items[i].L_SoDetailApprovedQty = 0
            this.$store.commit('salesorder/update_items', items)
            return false
        },

        change_appr_qty (i, e) {
            let items = this.items
            items[i].L_SoDetailApprovedQty = e
            this.$store.commit('salesorder/update_items', items)
        },

        approve () {
            this.$store.dispatch('salesorder/approve')
        },

        cancel_item (x) {
            this.select(x)
            this.dialog_confirm = true
        },

        do_cancel_item () {
            this.$store.dispatch('salesorder/cancel_item')
        },

        cancel_order () {
            this.$store.dispatch('salesorder/cancel')
            this.dialog = false
        },

        payment_confirmation (x) {
            this.$store.commit('payment/set_common', ['transfer_amount', this.grand_total])
            this.$store.commit('salesorder/set_common', ['dialog_payment', true])
        }
    },

    mounted () {
        this.$store.dispatch('salesorder/search_expedition')
    },

    watch : {
        weight_total (v, o) {
            this.$store.dispatch('salesorder/search_service', {})              
        }
    }
}
</script>