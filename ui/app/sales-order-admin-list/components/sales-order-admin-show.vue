<template>
<v-dialog
    v-model="dialog"
    scrollable
    persistent 
    max-width="1000px"
    transition="dialog-transition"
>
    <v-card>
        <v-card-title primary-title class="pb-0 py-2 indigo lighten-2 white--text">
            <v-layout row wrap>
                <v-flex xs4>
                    <h3 class="headline mb-0 mt-3">DETAIL ORDER</h3>        
                </v-flex>
                <v-flex xs4 class="text-xs-right" v-show="selected_order.L_SoIsDS=='Y'">
                    <h3 class="">{{ selected_order.ds_customer_name }}</h3>
                    <p class="mb-0">{{ selected_order.ds_customer_address }}</p>
                    <p class="mb-0">{{ selected_order.ds_city_name }}</p>
                </v-flex>
                <v-flex xs4 v-show="selected_order.L_SoIsDS=='N'">&nbsp;</v-flex>
                <v-flex xs4 class="text-xs-right pl-2">
                    <h3>{{ selected_order.M_CustomerName }}</h3>
                    <p class="mb-0">{{ selected_order.M_CustomerAddress }}</p>
                    <p class="mb-0">{{ selected_order.city_name }}</p>
                </v-flex>
            </v-layout>
        </v-card-title>
        <v-card-text class="pt-2">
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
                            :disabled="props.item.L_SoDetailApproved != 'N'"
                            v-show="['SO.NEW','SO.Approved'].indexOf(selected_order.M_OrderStatusCode) > -1"></v-checkbox>
                        <v-btn color="red" class="btn-icon ma-0" dark small
                            v-show="['SO.Confirmed'].indexOf(selected_order.M_OrderStatusCode) > -1"
                            @click="cancel_item(props.item)"><v-icon>delete</v-icon></v-btn>
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
                            :disabled="!is_selected(props.item) || props.item.L_SoDetailApproved != 'N' || coupon_set"
                            class="zalfa-input-super-dense"
                            v-show="is_new"
                            
                        ></v-text-field>
                        <div v-show="!is_new">{{props.item.L_SoDetailApprovedQty}}</div>
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
                    <!-- <h4 :class="{'font-weight-light':!is_new}">Berat Item :</h4>
                    <h3 :class="{'font-weight-light':!is_new}">{{ weight_subtotal }} KG</h3> -->
                    <!-- <v-layout row wrap v-show="is_new">
                        <v-flex xs5>
                            <v-text-field
                                label="Berat Packing"
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
                        
                        <v-flex xs12 mt-2 v-show="selected_order.so_ads_number!=''||selected_order.so_mp_number!=''">
                            <v-divider class="dashed mt-1 mb-2"></v-divider>
                            <div>Kode Iklan : <b>{{ selected_order.so_ads_number }}</b> <br>No Pesanan MP : <b>{{ selected_order.so_mp_number }}</b></div>
                        </v-flex>
                    </v-layout> -->

                    <!-- <v-layout row wrap v-show="!is_new" class="mt-2">
                        <v-flex xs5>
                            <h4 class="font-weight-light">Berat Packing</h4>
                            <h3 class="font-weight-light">{{weight_add}} KG</h3>
                        </v-flex>
                        <v-flex xs1>
                            &nbsp;
                        </v-flex>
                        <v-flex xs5>
                            <h4>Berat Total</h4>
                            <h3 class="blue--text">{{weight_total}} KG</h3>
                        </v-flex>
                        <v-flex xs12>
                            <v-divider class="mt-1 dashed blue"></v-divider>
                        </v-flex>
                        <v-divider class="dashed mt-1 mb-2"></v-divider>
                        <v-flex xs12 mt-2 v-show="selected_order.so_ads_number!=''||selected_order.so_mp_number!=''">
                            Kode Iklan : <b>{{ selected_order.so_ads_number }}</b> <br>No Pesanan MP : <b>{{ selected_order.so_mp_number }}</b>
                        </v-flex>
                    </v-layout> -->
                    
                </v-flex>

                <v-flex xs9>
                    <v-layout row wrap mt-3>
                        <v-flex xs9 class="text-xs-right">
                            <h4>Subtotal</h4>
                        </v-flex>
                        <v-flex xs3 class="text-xs-right pr-2">
                            <h4><span class="font-weight-light">Rp</span> {{ one_money(total) }}</h4>
                        </v-flex>
                    </v-layout>
                    
                    <v-layout row wrap mt-2>

                        <v-flex xs12 v-show="!is_new">
                            <v-layout row wrap class="mb-1 d-none">
                                <v-flex xs3 offset-xs6 class="text-xs-right">
                                    Biaya Pengiriman
                                    <div class="caption">─ {{selected_expedition.M_ExpeditionName}}, {{selected_service.service}}</div>
                                </v-flex>
                                <v-flex xs3 class="text-xs-right pr-2">
                                    <h4><span class="font-weight-light">Rp</span> {{one_money(courier_cost)}}</h4>
                                </v-flex>
                                <!-- <v-flex offset-xs6 xs6 pr-2>
                                    <v-divider class="dashed"></v-divider>
                                </v-flex>         -->
                            </v-layout>
                            <v-layout row wrap v-show="selected_order.L_SoCouponAmount>0">
                                <v-flex xs3 offset-xs6 class="text-xs-right">
                                    Kupon
                                    <span class="caption">─ </span>
                                </v-flex>
                                <v-flex xs3 class="text-xs-right pr-2">
                                    <h4><span class="font-weight-light">Rp</span> {{one_money(selected_order.L_SoCouponAmount)}}</h4>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap>
                                <v-flex offset-xs6 xs6 pr-2>
                                    <v-divider class="dashed"></v-divider>
                                </v-flex>        
                            </v-layout>
                            
                        </v-flex>
                        
                        <v-flex xs12 v-show="is_new">
                            <v-layout row wrap>
                                <v-flex xs2 class="text-xs-right pr-4 pt-2"
                                :class="['EX.OTHER','EX.FREE'].indexOf(selected_expedition.M_ExpeditionCode)<0?'':'offset-xs3'">
                                    Ekspedisi
                                </v-flex>

                                <v-flex xs4 class="text-xs-right"
                                :class="['EX.OTHER','EX.FREE'].indexOf(selected_expedition.M_ExpeditionCode)<0?'pr-2':''"
                                >
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
                                        :disabled="true"
                                    >
                                    </v-select>
                                    <div class="mt-1 text-right caption orange--text" v-show="selected_expedition.M_ExpeditionCode=='EX.FREE'">{{selected_order.exp_note}}</div>
                                    <div class="mt-1 text-right caption orange--text" v-show="selected_expedition.M_ExpeditionCode=='EX.OTHER'">{{selected_order.exp_other}}</div>
                                </v-flex>

                                <v-flex xs3 v-show="['EX.OTHER','EX.FREE'].indexOf(selected_expedition.M_ExpeditionCode)<0">
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
                                        :disabled="true"
                                    ></v-select>
                                </v-flex>
                                
                                <v-flex xs3 class="text-xs-right pr-2 pl-5">
                                    <v-text-field
                                        solo
                                        hide-details
                                        class="zalfa-input-super-dense"
                                        reverse
                                        v-model="courier_cost"
                                        :readonly="selected_expedition.M_ExpeditionCode!='EX.OTHER'"
                                        ></v-text-field>
                                </v-flex>
                            </v-layout>

                            <v-layout row wrap v-show="coupon_set" class="pt-2">
                                <v-flex xs3 offset-xs6 class="text-xs-right">
                                    Kupon : 
                                    <span class="font-weight-bold">{{coupon_code}}</span>
                                    <a href="javascript:;" @click="coupon_clear"><v-icon small>clear</v-icon></a>
                                </v-flex>
                                <v-flex xs3 class="text-xs-right pr-2">
                                    <h4><span class="font-weight-light">Rp</span> {{one_money(coupon_amounts)}}</h4>
                                </v-flex>

                                <v-layout row wrap class="py-1">
                                    <v-flex offset-xs6 xs6 pr-2>
                                        <v-divider class="dashed"></v-divider>
                                    </v-flex>        
                                </v-layout>
                            </v-layout>

                            <v-layout row wrap v-show="!coupon_set" class="pt-2">
                                <v-flex xs3 offset-xs3 class="text-xs-right pr-2 pt-2">
                                    Kupon
                                </v-flex>
                                <v-flex xs3>
                                    <v-text-field
                                        solo
                                        hide-details
                                        v-model="coupon_code"
                                        @change="check_coupon"
                                    ></v-text-field>
                                </v-flex>
                                <v-flex xs3 class="text-xs-right pr-2 pt-2">
                                    <h4><span class="font-weight-light">Rp</span> {{one_money(selected_order.L_SoCouponAmount)}}</h4>
                                </v-flex>

                                <v-layout row wrap class="py-1">
                                    <v-flex offset-xs6 xs6 pr-2>
                                        <v-divider class="dashed"></v-divider>
                                    </v-flex>        
                                </v-layout>
                            </v-layout>

                        </v-flex>
                    </v-layout>

                    <v-layout row wrap class="pt-2" v-show="selected_order.L_SoIsCOD=='Y'">
                        <v-flex xs3 offset-xs6 class="text-xs-right">
                            Sub Total
                        </v-flex>
                        <v-flex xs3 class="text-xs-right pr-2">
                            <h4><span class="font-weight-light">Rp</span> {{one_money(sub_total)}}</h4>
                        </v-flex>
                        <v-flex xs3 offset-xs6 class="text-xs-right mt-1">
                            Biaya COD
                        </v-flex>
                        <v-flex xs3 class="text-xs-right pr-2 mt-1 mb-1">
                            <h4><span class="font-weight-light">Rp</span> {{one_money(cod_cost)}}</h4>
                        </v-flex>
                        <v-flex offset-xs6 xs6 pr-2>
                            <v-divider class="dashed"></v-divider>
                        </v-flex>        
                    </v-layout>

                    <v-layout row wrap mt-3>
                        <v-flex xs9 class="text-xs-right">
                            <h3>Total</h3>
                        </v-flex>
                        <v-flex xs3 class="text-xs-right pr-2">
                            <h3><span class="font-weight-light">Rp</span> {{ one_money(grand_total) }}</h3>
                        </v-flex>
                    </v-layout>
                </v-flex>
            </v-layout>
            

        </v-card-text>
        <v-card-actions class="indigo lighten-2">
            <v-btn flat color="white" @click="dialog=!dialog" :dark="false">Tutup</v-btn>
            <v-spacer></v-spacer>
            <v-btn color="red" @click="cancel_order" 
                :disabled="true" dark>Tolak Semua</v-btn>
            <v-btn color="primary" @click="approve" 
                v-show="['SO.NEW','SO.Approved'].indexOf(selected_order.M_OrderStatusCode) > -1" 
                :disabled="!btn_appr_enable">Setujui</v-btn>
            <v-btn color="red" @click="cancel_order" 
                v-show="['SO.Confirmed'].indexOf(selected_order.M_OrderStatusCode) > -1"
                dark ><v-icon small class="mr-1">delete</v-icon> Batalkan Order</v-btn>
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

