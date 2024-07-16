<template>
<v-dialog
    v-model="dialog"
    scrollable
    persistent 
    max-width="500px"
    transition="dialog-transition"
>
    <v-card>
        <v-card-title primary-title class="pb-0 pt-3">
            <v-layout row wrap>
                <v-flex xs8>
                    <h3 class="headline mb-0">PENGIRIMAN BARANG</h3>        
                </v-flex>
                <v-flex xs4 class="text-xs-right">
                    <h3>No #{{ selected_order.L_SoNumber }}</h3>
                    <p class="mb-0">-</p>
                </v-flex>
            </v-layout>
        </v-card-title>
        <v-card-text>
            
            <v-layout row wrap mb-4 v-if="selected_order.L_SoIsDS == 'N'">
                <v-flex xs9>
                    <h3 class="">{{ selected_order.M_CustomerName }}</h3>
                    <p class="mb-0">{{ selected_order.M_CustomerAddress }}</p>
                    <p class="mb-0">{{ selected_order.customer_full_address.city_name }}</p>
                </v-flex>  

                <v-flex xs3 class="text-xs-right">
                    <v-img :src="'../'+selected_order.M_ExpeditionLogo" height="48" width="96" position="top right"></v-img>
                </v-flex>  
            </v-layout>

            <v-layout row wrap mb-4 v-if="selected_order.L_SoIsDS == 'Y'">
                <v-flex xs9>
                    <h3 class="">{{ selected_order.ds_customer_name }}</h3>
                    <p class="mb-0">{{ selected_order.ds_customer_address }}</p>
                    <p class="mb-0">Kel. {{ selected_order.ds_customer_full_address.village_name }}, 
                        Kec. {{ selected_order.ds_customer_full_address.district_name }}
                    </p>
                    
                    <p class="mb-0">{{ selected_order.ds_customer_full_address.city_name }} -  
                        {{ selected_order.ds_customer_full_address.province_name }}
                    </p>
                </v-flex> 

                <v-flex xs3 class="text-xs-right">
                    <v-spacer></v-spacer><v-img :src="'../'+selected_order.M_ExpeditionLogo" height="48" width="96" style="float:right"></v-img>
                    <h3 class="blue white--text pl-2 pr-2" style="clear:both">
                        {{ selected_order.M_ExpeditionName }}, {{ selected_order.L_SoExpService }}
                    </h3>
                </v-flex>   
            </v-layout>

            <v-card flat>
                <v-card-title primary-title class="orange white--text pt-2 pb-2">
                    <v-layout row wrap>
                        <v-flex xs9>
                            <h4>Items</h4>
                        </v-flex>
                        <v-flex xs3 class="text-xs-right">
                            <h4>Total Berat</h4>
                        </v-flex>
                    </v-layout>
                    
                </v-card-title>

                <v-card-text class="pt-2">
                    <v-layout row wrap>
                        <v-flex xs9>
                            <div v-for="(i, n) in selected_order.items" v-bind:key="n" v-bind:class="n%2 == 0 ? 'cyan--text' : ''"># {{ i }}</div>
                        
                        
                            
                        </v-flex>
                        <v-flex xs3 class="text-xs-right">
                            <h3>{{ selected_order.L_SoTotalWeight/1000 }} Kg</h3>
                        </v-flex>
                    </v-layout>
                </v-card-text>
            </v-card>

            <v-divider class="orange"></v-divider>
            <v-card flat>
                <v-card-text>
                    <v-layout row wrap>
                        <v-flex xs12 mb-2>
                            <v-text-field
                                label="Nomor Resi (opsional)" 
                                placeholder=" "
                                class="mt-1" 
                                outline
                                v-model="receipt_no"
                                hide-details
                            ></v-text-field>
                        </v-flex>
                        <v-flex xs12>
                            <v-textarea label="Catatan" rows="2" placeholder=" " outline v-model="courier_note"></v-textarea>
                        </v-flex>
                    </v-layout>

                    <v-layout row wrap>
                        <v-flex xs12>
                            <div v-show="selected_order.order_note!=''">
                                <v-divider class="mb-2"></v-divider>
                                <div class="body-2 pl-3"><u>Catatan Admin</u></div>
                                <div class="caption pl-3 pr-3">{{selected_order.order_note}}</div>
                                <v-divider class="mt-2 mb-2"></v-divider>
                            </div>                            
                        </v-flex>
                    </v-layout>
                </v-card-text>
            </v-card>

        </v-card-text>
        <v-card-actions>
            <v-btn flat color="primary" @click="dialog=!dialog">Tutup</v-btn>
            <v-spacer></v-spacer>
            <v-btn color="primary" dark @click="send">Kirim</v-btn>
            
        </v-card-actions>
    </v-card>
</v-dialog>
    
</template>

<style>
.zalfa-line-trough {
    text-decoration: line-through;
}

.zalfa-input-super-dense .v-input__control {
    min-height: 36px !important;
}
</style>

<script>
module.exports = {
    data () {
        return {
            w : []
        }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.warehouse.dialog_receipt },
            set (v) { this.$store.commit('warehouse/set_common', ['dialog_receipt', v]) }
        },

        selected_order () {
            if (this.$store.state.warehouse.selected_order)
                return this.$store.state.warehouse.selected_order
            return {}
        },

        courier_name : {
            get () { return this.$store.state.warehouse.courier_name },
            set (v) { this.$store.commit('warehouse/set_common', ['courier_name', v]) }
        },

        courier_note : {
            get () { return this.$store.state.warehouse.courier_note },
            set (v) { this.$store.commit('warehouse/set_common', ['courier_note', v]) }
        },

        receipt_no : {
            get () { return this.$store.state.warehouse.receipt_no },
            set (v) { this.$store.commit('warehouse/set_common', ['receipt_no', v]) }
        }
    },

    methods : {
       one_money (x) {
           return window.one_money(x)
       },

        confirm () {
            this.$store.dispatch('warehouse/confirm')
        },

        send () {
            this.$store.dispatch('warehouse/receipt')
            this.$store.commit('warehouse/set_common', ['dialog_receipt', false])
        }
    },

    mounted () {
        
    },

    watch : {
        
    }
}
</script>