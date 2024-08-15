<template>
    <v-dialog
        v-model="dialog" 
        max-width="300px"
        transition="dialog-transition"
    >
        <v-card>
            <v-card-title primary-title class="green white--text pt-2 pb-1">
                <h3 class="headline">{{ selected_report.report_name }}</h3>    
            </v-card-title>

            <v-card-text>
                <!-- <v-layout row wrap mb-3>
                    <v-flex xs12>
                        <v-autocomplete
                            label="Propinsi"
                            v-model="selected_province"
                            :items="provinces"
                            auto-select-first
                            return-object
                            clearable
                            item-text="M_ProvinceName"
                            item-value="M_ProvinceID"
                            placeholder="Pilih Propinsi"
                            hint="Kosongkan untuk memilih semua propinsi"
                            persistent-hint
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
                </v-layout>

                <v-layout row wrap mb-3>
                    <v-flex xs12>
                        <v-autocomplete
                            label="Kota"
                            v-model="selected_city"
                            :items="cities"
                            auto-select-first
                            return-object
                            clearable
                            item-text="M_CityName"
                            item-value="M_CityID"
                            placeholder="Pilih Kota"
                            :disabled="selected_province == null"
                            hint="Kosongkan untuk memilih semua kota"
                            persistent-hint
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
                </v-layout> -->

                <v-layout row wrap mb-3>
                    <v-flex xs12 mb-3>
                        <v-autocomplete
                            label="Semua Kursus"
                            v-model="selected_item"
                            :items="items"
                            auto-select-first
                            clearable
                            item-text="M_ItemName"
                            item-value="M_ItemID"
                            placeholder="Pilih Kursus"
                            hint="Kosongkan untuk memilih semua kursus"
                            persistent-hint
                            :readonly="!!selected_item"
                            >
                            <template
                                slot="item"
                                slot-scope="{ item }"
                                >
                                <v-list-tile-content>
                                <v-list-tile-title v-text="item.M_ItemName"></v-list-tile-title>
                                </v-list-tile-content>
                            </template>

                        </v-autocomplete>
                    </v-flex>

                    <v-flex xs12>
                        <v-autocomplete
                            label="Semua Kelas"
                            v-model="selected_customer_level"
                            :items="customer_levels"
                            auto-select-first
                            return-object
                            clearable
                            item-text="M_CustomerLevelName"
                            item-value="M_CustomerLevelID"
                            placeholder="Pilih Kelas"
                            hint="Kosongkan untuk memilih semua kelas"
                            persistent-hint
                            >
                            <template
                                slot="item"
                                slot-scope="{ item }"
                                >
                                <v-list-tile-content>
                                <v-list-tile-title v-text="item.M_CustomerLevelName"></v-list-tile-title>
                                </v-list-tile-content>
                            </template>

                        </v-autocomplete>
                    </v-flex>
                </v-layout>

                
            </v-card-text>

            <v-card-actions>
                <v-btn color="success" @click="dialog=!dialog" flat class="btn-icon">Tutup</v-btn>
                <v-spacer></v-spacer>
                <v-btn color="success" @click="generate('csv')" class="btn-icon" title="Download CSV">CSV</v-btn>
                <v-btn color="orange" dark @click="generate" class="btn-icon" title="Cetak PDF"><v-icon class="mr-1">print</v-icon> PDF</v-btn>
            </v-card-actions>
        </v-card>
        <!-- <common-dialog-print :report_url="report_url" v-if="dialog_report"></common-dialog-print> -->
    </v-dialog>
</template>

<script>
module.exports = {
    components : {
        'common-datepicker': httpVueLoader('./../../common/components/common-datepicker.vue'),
        // "common-dialog-print" : httpVueLoader("./../../common/components/common-dialog-print.vue")
    },

    computed : {
        ...Vuex.mapState('report_param', ['items']),
        dialog : {
            get () { return this.$store.state.report_param.dialog['master-001'] },
            set (v) { this.$store.commit('report_param/set_dialog', ['master-001', v]) }
        },

        report_url () {
            return this.$store.state.report_param.report_url
        },

        params () {
            return ['province_id='+(this.selected_province?this.selected_province.M_ProvinceID:0), 
                    'city_id='+(this.selected_city?this.selected_city.M_CityID:0),
                    'level_id='+(this.selected_customer_level?this.selected_customer_level.M_CustomerLevelID:0),
                    'item_id='+(this.selected_item??0),
                    'token='+this.$store.state.report_param.token].join('&')
        },

        edate : {
            get () { return this.$store.state.report_param.edate },
            set (v) { this.$store.commit('report_param/set_common', ['edate', v]) }
        },

        sdate : {
            get () { return this.$store.state.report_param.sdate },
            set (v) { this.$store.commit('report_param/set_common', ['sdate', v]) }
        },

        selected_report () {
            return this.$store.state.report.selected_report
        },

        admins () {
            return this.$store.state.report_param.admins
        },

        selected_admin : {
            get () { return this.$store.state.report_param.selected_admin },
            set (v) { this.$store.commit('report_param/set_selected_admin', v) }
        },

        selected_city : {
            get () { return this.$store.state.report_param.selected_city },
            set (v) { 
                this.$store.commit('report_param/set_selected_city', v)
            }
        },

        cities () {
            return this.$store.state.report_param.cities
        },

        provinces () {
            return this.$store.state.report_param.provinces
        },

        selected_province : {
            get () { return this.$store.state.report_param.selected_province },
            set (v) { 
                this.$store.commit('report_param/set_selected_province', v)
                this.$store.dispatch('report_param/search_city')
            }
        },

        customer_levels () {
            return this.$store.state.report_param.customer_levels
        },

        selected_customer_level : {
            get () { return this.$store.state.report_param.selected_customer_level },
            set (v) { 
                this.$store.commit('report_param/set_selected_customer_level', v)
            }
        },

        selected_item : {
            get () { return this.$store.state.report_param.selected_item },
            set (v) { 
                this.$store.commit('report_param/set_selected_item', v)
            }
        }
    },

    methods : {
        generate (type) {
            let urls = this.$store.state.report.URL+'report/'+this.selected_report.report_url+
                        '?'+this.params
            if (!!type)
                urls += '&type=' + type

            this.$store.commit('report_param/set_common', ['report_url', urls])
            this.$store.commit('set_dialog_print', true)

            this.dialog=!this.dialog
        },

        generate_csv () {

        },

        change_edate(x) {
            this.edate = x.new_date
        },

        change_sdate(x) {
            this.sdate = x.new_date
        }
    },

    mounted () {
        this.$store.dispatch('report_param/search_province')
        this.$store.dispatch('report_param/search_customer_level')
        this.$store.dispatch('report_param/search_item')
    }
}
</script>