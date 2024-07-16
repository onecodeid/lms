<template>
    <v-dialog
        v-model="dialog"
        scrollable
        :overlay="false"
        max-width="500px"
        transition="dialog-transition"
    >
        <v-card>
            <v-card-title primary-title class="cyan white--text pt-3">
                <h3>COUPON BARU</h3>
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs12>
                        <v-layout row wrap>

                            <v-flex xs12>
                                <v-text-field
                                    label="Kode Coupon"
                                    v-model="coupon_code"
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12>
                                <v-select
                                    :items="types"
                                    v-model="selected_type"
                                    item-value="M_CouponTypeID"
                                    item-text="M_CouponTypeName"
                                    label="Tipe Kupon"
                                    return-object
                                ></v-select>
                            </v-flex>

                            <v-flex xs12>
                                <v-select
                                    :items="categories"
                                    v-model="selected_category"
                                    item-value="M_CategoryID"
                                    item-text="M_CategoryName"
                                    label="Kategori Produk"
                                    return-object
                                    v-show="selected_type && selected_type.M_CouponTypeCode=='COUPON.CATEGORY'"
                                ></v-select>
                            </v-flex>

                            <v-flex xs12>
                                <v-combobox
                                    v-model="selected_item"
                                    :items="items"
                                    :search-input.sync="search"
                                    hide-selected
                                    
                                    label="Pilih Item"
                                    multiple
                                    persistent-hint
                                    small-chips
                                    item-text="M_ItemName"
                                    item-value="M_ItemID"
                                    return-object
                                    v-show="selected_type && selected_type.M_CouponTypeCode=='COUPON.PRODUCT'"
                                >
                                </v-combobox>
                            </v-flex>

                            <v-flex xs12>
                                <v-combobox
                                    v-model="selected_packet"
                                    :items="packets"
                                    :search-input.sync="search"
                                    hide-selected
                                    
                                    label="Pilih Paket"
                                    multiple
                                    persistent-hint
                                    small-chips
                                    item-text="M_PacketName"
                                    item-value="M_PacketID"
                                    return-object
                                    v-show="selected_type && selected_type.M_CouponTypeCode=='COUPON.PACKET'"
                                >
                                </v-combobox>
                            </v-flex>

                            <v-flex xs12>
                                <v-combobox
                                    v-model="selected_expedition"
                                    :items="expeditions"
                                    :search-input.sync="search"
                                    hide-selected
                                    
                                    label="Pilih Ekspedisi"
                                    multiple
                                    persistent-hint
                                    small-chips
                                    item-text="M_ExpeditionName"
                                    item-value="M_ExpeditionID"
                                    return-object
                                    v-show="selected_type && selected_type.M_CouponTypeCode=='COUPON.COURIER'"
                                >
                                </v-combobox>
                            </v-flex>

                            <v-flex xs6>
                                <common-datepicker
                                    label="Dari Tanggal"
                                    :date="coupon_sdate"
                                    data="coupon_sdate"
                                    @change="change_date"
                                    classs="mr-2 mb-3"
                                    hints=""
                                    :details="false"
                                    v-if="dialog"
                                    :solo="false"
                                ></common-datepicker>
                            </v-flex>

                            <v-flex xs6>
                                <common-datepicker
                                    label="Sampai Tanggal"
                                    :date="coupon_edate"
                                    data="coupon_edate"
                                    @change="change_date"
                                    classs="ml-2 mb-3"
                                    hints=""
                                    :details="false"
                                    v-if="dialog"
                                    :solo="false"
                                ></common-datepicker>
                            </v-flex>

                            <v-flex xs6 pr-2>
                                <v-text-field
                                    label="Minimum Belanja"
                                    v-model="coupon_min_spend"
                                    prefix="Rp"
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs6 pl-2>
                                <v-text-field
                                    label="Maksimum Belanja"
                                    v-model="coupon_max_spend"
                                    prefix="Rp"
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs6 pr-4>
                                <v-text-field
                                    label="Besar Kupon"
                                    v-model="coupon_amount"
                                    prefix="Rp"
                                    v-show="coupon_amount_t=='R'"
                                >
                                    <template slot="prepend-inner">
                                        <v-btn color="orange" dark small class="btn-icon" depressed @click="set_amount_t('P')">Rp</v-btn>
                                    </template>
                                </v-text-field>
                                <v-text-field
                                    label="Besar Kupon (%)"
                                    v-model="coupon_amount_p"
                                    prefix="%"
                                    v-show="coupon_amount_t=='P'"
                                    reverse
                                >
                                    <template slot="append">
                                        <v-btn color="orange" dark small class="btn-icon" depressed @click="set_amount_t('R')">%</v-btn>
                                    </template>
                                </v-text-field>
                            </v-flex>

                            <v-flex xs3 pr-2>
                                <v-text-field
                                    label="Jumlah Kupon"
                                    v-model="coupon_qty"
                                ></v-text-field>
                            </v-flex>
                            <v-flex xs3>
                                <v-text-field
                                    label="Kupon Terpakai"
                                    v-model="coupon_used"
                                    readonly
                                >
                                <template slot="append">
                                    <v-btn color="red" icon small outline @click="coupon_reset"><v-icon>refresh</v-icon></v-btn>
                                </template>
                                </v-text-field>
                            </v-flex>

                            

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
    components : {
        'common-datepicker' : httpVueLoader('../../common/components/common-datepicker.vue')
    },

    data () {
        return { x:[], search:null }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.coupon_new.dialog_new },
            set (v) { this.$store.commit('coupon_new/set_common', ['dialog_new', v]) }
        },

        coupon_edate : {
            get () { return this.$store.state.coupon_new.coupon_edate },
            set (v) { this.$store.commit('coupon_new/set_common', ['coupon_edate', v]) }
        },

        coupon_sdate : {
            get () { return this.$store.state.coupon_new.coupon_sdate },
            set (v) { this.$store.commit('coupon_new/set_common', ['coupon_sdate', v]) }
        },

        coupon_code : {
            get () { return this.$store.state.coupon_new.coupon_code },
            set (v) { this.$store.commit('coupon_new/set_common', ['coupon_code', v]) }
        },

        types () {
            return this.$store.state.coupon_new.types
        },

        selected_type : {
            get () { return this.$store.state.coupon_new.selected_type },
            set (v) { this.$store.commit('coupon_new/set_selected_type', v) }
        },

        coupon_amount : {
            get () { return this.$store.state.coupon_new.coupon_amount },
            set (v) { this.$store.commit('coupon_new/set_common', ['coupon_amount', v]) }
        },

        coupon_amount_p : {
            get () { return this.$store.state.coupon_new.coupon_amount_p },
            set (v) { this.$store.commit('coupon_new/set_common', ['coupon_amount_p', v]) }
        },

        coupon_amount_t : {
            get () { return this.$store.state.coupon_new.coupon_amount_t },
            set (v) { this.$store.commit('coupon_new/set_common', ['coupon_amount_t', v]) }
        },

        coupon_min_spend : {
            get () { return this.$store.state.coupon_new.coupon_min_spend },
            set (v) { this.$store.commit('coupon_new/set_common', ['coupon_min_spend', v]) }
        },

        coupon_max_spend : {
            get () { return this.$store.state.coupon_new.coupon_max_spend },
            set (v) { this.$store.commit('coupon_new/set_common', ['coupon_max_spend', v]) }
        },

        coupon_qty : {
            get () { return this.$store.state.coupon_new.coupon_qty },
            set (v) { this.$store.commit('coupon_new/set_common', ['coupon_qty', v]) }
        },

        coupon_used : {
            get () { return this.$store.state.coupon_new.coupon_used },
            set (v) { this.$store.commit('coupon_new/set_common', ['coupon_used', v]) }
        },

        items () {
            return this.$store.state.coupon_new.items
        },

        packets () {
            return this.$store.state.coupon_new.packets
        },

        expeditions () {
            return this.$store.state.coupon_new.expeditions
        },

        categories () {
            return this.$store.state.coupon_new.categories
        },

        selected_item : {
            get () { return this.$store.state.coupon_new.selected_item },
            set (v) { this.$store.commit('coupon_new/set_selected_item', v) }
        },

        selected_packet : {
            get () { return this.$store.state.coupon_new.selected_packet },
            set (v) { this.$store.commit('coupon_new/set_selected_packet', v) }
        },

        selected_expedition : {
            get () { return this.$store.state.coupon_new.selected_expedition },
            set (v) { this.$store.commit('coupon_new/set_selected_expedition', v) }
        },

        selected_category : {
            get () { return this.$store.state.coupon_new.selected_category },
            set (v) { this.$store.commit('coupon_new/set_selected_category', v) }
        }
    },

    methods : {
        save () {
            this.$store.dispatch('coupon_new/save')
        },

        change_date(x) {
            this[x.data] = x.new_date
        },

        coupon_reset() {
            this.$store.commit('coupon_new/set_common', ['coupon_reset', true])
            this.coupon_used = 0
        },

        set_amount_t(v) {
            if (v=='R')
                this.coupon_amount_p = 0
            else
                this.coupon_amount = 0
            this.coupon_amount_t = v
        }
    },

    mounted () {
        this.$store.dispatch('coupon_new/search_item')
        this.$store.dispatch('coupon_new/search_packet')
        this.$store.dispatch('coupon_new/search_expedition')
        this.$store.dispatch('coupon_new/search_category')
    },

    watch : {}
}
</script>