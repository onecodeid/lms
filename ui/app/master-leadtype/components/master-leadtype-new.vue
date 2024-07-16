<template>
    <v-dialog
        v-model="dialog"
        scrollable
        :overlay="false"
        max-width="500px"
        transition="dialog-transition"
    >
        <v-card>
            <v-card-title primary-title class="cyan white--text pt-3">
                <h3>DATA SUMBER LEAD BARU</h3>
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs12>
                        <v-layout row wrap>

                            <v-flex xs12>
                                <v-text-field
                                    label="Kode Sumber Lead"
                                    v-model="leadtype_code"
                                    :prefix="code_prefix"
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12>
                                <v-text-field
                                    label="Nama Sumber Lead"
                                    v-model="leadtype_name"
                                ></v-text-field>
                            </v-flex>

                        </v-layout>
                    </v-flex>
                </v-layout>
            </v-card-text>

            <v-card-actions>
                <v-btn color="primary" flat @click="dialog=!dialog">Batal</v-btn>
                <v-spacer></v-spacer>
                <v-btn color="primary" @click="save">Simpan</v-btn>                
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
    components : {
        'common-datepicker' : httpVueLoader('../../common/components/common-datepicker.vue')
    },

    data () {
        return { }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.leadtype_new.dialog_new },
            set (v) { this.$store.commit('leadtype_new/set_common', ['dialog_new', v]) }
        },

        leadtype_name : {
            get () { return this.$store.state.leadtype_new.leadtype_name },
            set (v) { this.$store.commit('leadtype_new/set_common', ['leadtype_name', v]) }
        },

        leadtype_code : {
            get () { return this.$store.state.leadtype_new.leadtype_code },
            set (v) { this.$store.commit('leadtype_new/set_common', ['leadtype_code', v]) }
        },

        code_prefix () {
            return this.$store.state.leadtype_new.leadtype_prefix
        }
    },

    methods : {
        save () {
            this.$store.dispatch('leadtype_new/save')
        }
    },

    mounted () {},

    watch : {}
}
</script>