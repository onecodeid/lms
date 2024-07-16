<template>
    <v-layout row wrap>
        <v-flex xs12>
            <h3>{{selected_order.L_SoPaymentChannel.toUpperCase()}}</h3>
            <v-divider class="mb-2"></v-divider>
        </v-flex>
        <v-flex xs5>
            <div class="caption">Kode pembayaran :</div>
            <h3 class="display-1 red--text">{{selected_order.F_IpaymuTrxCode}}</h3>
        </v-flex>
        <v-flex xs1 class="text-xs-right">
            <v-divider vertical></v-divider>
        </v-flex>
        <v-flex xs5 offset-xs1>
            <div class="caption">Jumlah yang harus dibayar :</div>
            <h3 class="display-1">Rp {{one_money(selected_order.L_SoTotal)}}</h3>
        </v-flex>
        <v-flex xs12>
            <v-divider class="my-2"></v-divider>
        </v-flex>
        <v-flex xs12>
            <div class="caption">Lakukan pembayaran sebelum <span class="zalfa-text-dashed font-weight-bold">{{selected_order.F_IpaymuExpired}}</span></div>
            <div class="caption">{{selected_order.F_IpaymuNote}}</div>
        </v-flex>
    </v-layout>
</template>

<style scoped>
.v-divider {
    border-bottom: dashed 1px #333;
}

.v-divider--vertical {
    border-right: dashed 1px #333;
}
</style>

<script>
module.exports = {
    computed : {
        selected_order () {
            return this.$store.state.payment.selected_order
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        }
    }
}
</script>