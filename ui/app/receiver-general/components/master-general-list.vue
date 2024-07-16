<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs4>
                    <h3 class="display-1 font-weight-light zalfa-text-title">PENERIMA MANFAAT UMUM</h3>
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
                            <v-btn color="primary" class="ma-0 btn-icon" @click="do_search">
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
                :items="generals"
                :loading="false"
                hide-actions
                class="elevation-1">
                <template slot="items" slot-scope="props">
                    <td class="text-xs-left pa-2" @click="select(props.item)"><b>{{ props.item.R_GeneralDate }}</b></td>
                    <td class="text-xs-left pa-2" @click="select(props.item)"><b>{{ props.item.R_GeneralName }}</b><br><v-icon small>smartphone</v-icon>{{props.item.R_GeneralPhone}}</td>
                    <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.R_GeneralAddress }}, {{ props.item.address_kelurahan }}</td>
                    <td class="text-xs-center pa-2" @click="select(props.item)">{{ props.item.M_CityName }}</td>
                    <td class="text-xs-center pa-2" @click="select(props.item)">{{ props.item.M_ProvinceName }}</td>
                    <td class="text-xs-center pa-2" @click="select(props.item)"><b>{{ props.item.R_GeneralAge }}<b/></td>
                    <td class="text-xs-center pa-2" @click="select(props.item)">
                        {{ props.item.R_GeneralJob }}
                    </td>
                    <td class="text-xs-center pa-0" @click="select(props.item)">
                        <v-btn color="primary" class="btn-icon ma-0" small @click="edit(props.item)"><v-icon>create</v-icon></v-btn>
                        <v-btn color="red" dark class="btn-icon ma-0" small @click="del(props.item)"><v-icon>delete</v-icon></v-btn>
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
        </v-card-text>
        
        <common-dialog-delete :data="general_id" @confirm_del="confirm_del" v-if="dialog_delete"></common-dialog-delete>
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
        "common-dialog-delete" : httpVueLoader("../../common/components/common-dialog-delete.vue")
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
                {
                    text: "USIA",
                    align: "center",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "PEKERJAAN",
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
        generals () {
            return this.$store.state.general.generals
        },

        dialog_delete () {
            return this.$store.state.dialog_delete
        },

        general_id () {
            return this.$store.state.general.selected_general.R_GeneralID
        },

        curr_page : {
            get () { return this.$store.state.general.current_page },
            set (v) { this.$store.commit('general/update_current_page', v) }
        },

        xtotal_page () {
            return this.$store.state.general.total_general_page
        },

        query : {
            get () { return this.$store.state.general.search },
            set (v) { this.$store.commit('general/update_search', v) }
        },

        provinces () {
            return this.$store.state.general.provinces
        },

        selected_province : {
            get () { return this.$store.state.general.selected_province },
            set (v) { 
                this.$store.commit('general/set_selected_province', v)
                this.$store.dispatch('general/search_city', {})
                // this.$store.dispatch('general/search', {})
            }
        },

        selected_city : {
            get () { return this.$store.state.general.selected_city },
            set (v) { 
                this.$store.commit('general/set_selected_city', v)
                // this.$store.dispatch('general/search', {})
            }
        },

        cities () {
            return this.$store.state.general.cities
        }
    },

    methods : {
        add () {
            this.$store.commit('general_new/set_common', ['edit', false])
            this.$store.commit('general_new/set_common', ['general_name', ''])
            this.$store.commit('general_new/set_common', ['general_address', ''])
            this.$store.commit('general_new/set_common', ['general_phone', ''])
            this.$store.commit('general_new/set_common', ['general_note', ''])
            this.$store.commit('general_new/set_common', ['general_email', ''])
            this.$store.commit('general_new/set_common', ['user_general', 'N'])
            this.$store.commit('general_new/set_common', ['user_general_username', ''])
            this.$store.commit('general_new/set_common', ['user_general_password', ''])
            this.$store.commit('general_new/set_common', ['user_general_password_confirm', ''])
            this.$store.commit('general_new/set_selected_general_level', null)
            this.$store.commit('general_new/set_selected_province', null)
            this.$store.commit('general_new/set_selected_city', null)
            this.$store.commit('general_new/set_selected_district', null)
            this.$store.commit('general_new/set_selected_village', null)
            this.$store.commit('general_new/set_dialog_new', true)
        },

        edit (x) {
            this.select(x)
            let sc = x
            this.$store.commit('general_new/set_common', ['edit', true])
            this.$store.commit('general_new/set_common', ['general_name', sc.R_GeneralName])
            this.$store.commit('general_new/set_common', ['general_address', sc.R_GeneralAddress])
            this.$store.commit('general_new/set_common', ['general_phone', sc.R_GeneralPhone])
            this.$store.commit('general_new/set_common', ['general_note', sc.R_GeneralNote])
            this.$store.commit('general_new/set_common', ['general_email', sc.R_GeneralEmail])

            // USER
            this.$store.commit('general_new/set_common', ['change_password', false])
            this.$store.commit('general_new/set_common', ['user_general', 'N'])
            this.$store.commit('general_new/set_common', ['user_general_id', 0])
            this.$store.commit('general_new/set_common', ['user_general_username', ''])
            this.$store.commit('general_new/set_common', ['user_general_password', ''])
            this.$store.commit('general_new/set_common', ['user_general_password_confirm', ''])
            if (sc.user_general_id) {
                this.$store.commit('general_new/set_common', ['user_general', 'Y'])
                this.$store.commit('general_new/set_common', ['user_general_id', sc.user_general_id])
                this.$store.commit('general_new/set_common', ['user_general_username', sc.user_general_username])
            }

            // general Level
            for (let cl of this.$store.state.general_new.general_levels)
                if (cl.R_GeneralLevelID == x.R_GeneralR_GeneralLevelID)
                    this.$store.commit('general_new/set_selected_general_level', cl)

            // Province
            this.$store.dispatch('general_new/search_province')

            // this.$store.commit('general_new/set_common', ['search_city', sc.full_address])
            // this.$store.commit('general_new/set_selected_city', {kelurahan_id:sc.kelurahan_id,full_address:sc.full_address})
            this.$store.commit('general_new/set_dialog_new', true)
        },

        del (x) {
            this.select(x)
            this.$store.commit('set_dialog_delete', true)
        },

        confirm_del (x) {
            this.$store.dispatch('general/del', {id:x.data})
        },

        select (x) {
            this.$store.commit('general/set_selected_general', x)
        },

        change_page(x) {
            this.curr_page = x
            this.$store.dispatch('general/search', {})
        },

        do_search(e) {
            if (e.which == 13)
                this.$store.dispatch('general/search', {})
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
        }
    },

    mounted () {
        this.$store.dispatch('general/search_province', {})
    },

    watch : {
        selected_city (v, o) {
            if (v != o)
                this.$store.dispatch('general/search', {})
        },

        selected_province (v, o) {
            if (v != o)
                if (this.$store.state.general.selected_city != null)
                    this.$store.commit('general/set_selected_city', null)
                else
                    this.$store.dispatch('general/search', {})
        }
    }
}
</script>