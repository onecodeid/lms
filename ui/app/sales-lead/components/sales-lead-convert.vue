<template>
    <v-dialog
        v-model="dialog"
        scrollable
        persistent
        max-width="400px"
        transition="dialog-transition"
        content-class="dialog-convert"
    >
        <v-card>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs6 v-for="(tab, n) in tabs" :key="n" @click="select_tab(tab)" class="tab-item pa-1 d-block"
                         >
                        <div class="px-2 py-3"
                        :class="!!is_selected_tab(tab)?'zalfa-bg-purple white--text active':'grey lighten-2'">{{tab.text}}</div>
                        
                    </v-flex>
                    <v-flex xs12 v-show="!!selected_tab&&selected_tab.id=='NEW'">
                        <v-card>
                            <v-card-title primary-title>
                                Customer Baru
                            </v-card-title>
                            <v-card-text>
                                <v-layout row wrap>
                                    <v-text-field
                                        label="Nama Prospek"
                                        placeholder="Tulis nama yang lengkap"
                                        v-model="lead_name"

                                        :clearable="true"
                                        :error="lead_name.length<1"
                                        :error-messages="lead_name.length<1?['Wajib diisi']:''"
                                        :error-count="lead_name.length<1?1:0"
                                    >
                                        <!-- <template slot="append">
                                            <v-btn color="btn-icon success" small @click="select_customer"><v-icon>person_pin</v-icon></v-btn>
                                        </template> -->
                                    </v-text-field>

                                    <v-text-field
                                        label="Telp / HP Prospek"
                                        placeholder="Tulis nomor HP tanpa awalan 0 / 62"
                                        v-model="lead_phone"

                                        :clearable="true"
                                        :error="lead_phone.length<1"
                                        :error-messages="lead_phone.length<1?['Wajib diisi']:''"
                                        :error-count="lead_phone.length<1?1:0"
                                        prefix="+62"
                                    >
                                    </v-text-field>

                                    <sales-lead-address></sales-lead-address>
                                </v-layout>
                            </v-card-text>
                        </v-card>
                    </v-flex>

                    <v-flex xs12 v-show="selected_tab.id=='EXISTING'">
                        <v-card>
                            <v-card-title primary-title>
                                Existing Customer
                            </v-card-title>
                            <v-card-text>
                                <v-layout row wrap>
                                    <v-flex xs12>
                                        <v-autocomplete
                                            label="Pilih Customer"
                                            v-model="selected_customer"
                                            :items="customers"
                                            :search-input.sync="search_customer"
                                            auto-select-first
                                            no-filter
                                            return-object
                                            :clearable="true"
                                            item-text="M_CustomerName"
                                            :loading="false"
                                            no-data-text="Pilih Customer"
                                            solo
                                            >
                                            <template
                                                slot="item"
                                                slot-scope="{ item }"
                                                >
                                                <v-list-tile-content>
                                                    <v-list-tile-title v-text="item.M_CustomerName"></v-list-tile-title>
                                                <v-list-tile-sub-title v-text="item.M_CustomerLevelName + ', ' + item.M_CityName"></v-list-tile-sub-title>
                                                </v-list-tile-content>
                                            </template>

                                        </v-autocomplete>
                                    </v-flex>
                                    <v-flex xs12 mt-2 v-if="!!selected_customer">
                                        {{selected_customer.M_CustomerAddress}}
                                        <div>{{selected_customer.full_address}}</div>
                                        <div><b>{{selected_customer.M_CustomerPhone}}</b></div>
                                    </v-flex>
                                </v-layout>
                            </v-card-text>
                        </v-card>
                    </v-flex>

                    <v-flex xs12>
                        <v-card>
                            <v-card-actions class="primary lighten-3">
                                <v-layout row wrap>
                                    <v-flex class="text-xs-right">
                                        <v-spacer></v-spacer>
                                        <v-btn color="red" dark @click="dialog=!dialog">Tutup</v-btn>
                                        <v-btn color="primary" @click="save" :disabled="!btn_save_enable">Simpan</v-btn>
                                    </v-flex>
                                </v-layout>
                            </v-card-actions>
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-card-text>
        </v-card>   
    </v-dialog>
