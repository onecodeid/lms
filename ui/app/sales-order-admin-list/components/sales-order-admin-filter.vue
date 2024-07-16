<template>
    <v-dialog
        v-model="dialog"
        scrollable
        persistent 
        fullscreen
        transition="dialog-transition"
        content-class="dialog-filter"
        v-if="dialog"
    >
        <v-card>
            <v-card-title primary-title class="pa-2 purple white--text">
                <v-btn flat color="primary" class="ma-0 btn-icon mr-2 hidden-md-and-up" @click="dialog=!dialog" style="float:left">
                    <v-icon class="white--text" medium>arrow_back</v-icon>
                </v-btn>
                <h3 class="headline">FILTER</h3>
            </v-card-title>
            <v-card-text class="grow" grow>
                <v-layout row wrap>
                    <v-flex xs12>
                        <common-datepicker
                            label="Dari Tanggal"
                            :date="sdate"
                            data="0"
                            @change="change_sdate"
                            classs=""
                            hints=" "
                            :details="true"
                            v-if="dialog"
                        ></common-datepicker>
                    </v-flex>

                    <v-flex xs12>
                        <common-datepicker
                            label="Sampai Tanggal"
                            :date="edate"
                            data="0"
                            @change="change_edate"
                            classs=""
                            hints=""
                            :details="true"
                            v-if="dialog"
                        ></common-datepicker>
                    </v-flex>
                </v-layout>

            </v-card-text>
            <v-card-actions class="px-0">
                <v-layout row wrap>
                    <v-flex xs12 sm6 offset-xs0 offset-sm6>
                        <v-btn color="primary" @click="save" block large>Terapkan</v-btn>        
                    </v-flex>
                </v-layout>
                
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<style>
.dialog-filter .v-card__title, .dialog-filter .v-card__actions {
    flex-grow: 0 !important;
}    
</style>

<script>
module.exports = {
    components : {
        'common-datepicker' : httpVueLoader('../../common/components/common-datepicker.vue')
    },

    data () {
        return {
            sdate: this.$store.state.salesorder.sdate,
            edate: this.$store.state.salesorder.edate
        }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.salesorder.dialog_filter },
            set (v) { this.$store.commit('salesorder/set_common', ['dialog_filter', v]) }
        },

        // edate : {
        //     get () { return this.$store.state.salesorder.edate },
        //     set (v) { return }
        // },

        // sdate : {
        //     get () { return this.$store.state.salesorder.sdate },
        //     set (v) { return }
        // }
    },

    methods : {
        change_edate(x) {
            this.edate = x.new_date
        },

        change_sdate(x) {
            this.sdate = x.new_date
        },

        save () {
            this.$store.commit('salesorder/set_sdate', this.sdate)
            this.$store.commit('salesorder/set_edate', this.edate)
            this.$store.dispatch('salesorder/search', {})
            this.dialog=false
        }
    },

    watch : {
        dialog (v, o) {
            if (v && !o) {
                this.sdate = this.$store.state.salesorder.sdate
                this.edate = this.$store.state.salesorder.edate
            }
        }
    }
}
</script>