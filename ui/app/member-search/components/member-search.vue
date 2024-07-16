<template>
    <v-card>
        <v-card-text>
            <v-layout row wrap>
                <v-flex xs12 mb-2>
                    <v-text-field
                        label="Nomor Member"
                        outline
                        placeholder="0000 00000"
                        mask="#### #####"
                        prefix="Z"
                        hide-details
                    ></v-text-field>
                </v-flex>

                <v-flex xs12>
                    <v-alert
                    :value="true"
                    color="warning"
                    icon="priority_high"
                    outline
                    >
                    Nomor member tidak dikenali, coba dengan yang lain.
                    </v-alert>
                </v-flex>

                <v-divider class="my-2"></v-divider>
                <v-flex xs12 mb-2>
                    <h3 class="headline">SUTARJO</h3>
                    <div>Distributor, Kota Malang</div>
                    <div>Propinsi Jawa Timur</div>
                    <div><v-icon small>phonelink_ring</v-icon> 089234432</div>
                </v-flex>

                <v-flex xs12>
                    <v-img src="https://register.zalfa.id/img/Z010100001.jpg"></v-img>
                </v-flex>
            </v-layout>
        </v-card-text>
    </v-card>
</template>

<script>
module.exports = {
    mounted () {
        this.$store.dispatch('member/search')
    }
}
</script>