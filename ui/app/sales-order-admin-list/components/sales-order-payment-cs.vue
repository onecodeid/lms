<template>
    <v-layout row wrap>
        <v-flex xs2 pr-2>
            <v-img :src="logo_cs" :alt="logo_cs" aspect-ratio="2"></v-img>
        </v-flex>
        <v-flex xs10 pt-2 class="ipaymu_bottom_dashed">
            <h3 class="title">{{selected_order.ipaymu_channel.toUpperCase()}}</h3>
        </v-flex>
        <v-flex offset-xs2 xs10 pt-1>
            <span class="caption">Kode pembayaran :</span>
        </v-flex>
        <v-flex offset-xs2 xs10 pb-4>
            <h3 class="orange--text">{{selected_order.ipaymu_trx_code}}</h3>
        </v-flex>
        <v-flex xs12 class="ipaymu_top_dashed" pt-2>
            <span class="caption">{{selected_order.ipaymu_note}}</span>
        </v-flex>
    </v-layout>
</template>

<style scoped>
.ipaymu_bottom_dashed {
    border-bottom: dashed 1px blue
}

.ipaymu_top_dashed {
    border-top: dashed 1px blue
}
</style>
<script>
module.exports = {
    computed : {
        selected_order () {
            if (this.$store.state.salesorder.selected_order)
                return this.$store.state.salesorder.selected_order
            return {}
        },

        URL () {
            return this.$store.state.salesorder.URL
        },

        logo_cs () {
            return "../assets/img/logo-"+this.selected_order.ipaymu_channel+".png"
        }
    }
}
</script>