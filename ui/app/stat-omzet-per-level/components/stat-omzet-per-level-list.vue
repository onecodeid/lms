<template>
    <v-card>
        <v-card-title primary-title class="pb-0">
            <v-layout row wrap>
                <v-flex xs12 sm8 md7 offset-xs0 offset-sm2 offset-md0 mb-2>
                    <h3 class="display-1 zalfa-text-pink font-weight-light"
                        :class="{'text-xs-center':$vuetify.breakpoint.smAndDown}">ANALISA OMZET PER LEVEL CUSTOMER</h3>
                </v-flex>

                <v-flex xs2 class="hidden-xs-only hidden-md-and-up">&nbsp;</v-flex>
                <v-flex xs1 class="hidden-xs-only hidden-md-and-up">&nbsp;</v-flex>
                

                <v-flex xs12 sm6 md3>
                    <v-layout row wrap>
                        <v-flex xs12 sm6 md6 :class="{'pl-4':$vuetify.breakpoint.smAndUp, 'mb-2':true}">
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

                        <v-flex xs12 sm6 md6 :class="{'pl-1 pr-4':$vuetify.breakpoint.smAndUp, 'mb-2':true}">
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
                    </v-layout>
                    <v-layout row wrap>
                        <v-flex xs12 sm6 md6 :class="{'pl-4':$vuetify.breakpoint.smAndUp, 'mb-2':$vuetify.breakpoint.xsOnly}">
                            <v-text-field
                                label="Min Omzet"
                                solo
                                hide-details
                                reverse
                                suffix="Rp"
                                v-model="omzet_min"
                            ></v-text-field>
                        </v-flex>

                        <v-flex xs12 sm6 md6 :class="{'pl-1 pr-4':$vuetify.breakpoint.smAndUp, 'mb-2':$vuetify.breakpoint.xsOnly}">
                            <v-text-field
                                label="Max Omzet"
                                solo
                                hide-details
                                reverse
                                suffix="Rp"
                                v-model="omzet_max"
                            ></v-text-field>
                        </v-flex>
                    </v-layout>
                </v-flex>
                

                <!-- <v-flex xs12 sm4 md2 :class="{'pr-2':$vuetify.breakpoint.smAndUp}">
                    <v-select
                        :items="levels"
                        v-model="selected_levelx"
                        return-object
                        item-text="M_CustomerLevelName"
                        item-value="M_CustomerLevelID"
                        label="Jenjang Customer"
                        placeholder="SEMUA LEVEL"
                        solo
                        clearable
                        multiple
                    >
                    </v-select>
                </v-flex> -->

                <v-flex xs12 sm4 md2>
                    <v-select
                        :items="admins"
                        v-model="selected_admin"
                        label="Semua Admin"
                        hide-details
                        return-object
                        item-text="user_full_name"
                        item-id="user_id"
                        solo
                        clearable
                        class="mb-2"
                    ></v-select>
                    <v-select
                        :items="types"
                        v-model="selected_type"
                        return-object
                        item-text="text"
                        item-value="value"
                        label="Tipe Data"
                        solo
                    >
                        <template slot="append-outer">
                            <v-btn color="primary" class="btn-icon ma-0" @click="search"><v-icon>search</v-icon></v-btn>
                        </template>
                    </v-select>
                </v-flex>

                <v-flex xs12>
                    <v-divider class="my-2"></v-divider>
                </v-flex>
            </v-layout>
        </v-card-title>
        <v-card-text>
            <v-layout row wrap>
                <v-flex xs12 sm8 md8 offset-xs0 offset-sm2 offset-md2>
                    
                    <v-autocomplete
                        :items="levels"
                        v-model="selected_levelx"
                        return-object
                        item-text="M_CustomerLevelName"
                        item-value="M_CustomerLevelID"
                        label="Jenjang Customer"
                        placeholder="SEMUA LEVEL"
                        solo
                        clearable
                        multiple
                        chips
                        deletable-chips
                    >
                        <template slot="prepend-inner">
                            
                                <v-btn color="success" class="ma-0" depressed style="height:42px">Customer Level</v-btn>
                            
                        </template>
                    </v-autocomplete>
                
                </v-flex>
            </v-layout>
            <v-layout row wrap>
                <v-flex xs12 sm6 md4 offset-xs0 offset-0 offset-md2 :class="{'pr-2':$vuetify.breakpoint.smAndUp}">
                    <div><h3 class="headline text-xs-center mb-3">REKAPITULASI PER LEVEL</h3></div>
                    <div v-for="(o, n) in omzet_levels" v-bind:key="n">
                        <v-layout row wrap>
                            <v-flex xs9 class="caption">{{n+1}}. {{o.level_name}} ({{o.customer_qty}})</v-flex>
                            <v-flex xs3 class="caption text-xs-right">{{one_money(o.omzet)}}</v-flex>
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

                <v-flex xs12 sm6 md4 :class="{'pl-2':$vuetify.breakpoint.smAndUp}">
                    <div><h3 class="headline text-xs-center mb-3">PER CUSTOMER (Top 25)</h3></div>
                    <div v-for="(o, n) in omzet_customers" v-bind:key="n">
                        <v-layout row wrap>
                            <v-flex xs9 class="caption">{{n+1}}. {{o.customer_name}}, {{o.city_name}} — <i>{{o.level_name}}</i></v-flex>
                            <v-flex xs3 class="caption text-xs-right">{{one_money(o.omzet)}}</v-flex>
                        </v-layout>
                        
                        <v-progress-linear
                            :color="is_selected(o)?'green':'orange'"
                            :height="is_selected(o)?15:15"
                            :value="o.scale"
                            class="mt-0 mb-2"
                            @click="selectc(o)"
                        ></v-progress-linear>

                        <div v-show="is_selected_c(o)">
                            <i class="caption">Produk paling digemari : 
                            <div v-for="(i,m) in o.pareto" :key="m" :class="{'blue--text':false}">
                                <v-layout row wrap>
                                    <v-flex xs9>
                                        {{m+1}} — {{i.item_name}}        
                                    </v-flex>
                                    <v-flex xs3 class="text-xs-right">
                                        {{one_money(i.sub_total)}}
                                    </v-flex>
                                </v-layout>
                                
                            </div>
                            </i>
                            <v-divider class="my-2"></v-divider>
                        </div>
                    </div>
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

