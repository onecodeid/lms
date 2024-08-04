<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs4>
                    <h3 class="display-1 font-weight-light zalfa-text-title">Masterdata Siswa</h3>
                </v-flex>

                <v-flex xs2 pr-2>
                    <v-autocomplete
                            label="Propinsi"
                            v-model="selected_province"
                            :items="provinces"
                            auto-select-first
                            return-object
                            clearable
                            item-text="M_ProvinceName"
                            item-value="M_ProvinceID"
                            placeholder="Semua Propinsi"
                            hide-details
                            solo
                            >
                            <template
                                slot="item"
                                slot-scope="{ item }"
                                >
                                <v-list-tile-content>
                                <v-list-tile-title v-text="item.M_ProvinceName"></v-list-tile-title>
                                <!-- <v-list-tile-sub-title v-text="getAddress(item)"></v-list-tile-sub-title> -->
                                </v-list-tile-content>
                            </template>

                        </v-autocomplete>
                </v-flex>

                <v-flex xs2 pr-2>
                    <v-autocomplete
                            label="Kota"
                            v-model="selected_city"
                            :items="cities"
                            auto-select-first
                            return-object
                            clearable
                            item-text="M_CityName"
                            item-value="M_CityID"
                            placeholder="Semua Kota"
                            :disabled="selected_province == null"
                            solo
                            hide-details
                            >
                            <template
                                slot="item"
                                slot-scope="{ item }"
                                >
                                <v-list-tile-content>
                                <v-list-tile-title v-text="item.M_CityName"></v-list-tile-title>
                                <!-- <v-list-tile-sub-title v-text="getAddress(item)"></v-list-tile-sub-title> -->
                                </v-list-tile-content>
                            </template>

                        </v-autocomplete>
                </v-flex>

                <v-flex xs2>
                    <v-select :items="items" item-value="M_ItemID" item-text="M_ItemName" v-model="selected_item" clearable dense hide-details solo></v-select>
                </v-flex>

                <v-flex xs2 class="text-xs-right" pl-3>
                    
                    <v-text-field
                        solo
                        hide-details
                        placeholder="Pencarian" 
                        v-model="query"
                        @keyup="do_search($event)"
                    >
                        <template v-slot:append-outer>
                            <v-btn color="primary" class="ma-0 btn-icon" @click="search">
                                <v-icon>search</v-icon>
                            </v-btn>      

                            <!-- <v-btn color="orange" dark class="ma-0 ml-2 btn-icon" @click="transfer">
                                <v-icon>refresh</v-icon>
                            </v-btn>  -->

                            <v-btn color="success" class="ma-0 ml-2 btn-icon" @click="add">
                                <v-icon>add</v-icon>
                            </v-btn>  
                             
                        </template>
                    </v-text-field>
                    
                
                    <!-- <v-btn color="success" class="ma-0 btn-icon" @click="add">
                        <v-icon>add</v-icon>
                    </v-btn> -->
                </v-flex>
            </v-layout>
        </v-card-title>
        <v-card-text class="pt-2">
            <v-data-table 
                :headers="headers"
                :items="customers"
                :loading="false"
                hide-actions
                class="elevation-1">
                <template slot="items" slot-scope="props">
                    <td class="text-xs-left pa-2" v-bind:class="level_color(props.item.M_CustomerLevelCode)" @click="select(props.item)">
                        <b>{{ props.item.M_CustomerCode }}</b>
                        <div :class="props.item.leadsource_color+'--text'">{{ props.item.leadsource_name }}</div></td>
                    <td class="text-xs-left pa-2" v-bind:class="[level_color(props.item.M_CustomerLevelCode), props.item.M_CustomerIsNew == 'Y'?'customer-new':'']" @click="select(props.item)">
                        <b>{{ props.item.M_CustomerName }}</b><br><v-icon small>smartphone</v-icon>{{props.item.M_CustomerPhone}}</td>
                    <td class="text-xs-left pa-2" v-bind:class="level_color(props.item.M_CustomerLevelCode)" @click="select(props.item)">{{ props.item.M_CustomerAddress }}, {{ props.item.address_kelurahan }}</td>
                    <!-- <td class="text-xs-center pa-2" v-bind:class="level_color(props.item.M_CustomerLevelCode)" @click="select(props.item)">{{ props.item.M_CityName }}</td>
                    <td class="text-xs-center pa-2" v-bind:class="level_color(props.item.M_CustomerLevelCode)" @click="select(props.item)">{{ props.item.M_ProvinceName }}</td> -->
                    <!-- <td class="text-xs-center pa-2" v-bind:class="level_color(props.item.M_CustomerLevelCode)" @click="select(props.item)"><b>{{ props.item.M_CustomerLevelName }}<b/><br>{{duration(props.item.M_CustomerJoinDate)}}</td> -->
                    
                        <td class="text-xs-center pa-2" v-bind:class="level_color(props.item.M_CustomerLevelCode)" @click="select(props.item)"><b>{{ props.item.item_name }}</b></td>
                    <td class="text-xs-center pa-2" v-bind:class="level_color(props.item.M_CustomerLevelCode)" @click="select(props.item)"><b>{{ props.item.M_CustomerLevelName }}</b></td>

                    <td class="text-xs-center pa-2" v-bind:class="level_color(props.item.M_CustomerLevelCode)" @click="select(props.item)"><b>{{ props.item.customer_join_date_formatted }}</b></td>
                    <td class="text-xs-center pa-2" v-bind:class="level_color(props.item.M_CustomerLevelCode)" @click="select(props.item)"><b>{{ props.item.customer_end_date_formatted }}</b></td>
                    
                        <!-- <td class="text-xs-center pa-2" v-bind:class="level_color(props.item.M_CustomerLevelCode)" @click="select(props.item)">
                        <div v-for="(r, i) in props.item.referrer" v-bind:key="i"><span v-show="i=='level'">—</span> {{ r }}</div>
                    </td> -->
                    <td class="text-xs-center pa-0" v-bind:class="level_color(props.item.M_CustomerLevelCode)" @click="select(props.item)">
                        <v-btn color="primary" class="btn-icon ma-0" small @click="edit(props.item)"><v-icon>create</v-icon></v-btn>
                        <v-btn color="red" dark class="btn-icon ma-0" small @click="del(props.item)"><v-icon>delete</v-icon></v-btn>
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
                @input="change_page"
            ></v-pagination>
        </v-card-text>
        
        <common-dialog-delete :data="customer_id" @confirm_del="confirm_del" v-if="dialog_delete"></common-dialog-delete>
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

