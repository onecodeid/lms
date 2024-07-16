<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs5>
                    <h3 class="display-1 font-weight-light zalfa-text-title">STOK OPNAME</h3>
                </v-flex>
                <v-flex xs2>
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
                </v-flex>
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
                :items="opnames"
                :loading="false"
                hide-actions
                class="elevation-1">
                <template slot="items" slot-scope="props">
                    <td class="text-xs-center pa-2" @click="select(props.item)">{{ reverse_date(props.item.I_OpnameDate) }}</td>
                    <td class="text-xs-center pa-2" @click="select(props.item)">{{ props.item.I_OpnameNumber }}</td>
                    <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.I_OpnameNote }}</td>
                    
                    <td class="text-xs-center pa-0" @click="select(props.item)">
                        <v-btn color="primary" class="btn-icon ma-0" small @click="edit(props.item)"><v-icon>info</v-icon></v-btn>
                        <v-btn color="orange" dark class="btn-icon ma-0" small @click="print_me(props.item)"><v-icon>print</v-icon></v-btn>
                        <!-- <v-btn color="red" dark class="btn-icon ma-0" small @click="del(props.item)"><v-icon>delete</v-icon></v-btn> -->
                    </td>
                    <!-- <td class="text-xs-center pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.M_DoctorHP}}</td>
                    <td class="text-xs-left pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.status}}</td> -->
                </template>
            </v-data-table>
            <v-divider></v-divider>
            <v-pagination
                style="margin-top:10px;margin-bottom:10px"
                v-model="curr_page"
                :length="xtotal_page"
            ></v-pagination>
        </v-card-text>
        
        <common-dialog-delete :data="opname_id" @confirm_del="confirm_del" v-if="dialog_delete"></common-dialog-delete>
        <common-dialog-print :data="opname_id" :report_url="report_url" v-if="dialog_print"></common-dialog-print>
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
        "common-dialog-print" : httpVueLoader("../../common/components/common-dialog-print.vue"),
        'common-datepicker' : httpVueLoader('../../common/components/common-datepicker.vue')
    },

    data () {
        return {
            curr_page: 1,
            xtotal_page: 1,
            headers: [
                {
                    text: "TANGGAL",
                    align: "center",
                    sortable: false,
                    width: "8%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "NOMOR",
                    align: "center",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "CATATAN",
                    align: "left",
                    sortable: false,
                    width: "74%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },,
                {
                    text: "ACTION",
                    align: "center",
                    sortable: false,
                    width: "8%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ]
        }
    },

    computed : {
        opnames () {
            return this.$store.state.opname.opnames
        },

        dialog_delete () {
            return this.$store.state.dialog_delete
        },

        opname_id () {
            return this.$store.state.opname.selected_opname.I_OpnameID
        },

        edate : {
            get () { return this.$store.state.opname.e_date },
            set (v) { this.$store.commit('opname/set_edate', v) }
        },

        sdate : {
            get () { return this.$store.state.opname.s_date },
            set (v) { this.$store.commit('opname/set_sdate', v) }
        },

        query : {
            get () { return this.$store.state.opname.query },
            set (v) { this.$store.commit('opname/set_common', ['query', v]) }
        },

        report_url () {
            return this.$store.state.opname.URL + 'report/one_wh_006?opnameid=' + this.opname_id
        },

        dialog_print () {
            return this.$store.state.dialog_print
        }
    },

    methods : {
        add () {
            this.$store.commit('opname_new/set_common', ['edit', false])
            this.$store.commit('opname_new/set_dialog_new', true)
            this.$store.commit('opname_new/set_items', [])
            this.$store.commit('opname_new/set_common', ['opname_note', ''])
        },

        edit (x) {
            this.select(x)
            let sc = x
            this.$store.commit('opname_new/set_common', ['edit', true])
            this.$store.commit('opname_new/set_common', ['opname_note', sc.I_OpnameNote])
            this.$store.commit('opname_new/set_common', ['opname_number', sc.I_OpnameNumber])
            this.$store.dispatch('opname_new/search_detail', {})
            // this.$store.commit('opname_new/set_common', ['opname_address', sc.I_OpnameAddress])
            // this.$store.commit('opname_new/set_common', ['search_city', sc.full_address])
            // this.$store.commit('opname_new/set_selected_city', {kelurahan_id:sc.kelurahan_id,full_address:sc.full_address})
            this.$store.commit('opname_new/set_dialog_new', true)
        },

        del (x) {
            this.select(x)
            this.$store.commit('set_dialog_delete', true)
        },

        confirm_del (x) {
            this.$store.dispatch('opname/del', {id:x.data})
        },

        select (x) {
            this.$store.commit('opname/set_selected_opname', x)
        },

        reverse_date(x) {
            return x.substr(0,10).split('-').reverse().join('-')
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
            this.$store.dispatch('opname/search', {})
        },

        print_me (x) {
            this.select(x)
            this.$store.commit('set_dialog_print', true)
        }
    },

    mounted () {
        this.$store.dispatch('opname_new/search_warehouse').then(x => {
            this.$store.dispatch('opname/search', {})
        })
    }
}
</script>