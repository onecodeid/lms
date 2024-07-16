<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs7>
                    <h3 class="display-1 font-weight-light zalfa-text-title">REKAPITULASI OMZET SELLER</h3>
                </v-flex>
                <v-flex xs2 pr-2>
                    <v-select
                        :items="levels"
                        v-model="selected_level"
                        return-object
                        item-text="M_CustomerLevelName"
                        item-value="M_CustomerLevelID"
                        placeholder="PILIH JENJANG"
                        solo
                        hint="Pilih jenjang"
                        persistent-hint
                    >
                    </v-select>
                </v-flex>
                <!-- <v-flex xs2>
                    <common-datepicker
                        label="Tanggal"
                        :date="sdate"
                        data="0"
                        @change="change_sdate"
                        classs="mt-0 ml-5"
                        hints="Tanggal Awal"
                        :details="true"
                    ></common-datepicker>
                </v-flex>

                <v-flex xs2>
                    <common-datepicker
                        label="Tanggal"
                        :date="edate"
                        data="0"
                        @change="change_edate"
                        classs="mt-0 ml-1 mr-5"
                        hints="Tanggal Akhir"
                        :details="true"
                    ></common-datepicker>
                </v-flex> -->
                <v-flex xs3 class="text-xs-right">
                    <v-text-field
                        solo
                        hide-details
                        placeholder="Pencarian" v-model="query"
                        @change="search"
                    >
                        <template v-slot:append-outer>
                            <v-btn color="primary" class="ma-0 btn-icon" @click="search">
                                <v-icon>search</v-icon>
                            </v-btn>      

                            <v-btn color="success" class="ma-0 ml-2 btn-icon" @click="add">
                                <v-icon>add</v-icon>
                            </v-btn>  
                        </template>
                    </v-text-field>
                    
                </v-flex>
            </v-layout>
        </v-card-title>
        <v-card-text class="pt-2">
            <v-data-table 
                :headers="headers"
                :items="orders"
                :loading="false"
                hide-actions
                class="elevation-1">
                <template slot="items" slot-scope="props">
                    <td class="text-xs-left pa-2" @click="select(props.item)">
                        {{ props.item.customer_name }}
                        <div class="caption"><i>{{ props.item.base_date }}</i></div></td>

                    <td v-for="(n) in 12" :key="n" class="text-xs-left pa-2" @click="select(props.item)" :class="class_monthly(n,props.item['month'+n],props.item.target_monthly,props.item.target_duration)">
                        {{ one_money(props.item['month'+n]) }}
                        <div class="caption" :class="{'blue--text':n<=props.item.target_duration}"><i>{{ one_money(props.item.target_monthly) }}</i></div>
                    </td>

                    <!-- <td class="text-xs-left pa-2" @click="select(props.item)" :class="class_monthly(1,props.item.month1,props.item.target_monthly,props.item.target_duration)">{{ one_money(props.item.month2) }}</td>
                    <td class="text-xs-center pa-2" @click="select(props.item)" :class="class_monthly(1,props.item.month1,props.item.target_monthly,props.item.target_duration)">{{ one_money(props.item.month3) }}</td>
                    <td class="text-xs-center pa-2" @click="select(props.item)" :class="class_monthly(1,props.item.month1,props.item.target_monthly,props.item.target_duration)">{{ one_money(props.item.month4) }}</td>
                    <td class="text-xs-right pa-2" @click="select(props.item)" :class="class_monthly(1,props.item.month1,props.item.target_monthly,props.item.target_duration)">{{ one_money(props.item.month5) }}</td>
                    <td class="text-xs-right pa-2" @click="select(props.item)" :class="class_monthly(1,props.item.month1,props.item.target_monthly,props.item.target_duration)">{{ one_money(props.item.month6) }}</td>
                    <td class="text-xs-center pa-2" @click="select(props.item)" :class="class_monthly(1,props.item.month1,props.item.target_monthly,props.item.target_duration)">{{ one_money(props.item.month7) }}</td>
                    <td class="text-xs-center pa-2" @click="select(props.item)" :class="class_monthly(1,props.item.month1,props.item.target_monthly,props.item.target_duration)">{{ one_money(props.item.month8) }}</td>
                    <td class="text-xs-center pa-2" @click="select(props.item)" :class="class_monthly(1,props.item.month1,props.item.target_monthly,props.item.target_duration)">{{ one_money(props.item.month9) }}</td>
                    <td class="text-xs-center pa-2" @click="select(props.item)"  :class="class_monthly(1,props.item.month1,props.item.target_monthly,props.item.target_duration)">{{ one_money(props.item.month10) }}</td>
                    <td class="text-xs-center pa-2" @click="select(props.item)" :class="class_monthly(1,props.item.month1,props.item.target_monthly,props.item.target_duration)">{{ one_money(props.item.month11) }}</td>
                    <td class="text-xs-center pa-2" @click="select(props.item)" :class="class_monthly(1,props.item.month1,props.item.target_monthly,props.item.target_duration)">{{ one_money(props.item.month12) }}</td> -->
                    <td class="text-xs-center pa-2" @click="select(props.item)"
                        :class="{'green lighten-2':Math.round(props.item.cumulative)>=Math.round(props.item.target),
                                'red lighten-3':Math.round(props.item.cumulative)<(Math.round(props.item.target)*0.75),
                                'orange lighten-3':Math.round(props.item.cumulative)>=(Math.round(props.item.target)*0.75)&&Math.round(props.item.cumulative)<Math.round(props.item.target)}">{{ one_money(props.item.cumulative) }}
                        <div class="caption blue--text"><i>{{ one_money(props.item.target) }}</i></div>
                    </td>
                    <td class="text-xs-center pa-2" @click="select(props.item)">
                        <v-btn color="red" dark class="btn-icon ma-0" small @click="degrad(props.item)">Degradasi</v-btn>
                    </td>
                    <!-- <td class="text-xs-center pa-0" @click="select(props.item)"> -->
                        <!-- <v-btn color="orange" class="btn-icon ma-0" small @click="print_invoice(props.item)" dark>
                            <v-icon class="mr-2">print</v-icon> Inv</v-btn>
                        <v-btn color="primary" class="btn-icon ma-0" small @click="show(props.item)">Lihat</v-btn> -->
                        
                        <!--  -->
                    <!-- </td> -->
                    <!-- <td class="text-xs-center pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.M_DoctorHP}}</td>
                    <td class="text-xs-left pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.status}}</td>-->
                </template>
            </v-data-table>
            <v-divider></v-divider>
            <v-pagination
                style="margin-top:10px;margin-bottom:10px"
                v-model="curr_page"
                :length="xtotal_page"
                @input="change_page"
            ></v-pagination>
        </v-card-text> -->
        
        <!-- <common-dialog-delete :data="order_id" @confirm_del="confirm_del" v-if="dialog_delete"></common-dialog-delete>
        <common-dialog-print :report_url="report_url" v-if="dialog_report"></common-dialog-print> -->
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
        'common-datepicker' : httpVueLoader('../../common/components/common-datepicker.vue'),
        "common-dialog-print" : httpVueLoader("../../common/components/common-dialog-print.vue")
    },

    data () {
        return {
            headers: [
                {
                    text: "CUSTOMER",
                    align: "left",
                    width: "10%",
                    sortable:false,
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ],

            report_url: ''
        }
    },

    computed : {
        orders () {
            return this.$store.state.omzet.orders
        },

        dialog_delete () {
            return this.$store.state.dialog_delete
        },

        order_id () {
            return this.$store.state.omzet.selected_order.L_SalesOrderID
        },

        edate : {
            get () { return this.$store.state.omzet.e_date },
            set (v) { this.$store.commit('omzet/set_edate', v) }
        },

        sdate : {
            get () { return this.$store.state.omzet.s_date },
            set (v) { this.$store.commit('omzet/set_sdate', v) }
        },

        query : {
            get () { return this.$store.state.omzet.query },
            set (v) { this.$store.commit('omzet/set_common', ['query', v]) }
        },

        dialog_report : {
            get () { return this.$store.state.dialog_print },
            set (v) { this.$store.commit('set_dialog_print', v) }
        },

        levels () {
            return this.$store.state.omzet.levels
        },

        selected_level : {
            get () { return this.$store.state.omzet.selected_level },
            set (v) { 
                this.$store.commit('omzet/set_selected_level', v)
                this.$store.dispatch('omzet/search')
            }
        },

        xtotal_page () {
            return this.$store.state.omzet.total_order_page
        },

        curr_page : {
            get () { return this.$store.state.omzet.current_page },
            set (v) { this.$store.commit('omzet/set_common', ['current_page', v]) }
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        },

        reverse_date(x) {
            return x.substr(0,10).split('-').reverse().join('-')
        },

        add () {
            
        },

        edit (x) {
            this.select(x)
        },

        del (x) {
            this.select(x)
            this.$store.commit('set_dialog_delete', true)
        },

        confirm_del (x) {
            // this.$store.dispatch('order/del', {id:x.data})
        },

        select (x) {
            this.$store.commit('omzet/set_selected_order', x)
        },

        show (x) {
            this.select(x)
            this.$store.dispatch('omzet/search_item')
            this.$store.commit('omzet/set_common', ['dialog_item', true])

            this.$store.commit('omzet/set_common', ['weight_add', x.L_SoAddWeight])
            this.$store.commit('omzet/set_common', ['weight_total', x.L_SoTotalWeight])
            return
        },

        change_edate(x) {
            this.edate = x.new_date
            this.search()
        },

        change_sdate(x) {
            this.sdate = x.new_date
            this.search()
        },

        search () {
            this.$store.dispatch('omzet/search', {})
        },

        print_invoice (x) {
            let so = this.$store.state.omzet.selected_order
            this.report_url = "http://localhost:8080/one-sales/api/report/one_wh_001?soid=2"
            this.$store.commit('set_dialog_print', true)
        },

        class_monthly (i, n, t, d) {     
            if (i > d)       
                return "grey lighten-3 grey--text"
            if (Math.round(n) < Math.round(t))
                return "red lighten-3"
            else
                return "green lighten-3"
        },

        degrad (x) {
            this.select(x)
            this.$store.commit('omzet/set_common', ['dialog_degrade', true])
            // this.$store.dispatch('omzet/degrade')
        },

        change_page(x) {
            this.curr_page = x
            this.$store.dispatch('omzet/search')
        }
    },

    mounted () {
        for (let i=1; i<=12; i++)
            this.headers.push({
                    text: this.$store.state.omzet.months[i].toUpperCase(),
                    align: "right",
                    width: "6%",
                    sortable:false,
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                })
        this.headers.push({
                    text: "KUMULATIF",
                    align: "right",
                    width: "6%",
                    sortable:false,
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                })
        this.headers.push({
                    text: "ACTION",
                    align: "center",
                    width: "6%",
                    sortable:false,
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                })
        this.$store.dispatch('omzet/search_level')
    }
}
</script>