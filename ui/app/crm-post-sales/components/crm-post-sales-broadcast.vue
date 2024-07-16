<template>
    <v-dialog
        v-model="dialog"
        scrollable
        :overlay="false"
        max-width="1000px"
        transition="dialog-transition"
        content-class="dialog-wa"
        id="dialog_broadcast"
    >
        <v-card>
            <v-card-title primary-title class="green white--text py-1 pr-1">
                <v-layout row wrap>
                    <v-flex class="pt-2">
                        <img src="../assets/img/logo-whatsapp-64x64.png" width="24" style="float: left" class="mr-2 mb-2" />
                        <h3>BROADCAST PESAN</h3>        
                    </v-flex>
                    <v-flex class="text-xs-right">
                        <v-btn color="red" class="btn-icon ma-0 white--text" depressed @click="dialog=!dialog"><v-icon>close</v-icon></v-btn>
                    </v-flex>
                </v-layout>
                
            </v-card-title>
            <v-card-text class="pa-0">
                <v-layout row wrap class="fill-height">
                    <v-flex xs4 class="fill-height">
                        <crm-broadcast-filter></crm-broadcast-filter>
                    </v-flex>
                    <v-flex xs8 class="fill-height">
                        <crm-broadcast-list></crm-broadcast-list>
                    </v-flex>
                </v-layout>
               
            </v-card-text>

            <v-card-actions>
                
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<style>
.input-dense .v-input__control {
    min-height: 36px !important;
}

.dialog-wa .wa-container .v-card .v-card__text {
    max-height: 180px;
    overflow-y: scroll;
}
.dialog-wa .v-text-field.v-text-field--solo .v-input__control {
    min-height: 36px;
}
.dialog-wa .v-text-field.v-text-field--solo .v-input__append-outer {
    margin-top: 0px;
    margin-left: 0px;
}
</style>
<script>
let ts = Math.random() * 10e10
module.exports = {
    components : {
        "crm-broadcast-filter" : httpVueLoader("../../crm-broadcast/components/crm-broadcast-filter.vue?t="+ts),
        "crm-broadcast-list" : httpVueLoader("../../crm-broadcast/components/crm-broadcast-list.vue?t="+ts)
    },

    data () {
        return {
        }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.post_sales.dialog_broadcast },
            set (v) { this.$store.commit('post_sales/set_common', ['dialog_broadcast', v]) }
        }
    },

    methods : {
       
    },

    mounted () {
        
    },

    watch : {},

    ready () {
        
    }
}
</script>