<template>
    <v-card>
        <v-card-title primary-title class="pb-0">
            <v-layout row wrap>
                <v-flex xs12 sm8 md6 offset-xs0 offset-sm2 offset-md0 mb-2>
                    <h3 class="display-1 zalfa-text-pink font-weight-light"
                        :class="{'text-xs-center':$vuetify.breakpoint.smAndDown}">ANALISA OMZET PER PRODUK</h3>
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
                        
                        hints=" "
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
                <v-flex xs12 sm6 md4 offset-xs0 offset-0 offset-md2 :class="{'pr-2':$vuetify.breakpoint.smAndUp}">
                    <div><h3 class="headline text-xs-center mb-3">OMZET NOMINAL</h3></div>
                    <div v-for="(o, n) in omzets" v-bind:key="n">
                        <v-layout row wrap>
                            <v-flex xs9 class="caption">{{n+1}}. {{o.item_name}}</v-flex>
                            <v-flex xs3 class="caption text-xs-right">{{one_money(o.omzet)}}</v-flex>
                        </v-layout>
                        
                        <v-progress-linear
                            :color="is_selected(o)?'green':'error'"
                            :height="is_selected(o)?30:15"
                            :value="o.scale"
                            class="mt-0 mb-2"
                            @click="select(o)"
                        ></v-progress-linear>
                    </div>
                </v-flex>

                <v-flex xs12 sm6 md4 :class="{'pl-2':$vuetify.breakpoint.smAndUp}">
                    <div><h3 class="headline text-xs-center mb-3">OMZET QTY</h3></div>
                    <div v-for="(o, n) in qtys" v-bind:key="n">
                        <v-layout row wrap>
                            <v-flex xs9 class="caption">{{n+1}}. {{o.item_name}}</v-flex>
                            <v-flex xs3 class="caption text-xs-right">{{one_money(o.omzet)}}</v-flex>
                        </v-layout>
                        
                        <v-progress-linear
                            :color="is_selected(o)?'green':'orange'"
                            :height="is_selected(o)?30:15"
                            :value="o.scale"
                            class="mt-0 mb-2"
                            @click="select(o)"
                        ></v-progress-linear>
                    </div>
                </v-flex>
            </v-layout>

            <v-layout row wrap class="mt-2">
                <v-flex xs12>
                    <stat-omzet-per-product-area></stat-omzet-per-product-area>
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
        'common-datepicker' : httpVueLoader('../../common/components/common-datepicker.vue'),
        'stat-omzet-per-product-area' : httpVueLoader('./stat-omzet-per-product-area.vue')
    },

    computed :{
        omzets () {
            return this.$store.state.omzet_product.omzet_products
        },

        omzet_high () {
            return this.$store.state.omzet_product.omzet_high
        },
        
        qtys () {
            return this.$store.state.omzet_product.qty_products
        },

        qty_high () {
            return this.$store.state.omzet_product.qty_high
        },

        sdate () {
            return this.$store.state.omzet_product.sdate
        },

        edate () {
            return this.$store.state.omzet_product.edate
        },

        types () {
            return this.$store.state.omzet_product.types
        },

        selected_type : {
            get () { return this.$store.state.omzet_product.selected_type },
            set (v) { this.$store.commit('omzet_product/set_selected_type', v) }
        },

        selected_item () {
            return this.$store.state.omzet_product.selected_item
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        },

        change_sdate (x) {
            this.$store.commit('omzet_product/set_common', ['sdate', x.new_date])
        },

        change_edate (x) {
            this.$store.commit('omzet_product/set_common', ['edate', x.new_date])
        },

        search () {
            this.$store.dispatch('omzet_product/search')
            this.$store.dispatch('omzet_product/search_qty')
        },

        is_selected (x) {
            if (!this.selected_item) return false
            if (this.selected_item.item_name == x.item_name)
                return true
            return false
        },

        select (x) {
            this.$store.commit('omzet_product/set_selected_item', x)
            this.$store.dispatch('omzet_product/search_area')
        }
    }
}
</script>