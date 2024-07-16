<template>
    <v-dialog v-model="dialog" scrollable :overlay="false" max-width="500px" transition="dialog-transition">
        <v-card>
            <v-card-title primary-title class="cyan white--text pt-3">
                <h3>EXPENSE BARU</h3>
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs12>
                        Item Name
                    </v-flex>
                    <v-flex xs12>
                        <v-divider></v-divider>
                    </v-flex>
                    
                    <v-flex xs12>
                        <v-text-field value="2" prefix="Unit" suffix="Awal" label="Awal" solo reverse hide-details class="pb-2" flat></v-text-field>
                        <v-text-field value="2" prefix="Unit" suffix="Adjustment" label="Awal" solo reverse hide-details class="pb-2"></v-text-field>
                        <v-text-field value="2" prefix="Unit" suffix="Akhir" label="Awal" solo reverse hide-details flat></v-text-field>
                    </v-flex>
                </v-layout>
            </v-card-text>
                
            <v-card-actions>
                <v-btn color="primary" flat @click="dialog = !dialog">Batal</v-btn>
                <v-spacer></v-spacer>
                <v-btn color="primary">Simpan</v-btn>
            </v-card-actions>
        </v-card>


    </v-dialog>
</template>

<style>
.input-dense .v-input__control {
    min-height: 36px !important;
}
</style>
<script>
module.exports = {
    components: {
        'common-datepicker': httpVueLoader('../../common/components/common-datepicker.vue')
    },

    data() {
        return {
            add_mode: false
        }
    },

    computed : {
        ...Vuex.mapState({
            __s: s => s.adjusttransfer
        }),

        dialog : {
            get () { return this.__s.dialogAdjust },
            set (v) { this.__c("dialogAdjust", v) }
        }
    },

    methods : {
        __c(a,b) { return this.$store.commit('adjusttransfer/set_object', [a,b]) }
    }
}
</script>