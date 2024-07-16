<template>
    <v-card depressed flat>
        <v-card-title primary-title class="blue lighten-2 white--text py-2">
            <h3>DETAIL OMZET PER WILAYAH</h3>
        </v-card-title>
        <v-card-text>
            <v-layout row wrap>
                <v-flex xs3 v-for="(prov, n) of areas.province" :key="n" 
                    :class="{'pr-2':n%4!=3,'pl-2':n%4!=0}">
                    <div class="caption d-flex">
                        <div>{{n+1}}. {{prov.M_ProvinceName}}</div><v-spacer></v-spacer>
                        <div class="text-xs-right">{{one_money(prov.total)}}</div>
                    </div>
                    <v-progress-linear
                        :color="'blue'"
                        :height="10"
                        :value="prov.scale"
                        class="mt-0 mb-0"
                    ></v-progress-linear>

                    <v-layout row wrap>
                        <v-flex xs1 pt-1>
                            <v-divider vertical></v-divider>
                        </v-flex>
                        <v-flex xs11>
                            <v-layout row wrap>
                                <v-flex xs12 v-for="(city, i) of prov.city" :key="'city-'+i" mt-2>
                                    <div class="caption d-flex">
                                        <div>{{n+1}}.{{i+1}}. {{city.M_CityName}}</div><v-spacer></v-spacer>
                                        <div class="text-xs-right">{{one_money(city.total)}}</div>
                                    </div>
                                    <v-progress-linear
                                        :color="'cyan'"
                                        :height="10"
                                        :value="city.scale"
                                        class="mt-0 mb-0"
                                    ></v-progress-linear>
                                </v-flex>
                            </v-layout>
                        </v-flex>
                    </v-layout>
                </v-flex>
            </v-layout>        
        </v-card-text>
    </v-card>
    
</template>

<style scoped>
.v-divider--vertical {
    border-width: 2px;
}
</style>

<script>
module.exports = {
    computed : {
        areas () {
            let areas = this.$store.state.omzet_product.areas
            for (let i in areas.province) {
                areas.province[i].scale = areas.province[i].total * 100 / areas.province_max

                for (let j in areas.province[i].city) {
                    areas.province[i].city[j].scale = areas.province[i].city[j].total * 100 / areas.province_max //areas.province[i].city_max
                }
            }

            return areas
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        }
    }
}
</script>