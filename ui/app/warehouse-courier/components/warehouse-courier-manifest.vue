<template>
    <v-dialog
        v-model="dialog"
        scrollable
        
        max-width="500px"
        transition="dialog-transition"
    >
        <v-card>
            <v-card-title class="primary white--text">
                <h3 class="headline">Histori Pengiriman</h3>
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <template v-for="(m, i) in manifests">
                        <v-flex xs3 :key="i" class="text-xs-right pr-2 blue--text">
                            <div>{{ m.manifest_date }}</div>
                            <div>{{ m.manifest_time }}</div>
                        </v-flex>
                        <v-flex xs8 :key="i">
                            {{ m.manifest_description }}
                        </v-flex>
                    </template>
                </v-layout>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
module.exports = {
    computed : {
        dialog : {
            get () { return this.$store.state.warehouse.dialog_manifest },
            set (v) { this.$store.commit('warehouse/set_common', ['dialog_manifest', v]) }
        },

        manifests () {
            return this.$store.state.warehouse.manifests
        }
    }
}
</script>