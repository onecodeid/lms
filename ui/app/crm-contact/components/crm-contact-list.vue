<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs4>
                    <h3 class="display-1 font-weight-light zalfa-text-title">Masterdata Kontak</h3>
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
                                </v-list-tile-content>
                            </template>

                        </v-autocomplete>
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
                :items="contacts"
                :loading="false"
                hide-actions
                class="elevation-1">
                <template slot="items" slot-scope="props">
                    <!-- <td class="text-xs-left pa-2" v-bind:class="level_color(props.item.Crm_ContactLevelCode)" @click="select(props.item)">
                        <b>{{ props.item.Crm_ContactCode }}</b>
                        <div :class="props.item.leadsource_color+'--text'">{{ props.item.leadsource_name }}</div></td> -->
                    <td class="text-xs-left pa-2" @click="select(props.item)">
                        <b>{{ props.item.contact_name }}</b><br><v-icon small>smartphone</v-icon>{{props.item.contact_phone}}</td>
                    <td class="text-xs-left pa-2" v-bind:class="level_color(props.item.Crm_ContactLevelCode)" @click="select(props.item)">{{ props.item.Crm_ContactAddress }}, {{ props.item.address_kelurahan }}</td>
                    <td class="text-xs-center pa-2" v-bind:class="level_color(props.item.Crm_ContactLevelCode)" @click="select(props.item)">{{ props.item.city_name }}</td>
                    <td class="text-xs-center pa-2" v-bind:class="level_color(props.item.Crm_ContactLevelCode)" @click="select(props.item)">{{ props.item.province_name }}</td>
                    <!-- <td class="text-xs-center pa-2" v-bind:class="level_color(props.item.Crm_ContactLevelCode)" @click="select(props.item)"><b>LVL<b/><br>JOINDATE</td> -->
                    <td class="text-xs-center pa-2" v-bind:class="level_color(props.item.Crm_ContactLevelCode)" @click="select(props.item)">
                        REFERRER
                    </td>
                    <td class="text-xs-center pa-0" v-bind:class="level_color(props.item.Crm_ContactLevelCode)" @click="select(props.item)">
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
        
        <common-dialog-delete :data="contact_id" @confirm_del="confirm_del" v-if="dialog_delete"></common-dialog-delete>
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

