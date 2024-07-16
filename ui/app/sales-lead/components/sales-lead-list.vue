<template>
    <v-layout style="padding-right:300px">
        <v-flex xs12>
            <v-card>
                <v-card-title primary-title class="pt-2 pb-0">
                    <v-layout row wrap>
                        <v-flex xs9>
                            <h3 class="display-1 zalfa-text-pink font-weight-light">DAFTAR LEAD</h3>
                        </v-flex>
                        <!-- <v-flex xs2>
                            <common-datepicker label="Dari Tanggal" :date="sdate" data="0" @change="change_sdate"
                                classs="mt-0 ml-5" hints=" " :details="false"></common-datepicker>
                        </v-flex>

                        <v-flex xs2>
                            <common-datepicker label="Sampai Tanggal" :date="edate" data="0" @change="change_edate"
                                classs="mt-0 ml-1 mr-5" hints="" :details="false"></common-datepicker>
                        </v-flex> -->
                        <v-flex xs3 class="text-xs-right">
                            <!-- <v-text-field solo hide-details placeholder="Pencarian" v-model="query" @change="search">
                                <template v-slot:append-outer>
                                    <v-btn color="primary" class="ma-0 btn-icon" @click="search">
                                        <v-icon>search</v-icon>
                                    </v-btn>

                                    <v-btn color="success" class="ma-0 ml-1 btn-icon" @click="add_lead"
                                        title="Order Normal">
                                        <v-icon>add</v-icon>
                                    </v-btn>
                                </template>
                            </v-text-field> -->
                            <v-btn
                                color="success"
                                class="white--text mx-0"
                                @click="add_lead"
                                >
                                LEAD BARU
                                <v-icon small class="ml-2">add</v-icon>
                            </v-btn>
                        </v-flex>
                    </v-layout>
                </v-card-title>
                <v-card-text class="pt-2">
                    <v-data-table :headers="headers" :items="leads" :loading="false" hide-actions class="elevation-1">
                        <template slot="items" slot-scope="props">
                            <td class="text-xs-left pa-2" @click="select(props.item)">{{ reverse_date(props.item.lead_date)
                                }}</td>
                            <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.lead_number }}</td>
                            <td class="text-xs-left pa-2" @click="select(props.item)">
                                <div><b>{{ props.item.lead_name }}</b> — <span class="grey--text">{{ props.item.leadtype }},
                                        {{ props.item.city_name }}</span></div>
                                <div><v-icon small>smartphone</v-icon> {{ props.item.lead_phone }}</div>
                            </td>
                            <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.leadsource_name }}</td>
                            <!-- <td class="text-xs-left pa-2" @click="select(props.item)"><span
                                    class="green--text font-weight-bold">{{ props.item.sales_number }}</span>
                                <div v-show="props.item.sales_date != ''"><i>─ {{ props.item.sales_date }}</i></div>
                            </td> -->
                            <td class="text-xs-left pa-2" @click="select(props.item)">
                                <v-chip color="teal" text-color="white" v-if="props.item.lead_close == 'Y'" class="mx-0" small>
                                    <v-avatar>
                                        <v-icon>check_circle</v-icon>
                                    </v-avatar>
                                    Close
                                </v-chip>
                                <v-chip color="red lighten-3" text-color="white" v-if="props.item.lead_close == 'N'" class="mx-0" small>
                                    <v-avatar>
                                        <v-icon>info</v-icon>
                                    </v-avatar>
                                    Belum
                                </v-chip>
                                <!-- <v-btn color="success" small class="mx-0" depressed
                                    v-if="props.item.lead_close == 'Y'">Closing</v-btn> -->
                                <!-- <v-btn color="red lighten-3 white--text" small class="mx-0" depressed
                                    v-if="props.item.lead_close == 'N'">Belum</v-btn> -->
                                <template>

                                    <template v-for="(f, i) in props.item.fus">
                                        <v-btn :color="fuColors[i] + ' lighten-2 white--text'" small class="mx-0" depressed
                                            icon @click="fuOpen(props.item, i)">{{ i + 1 }}</v-btn>
                                        <!-- <v-btn color="orange lighten-2 white--text" small class="mx-0" depressed icon>2</v-btn>
                                    <v-btn color="brown lighten-2 white--text" small class="mx-0" depressed icon>3</v-btn> -->
                                    </template>

                                    <v-btn color="red white--text" small class="mx-0" depressed icon flat
                                        v-if="props.item.lead_close == 'N'" @click="fuAdd(props.item)">+</v-btn>
                                </template>

                            </td>

                            <td class="text-xs-center pa-1" @click="select(props.item)">
                                <!-- <v-layout row wrap v-if="props.item.M_OrderStatusCode=='SO.Confirmed'">
                                <v-flex xs6 pr-1><v-btn color="primary" class="btn-icon ma-0" small @click="show(props.item)" block>Detail</v-btn></v-flex>
                                <v-flex xs6><v-btn color="primary" class="btn-icon ma-0" small @click="send_email(props.item)" block> <v-icon>email</v-icon> Email</v-btn></v-flex>
                            </v-layout> -->

                                <v-btn color="red" dark class="btn-icon ma-0" small @click="del(props.item)"
                                    title="Hapus data"><v-icon>delete</v-icon></v-btn>
                                <v-btn color="primary" class="btn-icon ma-0" small @click="edit(props.item)"
                                    title="Edit data"><v-icon>edit</v-icon></v-btn>
                                <!-- <v-btn color="primary" class="btn-icon ma-0" small @click="convert(props.item)"
                                    title="Konversi ke Penjualan"
                                    :disabled="props.item.sales_id != 0"><v-icon>send</v-icon></v-btn> -->
                            </td>
                        </template>
                    </v-data-table>
                    <v-divider></v-divider>
                    <v-pagination style="margin-top:10px;margin-bottom:10px" v-model="curr_page" :length="xtotal_page"
                        @input="change_page"></v-pagination>
                </v-card-text>

                <common-dialog-delete :data="lead_id" @confirm_del="confirm_del"
                    v-if="dialog_delete"></common-dialog-delete>
                <common-dialog-print :report_url="report_url" v-if="dialog_report"></common-dialog-print>
                <sales-lead-convert></sales-lead-convert>
            </v-card>
        </v-flex>
        

        <v-navigation-drawer v-model="drawer" absolute right class="mt-1">
            <v-list class="pa-1">
                <v-list-tile avatar>
                    <!-- <v-list-tile-avatar>
                        <img src="https://randomuser.me/api/portraits/men/85.jpg">
                    </v-list-tile-avatar> -->

                    <v-list-tile-content>
                        <v-list-tile-title><b>FILTER PENCARIAN</b></v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>
            <v-divider></v-divider>

            <v-list class="px-2">
                <filters></filters>
            </v-list>
            
        </v-navigation-drawer>
    </v-layout>

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
    components: {
        "common-dialog-delete": httpVueLoader("../../common/components/common-dialog-delete.vue"),
        'common-datepicker': httpVueLoader('../../common/components/common-datepicker.vue'),
        "common-dialog-print": httpVueLoader("../../common/components/common-dialog-print.vue"),
        "sales-lead-convert": httpVueLoader("./sales-lead-convert.vue"),
        filters: httpVueLoader("./sales-lead-filter.vue")
    },

    data() {
        return {
            headers: [
                {
                    text: "TANGGAL",
                    align: "left",
                    width: "8%",
                    sortable: false,
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "NOMOR",
                    align: "left",
                    sortable: false,
                    width: "8%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "NAMA PROSPEK",
                    align: "left",
                    sortable: false,
                    width: "44%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "SUMBER LEAD",
                    align: "left",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                // {
                //     text: "SO NUMBER",
                //     align: "left",
                //     sortable: false,
                //     width: "15%",
                //     class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                // },
                {
                    text: "STATUS / FOLLOW UP",
                    align: "left",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "ACTION",
                    align: "center",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ],

            fuColors: ['amber', 'orange', 'brown'],
            // report_url: ''

            drawer: true,
            items: [
            { title: 'Home', icon: 'dashboard' },
            { title: 'About', icon: 'question_answer' }
            ]
        }
    },

    computed: {
        leads() {
            return this.$store.state.saleslead.leads
        },

        dialog_delete() {
            return this.$store.state.dialog_delete
        },

        lead_id() {
            return this.$store.state.saleslead.selected_lead.lead_id
        },

        edate: {
            get() { return this.$store.state.saleslead.edate },
            set(v) { this.$store.commit('saleslead/set_edate', v) }
        },

        sdate: {
            get() { return this.$store.state.saleslead.sdate },
            set(v) { this.$store.commit('saleslead/set_sdate', v) }
        },

        query: {
            get() { return this.$store.state.saleslead.query },
            set(v) { this.$store.commit('saleslead/set_common', ['query', v]) }
        },

        dialog_report: {
            get() { return this.$store.state.dialog_print },
            set(v) { this.$store.commit('set_dialog_print', v) }
        },

        curr_page: {
            get() { return this.$store.state.saleslead.current_page },
            set(v) { this.$store.commit('saleslead/set_common', ['current_page', v]) }
        },

        xtotal_page() {
            return this.$store.state.saleslead.total_page
        },

        report_url() {
            return this.$store.state.saleslead.URL + "report/one_iv_001?soid=" +
                this.$store.state.saleslead.selected_lead.lead_ID
        }
    },

    methods: {
        __c(a, b) { this.$store.commit('saleslead_new/set_object', [a, b]) },
        __d(a, b) { return this.$store.dispatch("saleslead_new/" + a, b) },

        one_money(x) {
            return window.one_money(x)
        },

        reverse_date(x) {
            return x.substr(0, 10).split('-').reverse().join('-')
        },

        add() {

        },

        edit(x) {
            this.select(x)
        },

        del(x) {
            this.select(x)
            this.$store.commit('set_dialog_delete', true)
        },

        confirm_del(x) {
            // this.$store.dispatch('order/del', {id:x.data})
        },

        select(x) {
            this.$store.commit('saleslead/set_selected_lead', x)
        },

        show(x) {
            return
        },

        edit(x) {
            this.select(x)
            this.$store.commit('saleslead_new/set_common', ['dialog_quick', true])
            this.$store.commit('saleslead_new/set_common', ['edit', true])
            this.$store.commit('saleslead_new/set_details', x.details)

            this.$store.commit('saleslead_new/set_common', ['lead_id', x.lead_id])
            this.$store.commit('saleslead_new/set_common', ['lead_name', x.lead_name])
            this.$store.commit('saleslead_new/set_common', ['lead_address', x.lead_address])
            this.$store.commit('saleslead_new/set_common', ['lead_postcode', ''])
            this.$store.commit('saleslead_new/set_common', ['lead_phone', x.lead_phone])
            this.$store.commit('saleslead_new/set_common', ['lead_note', ''])
            this.$store.commit('saleslead_new/set_selected_leadsource', null)
            this.$store.commit('saleslead_new/set_selected_leadtype', null)
            this.$store.commit('saleslead_new/set_selected_city', null)

            this.__c("leadClose", x.lead_close)
            this.__c("leadAdsNumber", x.lead_adsnumber)
            this.__c("selected_items", x.details)
            this.__c("selectedLeadGreeting", x.lead_greeting)
            this.__c("selectedLeadPreclose", x.lead_preclose)
            this.__c("selectedLeadProblem", x.lead_problem)
            this.__c("followUps", x.fus)

            for (let ls of this.$store.state.saleslead_new.leadsources) {
                if (ls.leadsource_id == x.leadsource_id)
                    this.$store.commit('saleslead_new/set_selected_leadsource', ls)
            }

            for (let lt of this.$store.state.saleslead_new.leadtypes) {
                if (lt.leadtype_id == x.leadtype_id)
                    this.$store.commit('saleslead_new/set_selected_leadtype', lt)
            }

            this.$store.commit('saleslead_new/set_selected_city', { city_id: x.city_id, full_city_reverse: `${x.province_name} » ${x.city_name}` })

            this.__c("selectedCustomer", null)
            if (x.lead_customer > 0) {
                this.__c("mainTab", 0)
                this.__c("cust_id", x.lead_customer)
                this.__d("searchCustomer").then(d => {
                    this.__c("selectedCustomer", d.records[0])
                })
            } else this.__c("mainTab", 1)



            // this.$store.dispatch('saleslead/search_item')
            // this.$store.commit('saleslead/set_common', ['dialog_item', true])

            // this.$store.commit('saleslead/set_common', ['weight_add', x.lead_AddWeight])
            // this.$store.commit('saleslead/set_common', ['weight_total', x.lead_TotalWeight])

            // this.$store.commit('saleslead/set_common', ['edit', true])
            // for (let exp of this.$store.state.saleslead.expeditions)
            //     if (exp.M_ExpeditionID == x.lead_M_ExpeditionID) {
            //         this.$store.commit('saleslead/set_selected_expedition', exp)
            //         this.$store.dispatch('saleslead/search_service')
            //     }
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

        search() {
            this.$store.dispatch('saleslead/search', {})
        },

        print_invoice(x) {
            return
        },

        payment_confirmation(x) {
            // this.select(x)
            // this.$store.commit('payment/set_common', ['transfer_amount', x.lead_Total])
            // this.$store.commit('saleslead/set_common', ['dialog_payment', true])
            return
        },

        btn_inv_show(x) {
            // if (['SO.Confirmed'].indexOf(x) > -1)
            //     return true
            return false
        },

        status(x) {
            // if (x.W_CourierSent == 'Y')
            // return x.M_OrderStatusName.split(';')
            // if (x.W_CourierProcessing == 'Y')
            //     return ['Sedang Diproses']
            // return ['Menunggu Diproses']
        },

        status_color(x) {
            // if (['SO.NEW', 'SO.Approved'].indexOf(x) > -1)
            //     return 'success'
            // if (x == 'SO.Confirmed') return 'orange'
            // if (x == 'IV.Paid') return 'darken-4 deep-orange'
            // if (x == 'IV.Confirmed') return 'cyan'
        },

        add_lead() {
            this.$store.commit('saleslead_new/set_common', ['dialog_quick', true])
            this.$store.commit('saleslead_new/set_common', ['edit', false])
            this.$store.commit('saleslead_new/set_details', [])

            this.$store.commit('saleslead_new/set_common', ['lead_id', 0])
            this.$store.commit('saleslead_new/set_common', ['lead_name', ''])
            this.$store.commit('saleslead_new/set_common', ['lead_address', ''])
            this.$store.commit('saleslead_new/set_common', ['lead_postcode', ''])
            this.$store.commit('saleslead_new/set_common', ['lead_phone', ''])
            this.$store.commit('saleslead_new/set_common', ['lead_note', ''])
            this.$store.commit('saleslead_new/set_selected_leadsource', null)
            this.$store.commit('saleslead_new/set_selected_leadtype', null)
            this.$store.commit('saleslead_new/set_selected_city', null)

            this.__c("leadClose", "N")
            this.__c("leadAdsNumber", "")
            this.__c("selectedLeadGreeting", null)
            this.__c("selectedLeadPreclose", null)
            this.__c("selectedLeadProblem", null)
            this.__c("followUps", [])

            this.__c("selectedCustomer", null)
            this.__c("selected_items", null)

            this.__c("mainTab", 0)


            // this.$store.dispatch('saleslead_new/search_item')

            // window.location.href = "../sales-order-seller-add"
        },

        change_page(x) {
            this.curr_page = x
            this.$store.dispatch('saleslead/search')
        },

        convert(x) {
            this.select(x)

            this.$store.commit('saleslead_convert/set_common', ['lead_name', x.lead_name])
            this.$store.commit('saleslead_convert/set_common', ['lead_phone', x.lead_phone])
            this.$store.commit('saleslead_address/set_common', ['address', x.lead_address])
            this.$store.commit('saleslead_address/set_selected_city', {
                city_id: x.city_id,
                kecamatan_id: x.district_id,
                full_city_reverse: x.province_name + ' » ' + x.city_name
            })
            this.$store.commit('saleslead_convert/set_selected_tab', this.$store.state.saleslead_convert.tabs[0])
            this.$store.commit('saleslead_convert/set_common', ['dialog_convert', true])

            this.$store.dispatch('saleslead_address/search_district')
        },

        fuOpen(y, z) {
            this.edit(y)
            this.__c("fuTab", z)
        },

        fuAdd(z) {
            this.edit(z)

            let x = structuredClone(this.$store.state.saleslead_new.followUps), y = structuredClone(this.$store.state.saleslead_new.defaultFollowUp)
            x.push(y)
            this.__c("followUps", x)
            this.__c("fuTab", (x.length - 1))
        }
    },

    mounted() {
        this.$store.dispatch('saleslead/search')
    }
}
</script>