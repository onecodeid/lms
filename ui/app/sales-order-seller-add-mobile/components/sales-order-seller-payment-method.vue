<template>
    
    <v-card>
        <v-card-title primary-title class="pb-0 pt-2">
            <v-layout row wrap>
                <v-flex xs12>
                    <h3 class="display-1 font-weight-light zalfa-text-title">
                        METODE PEMBAYARAN</h3>
                </v-flex>
                <!-- <v-flex xs3 class="text-xs-right"> -->
                    <!-- <v-btn color="default" class="btn-icon ma-0" @click="back">
                        <v-icon>undo</v-icon> &nbsp; KEMBALI
                    </v-btn>

                    <v-btn color="primary" class="btn-icon ma-0 mr-4" @click="save">
                        <v-icon>save</v-icon> &nbsp; SIMPAN
                    </v-btn> -->
                <!-- </v-flex> -->
            </v-layout>
            
        </v-card-title>
        <v-card-text class="pb-0 pt-2">
            
            <v-divider></v-divider>

            <v-layout row wrap>
                <v-flex xs12>
                    <v-radio-group v-model="selected_payment_id" class="mt-1 pt-0">
                        <v-card v-for="(p, i) in payments" v-bind:key="i" class="mb-2">
                            <v-card-title class="cyan white--text">
                                <v-radio :value="p.M_PaymentTypeID" dark color="orange">
                                    <template slot="label">
                                        <h3 class="subheading">{{ p.M_PaymentTypeName }}</h3>
                                    </template>
                                </v-radio>
                            </v-card-title>
                            <v-card-text class="pa-0">
                                <v-layout row wrap>
                                    <v-flex xs12 pa-2>
                                        <v-layout row wrap v-show="p.M_PaymentTypeCode == 'TRANSFER'">
                                            <template v-for="(item, index) in accounts">
                                                <v-flex xs3 v-bind:key="index" pa-1>
                                                    <v-img :src="'../'+item.bank_logo" width="100%"></v-img>
                                                </v-flex>
                                            </template>
                                        </v-layout>

                                        <v-layout row wrap v-show="p.M_PaymentTypeCode == 'IPAYMU.CS'">
                                            <template v-for="(item, index) in channels">
                                                <v-flex xs6 v-bind:key="index" pa-3 v-bind:class="is_selected_channel_color(item)" @click="select_channel(item)">
                                                    <v-img :src="'../assets/img/logo-'+item.value+'.png'" width="100%"></v-img>
                                                </v-flex>
                                            </template>
                                        </v-layout>
                                    </v-flex>
                                </v-layout>
                                
                                
                            </v-card-text>
                        </v-card>
                    </v-radio-group>
                </v-flex>
                <v-flex xs12>
                    <v-card class="mt-1">
                        <v-card-title class="cyan white--text">
                            <h3 class="title">Ringkasan Belanja</h3>
                        </v-card-title>
                        <v-card-text>
                            <v-layout row wrap pt-3>
                                <v-flex xs6 class="">Total Harga Barang</v-flex>
                                <v-flex xs6 class="text-xs-right">Rp {{ one_money(total_price) }}</v-flex>
                            </v-layout>

                            <v-layout row wrap pt-2>
                                <v-flex xs6 class="">Ongkos Kirim</v-flex>
                                <v-flex xs6 class="text-xs-right">Rp {{ one_money(courier_cost) }}</v-flex>
                            </v-layout>

                            <v-layout row wrap pt-2 v-show="coupon_set">
                                <v-flex xs6 class="">Coupon : <b>{{coupon_code}}</b> <a href="javascript:;" @click="coupon_clear"><v-icon small>clear</v-icon></a></v-flex>
                                <v-flex xs6 class="text-xs-right red--text">- Rp {{ one_money(coupon_amounts) }}</v-flex>
                            </v-layout>

                            <v-layout row wrap>
                                <v-divider class="mt-2 mb-2"></v-divider>
                            </v-layout>

                            <v-layout row wrap pt-2>
                                <v-flex xs6 class=""><b>Total Belanja</b></v-flex>
                                <v-flex xs6 class="text-xs-right">Rp <b>{{ one_money(grand_total_price) }}</b></v-flex>
                            </v-layout>

                            <v-layout row wrap pt-2>
                                <v-flex xs6 class="">Biaya COD 
                                    <span v-if="cod_cost>0">({{selected_expedition.M_ExpeditionCODRate}}%)</span>
                                </v-flex>
                                <v-flex xs6 class="text-xs-right">Rp {{ one_money(cod_cost) }}</v-flex>
                            </v-layout>

                            <v-layout row wrap>
                                <v-divider class="mt-2 mb-2"></v-divider>
                            </v-layout>

                            <v-layout row wrap pt-2>
                                <v-flex xs8 class=""><b>Total Yang Harus Dibayar</b></v-flex>
                                <v-flex xs4 class="text-xs-right">Rp <b>{{ one_money(final_total_price) }}</b></v-flex>
                            </v-layout>

                            <v-layout row wrap>
                                <v-flex xs12>
                                    <v-divider class="mt-2 mb-2"></v-divider>    
                                </v-flex>
                                
                                <v-flex xs12>
                                    <v-text-field
                                        label="Masukkan Coupon jika ada"
                                        outline
                                        v-model="coupon_code"
                                        @change="check_coupon"
                                        :readonly="coupon_set"
                                        :clearable="coupon_set"
                                        @click:clear="coupon_clear"
                                        v-show="!coupon_set"
                                        :error="coupon_error"
                                        :error-messages="[coupon_error_msg]"
                                        :error-count="coupon_error==true?1:0"
                                    ></v-text-field>
                                </v-flex>

                                <v-flex xs12>
                                    <v-divider class="mt-2"></v-divider>    
                                </v-flex>
                            </v-layout>

                            <v-layout row wrap pt-3>
                                <v-flex xs12>
                                    <v-btn color="primary" class="btn-icon ma-0 mr-4" @click="save" block :disabled="!btn_pay_enable">
                                        SIMPAN & BAYAR
                                    </v-btn>    
                                </v-flex>
                                
                            </v-layout>
                        </v-card-text>
                    </v-card>
                    
                </v-flex>
            </v-layout>
            
            
        </v-card-text>

        <common-dialog-confirm
            text="APAKAH DATA YANG ANDA MASUKKAN SUDAH BENAR? Setelah konfirmasi, data akan dikirimkan ke Admin !"
            btn_cancel="Sebentar, Saya mau ubah data"
            btn_confirm="Simpan"
            :data="1"
            @confirm="do_save"
        ></common-dialog-confirm>
        <common-dialog-progress></common-dialog-progress>
    </v-card>
