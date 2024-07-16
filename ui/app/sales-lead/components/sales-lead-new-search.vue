<template>
    <v-card>
        <v-card-text class="grey lighten-5">
            <v-layout>
                <v-flex xs12>
                    <v-autocomplete solo large placeholder="Masukkan NO HP atau Nama"
                    v-model="selectedCustomer"
                    :items="customers"
                    :search-input.sync="searchCustomer"
                    auto-select-first
                    no-filter
                    return-object
                    :clearable="true"
                    item-text="M_CustomerName"
                    :loading="false"
                    no-data-text="Pilih Customer"
                    autoComplete='off'
                    >
                    <template slot="item" slot-scope="{ item }">
                        <v-list-tile-content>
                            <v-list-tile-title>
                                <span class="black--text">{{ item.M_CustomerName }}</span>
                            </v-list-tile-title>
                        <v-list-tile-sub-title>
                            <span class="primary--text">{{ item.M_CustomerPhone }}</span> | {{ item.M_CustomerLevelName }}, {{ item.M_CityName }}
                        </v-list-tile-sub-title>
                        </v-list-tile-content>
                    </template>
                    </v-autocomplete>
                </v-flex>
            </v-layout> 
            
            <v-layout v-if="!!selectedCustomer">
                <v-flex xs12>
                    <v-text-field
                        label="Telp / HP"
                        :value="selectedCustomer.M_CustomerPhone" flat readonly
                    >
                    </v-text-field>
                    <v-textarea
                        label="Alamat Lengkap"
                        placeholder="Kota, Kecamatan, Kelurahan RT RW"
                        rows="2" auto-grow
                        :value="selectedCustomer.M_CustomerAddress"
                        :readonly="true" flat>
                    </v-textarea>
                </v-flex>
            </v-layout>

            <v-layout>
                <v-flex xs12 class="caption text-xs-center cyan lighten-5 pa-2" v-if="orderHistories.length<1">
                    <i>Belum ada catatan order</i>
                </v-flex>

                <v-flex xs12 class="text-xs-left cyan lighten-5 pa-2" v-else>
                    <div class="primary--text">Pembelian terakhir :</div>
                    <v-divider></v-divider>
                    <div class="mt-2">
                        <b>Tanggal : </b>{{ orderHistories[0].so_date }}
                    </div>
                    <div>
                        <b>Items : </b>
                        <span v-for="(i, n) in orderHistories[0].details" :key="n">
                            <template v-if="n>0">, </template>{{ i.item_code }} - {{ i.item_name }} ({{ i.item_qty }})
                        </span>
                    </div>
                </v-flex>
            </v-layout>
        </v-card-text>
    </v-card>
                                        
</template>

<style>
.v-text-field.v-text-field--solo .v-input__control {
    min-height: 36px;
}
.vertical-center {
  margin: 0;
  position: absolute;
  top: 50%;
  -ms-transform: translateY(-50%);
  transform: translateY(-50%);
}
.zalfa-dialog-item {
    margin: 12px !important;
    max-height: 95% !important;
}
.zalfa-dialog-item .v-text-field.v-text-field--solo .v-input__append-outer {
    margin-top: 0px;
    margin-left: 0px;
}
.qo-detail-table table thead tr {
    height: auto
}
.zalfa-card-dashed {
    border: dashed 1px red !important
}
</style>
<script>
module.exports = {
    components : {
    },

    data () {
        return {
            
        }
    },

    computed : {
        ...Vuex.mapState({
            customers: s => s.saleslead_new.customers,
            orderHistories: s => s.saleslead_new.orderHistories
        }),

        searchCustomer : {
            get () { return this.$store.state.saleslead_new.searchCustomer },
            set (v) { this.$store.commit('saleslead_new/set_object', ['searchCustomer', v]) }
        },

        selectedCustomer : {
            get () { return this.$store.state.saleslead_new.selectedCustomer },
            set (v) { 
                this.$store.commit('saleslead_new/set_object', ['selectedCustomer', v])
                this.$store.dispatch('saleslead_new/searchLastOrder').then(x => {
                    
                })
            }
        }
    },

    methods : {
        thr_search_2: _.debounce( function () {
            this.$store.dispatch("saleslead_new/searchCustomer")
        }, 700)
    },

    mounted () {
    },

    watch : {
        searchCustomer(val, old) {
            if (val == null || typeof val == 'undefined') val = ""
            if (val == old ) return
            if (this.$store.state.saleslead_new.search_status == 1 ) return  
            this.$store.commit("saleslead_new/set_object", ['searchCustomer', val])
            this.thr_search_2()
        }
    }
}
</script>