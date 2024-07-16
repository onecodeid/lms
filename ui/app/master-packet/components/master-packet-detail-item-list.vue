<template>
    <v-dialog
        v-model="dialog"
        scrollable
        :overlay="false"
        max-width="500px"
        transition="dialog-transition"
    >
        <v-card>
            <v-card-title primary-title class="pt-2 pb-0">
                <v-layout row wrap>
                    <v-flex xs6>
                        <h3>Pilih Item</h3>
                    </v-flex>
                    <v-flex xs6 class="text-xs-right">
                        <v-spacer></v-spacer><v-btn color="red" dark small @click="dialog=!dialog" class="ma-0 mb-2 btn-icon"><v-icon>clear</v-icon></v-btn>
                    </v-flex>
                </v-layout>
            </v-card-title>
            <v-card-text class="pt-0">
                <v-data-table 
                    :headers="headers"
                    :items="items"
                    :loading="false"
                    hide-actions
                    class="elevation-1">
                    <template slot="items" slot-scope="props">
                        <td class="text-xs-left pa-0 pt-1 pl-1" @click="select(props.item)">
                            {{ props.item.M_ItemCode }}</td>
                        <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.M_ItemName }}</td>
                        
                        
                        <td class="text-xs-center pa-0" @click="select(props.item)">
                            <v-btn color="primary" class="btn-icon ma-0" small @click="add(props.item)"><v-icon>arrow_drop_down</v-icon></v-btn>
                        </td>
                    </template>
                </v-data-table>
                <v-divider></v-divider>
                <v-pagination
                    style="margin-top:10px;margin-bottom:10px"
                    v-model="curr_page"
                    :length="xtotal_page"
                    @input="change_page"
                ></v-pagination>
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
            headers: [
                {
                    text: "KODE",
                    align: "left",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "NAMA",
                    align: "left",
                    sortable: false,
                    width: "75%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "ACTION",
                    align: "center",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ]
        }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.packetdetail.dialog_item_av },
            set (v) { this.$store.commit('packetdetail/set_common', ['dialog_item_av', v]) }
        },

        items () {
            return this.$store.state.packetdetail.items_av
        },

        item_id () {
            return this.$store.state.packetdetail.selected_item_av.M_ItemID
        },

        curr_page : {
            get () { return this.$store.state.packetdetail.current_page },
            set (v) { this.$store.commit('packetdetail/update_current_page', v) }
        },

        xtotal_page () {
            return this.$store.state.packetdetail.total_item_av_page
        }
    },

    methods : {
        select (x) {
            this.$store.commit('packetdetail/set_selected_item_av', x)
        },

        add (x) {
            this.select(x)
            this.$store.dispatch('packetdetail/add', {})
        },

        change_page(x) {
            this.curr_page = x
            this.$store.dispatch('packetdetail/search_av_item', {})
        }
    }
}
</script>