<template>
    <v-card>
        <v-card-text class="pa-1">
            <v-card flat class="mb-3"><v-card-title primary-title class="pa-2 text-xs-center cyan lighten-3">
                <b>PERIODE PENJUALAN</b>
                </v-card-title>
                <v-card-text class="pa-2">
                    <v-layout row wrap>
                        <v-flex xs9 pr-2>
                            <v-select
                                :items="salesdurs" v-model="selected_salesdur"
                                return-object
                                item-value="salesdur_id"
                                item-text="salesdur_name"
                                label="Jangka Waktu Penjualan"
                                :disabled="!!custom_date"
                            ></v-select>
                        </v-flex>
                        <v-flex xs3>
                            <v-checkbox label="Custom" v-model="custom_date" :value="false"></v-checkbox>
                        </v-flex>
                    </v-layout>
                    

                    <v-layout row wrap v-show="!!custom_date" v-if="!!custom_date">
                        <v-flex xs6>
                            <common-datepicker
                                label="Dari Tanggal"
                                :date="custom_sdate"
                                data="0"
                                @change="change_custom_sdate"
                                classs="mt-0"
                                hints=" "
                                :details="false"
                                :solo="false"
                            ></common-datepicker>
                        </v-flex>

                        <v-flex xs6>
                            <common-datepicker
                                label="Sampai Tanggal"
                                :date="custom_edate"
                                data="0"
                                @change="change_custom_edate"
                                classs="mt-0 ml-2"
                                hints=""
                                :details="false"
                                :solo="false"
                            ></common-datepicker>
                        </v-flex>
                    </v-layout>
                </v-card-text>
            </v-card>
            <v-tabs
                v-model="tab"
                color="cyan lighten-3"
                grow
                >
                <v-tabs-slider color="yellow"></v-tabs-slider>

                <v-tab
                    v-for="item in tab_titles"
                    :key="item"
                >
                    <v-layout column>
                        <v-flex><b>{{ tab_title(item)[0] }}</b></v-flex>
                        <v-flex class="caption">{{ tab_title(item)[1] }}</v-flex>
                    </v-layout>
                </v-tab>
            </v-tabs>

            <v-tabs-items v-model="tab">
                <v-tab-item>
                    <v-card flat>
                        <v-card-text>
                            <v-select
                                :items="customer_types" v-model="selected_customer_type"
                                return-object
                                item-value="id"
                                item-text="text"
                                label="Tipe Customer"
                            ></v-select>

                            <v-layout row wrap>
                                <v-flex xs6 pr-1>
                                    <v-select
                                        :items="sales_types" v-model="selected_sales_type"
                                        return-object
                                        item-value="id"
                                        item-text="text"
                                        label="Status Penjualan"
                                    ></v-select>
                                </v-flex>
                                <v-flex xs6 pl-1>
                                    <v-select
                                        :items="leadsources"
                                        v-model="selected_leadsource"
                                        label="Sumber Lead / Penjualan"
                                        return-object
                                        item-value="leadsource_id"
                                        item-text="leadsource_name"
                                        clearable
                                    ></v-select>
                                </v-flex>
                            </v-layout>
                            
                        </v-card-text>
                    </v-card>
                </v-tab-item>
                <v-tab-item>
                    <v-card flat>
                        <v-card-text>
                            <v-layout row wrap>
                                <v-flex>
                                    <v-select
                                        :items="expeditions" v-model="selected_expedition"
                                        return-object
                                        item-value="M_ExpeditionID"
                                        item-text="M_ExpeditionName"
                                        label="Jasa Ekspedisi"
                                        clearable
                                        placeholder="Semua Ekspedisi"
                                        multiple
                                    ></v-select>
                                </v-flex>
                                <v-flex>
                                    <v-select
                                        :items="services" v-model="selected_service"
                                        return-object
                                        item-value="service"
                                        item-text="service"
                                        label="Layanan"
                                        clearable
                                        placeholder="Semua Layanan"
                                        multiple
                                        :disabled="!selected_expedition||(!!selected_expedition&&selected_expedition.length!=1)"
                                    ></v-select>
                                </v-flex>
                            </v-layout>

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
                </v-tab-item>
                <v-tab-item>
                    <v-card flat>
                        <v-card-text>
                            <v-layout row wrap>
                                <v-flex xs12>
                                    <v-select
                                        :items="items"
                                        v-model="selected_item"
                                        label="Semua Produk"
                                        item-text="M_ItemName"
                                        item-value="M_ItemID"
                                        return-object
                                        multiple
                                    >
                                        <template
                                            slot="selection"
                                            slot-scope="data"
                                            >
                                            <!-- <v-flex class="pa-1 cyan mr-1 mb-1"> -->
                                                <span v-show="data.index===0">Item dipilih ({{selected_item.length}})</span>
                                                <!-- {{ item.M_ItemName }} -->
                                            <!-- </v-flex> -->
                                        </template>
                                    </v-select>
                                </v-flex>

                                <v-flex xs12>
                                    <v-select
                                        :items="packets"
                                        v-model="selected_packet"
                                        label="Semua Paket"
                                        item-text="M_PacketName"
                                        item-value="M_PacketID"
                                        return-object
                                        multiple
                                    >
                                        <template
                                            slot="selection"
                                            slot-scope="data"
                                            >
                                                <span v-show="data.index===0">Paket dipilih ({{selected_packet.length}})</span>
                                        </template>
                                    </v-select>
                                </v-flex>

                                <v-flex xs12>
                                    <v-layout row wrap>
                                        <v-flex v-for="(item, n) in selected_item" :key="n" class="px-2 py-1 cyan lighten-3 mr-1 mb-1 rounded">
                                            <v-layout row>
                                                <v-flex class="d-flex align-center">
                                                    {{ item.M_ItemName }}         
                                                </v-flex>
                                                <v-spacer></v-spacer>    
                                                <v-flex class="btn-item-delete">
                                                    <v-btn flat class="btn-icon ma-0 pa-0" small @click="del_item(n)"><v-icon>close</v-icon></v-btn>
                                                </v-flex>
                                            </v-layout>
                                        </v-flex>

                                        <v-flex v-for="(packet, n) in selected_packet" :key="n" class="px-2 py-1 blue lighten-3 mr-1 mb-1 rounded">
                                            <v-layout row>
                                                <v-flex class="d-flex align-center">
                                                    {{ packet.M_PacketName }}         
                                                </v-flex>
                                                <v-spacer></v-spacer>    
                                                <v-flex class="btn-item-delete">
                                                    <v-btn flat class="btn-icon ma-0 pa-0" small @click="del_packet(n)"><v-icon>close</v-icon></v-btn>
                                                </v-flex>
                                            </v-layout>
                                        </v-flex>
                                    </v-layout>
                                </v-flex>

                                <!-- <v-flex xs6 v-for="(item, i) in items">
                                    {{ item.M_ItemName }}
                                </v-flex> -->
                            </v-layout>
                        </v-card-text>
                    </v-card>

                    
                </v-tab-item>
            <!-- <v-tab-item
                v-for="item in items"
                :key="item"
            >
                <v-card flat>
                <v-card-text>{{tab}} - {{ text }}</v-card-text>
                </v-card>
            </v-tab-item> -->
            </v-tabs-items>

            

            <v-layout row wrap>
                <v-flex xs6 pr-2>
                    <v-btn color="primary lighten-2" block @click="save">
                    <v-icon small class="mr-2">bookmark</v-icon>
                    S I M P A N</v-btn>
                    
                </v-flex>
                <v-flex xs6>
                    <v-btn color="success" block @click="search" dark>
                    <v-icon small class="mr-2">search</v-icon>
                    C A R I</v-btn>
                </v-flex>
            </v-layout>
            
        </v-card-text>
    </v-card>
