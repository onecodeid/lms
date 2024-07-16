<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs9>
                    <h3 class="headline font-weight-light zalfa-text-title">Komisi, Reward dan Poin</h3>
                </v-flex>
                <v-flex xs3 class="text-xs-right">
                    <v-btn color="primary" v-show="selected_packet != null" class="ma-0" @click="go_to_price">Setting Harga</v-btn>
                    <v-btn color="success" class="btn-icon ma-0" @click="save_fee"><v-icon>save</v-icon></v-btn>
                </v-flex>
            </v-layout>
        </v-card-title>

        <v-card-text class="pt-2">
            <div class="v-table__overflow">
                <table class="v-datatable v-table theme--light">
                    <thead>
                        <tr>
                            <th v-for="(h,i) in headers_price" v-bind:key="i"
                                role="columnheader"
                                scope="col" :width="h.width"
                                :aria-label="h.text+': Not sorted.'"
                                aria-sort="none" 
                                class="column pa-2 zalfa-bg-purple lighten-3 white--text"
                                v-bind:class="'text-xs-'+h.align"
                                >{{h.text}}</th>
                        </tr>
                        <tr class="v-datatable__progress">
                            <th colspan="8" class="column"></th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        <tr>
                            <td class="text-xs-left pa-2">KOMISI Admin</td>
                            <td v-for="(f,i) in fees" v-bind:key="i" class="text-xs-right pa-1">
                                <v-text-field
                                    solo
                                    hide-details
                                    reverse
                                    :value="f.M_PacketFeeAmount"
                                    @change="set_fee(f.M_CustomerLevelCode, 'fee', $event)"
                                ></v-text-field>
                            </td>
                            <!-- <td class="text-xs-center pa-0">
                                <v-btn color="success" class="btn-icon ma-0" small><v-icon>save</v-icon></v-btn>
                            </td> -->
                        </tr>

                        <tr>
                            <td class="text-xs-left pa-2">REWARD Customer</td>
                            <td v-for="(f,i) in fees" v-bind:key="i" class="text-xs-right pa-1">
                                <v-text-field
                                    solo
                                    hide-details
                                    reverse
                                    :value="f.M_PacketFeeRewardAmount"
                                    @change="set_fee(f.M_CustomerLevelCode, 'reward', $event)"
                                ></v-text-field>
                            </td>
                            <!-- <td class="text-xs-center pa-0">
                                <v-btn color="success" class="btn-icon ma-0" small><v-icon>save</v-icon></v-btn>
                            </td> -->
                        </tr>

                        <tr>
                            <td class="text-xs-left pa-2">POIN Customer</td>
                            <td v-for="(f,i) in fees" v-bind:key="i" class="text-xs-right pa-1">
                                <v-text-field
                                    solo
                                    hide-details
                                    reverse
                                    :value="f.M_PacketFeePointAmount"
                                    @change="set_fee(f.M_CustomerLevelCode, 'point', $event)"
                                ></v-text-field>
                            </td>
                            <!-- <td class="text-xs-center pa-0">
                                <v-btn color="success" class="btn-icon ma-0" small><v-icon>save</v-icon></v-btn>
                            </td> -->
                        </tr>
                    </tbody>
                </table>
            </div>
        </v-card-text>
    </v-card>
</template>

<script>
module.exports = {
    data () {
        return {
            headers: [
                {
                    text: "DESKRIPSI",
                    align: "center",
                    sortable: false,
                    width: "30%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ]
        }
    },

    computed : {
        headers_price () {
            let h = this.headers
            let i = this.$store.state.packetdetail.headers_price

            // let hx = h.splice(0)
            if (i)
                for (let x of i)
                    h.push({
                        text: x['M_CustomerLevelName'],
                        align: "right",
                        sortable: false,
                        width: "10%",
                        class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                    })
            // h.push(hx[0])
            return h
        },

        fees () {
            return this.$store.state.packetdetail.fees
        },

        selected_packet () {
            return this.$store.state.packet.selected_packet
        }
    },

    methods : {
        save_fee () {
            this.$store.dispatch('packetdetail/save_fee')
        },

        set_fee (level, type, amount) {
            let f = this.fees
            for (let i in f) {
                if (f[i].M_CustomerLevelCode == level) {
                    if (type == 'fee')
                        f[i].M_PacketFeeAmount = amount
                    else if (type == 'reward')
                        f[i].M_PacketFeeRewardAmount = amount
                    else if (type == 'point')
                        f[i].M_PacketFeePointAmount = amount
                }
            }
            this.$store.commit('packetdetail/set_fees', f)
        },

        go_to_price () {
            this.$store.commit('packetdetail/set_common', ['selected_tab', 1])
        }
    }
}
</script>