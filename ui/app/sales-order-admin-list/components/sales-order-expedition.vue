<template>
    <v-dialog
        v-model="dialog"
        scrollable
        max-width="300px"
        transition="dialog-transition"
        :fullscreen="$vuetify.breakpoint.smAndDown"
    >
        <v-card :class="{'fill-height':$vuetify.breakpoint.smAndDown}">
            <v-card-title primary-title class="primary white--text pt-2 pb-2 px-2 hidden-md-and-up shrink">
                <v-layout row wrap>
                    <v-flex xs12 sm12>
                        <v-btn flat color="primary" class="ma-0 btn-icon mr-2 hidden-md-and-up" @click="dialog=!dialog" style="float:left">
                            <v-icon class="white--text" medium>arrow_back</v-icon>
                        </v-btn>
                        <h3 class="headline text-xs-center pr-5">PILIH PENGIRIMAN</h3>        
                    </v-flex>
                </v-layout>
            </v-card-title>

            <v-card-text :class="{'grow':$vuetify.breakpoint.smAndDown}">
                <v-layout row wrap>
                    <v-flex xs12 sm6 md12 offset-xs0 offset-sm3 offset-md0>
                        <v-select
                            :items="expeditions"
                            v-model="selected_expedition"
                            item-text="M_ExpeditionName"
                            item-value="M_ExpeditionID"
                            label="Ekspedisi"
                            placeholder="Pilih salah satu"
                            return-object
                        ></v-select>
                    </v-flex>

                    <v-flex xs12 sm6 md12 offset-xs0 offset-sm3 offset-md0>
                        <v-select
                            :items="services"
                            v-model="selected_service"
                            item-text="service"
                            item-value="service"
                            label="Service"
                            placeholder="Pilih salah satu"
                            return-object
                            :loading="loading_service"
                            v-show="!ex_cargo_show && !ex_free_show"
                        ></v-select>

                        <v-text-field
                            label="Nama Ekspedisi"
                            v-model="ex_other"
                            v-show="ex_cargo_show"
                        ></v-text-field>

                        <v-text-field
                            label="Catatan"
                            v-model="ex_note"
                            v-show="ex_cargo_show || ex_free_show"
                        ></v-text-field>

                        <v-text-field
                            label="Perkiraan Ongkos Kirim"
                            v-model="courier_cost"
                            prefix="Rp"
                            v-show="ex_cargo_show"
                        ></v-text-field>
                        
                    </v-flex>

                    <v-flex xs12 sm6 md12 offset-xs0 offset-sm3 offset-md0 class="hidden-md-and-up">
                        <v-layout row wrap>
                            <v-flex xs8 class="text-xs-right" mt-2>
                                Total
                            </v-flex>
                            <v-flex xs4 class="text-xs-right" pr-3 mt-2>
                                Rp {{one_money(total)}}
                            </v-flex>
                            
                            <v-flex xs8 class="text-xs-right" mt-1>
                                Ongkir
                                <div class="caption blue--text" v-if="!!origin&&!!destination">
                                       {{origin.city}} <span class="black--text">»</span> {{destination.subdistrict_name}}, {{destination.city}}, {{destination.province}}</div>
                            </v-flex>
                            <v-flex xs4 class="text-xs-right" pr-3 mt-1>
                                Rp {{one_money(exp_cost)}}
                            </v-flex>

                            <v-flex xs8 class="text-xs-right" mt-1>
                                COD
                            </v-flex>
                            <v-flex xs4 class="text-xs-right" pr-3 mt-1>
                                Rp {{one_money(cod_cost)}}
                            </v-flex>

                            <v-flex xs8 class="text-xs-right subheading" mt-1>
                                <b>Grand Total</b>
                            </v-flex>
                            <v-flex xs4 class="text-xs-right subheading" pr-3 mt-1>
                                Rp <b>{{one_money(grand_total)}}</b>
                            </v-flex>
                        </v-layout>
                        
                    </v-flex>
                </v-layout>
            </v-card-text>

            <v-card-actions class="hidden-sm-and-down">
                <v-spacer></v-spacer>
                <v-btn color="red" dark @click="dialog=!dialog" small class="mr-2">Tutup</v-btn>
            </v-card-actions>

            <v-card-actions class="hidden-md-and-up shrink pa-0">
                <v-layout row wrap>
                    <v-flex xs12 sm6 offset-xs0 offset-sm3>
                        <v-btn color="orange" :dark="!!selected_expedition&&!!selected_service" @click="payment" large block :disabled="!selected_expedition||!selected_service">
                            <h3 class="headline">KE PEMBAYARAN</h3><v-icon class="ml-2">arrow_forward</v-icon></v-btn>
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
            get () { return this.$store.state.quickorder.dialog_expedition },
            set (v) { this.$store.commit('quickorder/set_common', ['dialog_expedition', v]) }
        },

        expeditions () {
            return this.$store.state.quickorder.expeditions
        },

        selected_expedition : {
            get () { return this.$store.state.quickorder.selected_expedition },
            set (v) { 
                this.$store.commit('quickorder/set_selected_expedition', v)
                if (['EX.FREE','EX.OTHER'].indexOf(v.M_ExpeditionCode) < 0)
                    this.$store.dispatch('quickorder/search_service', {})
                if (['EX.FREE'].indexOf(v.M_ExpeditionCode) > -1) { 
                    this.courier_cost = 0
                    this.ex_other = ''
                    this.ex_note = ''
                }
            }
        },

        services () {
            return this.$store.state.quickorder.services
        },

        selected_service : {
            get () { return this.$store.state.quickorder.selected_service },
            set (v) { 
                this.$store.commit('quickorder/set_selected_service', v) 
                this.$store.commit('quickorder/set_common', ['courier_cost', v.cost[0].value])
            }
        },

        loading_service () {
            return this.$store.state.quickorder.loading_service
        },

        ex_other : {
            get () { return this.$store.state.quickorder.ex_other },
            set (v) { this.$store.commit('quickorder/set_common', ['ex_other', v]) }
        },

        ex_note : {
            get () { return this.$store.state.quickorder.ex_note },
            set (v) { this.$store.commit('quickorder/set_common', ['ex_note', v]) }
        },

        courier_cost : {
            get () { return this.$store.state.quickorder.courier_cost },
            set (v) { this.$store.commit('quickorder/set_common', ['courier_cost', v]) }
        },

        ex_cargo_show () {
            if (!this.selected_expedition) return false
            if (this.selected_expedition.M_ExpeditionCode != 'EX.OTHER') return false
            return true
        },

        ex_free_show () {
            if (!this.selected_expedition) return false
            if (this.selected_expedition.M_ExpeditionCode != 'EX.FREE') return false
            return true
        },

        total () {
            return this.$store.state.quickorder.total
        },

        exp_cost () {
            return this.$store.state.quickorder.courier_cost
        },

        cod_cost () {
            return 0
        },

        grand_total () {
            return parseFloat(this.total) + parseFloat(this.exp_cost) + parseFloat(this.cod_cost)
        },

        origin () {
            return this.$store.state.quickorder.origin
        },

        destination () {
            return this.$store.state.quickorder.destination
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        },

        payment () {
            this.$store.commit('quickorder/set_common', ['dialog_note', true])
        }
    },

    mounted () {
        this.$store.dispatch('quickorder/search_expedition')
    }
}
</script>