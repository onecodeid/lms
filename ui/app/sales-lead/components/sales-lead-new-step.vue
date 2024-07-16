<template>
    <v-dialog
        v-model="dialog"
        :height="height"
        scrollable
        persistent
        max-width="100%"
        transition="dialog-transition"
        content-class="zalfa-dialog-item"
    >
        <v-card>
            <v-card-title primary-title class="primary white--text pt-2 pb-2">
                <v-layout row wrap>
                    <v-flex xs6>
                        <h3 class="headline">LEAD BARU</h3>        
                    </v-flex>
                    <v-flex xs6>
                        
                    </v-flex>
                </v-layout>
            </v-card-title>
            <v-card-text>
                <v-stepper v-model="e1">
                    <v-stepper-header>
                        <v-stepper-step :complete="e1 > 1" step="1">Name of step 1</v-stepper-step>
                
                        <v-divider></v-divider>
                
                        <v-stepper-step :complete="e1 > 2" step="2">Name of step 2</v-stepper-step>
                
                        <v-divider></v-divider>
                
                        <v-stepper-step step="3">Name of step 3</v-stepper-step>
                    </v-stepper-header>
                
                    <v-stepper-items>
                        <v-stepper-content step="1">
                        <v-card
                            class="mb-5"
                            color="grey lighten-1"
                            height="200px"
                        ></v-card>
                
                        <v-btn
                            color="primary"
                            @click="e1 = 2"
                        >
                            Continue
                        </v-btn>
                
                        <v-btn flat>Cancel</v-btn>
                        </v-stepper-content>
                
                        <v-stepper-content step="2">
                        <v-card
                            class="mb-5"
                            color="grey lighten-1"
                            height="200px"
                        ></v-card>
                
                        <v-btn
                            color="primary"
                            @click="e1 = 3"
                        >
                            Continue
                        </v-btn>
                
                        <v-btn flat>Cancel</v-btn>
                        </v-stepper-content>
                
                        <v-stepper-content step="3">
                        <v-card
                            class="mb-5"
                            color="grey lighten-1"
                            height="200px"
                        ></v-card>
                
                        <v-btn
                            color="primary"
                            @click="e1 = 1"
                        >
                            Continue
                        </v-btn>
                
                        <v-btn flat>Cancel</v-btn>
                        </v-stepper-content>
                    </v-stepper-items>
                </v-stepper>
            </v-card-text>
        </v-card>
    </v-dialog>
    
  </template>

<script>
module.exports = {
    components : {
    },

    data () {
        return {
            e1: 1
        }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.saleslead_new.dialog_step },
            set (v) { this.$store.commit('saleslead_new/set_common', ['dialog_step', v]) }
        }
    }
}