.v-select.v-select--chips .v-input__control .v-input__slot {
    padding-left: 0px;
}

.v-select.v-select--chips .v-input__control .v-input__slot .v-input__prepend-inner {
    margin-right: 10px;
}
</style>

<script>
module.exports = {
    components : {
        'common-datepicker' : httpVueLoader('../../common/components/common-datepicker.vue')
    },

    computed :{
        omzet_levels () {
            return this.$store.state.omzet_level.omzet_levels
        },

        omzet_high () {
            return this.$store.state.omzet_level.omzet_high
        },
        
        omzet_customers () {
            return this.$store.state.omzet_level.omzet_customers
        },

        customer_high () {
            return this.$store.state.omzet_level.customer_high
        },

        sdate () {
            return this.$store.state.omzet_level.sdate
        },

        edate () {
            return this.$store.state.omzet_level.edate
        },

        types () {
            return this.$store.state.omzet_level.types
        },

        selected_type : {
            get () { return this.$store.state.omzet_level.selected_type },
            set (v) { this.$store.commit('omzet_level/set_selected_type', v) }
        },

        selected_level () {
            return this.$store.state.omzet_level.selected_level
        },

        selected_customer () {
            return this.$store.state.omzet_level.selected_omzet_customer
        },

        levels () {
            return this.$store.state.omzet_level.levels
        },

        selected_levelx : {
            get () { return this.$store.state.omzet_level.selected_levelx },
            set (v) { 
                this.$store.commit('omzet_level/set_selected_levelx', v)
                this.search() 
            }
        },

        admins () {
            return this.$store.state.omzet_level.users
        },

        selected_admin : {
            get () { return this.$store.state.omzet_level.selected_user },
            set (v) { 
                this.$store.commit('omzet_level/set_selected_user', v)
            }
        },

        omzet_min : {
            get () { return this.$store.state.omzet_level.omzet_min },
            set (v) { 
                this.$store.commit('omzet_level/set_common', ['omzet_min', v])
            }
        },

        omzet_max : {
            get () { return this.$store.state.omzet_level.omzet_max },
            set (v) { 
                this.$store.commit('omzet_level/set_common', ['omzet_max', v])
            }
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        },

        change_sdate (x) {
            this.$store.commit('omzet_level/set_common', ['sdate', x.new_date])
        },

        change_edate (x) {
            this.$store.commit('omzet_level/set_common', ['edate', x.new_date])
        },

        search () {
            this.$store.dispatch('omzet_level/search')
            this.$store.dispatch('omzet_level/search_qty')
        },

        is_selected (x) {
            if (!this.selected_level) return false
            if (this.selected_level == x.level_name)
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
            this.$store.commit('omzet_level/set_selected_level', x.level_name)
        },

        selectc (x) {
            this.select(x)
            this.$store.commit('omzet_level/set_selected_omzet_customer', x)
        }
    },

    mounted () {
        this.$store.dispatch('omzet_level/search_level')
        this.$store.dispatch('omzet_level/search_user', {})
    }
}
</script>