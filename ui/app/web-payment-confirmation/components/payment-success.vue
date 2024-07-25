<template>
    <v-container>
        <v-row no-gutters>
            <v-spacer></v-spacer>
            <v-col cols="12" class="d-flex">
                <!-- <v-sheet class="pa-12" color="grey lighten-3"> -->
                <v-sheet :elevation="0" class="mx-auto fill-height py-10 px-10 grow" height="auto" width="100%">
                    <h2 class="my-5">Konfirmasi Pembayaran Berhasil</h2>
                    <p>Kami akan segera memverifikasi pembayaran anda. Anda bisa mengecek email, kami akan
                        mengirimkan bukti pembayaran begitu proses verifikasi selesai.</p>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="primary" @click="goToHome">Kembali ke Beranda</v-btn>
                    </v-card-actions>
                </v-sheet>
                <!-- </v-sheet> -->
            </v-col>

            
        </v-row>
    </v-container>
</template>

<script>
module.exports = {
    data() {
        return {}
    },
    methods: {
        goToHome() {
            window.open("../web-product")
        }
    }
}
