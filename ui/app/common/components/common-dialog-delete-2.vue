<template>
    <v-dialog
        v-model="dialog"
        max-width="500px"
        transition="dialog-transition"
    >
        <v-card>
            <v-card-title primary-title class="red white--text">
                <v-icon color="white">error_outline</v-icon> <h3 class="ml-2">Konfirmasi Hapus</h3>
            </v-card-title>

            <v-card-text>
                {{texts}}
            </v-card-text>

            <v-card-actions>
                <v-btn @click="dialog=false">Batal</v-btn>
                <v-spacer></v-spacer>
                <v-btn color="red" dark @click="delx()">Hapus</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
module.exports = {
    props : ['data', 'text'],
    data () {
        return {}
    },

    computed : {
        dialog : {
            get () { return this.$store.state.dialog_delete },
            set (v) { this.$store.commit('set_dialog_delete', v) }
        },

        texts () {
            return this.text? this.text : 'Apakah anda yakin akan menghapus data tersebut ?'
        }
    },

    methods : {
        delx () {
            console.log("Executing emit ...")
            this.$emit('confirm', {data: this.data});
            this.dialog = false
        }
    }
}
</script>