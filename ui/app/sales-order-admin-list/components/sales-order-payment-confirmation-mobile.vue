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
                        <v-flex xs12 sm6>
                            <v-btn flat color="primary" class="ma-0 btn-icon mr-2 hidden-md-and-up" @click="dialog=!dialog" style="float:left">
                                <v-icon class="white--text" medium>arrow_back</v-icon>
                            </v-btn>
                            <h3 class="headline mb-0 pr-5 text-xs-center text-sm-left">Detail Order</h3>  
                            <!-- <h3 class="headline mb-0">Detail Order</h3>         -->
                        </v-flex>
                        <v-flex s12 sm6 class="text-xs-right hidden-xs-only">
                            <!-- <h3>Invoice No #{{ selected_order.L_SoNumber }}</h3> -->
                            <!-- <p class="mb-0">{{ selected_order.L_SoDate }}</p> -->
                        </v-flex>
                    </v-layout>
                </v-card-title>
                <v-card-text class="px-2">
                    <v-layout row wrap>
                        <v-flex xs12>
                            
                            <v-layout row wrap mb-4>
                                <v-flex xs8>
                                    <h3 class="">{{ selected_order.M_CustomerName }}</h3>
                                    <p class="mb-0">{{ selected_order.M_CustomerAddress }}</p>
                                    <p class="mb-0">{{ selected_order.city_name }}</p>
                                </v-flex> 

                                <v-flex xs4 class="text-xs-right">
                                    <h3>Invoice No #{{ selected_order.L_SoNumber }}</h3>
                                </v-flex> 
                                 
                            </v-layout>

                            <v-card flat>
                                <v-card-title primary-title class="orange white--text pt-2 pb-2">
                                    <v-layout row wrap>
                                        <v-flex xs6>
                                            <h4>Metode Pembayaran</h4>
                                        </v-flex>
                                        <v-flex xs6 class="text-xs-right">
                                            <h4>{{selected_order.payment_code}}</h4>
                                        </v-flex>
                                    </v-layout>
                                    
                                </v-card-title>

                                <v-card-text v-show="selected_order.F_PaymentID == null" class="px-1">
                                    <sales-order-payment-form v-if="selected_order.payment_code=='TRANSFER'"></sales-order-payment-form>
                                    <sales-order-payment-cs v-if="selected_order.payment_code=='IPAYMU.CS'"></sales-order-payment-cs>
                                    <sales-order-payment-cod-free v-if="selected_order.payment_code=='COD.FREE'"></sales-order-payment-cod-free>
                                </v-card-text>
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

                        </v-flex>
                        
                    </v-layout>
                    

                </v-card-text>
                <v-card-actions class="pa-0">
                    <v-layout row wrap>
                        <v-flex xs12 sm6 offset-xs0 offset-sm6>
                            <v-btn color="primary" block large @click="confirm" v-if="selected_order.payment_code=='TRANSFER'" :disabled="!btn_payment_enable">Konfirmasi</v-btn>
                            <v-btn color="primary" block large @click="confirm_cod" v-if="selected_order.payment_code=='COD.FREE'">Konfirmasi</v-btn>        
                        </v-flex>
                    </v-layout>
                    
                    
                </v-card-actions>
            </v-card>
        

        <!-- <v-flex xs6>
            <v-card class="fill-height">
                <v-card-text style="height:100%">
                    <object :data="report_url" type="application/pdf" width="100%" height="100%"></object>
                </v-card-text>
            </v-card>
        </v-flex> -->

        
    
    
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
        "sales-order-payment-form":httpVueLoader("./sales-order-payment-form.vue"),
        "sales-order-payment-cs":httpVueLoader("./sales-order-payment-cs.vue"),
        "sales-order-payment-cod-free":httpVueLoader("./sales-order-payment-cod-free.vue"),
        // "common-dialog-image":httpVueLoader("../../common/components/common-dialog-image.vue")
    },

    data () {
        return {
            w : []
        }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.salesorder.dialog_payment },
            set (v) { this.$store.commit('salesorder/set_common', ['dialog_payment', v]) }
        },

        selected_order () {
            if (this.$store.state.salesorder.selected_order)
                return this.$store.state.salesorder.selected_order
            return {}
        },

        report_url () {
            return this.$store.state.payment.URL+"report/one_iv_001?soid="+this.selected_order.L_SoID+
                "&ts="+(Math.random()*1000000)
        },

        btn_payment_enable () { 
            let thiss = this.$store.state.payment
            if (!this.$store.state.salesorder.selected_order ||
                !thiss.selected_account ||
                !thiss.selected_bank ||
                thiss.transfer_name == '' ||
                // thiss.transfer_date == '' ||
                thiss.transfer_time == '' ||
                parseFloat(thiss.transfer_amount) == 0)
                en = false
            else
                en = true
            return en
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        },

        confirm () {
            this.$store.dispatch('payment2/confirm')
        },

        confirm_cod () {
            this.$store.dispatch('payment2/confirm_cod')
        }
    },

    mounted () {
        
    },

    watch : {
        
    }
}
</script>