<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs9>
                    <h3 class="display-1 font-weight-light zalfa-text-title">Masterdata Coupon</h3>
                </v-flex>
                <v-flex xs3 class="text-xs-right">
                    <!-- <v-btn color="success" class="ma-0 btn-icon" @click="add">
                        <v-icon>add</v-icon>
                    </v-btn> -->

                    <v-text-field
                        solo
                        hide-details
                        placeholder="Pencarian" v-model="query"
                        @change="search"
                    >
                        <template v-slot:append-outer>
                            <v-btn color="primary" class="ma-0 btn-icon" @click="search">
                                <v-icon>search</v-icon>
                            </v-btn>      

                            <v-btn color="success" class="ma-0 ml-2 btn-icon" @click="add">
                                <v-icon>add</v-icon>
                            </v-btn>  
                        </template>
                    </v-text-field>
                </v-flex>
            </v-layout>
        </v-card-title>
        <v-card-text class="pt-2">
            <v-data-table 
                :headers="headers"
                :items="coupons"
                :loading="false"
                hide-actions
                class="elevation-1">
                <template slot="items" slot-scope="props">
                    
                    <td class="text-xs-left pa-2" @click="select(props.item)" :class="{'red lighten-4':props.item.expired=='Y'}">{{ props.item.M_CouponCode }}</td>
                    <td class="text-xs-left pa-2" @click="select(props.item)" :class="{'red lighten-4':props.item.expired=='Y'}">
                        {{ props.item.M_CouponTypeName }}
                        <span v-show="props.item.expired=='Y'" class="red--text font-weight-bold"><i>( expired )</i></span>
                        </td>
                    <td class="text-xs-center pa-2" @click="select(props.item)" :class="{'red lighten-4':props.item.expired=='Y'}">
                        <b>{{ props.item.M_CouponStartDate.split('-').reverse().join('-') }}</b> s/d
                        <b>{{ props.item.M_CouponEndDate.split('-').reverse().join('-') }}</b>
                    </td>
                    <td class="text-xs-center pa-2" @click="select(props.item)" :class="{'red lighten-4':props.item.expired=='Y'}">
                        <div v-show="props.item.M_CouponMaxSpend > 0">Rp {{props.item.M_CouponMinSpend}} - Rp {{props.item.M_CouponMaxSpend}}</div>
                        <div v-show="props.item.M_CouponMaxSpend == 0">Rp ~</div>
                    </td>
                    <td class="text-xs-right pa-2" @click="select(props.item)" :class="{'red lighten-4':props.item.expired=='Y'}"><b>Rp {{ one_money(props.item.M_CouponAmount) }}</b></td>
                    <td class="text-xs-center pa-2" @click="select(props.item)" :class="{'red lighten-4':props.item.expired=='Y'}">{{ props.item.M_CouponMaxQty == 0 ? '~' : one_money(props.item.max_qty) }} / {{ one_money(props.item.used_qty) }}</td>
                    
                    <td class="text-xs-center pa-0" @click="select(props.item)" :class="{'red lighten-4':props.item.expired=='Y'}">
                        <v-btn color="primary" class="btn-icon ma-0" small @click="edit(props.item)"><v-icon>create</v-icon></v-btn>
                        <v-btn color="red" dark class="btn-icon ma-0" small @click="del(props.item)"><v-icon>delete</v-icon></v-btn>
                    </td>
                </template>
            </v-data-table>
            <v-divider></v-divider>
            <v-pagination
                style="margin-top:10px;margin-bottom:10px"
                v-model="curr_page"
                :length="xtotal_page"
                @input="change_page"
            ></v-pagination>
        </v-card-text>
        
        <common-dialog-delete :data="coupon_id" @confirm_del="confirm_del" v-if="dialog_delete"></common-dialog-delete>
    </v-card>
</template>

<style scoped>
.v-text-field.v-text-field--solo .v-input__control {
    min-height: 36px;
}
.v-text-field.v-text-field--solo .v-input__append-outer {
    margin-top: 0px;
    margin-left: 0px;
}
</style>

