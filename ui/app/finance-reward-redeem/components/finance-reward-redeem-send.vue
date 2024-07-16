<template>
    <v-dialog
        v-model="dialog"
        scrollable
        persistent :overlay="false"
        max-width="500px"
        transition="dialog-transition"
    >
        <v-card>
            <v-card-title primary-title class="pb-0 py-2 indigo lighten-2 white--text">
                <h3 class="headline mb-0 mt-3">KIRIM HADIAH</h3>
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs12>
                        <v-layout row wrap>
                            <v-flex xs4>
                                <v-layout row wrap>
                                    <v-flex xs12>
                                        <v-select
                                            :items="types"
                                            v-model="selected_type"
                                            label="Diambil / Kirim"
                                            item-value="id"
                                            item-text="label"
                                            return-object
                                        ></v-select>
                                    </v-flex>

                                    <v-flex xs12>
                                        <common-datepicker
                                            :label="'Tanggal ' + (selected_type.id=='Y'?'Pengiriman':'Pengambilan')"
                                            :date="pdate"
                                            data="0"
                                            @change="change_pdate"
                                            classs=""
                                            hints=" "
                                            :solo="false"
                                            :details="true"
                                        ></common-datepicker>
                                    </v-flex>
                                </v-layout>
                            </v-flex>
                            <v-flex xs8 class="text-xs-right pl-3">
                                <h3>{{selected_redeem.customer_name}}</h3>
                                <v-card>
                                    <v-card-text class="green lighten-4 pa-2">
                                        Reward dipilih : {{selected_redeem.reward_name}}
                                    </v-card-text>
                                </v-card>
                            </v-flex>
                        </v-layout>
                        
                    </v-flex>
                    

                    <v-flex xs12>
                        <v-textarea
                            label="Catatan"
                            v-model="note"
                        ></v-textarea>
                    </v-flex>
                </v-layout>
            </v-card-text>
            <v-card-actions>
                <v-btn color="primary" flat @click="dialog=!dialog">Batal</v-btn>
                <v-spacer></v-spacer>
                <v-btn color="primary" @click="save">Kirim</v-btn>                
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
module.exports = {
    components : {
        'common-datepicker' : httpVueLoader('../../common/components/common-datepicker.vue')
    },

    computed : {
        dialog : {
            get () { return this.$store.state.redeem.dialog_send },
            set (v) { this.$store.commit('redeem/set_common', ['dialog_send', v]) }
        },

        types () {
            return this.$store.state.redeem.redeem_type
        },

        selected_type : {
            get () { return this.$store.state.redeem.selected_redeem_type },
            set (v) { this.$store.commit('redeem/set_selected_redeem_type', v) }
        },

        pdate : {
            get () { return this.$store.state.redeem.send_date },
            set (v) { this.$store.commit('redeem/set_common', ['send_date', v]) }
        },

        note : {
            get () { return this.$store.state.redeem.send_note },
            set (v) { this.$store.commit('redeem/set_common', ['send_note', v]) }
        },

        selected_redeem () {
            return this.$store.state.redeem.selected_redeem
        }
    },

    methods : {
        change_pdate(x) {
            this.pdate = x.new_date
        },

        save () {
            this.$store.dispatch('redeem/redeem_send', {})
        }
    }
}
</script>
