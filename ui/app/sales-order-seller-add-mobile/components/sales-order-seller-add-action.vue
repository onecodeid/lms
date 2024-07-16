<template>
    <v-dialog
        v-model="dialog"
        scrollable
        overlay
        transition="dialog-transition"
        min-width="360px"
    >
        <v-card>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs12>
                        <v-list>
                            <v-list-tile @click="change_qty">
                                <v-list-tile-title>UBAH QTY</v-list-tile-title>
                            </v-list-tile>

                            <v-list-tile @click="del">
                                <v-list-tile-title>HAPUS ITEM</v-list-tile-title>
                            </v-list-tile>
                            
                            <!-- <v-list-tile
                            v-for="(item, index) in items"
                            :key="index"
                            >
                            <v-list-tile-title>{{ item.title }}</v-list-tile-title>
                            </v-list-tile> -->
                        </v-list>        
                    </v-flex>
                </v-layout>
            </v-card-text>
        </v-card>
        
    </v-dialog>
</template>

<script>
module.exports = {
    data () {
        return {
            items: [
                { title: 'Click Me' },
                { title: 'Click Me' },
                { title: 'Click Me' },
                { title: 'Click Me 2' }
            ]
        }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.salesorder.dialog_action },
            set (v) { this.$store.commit('salesorder/set_common', ['dialog_action', v]) }
        },

        height () {
            return window.innerHeight * 0.95
        }
    },

    methods : {
        change_qty () {
            this.dialog = false
            this.$store.commit('salesorder/set_common', ['dialog_qty', true])
        },

        del () {
            let idx = 0
            let itm = this.$store.state.salesorder.selected_item
            let items = this.$store.state.salesorder.items
            for (let i in items)
                if (items[i].item_id == itm.item_id && items[i].is_packet == itm.is_packet)
                    idx = i
            
            items.splice(idx, 1)
            this.$store.commit('salesorder/update_items', items)
            this.$store.commit('salesorder/update_total_item', items.length)
            this.dialog = false
        }
    },

    mounted () {
        
    }
}
</script>