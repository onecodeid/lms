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
                <h3>DATA ATRIBUT LEAD</h3>
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs12>
                        <v-layout row wrap>

                            <v-flex xs12>
                                <v-select :items="leadAttrCodes" item-value="id" item-text="text" v-model="selectedLeadAttrCode" 
                                :disabled="!!edit" label="Tipe Atribut"></v-select> 
                                <!-- <v-text-field
                                    label="Kode Sumber Lead"
                                    v-model="leadtype_code"
                                    :prefix="code_prefix"
                                ></v-text-field> -->
                            </v-flex>

                            <v-flex xs12>
                                <v-text-field
                                    label="Nama / Deskripsi"
                                    v-model="attrName"
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
        ...Vuex.mapState('leadAttr', ['leadAttrCodes', 'edit']),
        __s () { return this.$store.state.leadAttr },

        dialog : {
            get () { return this.$store.state.leadAttr.dialog },
            set (v) { this.$store.commit('leadAttr/setObject', ['dialog', v]) }
        },

        attrName : {
            get () { return this.__s.attrName },
            set (v) { this.__c('attrName', v) }
        },

        selectedLeadAttrCode : {
            get () { return this.__s.selectedLeadAttrCode },
            set (v) { this.__c('selectedLeadAttrCode', v) }
        },

        code_prefix () {
            return this.$store.state.leadtype_new.leadtype_prefix
        }
    },

    methods : {
        __c (a, b) { this.$store.commit('leadAttr/setObject', [a, b]) },
        save () {
            this.$store.dispatch('leadAttr/save')
        }
    },

    mounted () {},

    watch : {}
}
</script>