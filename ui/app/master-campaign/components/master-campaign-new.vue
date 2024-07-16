<template>
    <v-dialog
        v-model="dialog"
        scrollable
        :overlay="false"
        max-width="300px"
        transition="dialog-transition"
    >
        <v-card>
            <v-card-title primary-title>
                <h3>{{edit?'UBAH DATA CAMPAIGN':'CAMPAIGN BARU'}}</h3>
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs12>
                        <v-layout row wrap>
                            <v-flex xs12 mt-3>
                                <v-text-field
                                    label="Nama Campaign"
                                    v-model="campaign_name"
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12>
                                <v-select
                                    :items="expeditions"
                                    v-model="selected_expedition"
                                    item-text="M_ExpeditionName"
                                    item-value="M_ExpeditionID"
                                    label="Ekspedisi"
                                    placeholder="Pilih salah satu"
                                    return-object
                                ></v-select>
                            </v-flex>

                            <v-flex xs12>
                                <v-select
                                    :items="services"
                                    v-model="selected_service"
                                    item-text="service"
                                    item-value="service"
                                    label="Service"
                                    placeholder="Pilih salah satu"
                                    return-object
                                    :loading="loading_service"
                                ></v-select>
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

<script>
module.exports = {
    data () {
        return {
        }
    },

    computed : {
        edit () {
            return this.$store.state.campaign_new.edit
        },

        dialog : {
            get () { return this.$store.state.campaign_new.dialog_new },
            set (v) { this.$store.commit('campaign_new/set_common', ['dialog_new', v]) }
        },

        campaign_name : {
            get () { return this.$store.state.campaign_new.campaign_name },
            set (v) { this.$store.commit('campaign_new/set_common', ['campaign_name', v]) }
        },

        expeditions () {
            return this.$store.state.campaign_new.expeditions
        },

        selected_expedition : {
            get () { return this.$store.state.campaign_new.selected_expedition },
            set (v) { 
                this.$store.commit('campaign_new/set_selected_expedition', v)
                this.$store.dispatch('campaign_new/search_service', {})
            }
        },

        services () {
            return this.$store.state.campaign_new.services
        },

        selected_service : {
            get () { return this.$store.state.campaign_new.selected_service },
            set (v) { 
                this.$store.commit('campaign_new/set_selected_service', v)
            }
        },

        loading_service () {
            return this.$store.state.campaign.loading_service
        }
    },

    methods : {
        save () {
            this.$store.dispatch('campaign_new/save')
        }
    },

    mounted () {
        this.$store.dispatch('campaign_new/search_expedition')
    },

    watch : {
        
    }
}
</script>