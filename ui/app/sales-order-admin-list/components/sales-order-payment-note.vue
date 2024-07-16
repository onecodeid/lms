<template>
    <v-dialog
        v-model="dialog"
        scrollable
        overlay persistent
        max-width="700px"
        transition="dialog-transition"
        :fullscreen="$vuetify.breakpoint.smAndDown"
    >
        <v-card :class="{'fill-height':$vuetify.breakpoint.smAndDown}">
            <v-card-title class="pb-2 pt-2 orange white--text hidden-sm-and-down">
                <h3 class="headline mb-0">Pembayaran</h3>
            </v-card-title>

            <v-card-title primary-title class="orange white--text pt-2 pb-2 px-2 hidden-md-and-up shrink">
                <v-layout row wrap>
                    <v-flex xs12 sm12>
                        <v-btn flat color="primary" class="ma-0 btn-icon mr-2 hidden-md-and-up" @click="dialog=!dialog" style="float:left">
                            <v-icon class="white--text" medium>arrow_back</v-icon>
                        </v-btn>
                        <h3 class="headline text-xs-center pr-5">PEMBAYARAN</h3>        
                    </v-flex>
                </v-layout>
            </v-card-title>

            <v-card-text :class="{'grow':$vuetify.breakpoint.smAndDown}">
                <v-layout row wrap>
                    <v-flex xs12 sm6 md6 offset-xs0 offset-sm3 offset-md0 :class="{'pr-4':$vuetify.breakpoint.mdAndUp}">
                        <v-select
                            :items="payments"
                            v-model="selected_payment"
                            return-object
                            placeholder="Silahkan pilih satu metode pembayaran"
                            label="Metode pembayaran"
                        >
                            <template slot="item" slot-scope="data">
                                {{data.item.M_PaymentTypeName}} {{data.item.channel_id==null?'':' - '+data.item.channel_name}}
                            </template>
                            <template slot="selection" slot-scope="data">
                                {{data.item.M_PaymentTypeName}} {{data.item.channel_id==null?'':' - '+data.item.channel_name}}
                            </template>
                        </v-select>
                        
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

                        <v-textarea
                            outline
                            label="Tuliskan catatan anda disini"
                            v-model="order_note"
                            hide-details
                        >
                        </v-textarea>
                    </v-flex>

                    <v-flex xs12 sm6 md6 offset-xs0 offset-sm3 offset-md0 :class="{'pr-4':$vuetify.breakpoint.mdAndUp}">
                        <v-layout row wrap pt-3>
                                <v-flex xs6 class="">Total Harga Barang</v-flex>
                                <v-flex xs6 class="text-xs-right">Rp {{ one_money(total_price) }}</v-flex>
                            </v-layout>

                            <v-layout row wrap pt-2>
                                <v-flex xs6 class="">Ongkos Kirim</v-flex>
                                <v-flex xs6 class="text-xs-right">Rp {{ one_money(courier_cost) }}</v-flex>
                            </v-layout>

                            

                            <v-layout row wrap>
                                <v-divider class="mt-2 mb-2"></v-divider>
                            </v-layout>

                            <v-layout row wrap>
                                <!-- <v-flex xs12>
                                    <v-text-field
                                        label="Masukkan Coupon jika ada"
                                        outline
                                        v-model="coupon_code"
                                        @change="check_coupon"
                                        :readonly="coupon_set"
                                        :clearable="coupon_set"
                                        @click:clear="coupon_clear"
                                        v-show="!coupon_set"
                                    ></v-text-field>
                                </v-flex> -->
                                <v-flex xs6 v-show="coupon_set">
                                    Coupon : <b>{{coupon_code}}</b> <a href="javascript:;" @click="coupon_clear"><v-icon small>clear</v-icon></a>
                                </v-flex>
                                <v-flex xs6 class="text-xs-right red--text" v-show="coupon_set">
                                    - Rp {{ one_money(coupon_amounts) }}
                                </v-flex>
                                <v-flex xs6 class="caption red--text" v-show="false">
                                    {{coupon_note}}
                                </v-flex>
                            </v-layout>

                            <v-layout row wrap pt-2>
                                <v-flex xs6 class=""><b>Total Belanja</b></v-flex>
                                <v-flex xs6 class="text-xs-right">Rp <b>{{ one_money(grand_total_price) }}</b></v-flex>
                            </v-layout>

                            <v-layout row wrap pt-2>
                                <v-flex xs6 class="">Biaya COD</v-flex>
                                <v-flex xs6 class="text-xs-right">Rp {{ one_money(cod_cost) }}</v-flex>
                            </v-layout>

                            <v-layout row wrap>
                                <v-divider class="mt-2 mb-2"></v-divider>
                            </v-layout>

                            <v-layout row wrap pt-2>
                                <v-flex xs8 class=""><b>Total Yang Harus Dibayar</b></v-flex>
                                <v-flex xs4 class="text-xs-right">Rp <b>{{ one_money(final_total_price) }}</b></v-flex>
                            </v-layout>
                    </v-flex>
                </v-layout>
                
            </v-card-text>

            <v-card-actions class="hidden-sm-and-down">
                <v-spacer></v-spacer>
                <v-btn color="red" outline dark @click="close">Tutup</v-btn>
                <v-btn color="primary" @click="save">Simpan</v-btn>
            </v-card-actions>

            <v-card-actions class="hidden-md-and-up pa-0 shrink">
                <v-layout row wrap>
                    <v-flex xs12 sm6 offset-xs0 offset-sm3>
                        <v-btn color="primary" @click="save" block large>
                            <h3 class="headline">Simpan</h3></v-btn>
                    </v-flex>
                </v-layout>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
