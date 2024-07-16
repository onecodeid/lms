<template>
    <v-dialog
        v-model="dialog"
        scrollable
        max-width="1000px"
        transition="dialog-transition"
        content-class="dialog-customer-list"
    >
        <v-card>
            <v-card-title primary-title class="pb-2 pt-3">
                <h3 class="headline">Daftar Customer</h3>
                <v-spacer></v-spacer>
                <v-text-field
                        solo
                        hide-details
                        placeholder="Pencarian" 
                        v-model="query"
                        @keyup="do_search($event)"
                    >
                        <template v-slot:append-outer>
                            <v-btn color="primary" class="ma-0 btn-icon" @click="do_search">
                                <v-icon>search</v-icon>
                            </v-btn>
                        </template>
                    </v-text-field>
            </v-card-title>
            <v-card-text class="pt-1">
                <v-data-table 
                :headers="headers"
                :items="customers"
                :loading="false"
                hide-actions
                class="elevation-1">
                <template slot="items" slot-scope="props">
                    <td class="text-xs-left pa-2" v-bind:class="level_color(props.item.M_CustomerLevelCode)" @click="select(props.item)"><b>{{ props.item.M_CustomerCode }}</b></td>
                    <td class="text-xs-left pa-2" v-bind:class="level_color(props.item.M_CustomerLevelCode)" @click="select(props.item)"><b>{{ props.item.M_CustomerName }}</b><br><v-icon small>smartphone</v-icon>{{props.item.M_CustomerPhone}}</td>
                    <td class="text-xs-center pa-2" v-bind:class="level_color(props.item.M_CustomerLevelCode)" @click="select(props.item)">{{ props.item.M_CityName }}</td>
                    <td class="text-xs-center pa-2" v-bind:class="level_color(props.item.M_CustomerLevelCode)" @click="select(props.item)">{{ props.item.M_ProvinceName }}</td>
                    <td class="text-xs-center pa-2" v-bind:class="level_color(props.item.M_CustomerLevelCode)" @click="select(props.item)"><b>{{ props.item.M_CustomerLevelName }}<b/></td>
                    <td class="text-xs-center pa-0" v-bind:class="level_color(props.item.M_CustomerLevelCode)" @click="select(props.item)">
                        <v-btn color="primary" flat class="btn-icon ma-0" @click="pick_me(props.item)"><v-icon>get_app</v-icon></v-btn>
                    </td>
                </template>
            </v-data-table>
            <v-divider></v-divider>
            
            </v-card-text>
            <v-card-actions>
                <v-pagination
                    v-model="curr_page"
                    :length="xtotal_page"
                    @input="change_page"
                ></v-pagination>    
                <v-spacer></v-spacer>
                <v-btn color="primary" @click="dialog=!dialog">Tutup</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<style>
.dialog-customer-list .v-text-field.v-text-field--solo .v-input__control {
    min-height: 36px;
}
.dialog-customer-list .v-text-field.v-text-field--solo .v-input__append-outer {
    margin-top: 0px;
    margin-left: 0px;
}
</style>

<script>
module.exports = {
    data () {
        return {
            headers: [
                {
                    text: "NOMOR",
                    align: "left",
                    sortable: false,
                    width: "12%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "NAMA",
                    align: "left",
                    sortable: false,
                    width: "38%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "KOTA",
                    align: "center",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "PROPINSI",
                    align: "center",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "LEVEL",
                    align: "center",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "ACTION",
                    align: "center",
                    sortable: false,
                    width: "5%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ]
        }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.quickorder.dialog_customer },
            set (v) { this.$store.commit('quickorder/set_common', ['dialog_customer', v]) }
        },

        customers () {
            return this.$store.state.customer.customers
        },

        curr_page : {
            get () { return this.$store.state.customer.current_page },
            set (v) { this.$store.commit('customer/update_current_page', v) }
        },

        xtotal_page () {
            return this.$store.state.customer.total_customer_page
        },

        query : {
            get () { return this.$store.state.customer.search },
            set (v) { this.$store.commit('customer/update_search', v) }
        }
    },

    methods : {
        select (x) {
            this.$store.commit('customer/set_selected_customer', x)
        },

        change_page(x) {
            this.curr_page = x
            this.$store.dispatch('customer/search', {})
        },

        do_search(e) {
            if (e.which == 13)
                this.$store.dispatch('customer/search', {})
        },

        level_color (x) {
            if (x == 'CUST.DISTRIBUTOR')
                return 'pink lighten-4'
            if (x == 'CUST.AGENCY')
                return 'orange lighten-4'
            if (x == 'CUST.RESELLER')
                return 'yellow lighten-4'
            if (x == 'CUST.FAMILY')
                return 'green lighten-4'
            return 'cyan lighten-4'
        },

        pick_me (x) {
            this.$store.commit('quickorder/set_common', ['cust_id', x.M_CustomerID])
            this.$store.commit('quickorder/set_common', ['cust_name', x.M_CustomerName])
            this.$store.commit('quickorder/set_common', ['cust_address', x.M_CustomerAddress])
            this.$store.commit('quickorder/set_common', ['cust_city', x.full_address])
            this.$store.commit('quickorder/set_common', ['cust_postcode', ''])
            this.$store.commit('quickorder/set_common', ['cust_phone', x.M_CustomerPhone])
            this.$store.commit('quickorder/set_selected_city', {kecamatan_id:x.M_DistrictID})
            this.dialog=false
        },

        do_search(e) {
            if (e.which == 13)
                this.$store.dispatch('customer/search', {})
        }
    },

    mounted () {
        this.$store.commit('customer/set_selected_level', {M_CustomerLevelID:5})
        this.$store.dispatch('customer/search', {})
    }
}
</script>