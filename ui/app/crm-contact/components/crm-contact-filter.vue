<template>
    <v-card>
        <v-card-text>
            <v-layout row wrap>
                <v-flex>
                    <v-autocomplete
                        :items="provinces" v-model="selected_province"
                        return-object
                        item-value="M_ProvinceID"
                        item-text="M_ProvinceName"
                        label="Area Propinsi"
                        clearable
                        placeholder="Semua Propinsi"
                        multiple
                    ></v-autocomplete>
                </v-flex>

                <v-flex>
                    <v-autocomplete
                        :items="cities" v-model="selected_city"
                        return-object
                        item-value="M_CityID"
                        item-text="M_CityName"
                        label="Area Kota"
                        clearable
                        placeholder="Semua Kota"
                        multiple
                        :disabled="!selected_province||(!!selected_province&&selected_province.length>1)"
                    ></v-autocomplete>
                </v-flex>
            </v-layout>
        </v-card-text>
    </v-card>
</template>

<script>
module.exports = {
    components : {
        'common-datepicker' : httpVueLoader('../../common/components/common-datepicker.vue')
    },

    data () {
        return {
            tab: null,
            tab_titles: [
                'Customer|& Sales', 'Ekspedisi|& Area', 'Produk'
            ],
            text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.'
        }
    },

    computed : {
        provinces() {
            return this.$store.state.contact.provinces
        },

        selected_province: {
            get () { return this.$store.state.contact.selected_province },
            set (v) { 
                this.$store.commit('contact/set_object', ['selected_province', v])

                this.selected_city = null
                this.$store.commit('contact/set_object', ['cities', []])
                if (!!v && v.length == 1) {
                    this.$store.dispatch('contact/search_city')
                }
            }
        },

        cities() {
            return this.$store.state.contact.cities
        },

        selected_city: {
            get () { return this.$store.state.contact.selected_city },
            set (v) { this.$store.commit('contact/set_object', ['selected_city', v]) }
        }
    }
}