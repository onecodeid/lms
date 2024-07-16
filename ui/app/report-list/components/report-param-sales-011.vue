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
                &nbsp;
            </v-card-text>

            <v-card-actions>
                <v-btn color="success" @click="dialog=!dialog" flat class="btn-icon">Tutup</v-btn>
                <v-spacer></v-spacer>
                <!-- <v-btn color="success" @click="generate('csv')" class="btn-icon" :disabled="!selected_admin" title="Download CSV">CSV</v-btn> -->
                <v-btn color="success" @click="generate('xls')" class="btn-icon" :disabled="false" title="Download EXCEL">Excel</v-btn>
                <v-btn color="orange" :dark="true" @click="generate()"
                    :disabled="false" class="btn-icon" title="Cetak PDF"><v-icon class="mr-1">print</v-icon> PDF</v-btn>
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
            get () { return this.$store.state.report_param.dialog['sales-011'] },
            set (v) { this.$store.commit('report_param/set_dialog', ['sales-011', v]) }
        },

        report_url () {
            return this.$store.state.report_param.report_url
        },

        params () {
            return "t=" + Math.round(Math.random() * 1e10)
            // ['sdate='+this.sdate, 'edate='+this.edate, 'uid='+this.selected_admin.user_id, 'levelid='+this.selected_level.M_CustomerLevelID].join('&')
        },

        selected_report () {
            return this.$store.state.report.selected_report
        },

        admins () {
            return this.$store.state.report_param.admins
        },

        selected_admin : {
            get () { return this.$store.state.report_param.selected_admin },
            set (v) { this.$store.commit('report_param/set_selected_admin', v) }
        },
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
    },

    mounted () {
        // this.$store.dispatch('report_param/search_admins')
        // this.$store.dispatch('report_param/search_customer_level')
    }
}
</script>