.customer-new {
    background: url(../../assets/img/new-badge.png);
    background-repeat: no-repeat;
    background-position: top right;
    background-size: 30px;
}
</style>

<script>
module.exports = {
    components : {
        "common-dialog-delete" : httpVueLoader("../../common/components/common-dialog-delete.vue")
    },

    data () {
        return {
            headers: [
                {
                    text: "NOMOR",
                    align: "left",
                    sortable: false,
                    width: "8%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "NAMA",
                    align: "left",
                    sortable: false,
                    width: "20%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "ALAMAT",
                    align: "center",
                    sortable: false,
                    width: "21%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                // {
                //     text: "KOTA",
                //     align: "center",
                //     sortable: false,
                //     width: "10%",
                //     class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                // },
                // {
                //     text: "PROPINSI",
                //     align: "center",
                //     sortable: false,
                //     width: "10%",
                //     class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                // },
                {
                    text: "KURSUS",
                    align: "center",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "KELAS",
                    align: "center",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "TANGGAL MULAI",
                    align: "center",
                    sortable: false,
                    width: "8%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "TANGGAL SELESAI",
                    align: "center",
                    sortable: false,
                    width: "8%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "ACTION",
                    align: "center",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ]
        }
    },

    computed : {
        ...Vuex.mapState('customer', ['items']),

        customers () {
            return this.$store.state.customer.customers
        },

        dialog_delete () {
            return this.$store.state.dialog_delete
        },

        customer_id () {
            return this.$store.state.customer.selected_customer.M_CustomerID
        },

        curr_page : {
            get () { return this.$store.state.customer.current_page },
            set (v) { this.$store.commit('customer/update_current_page', v) }
        },

        xtotal_page () {
            return this.$store.state.customer.total_customer_page
        },

        levels () {
            return this.$store.state.customer_new.customer_levels
        },

        selected_level : {
            get () { return this.$store.state.customer.selected_level },
            set (v) { 
                this.$store.commit('customer/set_selected_level', v) 
                this.$store.dispatch('customer/search', {})
            }
        },

        query : {
            get () { return this.$store.state.customer.search },
            set (v) { this.$store.commit('customer/update_search', v) }
        },

        provinces () {
            return this.$store.state.customer.provinces
        },

        selected_province : {
            get () { return this.$store.state.customer.selected_province },
            set (v) { 
                this.$store.commit('customer/set_selected_province', v)
                this.$store.dispatch('customer/search_city', {})
                // this.$store.dispatch('customer/search', {})
            }
        },

        selected_city : {
            get () { return this.$store.state.customer.selected_city },
            set (v) { 
                this.$store.commit('customer/set_selected_city', v)
                // this.$store.dispatch('customer/search', {})
            }
        },

        cities () {
            return this.$store.state.customer.cities
        },

        selected_item : {
            get () { return this.$store.state.customer.selected_item },
            set (v) { this.$store.commit('customer/set_object', ['selected_item', v]) }
        }
    },

    methods : {
        add () {
            this.$store.commit('customer_new/set_common', ['edit', false])
            this.$store.commit('customer_new/set_common', ['customer_name', ''])
            this.$store.commit('customer_new/set_common', ['customer_dob', null])
            this.$store.commit('customer_new/set_object', ['selected_sex', null])
            this.$store.commit('customer_new/set_common', ['customer_address', ''])
            this.$store.commit('customer_new/set_common', ['customer_phone', ''])
            this.$store.commit('customer_new/set_common', ['customer_phone2', ''])
            this.$store.commit('customer_new/set_common', ['customer_phone3', ''])
            this.$store.commit('customer_new/set_common', ['customer_note', ''])
            this.$store.commit('customer_new/set_common', ['customer_email', ''])
            this.$store.commit('customer_new/set_common', ['customer_postcode', ''])
            this.$store.commit('customer_new/set_common', ['customer_auto', 'N'])
            this.$store.commit('customer_new/set_common', ['customer_due_payment', 'N'])
            this.$store.commit('customer_new/set_common', ['customer_join_date', this.$store.state.customer_new.current_date])
            this.$store.commit('customer_new/set_common', ['customer_end_date', null])
            this.$store.commit('customer_new/set_customer_mps', [])
            this.$store.commit('customer_new/set_common', ['user_customer', 'N'])
            this.$store.commit('customer_new/set_common', ['user_customer_username', ''])
            this.$store.commit('customer_new/set_common', ['user_customer_password', ''])
            this.$store.commit('customer_new/set_common', ['user_customer_password_confirm', ''])
            this.$store.commit('customer_new/set_selected_customer_level', null)
            this.$store.commit('customer_new/set_selected_province', null)
            this.$store.commit('customer_new/set_selected_city', null)
            this.$store.commit('customer_new/set_selected_district', null)
            this.$store.commit('customer_new/set_selected_village', null)
            this.$store.commit('customer_new/set_dialog_new', true)
        },

        edit (x) {
            this.select(x)
            let sc = x
            this.$store.commit('customer_new/set_common', ['edit', true])
            this.$store.commit('customer_new/set_common', ['customer_name', sc.M_CustomerName])
            this.$store.commit('customer_new/set_common', ['customer_dob', sc.M_CustomerDob])
            this.$store.commit('customer_new/set_object', ['selected_sex', null])
            this.$store.commit('customer_new/set_common', ['customer_address', sc.M_CustomerAddress])
            this.$store.commit('customer_new/set_common', ['customer_phone', sc.M_CustomerPhone])
            this.$store.commit('customer_new/set_common', ['customer_phone2', sc.M_CustomerPhone2])
            this.$store.commit('customer_new/set_common', ['customer_phone3', sc.M_CustomerPhone3])
            this.$store.commit('customer_new/set_common', ['customer_note', sc.M_CustomerNote])
            this.$store.commit('customer_new/set_common', ['customer_email', sc.M_CustomerEmail])
            this.$store.commit('customer_new/set_common', ['customer_postcode', sc.M_CustomerPostCode?sc.M_CustomerPostCode:''])
            this.$store.commit('customer_new/set_common', ['customer_auto', sc.M_CustomerAutoAccept?sc.M_CustomerAutoAccept:'N'])
            this.$store.commit('customer_new/set_common', ['customer_due_payment', sc.M_CustomerDuePayment?sc.M_CustomerDuePayment:'N'])
            this.$store.commit('customer_new/set_common', ['customer_join_date', sc.M_CustomerJoinDate])
            this.$store.commit('customer_new/set_common', ['customer_end_date', sc.M_CustomerEndDate])
            this.$store.commit('customer_new/set_customer_mps', sc.customer_mps)

            // USER
            this.$store.commit('customer_new/set_common', ['change_password', false])
            this.$store.commit('customer_new/set_common', ['user_customer', 'N'])
            this.$store.commit('customer_new/set_common', ['user_customer_id', 0])
            this.$store.commit('customer_new/set_common', ['user_customer_username', ''])
            this.$store.commit('customer_new/set_common', ['user_customer_password', ''])
            this.$store.commit('customer_new/set_common', ['user_customer_password_confirm', ''])
            if (sc.user_customer_id) {
                this.$store.commit('customer_new/set_common', ['user_customer', 'Y'])
                this.$store.commit('customer_new/set_common', ['user_customer_id', sc.user_customer_id])
                this.$store.commit('customer_new/set_common', ['user_customer_username', sc.user_customer_username])
            }

            // Customer Level
            for (let cl of this.$store.state.customer_new.customer_levels)
                if (cl.M_CustomerLevelID == x.M_CustomerM_CustomerLevelID)
                    this.$store.commit('customer_new/set_selected_customer_level', cl)

            // Sex
            for (let sx of this.$store.state.customer_new.sexes)
                if (sx.id == x.M_CustomerSex)
                    this.$store.commit('customer_new/set_object', ['selected_sex', sx])

            // Province
            this.$store.dispatch('customer_new/search_province')

            // this.$store.commit('customer_new/set_common', ['search_city', sc.full_address])
            // this.$store.commit('customer_new/set_selected_city', {kelurahan_id:sc.kelurahan_id,full_address:sc.full_address})
            this.$store.commit('customer_new/set_dialog_new', true)
        },

        del (x) {
            this.select(x)
            this.$store.commit('set_dialog_delete', true)
        },

        confirm_del (x) {
            this.$store.dispatch('customer/del', {id:x.data})
        },

        select (x) {
            this.$store.commit('customer/set_selected_customer', x)
        },

        change_page(x) {
            this.curr_page = x
            this.$store.dispatch('customer/search', {})
        },

        search () {
            this.$store.dispatch('customer/search', {})
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
        
        report () {
            let params = ['province_id='+(this.selected_province?this.selected_province.M_ProvinceID:0), 
                    'city_id='+(this.selected_city?this.selected_city.M_CityID:0),
                    'level_id='+(this.selected_level?this.selected_level.M_CustomerLevelID:0),
                    'token='+this.$store.state.customer.token].join('&')
            let urls = this.$store.state.customer.URL+'report/one_master_001'+
                        '?'+params
            this.$store.commit('customer/set_common', ['report_url', urls])
            this.$store.commit('set_dialog_print', true)
        },

        duration(x) {
            let d1 = window.moment(x)
            let d2 = window.moment(window.moment().format("YYYY-MM-DD"))

            let y = d2.diff(d1, "years")
            let m = d2.diff(d1, "months")
            let md = d2.diff(d1, "months", true)
            let d = d2.diff(d1, "days")
            if (y < 1) {
                if (m > 0 && d > 14)
                    return m + ",5 bulan"
                else if (m > 0)
                    return m + " bulan"
                else
                    return d + " hari"
            } else if (y < 5 && m > 5) {
                return y + ",5 tahun"
            } else {
                return y + " tahun"
            }
        },

        transfer () {
            this.$store.commit('customer_transfer/set_object', ['selected_user_to', null])
            this.$store.commit('customer_transfer/set_object', ['selected_user_from', null])
            this.$store.commit('customer_transfer/set_object', ['selected_transfer_type', null])
            this.$store.commit('customer_transfer/set_object', ['customers', []])
            this.$store.commit('customer_transfer/set_object', ['transfers', []])
            this.$store.commit('customer_transfer/set_common', ['dialog_transfer', true])
        }
    },

    mounted () {
        this.$store.dispatch('customer/search_province', {})
        this.$store.dispatch('customer/search_item')
    },

    watch : {
        selected_city (v, o) {
            if (v != o)
                this.$store.dispatch('customer/search', {})
        },

        selected_province (v, o) {
            if (v != o)
                if (this.$store.state.customer.selected_city != null)
                    this.$store.commit('customer/set_selected_city', null)
                else
                    this.$store.dispatch('customer/search', {})
        }
    }
}
</script>