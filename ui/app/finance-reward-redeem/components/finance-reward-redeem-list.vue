<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs6>
                    <h3 class="display-1 font-weight-light zalfa-text-title">MEMBER</h3>
                </v-flex>
                <v-flex xs2>
                    <common-datepicker
                        label="Dari Tanggal"
                        :date="sdate"
                        data="0"
                        @change="change_sdate"
                        classs="mt-0 ml-5"
                        hints=" "
                        :details="false"
                    ></common-datepicker>
                </v-flex>

                <v-flex xs2>
                    <common-datepicker
                        label="Sampai Tanggal"
                        :date="edate"
                        data="0"
                        @change="change_edate"
                        classs="mt-0 ml-1 mr-5"
                        hints=""
                        :details="false"
                    ></common-datepicker>
                </v-flex>

                <v-flex xs2>
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
                        </template>
                    </v-text-field>
                </v-flex>
            </v-layout>
        </v-card-title>
        <v-card-text class="pt-2">
            <v-layout row wrap>
                <v-flex xs12>
                    <v-data-table 
                        :headers="headers"
                        :items="redeems"
                        :loading="false"
                        hide-actions
                        class="elevation-1">
                        <template slot="items" slot-scope="props">
                            <td class="text-xs-left pa-2" @click="select(props.item)" v-bind:class="[is_selected(props.item)?class_selected:'']">
                                {{ props.item.redeem_date }}
                            </td>
                            <td class="text-xs-left pa-2" @click="select(props.item)" v-bind:class="[is_selected(props.item)?class_selected:'']">
                                <v-layout row wrap>
                                    <v-flex xs12>
                                        {{ props.item.customer_name }}
                                    </v-flex>
                                </v-layout>
                            </td>
                            <td class="text-xs-left pa-2" @click="select(props.item)" v-bind:class="[is_selected(props.item)?class_selected:'']">
                                {{ props.item.reward_name }} <a href="javascript:;">[detail]</a>
                            </td>
                            <td class="text-xs-right pa-2" @click="select(props.item)" v-bind:class="[is_selected(props.item)?class_selected:'']">
                                {{ props.item.redeem_point }}
                            </td>
                            <td class="text-xs-right pa-2" @click="select(props.item)" v-bind:class="[is_selected(props.item)?class_selected:'']">
                                {{ props.item.point_before }}
                            </td>
                            <td class="text-xs-right pa-2" @click="select(props.item)" v-bind:class="[is_selected(props.item)?class_selected:'']">
                                {{ props.item.point_after }}
                            </td>

                            <td class="text-xs-center pa-2" @click="select(props.item)" v-bind:class="[is_selected(props.item)?class_selected:'']" v-show="props.item.status == 'N'">
                                Belum Dikirim / Diambil
                            </td>
                            <td class="text-xs-center pa-2" @click="select(props.item)" v-bind:class="[is_selected(props.item)?class_selected:'']" v-show="props.item.status != 'N'">
                                <span v-show="props.item.status == 'Y'" class="blue--text">Dikirim</span>
                                <span v-show="props.item.status == 'X'" class="green--text">Diambil</span>
                                <br/>
                                {{ props.item.sent_date }}
                            </td>
                            
                            <td class="text-xs-center pa-0" @click="select(props.item)" v-bind:class="[is_selected(props.item)?class_selected:'']">
                                <v-btn color="primary" class="btn-icon ma-0" small title="Kirim / Ambil hadiah" @click="send(props.item)"
                                    :disabled="false"> <v-icon>send</v-icon></v-btn>
                                <v-btn color="red" dark class="btn-icon ma-0" small title="Batalkan penukaran" @click="del(props.item)"><v-icon>delete</v-icon></v-btn>
                            </td>
                            <!-- <td class="text-xs-center pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.M_DoctorHP}}</td>
                            <td class="text-xs-left pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.status}}</td> -->
                        </template>
                    </v-data-table>
                </v-flex>
                <v-flex xs12>
                    <v-divider></v-divider>
                    <v-pagination
                        style="margin-top:10px;margin-bottom:10px"
                        v-model="curr_page"
                        :length="xtotal_page"
                        @input="change_page"
                    ></v-pagination>
                </v-flex>
            </v-layout>
            
        </v-card-text>
        <finance-reward-redeem-send></finance-reward-redeem-send>
        <common-dialog-delete :data="redeem_id" @confirm_del="confirm_del" v-if="dialog_delete" text="Apakah anda yakin akan membatalkan penukaran?"></common-dialog-delete>
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
        'finance-reward-redeem-send' : httpVueLoader('./finance-reward-redeem-send.vue')
    },

    data () {
        return {
            headers: [
                {
                    text: "TANGGAL",
                    align: "left",
                    sortable: false,
                    width: "8%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "NAMA CUSTOMER",
                    align: "left",
                    sortable: false,
                    width: "22%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "REWARD",
                    align: "left",
                    sortable: false,
                    width: "20%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "POIN DITUKAR",
                    align: "right",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "POIN SEBELUM",
                    align: "right",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "POIN SESUDAH",
                    align: "right",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "STATUS",
                    align: "center",
                    sortable: false,
                    width: "10%",
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
            class_selected: 'cyan lighten-4'
        }
    },

    computed : {

        dialog_delete () {
            return this.$store.state.dialog_delete
        },

        query : {
            get () { return this.$store.state.redeem.search },
            set (v) { this.$store.commit('redeem/set_common', ['search', v]) }
        },

        curr_page : {
            get () { return this.$store.state.redeem.current_page },
            set (v) { this.$store.commit('redeem/set_common', ['current_page', v]) }
        },

        xtotal_page () {
            return this.$store.state.redeem.total_page
        },

        redeems () {
            return this.$store.state.redeem.redeems
        },

        selected_redeem : {
            get() { return this.$store.state.redeem.selected_redeem },
            set (v) { this.$store.commit('redeem/set_selected_redeem', v) }
        },

        edate : {
            get () { return this.$store.state.redeem.edate },
            set (v) { this.$store.commit('redeem/set_edate', v) }
        },

        sdate : {
            get () { return this.$store.state.redeem.sdate },
            set (v) { this.$store.commit('redeem/set_sdate', v) }
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        },

        del (x) {
            this.select(x)
            this.$store.commit('set_dialog_delete', true)
        },

        confirm_del (x) {
            this.$store.dispatch('redeem/redeem_cancel', {id:x.data})
        },

        select (x) {
            this.$store.commit('redeem/set_selected_redeem', x)
        },

        change_page(x) {
            this.curr_page = x
            this.$store.dispatch('redeem/search', {})
        },

        is_selected (x) {
            if (this.selected_redeem)
                if (this.selected_redeem.redeem_id == x.redeem_id)
                    return true
            return false
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
            this.$store.dispatch('redeem/search', {})
        },

        send (x) {
            this.select(x)
            this.$store.commit('redeem/set_common', ['dialog_send', true])
        }
    },

    mounted () {
        this.$store.dispatch('redeem/search', {})
    }
}
</script>