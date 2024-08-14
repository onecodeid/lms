<template>
    <v-row no-gutters>
        <v-col cols="6" class="offset-3">
            <v-alert :value="true" type="info" class="mt-2 mb-4">
                Screenshot halaman ini untuk melakukan konfirmasi pembayaran !
            </v-alert>

            <v-card>
                <v-card-title primary-title class="pb-0 pt-3">
                    <v-row no-gutters>
                        <v-col cols="6">
                            <h3 class="headline mb-0">Detail Order</h3>
                        </v-col>
                        <v-col cols="6" class="text-xs-right">
                            <h3>Invoice No #{{ selected_order.L_SoNumber }}</h3>
                            <p class="mb-0">{{ selected_order.L_SoDate }}</p>
                        </v-col>
                    </v-row>
                </v-card-title>
                <v-card-text>
                    <v-row no-gutters>
                        <v-col cols="12">

                            <v-row no-gutters class="mb-4">
                                <v-col cols="8">
                                    <h3 class="">{{ selected_order.M_CustomerName }}</h3>
                                    <p class="mb-0">{{ selected_order.M_CustomerAddress }}</p>
                                    <p class="mb-0">{{ selected_order.city_name }}</p>
                                </v-col>
                                <!-- <v-col cols="4" class="text-xs-right yellow pa-2"
                                    v-show="selected_order.F_PaymentReceiptImg != '' && selected_order.F_PaymentReceiptImg != null">
                                    <div>Bukti transfer :</div>
                                    <div><a href="javascript:;" @click="show_receipt">{{
                                selected_order.F_PaymentReceiptImg }}</a></div>
                                </v-col> -->
                            </v-row>

                            <!-- <v-row>
                                <v-col xs6>
                                    <p class="caption mb-0">Kode iklan : <b>{{ selected_order.so_ads_number }}</b></p>
                                </v-col>
                                <v-col xs6>
                                    <p class="caption mb-0 text-right">Nomor pesanan mp : <b>{{
                                selected_order.so_mp_number }}</b></p>
                                </v-col>
                            </v-row> -->
                            <v-card flat>
                                <v-card-title primary-title class="orange white--text pt-2 pb-2">
                                    <v-row no-gutters>
                                        <v-col cols="12">
                                            <h4>Metode Pembayaran</h4>
                                        </v-col>
                                        <!-- <v-col cols="6" class="text-xs-right">
                                            <h4>Jumlah</h4>
                                        </v-col> -->
                                    </v-row>

                                    <v-row no-gutters>
                                        <v-col cols="12">

                                        </v-col>
                                    </v-row>
                                </v-card-title>

                                <v-card-text>
                                    <v-list-item two-line v-for="(a, n) in accounts" :key="n">
                                        <v-list-item-icon>
                                            <v-img :src="'../' + a.bank_logo" height="20" contain width="30"></v-img>
                                        </v-list-item-icon>
                                        <v-list-item-content>
                                            <v-list-item-title>{{ a.bank_name }}</v-list-item-title>
                                            <v-list-item-subtitle>{{ a.account_name }}</v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                </v-card-text>

                                <!-- <template v-if="selected_order.M_PaymentTypeCode == 'TRANSFER'"> -->
                                <!-- <v-card-text v-show="selected_order.F_PaymentID == null">
                                        <finance-payment-admin-payment></finance-payment-admin-payment>
                                    </v-card-text> -->

                                <v-card-text v-show="selected_order.F_PaymentID != null">
                                    <v-row no-gutters>
                                        <v-col cols="9">
                                            <div><b>{{ selected_order.M_PaymentTypeName }}</b></div>
                                            <div>{{ selected_order.account_bank_name }} No. {{
                selected_order.M_BankAccountNumber }}</div>
                                            <div>a/n {{ selected_order.M_BankAccountName }}</div>

                                            <div class="mt-3"><b>BANK PENGIRIM</b></div>
                                            <div>{{ selected_order.M_BankName }} a/n {{
                selected_order.F_PaymentSenderName }}</div>

                                        </v-col>
                                        <v-col cols="3" class="text-xs-right">
                                            <div>Rp <b>{{ one_money(selected_order.F_PaymentAmount) }}</b></div>
                                            <div>&nbsp;</div>
                                            <div class="mt-3">{{ selected_order.F_PaymentDate }}</div>
                                        </v-col>
                                    </v-row>
                                </v-card-text>
                                <!-- </template> -->

                            </v-card>

                            <v-card flat>
                                <v-card-title primary-title class="black white--text pt-2 pb-2">
                                    <v-row no-gutters>
                                        <v-col cols="6">
                                            <h4>Item</h4>
                                        </v-col>
                                        <v-col cols="6" class="text-right">
                                            <h4>Total Tagihan</h4>
                                        </v-col>
                                    </v-row>

                                </v-card-title>

                                <v-card-text>
                                    <v-row no-gutters>
                                        <v-col cols="9">
                                            <div>
                                                <template v-for="(item, i) of selected_order.items">
                                                    <div :class="(i % 2 == 0 ? 'blue--text' : '') + ' mr-2'"
                                                        v-bind:key="i">{{
                item.item_name }} ({{ item.item_qty }})</div>
                                                </template>
                                            </div>

                                        </v-col>
                                        <v-col cols="3" class="text-right mt-3">
                                            <div>Rp <b>{{ one_money(selected_order.L_SoTotal) }}</b></div>
                                        </v-col>
                                    </v-row>
                                </v-card-text>
                            </v-card>

                            <div v-show="selected_order.order_note!=''">
                                <v-divider class="mt-2 mb-2"></v-divider>
                                <div class="body-2 pl-3"><u>Catatan Admin</u></div>
                                <div class="caption pl-3 pr-3">{{selected_order.order_note}}</div>
                                <v-divider class="mt-2 mb-2"></v-divider>
                            </div>


                        </v-col>

                    </v-row>


                </v-card-text>
                <v-card-actions>
                    <!-- <v-btn flat color="primary" @click="dialog=!dialog">Tutup</v-btn>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" @click="confirm" :disabled="!btn_confirm_enable">Konfirmasi</v-btn> -->

                </v-card-actions>
            </v-card>
        </v-col>



        <!-- <common-dialog-image v-if="dialog_image" :image_url="receipt_url"></common-dialog-image> -->
    </v-row>
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
    components: {
        // "finance-payment-admin-payment":httpVueLoader("./finance-payment-admin-payment.vue"),
        // "finance-payment-admin-cs":httpVueLoader("./finance-payment-admin-cs.vue"),
        // "common-dialog-image":httpVueLoader("../../common/components/common-dialog-image-2.vue?t=123")
    },

    computed: {
        ...Vuex.mapState('payment2', ['accounts', 'banks']),
        ...Vuex.mapState('register', ['selected_order'])

        // accounts () { return this.$store.state.payment2.accounts }
    },

    methods: {
        __c(a, b) { this.$store.commit('register/set_object', [a, b]) },

        one_money(x) {
            return window.one_money(x)
        }
    },

    mounted() {
        with (this.$store) {
            dispatch('payment2/search_bank')

        }

        this.$store.dispatch('payment2/search_account', {})
        // debug
        // this.__c("invoiceNumber", "IV-24070011")
        // this.$store.dispatch("register/searchInvoice").then(y => {
        //     this.__c("selected_order", y.records[0])
        //     this.step++
        // })

    }
}