<template>
    <v-dialog
        v-model="dialog"
        scrollable
        persistent
        max-width="1200px"
        transition="dialog-transition"
    >
        <v-card>
            <v-card-title primary-title class="py-2 info white--text">
                <h3 class="headline">PENERIMAAN / PEMBATALAN TRANSAKSI COD</h3>
            </v-card-title>
            <v-card-text class="py-2">
                <v-layout row wrap>
                    <v-flex xs3 pr-4>
                        <v-select
                            :items="types"
                            v-model="selected_type"
                            return-object
                            solo
                            :disabled="edit"
                        >
                            <template slot="item" slot-scope="data">
                                <div class="" :class="{'blue--text':data.item.value=='A','red--text':data.item.value=='R'}">
                                    {{data.item.text}}
                                </div>
                            </template>

                            <template slot="selection" slot-scope="data">
                                <div class="" :class="{'blue--text':data.item.value=='A','red--text':data.item.value=='R'}">
                                    {{data.item.text}}
                                </div>
                            </template>
                        </v-select>
                        
                        <common-datepicker
                            label="Tanggal"
                            :date="date"
                            data="0"
                            @change="change_date"
                            classs=""
                            hints=" "
                            :solo="false"
                            :details="true"
                            :disabled="edit"
                            v-if="dialog&&!edit"
                        ></common-datepicker>
                        <v-text-field
                            label="Tanggal"
                            :value="date"
                            disabled
                            v-show="edit"
                        ></v-text-field>

                    <!-- </v-flex>
                    <v-flex xs3 px-2> -->
                        <v-select
                            :items="expeditions"
                            item-text="M_ExpeditionName"
                            item-value="M_ExpeditionID"
                            return-object
                            v-model="selected_expedition"
                            label="Ekspedisi"
                            placeholder="Pilih ekspedisi"
                            :disabled="edit||item_length>0"
                        >
                            <template slot="item" slot-scope="data">
                                <v-img :src="URL+'../ui/app/'+data.item.M_ExpeditionLogo" max-width="48" height="24" contain class="mr-2"></v-img>
                                {{data.item.M_ExpeditionName}}
                            </template>
                            <template slot="selection" slot-scope="data">
                                <div class="black--text">{{data.item.M_ExpeditionName}}</div><v-spacer></v-spacer>
                                <v-img :src="URL+'../ui/app/'+data.item.M_ExpeditionLogo" max-width="48" height="24" contain class="mr-2"></v-img>
                                
                            </template>
                        </v-select>

                        <v-text-field
                            label="Catatan"
                            v-model="note"
                            placeholder=" "
                            small
                            :disabled="edit"
                        >
                            <!-- <template slot="prepend-inner">
                                <div class="caption blue--text" style="width:60px">
                                    Catatan
                                </div>
                            </template> -->
                        </v-text-field>
                    <!-- </v-flex>
                    <v-flex xs3 offset-xs3> -->
                        <v-text-field
                            v-model="total_actual"
                            suffix="Rp"
                            reverse
                            :disabled="edit"
                        >
                            <template slot="label">
                                <div class="blue--text" v-show="selected_type&&selected_type.value=='A'">Total Uang yang Diterima</div>
                                <div class="red--text" v-show="selected_type&&selected_type.value=='R'">Total Uang yang Dibayarkan</div>
                            </template>
                        </v-text-field>

                        <v-text-field
                            label="Total Tagihan"
                            :Value="one_money(total)"
                            suffix="Rp"
                            reverse
                            disabled
                            v-show="selected_type&&selected_type.value=='A'"
                        >
                        </v-text-field>

                        <v-text-field
                            label="Total Ongkos Kirim yang Harus Dibayar"
                            :Value="one_money(total_exp)"
                            suffix="Rp"
                            reverse
                            disabled
                            v-show="selected_type&&selected_type.value=='R'"
                        >
                        </v-text-field>
                    </v-flex>

                    <v-flex xs9>
                        <v-layout row wrap>
                            <v-flex xs6 pr-2>
                                <table-pending></table-pending>
                            </v-flex>

                            <v-flex xs6 pl-2>
                                <table-selected></table-selected>
                            </v-flex>
                        </v-layout>
                    </v-flex>
                </v-layout>

                
            </v-card-text>

            <v-card-actions class="info lighten-4">
                <v-layout row wrap>
                    <v-flex xs3>
                        <v-layout row wrap>
                            <v-flex xs4>
                                <v-btn color="primary" flat @click="dialog=!dialog" block>Tutup</v-btn>
                            </v-flex>
                            <v-flex xs8 pr-3>
                                <v-btn color="red" @click="reject" block dark v-show="selected_type&&selected_type.value=='R'" class="ma-0" :disabled="!btn_confirm_enable">KONFIRMASI</v-btn>
                                <v-btn color="primary" @click="accept" block v-show="selected_type&&selected_type.value=='A'" class="ma-0" :disabled="!btn_confirm_enable">KONFIRMASI</v-btn>        
                            </v-flex>
                        </v-layout>
                        
                        
                    </v-flex>

                    <v-flex xs9>
                        <v-layout row wrap>
                            <v-flex xs6 class="text-xs-right" pr-3 pt-1>
                                Transaksi Pending : <span class="zalfa-text-dashed"><b>{{pending_length}}</b> Transaksi</span>, Nominal : <span class="zalfa-text-dashed">Rp <b>{{one_money(pending_nominal)}}</b></span>
                            </v-flex>
                            <v-flex xs6 class="text-xs-right" pr-3 pt-1>
                                Total Dipilih : <span class="zalfa-text-dashed"><b>{{item_length}}</b> Transaksi</span>
                            </v-flex>
                        </v-layout>
                    </v-flex>
                </v-layout>
                
                
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
module.exports = {
    components : {
        'common-datepicker' : httpVueLoader('../../common/components/common-datepicker.vue'),
        'table-pending' : httpVueLoader('./finance-payment-cod-new-pending.vue'),
        'table-selected' : httpVueLoader('./finance-payment-cod-new-selected.vue')
    },

    data () {
        return {
        }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.cod.dialog_new },
            set (v) { this.$store.commit('cod/set_common', ['dialog_new', v]) }
        },

        edit () {
            return this.$store.state.cod.edit
        },

        expeditions () {
            return this.$store.state.cod_new.expeditions
        },

        selected_expedition : {
            get () { return this.$store.state.cod_new.selected_expedition },
            set (v) { 
                this.$store.commit('cod_new/set_selected_expedition', v) 
                this.$store.dispatch('cod_new/search_pending')
            }
        },

        note : {
            get () { return this.$store.state.cod_new.note },
            set (v) { this.$store.commit('cod_new/set_common', ['note', v]) }
        },

        total () {
            let items = this.$store.state.cod_new.items
            let total = 0
            for (let i of items)
                total += parseFloat(i.L_SoTotal)
            return total
        },

        total_actual : {
            get () { return this.$store.state.cod_new.total_actual },
            set (v) { this.$store.commit('cod_new/set_common', ['total_actual', v]) }
        },

        total_exp () {
            let items = this.$store.state.cod_new.items
            let total = 0
            let rate = this.selected_expedition
            for (let i of items)
                total += parseFloat(i.L_SoExpCost)*rate.M_ExpeditionCODFailRate/100
            return total
        },

        date : {
            get () { return this.$store.state.cod_new.date },
            set (v) { this.$store.commit('cod_new/set_common', ['date', v]) }
        },

        pending_length () {
            return this.$store.state.cod_new.pendings.length
        },

        item_length () {
            return this.$store.state.cod_new.items.length
        },

        pending_nominal () {
            let t = 0
            for (let p of this.$store.state.cod_new.pendings)
                t += parseFloat(p.L_SoTotal)
            return t
        },

        types () {
            return this.$store.state.cod_new.types
        },

        selected_type : {
            get () { return this.$store.state.cod_new.selected_type },
            set (v) { this.$store.commit('cod_new/set_selected_type', v) }
        },

        URL () {
            return this.$store.state.cod_new.URL
        },

        btn_confirm_enable () {
            if (this.item_length < 1 || this.total_actual == 0)
                return false
            if (!this.selected_type || !this.selected_expedition)
                return false
            if (this.edit)
                return false
            return true
        }
    },

    methods: {
        one_money (x) {
            return window.one_money(x)
        },

        accept () {
            this.$store.dispatch('cod_new/accept')
        },

        reject () {
            this.$store.dispatch('cod_new/reject')
        },

        change_date (x) {
            this.$store.commit('cod_new/set_common', ['date', x.new_date])
        }
    },

    mounted () {
        this.$store.dispatch('cod_new/search_expedition')
        this.selected_type = this.types[0]
    }
}
</script>