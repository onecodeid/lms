<template>
    <v-dialog
        v-model="dialog"
        persistent overlay
        max-width="500px"
        transition="dialog-transition"
    >
        <v-card>
            <v-card-text>
                <v-progress-linear :indeterminate="true"></v-progress-linear>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
module.exports = {
    computed : {
        dialog : {
            get () { return this.$store.state.dialog_progress },
            set (v) { this.$store.commit('set_dialog_progress', v) }
        }
    }
}
</script>