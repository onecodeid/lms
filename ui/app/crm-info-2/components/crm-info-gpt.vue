<template>
    <v-dialog
        v-model="dialog"
        scrollable
        :overlay="false"
        max-width="500px"
        transition="dialog-transition"
    >
        <v-card>
            <v-card-title primary-title class="teal accent-4 white--text pt-3">
                <h3>GPT HELPER</h3>
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs12>
                        <v-select
                            :items="gpt_types"
                            item-value="id"
                            item-text="text"
                            label="Jenis Informasi"
                            return-object
                            v-model="selected_gpt_type"
                        >
                        </v-select>
                        
                        <v-text-field
                            v-model="gpt_topic"
                            label="Tuliskan Topik"
                        >
                        </v-text-field>
                        
                    </v-flex>
                    
                </v-layout>
            </v-card-text>

            <v-card-actions>
                <!-- <v-btn color="primary" flat @click="dialog=!dialog">Batal</v-btn>
                <v-spacer></v-spacer> -->
                <v-btn class="teal accent-4 white--text" @click="getGpt">Simpan</v-btn>                
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<style>
.input-dense .v-input__control {
    min-height: 36px !important;
}

.watzap-text-container {
    /* background-image:url(../../assets/img/bg-whatsapp.png); */
    font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", 
  Helvetica, Arial, "Lucida Grande", sans-serif;
}

.watzap-text-container textarea {
    overflow: auto;
}

.watzap-text-container textarea::-webkit-scrollbar {
    width: .3em;
}

/* .watzap-text-container textarea::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
}

.watzap-text-container textarea::-webkit-scrollbar-thumb {
  background-color: darkgrey;
  outline: 1px solid slategrey;
} */
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
        dialog : {
            get () { return this.$store.state.info_new.dialog_gpt },
            set (v) { this.$store.commit('info_new/set_common', ['dialog_gpt', v]) }
        },

        gpt_topic : {
            get () { return this.$store.state.info_new.gpt_topic },
            set (v) { this.$store.commit('info_new/set_common', ['gpt_topic', v]) }
        },

        gpt_types () {
            return this.$store.state.info_new.gpt_types
        },

        selected_gpt_type : {
            get () { return this.$store.state.info_new.selected_gpt_type },
            set (v) { this.$store.commit('info_new/set_object', ['selected_gpt_type', v]) }
        }
    },

    methods : {
        getGpt() {
            this.$store.dispatch("info_new/getGpt")
        }
    },

    mounted () {
        
    },

    watch : {}
}
</script>