<script>
module.exports = {
    components : {
        "common-dialog-delete" : httpVueLoader("../../common/components/common-dialog-delete.vue")
    },

    data () {
        return {
            headers: [
                {
                    text: "KODE",
                    align: "left",
                    sortable: false,
                    width: "7%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "TIPE",
                    align: "left",
                    sortable: false,
                    width: "31%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "MASA BERLAKU",
                    align: "center",
                    sortable: false,
                    width: "14%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "RANGE BELANJA",
                    align: "center",
                    sortable: false,
                    width: "14%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "BESAR KUPON",
                    align: "right",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "QTY / USED",
                    align: "center",
                    sortable: false,
                    width: "14%",
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
        coupons () {
            return this.$store.state.coupon.coupons
        },

        dialog_delete () {
            return this.$store.state.dialog_delete
        },

        coupon_id () {
            return this.$store.state.coupon.selected_coupon.M_CouponID
        },

        query : {
            get () { return this.$store.state.coupon.search },
            set (v) { this.$store.commit('coupon/update_search', v) }
        },

        curr_page : {
            get () { return this.$store.state.coupon.current_page },
            set (v) { this.$store.commit('coupon/update_current_page', v) }
        },

        xtotal_page () {
            return this.$store.state.coupon.total_coupon_page
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        },

        add () {
            this.$store.commit('coupon_new/set_common', ['edit', false])
            this.$store.commit('coupon_new/set_common', ['coupon_code', ''])
            this.$store.commit('coupon_new/set_common', ['coupon_amount', ''])
            this.$store.commit('coupon_new/set_common', ['coupon_amount_t', 'R'])
            this.$store.commit('coupon_new/set_common', ['coupon_sdate', new Date().toISOString().substr(0, 10)])
            this.$store.commit('coupon_new/set_common', ['coupon_edate', new Date().toISOString().substr(0, 10)])
            this.$store.commit('coupon_new/set_common', ['coupon_min_spend', ''])
            this.$store.commit('coupon_new/set_common', ['coupon_max_spend', ''])
            this.$store.commit('coupon_new/set_common', ['coupon_qty', 0])
            this.$store.commit('coupon_new/set_common', ['coupon_used', 0])
            this.$store.commit('coupon_new/set_common', ['coupon_reset', false])
            this.$store.commit('coupon_new/set_selected_type', null)
            this.$store.commit('coupon_new/set_selected_item', [])
            this.$store.commit('coupon_new/set_selected_packet', [])
            this.$store.commit('coupon_new/set_common', ['dialog_new', true])
        },

        edit (x) {
            this.select(x)
            let sc = x
            this.$store.commit('coupon_new/set_common', ['edit', true])
            this.$store.commit('coupon_new/set_common', ['coupon_code', x.M_CouponCode])
            this.$store.commit('coupon_new/set_common', ['coupon_amount', x.M_CouponAmount])
            this.$store.commit('coupon_new/set_common', ['coupon_amount_p', x.M_CouponAmountPercent])
            this.$store.commit('coupon_new/set_common', ['coupon_sdate', x.M_CouponStartDate])
            this.$store.commit('coupon_new/set_common', ['coupon_edate', x.M_CouponEndDate])
            this.$store.commit('coupon_new/set_common', ['coupon_min_spend', x.M_CouponMinSpend])
            this.$store.commit('coupon_new/set_common', ['coupon_max_spend', x.M_CouponMaxSpend])
            this.$store.commit('coupon_new/set_common', ['coupon_qty', x.M_CouponMaxQty])
            this.$store.commit('coupon_new/set_common', ['coupon_used', x.used_qty])
            this.$store.commit('coupon_new/set_common', ['coupon_reset', false])
            
            let t = 'R'
            if (Math.round(x.M_CouponAmountPercent) > 0) t = 'P'
            this.$store.commit('coupon_new/set_common', ['coupon_amount_t', t])

            for (let t of this.$store.state.coupon_new.types)
                if (t.M_CouponTypeID == x.M_CouponM_CouponTypeID)
                    this.$store.commit('coupon_new/set_selected_type', t)

            let items = []
            let packets = []
            let exps = []
            if (x.M_CouponTypeCode == "COUPON.PRODUCT") {
                for (let item of this.$store.state.coupon_new.items) {                    
                    if (x.items.indexOf(parseInt(item.M_ItemID)) > -1)
                        items.push(item)
                }
            }
            if (x.M_CouponTypeCode == "COUPON.PACKET") {
                for (let item of this.$store.state.coupon_new.packets) {
                    if (x.packets.indexOf(parseInt(item.M_PacketID)) > -1)
                        packets.push(item)
                }
            }
            if (x.M_CouponTypeCode == "COUPON.COURIER") {
                for (let item of this.$store.state.coupon_new.expeditions) {
                    if (x.exps.indexOf(parseInt(item.M_ExpeditionID)) > -1)
                        exps.push(item)
                }
            }
            
            this.$store.commit('coupon_new/set_selected_item', items)
            this.$store.commit('coupon_new/set_selected_packet', packets)
            this.$store.commit('coupon_new/set_selected_expedition', exps)
            this.$store.commit('coupon_new/set_common', ['dialog_new', true])
        },

        del (x) {
            this.select(x)
            this.$store.commit('set_dialog_delete', true)
        },

        confirm_del (x) {
            this.$store.dispatch('coupon/del', {id:x.data})
        },

        select (x) {
            this.$store.commit('coupon/set_selected_coupon', x)
        },

        search () {
            return this.$store.dispatch('coupon/search', {})
        },

        change_page(x) {
            this.curr_page = x
            this.$store.dispatch('coupon/search', {})
        }
    },

    mounted () {
        this.$store.dispatch('coupon_new/search_type')
    }
}
</script>