</template>

<style>
.dialog-convert .tab-item > div {
    align-items: center;
    justify-content: center;
    display: flex;
    text-transform: uppercase;
}

.dialog-convert .tab-item.active > div {
    font-weight: bold;
}
</style>

<script>
module.exports = {
    components: {
        "sales-lead-address": httpVueLoader('./sales-lead-address.vue?1234'),
    },

    data() {
        return {
        }
    },

    computed: {
        dialog : {
            get () { return this.$store.state.saleslead_convert.dialog_convert },
            set (v) { this.$store.commit('saleslead_convert/set_common', ['dialog_convert', v]) }
        },

        lead_name: {
            get () { return this.$store.state.saleslead_convert.lead_name },
            set (v) { this.$store.commit('saleslead_convert/set_common', ['lead_name', v]) }
        },

        lead_phone: {
            get () { return this.$store.state.saleslead_convert.lead_phone },
            set (v) { this.$store.commit('saleslead_convert/set_common', ['lead_phone', v]) }
        },

        lead_address: {
            get () { return this.$store.state.saleslead_address.address },
            set (v) { this.$store.commit('saleslead_address/set_common', ['address', v]) }
        },

        cities () {
            return this.$store.state.saleslead_convert.cities
        },

        selected_city : {
            get () { return this.$store.state.saleslead_convert.selected_city },
            set (v) { this.$store.commit('saleslead_convert/set_selected_city', v) }
        },

        search_city : {
            get () { return this.$store.state.saleslead_convert.search_city },
            set (v) { this.$store.commit('saleslead_convert/set_common', ['search_city', v]) }
        },

        loading_city () {
            return this.$store.state.saleslead_convert.loading_city
        },

        customers () {
            return this.$store.state.saleslead_convert.customers
        },

        selected_customer : {
            get () { return this.$store.state.saleslead_convert.selected_customer },
            set (v) { this.$store.commit('saleslead_convert/set_selected_customer', v) }
        },

        search_customer : {
            get () { return this.$store.state.saleslead_convert.search_customer },
            set (v) { this.$store.commit('saleslead_convert/set_common', ['search_customer', v]) }
        },

        tabs () {
            return this.$store.state.saleslead_convert.tabs
        },

        selected_tab : {
            get () { return this.$store.state.saleslead_convert.selected_tab },
            set (v) { this.$store.commit('saleslead_convert/set_selected_tab', v) }
        },

        btn_save_enable () {
            return true
        }
    },

    methods : {
        thr_search: _.debounce( function () {
            this.$store.dispatch("saleslead_convert/search_city")
        }, 700),

        thr_search_2: _.debounce( function () {
            this.$store.dispatch("saleslead_convert/search_customer")
        }, 700),

        is_selected_tab (v) {
            if (!this.selected_tab) return false
            if (v.id == this.selected_tab.id)
                return true
            return false
        },

        select_tab (v) {
            this.selected_tab =  v    
        },

        save () {
            this.$store.dispatch('saleslead_convert/save')
        }
    },

    watch : {
        search_city(val, old) {
            if (val == null || typeof val == 'undefined') val = ""
            if (val == old ) return
            if (this.$store.state.saleslead_convert.search_status == 1 ) return  
            this.$store.commit("saleslead_convert/set_common", ['search_city', val])
            this.thr_search()
        },

        search_customer(val, old) {
            if (val == null || typeof val == 'undefined') val = ""
            if (val == old ) return
            if (this.$store.state.saleslead_convert.search_status == 1 ) return  
            this.$store.commit("saleslead_convert/set_common", ['search_customer', val])
            this.thr_search_2()
        }
    }
}
</script>