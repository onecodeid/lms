<template>
    <v-dialog
        v-model="dialog"
        scrollable
        :overlay="false"
        :max-width="!!selected_transfer_type&&selected_transfer_type.id=='S'?'800px':'400px'"
        :fullscreen="false"
        transition="dialog-transition"
        content-class="dialog-transfer"
        persistent
    >
        <v-card>
            <v-card-title primary-title class="py-2 orange">
                <h3 class="headline white--text pt-1">TRANSFER DATA CUSTOMER</h3>
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex :class="!!selected_transfer_type&&selected_transfer_type.id=='S'?'xs6 pr-4':'xs12'" >
                        <v-autocomplete
                            :items="users"
                            v-model="selected_user_from"
                            label="Dari User"
                            return-object
                            item-value="user_id"
                            item-text="user_full_name"
                            clearable
                            :readonly="!!selected_user_from"
                            @click:clear="selected_user_from=null"
                            :disabled="transfers.length>0"
                        >
                            <template slot="item" slot-scope="data">
                                <v-layout row wrap>
                                    <v-flex xs12>{{data.item.user_full_name}}</v-flex>
                                    <v-flex xs12 class="caption grey--text">
                                        {{data.item.group_name}}
                                    </v-flex>
                                    <v-divider class="my-2"></v-divider>
                                </v-layout>
                            </template>
                        </v-autocomplete>

                        <v-autocomplete
                            :items="users"
                            v-model="selected_user_to"
                            label="Kepada User"
                            return-object
                            item-value="user_id"
                            item-text="user_full_name"
                            clearable
                            :readonly="!!selected_user_to"
                        >
                            <template slot="item" slot-scope="data">
                                <v-layout row wrap>
                                    <v-flex xs12>{{data.item.user_full_name}}</v-flex>
                                    <v-flex xs12 class="caption grey--text">
                                        {{data.item.group_name}}
                                    </v-flex>
                                    <v-divider class="my-2"></v-divider>
                                </v-layout>
                            </template>
                        </v-autocomplete>

                        <v-select
                            :items="transfer_types"
                            v-model="selected_transfer_type"
                            label="Customer yang Akan Ditransfer"
                            return-object
                            item-value="id"
                            item-text="text"
                        >
                        </v-select>

                        
                        <v-card depressed v-show="!!selected_transfer_type&&selected_transfer_type.id=='S'">
                            <v-card-title primary-title class="py-2 px-3 red lighten-2 white--text">
                                Customer Terpilih ({{transfers.length}})
                            </v-card-title>
                            <v-card-text class="py-1 px-2">
                                <v-chip v-show="transfers.length<1" block class="red lighten-2 white--text">Belum ada customer terpilih</v-chip>
                                <v-chip close v-for="(trn, n) in transfers" :key="n" small @input="delTransfer(n)">
                                    <span class="caption">{{trn.M_CustomerName}}</span>
                                </v-chip>
                            </v-card-text>
                        </v-card>

                        <v-layout row wrap>
                            <v-flex  xs3 class="pr-2">
                                <v-btn color="red" block class="mt-2" @click="dialog=!dialog" dark outline>Tutup</v-btn>
                            </v-flex>
                            <v-flex xs9>
                                <v-btn color="primary" block class="mt-2" :disabled="!btn_save_enabled" @click="transfer">T r a n s f e r</v-btn>
                            </v-flex>
                        </v-layout>
                        
                    </v-flex>
                    <v-flex xs6 v-show="!!selected_transfer_type&&selected_transfer_type.id=='S'">
                        <v-text-field
                            solo
                            hide-details
                            placeholder="Pencarian" 
                            v-model="query"
                            @keyup="do_search($event)"
                            dense
                        >
                            <template v-slot:append-outer>
                                <v-btn color="primary" class="ma-0 btn-icon" @click="search">
                                    <v-icon>search</v-icon>
                                </v-btn>
                            </template>
                        </v-text-field>
                        <v-data-table 
                            :headers="headers"
                            :items="customers"
                            :loading="false"
                            hide-actions
                            class="elevation-1"
                            >
                            <template slot="items" slot-scope="props">
                                <td class="text-xs-center pa-2" v-bind:class="level_color(props.item.M_CustomerLevelCode)">
                                    <v-btn color="orange" dark class="btn-icon ma-0" small @click="transferMe(props.item)" :disabled="isSelected(props.item)"><v-icon>arrow_left</v-icon></v-btn>
                                </td>
                                <td class="text-xs-left pa-2" v-bind:class="level_color(props.item.M_CustomerLevelCode)" @click="select(props.item)">
                                    {{ props.item.M_CustomerCode }}
                                    <!-- <div :class="props.item.leadsource_color+'--text'">{{ props.item.leadsource_name }}</div> -->
                                </td>
                                <td class="text-xs-left pa-2" v-bind:class="[level_color(props.item.M_CustomerLevelCode), props.item.M_CustomerIsNew == 'Y'?'customer-new':'']" @click="select(props.item)">
                                    {{ props.item.M_CustomerName }}
                                    <!-- <br><v-icon small>smartphone</v-icon>{{props.item.M_CustomerPhone}} -->
                                </td>
                                
                            </template>
                        </v-data-table>
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
        </v-card>
    </v-dialog>
