<template>
    <v-layout row wrap>
        <v-flex xs12>
            <v-textarea
                label="Alamat Lengkap"
                placeholder="Kota, Kecamatan, Kelurahan RT RW"
                rows="2"
                v-model="lead_address"
                :readonly="false">
            </v-textarea>
        </v-flex>
        
        <v-flex xs12>
            <v-autocomplete
                v-model="selected_city"
                :items="cities"
                :search-input.sync="search_city"
                auto-select-first
                no-filter
                return-object
                :clearable="true"
                item-text="full_city_reverse"
                :loading="loading_city"
                no-data-text="Pilih Kota"
                label="Kota"
                placeholder="Ketikkan minimal 3 huruf"
                v-show="true"
                v-if="!selected_city"
                >
                <template
                    slot="item"
                    slot-scope="{ item }"
                    >
                    <v-list-tile-content>
                        <v-list-tile-title v-text="item.full_city_reverse"></v-list-tile-title>
                    </v-list-tile-content>
                </template>

            </v-autocomplete>

            <v-text-field
                label="Kota"
                :value="!!selected_city?selected_city.full_city_reverse:''"
                readonly
                v-show="!!selected_city"
                clearable
                @click:clear="selected_city=null"
            ></v-text-field>
        </v-flex>
        
        <v-flex xs12>
            <v-autocomplete
                label="Kecamatan"
                v-model="selected_district"
                :items="districts"
                auto-select-first
                return-object
                clearable
                item-text="M_DistrictName"
                item-value="M_DistrictID"
                placeholder="Pilih Kecamatan"
                :disabled="selected_city == null"
                >
                <template
                    slot="item"
                    slot-scope="{ item }"
                    >
                    <v-list-tile-content>
                    <v-list-tile-title v-text="item.M_DistrictName"></v-list-tile-title>
                    <!-- <v-list-tile-sub-title v-text="getAddress(item)"></v-list-tile-sub-title> -->
                    </v-list-tile-content>
                </template>
            </v-autocomplete>
        </v-flex>

        <v-flex xs12>
            <v-autocomplete
                label="Kelurahan"
                v-model="selected_village"
                :items="villages"
                auto-select-first
                return-object
                clearable
                item-text="M_KelurahanName"
                item-value="M_KelurahanID"
                placeholder="Pilih Kelurahan"
                :disabled="selected_district == null"
                >
                <template
                    slot="item"
                    slot-scope="{ item }"
                    >
                    <v-list-tile-content>
                    <v-list-tile-title v-text="item.M_KelurahanName"></v-list-tile-title>
                    <!-- <v-list-tile-sub-title v-text="getAddress(item)"></v-list-tile-sub-title> -->
                    </v-list-tile-content>
                </template>
            </v-autocomplete>
        </v-flex>
    </v-layout>
</template>

<script>
module.exports = {
    data() {
        return {
        }
    },

    computed: {
        lead_address: {
            get () { return this.$store.state.saleslead_address.address },
            set (v) { this.$store.commit('saleslead_address/set_common', ['address', v]) }
        },

        cities () {
            return this.$store.state.saleslead_address.cities
        },

        selected_city : {
            get () { return this.$store.state.saleslead_address.selected_city },
            set (v) { 
                this.$store.commit('saleslead_address/set_selected_city', v) 
                this.$store.dispatch('saleslead_address/search_district')
            }
        },

        search_city : {
            get () { return this.$store.state.saleslead_address.search_city },
            set (v) { this.$store.commit('saleslead_address/set_common', ['search_city', v]) }
        },

        loading_city () {
            return this.$store.state.saleslead_new.loading_city
        },

        districts () {
            return this.$store.state.saleslead_address.districts
        },

        selected_district : {
            get () { return this.$store.state.saleslead_address.selected_district },
            set (v) { 
                this.$store.commit('saleslead_address/set_selected_district', v)
                this.$store.dispatch('saleslead_address/search_village', {})
            }
        },

        villages () {
            return this.$store.state.saleslead_address.villages
        },

        selected_village : {
            get () { return this.$store.state.saleslead_address.selected_village },
            set (v) { 
                this.$store.commit('saleslead_address/set_selected_village', v)
            }
        }
    },

    methods : {
        thr_search: _.debounce( function () {
            this.$store.dispatch("saleslead_address/search_city")
        }, 700)
    },

    watch : {
        search_city(val, old) {
            if (val == null || typeof val == 'undefined') val = ""
            if (val == old ) return
            if (this.$store.state.saleslead_address.search_status == 1 ) return  
            this.$store.commit("saleslead_address/set_common", ['search_city', val])
            this.thr_search()
        },
    }
}
</script>