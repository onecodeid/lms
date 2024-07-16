<template>
    <v-card>
        <v-card-text class="pa-2">
            <v-layout row wrap>
                <v-flex xs4 v-for="(r, n) in reports" v-bind:key="n" pr-2 :class="{'mt-2':n>2}">
                    <v-card @click="select(r)">
                        <v-card-text class="pa-1">
                            <v-layout row wrap>
                                <v-flex xs2>
                                    <v-btn color="success" large class="ma-0 btn-icon-report"><v-icon large>assessment</v-icon></v-btn>
                                </v-flex>
                                <v-flex xs10 pl-2>
                                    <h3 class="subheading">{{ r.report_name }}</h3>
                                    <div class="caption">{{ r.report_code }}</div>
                                </v-flex>
                            </v-layout>
                            
                            
                        </v-card-text>
                    </v-card>
                </v-flex>
            </v-layout>   
        </v-card-text>
        
        <!-- PARAMS -->
        <report-param-fin-001 v-if="dialog['fin-001']"></report-param-fin-001>
        <report-param-fin-002 v-if="dialog['fin-002']"></report-param-fin-002>
        <report-param-fin-003 v-if="dialog['fin-003']"></report-param-fin-003>
        <report-param-fin-004 v-if="dialog['fin-004']"></report-param-fin-004>
        <report-param-fin-005 v-if="dialog['fin-005']"></report-param-fin-005>
        <report-param-wh-001 v-if="dialog['wh-001']"></report-param-wh-001>
        <report-param-wh-002 v-if="dialog['wh-002']"></report-param-wh-002>
        <report-param-wh-005 v-if="dialog['wh-005']"></report-param-wh-005>
        <report-param-iv-001 v-if="dialog['iv-001']"></report-param-iv-001>
        <report-param-sales-002 v-if="dialog['sales-002']"></report-param-sales-002>
        <report-param-sales-004 v-if="dialog['sales-004']"></report-param-sales-004>
        <report-param-sales-005 v-if="dialog['sales-005']"></report-param-sales-005>
        <report-param-sales-006 v-if="dialog['sales-006']"></report-param-sales-006>
        <report-param-sales-008 v-if="dialog['sales-008']"></report-param-sales-008>
        <report-param-sales-009 v-if="dialog['sales-009']"></report-param-sales-009>
        <report-param-sales-010 v-if="dialog['sales-010']"></report-param-sales-010>
        <report-param-sales-011 v-if="dialog['sales-011']"></report-param-sales-011>
        <report-param-master-001 v-if="dialog['master-001']"></report-param-master-001>
    </v-card>
</template>

<style scoped>
    .btn-icon-report {
        padding: 5px !important;
        min-width: 0px !important;
    }
</style>

<script>
let t = Math.round(Math.random() * 1e10)
module.exports = {
    components :{
        "report-param-fin-001" : httpVueLoader("./report-param-fin-001.vue?t="+t),
        "report-param-fin-002" : httpVueLoader("./report-param-fin-002.vue?t="+t),
        "report-param-fin-003" : httpVueLoader("./report-param-fin-003.vue?t="+t),
        "report-param-fin-004" : httpVueLoader("./report-param-fin-004.vue?t="+t),
        "report-param-fin-005" : httpVueLoader("./report-param-fin-005.vue?t="+t),
        "report-param-iv-001" : httpVueLoader("./report-param-iv-001.vue?t="+t),
        "report-param-wh-001" : httpVueLoader("./report-param-wh-001.vue?t="+t),
        "report-param-wh-002" : httpVueLoader("./report-param-wh-002.vue?t="+t),
        "report-param-wh-005" : httpVueLoader("./report-param-wh-005.vue?t="+t),
        "report-param-sales-002" : httpVueLoader("./report-param-sales-002.vue?t="+t),
        "report-param-sales-004" : httpVueLoader("./report-param-sales-004.vue?t="+t),
        "report-param-sales-005" : httpVueLoader("./report-param-sales-005.vue?t="+t),
        "report-param-sales-006" : httpVueLoader("./report-param-sales-006.vue?t="+t),
        "report-param-sales-008" : httpVueLoader("./report-param-sales-008.vue?t="+t),
        "report-param-sales-009" : httpVueLoader("./report-param-sales-009.vue?t="+t),
        "report-param-sales-010" : httpVueLoader("./report-param-sales-010.vue?t="+t),
        "report-param-sales-011" : httpVueLoader("./report-param-sales-011.vue?t="+t),
        "report-param-master-001" : httpVueLoader("./report-param-master-001.vue?t="+t)
    },

    computed : {
        reports () {
            if (this.$store.state.report.selected_group)
                return this.$store.state.report.selected_group.childs
            return []
        },

        selected_report () {
            return this.$store.state.report.selected_report
        },

        dialog : {
            get () { return this.$store.state.report_param.dialog },
            set (v) {
                let x = this.selected_report.report_code.replace(/ONE\-/, '').toLowerCase()
                this.$store.commit('report_param/set_dialog', [x, v]) 
            }
        }
    },

    methods : {
        select (x) {
            this.$store.commit('report/set_selected_report', x)
            //open
            this.dialog = true
        }
    }
}
</script>