</template>

<style>
    .dialog-transfer table.v-table tbody td {
        height: 36px;
    }

    .dialog-transfer table.v-table thead tr {
        height: 40px;
    }

    .dialog-transfer .v-text-field.v-text-field--solo .v-input__control {
    min-height: 36px;
    }
    
    .dialog-transfer .v-text-field.v-text-field--solo .v-input__append-outer {
        margin-top: 0px;
        margin-left: 0px;
    }
</style>
<script>
    module.exports = {
        components : {
            
        },
    
        data () {
            return {
                headers: [
                    {
                        text: "ACTION",
                        align: "center",
                        sortable: false,
                        width: "10%",
                        class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                    },
                    {
                        text: "NOMOR",
                        align: "left",
                        sortable: false,
                        width: "30%",
                        class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                    },
                    {
                        text: "NAMA",
                        align: "left",
                        sortable: false,
                        width: "70%",
                        class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                    }
                ]
            }
        },
    
        computed : {
            URL () {
                return this.$store.state.customer.URL
            },
    
            dialog : {
                get () { return this.$store.state.customer_transfer.dialog_transfer },
                set (v) { this.$store.commit('customer_transfer/set_common', ['dialog_transfer', v]) }
            },
            
            customers () {
                return this.$store.state.customer_transfer.customers
            },
            
            total_customer () {
                return this.$store.state.customer_transfer.total_customer
            },
            
            transfers () {
                return this.$store.state.customer_transfer.transfers
            },
            
            transfers_idx () {
                let idx = []
                for (let t of this.transfers)
                    idx.push(t.M_CustomerID)

                return idx
            },

            curr_page : {
                get () { return this.$store.state.customer_transfer.current_page },
                set (v) { this.$store.commit('customer_transfer/update_current_page', v) }
            },

            xtotal_page () {
                return this.$store.state.customer_transfer.total_customer_page
            },

            query : {
                get () { return this.$store.state.customer_transfer.search },
                set (v) { this.$store.commit('customer_transfer/update_search', v) }
            },
            
            users () {
                return this.$store.state.customer_transfer.users
            },

            selected_user_from : {
                get () { return this.$store.state.customer_transfer.selected_user_from },
                set (v) { 
                    this.$store.commit('customer_transfer/set_object', ['selected_user_from', v])
                    this.$store.dispatch('customer_transfer/search', {})
                }
            },

            selected_user_to : {
                get () { return this.$store.state.customer_transfer.selected_user_to },
                set (v) { this.$store.commit('customer_transfer/set_object', ['selected_user_to', v]) }
            },
            
            transfer_types () {
                return this.$store.state.customer_transfer.transfer_types
            },

            selected_transfer_type : {
                get () { return this.$store.state.customer_transfer.selected_transfer_type },
                set (v) { this.$store.commit('customer_transfer/set_object', ['selected_transfer_type', v]) }
            },

            btn_save_enabled () {
                if (!this.selected_user_from || !this.selected_user_to || !this.selected_transfer_type)
                    return false

                if (this.selected_transfer_type.id == 'S' && this.transfers.length < 1)
                    return false

                if (this.selected_user_from.user_id == this.selected_user_to.user_id)
                    return false

                return true
            }
        },

        methods : {
            change_page(x) {
                this.curr_page = x
                this.$store.dispatch('customer_transfer/search', {})
            },

            search () {
                this.$store.commit('customer_transfer/set_common', ['current_page', 1])
                this.$store.dispatch('customer_transfer/search', {})
            },

            do_search(e) {
                if (e.which == 13)
                    this.search()
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

            transferMe (x) {
                let transfers = this.transfers

                let exists = false
                for (let t of transfers) {
                    if (t.M_CustomerID == x.M_CustomerID) exists = true
                }

                if (!exists) {
                    transfers.push(x)
                    this.$store.commit('customer_transfer/set_object', ['transfers', transfers])
                }
            },

            delTransfer (x) {
                let transfers = this.transfers
                transfers.splice(x, 1)

                this.$store.commit('customer_transfer/set_object', ['transfers', transfers])
            },

            transfer () {
                this.$store.dispatch('customer_transfer/transfer')
            },

            isSelected (x) {
                if (this.transfers_idx.indexOf(x.M_CustomerID) > -1)
                    return true

                return false
            }
        },

        mounted () {
            // this.$store.dispatch('customer_transfer/search', {})
            this.$store.dispatch('customer_transfer/search_users')
        }
    }
</script>
