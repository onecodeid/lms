<template>
    <v-card>
        <!-- <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs12>
                    <h3 class="display-1 font-weight-light zalfa-text-title">Target Omzet per Level Customer</h3>
                </v-flex>

                

                <v-flex xs3 class="text-xs-right">

                </v-flex>
            </v-layout>
        </v-card-title> -->
        <v-card-text class="pt-3">
            <v-layout row v-for="(t, n) in targetomzets" :key="n">
                <v-flex md3 sm4 xs12 >
                    <v-text-field
                        :label="t.level_name"
                        outline
                        :value="t.target_amount"
                        reverse
                        :mask="one_mask_money(t.target_amount)"
                        @input="change_amount(n, $event)"
                    >
                    <template slot="append">
                        <div class="pt-2">Rp</div>
                    </template>
                    </v-text-field>
                </v-flex>
            </v-layout>

            <v-layout row>
                <v-flex md3 sm4 xs12 class="text-xs-right">
                    
                    <v-btn color="primary" class="ma-0" 
                    :block="$vuetify.breakpoint.smAndDown"
                    :large="$vuetify.breakpoint.smAndDown"
                    @click="save">Simpan</v-btn>
                </v-flex>
            </v-layout>
        </v-card-text>
        
        <v-snackbar
            v-model="snackbar"
            top
            :vertical="$vuetify.breakpoint.smAndDown"
        >
            Data berhasil disimpan
            <v-btn flat color="primary" @click.native="snackbar=!snackbar">Tutup</v-btn>
        </v-snackbar>
        
    </v-card>
</template>

<style scoped>
.v-text-field.v-text-field--solo .v-input__control {
    min-height: 36px;
}
.v-text-field.v-text-field--solo .v-input__append-outer {
    margin-top: 0px;
    margin-left: 0px;
}
</style>

<script>
module.exports = {
    components : {
        "common-dialog-delete" : httpVueLoader("../../common/components/common-dialog-delete.vue"),
        'common-datepicker' : httpVueLoader('../../common/components/common-datepicker.vue')
    },

    data () {
        return {
            headers: [
                {
                    text: "TANGGAL",
                    align: "center",
                    sortable: false,
                    width: "7%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "KODE",
                    align: "center",
                    sortable: false,
                    width: "7%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "NAMA",
                    align: "left",
                    sortable: false,
                    width: "26%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "CATATAN",
                    align: "left",
                    sortable: false,
                    width: "40%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "J U M L A H",
                    align: "right",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "ACTION",
                    align: "center",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ]
        }
    },

    computed : {
        targetomzets () {
            return this.$store.state.targetomzet.targetomzets
        },

        query : {
            get () { return this.$store.state.targetomzet.search },
            set (v) { this.$store.commit('targetomzet/set_common', ['search', v]) }
        },

        snackbar : {
            get () { return this.$store.state.targetomzet.snackbar },
            set (v) { this.$store.commit('targetomzet/set_common', ['snackbar', v]) }
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        },

        search () {
            return this.$store.dispatch('targetomzet/search', {})
        },

        one_mask_money (x) {
            return window.one_mask_money(x)
        },

        change_amount (x, y) {
            let z = this.targetomzets
            z[x].target_amount = y

            this.$store.commit('targetomzet/update_targetomzets', z)
        },

        save () {
            this.$store.dispatch('targetomzet/save')
        }
    },

    mounted () {
        this.$store.dispatch('targetomzet/search')
    }
}
</script>