</template>
<style scoped>
.rounded {
    border-radius: 10px;
}
.btn-item-delete {
    flex-grow: 0;
}
</style>
<script>
module.exports = {
    components : {
        "common-dialog-delete" : httpVueLoader("../../common/components/common-dialog-delete.vue"),
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
        customer_types() {
            return this.$store.state.post_sales.customer_types
        },

        selected_customer_type: {
            get () { return this.$store.state.post_sales.selected_customer_type },
            set (v) { this.$store.commit('post_sales/set_object', ['selected_customer_type', v]) }
        },

        sales_types() {
            return this.$store.state.post_sales.sales_types
        },

        selected_sales_type: {
            get () { return this.$store.state.post_sales.selected_sales_type },
            set (v) { this.$store.commit('post_sales/set_object', ['selected_sales_type', v]) }
        },

        salesdurs() {
            return this.$store.state.post_sales.salesdurs
        },

        selected_salesdur: {
            get () { return this.$store.state.post_sales.selected_salesdur },
            set (v) { this.$store.commit('post_sales/set_object', ['selected_salesdur', v]) }
        },

        expeditions() {
            return this.$store.state.post_sales.expeditions
        },

        selected_expedition: {
            get () { return this.$store.state.post_sales.selected_expedition },
            set (v) { 
                this.$store.commit('post_sales/set_object', ['selected_expedition', v]) 
                
                this.selected_service = null
                if (!!v && v.length == 1) {
                    this.$store.dispatch("post_sales/search_service")
                }
            }
        },

        services() {
            return this.$store.state.post_sales.services
        },

        selected_service: {
            get () { return this.$store.state.post_sales.selected_service },
            set (v) { this.$store.commit('post_sales/set_object', ['selected_service', v]) }
        },

        provinces() {
            return this.$store.state.post_sales.provinces
        },

        selected_province: {
            get () { return this.$store.state.post_sales.selected_province },
            set (v) { 
                this.$store.commit('post_sales/set_object', ['selected_province', v])

                this.selected_city = null
                this.$store.commit('post_sales/set_object', ['cities', []])
                if (!!v && v.length == 1) {
                    this.$store.dispatch('post_sales/search_city')
                }
            }
        },

        cities() {
            return this.$store.state.post_sales.cities
        },

        selected_city: {
            get () { return this.$store.state.post_sales.selected_city },
            set (v) { this.$store.commit('post_sales/set_object', ['selected_city', v]) }
        },

        custom_date : {
            get () { return this.$store.state.post_sales.custom_date },
            set (v) { this.$store.commit('post_sales/set_object', ['custom_date', v]) }
        },

        custom_edate : {
            get () { return this.$store.state.post_sales.custom_edate },
            set (v) { this.$store.commit('post_sales/set_common', ['custom_edate', v]) }
        },

        custom_sdate : {
            get () { return this.$store.state.post_sales.custom_sdate },
            set (v) { this.$store.commit('post_sales/set_common', ['custom_sdate', v]) }
        },

        items() {
            return this.$store.state.post_sales.items
        },

        selected_item : {
            get () { return this.$store.state.post_sales.selected_item },
            set (v) { this.$store.commit('post_sales/set_object', ['selected_item', v]) }
        },

        packets() {
            return this.$store.state.post_sales.packets
        },

        selected_packet : {
            get () { return this.$store.state.post_sales.selected_packet },
            set (v) { this.$store.commit('post_sales/set_object', ['selected_packet', v]) }
        },

        leadsources () {
            return this.$store.state.post_sales.leadsources
        },

        selected_leadsource : {
            get () { return this.$store.state.post_sales.selected_leadsource },
            set (v) { this.$store.commit('post_sales/set_object', ['selected_leadsource', v]) }
        }
    },

    methods: {
        search () {
            this.$store.dispatch('post_sales/search')
        },

        change_custom_edate(x) {
            this.custom_edate = x.new_date
        },

        change_custom_sdate(x) {
            this.custom_sdate = x.new_date
        },

        tab_title(x) {
            let e = x.split('|')            
            if (!!e[1]) e.push("")
            return e
        },

        del_item(idx) {
            let x = this.selected_item
            x.splice(idx, 1)
            
            this.selected_item = x
        },

        del_packet(idx) {
            let x = this.selected_packet
            x.splice(idx, 1)
            
            this.selected_packet = x
        },

        save () {
            let step = 0
            if (!this.$store.state.post_sales.selected_profile)
                step = 1
            this.$store.commit("post_sales/set_common", ["save_step", step])
            this.$store.commit("post_sales/set_common", ["save_to", 0])

            if (this.$store.state.post_sales.profile_id == 0)
                this.$store.commit("post_sales/set_common", ["profile_name", ""])
            this.$store.dispatch("post_sales/collect", true).then(res => {
                this.$store.commit("post_sales/set_object", ["profile_json", JSON.stringify(res)])
                this.$store.commit("post_sales/set_common", ["dialog_new", true])
            })
            
            
        }
    },

    mounted () {
        this.selected_customer_type = this.customer_types[0]
        this.selected_sales_type = this.sales_types[0]

        this.$store.dispatch('post_sales/search_salesdur')
        this.$store.dispatch('post_sales/search_expedition')
        this.$store.dispatch('post_sales/search_province')
        this.$store.dispatch('post_sales/search_item')
        this.$store.dispatch('post_sales/search_packet')
        this.$store.dispatch('post_sales/search_leadsource')
    }
}
</script>