</template>

<style scoped>
.zalfa-input-super-dense .v-input__control {
    min-height: 36px !important;
}

.v-avatar {
    height: 48px !important;
    width: 96px !important;
}

.v-avatar .v-image {
    border-radius: 0px;
}

.v-list--two-line .v-list__tile {
    height: auto;
}

.v-input--selection-controls.v-input .v-label {
    display: inline;
}

.v-input--selection-controls .v-input__control {
    width: 100%;
}
</style>

<script>
module.exports = {
    components : {
        "common-dialog-confirm" : httpVueLoader("../../common/components/common-dialog-confirm.vue"),
        "common-dialog-progress" : httpVueLoader("../../common/components/common-dialog-progress.vue")
    },

    data () {
        return {
            
        }
    },

    computed : {
        details () {
            return this.$store.state.salesorder.items
        },
        
        total_price () {
            let details = this.details
            let ttl = 0
            for (let i in details)
                ttl += Math.round(details[i].item_subtotal)

            return ttl
        },

        cod_cost () {
            if (!this.selected_expedition || !this.selected_payment_id)
                return 0

            let pc = ''
            for (let p of this.payments)
                if (p.M_PaymentTypeID == this.selected_payment_id)
                    pc = p.M_PaymentTypeCode
            
            if (pc != 'COD')
                return 0

            if(this.selected_expedition.M_ExpeditionIsCOD=="Y") {
                if (this.selected_expeditions.M_ExpeditionCODRateItemsOnly=="N")
                    return Math.round(this.selected_expedition.M_ExpeditionCODRate * this.grand_total_price / 100)
                else
                    return Math.round(this.selected_expedition.M_ExpeditionCODRate * this.total_price / 100)
            }
            
            return 0
        },

        grand_total_price () {
            return parseFloat(this.total_price) + parseFloat(this.courier_cost) - parseFloat(this.coupon_amounts)
        },

        final_total_price () {
            return parseFloat(this.grand_total_price) + parseFloat(this.cod_cost)
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

        payments () {
            return this.$store.state.salesorder.payments
        },

        selected_payment : {
            get () { return this.$store.state.salesorder.selected_payment },
            set (v) { this.$store.commit('salesorder/set_selected_payment', v) }
        },

        total_weight () {
            return this.$store.state.salesorder.total_weight
        },

        courier_cost : {
            get () { return this.$store.state.salesorder.courier_cost },
            set (v) { this.$store.commit('salesorder/set_common', ['courier_cost', v]) }
        },

        ds_customers () {
            return this.$store.state.salesorder.ds_customers
        },

        selected_ds_customer : {
            get () { return this.$store.state.salesorder.selected_ds_customer },
            set (v) { this.$store.commit('salesorder/set_selected_ds_customer', v) }
        },

        search_ds_customer : {
            get () { return this.$store.state.salesorder.search_ds_customer },
            set (v) { this.$store.commit('salesorder/set_common', ['search_ds_customer', v]) }
        },

        is_dropship : {
            get () { return this.$store.state.salesorder.is_dropship },
            set (v) { this.$store.commit('salesorder/set_common', ['is_dropship', v]) }
        },

        accounts () {
            return this.$store.state.salesorder.accounts
        },

        selected_payment_id : {
            get () { return this.$store.state.salesorder.selected_payment_id },
            set (v) { this.$store.commit('salesorder/set_common', ['selected_payment_id', v]) }
        },

        channels () {
            return this.$store.state.salesorder.channels
        },

        selected_channel : {
            get () { return this.$store.state.salesorder.selected_channel },
            set (v) { this.$store.commit('salesorder/set_selected_channel', v) }
        },

        btn_pay_enable () {
            if (!this.selected_payment_id) return false

            let code = ''
            for (let p of this.payments)
                if (p.M_PaymentTypeID == this.selected_payment_id)
                    code = p.M_PaymentTypeCode

            if (code == 'IPAYMU.CS')
                if (!this.selected_channel) return false
            return true
        },

        order_note : {
            get () { return this.$store.state.salesorder.order_note },
            set (v) { this.$store.commit('salesorder/set_common', ['order_note', v]) }
        },

        coupon_code : {
            get () { return this.$store.state.salesorder.coupon_code },
            set (v) { this.$store.commit('salesorder/set_common', ['coupon_code', v]) }
        },

        coupon_set () {
            return this.$store.state.salesorder.coupon_set
        },

        coupon_amount () {
            return this.$store.state.salesorder.coupon_amount
        },

        coupon_type () {
            return this.$store.state.salesorder.coupon_type
        },

        coupon_amounts () {
            return this.$store.state.salesorder.coupon_amounts
        },

        coupon_note () {
            return this.$store.state.salesorder.coupon_note
        },

        coupon_error () {
            return this.$store.state.salesorder.coupon_error
        },

        coupon_error_msg () {
            return this.$store.state.salesorder.coupon_error_msg
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        },

        select (x) { return },

        add () {
            this.$store.commit('newitem/set_common', ['dialog_items', true] )
        },

        save () {
            this.$store.commit('set_dialog_confirm', true)
        },

        do_save () {
            this.$store.dispatch('salesorder/save')
        },

        back () {
            this.$store.commit('salesorder/set_common', ['selected_tab', 1])
        },

        select_channel (x) {
            this.$store.commit('salesorder/set_selected_channel', x)
        },

        is_selected_channel_color (x) {
            if (!this.selected_channel) return ''
            if (x.value == this.selected_channel.value)
                return 'blue lighten-4'
            return ''
        },

        check_coupon () {
            this.$store.dispatch('salesorder/check_coupon')
        },

        coupon_clear () {
            this.$store.commit('salesorder/set_common', ['coupon_set', false])
            this.$store.commit('salesorder/set_common', ['coupon_amount', 0])
            this.$store.commit('salesorder/set_common', ['coupon_amounts', 0])
            this.$store.commit('salesorder/set_common', ['coupon_code', ''])
            this.$store.commit('salesorder/set_common', ['coupon_type', ''])
            this.$store.commit('salesorder/set_common', ['coupon_id', 0])
            this.$store.commit('salesorder/set_common', ['coupon_item_id', 0])
            this.$store.commit('salesorder/set_common', ['coupon_courier_id', 0])

            // Clear items
            let dt = []
            for (let d of this.details) {
                // d.item_subtotal = d.item_qty * ((d.item_price * (100-d.item_disc) / 100) - d.item_discrp);
                d.coupon_amount = 0
                d.coupon_id = 0
                dt.push(d)
            }
            this.$store.commit('salesorder/update_items', dt)
        }
    },

    mounted () { },

    watch : { }
}
</script>