.dashed {
    border-style: dashed !important;
    background: none !important;
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
                    class: "pa-2 indigo lighten-3 white--text"
                },
                {
                    text: "NAMA BARANG",
                    align: "center",
                    sortable: false,
                    width: "35%",
                    class: "pa-2 indigo lighten-3 white--text"
                },
                {
                    text: "QTY",
                    align: "right",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 indigo lighten-3 white--text"
                },
                {
                    text: "APPR QTY",
                    align: "right",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 indigo lighten-3 white--text"
                },
                {
                    text: "STOK",
                    align: "right",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 indigo lighten-3 white--text"
                },
                {
                    text: "HARGA",
                    align: "right",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 indigo lighten-3 white--text"
                },
                {
                    text: "DISKON",
                    align: "right",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 indigo lighten-3 white--text"
                },
                {
                    text: "HARGA TOTAL",
                    align: "right",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 indigo lighten-3 white--text"
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

        // selected_order () {
        //     return this.$store.state.salesorder.selected_order
        // },

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
            console.log(this.$store.state.salesorder.selected_order)
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

        sub_total () {
            return parseFloat(this.total) + parseFloat(this.courier_cost) - this.coupon_amounts
        },

        grand_total () {
            return parseFloat(this.sub_total) + parseFloat(this.selected_order.L_SoCODCost)
            // return parseFloat(this.total) + parseFloat(this.courier_cost)
        },

        dialog_confirm : {
            get () { return this.$store.state.dialog_confirm },
            set (v) { this.$store.commit('set_dialog_confirm', v) }
        },

        btn_appr_enable () {
            if (this.selected_items.length < 1) return false
            if (this.selected_order.L_SoApproved == 'Y') return false
            if (this.warn_item_qty_0) return false
            
            return true
        },

        warn_item_qty_0 () {
            for (let i of this.items) {
                if (this.selected_items.indexOf(i.L_SoDetailID) > -1)
                    if (i.L_SoDetailApprovedQty < 1)
                        return true
            }

            return false
        },

        is_new () {
            if (this.selected_order.M_OrderStatusCode == 'SO.NEW')
                return true
            return false
        },

        coupon_set () {
            if (this.coupon_amounts > 0)
                return true
            return false
        },

        cod_cost () {
            if (this.is_new) {
                if (!this.selected_expedition)
                    return 0
                if (this.selected_order.cod_item_only == "Y")
                    return (this.sub_total-this.courier_cost) * this.selected_expedition.M_ExpeditionCODRate / 100
                return this.sub_total * this.selected_expedition.M_ExpeditionCODRate / 100
            } else {
                return this.selected_order.L_SoCODCost
            }
        },

        coupon_amount () {
            return this.$store.state.coupon.coupon_amount
        },

        coupon_type () {
            return this.$store.state.coupon.coupon_type
        },

        coupon_amounts () {
            return this.$store.state.coupon.coupon_amounts
        },

        coupon_note () {
            return this.$store.state.coupon.coupon_note
        },

        coupon_error () {
            return this.$store.state.coupon.coupon_error
        },

        coupon_error_msg () {
            return this.$store.state.coupon.coupon_error_msg
        },

        coupon_code : {
            get () { return this.$store.state.coupon.coupon_code },
            set (v) { this.$store.commit('coupon/set_common', ['coupon_code', v]) }
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

        coupon_clear () {
            let so = this.selected_order
            so.coupon_code = ''
            so.L_SoCouponAmount = 0
            this.$store.commit('salesorder/set_selected_order', so)

            this.$store.commit('coupon/clear_coupon')
        },

        check_coupon () {
            let x = []
            for (let i of this.items)
                x.push({
                    item_id:i.M_ItemID,
                    item_name:i.L_SoDetailM_ItemName,
                    item_qty:i.L_SoDetailQty,
                    is_packet:i.L_SoDetailIsPacket,
                    item_subtotal:i.L_SoDetailSubTotal
                })
            this.$store.commit('coupon/set_items', x)
            this.$store.commit('coupon/set_common', ['expedition_id', this.selected_expedition.M_ExpeditionID])
            this.$store.dispatch('coupon/check_coupon')
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