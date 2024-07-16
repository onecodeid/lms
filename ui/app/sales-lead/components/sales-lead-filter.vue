<template>
    <v-layout row wrap>
        <v-flex xs12>
            <v-text-field solo placeholder="Pencarian" v-model="query" @change="search">
                <template v-slot:append-outer>
                    <v-btn color="primary" class="ma-0 btn-icon" @click="search">
                        <v-icon>search</v-icon>
                    </v-btn>
                </template>
            </v-text-field>
        </v-flex>
        <v-flex xs12>
            <label class="grey--text">Tanggal Lead</label>
            <v-divider class="mb-2"></v-divider>
            <v-layout>
                <v-flex xs6>
                    <common-datepicker label="Dari Tanggal" :date="sdate" data="0" @change="change_sdate"
                                        classs="mr-1" hints=" " :details="false"></common-datepicker>
                </v-flex>

                <v-flex xs6>
                    <common-datepicker label="Sampai Tanggal" :date="edate" data="0" @change="change_edate"
                                        classs="ml-1" hints="" :details="false"></common-datepicker>
                </v-flex>
            </v-layout>
        </v-flex>
        

        <v-flex xs12 class="pt-4">
            <label class="grey--text">Status Closing</label>
            <v-divider class="mb-2"></v-divider>
            <v-select :items="fLeadCloses" item-value="id" item-text="text" label="Status Closing" v-model="selectedFLeadClose" solo clearable placeholder="Semua Status"></v-select>
        </v-flex>

        <v-flex xs12 class="pt-1">
            <label class="grey--text">Follow Up</label>
            <v-divider class="mb-2"></v-divider>
            <v-select :items="fLeadFollowups" item-value="id" item-text="text" multiple chips solo v-model="selectedFLeadFollowup" clearable></v-select>
            
        </v-flex>

        <v-flex xs12 class="pt-1">
            <label class="grey--text">Tipe Greeting</label>
            <v-divider class="mb-2"></v-divider>
            <v-select :items="leadGreetings" item-value="attr_id" item-text="attr_name"
                                        label="Tipe greeting" v-model="selectedFLeadGreeting" solo clearable chips></v-select>
        </v-flex>

        <v-flex xs12 class="pt-1">
            <label class="grey--text">Lead Source</label>
            <v-divider class="mb-2"></v-divider>
            <v-select :items="leadsources" item-value="leadsource_id"
                                                                item-text="leadsource_name"
                                        label="" v-model="selectedFLeadSource" solo clearable chips multiple></v-select>
        </v-flex>
    </v-layout>
</template>

<script>
module.exports = {
    components: {
        'common-datepicker': httpVueLoader('../../common/components/common-datepicker.vue'),
    },

    data() {
        return {
        }
    },

    computed : {
        ...Vuex.mapState('saleslead', ['fLeadCloses', 'fLeadFollowups']),
        ...Vuex.mapState('saleslead_new', ['leadGreetings', 'leadsources']),

        query: {
            get() { return this.$store.state.saleslead.query },
            set(v) { this.$store.commit('saleslead/set_common', ['query', v]) }
        },

        sdate: {
            get() { return this.$store.state.saleslead.sdate },
            set(v) { this.$store.commit('saleslead/set_sdate', v) }
        },

        edate: {
            get() { return this.$store.state.saleslead.edate },
            set(v) { this.$store.commit('saleslead/set_edate', v) }
        },

        selectedFLeadClose: {
            get() { return this.$store.state.saleslead.selectedFLeadClose },
            set(v) { this.$store.commit('saleslead/set_object', ['selectedFLeadClose', v]) }
        },

        selectedFLeadGreeting: {
            get() { return this.$store.state.saleslead.selectedFLeadGreeting },
            set(v) { this.$store.commit('saleslead/set_object', ['selectedFLeadGreeting', v]) }
        },

        selectedFLeadSource: {
            get() { return this.$store.state.saleslead.selectedFLeadSource },
            set(v) { this.$store.commit('saleslead/set_object', ['selectedFLeadSource', v]) }
        },

        selectedFLeadFollowup: {
            get() { return this.$store.state.saleslead.selectedFLeadFollowup },
            set(v) { this.$store.commit('saleslead/set_object', ['selectedFLeadFollowup', v]) }
        }
    },

    methods : {
        search() {
            this.$store.dispatch('saleslead/search', {})
        },

        change_sdate(x) {
            this.sdate = x.new_date
            this.search()
        },

        change_edate(x) {
            this.edate = x.new_date
            this.search()
        }
    }
}