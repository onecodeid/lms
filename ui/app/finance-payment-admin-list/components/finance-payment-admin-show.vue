<template>
<v-dialog
    v-model="dialog"
    scrollable
    persistent 
    max-width="1000px"
    transition="dialog-transition"
>
    <v-layout row wrap>
        <v-flex xs6>
            <v-card>
                <v-card-title primary-title class="pb-0 pt-3">
                    <v-layout row wrap>
                        <v-flex xs6>
                            <h3 class="headline mb-0">Detail Order</h3>        
                        </v-flex>
                        <v-flex xs6 class="text-xs-right">
                            <h3>Invoice No #{{ selected_order.L_SoNumber }}</h3>
                            <p class="mb-0">{{ selected_order.L_SoDate }}</p>
                        </v-flex>
                    </v-layout>
                </v-card-title>
                <v-card-text>
                    <v-layout row wrap>
                        <v-flex xs12>
                            
                            <v-layout row wrap mb-4>
                                <v-flex xs8>
                                    <h3 class="">{{ selected_order.M_CustomerName }}</h3>
                                    <p class="mb-0">{{ selected_order.M_CustomerAddress }}</p>
                                    <p class="mb-0">{{ selected_order.city_name }}</p>
                                </v-flex>  
                                <v-flex xs4 class="text-xs-right yellow pa-2" v-show="selected_order.F_PaymentReceiptImg != '' && selected_order.F_PaymentReceiptImg != null">
                                    <div>Bukti transfer :</div>
                                    <div><a href="javascript:;" @click="show_receipt">{{ selected_order.F_PaymentReceiptImg }}</a></div>
                                </v-flex>  
                            </v-layout>

                            <v-layout>
                                <v-flex xs6>
                                    <p class="caption mb-0">Kode iklan : <b>{{ selected_order.so_ads_number }}</b></p>
                                </v-flex>
                                <v-flex xs6>
                                    <p class="caption mb-0 text-right">Nomor pesanan mp : <b>{{ selected_order.so_mp_number }}</b></p>
                                </v-flex>
                            </v-layout>
                            <v-card flat>
                                <v-card-title primary-title class="orange white--text pt-2 pb-2">
                                    <v-layout row wrap>
                                        <v-flex xs6>
                                            <h4>Metode Pembayaran</h4>
                                        </v-flex>
                                        <v-flex xs6 class="text-xs-right">
                                            <h4>Jumlah</h4>
                                        </v-flex>
                                    </v-layout>
                                    
                                </v-card-title>

                                <template v-if="selected_order.M_PaymentTypeCode == 'IPAYMU.CS'">
                                    <v-card-text class="py-2">
                                        <finance-payment-admin-cs></finance-payment-admin-cs>    
                                    </v-card-text>
                                    
                                </template>

                                <template v-if="selected_order.M_PaymentTypeCode == 'TRANSFER'">
                                    <v-card-text v-show="selected_order.F_PaymentID == null">
                                        <finance-payment-admin-payment></finance-payment-admin-payment>
                                    </v-card-text>

                                    <v-card-text v-show="selected_order.F_PaymentID != null">
                                        <v-layout row wrap>
                                            <v-flex xs9>
                                                <div><b>{{ selected_order.M_PaymentTypeName }}</b></div>
                                                <div>{{ selected_order.account_bank_name }} No. {{ selected_order.M_BankAccountNumber }}</div>
                                                <div>a/n {{ selected_order.M_BankAccountName }}</div>

                                                <div class="mt-3"><b>BANK PENGIRIM</b></div>
                                                <div>{{ selected_order.M_BankName }} a/n {{ selected_order.F_PaymentSenderName }}</div>
                                                
                                            </v-flex>
                                            <v-flex xs3 class="text-xs-right">
                                                <div>Rp <b>{{ one_money(selected_order.F_PaymentAmount) }}</b></div>
                                                <div>&nbsp;</div>
                                                <div class="mt-3">{{ selected_order.F_PaymentDate }}</div>
                                            </v-flex>
                                        </v-layout>
                                    </v-card-text>
                                </template>
                                
                            </v-card>

                            <v-card flat>
                                <v-card-title primary-title class="black white--text pt-2 pb-2">
                                    <v-layout row wrap>
                                        <v-flex xs6>
                                            <h4>Item</h4>
                                        </v-flex>
                                        <v-flex xs6 class="text-xs-right">
                                            <h4>Total Tagihan</h4>
                                        </v-flex>
                                    </v-layout>
                                    
                                </v-card-title>

                                <v-card-text>
                                    <v-layout row wrap>
                                        <v-flex xs9>
                                            <div>
                                                <template v-for="(item, i) of selected_order.items">
                                                    <div :class="(i%2==0? 'blue--text':'') + ' mr-2'" v-bind:key="i">{{ item.item_name }} ({{ item.item_qty }})</div>
                                                </template>
                                            </div>
                                            
                                        </v-flex>
                                        <v-flex xs3 class="text-xs-right">
                                            <div>Rp <b>{{ one_money(selected_order.L_SoTotal) }}</b></div>
                                        </v-flex>
                                    </v-layout>
                                </v-card-text>
                            </v-card>

                            <div v-show="selected_order.order_note!=''">
                                <v-divider class="mt-2 mb-2"></v-divider>
                                <div class="body-2 pl-3"><u>Catatan Admin</u></div>
                                <div class="caption pl-3 pr-3">{{selected_order.order_note}}</div>
                                <v-divider class="mt-2 mb-2"></v-divider>
                            </div>
                            

                        </v-flex>
                        
                    </v-layout>
                    

                </v-card-text>
                <v-card-actions>
                    <v-btn flat color="primary" @click="dialog=!dialog">Tutup</v-btn>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" @click="confirm" :disabled="!btn_confirm_enable">Konfirmasi</v-btn>
                    
                </v-card-actions>
            </v-card>
        </v-flex>

        <v-flex xs6>
            <v-card class="fill-height">
                <v-card-text style="height:100%">
                    <object :data="report_url" type="application/pdf" width="100%" height="100%"></object>
                </v-card-text>
            </v-card>
        </v-flex>

        <common-dialog-image v-if="dialog_image" :image_url="receipt_url"></common-dialog-image>
    </v-layout>
    
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
        "finance-payment-admin-payment":httpVueLoader("./finance-payment-admin-payment.vue"),
        "finance-payment-admin-cs":httpVueLoader("./finance-payment-admin-cs.vue"),
        "common-dialog-image":httpVueLoader("../../common/components/common-dialog-image-2.vue?t=123")
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
            ],

            
        }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.payment.dialog_item },
            set (v) { this.$store.commit('payment/set_common', ['dialog_item', v]) }
        },

        selected_order () {
            if (this.$store.state.payment.selected_order)
                return this.$store.state.payment.selected_order
            return {}
        },

        report_url () {
            return this.$store.state.payment.URL+"report/one_iv_001?soid="+this.selected_order.L_SoID+
                "&ts="+(Math.random()*1000000)
        },

        dialog_image : {
            get () { return this.$store.state.dialog_image },
            set (v) { this.$store.commit('set_dialog_image', v) }
        },

        receipt_url () {
            let x = this.selected_order.F_PaymentReceiptImg
            
            if (x == '' || x == null)
                return ''
            return this.$store.state.payment.UPL_URL+'receipts/'+x
        },

        btn_confirm_enable () {
            if (this.selected_order.M_PaymentTypeCode == 'IPAYMU.CS')
                return false
            return true
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        },

        confirm () {
            this.$store.dispatch('payment/confirm')
        },

        show_receipt () {
            this.dialog_image = true
        }
    },

    mounted () {
        
    },

    watch : {
        
    }
}
</script>