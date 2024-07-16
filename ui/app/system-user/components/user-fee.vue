<template>
    <v-layout row wrap>
        <v-flex xs12>
            <v-data-table 
                :headers="headers"
                :items="fees"
                :loading="false"
                hide-actions
                class="elevation-1">
                <template slot="items" slot-scope="props">
                    <td class="text-xs-left pa-2">
                        <v-checkbox :label="props.item.level_name" 
                            true-value="Y"
                            false-value="N"
                            :value="props.item.is"
                            hide-details
                            @change="change_price('is', props.index, $event)"></v-checkbox>
                        </td>
                    <td class="text-xs-left pa-1">
                        <v-text-field
                            solo
                            :value="props.item.amount"
                            hide-details
                            reverse
                            class="input-dense"
                            @input="change_price('amount', props.index, $event)"
                            :disabled="props.item.is=='N'"
                        ></v-text-field>
                    </td>

                    <td class="text-xs-left pa-1">
                        <v-text-field
                            solo
                            :value="props.item.point"
                            hide-details
                            reverse
                            class="input-dense"
                            @input="change_price('point', props.index, $event)"
                            :disabled="props.item.is=='N'"
                        ></v-text-field>
                    </td>

                    <td class="text-xs-left pa-1">
                        <v-text-field
                            solo
                            :value="props.item.reward"
                            hide-details
                            reverse
                            class="input-dense"
                            @input="change_price('reward', props.index, $event)"
                            :disabled="props.item.is=='N'"
                        ></v-text-field>
                    </td>
                    <!-- <td class="text-xs-center pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.M_DoctorHP}}</td>
                    <td class="text-xs-left pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.status}}</td> -->
                </template>
            </v-data-table>
        </v-flex>
    </v-layout>
</template>
<style>
.input-dense .v-input__control {
    min-height: 36px !important;
}
</style>
<script>
module.exports = {
    data () {
        return {
            headers: [
                {
                    text: "LEVEL",
                    align: "center",
                    sortable: false,
                    width: "40%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "KOMISI",
                    align: "center",
                    sortable: false,
                    width: "20%",
                    class: "pa-2 zalfa-bg-cyan lighten-3 white--text"
                },
                {
                    text: "POIN",
                    align: "center",
                    sortable: false,
                    width: "20%",
                    class: "pa-2 zalfa-bg-cyan lighten-3 white--text"
                },
                {
                    text: "REWARD",
                    align: "center",
                    sortable: false,
                    width: "20%",
                    class: "pa-2 zalfa-bg-cyan lighten-3 white--text"
                }
            ]
        }
    },

    computed : {
        fees () {
            return this.$store.state.user_new.fees
        }
    },

    methods : {
        change_price(type, i, v) {
            let x = this.fees
            x[i][type] = v
            this.$store.commit('user_new/set_fees', x)
        }
    }
}
</script>