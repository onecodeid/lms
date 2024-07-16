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
                <h3>
                    <span>DEGRADASI CUSTOMER</span>
                </h3>
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs8 class="caption">
                        Nama Customer
                    </v-flex>
                    <v-flex xs4 class="caption">
                        Level
                    </v-flex>
                    <v-flex xs8 class="subheading font-weight-bold">
                        {{me.customer_name}}
                    </v-flex>
                    <v-flex xs4 class="subheading font-weight-bold">
                        {{me.level_name}}
                    </v-flex>

                    <v-flex xs12 class="caption mt-3">
                        Alamat
                    </v-flex>
                    <v-flex xs12 class="subheading font-weight-bold">
                        {{me.customer_address}}
                        <div class="caption font-weight-bold">Kota {{me.cities.city_name}} - Propinsi {{me.cities.province_name}}</div>
                    </v-flex>

                    <v-flex xs4 class="caption mt-3">
                        Bergabung sejak
                    </v-flex>
                    <v-flex xs8 class="caption mt-3">
                        Tanggal Perhitungan Omzet
                    </v-flex>
                    <v-flex xs4 class="subheading font-weight-bold">
                        {{me.created_date}}
                    </v-flex>
                    <v-flex xs8 class="subheading font-weight-bold">
                        {{me.base_date}}
                    </v-flex>

                    <v-flex xs4 class="caption mt-3">
                        Omzet {{me.target_duration}} Bulan Terakhir
                    </v-flex>
                    <v-flex xs8 class="caption mt-3">
                        Target Omzet
                    </v-flex>
                    <v-flex xs4 class="subheading font-weight-bold">
                        <span class="font-weight-light">Rp</span> {{one_money(me.cumulative)}}
                    </v-flex>
                    <v-flex xs8 class="subheading font-weight-bold">
                        <span class="font-weight-light">Rp</span> {{one_money(me.target)}}
                    </v-flex>
                </v-layout>
            </v-card-text>
            <v-card-actions>
                <v-btn color="primary" flat @click="dialog=!dialog">Batal</v-btn>
                <v-spacer></v-spacer>
                <v-btn color="red" :dark="false" @click="save" disabled>BUTTON ON PROGRESS</v-btn>     
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
module.exports = {
    computed : {
        dialog : {
            get () { return this.$store.state.omzet.dialog_degrade },
            set (v) { this.$store.commit('omzet/set_common', ['dialog_degrade', v]) }
        },

        me () {
            return this.$store.state.omzet.selected_order
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        },

        save () {
            this.$store.dispatch('omzet/degrade')
        }
    }
}
</script>