<template>
    <v-card>
        <v-card-title primary-title class="pb-0">
            <v-layout row wrap>
                <v-flex xs12 sm8 md6 offset-xs0 offset-sm2 offset-md0 mb-2>
                    <h3 class="display-1 zalfa-text-pink font-weight-light"
                        :class="{'text-xs-center':$vuetify.breakpoint.smAndDown}">ANALISA OMZET PER WILAYAH</h3>
                </v-flex>

                <v-flex xs2 class="hidden-xs-only hidden-md-and-up">&nbsp;</v-flex>
                <v-flex xs1 class="hidden-xs-only hidden-md-and-up">&nbsp;</v-flex>
                
                <v-flex xs12 sm3 md2 :class="{'pl-5':$vuetify.breakpoint.smAndUp, 'mb-2':$vuetify.breakpoint.xsOnly}">
                    <common-datepicker
                        label="Dari Tanggal"
                        :date="sdate"
                        data="0"
                        @change="change_sdate"
                        classs="mt-0"
                        
                        hints=""
                        :details="false"
                    ></common-datepicker>
                </v-flex>

                <v-flex xs12 sm3 md2 :class="{'pl-1 pr-5':$vuetify.breakpoint.smAndUp, 'mb-2':$vuetify.breakpoint.xsOnly}">
                    <common-datepicker
                        label="Sampai Tanggal"
                        :date="edate"
                        data="0"
                        @change="change_edate"
                        classs="mt-0"
                        hints=""
                        :details="false"
                    ></common-datepicker>
                </v-flex>

                <v-flex xs12 sm4 md2>
                    <v-select
                        :items="types"
                        v-model="selected_type"
                        return-object
                        item-text="text"
                        item-value="value"
                        label="Tipe Data"
                        solo
                        hide-details
                    >
                        <template slot="append-outer">
                            <v-btn color="primary" class="btn-icon ma-0" @click="search"><v-icon>search</v-icon></v-btn>
                        </template>
                    </v-select>
                </v-flex>

                <v-flex xs12>
                    <v-divider class="my-0"></v-divider>
                </v-flex>
            </v-layout>
        </v-card-title>
        <v-card-text>
            <v-layout row wrap>
                <!-- <v-flex xs12 sm6 md4 offset-xs0 offset-0 offset-md2 :class="{'pr-2':$vuetify.breakpoint.smAndUp}">
                    A
                </v-flex> -->

                <v-flex xs4 v-for="(d, m) in omzet_divs" :key="m" :class="{'pl-4':m>0}">
                    <div v-for="(o, n) in d" v-bind:key="n">
                        <v-layout row wrap>
                            <v-flex xs9 class="caption" :class="{'font-weight-bold':is_selected(o)}">{{n+1}}. {{o.province_name}}</v-flex>
                            <v-flex xs3 class="caption text-xs-right" :class="{'font-weight-bold':is_selected(o)}">{{one_money(o.omzet)}}</v-flex>
                        </v-layout>
                        
                        <v-progress-linear
                            :color="is_selected(o)?'green':'error'"
                            :height="is_selected(o)?15:15"
                            :value="o.scale"
                            class="mt-0 mb-2"
                            @click="select(o)"
                        ></v-progress-linear>
                    </div>
                </v-flex>
                
                <v-flex xs12 sm12 md12 :class="{'pl-2':false}">
                    <v-data-table 
                        :headers="headers1"
                        :items="omzet_cities"
                        :loading="false"
                        hide-actions
                        class="elevation-1">
                        <template slot="items" slot-scope="props">
                            <td class="text-xs-left pa-2" @click="select(props.item)"><b>{{ props.item.city_name }}</b></td>
                            <!-- <td class="text-xs-left pa-2" @click="select(props.item)">
                                <div v-for="(c, n) in props.item.omzet_cities" :key="n"><span v-if="n>99">, </span>{{n+1}}. {{c.city_name}} <span class="blue--text">(Rp {{one_money(c.sub_total)}} / {{one_money(c.item_qty)}})</span></div>
                            </td>
                            <td class="text-xs-left pa-2" @click="select(props.item)">
                                <span v-for="(c, n) in props.item.omzet_products" :key="n"><span v-if="n>0">, </span>{{c.item_name}} <span class="blue--text">(Rp {{one_money(c.sub_total)}} / {{one_money(c.item_qty)}})</span></span>
                            </td> -->
                            <td class="text-xs-left pa-2" @click="select(props.item)">
                                <span v-for="(c, n) in paretos[props.item.city_id]" :key="n"><span v-if="n>0">, </span>{{c.item_name}} <span class="blue--text">(Rp {{one_money(c.sub_total)}} / {{one_money(c.item_qty)}})</span></span>
                                
                            </td>
                            <td class="text-xs-right pa-2" @click="select(props.item)">Rp <b>{{ one_money(props.item.sub_total) }}</b></td>
                        </template>
                    </v-data-table>

                    <!-- <v-data-table 
                        :headers="headers"
                        :items="omzet_provinces"
                        :loading="false"
                        hide-actions
                        class="elevation-1">
                        <template slot="items" slot-scope="props">
                            <td class="text-xs-left pa-2" @click="select(props.item)"><b>{{ props.item.province_name }}</b></td>
                            <td class="text-xs-left pa-2" @click="select(props.item)">
                                <div v-for="(c, n) in props.item.omzet_cities" :key="n"><span v-if="n>99">, </span>{{n+1}}. {{c.city_name}} <span class="blue--text">(Rp {{one_money(c.sub_total)}} / {{one_money(c.item_qty)}})</span></div>
                            </td>
                            <td class="text-xs-left pa-2" @click="select(props.item)">
                                <span v-for="(c, n) in props.item.omzet_products" :key="n"><span v-if="n>0">, </span>{{c.item_name}} <span class="blue--text">(Rp {{one_money(c.sub_total)}} / {{one_money(c.item_qty)}})</span></span>
                            </td>
                            
                            <td class="text-xs-right pa-2" @click="select(props.item)">Rp <b>{{ one_money(props.item.omzet) }}</b></td>
                        </template>
                    </v-data-table> -->
                </v-flex>
            </v-layout>
        </v-card-text>
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
        'common-datepicker' : httpVueLoader('../../common/components/common-datepicker.vue')
    },

    data () {
        return {
            headers: [
                {
                    text: "PROPINSI",
                    align: "left",
                    width: "15%",
                    sortable:false,
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "DETAIL PER KOTA",
                    align: "left",
                    width: "35%",
                    sortable:false,
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "DETAIL PER PRODUCT",
                    align: "left",
                    width: "35%",
                    sortable:false,
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                // {
                //     text: "TOTAL QTY",
                //     align: "right",
                //     sortable: false,
                //     width: "10%",
                //     class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                // },
                {
                    text: "TOTAL OMZET",
                    align: "right",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ],

            headers1: [
                {
                    text: "KOTA",
                    align: "left",
                    width: "15%",
                    sortable:false,
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "PRODUK PALING DIGEMARI",
                    align: "left",
                    width: "70%",
                    sortable:false,
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "TOTAL OMZET",
                    align: "right",
                    width: "15%",
                    sortable:false,
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ]
        }
    },

    computed :{
        omzet_provinces () {
            return this.$store.state.omzet_province.omzet_provinces
        },

        omzet_high () {
            return this.$store.state.omzet_province.omzet_high
        },
        
        omzet_customers () {
            return this.$store.state.omzet_province.omzet_customers
        },

        customer_high () {
            return this.$store.state.omzet_province.customer_high
        },

        sdate () {
            return this.$store.state.omzet_province.sdate
        },

        edate () {
            return this.$store.state.omzet_province.edate
        },

        types () {
            return this.$store.state.omzet_province.types
        },

        selected_type : {
            get () { return this.$store.state.omzet_province.selected_type },
            set (v) { this.$store.commit('omzet_province/set_selected_type', v) }
        },

        selected_province () {
            return this.$store.state.omzet_province.selected_omzet_province
        },

        selected_customer () {
            return this.$store.state.omzet_province.selected_omzet_customer
        },

        omzet_divs () {
            let v = this.$store.state.omzet_province.omzet_provinces
            let x = this.$store.state.omzet_province.omzet_length
            let y = []
            let z = []
            let k = 0;
            for (let i = 0; i < 3; i++) {
                z[i] = []
                if (x > 0) {
                    y[i] = Math.ceil(x/(3-i))
                    x -= y[i]
                } else y[i] = 0

                for (let j = k; j < k+y[i]; j++)
                    z[i].push(v[j])
                k += y[i]
            }
            return z
        },

        omzet_cities () {
            if (!this.selected_province)
                return []
            return this.selected_province.omzet_cities
        },

        paretos () {
            return this.$store.state.omzet_province.paretos
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        },

        change_sdate (x) {
            this.$store.commit('omzet_province/set_common', ['sdate', x.new_date])
        },

        change_edate (x) {
            this.$store.commit('omzet_province/set_common', ['edate', x.new_date])
        },

        search () {
            this.$store.dispatch('omzet_province/search')
            this.$store.dispatch('omzet_province/search_qty')
        },

        is_selected (x) {
            if (!this.selected_province) return false
            if (this.selected_province.province_name == x.province_name)
                return true
            return false
        },

        is_selected_c (x) {
            if (!this.selected_customer) return false
            if (this.selected_customer.customer_name == x.customer_name)
                return true
            return false
        },

        select (x) {
            this.$store.commit('omzet_province/set_selected_omzet_province', x)
            this.$store.dispatch('omzet_province/search_pareto')
        },

        selectc (x) {
            this.select(x)
            this.$store.commit('omzet_province/set_selected_omzet_customer', x)
        }
    }
}
</script>