.contact-new {
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
                // {
                //     text: "NOMOR",
                //     align: "left",
                //     sortable: false,
                //     width: "8%",
                //     class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                // },
                {
                    text: "NAMA",
                    align: "left",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "ALAMAT",
                    align: "center",
                    sortable: false,
                    width: "27%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "KOTA",
                    align: "center",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "PROPINSI",
                    align: "center",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                // {
                //     text: "LEVEL",
                //     align: "center",
                //     sortable: false,
                //     width: "10%",
                //     class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                // },
                {
                    text: "REFERRER",
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
            ]
        }
    },

    computed : {
        contacts () {
            return this.$store.state.contact.contacts
        },

        dialog_delete () {
            return this.$store.state.dialog_delete
        },

        contact_id () {
            return this.$store.state.contact.selected_contact.Crm_ContactID
        },

        curr_page : {
            get () { return this.$store.state.contact.current_page },
            set (v) { this.$store.commit('contact/update_current_page', v) }
        },

        xtotal_page () {
            return this.$store.state.contact.total_contact_page
        },

        query : {
            get () { return this.$store.state.contact.search },
            set (v) { this.$store.commit('contact/update_search', v) }
        },

        provinces () {
            return this.$store.state.contact.provinces
        },

        selected_province : {
            get () { return this.$store.state.contact.selected_province },
            set (v) { 
                this.$store.commit('contact/set_selected_province', v)
                this.$store.dispatch('contact/search_city', {})
                // this.$store.dispatch('contact/search', {})
            }
        },

        selected_city : {
            get () { return this.$store.state.contact.selected_city },
            set (v) { 
                this.$store.commit('contact/set_selected_city', v)
                // this.$store.dispatch('contact/search', {})
            }
        },

        cities () {
            return this.$store.state.contact.cities
        }
    },

    methods : {
        add () {
            this.$store.commit('contact_new/set_common', ['edit', false])
            this.$store.commit('contact_new/set_common', ['contact_name', ''])
            this.$store.commit('contact_new/set_common', ['contact_address', ''])
            this.$store.commit('contact_new/set_common', ['contact_phone', ''])
            this.$store.commit('contact_new/set_common', ['contact_note', ''])
            this.$store.commit('contact_new/set_common', ['contact_email', ''])
            this.$store.commit('contact_new/set_common', ['contact_postcode', ''])
            this.$store.commit('contact_new/set_common', ['contact_auto', 'N'])
            this.$store.commit('contact_new/set_common', ['contact_join_date', this.$store.state.contact_new.current_date])
            this.$store.commit('contact_new/set_contact_mps', [])
            this.$store.commit('contact_new/set_common', ['user_contact', 'N'])
            this.$store.commit('contact_new/set_common', ['user_contact_username', ''])
            this.$store.commit('contact_new/set_common', ['user_contact_password', ''])
            this.$store.commit('contact_new/set_common', ['user_contact_password_confirm', ''])
            this.$store.commit('contact_new/set_selected_contact_level', null)
            this.$store.commit('contact_new/set_selected_province', null)
            this.$store.commit('contact_new/set_selected_city', null)
            this.$store.commit('contact_new/set_selected_district', null)
            this.$store.commit('contact_new/set_selected_village', null)
            this.$store.commit('contact_new/set_dialog_new', true)
        },

        edit (x) {
            this.select(x)
            let sc = x
            this.$store.commit('contact_new/set_common', ['edit', true])
            this.$store.commit('contact_new/set_common', ['contact_name', sc.Crm_ContactName])
            this.$store.commit('contact_new/set_common', ['contact_address', sc.Crm_ContactAddress])
            this.$store.commit('contact_new/set_common', ['contact_phone', sc.Crm_ContactPhone])
            this.$store.commit('contact_new/set_common', ['contact_note', sc.Crm_ContactNote])
            this.$store.commit('contact_new/set_common', ['contact_email', sc.Crm_ContactEmail])
            this.$store.commit('contact_new/set_common', ['contact_postcode', sc.Crm_ContactPostCode?sc.Crm_ContactPostCode:''])
            this.$store.commit('contact_new/set_common', ['contact_auto', sc.Crm_ContactAutoAccept?sc.Crm_ContactAutoAccept:'N'])
            this.$store.commit('contact_new/set_common', ['contact_join_date', sc.Crm_ContactJoinDate])
            this.$store.commit('contact_new/set_contact_mps', sc.contact_mps)

            // USER
            this.$store.commit('contact_new/set_common', ['change_password', false])
            this.$store.commit('contact_new/set_common', ['user_contact', 'N'])
            this.$store.commit('contact_new/set_common', ['user_contact_id', 0])
            this.$store.commit('contact_new/set_common', ['user_contact_username', ''])
            this.$store.commit('contact_new/set_common', ['user_contact_password', ''])
            this.$store.commit('contact_new/set_common', ['user_contact_password_confirm', ''])
            if (sc.user_contact_id) {
                this.$store.commit('contact_new/set_common', ['user_contact', 'Y'])
                this.$store.commit('contact_new/set_common', ['user_contact_id', sc.user_contact_id])
                this.$store.commit('contact_new/set_common', ['user_contact_username', sc.user_contact_username])
            }

            // Customer Level
            for (let cl of this.$store.state.contact_new.contact_levels)
                if (cl.Crm_ContactLevelID == x.Crm_ContactCrm_ContactLevelID)
                    this.$store.commit('contact_new/set_selected_contact_level', cl)

            // Province
            this.$store.dispatch('contact_new/search_province')

            // this.$store.commit('contact_new/set_common', ['search_city', sc.full_address])
            // this.$store.commit('contact_new/set_selected_city', {kelurahan_id:sc.kelurahan_id,full_address:sc.full_address})
            this.$store.commit('contact_new/set_dialog_new', true)
        },

        del (x) {
            this.select(x)
            this.$store.commit('set_dialog_delete', true)
        },

        confirm_del (x) {
            this.$store.dispatch('contact/del', {id:x.data})
        },

        select (x) {
            this.$store.commit('contact/set_selected_contact', x)
        },

        change_page(x) {
            this.curr_page = x
            this.$store.dispatch('contact/search', {})
        },

        search () {
            this.$store.dispatch('contact/search', {})
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
                    'level_id='+(this.selected_level?this.selected_level.Crm_ContactLevelID:0),
                    'token='+this.$store.state.contact.token].join('&')
            let urls = this.$store.state.contact.URL+'report/one_master_001'+
                        '?'+params
            this.$store.commit('contact/set_common', ['report_url', urls])
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
            this.$store.commit('contact_transfer/set_object', ['selected_user_to', null])
            this.$store.commit('contact_transfer/set_object', ['selected_user_from', null])
            this.$store.commit('contact_transfer/set_object', ['selected_transfer_type', null])
            this.$store.commit('contact_transfer/set_object', ['contacts', []])
            this.$store.commit('contact_transfer/set_object', ['transfers', []])
            this.$store.commit('contact_transfer/set_common', ['dialog_transfer', true])
        }
    },

    mounted () {
        this.$store.dispatch('contact/search_province', {})
        this.$store.dispatch('contact/search_tag')
    },

    watch : {
        selected_city (v, o) {
            if (v != o)
                this.$store.dispatch('contact/search', {})
        },

        selected_province (v, o) {
            if (v != o)
                if (this.$store.state.contact.selected_city != null)
                    this.$store.commit('contact/set_object', ['selected_city', null])
                else
                    this.$store.dispatch('contact/search', {})
        }
    }
}
</script>