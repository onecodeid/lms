<template>
    <v-dialog
        v-model="dialog"
        max-width="500px"
        transition="dialog-transition"
    >
        <v-card>
            <v-card-title primary-title class="red white--text">
                <v-icon color="white">error_outline</v-icon> <h3 class="ml-2">Peringatan</h3>
            </v-card-title>

            <v-card-text>
                Kembali ke halaman sebelumnya, Kupon otomatis akan terhapus. Anda harus memasukkan ulang nanti.<br><br>Lanjut ?
            </v-card-text>

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="red" outline dark @click="dialog=!dialog">Batal</v-btn>
                <v-btn color="red" dark @click="confirm">Lanjut</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
module.exports = {
    data () {
        return {
            
        }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.quickorder.dialog_confirm_remove_coupon },
            set (v) { this.$store.commit('quickorder/set_common', ['dialog_confirm_remove_coupon', v]) }
        }
    },

    methods : {
        confirm () {
            this.dialog = false

            // CLEAR COUPON
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

            this.$store.commit('quickorder/set_common', ['dialog_note', false])
        }
    }
}
</script>