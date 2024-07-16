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
                        <v-text-field
                            label="Nama Item"
                            readonly
                            :value="item.item_name"
                            outline
                        ></v-text-field>    
                    </v-flex>

                    <v-flex xs4>
                        <v-text-field
                            label="Qty"
                            type="number"
                            outline
                            :value="item.item_qty"
                            @change="change_qty"
                            v-if="dialog"
                        ></v-text-field>    
                    </v-flex>
                    <v-flex xs12>
                        <v-divider class="mb-2 mt-2"></v-divider>    
                    </v-flex>
                    
                    <v-flex xs6 pr-1>
                        <v-btn color="primary" outline block @click="dialog=!dialog">Tutup</v-btn>
                    </v-flex>

                    <v-flex xs6 pl-1>
                        <v-btn color="primary" block @click="save">Simpan</v-btn>
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
            qty:0
        }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.salesorder.dialog_qty },
            set (v) { this.$store.commit('salesorder/set_common', ['dialog_qty', v]) }
        },

        item () {
            return this.$store.state.salesorder.selected_item
        }
    },

    methods : {
        change_qty (x) {
            this.qty = x
        },

        save () {
            let idx = 0
            let itm = this.$store.state.salesorder.selected_item
            let items = this.$store.state.salesorder.items
            for (let i in items)
                if (items[i].item_id == itm.item_id && items[i].is_packet == itm.is_packet)
                    idx = i
            
            items[idx].item_qty = this.qty
            items[idx].item_subtotal = (itm.item_price - itm.item_discrp) * this.qty 
            itm = items[idx]
            this.$store.commit('salesorder/update_items', items)
            this.$store.commit('salesorder/set_selected_item', itm)
            this.dialog = false
        }
    },

    mounted () {
        
    }
}
</script>