module.exports = {
    computed : {
        dialog : {
            get () { return this.$store.state.quickorder.dialog_note },
            set (v) { this.$store.commit('quickorder/set_common', ['dialog_note', v]) }
        },

        order_note : {
            get () { return this.$store.state.quickorder.order_note },
            set (v) { this.$store.commit('quickorder/set_common', ['order_note', v])}
        },

        payments () {
            return this.$store.state.quickorder.payments
        },

        selected_payment : {
            get () { return this.$store.state.quickorder.selected_payment },
            set (v) { this.$store.commit('quickorder/set_selected_payment', v) }
        },

        total_price () {
            return this.$store.state.quickorder.total
        },

        courier_cost () {
            return this.$store.state.quickorder.courier_cost
        },

        cod_cost () {
            if (!this.selected_payment)
                return 0
            if (this.selected_payment.M_PaymentTypeCode != 'COD')
                return 0
            if (!this.$store.state.quickorder.selected_expedition)
                return 0
            else {
                let exp = this.$store.state.quickorder.selected_expedition
                if (exp.M_ExpeditionCODRateItemsOnly=='N')
                    return Math.round(exp.M_ExpeditionCODRate * this.grand_total_price / 100)
                else
                    return Math.round(exp.M_ExpeditionCODRate * (parseFloat(this.total_price) - parseFloat(this.coupon_amounts)) / 100)
            }
        },

        grand_total_price () {
            return parseFloat(this.total_price) + parseFloat(this.courier_cost) - this.coupon_amounts
        },

        final_total_price () {
            return parseFloat(this.grand_total_price) + parseFloat(this.cod_cost)
        },

        coupon_code : {
            get () { return this.$store.state.quickorder.coupon_code },
            set (v) { this.$store.commit('quickorder/set_common', ['coupon_code', v]) }
        },

        coupon_set () {
            return this.$store.state.quickorder.coupon_set
        },

        coupon_amount () {
            return this.$store.state.quickorder.coupon_amount
        },

        coupon_type () {
            return this.$store.state.quickorder.coupon_type
        },

        coupon_amounts () {
            return this.$store.state.quickorder.coupon_amounts
        },

        coupon_note () {
            return this.$store.state.quickorder.coupon_note
        },

        coupon_error () {
            return this.$store.state.quickorder.coupon_error
        },

        coupon_error_msg () {
            return this.$store.state.quickorder.coupon_error_msg
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        },

        check_coupon () {
            this.$store.dispatch('quickorder/check_coupon')
        },

        coupon_clear () {
            this.$store.commit('quickorder/set_common', ['coupon_set', false])
            this.$store.commit('quickorder/set_common', ['coupon_amount', 0])
            this.$store.commit('quickorder/set_common', ['coupon_amounts', 0])
            this.$store.commit('quickorder/set_common', ['coupon_code', ''])
            this.$store.commit('quickorder/set_common', ['coupon_type', ''])
            this.$store.commit('quickorder/set_common', ['coupon_id', 0])
            this.$store.commit('quickorder/set_common', ['coupon_item_id', 0])
            this.$store.commit('quickorder/set_common', ['coupon_courier_id', 0])

            // Clear items
            let dt = []
            for (let d of this.$store.state.quickorder.details) {
                d.coupon_amount = 0
                d.coupon_id = 0
                dt.push(d)
            }
            this.$store.commit('quickorder/set_details', dt)
        },

        close () {
            if (this.coupon_set) {
                this.$store.commit('quickorder/set_common', ['dialog_confirm_remove_coupon', true])
            } else {
                this.dialog = false
            }
        },

        save () {
            this.$store.dispatch('quickorder/save')
        }
    },

    watch : {
        dialog (v, o) {
            if (v && !o) {
                this.$store.commit('quickorder/set_selected_payment', v)
                this.$store.dispatch('quickorder/search_payment_expanded')
            }
        }
    }
}
</script>