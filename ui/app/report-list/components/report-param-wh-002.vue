<template>
    <v-dialog
        v-model="dialog" 
        max-width="300px"
        transition="dialog-transition"
    >
        <v-card>
            <v-card-title primary-title class="green white--text pt-2 pb-1">
                <h3 class="headline">{{ selected_report.report_name }}</h3>    
            </v-card-title>

            <v-card-text>
                <v-layout row wrap mb-3>
                    <v-flex xs12>
                        <common-datepicker label="Tanggal Awal" data="1" :solo="false" @change="change_sdate" :date="sdate"></common-datepicker>
                    </v-flex>
                </v-layout>

                <v-layout row wrap mb-3>
                    <v-flex xs12>
                        <common-datepicker label="Tanggal Akhir" data="1" :solo="false" @change="change_edate" :date="edate"></common-datepicker>
                    </v-flex>
                </v-layout>

                <v-layout row wrap>
                    <v-flex xs12>
                        <v-select
                            :items="expeditions"
                            item-text="M_ExpeditionName"
                            item-value="M_ExpeditionID"
                            return-object
                            v-model="selected_expedition"
                            label="Ekspedisi"
                            clearable
                        ></v-select>
                    </v-flex>
                </v-layout>
            </v-card-text>

            <v-card-actions>
                <v-btn color="success" @click="dialog=!dialog" flat class="btn-icon">Tutup</v-btn>
                <v-spacer></v-spacer>
                <v-btn color="success" @click="generate('csv')" class="btn-icon" title="Download CSV">CSV</v-btn>
                <!-- <v-btn color="success" @click="generate('xls')" class="btn-icon" title="Download EXCEL">Excel</v-btn> -->
                <v-btn color="orange" dark @click="generate()"
                    class="btn-icon" title="Cetak PDF"><v-icon class="mr-1">print</v-icon> PDF</v-btn>
            </v-card-actions>
        </v-card>
        <!-- <common-dialog-print :report_url="report_url" v-if="dialog_report"></common-dialog-print> -->
    </v-dialog>
</template>

<script>
module.exports = {
    components : {
        'common-datepicker': httpVueLoader('./../../common/components/common-datepicker.vue'),
        // "common-dialog-print" : httpVueLoader("./../../common/components/common-dialog-print.vue")
    },

    computed : {
        dialog : {
            get () { return this.$store.state.report_param.dialog['wh-002'] },
            set (v) { this.$store.commit('report_param/set_dialog', ['wh-002', v]) }
        },

        report_url () {
            return this.$store.state.report_param.report_url
        },

        params () {
            return ['sdate='+this.sdate, 'edate='+this.edate, 'uid='+(this.selected_expedition?this.selected_expedition.M_ExpeditionID:0)].join('&')
        },

        edate : {
            get () { return this.$store.state.report_param.edate },
            set (v) { this.$store.commit('report_param/set_common', ['edate', v]) }
        },

        sdate : {
            get () { return this.$store.state.report_param.sdate },
            set (v) { this.$store.commit('report_param/set_common', ['sdate', v]) }
        },

        selected_report () {
            return this.$store.state.report.selected_report
        },

        expeditions () {
            return this.$store.state.report_param.expeditions
        },

        selected_expedition : {
            get () { return this.$store.state.report_param.selected_expedition },
            set (v) { this.$store.commit('report_param/set_selected_expedition', v) }
        }
    },

    methods : {
        generate (out) {
            let xcel = out=='xls'?true:false
            let urls = this.$store.state.report.URL+'report/'+this.selected_report.report_url+
                        (xcel?'_excel':'')+'?'+this.params
            urls += out=='csv'?'&out=csv':''
            this.$store.commit('report_param/set_common', ['report_url', urls])
            this.$store.commit('set_dialog_print', true)

            this.dialog=!this.dialog
        },

        change_edate(x) {
            this.edate = x.new_date
        },

        change_sdate(x) {
            this.sdate = x.new_date
        }
    },

    mounted () {
        this.$store.dispatch('report_param/search_expedition')
    }
}
</script>