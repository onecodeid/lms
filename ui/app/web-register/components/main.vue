<template>
    <v-row>
        <v-col cols="12">
            <!-- <v-img
                src="https://higherlogicdownload.s3.amazonaws.com/NADP/932c62af-b1d9-4741-9d57-76fe04359af6/UploadedImages/Schedule-CON20-reg-header.png"></v-img> -->
        </v-col>

        <v-col cols="12">
            <v-stepper v-model="step">
                <v-stepper-header>
                    <v-stepper-step :complete="step > 1" step="1">
                        Data Pribadi
                    </v-stepper-step>

                    <v-divider></v-divider>

                    <v-stepper-step :complete="step > 2" step="2">
                        Data Pembayaran
                    </v-stepper-step>

                    <v-divider></v-divider>

                    <v-stepper-step step="3">
                        Selesai
                    </v-stepper-step>
                </v-stepper-header>

                <v-stepper-items>
                    <v-stepper-content step="1">
                        <v-card class="mb-12" color="grey lighten-1">
                            <register></register>
                        </v-card>
                    </v-stepper-content>

                    <v-stepper-content step="2">
                        <v-card class="mb-12" color="grey lighten-1">
                            <payment></payment>
                        </v-card>
                    </v-stepper-content>

                    <v-stepper-content step="3">
                        <success></success>
                    </v-stepper-content>
                </v-stepper-items>
            </v-stepper>
        </v-col>
    </v-row>


</template>

<script>
module.exports = {
    data() {
        return {
        }
    },

    components: {
        "register": httpVueLoader("./register.vue"),
        "payment": httpVueLoader("./payment.vue"),
        "success": httpVueLoader("./success.vue")
    },

    computed : {
        step: {
            get () { return this.$store.state.register.step },
            set (v) { this.$store.commit('register/set_object', ['step', v]) } }
    }
}
</script>